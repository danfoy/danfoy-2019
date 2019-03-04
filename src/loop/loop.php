<?php
/**
 * General loop template for danfoy_2019 WordPress theme
 *
 * This is the loop which gets used by default. It is kept vague to be a
 * general-purpose loop which functions in edge-case scenarios, such as landing
 * on a page which isn't linked to but which WordPress will happily generate
 * regardless.
 *
 * @package     danfoy_2019
 * @subpackage  loop
 * @author      Dan Foy <danfoy.com>
 * @since       1.0.0
 */

// Check for posts
if ( have_posts() ) :

    // Loop through posts
    while (have_posts()) :

        // Setup post data
        the_post();
        ?>
        <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
            <header class="post-header">

                <?php

                /**
                 * Open .post-title tag
                 *
                 * For semantics reasons, these are <h1> headings on standalone
                 * pages, and links inside <h2> headings on archives.
                 *
                 */
                if ( is_singular() ) {  // Single post/page pages
                    echo '<h1 class="post-title">' . get_the_title() . "</h1> \n";
                //
                } else {                // On archives
                    echo '<h2 class="post-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">';
                        the_title();
                    echo "</a></h2> \n";
                };

                ?>

            </header>

            <div class="post-content">

                <?php
                // The media library on my dev install is broken, turning this off fow now.
                /*
                if ( has_post_thumbnail() ) { ?>
                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" title="Go to <?php the_title_attribute(); ?>">
                        <?php the_post_thumbnail( ); ?>
                    </a>
                <?php
                }
                */
                ?>

                <?php

                // Site looks odd with excerpts and no featured images

                // if ( is_home() || is_archive() ) {
                //  // Show an excerpt with a 'read post' link on archive pages
                //  the_excerpt();
                //  echo '<p><a class="post-excerpt-link" href="'
                //      . get_permalink() . '" title="Go to '
                //      . the_title_attribute( array( 'echo' => false ) )
                //      . '">Read post &rarr;</a></p>';
                // }
                // else {
                //  // Show full post content on other pages
                //  the_content();
                // }
                //
                the_content();
                ?>
            </div>

            <?php
            if ( ! is_page() ) { // No footer for pages
                ?>
                <footer class="post-footer">
                    <h2 class="post-footer-title">Post Metadata</h2>

                    <ul class="post-meta">
                        <li class="post-meta-date">
                            <span class="post-meta-date-title">Posted on</span>
                            <?php
                            echo '<time datetime="' . get_the_time( 'Y-m-d' ) . 'T' . get_the_time( 'H:i' ) . '">';
                                the_time( get_option( 'date_format' ) );
                            echo '</time>' . "\n";
                            ?>
                        </li>

                        <?php
                        if ( get_comments_number() ) { ?>
                            <li class="post-meta-comments">
                                <a class="post-meta-comments-link" href="<?php comments_link() ?>"><?php
                                    comments_number(
                                        '0 responses',
                                        '1 response',
                                        '% responses' ); ?>
                                </a>
                            </li>

                        <?php
                        };

                        // Check whether the post actually has an taxonomy meta
                        if ( get_the_category() || get_the_tags() ) { ?>
                            <li><h3 class="post-meta-taxonomies-title">Taxonomies</h3>
                                <div class="post-meta-taxonomies-wrapper">
                                    <ul class="post-meta-taxonomies">
                                        <?php

                                        // Check for categories
                                        if ( get_the_category() ) {
                                            echo '<li class="post-meta-categories">' . "\n";
                                            echo '<h4 class="post-meta-categories-title">Categories</h4>' . "\n";
                                                echo '<ul class="post-meta-categories-list">'. "\n";
                                                    echo '<li>';
                                                        the_category('</li>' . "\n" . '<li>', 'multiple');
                                                    echo '</li>' . "\n";
                                                echo '</ul>' . "\n";
                                            echo '</li>' . "\n";
                                        };

                                        // Check for tags
                                        if ( get_the_tags() ) {
                                            echo '<li class="post-meta-tags">' . "\n";
                                                echo '<h4 class="post-meta-tags-title">Tags</h4>';
                                                the_tags(
                                                    '<ul class="post-meta-tags-list">' . "\n" . '<li>',
                                                    "</li>\n<li>",
                                                    "</li>\n</ul>\n"
                                                );
                                            echo '</li>' . "\n";
                                        };

                                        ?>
                                    </ul>
                                </div>
                            </li>
                        <?php
                        }

                        ?>
                    </ul>

                </footer>

                <?php

                comments_template();

            }

            ?>

        </article>

    <?php
    endwhile; // End the loop
    ?>

    <?php
    danfoy_2019_paginate();
    ?>

<?php
else: // If no posts are found:
?>

    <article>
        <h2>Sorry, nothing to display</h2>
    </article>

<?php
endif;
