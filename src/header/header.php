<?php
/**
 * Header template for danfoy-2019 WordPress theme
 *
 * @package df19
 * @since  1.0.0
 * @author Dan Foy (danfoy.com)
 *
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

        <link href="//www.google-analytics.com" rel="dns-prefetch" />
        <link href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/icons/favicon.ico" rel="shortcut icon" />
        <link href="<?php echo esc_url( get_template_directory_uri() ); ?>/img/icons/touch.png" rel="apple-touch-icon-precomposed" />

        <link href="https://fonts.googleapis.com/css?family=Crimson+Text:400,400i,700,700i" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i" rel="stylesheet" />

        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="<?php bloginfo( 'description' ); ?>" />

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
