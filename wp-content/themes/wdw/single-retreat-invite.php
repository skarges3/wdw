<?php get_header( 'invites' ); ?>

<?php the_post() ?>
<?php the_content() ?>

<?php 
    // Initialize
    $event_id = 0;
    $event_title = "";
    $hosted_by = "";
    $theme = "";
    $color = "";

    $start_date = null;
    $end_date = null;
    $days_away = 0;
    $event_full = false;
    
    $invite_time = "";
    $invite_date = "";
    $rsvp_date = "";
    $start_time = null;
    $end_time = null;
    $time_zone = null;
    $contact_name = "";
    $contact_email = "";
    $contact_email_2 = "";
    $contact_phone = "";
    $invite_location = "";
    $invite_street = "";
    $invite_citystatezip = "";
    $event_description = "";

    // Get values
    $event_id = get_post_meta( get_the_ID(), 'event_id', true );
    $event_title = get_post_meta( get_the_ID(), 'event_title', true );
    $hosted_by = get_post_meta( get_the_ID(), 'hosted_by', true );
    $theme = get_post_meta( get_the_ID(), 'theme', true );
    $color = get_post_meta( get_the_ID(), 'color', true );

    $start_date = get_post_meta( get_the_ID(), 'start_date', true );
    $end_date = get_post_meta( get_the_ID(), 'end_date', true );
    $days_away = get_post_meta( get_the_ID(), 'days_away', true );
    $event_full = get_post_meta( get_the_ID(), 'event_full', true );

    $invite_time = get_post_meta( get_the_ID(), 'invite_time', true );
    $invite_date = get_post_meta( get_the_ID(), 'invite_date', true );
    $rsvp_date = get_post_meta( get_the_ID(), 'rsvp_date', true );
    $start_time = get_post_meta( get_the_ID(), 'start_time', true );
    $end_time = get_post_meta( get_the_ID(), 'end_time', true );
    $time_zone = get_post_meta( get_the_ID(), 'time_zone', true );
    $contact_name = get_post_meta( get_the_ID(), 'contact_name', true );
    $contact_email = get_post_meta( get_the_ID(), 'contact_email', true );
    $contact_email_2 = get_post_meta( get_the_ID(), 'contact_email_2', true );
    $contact_phone = get_post_meta( get_the_ID(), 'contact_phone', true );
    $invite_location = get_post_meta( get_the_ID(), 'invite_location', true );
    $invite_street = get_post_meta( get_the_ID(), 'invite_street', true );
    $invite_citystatezip = get_post_meta( get_the_ID(), 'invite_citystatezip', true );
    $event_description = get_post_meta( get_the_ID(), 'event_description', true );
    
?>


<div class="wdw-theme">

<div class="WDWR--invitedetails">

    <section class="invitation_form invitation_form--IGJ">
        <section id="register" class="invitation_form__container animated">
            <?php echo do_shortcode("[formassembly formid=4758515]"); ?>
        </section>
    </section>

    <div class="WDWR-Mockup">
        <section
            class="invite invite--WDWRetreat invite--<?php echo str_replace(" ", "", $theme); ?> invite--<?php echo $color; ?>"
        >

            <?php 
            
            if ($theme == "Power Words") {
                if (($color == "Navy") || ($color == "Teal")) {
                    $env_behind_image = "env-behind--Power-Words-Teal";
                } elseif (($color == "Mulberry") || ($color == "Blush")) {
                    $env_behind_image = "env-behind--Power-Words-Blush";
                }
            } else {
            $env_behind_image = "env-behind--Solid-".$color;
            }

            ?>

            <?php 
            
            if ($color == "Mulberry") {
                $ppp_image = "mulberry";
            } elseif ($color == "Teal") {
                $ppp_image = "teal";
            } else {
                $ppp_image = "navy";
            }

            ?>

            <img
            class="env-behind"
            src="/wp-content/themes/wdw/images/invites/<?php echo $env_behind_image; ?>.png"
            />

            <img class="env-front" src="/wp-content/themes/wdw/images/invites/env-front.png" />
            <div
            class="env-card env-card--WDWRetreat env-card--<?php echo str_replace(" ", "", $theme); ?> env-card--<?php echo $color; ?>"
            >
            <div class="border"></div>


            <div class="detailbox detailbox--solid">
                <img src="/wp-content/themes/wdw/images/invites/wdw-white.png" />
                <p class="env-card__details">
                Join us <em>for a</em><br />
                Women Doing Well Retreat
                </p>
                <p class="break"><span></span></p>
                <p class="env-card__details">
                <em>Hosted by</em> <span class="hostedby_value"><?php echo $hosted_by; ?></span><br /><br />
                <span class="location1_value"><?php echo $invite_location; ?></span><br />
                <span class="location2_value"><?php echo $invite_citystatezip; ?></span><br />
                <span class="date_value"><?php echo $invite_date; ?></span>
                </p>
                <img src="/wp-content/themes/wdw/images/invites/ppp-wide-white.png" />
            </div>


            <div class="detailbox detailbox--powerwords">
                <p class="env-card__details">
                Join us <em>for a</em><br />
                Women Doing Well Retreat
                </p>
                <p class="break"><span></span></p>
                <img id="ppp" src="/wp-content/themes/wdw/images/invites/ppp-<?php echo $ppp_image; ?>.png" /> 
                <p class="break"><span></span></p>
                <p class="env-card__details">
                <em>Hosted by</em> <span class="hostedby_value"><?php echo $hosted_by; ?></span><br /><br />
                <span class="location1_value"><?php echo $invite_location; ?></span><br />
                <span class="location2_value"><?php echo $invite_citystatezip; ?></span><br />
                <span class="date_value"><?php echo $invite_date; ?></span>
                </p>
            </div>

            <div class="detailbox detailbox--pictureright">
                <img
                id="sig-event-navy"
                src="/wp-content/themes/wdw/images/invites/retreat-<?php echo $ppp_image; ?>.png"
                />
                <p class="break"><span></span></p>

                <img id="ppp-navy" src="/wp-content/themes/wdw/images/invites/ppp-disc-<?php echo $ppp_image; ?>.png" />

                <p class="break"><span></span></p>
                <p class="env-card__details">
                <em>Hosted by</em> <span class="hostedby_value"><?php echo $hosted_by; ?></span><br /><br />
                <span class="location1_value"><?php echo $invite_location; ?></span><br />
                <span class="location2_value"><?php echo $invite_citystatezip; ?></span><br />
                <span class="date_value"><?php echo $invite_date; ?></span>
                </p>
            </div>

            <div class="detailbox detailbox--picturemask">
                <p class="env-card__intro">
                <span>Join us<br /><em>for a</em></span>
                <span>Women Doing Well Retreat</span>
                </p>
                <img id="whitemask" src="/wp-content/themes/wdw/images/invites/white-mask.svg" />
                <div id="mask-bg"></div>

                <img id="ppp-navy" src="/wp-content/themes/wdw/images/invites/ppp-<?php echo $ppp_image; ?>.png" />

                <p class="env-card__details">
                <em>Hosted by</em> <span class="hostedby_value"><?php echo $hosted_by; ?></span><br /><br />
                <span class="location1_value"><?php echo $invite_location; ?></span><br />
                <span class="location2_value"><?php echo $invite_citystatezip; ?></span><br />
                <span class="date_value"><?php echo $invite_date; ?></span>
                </p>
            </div>

                
                    <div class="bg-img-contain"><div class="bg-img woman-jacket"></div></div> 
                

            </div>

        </section>
        <a class="page-anchor" name="details"></a>
        <section
            class="detailsection detailsection--IGJ detailsection--<?php echo $color; ?>"
            id="details"
        >
            <div>
            <h3>Host</h3>
            <h2 class="hostedby_value"><?php echo $hosted_by; ?></h2>
            </div>
            <div>
            <h3>Date</h3>
            <h2><span class="date_value"><?php echo $invite_date; ?></span><br /><span class="time_value"><?php echo $invite_time; ?></span></h2>
            </div>
            <div>
            <h3>Location</h3>
            <h2>
            <span class="location1_value"><?php echo $invite_location; ?></span><br /><span class="location2_value"><?php echo $invite_street; ?></span><br /><span class="location3_value"><?php echo $invite_citystatezip; ?></span>
            </h2>
            </div>
        </section>
    </div>
</div>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/1.18.4/TweenMax.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/2.0.1/plugins/ScrollToPlugin.min.js"></script>
<script src="https://unpkg.com/jquery@3.4.1/dist/jquery.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function(event) {
    var eventIdTag = document.querySelector("#tfa_1");
    eventIdTag.setAttribute("value", "<?php echo $event_id; ?>");
    var hostTag = document.querySelector("#tfa_2");
    hostTag.setAttribute("value", "<?php echo $hosted_by; ?>");
  });
</script>
<script>
  // elements
  window.onload = function() {
    const env_card = document.getElementsByClassName("env-card");
    const env_front = document.getElementsByClassName("env-front");
    const env_behind = document.getElementsByClassName("env-behind");

    const env = [env_card, env_front, env_behind];

    // setup timeline
    const tl = new TimelineLite();
    tl.fromTo(
      env,
      1.7,
      {
        y: -window.innerHeight / 2 - 100,
        opacity: 0,
        visibility: "visible"
      },
      {
        y: 0,
        x: 0,
        opacity: 1,
        visibility: "visible",
        ease: Power4.easeInOut
      },
      0.03
    )
      .to(
        env_card,
        1.0,
        {
          y: -window.innerHeight / 3 - 80,
          ease: SlowMo.easeOut
        },
        "-=0.75"
      )
      .set(env_card, { zIndex: 19 })
      .to(
        env_card,
        0.9,
        {
          y: 0,
          ease: Back.easeOut.config(1)
        },
        "+=0.05"
      )
      .to(
        [env_front, env_behind],
        0.9,
        {
          xPercent: -80,
          ease: Back.easeOut.config(1)
        },
        "-=0.95"
      );
  };
</script>
<script type="text/javascript">
  jQuery(function($) {
    // from http://imakewebthings.com/jquery-waypoints/

    // Wicked credit to
    // http://www.zachstronaut.com/posts/2009/01/18/jquery-smooth-scroll-bugs.html
    var scrollElement = "html, body";
    $("html, body").each(function() {
      var initScrollTop = $(this).attr("scrollTop");
      $(this).attr("scrollTop", initScrollTop + 1);
      if ($(this).attr("scrollTop") == initScrollTop + 1) {
        scrollElement = this.nodeName.toLowerCase();
        $(this).attr("scrollTop", initScrollTop);
        return false;
      }
    });

    // Smooth scrolling for internal links
    $("a[href^='#']").click(function(event) {
      event.preventDefault();

      var $this = $(this),
        target = this.hash,
        $target = $(target);

      $(scrollElement)
        .stop()
        .animate(
          {
            scrollTop: $target.offset().top
          },
          500,
          "swing",
          function() {
            window.location.hash = target;
          }
        );
    });
  });
</script>

<script>

    $('input:radio[value=tfa_12]').click(function () {
        $('.env-card').addClass("env-card--Solid").removeClass('env-card--PictureRight env-card--PictureMask env-card--PowerWords');
        $('.env-behind').addClass("env-behind--Solid").removeClass('env-behind--PictureRight env-behind--PictureMask env-behind--PowerWords');
    });
    $('input:radio[value=tfa_13]').click(function () {
        $('.env-card').addClass("env-card--PowerWords").removeClass('env-card--PictureRight env-card--PictureMask env-card--Solid');
        $('.env-behind').addClass("env-behind--PowerWords").removeClass('env-behind--PictureRight env-behind--PictureMask env-behind--Solid');

    });
    $('input:radio[value=tfa_14]').click(function () {
        $('.env-card').addClass("env-card--PictureRight").removeClass('env-card--PowerWords env-card--PictureMask env-card--Solid');
        $('.env-behind').addClass("env-behind--PictureRight").removeClass('env-behind--Solid env-behind--PictureMask env-behind--PowerWords');

    });
    $('input:radio[value=tfa_16]').click(function () {
        $('.detailsection').addClass("detailsection--Navy").removeClass('detailsection--Blush detailsection--Mulberry detailsection--Teal');
        $('.env-card').addClass("env-card--Navy").removeClass('env-card--Blush env-card--Mulberry env-card--Teal');
        $('.env-behind').addClass("env-behind--Navy").removeClass('env-behind--Blush env-behind--Mulberry env-behind--Teal');
        $('.ppp-navy').show();
        $('.ppp-teal').hide();
        $('.ppp-mulberry').hide();
    });
    $('input:radio[value=tfa_17]').click(function () {
        $('.detailsection').addClass("detailsection--Teal").removeClass('detailsection--Blush detailsection--Mulberry detailsection--Navy');
        $('.env-card').addClass("env-card--Teal").removeClass('env-card--Blush env-card--Mulberry env-card--Navy');
        $('.env-behind').addClass("env-behind--Teal").removeClass('env-behind--Blush env-behind--Mulberry env-behind--Navy');
        $('.ppp-navy').hide();
        $('.ppp-teal').show();
        $('.ppp-mulberry').hide();
    });
    $('input:radio[value=tfa_18]').click(function () {
        $('.detailsection').addClass("detailsection--Mulberry").removeClass('detailsection--Blush detailsection--Navy detailsection--Teal');
        $('.env-card').addClass("env-card--Mulberry").removeClass('env-card--Blush env-card--Navy env-card--Teal');
        $('.env-behind').addClass("env-behind--Mulberry").removeClass('env-behind--Blush env-behind--Navy env-behind--Teal');
        $('.ppp-navy').hide();
        $('.ppp-teal').hide();
        $('.ppp-mulberry').show();
    });
    $('input:radio[value=tfa_19]').click(function () {
        $('.detailsection').addClass("detailsection--Blush").removeClass('detailsection--Navy detailsection--Mulberry detailsection--Teal');
        $('.env-card').addClass("env-card--Blush").removeClass('env-card--Navy env-card--Mulberry env-card--Teal');
        $('.env-behind').addClass("env-behind--Blush").removeClass('env-behind--Navy env-behind--Mulberry env-behind--Teal');
        $('.ppp-navy').show();
        $('.ppp-teal').hide();
        $('.ppp-mulberry').hide();
    });

    $('#tfa_3').keyup(function () {
        $('.hostedby_value').text($(this).val());
    });

    $('#tfa_4').keyup(function () {
        $('.date_value').text($(this).val());
    });

    $('#tfa_5').keyup(function () {
        $('.time_value').text($(this).val());
    });

    $('#tfa_7').keyup(function () {
        $('.location1_value').text($(this).val());
    });

    $('#tfa_8').keyup(function () {
        $('.location2_value').text($(this).val());
    });

    $('#tfa_9').keyup(function () {
        $('.location3_value').text($(this).val());
    });

</script>