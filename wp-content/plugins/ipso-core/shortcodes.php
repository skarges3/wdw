<?php

class ParamCollector
{
    public $params = array();

    function parse($m)
    {
        $tag = $m[2];
        if ($tag == 'param') {
            $atts = shortcode_parse_atts($m[3]);
            $atts = shortcode_atts(array('name' => '', 'value' => null), $atts);
            $content = empty($m[5]) ? $atts['value'] : $m[5];
            $this->params[$atts['name']] = $content;
        }
        return null;
    }
}

add_shortcode('widget', function ($atts, $content) {
    global $wp_widget_factory;
    $widget_name = null;
    $class = null;
    $p = new ParamCollector();
    $pattern = get_shortcode_regex();
    preg_replace_callback("/$pattern/s", array($p, 'parse'), $content);

    $instance = $p->params;
    $id = null;

    $atts = array_merge(array(
        'widget_name' => FALSE,
        'class' => '',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ), $atts);


    extract($atts);

    foreach ($atts as $k => $v) {
        $atts[$k] = wp_specialchars_decode($v);
    }

    if (!isset($wp_widget_factory->widgets[$widget_name])
        || !is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')
    ):
        $widget_name = 'WP_Widget_' . ucwords(strtolower($class));
        if (empty($class)
            || !isset($wp_widget_factory->widgets[$widget_name])
            || !is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')
        ):
            return '<p>' . sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"), '<strong>' . $class . '</strong>') . '</p>'; else:
        endif;
    endif;

    ob_start();
    the_widget($widget_name, $instance, $atts);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;

});

add_shortcode('param', function ($atts, $content) {
    return '';
});


add_shortcode('facebook-stats', function ($atts) {
    $cache = Cache::$instance;
    $url = null;
    $stat = null;
    extract(shortcode_atts(array(
        'url' => null,
        'id' => null,
        'stat' => 'shares'
    ), $atts));
    if (!empty($id)) {
        $req = "http://graph.facebook.com/$id";
    } else if (!empty($url)) {
        $req = 'http://graph.facebook.com/?ids=' . urlencode($url);
    }
    $cacheName = 'facebook-stats-' . md5($req);
    $result = $cache->read($cacheName);
    if ($result === false) {
        $curl = curl_init($req);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $cache->write($cacheName, $result, 60 * 60);
        curl_close($curl);
    }
    $data = json_decode($result);

    if (!empty($id)) {
        return $data->$stat;
    } else if (!empty($url)) {
        return $data->$url->$stat;
    } else {
        return '';
    }
});

$twitter_api_settings = array(
    'oauth_access_token' => "1710972457-t6aupFO36QMRKHydhVGUCIwkJ2DZdVABMtHdHa4",
    'oauth_access_token_secret' => "06lG8uIGbv7kQBY99lBqOEMy4i7Fj7DKy2eewwaJpw",
    'consumer_key' => "AWYjQ5OO1FhTAxMGJrjQ",
    'consumer_secret' => "jrupl39dahvqFw1XgSjEqhU6BoXzJczT5BNTm2pVE"
);

add_shortcode('twitter-stats', function ($atts) {
    require_once('lib/TwitterAPIExchange.php');
    $cache = Cache::$instance;
    global $twitter_api_settings;
    $screen_name = null;
    $user_id = null;
    $stat = null;
    extract(shortcode_atts(array(
        'screen_name' => null,
        'user_id' => null,
        'stat' => 'followers_count'
    ), $atts));

    $req = 'https://api.twitter.com/1.1/users/show.json';
    $get = '';
    if (!empty($screen_name)) {
        $get = '?screen_name=' . $screen_name;
    }
    if (!empty($user_id)) {
        $get = '?user_id=' . $user_id;
    }

    $cacheName = 'twitter-stats-' . md5($get);
    $result = $cache->read($cacheName);
    if ($result === false) {
        $settings = $twitter_api_settings;
        $twitter = new TwitterAPIExchange($settings);
        $result = $twitter->setGetfield($get)
            ->buildOauth($req, 'GET')
            ->performRequest();
        $cache->write($cacheName, $result, 60 * 60);
    }
    $data = json_decode($result);

    return $data->$stat;
});


function ipso_shortcode_tweets($atts)
{
    require_once('lib/TwitterAPIExchange.php');
    $cache = Cache::get_instance();
    global $twitter_api_settings;
    $screen_name = null;
    $user_id = null;
    $stat = null;
    $timestamp_format = null;
    $show_images = null;
    $max_tweets = null;
    extract(shortcode_atts(array(
        'screen_name' => null,
        'user_id' => null,
        'stat' => 'followers_count',
        'show_images' => false,
        'timestamp_format' => 'd M',
        'max_tweets' => '0'
    ), $atts));

    $req = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $get = '';
    if (!empty($screen_name)) {
        $get = '?screen_name=' . $screen_name;
    }
    if (!empty($user_id)) {
        $get = '?user_id=' . $user_id;
    }

    $result = null;
    if ($cache != null) {
        $cacheName = 'twitter-timeline-' . md5($get);
        $result = $cache->read($cacheName);
    }
    if ($result === false) {
        $settings = $twitter_api_settings;
        $twitter = new TwitterAPIExchange($settings);
        $result = $twitter->setGetfield($get)
            ->buildOauth($req, 'GET')
            ->performRequest();
        if ($cache != null) {
            $cache->write($cacheName, $result, 60 * 60);
        }
    }
    $arr = json_decode($result);

    $html = '';

    foreach ($arr as $idx => $tweet) {
        if ($max_tweets != 0 && $max_tweets <= $idx) {
            break;
        }
        //print_r($tweet);

        // if retweet - use that data instead
        $retweet = false;

        if (!empty($tweet->retweeted_status)) {
            $retweet = true;
            $orig_entities = $tweet->retweeted_status->entities;
            $tweet_formatted = $tweet->retweeted_status->text;

        } else {
            $orig_entities = $tweet->entities;
            $tweet_formatted = $tweet->text;
        }

        // we need to preload all entities and process biggest index first
        $entities_arr = array();
        foreach ($orig_entities as $entity_type => $entities) {
            foreach ($entities as $entity) {
                //print_r($entity);
                $entities_arr[$entity->indices[0]] = array(
                    'type' => $entity_type,
                    'indice_stop' => $entity->indices[1],
                    // for mentions
                    'screen_name' => @$entity->screen_name,
                    // for links
                    'url' => @$entity->url,
                    'display_url' => @$entity->display_url,
                    // for hashtags
                    'text' => @$entity->text,
                );
            }
        }

        krsort($entities_arr, SORT_NUMERIC);

        foreach ($entities_arr as $indice_start => $entity) {
            if (!in_array($entity['type'], array('user_mentions', 'urls', 'media', 'hashtags')))
                continue;

            // text before entity
            $tmp = mb_substr($tweet_formatted, 0, $indice_start);

            if ($entity['type'] == 'user_mentions')
                $tmp .= '<a target="_blank" href="https://twitter.com/' . $entity['screen_name'] . '">@' . $entity['screen_name'] . '</a>';

            else if ($entity['type'] == 'urls' || $entity['type'] == 'media')
                $tmp .= '<a target="_blank" href="' . $entity['url'] . '">' . $entity['display_url'] . '</a>';

            else if ($entity['type'] == 'hashtags')
                $tmp .= '<a target="_blank" href="https://twitter.com/search/realtime?q=%23' . $entity['text'] . '&src=hash">#' . $entity['text'] . '</a>';

            // text after entity
            $tweet_formatted = $tmp . mb_substr($tweet_formatted, $entity['indice_stop']);

        }

        if ($retweet === true)
            $tweet_formatted = "RT: " . $tweet_formatted;

        if ($show_images) {
            $tweet_formatted = "<img class='twitter-user-profile' src='{$tweet->user->profile_image_url}'/>$tweet_formatted";
        }
        if (!empty($timestamp_format)) {
            $time = date($timestamp_format, strtotime($tweet->created_at));
            $tweet_formatted .= "<span class='tweet-time'>$time</span>";
        }
        $html .= "<li class='tweet'>$tweet_formatted</li>";
    }


    return "<ul class='twitter-feed'>$html</ul>";

}

;

add_shortcode('tweets', 'ipso_shortcode_tweets');


class SocialButtons {

    var $facebook_script_included;
    function __construct()
    {
        add_shortcode('facebook_like_button', array($this, 'facebook_like_button'));
    }

    function facebook_like_button($attr, $content)
    {
        add_action('wp_footer', array($this, 'facebook_footer_script'));
        extract(
            shortcode_atts(
                array('url' => ''),
                $attr
            )
        );
        return '<div class="fb-like" data-href="'.$url.'" data-layout="standard" data-action="like"
             data-show-faces="true" data-share="true"></div>';
    }

    function facebook_footer_script(){
        if ($this->facebook_script_included){
            return;
        }

        $app_id = get_option('facebook_app_id');
        ?>
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $app_id?>";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
        <?php
        $this->facebook_script_included = true;
    }
}

new SocialButtons();