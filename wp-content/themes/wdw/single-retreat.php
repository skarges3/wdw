<?php get_header(); ?>

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
    class="invite invite--WDWRetreat invite--<?php echo $theme; ?> invite--<?php echo $color; ?>"
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

    <img
      class="env-behind"
      src="/assets/img/content/invites/<?php echo $env_behind_image; ?>.png"
    />

    <img class="env-front" src="/assets/img/content/invites/env-front.png" />
    <div
      class="env-card env-card--WDWRetreat env-card--{{ theme }} env-card--{{ themecolor }}"
    >
      <div class="border"></div>

      {# SOLID THEME #}
      <div class="detailbox detailbox--solid">
        <img src="/assets/img/content/wdw/wdw-white.png" />
        <p class="env-card__details">
          Join us <em>for a</em><br />
          Women Doing Well Retreat
        </p>
        <p class="break"><span></span></p>
        <p class="env-card__details">
          <em>Hosted by</em> {{ host }}<br />
          {{ event_location | nl2br }}<br />
          {{ event_zip }}<br />
          {{ event_date }}
        </p>
        <img src="/assets/img/content/wdw/ppp-wide-white.png" />
      </div>

      {# POWER WORDS THEME #}
      <div class="detailbox detailbox--powerwords">
        <p class="env-card__details">
          Join us <em>for a</em><br />
          Women Doing Well Retreat
        </p>
        <p class="break"><span></span></p>
        {% if themecolor == "Teal" %}
        <img id="ppp" src="/assets/img/content/wdw/ppp-teal.png" /> {% elseif
        themecolor == "Mulberry" %}
        <img id="ppp" src="/assets/img/content/wdw/ppp-mulberry.png" /> {% else
        %} <img id="ppp" src="/assets/img/content/wdw/ppp-navy.png" /> {% endif
        %}
        <p class="break"><span></span></p>
        <p class="env-card__details">
          <em>Hosted by</em> {{ host }}<br />
          {{ event_location | nl2br }}<br />
          {{ event_zip }}<br />
          {{ event_date }}
        </p>
      </div>

      {# PICTURE RIGHT THEME #}
      <div class="detailbox detailbox--pictureright">
        {% if themecolor == "Teal" %}
        <img
          id="sig-event-navy"
          src="/assets/img/content/wdw/retreat-teal.png"
        />
        {% elseif themecolor == "Mulberry" %}
        <img
          id="sig-event-navy"
          src="/assets/img/content/wdw/retreat-mulberry.png"
        />
        {% else %}
        <img
          id="sig-event-navy"
          src="/assets/img/content/wdw/retreat-navy.png"
        />
        {% endif %}
        <p class="break"><span></span></p>
        {% if themecolor == "Teal" %}
        <img id="ppp-navy" src="/assets/img/content/wdw/ppp-disc-teal.png" /> {%
        elseif themecolor == "Mulberry" %}
        <img
          id="ppp-navy"
          src="/assets/img/content/wdw/ppp-disc-mulberry.png"
        />
        {% else %}
        <img id="ppp-navy" src="/assets/img/content/wdw/ppp-disc-navy.png" /> {%
        endif %}
        <p class="break"><span></span></p>
        <p class="env-card__details">
          <em>Hosted by</em> {{ host }}<br /><br />
          {{ event_location | nl2br }}<br />
          {{ event_zip }}<br />
          {{ event_date }}
        </p>
      </div>

      {# PICTURE MASK THEME #}
      <div class="detailbox detailbox--picturemask">
        <p class="env-card__intro">
          <span>Join us<br /><em>for a</em></span>
          <span>Women Doing Well Retreat</span>
        </p>
        <img id="whitemask" src="/assets/img/content/wdw/white-mask.svg" />
        <div id="mask-bg"></div>

        {% if themecolor == "Teal" %}
        <img id="ppp-navy" src="/assets/img/content/wdw/ppp-teal.png" /> {%
        elseif themecolor == "Mulberry" %}
        <img id="ppp-navy" src="/assets/img/content/wdw/ppp-mulberry.png" /> {%
        else %}
        <img id="ppp-navy" src="/assets/img/content/wdw/ppp-navy.png" /> {%
        endif %}

        <p class="env-card__details">
          <em>Hosted by</em> {{ host }}<br /><br />
          {{ event_location | nl2br }}<br />
          {{ event_zip }}<br />
          {{ event_date }}
        </p>
      </div>

      {% if theme == "PictureRight" or theme == "PictureMask" %}
      <div class="bg-img-contain"><div class="bg-img woman-jacket"></div></div>

      {% else %} {% endif %}
    </div>

  </section>
  <a class="page-anchor" name="details"></a>
  <section
    class="detailsection detailsection--IGJ detailsection--{{ themecolor }}"
    id="details"
  >
    <div>
      <h3>Host</h3>
      {% if host %}
      <h2>{{ host }}</h2>
      <a href="mailto:{{ contact_email }}?subject={{ event_type }}"
        >Send a message</a
      >
      {% else %}
      <h2>TBA</h2>
      {#
      <a href="mailto:{{ contact_email }}?subject={{ event_type }}"
        >Send a message</a
      >
      #} {% endif %}
    </div>
    <div>
      <h3>Date</h3>
      {% if event_date %}
      <h2>{{ event_date }}<br />{{ event_time }}</h2>
      <span class="addtocalendar atc-style-jog">
        <var class="atc_event">
          <var class="atc_date_start">{{ event_start_time }}</var>
          <var class="atc_date_end">{{ event_end_time }}</var>
          <var class="atc_timezone">{{ event_time_zone }}</var>
          <var class="atc_title">{{ event_type }}</var>
          <var class="atc_description">{{ event_desc }}</var>
          <var class="atc_location"
            >{{ event_location }}, {{ event_street }}, {{ event_zip }}</var
          >
          <var class="atc_organizer">{{ host }}</var>
          <var class="atc_organizer_email">{{ contact_email }}</var>
        </var>
      </span>
      {% else %}
      <h2>TBA</h2>
      {% endif %}
    </div>
    <div>
      <h3>Location</h3>
      {% if event_location %}
      <h2>
        {{ event_location | nl2br }}<br />{{ event_street }}<br />{{
          event_zip
        }}
      </h2>
      <a
        target="_blank"
        href="http://maps.google.com/?q={{ event_street|replace({'#':'%23'}) }}, {{ event_zip }}"
        >GET DIRECTIONS</a
      >
      {% else %}
      <h2>TBA</h2>
      {% endif %}
    </div>
  </section>


  <section class="register register--{{ themecolor }}">
    <a class="btn btn--wdw btn--{{ themecolor }}" href="#register">REGISTER</a>
  </section>

  <section class="power-words-banner-{{ themecolor }}"></section>
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
      {% if entry.invType == "std" %}
      <p>
        We are excited about the opportunity to fellowship together and hope you
        are able to join us! Please know that nothing will be asked of you by
        anyone at the event. The desire is that you leave blessed, in addition
        to feeling encouraged and enriched in your journey.
      </p>
      {% else %}
      <p>{{ event_desc | nl2br }}</p>
      {% endif %}
    </div>
    <span></span> {% if entry.invType == "std" %} {% elseif entry.invType ==
    "rsvp" %} <span></span>
    <div>
      <h3>RSVP</h3>
      <p>
        <strong>We look forward to seeing you!</strong> If you have questions or
        to send regrets, please email
        <a
          href="mailto:{{ contact_email }}?subject={{ event_type }}&cc={{ contact_email_2 }}"
          >{{ contact_name }}</a
        >
        or call {{ contact_phone }}.
      </p>
    </div>
    {% else %} <span></span>
    <div>
      <h3>RSVP</h3>
      <p>
        This is an invitation-only event with limited space available. If you
        would like to attend please register by filling out the registration
        form by {{ event.Invite_RSVP_Date__c }}. If you have questions or
        to send regrets, please email
        <a
          href="mailto:{{ contact_email }}?subject={{ event_type }}&cc={{ contact_email_2 }}"
          >{{ contact_name }}</a
        >
        or call {{ contact_phone }}.
      </p>
    </div>
    {% endif %}
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