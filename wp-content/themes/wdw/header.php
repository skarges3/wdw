<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie.css"/>
    <![endif]-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
    <header id="masthead" class="site-header" role="banner">
        <div class="header-main <?php echo get_option('menu_version')?>">
 <?php         if ( is_front_page() ) {     // Main Logo ?>
<?php   wp_nav_menu(array('theme_location' => 'primary_left', 'menu_class' => 'left-nav')); ?>
<a href="<?php echo esc_url(home_url('/')); ?>" class="logolockup"  rel="home"><img src="/wp-content/uploads/2018/08/Full_Lockup-White-full.png" alt="<?php bloginfo() ?> Logo"/><span><?php bloginfo('description') ?></span></a>
            <nav id="primary-navigation" class="site-navigation primary-navigation" role="navigation">
                <h1 class="menu-toggle">Menu</h1>
                <a class="screen-reader-text skip-link"
                   href="#content"><?php _e('Skip to content', 'ipso'); ?></a>

                <div id="mobile-side-nav"> 
<?php   wp_nav_menu(array('theme_location' => 'primary_left', 'container_class' => 'mobile-left-container', 'menu_class' => 'mobile-left-nav')); ?>
<?php    wp_nav_menu(array('theme_location' => 'primary_right', 'container_class' => 'mobile-right-container', 'menu_class' => 'mobile-right-nav')); ?>
</div>	</nav>			
<?php    wp_nav_menu(array('theme_location' => 'primary_right', 'menu_class' => 'right-nav')); ?>

 <?php } else {     // Interior Logo ?>
 <div class="interior-menu">
<?php   wp_nav_menu(array('theme_location' => 'primary_left', 'menu_class' => 'left-nav')); ?>
<a href="<?php echo esc_url(home_url('/')); ?>" class="logolockup" rel="home"><img src="/wp-content/uploads/2018/07/brandmark-outlined.png"  alt="<?php bloginfo() ?> Logo"/><span><?php bloginfo('description') ?></span></a>
            <nav id="primary-navigation" class="site-navigation primary-navigation " role="navigation">
                <h1 class="menu-toggle">Menu</h1>
                <a class="screen-reader-text skip-link"
                   href="#content"><?php _e('Skip to content', 'ipso'); ?></a>

                <div id="mobile-side-nav"> 
<?php   wp_nav_menu(array('theme_location' => 'primary_left', 'container_class' => 'mobile-left-container', 'menu_class' => 'mobile-left-nav')); ?>
<?php    wp_nav_menu(array('theme_location' => 'primary_right', 'container_class' => 'mobile-right-container', 'menu_class' => 'mobile-right-nav')); ?>
</div></nav>
<?php    wp_nav_menu(array('theme_location' => 'primary_right', 'menu_class' => 'right-nav')); ?>	
</div>
<?php }   ?>
  <!--                  <div class="header-reveal">
                        <div class="content-wrapper">
                            <?php echo apply_filters('the_content', get_option('header_reveal')) ?>
                        </div>
                    </div>
                </div>
                <div id="header-search">
                    <a href="#search" class="search-link fa fa-search">Search</a>

                    <div class="search-wrapper">
                        <?php get_search_form() ?>
                    </div>
                </div>
                <?php
                $link_facebook = get_option('facebook_page');
                $link_twitter = get_option('twitter_page');
                if (!empty($link_facebook) || !empty($link_twitter)) {
                    ?>
                    <div id="social-links">
                        <?php if (!empty($link_twitter)) { ?>
                            <a href="<?php echo $link_twitter ?>" target="_blank" rel="nofollow"
                               class="fa fa-twitter"><span>Twitter</span></a>
                        <?php } ?>
                        <?php if (!empty($link_facebook)) { ?>
                            <a href="<?php echo $link_facebook ?>" target="_blank" rel="nofollow"
                               class="fa fa-facebook"><span>Facebook</span></a>
                        <?php } ?>
                    </div> -->
                <?php } ?>
            </nav>
        </div>
    </header>
    <!-- #masthead -->

    <div id="main" class="site-main">
