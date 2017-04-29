<?php
/**
 * The template for displaying posts in the Aside post format
 *
 * @package F4D
 * @since F4D 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php f4d_posted_on(); ?>
	</header> <!-- /.entry-header -->
	<div class="entry-content">
		<?php the_content( wp_kses( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'f4d' ), array( 
			'span' => array( 
				'class' => array() )
			) ) ); ?>
            
            <?php

// check if the repeater field has rows of data
if( have_rows('rows') ):

 	// loop through the rows of data
    while ( have_rows('rows') ) : the_row();
	
		$Hex_color = get_sub_field('background_color');
		$bg_opacity = get_sub_field('opacity');
		
		if ($Hex_color != '') {
		$RGB_color = hex2rgb($Hex_color);
		$Final_Rgb_color = implode(", ", $RGB_color);
		}
		
		if ($bg_opacity == '') {
			$bg_opacity = '0';
		}
	
		$bgimage = get_sub_field('background_image');
		
		if ($bgimage != '' && $RGB_color == '') {
			echo '<div class="row hasbg" style="background-image: url(' . $bgimage . '); background-size: cover; background-position: center;">';
        	// display a sub field value
        	the_sub_field('row');
			echo '</div>';
		}
		
		elseif ($bgimage != '' && $RGB_color != '') {
			echo '<div class="row hasbg" style="background-image: url(' . $bgimage . '); background-size: cover; background-position: center;">';
			echo '<div style="background-color: rgba('. $Final_Rgb_color .', ' . $bg_opacity . ');">';
        	
			// display a sub field value
        	the_sub_field('row');
			
			echo '</div>';
			echo '</div>';
		}
		
		else {
		echo '<div class="row">';
        // display a sub field value
        the_sub_field('row');
		echo '</div>';
		}
		
    endwhile;

else :

    // no rows found

endif;

?>
		<?php wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'f4d' ),
			'after' => '</div>',
			'link_before' => '<span class="page-numbers">',
			'link_after' => '</span>'
		) ); ?>
	</div> <!-- /.entry-content -->

	<footer class="entry-meta">
		<?php if ( is_singular() ) {
			// Only show the tags on the Single Post page
			f4d_entry_meta();
		} ?>
		<?php edit_post_link( esc_html__( 'Edit', 'f4d' ) . ' <i class="fa fa-angle-right" aria-hidden="true"></i>', '<div class="edit-link">', '</div>' ); ?>
		<?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) {
			// If a user has filled out their description and this is a multi-author blog, show their bio
			get_template_part( 'author-bio' );
		} ?>
	</footer> <!-- /.entry-meta -->
</article> <!-- /#post -->
