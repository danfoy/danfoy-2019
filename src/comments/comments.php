<?php
/**
 * Comments template
 *
 * @package df19
 * @author 	danfoy <danfoy.com>
 * @since 	1.0.0
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
 */
if ( have_comments() || comments_open() ) {
?>
	<aside class="post-comments" id="comments">
		<?php

		if ( have_comments() ) {

			$comment_count = get_comments( array(
				'status' => 'approve',
				'post_id'=> get_the_ID(),
				'type'=> 'comment',
				'count' => true)
			 );

			$ping_count = get_comments( array(
				'status' => 'approve',
				'post_id'=> get_the_ID(),
				'type'=> 'pings',
				'count' => true)
			 );

			if ( $comment_count ) {
				// A manual comments_number() which doesn't count pings
				$comment_title = $comment_count > 1 ? $comment_count . ' comments' : $comment_count . ' comment';
				echo "<h2 class=\"post-comments-title\">$comment_title</h2>\n";
				echo "<ol class=\"post-comments-list\">\n";
					wp_list_comments( array(
						'walker'			=> null,
						'max_depth'			=> '',
						'style'				=> 'ol',
						'callback'			=> null,
						'end-callback'		=> null,
						'type'				=> 'comment',
						'reply_text'		=> '<small>Reply &darr;</small>',
						'page'				=> '',
						'per_page'			=> '',
						'avatar_size'		=> 64,
						'reverse_top_level'	=> null,
						'reverse_children'	=> '',
						'format'			=> 'html5',
						'echo'				=> true,
						)
					);
				echo "</ol>\n";
			};

			if ( $ping_count ) {
				// A manual comments_number() which *only* counts pings
				$ping_title = $ping_count > 1 ? $ping_count . ' pingbacks' : $ping_count . ' pingback';
				echo "<h2 class=\"post-comments-title\">$ping_title</h2>\n";
				echo "<ol class=\"post-comments-list post-pingbacks-list\">\n";
					wp_list_comments( array(
						'style'				=> 'ol',
						'type'				=> 'pings',
						'format'			=> 'html5',
						'short_ping'		=> true,
						)
					);
				echo "</ol>\n";
			}
		}
		?>

		<?php

		// comment_form( array(
		// 	'id_form'				=> 'commentform',
		// 	'class_form'			=> 'comment-form',
		// 	'id_submit'				=> 'submit',
		// 	'class_submit'			=> 'submit',
		// 	'name_submit'			=> 'submit',
		// 	'title_reply'			=> 'Leave a Reply',
		// 	'title_reply_to'		=> __( 'Leave a Reply to %s' ),
		// 	'cancel_reply_link'		=> 'Cancel Reply',
		// 	'label_submit'			=> 'Post Comment',
		// 	'format'				=> 'html5',
		// 	'comment_field'			=> '<p class="comment-form-"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" name="comment" aria-required="true">' . '</textarea></p>',
		// 	'must_log_in'			=> '<p class="comment-form-login-link">' . sprintf(__( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )) . '</p>',
		// 	'logged_in_as'			=> '<p class="comment-form-user">' . sprintf(__( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ),admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
		// 	'comment_notes_before'	=> '<p class="comment-form-above-textarea">' . __( 'Your email address will not be published.' ) . ( $req ? '(required)' : '' ) . '</p>',
		// 	'comment_notes_after'	=> '<p class="comment-form-below-textarea">Markdown and some HTML allowed</p>',
		// 	// 'fields'				=> apply_filters( 'comment_form_default_fields', $fields ),
		// 	)
		// );
		?>


		<?php
		/**
		 * Comment form
		 */

		if ( comments_open() ) {

		?>

			<div id="respond" class="comment-respond">

				<?php

				if ( get_option( 'comment_registration' ) && ! $user_ID ) {

				?>
					<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>
				<?php

				} else {

				?>
					<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" class="comment-form">
						<h3 class="comment-form-title">
							<?php

							comment_form_title( 'Leave a Comment', 'Replying to %s' );
							echo " ";
							cancel_comment_reply_link( '&#10006;' );

							?>
						</h3>

						<textarea class="comment-form-element comment-input-textarea" name="comment" id="comment" placeholder="Leave a comment"></textarea>
						<?php

						// User info for logged-in users
						// (just me then I guess)
						if ( $user_ID ) {

						?>
							<ul class="comment-meta">
								<li class="comment-author"><a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a> <?php $current_user = wp_get_current_user(); $useremail = $current_user->user_email; echo get_avatar($useremail, 64); ?></li>
								<li><time datetime="<?php echo date('c'); ?>" class="post-date"><?php echo date('j M Y'); ?></time></l64
							</ul>
						<?php

						// Login form for everyone else
						} else {

						?>
							<div class="comment-input-author-details">
								<input class="comment-form-element comment-input-author" type="text" name="author" id="author" placeholder="Name <?php if($req) echo "(required)"; ?>" value="<?php echo $comment_author; ?>">
								<input class="comment-form-element comment-input-email" type="text" name="email" id="email" placeholder="Email (not published) <?php if($req) echo "(required)"; ?>" value="<?php echo $comment_author_email; ?>">
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
				}
				?>
			</div>
		<?php

	};

	?>

	</aside>
<?php
}
?>