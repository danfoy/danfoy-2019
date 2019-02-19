<?php
/**
 * Header template for danfoy_2019 WordPress theme
 *
 * @package     danfoy_2019
 * @subpackage  header
 * @author      Dan Foy <danfoy.com>
 * @since       1.0.0
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />

        <title><?php

            wp_title( '' );
            if ( wp_title( '', false ) ) { echo ' : ';}
            bloginfo( 'name' );

        ?></title>


        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicons/apple-touch-icon.png" />
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicons/favicon-32x32.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_stylesheet_directory_uri(); ?>img/favicons/favicon-16x16.png" />
        <link rel="mask-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicons/safari-pinned-tab.svg" color="#000000" />

        <link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,700i" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Crimson+Text:400,400i,700,700i" rel="stylesheet" />

        <meta name="theme-color" content="#ffffff" />
        <meta name="msapplication-TileColor" content="#ffc40d" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="<?php bloginfo( 'description' ); ?>" />

        <link href="//www.google-analytics.com" rel="dns-prefetch" />

        <?php wp_head();?>

    </head>

    <body <?php body_class(); ?>>

        <div class="wrapper">

            <header class="site-header">

                    <div class="site-header-title">
                        <h1 class="site-header-title-heading">
                            <a class="site-header-title-link" href="<?php echo esc_url( home_url() ); ?>">
                                <?php
                                bloginfo( 'name' );
                                ?>
                            </a>
                        </h1>
                    </div>

                    <nav class="site-header-nav">
                        <?php

                        wp_nav_menu( array(
                            'theme_location'  => 'header-menu',
                            'menu'            => '',
                            'container'       => 'div',
                            'container_class' => 'site-header-menu',
                            'container_id'    => '',
                            'menu_class'      => 'site-header-menu',
                            'menu_id'         => '',
                            'echo'            => true,
                            'fallback_cb'     => 'wp_page_menu',
                            'before'          => '',
                            'after'           => '',
                            'link_before'     => '',
                            'link_after'      => '',
                            'items_wrap'      => '<ul>%3$s</ul>',
                            'depth'           => 0,
                            'walker'          => '',
                        ) );



                        ?>
                    </nav>

            </header>
