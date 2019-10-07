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
          <em>Hosted by</em> <?php echo $hosted_by; ?><br />
          <?php echo $invite_location; ?><br />
          <?php echo $invite_citystatezip; ?><br />
          <?php echo $invite_date; ?>
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
        <em>Hosted by</em> <?php echo $hosted_by; ?><br />
          <?php echo $invite_location; ?><br />
          <?php echo $invite_citystatezip; ?><br />
          <?php echo $invite_date; ?>
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
        <em>Hosted by</em> <?php echo $hosted_by; ?><br />
          <?php echo $invite_location; ?><br />
          <?php echo $invite_citystatezip; ?><br />
          <?php echo $invite_date; ?>
        </p>
      </div>

      <div class="detailbox detailbox--picturemask">
        <p class="env-card__intro">
          <span>Join us<br /><em>for a</em></span>
          <span>Women Doing Well Retreat</span>
        </p>
        <img id="whitemask" src="/wp-content/themes/wdw/images/invites/white-mask.svg" />
        <div id="mask-bg"></div>

        {% if themecolor == "Teal" %}
        <img id="ppp-navy" src="/assets/img/content/wdw/ppp-teal.png" /> {%
        elseif themecolor == "Mulberry" %}
        <img id="ppp-navy" src="/assets/img/content/wdw/ppp-mulberry.png" /> {%
        else %}
        <img id="ppp-navy" src="/assets/img/content/wdw/ppp-navy.png" /> {%
        endif %}

        <p class="env-card__details">
          <em>Hosted by</em> <?php echo $hosted_by; ?><br /><br />
          <?php echo $invite_location; ?><br />
          <?php echo $invite_citystatezip; ?><br />
          <?php echo $invite_date; ?>
        </p>
      </div>

        <?php if (($theme == "Picture Right") || ($color == "Picture Mask")) : ?>
            <div class="bg-img-contain"><div class="bg-img woman-jacket"></div></div> 
        <?php endif;  ?>

    </div>

  </section>
  <a class="page-anchor" name="details"></a>
  <section
    class="detailsection detailsection--IGJ detailsection--{{ themecolor }}"
    id="details"
  >
    <div>
      <h3>Host</h3>
      <h2><?php echo $hosted_by; ?></h2>
      <a href="mailto:<?php echo $contact_email; ?>?subject=WDW Retreat"
        >Send a message</a
      >
    </div>
    <div>
      <h3>Date</h3>
      <h2><?php echo $invite_date; ?><br /><?php echo $invite_time; ?></h2>
      <a href="#">Add to calendar</a>
    </div>
    <div>
      <h3>Location</h3>
      <h2>
      <?php echo $invite_location; ?><br /><?php echo $invite_street; ?><br /><?php echo $invite_citystatezip; ?>
      </h2>
      <a
        target="_blank"
        href="http://maps.google.com/?q=<?php echo str_replace("#", "%23", $invite_street); ?>, <?php echo $invite_citystatezip; ?>"
        >GET DIRECTIONS</a
      >

    </div>
  </section>


  <section class="register register--<?php echo $color; ?>">
    <a class="btn btn--wdw btn--<?php echo $color; ?>" href="#register">REGISTER</a>
  </section>

  <section class="power-words-banner-<?php echo $color; ?>"></section>
  <section class="about--section about--IGJ animated" id="about-section">
    <h1>What is a Women Doing Well Retreat?</h1>
    <div class="about__desc">
      <img
        id="ppp-navy"
        src="https://s3.amazonaws.com/GG-Evites/2018/WDW-Themes/ppp.png"
      />
      <div>
        <p>
          At some point we all ask, “What am I here for?” We naturally want to
          know our purpose for existing and how our unique talents, passions and
          abilities can be used to make a difference. Life becomes more rich and
          meaningful once we know our purpose, passion and have a plan to make
          it happen.
        </p>

        <p>
          Please join us for a <strong>WDW Retreat</strong> in which we will
          explore what it means to live and give in God’s image through greater
          knowledge of our purpose, passions and the pathway to living
          generously in our day to day lives. The day will take place in a small
          group setting and consist of four sessions and a facilitator will
          guide the conversation.
        </p>
      </div>
    </div>
    <span></span>
  </section>
  <section class="rsvp rsvp-retreat">
    <div>
      <h3>Details from your host</h3>
      <p><?php echo $event_description; ?></p>
    </div>
    <span></span>
    <span></span>
    <div>
      <h3>RSVP</h3>
      <p>
        This is an invitation-only event with limited space available. If you
        would like to attend please register by filling out the registration
        form by <?php echo $rsvp_date; ?>. If you have questions or
        to send regrets, please email
        <a
          href="mailto:<?php echo $contact_email; ?>?subject=Retreat&cc=<?php echo $contact_email_2; ?>"
          ><?php echo $contact_name; ?></a
        >
        or call <?php echo $contact_phone; ?>.
      </p>
    </div>
  </section>

  <section class="invitation_form invitation_form--IGJ">
    <section id="register" class="invitation_form__container animated">

      {% if jogfull == "yes" %}
        {% set formId = 4713375 %}
        <div class="retreatfull">
          <h2>This Women Doing Well Retreat is currently FULL.</h2>
          <p>
            But don't worry &mdash; below is a waitlist form, and if any
            availability opens up, you'll be the first to know.
          </p>
        </div>
      {% else %}
        {% set formId = entry.formAssemblyFormId %} 
      {% endif %}

      <?php echo do_shortcode("[formassembly formid=4649199]"); ?>
    </section>
  </section>

  <section class="reg-footer">
    <a href="http://womendoingwell.org">womendoingwell.org</a>
  </section>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/1.18.4/TweenMax.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/2.0.1/plugins/ScrollToPlugin.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function(event) {
    var eventIdTag = document.querySelector("#tfa_3");
    eventIdTag.setAttribute("value", "{{ event.Id }}");
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
  var formscroll = document.getElementById("register");
  wFORMS.behaviors.paging.onPageChange = function(formscroll) {
    scrollTo(formscroll, 0, 100);
  };
</script>
<script>
  const form = document.getElementsByClassName("invitation_form");
  const rsvpcon = document.getElementsByClassName("rsvp-confirmation");

  $(document).ready(function() {
    const tlr = new TimelineLite();
    tlr.fromTo(
      rsvpcon,
      0.5,
      {
        opacity: 0,
        scale: 0,
        visibility: "visible"
      },
      {
        opacity: 1,
        visibility: "visible",
        scale: 1,
        ease: Back.easeOut.config(1)
      },
      2.6
    );

    const tla = new TimelineLite();
    tla.set(form, {
      y: window.innerHeight / 2,
      opacity: 0
    });

    $(form).waypoint(
      function() {
        const tlb = new TimelineLite();
        tlb.to(
          form,
          0.9,
          {
            y: 0,
            x: 0,
            opacity: 1,
            ease: Power4.easeInOut
          },
          0
        );
      },
      { offset: "90%" }
    );
  });
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


<div style="white-space: pre-line; margin: 200px auto;">

ID: <?php echo $event_id; ?>

Title: <?php echo $event_title; ?>

Hosted By: <?php echo $hosted_by; ?>

Theme: <?php echo $theme; ?>

Color: <?php echo $color; ?>

State date: <?php echo $start_date; ?>

End date: <?php echo $end_date; ?>

Days Away: <?php echo $days_away; ?>

Invite Time: <?php echo $invite_time; ?>

Invite Date: <?php echo $invite_date; ?>

RSVP Date: <?php echo $rsvp_date; ?>

Start Time: <?php echo $start_time; ?>

End Time: <?php echo $end_time; ?>

Time Zone: <?php echo $time_zone; ?>

Contact Name: <?php echo $contact_name; ?>

Contact Email: <?php echo $contact_email; ?>

Contact Email 2: <?php echo $contact_email_2; ?>

Contact Phone: <?php echo $contact_phone; ?>

Invite Location: <?php echo $invite_location; ?>

Invite Street: <?php echo $invite_street; ?>

Invite City/State/ZIP: <?php echo $invite_citystatezip; ?>

Event Description: <?php echo $event_description; ?>

</div>