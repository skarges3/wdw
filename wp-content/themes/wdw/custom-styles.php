#primary-navigation .nav-menu-left > li a:hover,
#primary-navigation .nav-menu-right > li a:hover,
#primary-navigation .nav-menu-main > li a:hover {
    color: <?php echo $color1 ?>;
    border-bottom-color: <?php echo $color1 ?>;
}

#primary-navigation .nav-menu-left .button,
#primary-navigation .nav-menu-right .button,
#primary-navigation .nav-menu-main .button {
    border-color: <?php echo $color2 ?>;
}

#primary-navigation .nav-menu-left .button a,
#primary-navigation .nav-menu-right .button a,
#primary-navigation .nav-menu-main .button a {
    color: <?php echo $color2 ?>;
}

#primary-navigation .nav-menu-left .button a:hover,
#primary-navigation .nav-menu-right .button a:hover,
#primary-navigation .nav-menu-main .button a:hover {
    background-color: <?php echo $color2 ?>;
    color: #fff;
}

#colophon {
    background-image: url(<?php echo wp_get_attachment_url($footer_background) ?>);
}

#masthead .version_2 .search-link {
    color: <?php echo $color1 ?>;
}

