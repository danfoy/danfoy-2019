<?php
/**
 * Comments template
 *
 * @package     df19
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

            echo '<h3 class="post-pings-title">Incoming links</h3>' . "\n";

            echo '<ol class="post-pings-list">' . "\n";
                wp_list_comments( array(
                    'style'             => 'ol',
                    'type'              => 'pings',
                    'format'            => 'html5',
                    'short_ping'        => true,
                    )
                );
            echo '</ol>' . "\n";
        };

        /**
         * Display comments
         */
        if ( $comment_count ) {

            echo '<h3 class="post-comments-title">Comments</h3>' . "\n";
            echo '<ol class="post-comments-list">' . "\n";
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
        };


        // comment_form( array(
        //  'id_form'               => 'commentform',
        //  'class_form'            => 'comment-form',
        //  'id_submit'             => 'submit',
        //  'class_submit'          => 'submit',
        //  'name_submit'           => 'submit',
        //  'title_reply'           => 'Leave a Reply',
        //  'title_reply_to'        => __( 'Leave a Reply to %s' ),
        //  'cancel_reply_link'     => 'Cancel Reply',
        //  'label_submit'          => 'Post Comment',
        //  'format'                => 'html5',
        //  'comment_field'         => '<p class="comment-form-"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" name="comment" aria-required="true">' . '</textarea></p>',
        //  'must_log_in'           => '<p class="comment-form-login-link">' . sprintf(__( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )) . '</p>',
        //  'logged_in_as'          => '<p class="comment-form-user">' . sprintf(__( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ),admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
        //  'comment_notes_before'  => '<p class="comment-form-above-textarea">' . __( 'Your email address will not be published.' ) . ( $req ? '(required)' : '' ) . '</p>',
        //  'comment_notes_after'   => '<p class="comment-form-below-textarea">Markdown and some HTML allowed</p>',
        //  // 'fields'             => apply_filters( 'comment_form_default_fields', $fields ),
        //  )
        // );
        ?>


        <?php
        /**
         * Comment form
         */

        if ( comments_open() ) {

        ?>

            <div id="respond" class="respond">

                <?php

                // Show login link if required
                if ( get_option( 'comment_registration' ) && ! $user_ID ) {

                ?>
                    <p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>
                <?php

                // Otherwise show comment form
                } else {

                ?>
                    <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" class="comment-form">
                        <h3 class="comment-form-title">
                            Leave a Comment
                        </h3>
                        <?php

                        // Add some hidden form fields which enable nested comments
                        comment_id_fields();

                        // User info for logged-in users
                        // (just me then I guess)
                        if ( $user_ID ) {

                            $current_user = wp_get_current_user();
                            $user_email = $current_user->user_email;
                            $user_handle = $current_user->display_name;

                            ?>
                            <div class="comment-meta">
                                <div class="comment-author">
                                    <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php">
                                        <?php

                                        // To match built-in structure
                                        echo '<b class="fn">';
                                            echo $user_handle;
                                        echo '</b>' . "\n";

                                        echo get_avatar($user_email, 64);

                                        ?>
                                    </a>
                                </div>
                                <div class="comment-metadata">
                                    <?php

                                    comment_form_title( date('j F Y'), "Replying to %s" );
                                    echo " ";
                                    cancel_comment_reply_link( 'Cancel reply' );

                                    ?>
                                </div>
                            </div>
                        <?php

                        }; // ? logged-in

                        ?>
                        <label for="comment" class="comment-form-label">Comment</label>
                        <textarea class="comment-form-element comment-input-textarea" name="comment" id="comment" placeholder="Type here to leave a comment"></textarea>
                        <?php

                        // Comment author meta section
                        // (when not logged in to WordPress)
                        if ( ! $user_ID ) {

                        ?>
                            <div class="comment-metadata">
                            <?php cancel_comment_reply_link( 'Cancel reply' ); ?>
                            </div>

                            <div class="comment-input-author-details">

                                <label for="author" class="comment-form-label">Name<?php if($req) echo '<span class="required">*</a>'; ?></label>
                                <input class="comment-form-element comment-input-author" type="text" name="author" id="author" placeholder="Name<?php if($req) echo "*"; ?>" value="<?php echo $comment_author; ?>" />

                                <label for="email" class="comment-form-label">Email<?php if($req) echo '<span class="required">*</a>'; ?></label>
                                <input class="comment-form-element comment-input-email" type="text" name="email" id="email" placeholder="Email<?php if($req) echo '*'; ?>" value="<?php echo $comment_author_email; ?>">

                                <label for="url" class="comment-form-label">Website</label>
                                <input class="comment-form-element comment-input-website" type="text" name="url" id="url" placeholder="Website" value="<?php echo $comment_author_url; ?>">

                            </div>
                        <?php

                        }
                        ?>


                        <input class="comment-form-element comment-input-submit" name="submit" type="submit" id="submit" value="Submit Comment">
                        <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>">

                        <?php

                        // Call comment form via an action. This makes it hookable
                        // for JetPack and Disqus and things like that.
                        do_action( 'comment_form', $post->ID );

                        ?>
                    </form>
                <?php

                }; // ? login not required

                ?>
            </div>
        <?php

        }; // ? comments_open

        // Show a message if comments are closed but comments exist
        if ( ! comments_open() && have_comments() ) {
            echo '<p class="respond-closed">Comments are closed</p>' . "\n";
        }

        ?>
    </aside>
<?php

}
