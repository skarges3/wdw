<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 12/5/13
 * Time: 10:22 AM
 * To change this template use File | Settings | File Templates.
 */

class ipsoGoogleAnaltics{
    private $was_added;
    function __construct()
    {
        if (is_multisite()){
            $ns = new NetworkSettings('ipso Analytics');
            $ns->add_setting('google_analytics', 'google_analytics', 'Google Analytics Account ID', 'input');
            new GeneralSetting('google_analytics_site', 'google_analytics_site', 'Google Analytics Account ID (site)', 'input');
        }
        else{
            new GeneralSetting('google_analytics', 'google_analytics', 'Google Analytics Account ID', 'input');
        }
        $display = array($this, 'display');
        //Just in case the theme didn't setup call the ipso_analytics action, we will put the script at the bottom of the page
        add_action('wp_print_footer_scripts', $display);
        add_action('ipso_analytics', $display);
    }

    function display()
    {
        if ($this->was_added){
            return;
        }
        $this->was_added = true;
        $ga = get_option('google_analytics_site');
        if (empty($ga)) {
            $ga = get_site_option('google_analytics');
        }
        if (empty($ga)) {
            echo '<!-- Google Analytics ID is not setup-->';
            return;
        }
        $domain = network_site_url('', 'http');
        $domain = substr($domain, 7);
        $domain = untrailingslashit($domain);
        ?>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', '<?php echo $ga?>', 'auto');
    ga('require', 'displayfeatures');
    ga('send', 'pageview');

</script><?php
    }
}

new ipsoGoogleAnaltics();
