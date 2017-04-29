<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package F4D
 * @since F4D 1.0
 */
 
 $pdfurl = esc_attr( get_post_meta( $post->ID, 'pdflink', true ) );
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( is_sticky() && is_home() && ! is_paged() ) { ?>
			<div class="featured-post">
				<?php esc_html_e( 'Featured post', f4d ); ?>
			</div>
		<?php } ?>
		<header class="entry-header">
			<?php if ( is_single() ) { ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
                <div class="header-meta"><time class="entry-date" datetime="<?php echo get_the_date( 'c' ); ?>" itemprop="datePublished">
				<?php 
				$date = get_the_date( 'F d, Y' ); 
				$date = str_replace(',', '', $date);
				$arr = explode(' ',trim($date));
				echo '<span class="cal">';
				echo '<span class="month">' . $arr[0] . '</span>';
				echo '<span class="btmcal"><span class="day">' . $arr[1] . '</span>';
				echo '<span class="year">' . $arr[2] . '</span></span>';
				echo '</span>';
				?>
                </time><div class="header-meta-inner"><?php if( $pdfurl != '' ) {
			$pdf = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a target="_blank" href="' . $pdfurl . '">Download the PDF</a> '; 
			echo $pdf;
	} 
	else {
		
	}
		?></div>
        
        </div>
			<?php }
			else { ?>
				<h1 class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to ', f4d ) . '%s', the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h1>
			<?php } // is_single() ?>

		</header> <!-- /.entry-header -->

		<?php if ( is_search() ) { // Only display Excerpts for Search ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div> <!-- /.entry-summary -->
		<?php }
		else {  
		?>
			<div class="entry-content"><div class="cat-left">
          <?php  if ( has_post_thumbnail() && is_category() ) { 
		  if( $pdfurl != '' ) {
		  $pdf = '<div class="cat-pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a target="_blank" href="' . $pdfurl . '">Download the PDF</a> </div>'; 
			echo $pdf;}?>
				<a class="cat-thumb" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to ', f4d ) . '%s', the_title_attribute( 'echo=0' ) ) ); ?>">
					<?php the_post_thumbnail(  ); ?>
				</a></div>
			<?php 
		}?>
				<?php the_content( wp_kses( __( 'Continue reading <span class="meta-nav">&rarr;</span>', f4d ), array( 'span' => array( 
					'class' => array() ) ) )
					); ?>
				<?php wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', f4d ),
					'after' => '</div>',
					'link_before' => '<span class="page-numbers">',
					'link_after' => '</span>'
				) ); ?>
			</div> <!-- /.entry-content -->
		<?php } ?>

		<footer class="entry-meta">
			<?php if ( is_singular() ) {
				// Only show the tags on the Single Post page
				f4d_entry_meta();
			} ?>
			<?php edit_post_link( esc_html__( 'Edit', f4d ) . ' <i class="fa fa-angle-right"></i>', '<div class="edit-link">', '</div>' ); ?>
			<?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) {
				// If a user has filled out their description and this is a multi-author blog, show their bio
				get_template_part( 'author-bio' );
			} ?>
		</footer> <!-- /.entry-meta -->
	</article> <!-- /#post -->
