<div class="su-posts su-posts-teaser-loop">
	<?php
		// Posts are found
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) :
				$posts->the_post();
				global $post;
				?>
				<div id="su-post-<?php the_ID(); ?>" class="su-post">
                                        <?php
                                            echo do_shortcode( '[su_spoiler icon="plus-circle" title="' . get_the_title() . '"]'. get_the_content() .'[/su_spoiler]' );
                                        ?>
				</div>

				<?php
			endwhile;
		}
		// Posts not found
		else {
			echo '<h4>' . __( 'Posts not found', 'shortcodes-ultimate' ) . '</h4>';
		}
	?>
</div>