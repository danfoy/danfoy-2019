<?php
/**
 * Functions file for danfoy_2019
 *
 * This file contains theme setup and configuration information, and defines
 * functions which can be used by any other template within the theme.
 *
 * @package     danfoy_2019
 * @subpackage  global
 * @author      Dan Foy <danfoy.com>
 * @since       1.0.0
 */


/**
 * Set content width
 *
 * Content width is an annoying legacy global which is used to set the maximum
 * size of various elements. I have removed hard-coded dimension sizes from
 * images further down this file, but it still effects embedded content such as
 * videos and image galleries.
 *
 * This is a serious annoyance in the responsive design era, as this max-width
 * tends to be hard-coded into embedded items and requires hacky !important
 * css rules to override.
 *
 * This should be set to the largest width that is likely to appear for a
 * post-content section on the site, and then scaled down using CSS rules.
 * I have included a link to an (overengineered) alternative:
 * https://gist.github.com/glueckpress/61862e1f30c865b31715
 */
if ( ! isset( $content_width ) )
    $content_width = 1200;


/**
 * Add theme support for optional features
 *
 * These features are available on modern WordPress installations, but are
 * disabled by default for legacy reasons, so they are enabled here manually.
 */
if ( function_exists( 'add_theme_support' ) ) {

    // Enable post formats
    add_theme_support( 'post-formats',  array (
        'aside',
        'gallery',
        'quote',
        'image',
        'video',
    ) );

    // Enable post and comment RSS feed links
    add_theme_support( 'automatic-feed-links' );

    // Enable semantic HTML5 support
    add_theme_support( 'html5', array(
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
    ) );

    // Add thumbnail support
    add_theme_support( 'post-thumbnails' );

    // Customise image sizes
    add_image_size( 'large', 700, '', true );
    add_image_size( 'medium', 250, '', true );
    add_image_size( 'small', 120, '', true );
//  add_image_size( 'custom-size', 700, 200, true );
}


/**
 * Load theme stylesheet
 *
 * Loading the stylehseet in this manner allows caching. Bumping the version
 * number of the stylesheet causes the browser to request a fresh copy, removing
 * the need to reset your browser cache and lose all your logins.
 */
function danfoy_2019_styles() {
    wp_register_style( 'danfoy_2019_style', get_template_directory_uri() . '/style.css', array(), '1.0.0' );
    wp_enqueue_style( 'danfoy_2019_style' );
}
add_action( 'wp_enqueue_scripts', 'danfoy_2019_styles' );

/**
 * Load global JavaScript
 *
 * Load scripts which will be used on every page
 *
 */
function danfoy_2019_global_scripts() {
    wp_enqueue_script(
        'modernizr',                                            // Handle
        get_template_directory_uri() . '/js/modernizr.min.js',  // Location
        array(),                                                // Dependencies
        "3.6.0",                                                // Version
        false                                                   // In footer
    );
}
add_action( 'wp_print_scripts', 'danfoy_2019_global_scripts' );


/**
 * Register menu locations
 *
 * Menu locations registered here appear in the WordPress admin interface
 */
function danfoy_2019_menus() {
    register_nav_menus( array(
        'header-menu' => 'header-menu',
    ) );
}
add_action( 'init', 'danfoy_2019_menus' );


/**
 * Remove invalid rel attribute from cateogry lists
 *
 * By default WordPress will add a 'category' rel entry on category lists.
 * The W3C spec has a restricted list of valid rel values, and 'category' isn't
 * one of them. Removing this allows the page to validate to W3C specs.
 *
 * @link    https://www.w3.org/TR/html5/links.html#sec-link-types acceptable rel values
 * @param   string $thelist  The subject to be searched
 * @return  string           New string stripped of 'category' tag
 */
function remove_category_rel_from_category_list( $thelist ) {
    return str_replace( 'rel="category tag"', 'rel="tag"', $thelist );
}
add_filter( 'the_category', 'remove_category_rel_from_category_list' );


/**
 * Remove width and height tags from images
 *
 * Images with width and height attributes in the img tag are a pain to style
 * responsively without hacky !important rules. Remove them from post thumbnails,
 * and stop adding them to new images.
 *
 * @link    https://wpscholar.com/blog/remove-wp-image-size-attributes/ justification
 * @param   string $html    Placeholder for regular expression
 * @return  string          Empty string
 */
function remove_image_dimensions_attributes( $html ) {
    $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
    return $html;
}
// Remove images from thumbnails
add_filter( 'post_thumbnail_html', 'remove_image_dimensions_attributes', 10 );
// Remove images from post content
add_filter( 'image_send_to_editor', 'remove_image_dimensions_attributes', 10 );



/**
 * Remove wrapping paragraph tags from images
 *
 * WordPress wraps images and embeds in paragraph tags by default. This is annoying
 * because it means you can't have full-bleed images whilst having paragraphs with
 * margins or padding.
 *
 * @param  string $content The post content
 * @return string          The post content, where images are stripped of <p>s
 */
function remove_ptags_on_media( $content ){
    // filter from image tags
    $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
    // filter from iframe tags, used for embeds
    return preg_replace('/<p>\s*(<iframe .*>*.<\/iframe>)\s*<\/p>/iU', '\1', $content);
}
add_filter('the_content', 'remove_ptags_on_media', 15);


/**
 * Remove recent comment widget style tag in wp_head
 *
 * The recent comments widget adds its own styles within a style tag hooked to
 * wp_head. It's a mess. I don't use this widget, but I don't want these styles
 * showing up in my head either.
 */
function remove_recent_comments_style() {
    global $wp_widget_factory;
    if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
        remove_action( 'wp_head', array(
            $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
            'recent_comments_style'
        ) );
    }
}
add_action( 'widgets_init', 'remove_recent_comments_style' );


/**
 * Custom pagination
 *
 * WordPress pagination is clunky and is different for archives and 'Page'-type
 * pages. This function generates pagination for either with one function call.
 *
 * paginate_links() by default just returns a string of links. Having a custom
 * function allows returning as an unordered list (for more controllable styling)
 * or as an array of links to feed into a custom walker.
 *
 * @link https://codex.wordpress.org/Function_Reference/paginate_links Codex entry
 */
function danfoy_2019_paginate() {

    // Outside the loop, so need some globals
    global $wp_query;
    global $numpages;

    // For Archive pages:
    if ( $wp_query->max_num_pages > 0 ) {
        echo '<nav class="pagination">';

            // Varibles for checking for posts. Order is opposite what you might expect.
            $prev_page = get_previous_posts_link('&larr; Newer');   // 'back' link
            $next_page = get_next_posts_link('Older &rarr;');       // 'forward' link

            if ( $prev_page || $next_page ); {
                    $firstpage = is_paged() ? '' : ' pagination-first';
                echo '<ul class="pagination-text">';
                    if ( $prev_page )
                        echo '<li class="pagination-text-item pagination-previous">' . $prev_page . '</li>';
                    if ( $next_page )
                        echo '<li class="pagination-text-item pagination-next' . $firstpage . '">' . $next_page . '</li>';
                echo '</ul>'; // /.pagination-text
            }

            $bigint = 99999999;
            echo paginate_links( array(
                'format'        => '/page/%#%',
                'type'          => 'list',
                'prev_next'     => false, // don't show text links - handled above instead
                // where a query returns more items than fit within the posts-per-page option:
                'base'          => str_replace( $bigint, '%#%', get_pagenum_link( $bigint ) ),
                'current'       => max( 1, get_query_var( 'paged' ) ),
                'total'         => $wp_query->max_num_pages,
            ) );

        echo '</nav>'; // /.pagination
    }

    // For paginated Pages
    if ( is_singular() && $numpages > 1 ) {
        echo '<nav class="pagination">';

        wp_link_pages( array(
            'before'           => '<ul class="pagination-text"><li class="pagination-text-item">',
            'separator'        => '</li><li class="pagination-text-item">',
            'after'            => '</li></ul>',
            'link_before'      => '',                   // Text inside anchor element
            'link_after'       => '',                   // Text inside anchor element
            'next_or_number'   => 'next',               // Choose type of list to generate
            'nextpagelink'     => 'Next Page &rarr;',   // Can't isolate first page, so add label for clarity
            'previouspagelink' => '&larr;',
            'echo'             => 1
        ) );

        wp_link_pages( array(
            // Unfortunately have to use two sets of .page-numbers to match built-in functionality
            'before'           => '<ul class="page-numbers"><li class="page-numbers">',
            'separator'        => '</li><li class="page-numbers">',
            'after'            => '</li></ul>',
            'link_before'      => '',               // Text inside anchor element
            'link_after'       => '',               // Text inside anchor element
            'next_or_number'   => 'number',         // Choose type of list to generate
            'pagelink'         => '%',              // Only used with 'number'
            'echo'             => 1
        ) );

        echo '</nav>'; // /.pagination
    }
}
// add_action( 'init', 'danfoy_2019_paginate' ); // Add our HTML5 Pagination


/**
 * Enable threaded comments
 *
 * Without this, comments appear as one long list.
 */
function enable_threaded_comments() {
    if ( ! is_admin() ) {
        if ( is_singular() AND comments_open() AND ( get_option( 'thread_comments' ) == 1 ) )
            wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'get_header', 'enable_threaded_comments' );


/**
 * Custom output from built-in comment_form() function
 *
 * @param  array $fields    Commnent fields to filter
 * @return array $fields    Filtered comment fields
 */
function danfoy_2019_custom_comment_form ( $fields ) {

    // Get commenter and required variables
    $commenter      = wp_get_current_commenter();
    $required       = get_option( 'require_name_email' );
    $required_label = $required ? '*' : '';
    $required_aria  = $required ? ' aria-required="true"' : '';

    // Create the comment author name input
    $fields['author'] =
        '<label for="author" class="respond-form-label">' .
           'Name' . $required_label .
        '</label>' .
        '<input
            class="respond-form-element respond-form-input-author"
            type="text" name="author"
            id="author"
            placeholder="Name' . $required_label . '"
            value="'. $commenter['comment_author'] . '"' .
            $required_aria . '
        />';

        // Create the comment author email input
        $fields['email'] =
            '<label for="email" class="respond-form-label">' .
                'Email'. $required_label .
            '</label>' .
            '<input
                class="respond-form-element respond-form-input-email"
                type="text"
                name="email"
                id="email"
                placeholder="Email' . $required_label . '"
                value="' . $commenter['comment_author_email'] . '"' .
            $required_aria . '
            />';

        // Create the author website input
        $fields['url'] =
            '<label for="url" class="respond-form-label">
                Website
            </label>
            <input
                class="respond-form-element respond-form-input-website"
                type="text"
                name="url"
                id="url"
                placeholder="Website"
                value="' . $commenter['comment_author_url'] . '"
            />';

        return $fields;
};
add_filter('comment_form_default_fields', 'danfoy_2019_custom_comment_form' );


/**
 * Remove type attribute from stylesheet tag
 *
 * W3 Validator returns a warning if this is included in html5 files
 *
 * @param  string $tag  stylesheet tag
 * @return string       stylesheet tag stripped of type=""
 */
function danfoy_2019_remove_style_type( $tag ) {
    return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", "", $tag );
}
add_filter( 'style_loader_tag', 'danfoy_2019_remove_style_type' );
add_filter( 'script_loader_tag', 'danfoy_2019_remove_style_type' );


/**
 * Disabl
 *
 * https://kinsta.com/knowledgebase/disable-emojis-wordpress/
 */
function danfoy_2019_disable_emojis() {
    add_filter( 'tiny_mce_plugins', 'danfoy_2019_disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints', 'danfoy_2019_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'danfoy_2019_disable_emojis' );


remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );


/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * https://kinsta.com/knowledgebase/disable-emojis-wordpress/
 *
 * @param array $plugins
 * @return array Difference betwen the two arrays
 */
function danfoy_2019_disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
};

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * https://kinsta.com/knowledgebase/disable-emojis-wordpress/
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function danfoy_2019_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' == $relation_type ) {
        /** This filter is documented in wp-includes/formatting.php */
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
        $urls = array_diff( $urls, array( $emoji_svg_url ) );
    }
    return $urls;
}


/**
 * Remove frameborder from oEmbed iFrames
 *
 * Some WordPress embeds (eg. YouTube, Vimeo) are outputted as iFrames with
 * frameborder properties. These are deprecated and fail validation.
 *
 * @param  string $html     The string to filter
 * @return string           String filtered of "frameborder="0"
 */
function danfoy_2019_remove_frameborder( $html ) {
    $html = str_replace( 'frameborder="0"', '', $html );
    return $html;
}
add_filter( 'embed_oembed_html', 'danfoy_2019_remove_frameborder', 10, 2 );


/**
 * Load conditional scripts
 *
 * Some scripts don't need to be run on every page, and page loading time can
 * be improved by loading them conditionally based on whether they are likely
 * to be used by the page.
 *
 * e.g. gallery scripts, syntax highlighting scripts
 */
// function danfoy_2019_conditional_scripts() {
//     if ( is_page( 'pagenamehere' ) ) {
//         // Conditional script(s)
//         wp_register_script( 'scriptname', get_template_directory_uri() . '/js/scriptname.js', array( 'jquery' ), '1.0.0' );
//         wp_enqueue_script( 'scriptname' );
//     }
// }
// add_action( 'wp_print_scripts', 'danfoy_2019_conditional_scripts' ); // Add Conditional Page Scripts


/**
 * Remove built-in actions
 *
 * Below are several built-in actions which for whatever reason WordPress core
 * deems to be good defaults, but which need overriding for the purposes of
 * this theme.
 */
// Remove the links to the extra feeds such as category feeds
remove_action( 'wp_head', 'feed_links_extra', 3 );

// Remove the link to the Windows Live Writer manifest file.
// Seriously who uses Windows Live Writer
remove_action( 'wp_head', 'wlwmanifest_link' );

// Remove the generator meta tag from the head
// This is a security risk, bots use this to target known vunerabilities
remove_action( 'wp_head', 'wp_generator' );

// Remove paragraph tags from excerpts
// Needed because I'm adding a custom link to the end of each one
// remove_filter( 'the_excerpt', 'wpautop' );
