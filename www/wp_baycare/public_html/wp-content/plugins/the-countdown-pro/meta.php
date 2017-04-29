<?php
/*
	The Countdown Pro Meta
	@since 1.1
	
	Copyright 2014 zourbuth.com (email: zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class The_Countdown_Pro_Meta {

	var $textdomain;
	var $slug;
	var $version;
	
	public function __construct() {
		$this->slug = THE_COUNTDOWN_PRO_SLUG;
		$this->textdomain = THE_COUNTDOWN_PRO_LANG;
		$this->version = THE_COUNTDOWN_PRO_VERSION;
		
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'callback_head' ) );
		add_action( 'wp_ajax_the_countdown_pro_generate_callback', array( $this, 'generate_callback' ) ); // since 1.1
	}



	/**
	 * Creating the metabox
	 * Check if the current user can edit post or other post type
	 * Add the meta box if current custom post type is selected
	 * add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
	 * @param $key, 'side', 'high' for custom position
	 * @since 1.1
	**/
	function add_meta_box() {
		if( ! current_user_can( 'edit_others_posts' ) )
			return;

		$options = get_option( 'the_countdown_pro' );
		
		if ( isset( $options['cpt'] ) && is_array( $options['cpt'] ) ) {
			foreach( $options['cpt'] as $key => $post_type ) {  // array ( [0] => post ) 
				add_meta_box( 'the-countdown-pro-meta-box', __( 'Countdown Pro', $this->textdomain ), array( $this, 'meta_box' ), $post_type, 'normal', 'high' );
			}
		}
	}

	/**
	 * Creating the metabox fields
	 * We don't find any match to use the fields as a global variable, manually but best at least for now
	 * Using the name field [] for array results
	 * @param string $post_id
	 * @since 1.1
	**/
	function meta_box() {
		global $post, $post_id, $wp_registered_sidebars;
		$meta = get_post_meta( $post_id, 'the_countdown_pro', true );
		$options = get_option( 'the_countdown_pro' );

		$callbacks = array(
			'none' 				=> __( 'No Callback', $this->textdomain ),
			'lightbox' 			=> __( 'Lightbox', $this->textdomain ),
			'hide-content' 		=> __( 'Hide Content', $this->textdomain ),
			'show-content' 		=> __( 'Show Content', $this->textdomain ),
			'redirect' 			=> __( 'Redirect', $this->textdomain )
		);

		echo '<div class="total-options tabbable tabs-left">';
		// Create nonces
		echo '<input type="hidden" name="tcp_nonce" id="tcp_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

		$tabs = isset( $meta['tab'] ) ? $meta['tab'] : array( 'general' => 1, 'advanced' => 0 );
		
		$tabname = array( 
			'general'	=> __( 'General', $this->textdomain ),
			'advanced'	=> __( 'Advanced', $this->textdomain )
		);
		$tabname = apply_filters( 'tcp_addon_meta_tab', $tabname ); // for addons tab

		echo '<ul class="nav nav-tabs">';
			foreach ( $tabname as $key => $tab ) {
				$val = isset( $tabs[$key] ) ? $tabs[$key] : 0;
				$active[$key] = $val ? 'active' : '';
				echo "<li class='{$active[$key]}'>$tab<input type='hidden' name='tcp[tab][$key]' value='$val' /></li>";
			}
		echo '</ul>';
		
		// Create meta fields
		echo '<ul class="tab-content">';
			
			echo '<li class="tab-pane ' . $active['general'] . '">';
				echo '<ul>';
					
					echo '<li>';
						echo '<label for="tcp[callback]">' . __( 'Callback', $this->textdomain ) . '</label>';
						echo '<span class="description">' . __( 'Select the callback below for different action.', $this->textdomain ) . '</span>';
						echo '<select id="tcp[callback]" name="tcp[callback]">';
							foreach ( $callbacks as $key => $value )
								echo "<option value='$key'" . selected( $meta['callback'], $key, false ) . '>' . $value . '</option>';
						echo '</select>';
					echo '</li>';
					
					echo '<ul id="callback-wrapper"></ul>';
					
				echo '</ul>';
			echo '</li>';
			
			echo '<li class="tab-pane ' . $active['advanced'] . '">';
				echo '<ul>';
					echo '<li>';
						$custom = isset($meta['custom']) ? $meta['custom'] : '';
						echo '<label for="tcp[bg_image]">' . __( 'Custom Style Script', $this->textdomain ). '</label>';
						echo "<textarea class='widefat' id='tcp[custom]' name='tcp[custom]' rows='3'>$custom</textarea>";				
						echo '<span class="description">' . __( 'Use this option to push custom syles or script to the header.', $this->textdomain ) . '</span>';
					echo '</li>';
				echo '</ul>';
			echo '</li>';
			
			do_action('tcp_addon_meta_content', $active, $meta); // for addons tab content
			
		echo '</ul>';
		
		echo '</div>';
	}


	function generate_callback() {

		// Check the nonce and if not isset the id, just die
		// not best, but maybe better for avoid errors
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'the-countdown-pro' ) && !isset($_POST['id']) && !isset($_POST['callback']) )
			die();

		$id		  = $_POST['id'];
		$callback = $_POST['callback'];
		
		$meta = get_post_meta($id, 'the_countdown_pro', true);

		switch ( $callback ) {		
			
			case 'lightbox':
						
				echo '<li>';
					$interval = isset($meta['interval']) ? $meta['interval'] : 5;
					echo '<label for="tcp[interval]">' . __( 'Interval', $this->textdomain ) . '</label>';
					echo "<input id='tcp[interval]' name='tcp[interval]' type='text' value='$interval' />";				
					echo '<span class="description">' . __( 'The interval for lightbox to show in second', $this->textdomain ) . '</span>';
				echo '</li>';
				
				echo '<li>';
					$title = isset($meta['title']) ? $meta['title'] : '';
					echo '<label for="tcp[title]">' . __( 'Title', $this->textdomain ) . '</label>';
					echo "<input id='tcp[title]' name='tcp[title]' type='text' value='$title' />";				
					echo '<span class="description">' . __( 'The lightbox title.', $this->textdomain ) . '</span>';
				echo '</li>';
				
				echo '<li>';
					$description = isset($meta['description']) ? $meta['description'] : '';
					echo '<label for="tcp[description]">' . __( 'Description', $this->textdomain ) . '</label>';
					echo "<input class='widefat' id='tcp[description]' name='tcp[description]' type='text' value='$description' />";
					echo '<span class="description">' . __( 'The lightbox description.', $this->textdomain ) . '</span>';
				echo '</li>';

				echo '<li>';
					$link = isset($meta['link']) ? $meta['link'] : '';
					echo '<label for="tcp[link]">' . __( 'Content Link', $this->textdomain ). '</label>';
					echo "<input class='widefat' id='tcp[link]' name='tcp[link]' type='text' value='$link' />";
					echo '<span class="description">' . __( 'The lightbox link for content. You can insert a link to image or video like youtube or vimeo.
						  <br /> Example: http://vimeo.com/70323400', $this->textdomain ) . '</span>';
				echo '</li>';	
				
				echo '<li>';
					$html = isset($meta['html']) ? $meta['html'] : '';
					echo '<label for="tcp[html]">' . __( 'Content HTML', $this->textdomain ). '</label>';
					echo "<textarea class='widefat' id='tcp[html]' name='tcp[html]' >$html</textarea>";				
					echo '<span class="description">' . __( 'The lightbox content, you can insert a HTML content here. Please remove or let empty the link above to use this option.', $this->textdomain ) . '</span>';
				echo '</li>';			
			break;	
			
			case 'hide-content':
			case 'show-content':
			
				echo '<li>';
					$interval = isset($meta['interval']) ? $meta['interval'] : 5;
					echo '<label for="tcp[interval]">' . __( 'Interval', 'the-countdown-pro' ) . '</label>';
					echo "<input id='tcp[interval]' name='tcp[interval]' type='text' value='$interval' />";				
					echo '<span class="description">' . __( 'The interval for hiding content in seconds.', $this->textdomain ) . '</span>';
				echo '</li>';	
				
				echo '<li>';
					$html = isset($meta['html']) ? $meta['html'] : '';
					echo '<label for="tcp[html]">' . __( 'Content Replacement', 'the-countdown-pro' ). '</label>';
					echo "<textarea class='widefat' id='tcp[html]' name='tcp[html]' >$html</textarea>";				
					echo '<span class="description">' . __( 'The replacement content, you can insert a HTML content here.', $this->textdomain ) . '</span>';
				echo '</li>';			
			break;
			
			case 'redirect':
			
				echo '<li>';
					$interval = isset($meta['interval']) ? $meta['interval'] : 5;
					echo '<label for="tcp[interval]">' . __( 'Interval', 'the-countdown-pro' ) . '</label>';
					echo "<input id='tcp[interval]' name='tcp[interval]' type='text' value='$interval' />";				
					echo '<span class="description">' . __( 'The interval for hiding content in seconds.', $this->textdomain ) . '</span>';
				echo '</li>';	
				
				echo '<li>';
					$link = isset($meta['link']) ? $meta['link'] : '';
					echo '<label for="tcp[link]">' . __( 'Content Link', 'the-countdown-pro' ). '</label>';
					echo "<input class='widefat' id='tcp[link]' name='tcp[link]' type='text' value='$link' />";
					echo '<span class="description">' . __( 'Insert a link to image, web page or video like youtube or vimeo.
						  <br /> Example: http://codecanyon.net/item/the-countdown-pro/3228499', $this->textdomain ) . '</span>';
				echo '</li>';		
			break;
			
		}
		
		exit;
	}
	
	/**
	 * Saving metabox data on save action
	 * Checking the nonce, make sure the current post type have sidebar option enable
	 * Save the post metadata with update_post_meta for the current $post_id in array
	 * @param string $post_id
	 * @since 1.1
	**/
	function save_metabox( $post_id ) {

		// Verify this came from the our screen with proper authorization,
		// because save_post can be triggered at other times
		if ( isset($_POST['tcp_nonce']) && !wp_verify_nonce( $_POST['tcp_nonce'], plugin_basename(__FILE__) ))
			return $post_id;

		// Verify if this is an auto save routine. If our form has not been submitted, so we dont want to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return $post_id;

		$options = get_option( 'the_countdown_pro' );

		// Check permissions if this post type is use the sidebar meta option
		// Array value [cpt] => Array ( [testimonial] => 1 [statement] => 1 )
		if ( isset($_POST['post_type']) && isset($options['cpt']) && array_key_exists($_POST['post_type'], $options['cpt']) )  {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		// Alright folks, we're authenticated, let process
		if ( $parent_id = wp_is_post_revision($post_id) )
			$post_id = $parent_id;

		// Save the post meta data
		if ( isset( $_POST['tcp'] ) ) {
			$settings = array();
			foreach ( $_POST['tcp'] as $key => $data ) {
				$settings[$key] = $data;
			}
			
			update_post_meta( $post_id, 'the_countdown_pro', $settings );
		}
		
		if( isset( $_POST['tcp'] ) )
			do_action( 'tcp_addon_save_meta', $post_id, $_POST['tcp'] );	// save meta action for addons
	}


	/**
	 * Load custom style or script to the current page admin
	 * Enqueue the jQuery library including UI, colorpicker, 
	 * the popup window and some custom styles/scripts
	 * @param string $hook.
	 * @since 1.1
	**/
	function admin_enqueue($hook) {
		if( 'post.php' != $hook && 'post-new.php' != $hook )
			return;

		wp_enqueue_style( 'tcp-dialog', THE_COUNTDOWN_PRO_URL . 'lib/dialog.css', array( 'farbtastic', 'thickbox' ), $this->version );
		wp_register_script( 'total-dialog', THE_COUNTDOWN_PRO_URL . 'lib/jquery.dialog.js', array( 'jquery', 'farbtastic', 'media-upload', 'thickbox' ), $this->version );
		wp_enqueue_script( 'tcp-meta', THE_COUNTDOWN_PRO_URL . 'js/jquery.meta.js', array( 'total-dialog' ), $this->version );
		wp_localize_script( 'tcp-meta', 'tcpLocalize', array(
			'nonce'		=> wp_create_nonce( 'the-countdown-pro' ),  // generate a nonce for further checking below
			'action'	=> 'the_countdown_pro_generate_callback'
		));	
	}


	/**
	 * Get the custom styles/script for each meta for further use 
	 * Using wp_head hook to push this function to the head
	 * Countdown script and localize will be pushed via the main class
	 * @return
	 * @since 1.1
	**/
	function callback_head() {
		$id   = get_the_ID();
		$meta = get_post_meta( $id, 'the_countdown_pro', true );
		
		if ( isset( $meta['callback'] ) && !empty( $meta['callback'] ) ) {
			
			switch ( $meta['callback'] ) {
				
				case 'lightbox':
					wp_enqueue_style ( 'prettyPhoto', THE_COUNTDOWN_PRO_URL . 'css/prettyPhoto.css' );
					wp_enqueue_script( 'prettyPhoto', THE_COUNTDOWN_PRO_URL . 'js/jquery.prettyPhoto.js', array( 'jquery' ) );
					add_action( 'wp_head',  array( $this, 'callback_wp_head' ), 99 );
					add_filter( 'the_content', array( $this, 'add_content' ) );
				break;
					
				case 'hide-content':
				case 'show-content':
					add_action( 'wp_head',  array( $this, 'callback_wp_head' ), 99 );
					add_filter( 'the_content', array( $this, 'add_content' ) );
				break;
				
				case 'redirect':
					add_action( 'wp_head',  array( $this, 'callback_wp_head' ), 99 );
				break;
			
			}
		}
	}

	/**
	 * Push additional HTML content to the content for some purposes.
	 * Using the_content filter to push this content after the content
	 * @return
	 * @since 1.1
	**/
	function add_content($content) {
		$id   = get_the_ID();
		$meta = get_post_meta($id, 'the_countdown_pro', true);
		
		if ( isset( $meta['html'] ) && ! empty( $meta['html'] ) )
			return '<div id="' . $id . 'content">' . $content . '</div><div id="' . $id . 'html" class="hide">' . $meta['html'] . '</div>';
		
		return $content;
	}

	
	function callback_wp_head() {
		$id   = get_the_ID();
		$meta = get_post_meta($id, 'the_countdown_pro', true);

		$callback = $meta['callback'];
		
		switch ( $callback ) {		
			
			case 'lightbox':	
				/* var selector, count = 2,
				countdown = setInterval(function(){
					if ( count == 0 ) {
						clearInterval(countdown);
						$(selector).prettyPhoto();
						$.prettyPhoto.open("http://vimeo.com/70323400", "Title", "Description");
					}
					console.log(count);
					count--;
				}, 1000);	 */
				
				$interval = isset($meta['interval']) ? $meta['interval'] : 5;
				$title = isset($meta['title']) ? $meta['title'] : '';
				$description = isset($meta['description']) ? $meta['description'] : '';
				
				// Check if link is set, if not use the html id for the tag id, if not stop process
				if ( isset( $meta['link'] ) && !empty ( $meta['link'] ) )
					$content = $meta['link'];
				else
					$content = '#' . $id . 'html';
					
				echo '<script type="text/javascript">
						jQuery(document).ready(function($){
							var selector, current = new Date(); 
							current.setSeconds(current.getSeconds() + ' . $interval . ');
							$(document.body).append("<span id=\''.$id.'lightbox\' style=\'display:none;\'></span>");
							$("#'.$id.'lightbox").countdown({
								until: current,
								onExpiry: function() {
									$(selector).prettyPhoto();
									$.prettyPhoto.open("' . $content . '", "' . $title . '", "' . $description . '");
								}
							}); 				
						});
					  </script>';
			break;  
			  
			case 'hide-content':
				$interval = isset($meta['interval']) ? $meta['interval'] : 10;				
				echo '<script type="text/javascript">
						jQuery(document).ready(function($){
							var selector, current = new Date(); 
							current.setSeconds(current.getSeconds() + ' . $interval . ');
							$(document.body).append("<span id=\''.$id.'lightbox\' style=\'display:none;\'></span>");
							$("#'.$id.'lightbox").countdown({
								until: current,
								onExpiry: function() {
									$("#' . $id . 'content").fadeOut().remove();
									$("#' . $id . 'html").fadeIn();
								}
							});		
						});
					  </script>';
			break;
			
			case 'show-content':
				$interval = isset($meta['interval']) ? $meta['interval'] : 10;				
				echo '<script type="text/javascript">
						jQuery(document).ready(function($){
							$("#' . $id . 'content").hide();
							$("#' . $id . 'html").show();
							var selector, current = new Date(); 
							current.setSeconds(current.getSeconds() + ' . $interval . ');
							$(document.body).append("<span id=\''.$id.'lightbox\' style=\'display:none;\'></span>");
							$("#'.$id.'lightbox").countdown({
								until: current,
								onExpiry: function() {
									$("#' . $id . 'content").fadeIn();
									$("#' . $id . 'html").fadeOut();
								}
							});		
						});
					  </script>';
			break;
			
			case 'redirect':
				$interval = isset($meta['interval']) ? $meta['interval'] : 10;				
				$link = isset($meta['link']) ? $meta['link'] : '#';				
				echo '<script type="text/javascript">
						jQuery(document).ready(function($){
							var selector, current = new Date(); 
							current.setSeconds(current.getSeconds() + ' . $interval . ');
							$(document.body).append("<span id=\''.$id.'lightbox\' style=\'display:none;\'></span>");
							$("#'.$id.'lightbox").countdown({
								until: current,
								expiryUrl: "' . $link . '"
							});
						});
					  </script>';
			break;
			
		}	
	}
} new The_Countdown_Pro_Meta();
?>