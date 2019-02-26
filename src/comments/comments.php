<?php
/**
 * Comments template
 *
 * @package     danfoy_2019
 * @subpackage  comments
 * @author      Dan Foy <danfoy.com>
 * @since       1.0.0
 */

/**
 * Conditionally reveal the comments section
 *
 * Doing the condtional checks here means that I can use comments_template()
 * without worrying if the post type actually accepts comments.
 *
 * The section should show if comments exist. It should also show if comments
 * are open, but if comments are closed and there are none to show it just
 * gets skipped entirely.
 *
 */
if ( have_comments() || comments_open() ) {

    /**
     * Get the comment and ping counts
     */
    // Count comments
    $comment_count = get_comments( array(
        'status' => 'approve',
        'post_id'=> get_the_ID(),
        'type'=> 'comment',
        'count' => true)
     );
    // Count pings
    $ping_count = get_comments( array(
        'status' => 'approve',
        'post_id'=> get_the_ID(),
        'type'=> 'pings',
        'count' => true)
     );
    // String containing supplementary classes for aside.post-comments
    $custom_post_comments_class  = $comment_count || $ping_count
        ? ' has-responses'
        : ' no-responses';
    $custom_post_comments_class .= $comment_count
        ? ' has-comments'
        : ' no-comments';
    $custom_post_comments_class .= $ping_count
        ? ' has-pings'
        : ' no-pings';

    ?>
    <aside class="responses<?php echo $custom_post_comments_class; ?>" id="comments">
        <h2 class="responses-title"><?php comments_number( 'No reponses', '% response', '% responses' ); ?></h2>
        <?php


        /**
         * Display pingbacks
         */
        if ( $ping_count ) {
            echo '<div class="responses-pings">' . "\n";
                echo '<h3 class="responses-pings-title">Incoming links</h3>' . "\n";
                echo '<ol class="responses-pings-list">' . "\n";
                    wp_list_comments( array(
                        'style'             => 'ol',
                        'type'              => 'pings',
                        'format'            => 'html5',
                        'short_ping'        => true,
                        )
                    );
                echo '</ol>' . "\n";
            echo '</div>' . "\n";
        };

        /**
         * Display comments
         */
        if ( $comment_count ) {
            echo '<div class="responses-comments">' . "\n";
                echo '<h3 class="responses-comments-title">Comments</h3>' . "\n";
                echo '<ol class="responses-comments-list">' . "\n";
                    wp_list_comments( array(
                        // 'walker'         => null,
                        'max_depth'         => '',
                        'style'             => 'ol',
                        // 'callback'           => null,
                        // 'end-callback'       => null,
                        'type'              => 'comment',
                        'reply_text'        => '<small>Reply</small>',
                        // 'page'               => '',
                        // 'per_page'           => '',
                        'avatar_size'       => 64,
                        // 'reverse_top_level'  => null,
                        // 'reverse_children'   => '',
                        'format'            => 'html5',
                        'echo'              => true,
                        )
                    );
                echo '</ol>' . "\n";
            echo '</div>' . "\n";
        };


        /**
         * Generate the comment form
         *
         * WordPress theme guidelines dictate that themes should use the built-in comment generating functionality. That
         * seems like a very opinionated decison, but I suppose it does make things easier for plugins.
         */

        // Grab some variables first. A secret tool which will help us later!
        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;
        $user_handle = $current_user->display_name;

        // Generate the comment form using WordPress's built-in function, as per guidelines
        comment_form( array(
            'format'                => 'html5',

            'id_form'               => 'commentform',
            'class_form'            => 'respond-form',
            'id_submit'             => 'submit',

            'name_submit'           => 'submit',
            'class_submit'          => 'submit respond-form-element',
            'label_submit'          => 'Post Comment',

            'title_reply_before'    => '<div class="respond-cancel-reply">',
            'title_reply'           => '', // Repurposing for its nested cancel reply link
            'title_reply_to'        => 'Replying to %s',
            'cancel_reply_link'     => 'Cancel Reply',
            'title_reply_after'     => '</div>',

            'must_log_in'           => '<p class="respond-form-login-link">' .
                                    sprintf(__( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )) .
                                    '</p>',

            'logged_in_as'        => '<div class="comment-meta">
                                            <div class="comment-author">
                                                <a href="' . get_option('siteurl') . '/wp-admin/profile.php">
                                                    <b class="fn">' . $user_identity . '</b>
                                                    ' . get_avatar($user_email, 64) . '
                                                </a>
                                            </div>
                                            <div class="comment-metadata">
                                                ' . date('j F Y') . '
                                            </div>
                                        </div>',

            'comment_field'         => '<label for="comment" class="respond-form-label">Comment</label>' . "\n" .
                                    '<textarea id="comment" class="respond-form-element respond-form-input-comment" name="comment" placeholder="Add your thoughts here" aria-required="true">' . "\n" .
                                    '</textarea>' . "\n",

            'comment_notes_before'  => '',
            'comment_notes_after'   => '',
            )
        );


        // Show a message if comments are closed but comments exist
        if ( ! comments_open() && have_comments() ) {
            echo '<p class="respond-closed">Comments are closed</p>' . "\n";
        }

        ?>
    </aside>
<?php

}
