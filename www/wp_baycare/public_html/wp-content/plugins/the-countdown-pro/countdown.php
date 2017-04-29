<?php
/*
    The Countdown Pro
    @since 1.0.0
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

if ( ! defined( 'ABSPATH' ) )
	exit;

class The_Countdown_Pro {

	var $textdomain;
	var $slug;
	
	public function __construct() {
		$this->slug = THE_COUNTDOWN_PRO_SLUG;
		$this->textdomain = THE_COUNTDOWN_PRO_LANG;		

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		//add_action( 'wp_head', array( $this, 'shortcode_styles' ), 9 );

		add_shortcode( 'countdown', array( $this, 'shortcode' ) );
		add_shortcode( 'countdown-widget', array( $this, 'widget_shortcode' ) );

		add_action( 'wp_ajax_server_sync', array( &$this, 'server_sync' ) );
		add_action( 'wp_ajax_nopriv_server_sync', array( &$this, 'server_sync' ) );	
		
		add_action( 'widgets_init', array( $this, 'load_widgets' ) );

		if ( is_admin() ) { // bail early only for administration			
			add_action( 'wp_ajax_countdown-preview', array( &$this, 'preview' ) );
		}

		require_once( THE_COUNTDOWN_PRO_DIR . 'meta.php' );		
	}
	

	/**
	 * Pushes custom styles and scripts to the header
	 * Only push if current post has shortcode, active widgets or have a meta callback **
	 * To avoid more issues in the future, these scripts and style will be pulled in the header even there is no widget/shortcode existed in the page
	 * @since 1.4.3
	 */		
	function enqueue_scripts() {		 
		wp_enqueue_style( 'the-countdown-pro', THE_COUNTDOWN_PRO_URL . 'css/countdown.css', null, THE_COUNTDOWN_PRO_VERSION );
		wp_enqueue_script( 'the-countdown-pro', THE_COUNTDOWN_PRO_URL . 'js/jquery.countdown.min.js', array( 'jquery' ), '2.0.0', false );
		wp_localize_script( 'the-countdown-pro', 'tcp', array(
			'nonce'   => wp_create_nonce( 'the-countdown-pro' ),
			'action'  => 'server_sync',
			'ajaxurl' => admin_url('admin-ajax.php')
		));
	}
	
		
	/**
	 * Get the server date time via AJAX
	 * Date time format: M j, Y H:i:s O ( May 11, 2014 05:56:53 +0000 )
	 * @since 1.4.3
	 */		
	function server_sync() {
		//check_ajax_referer( 'the-countdown-pro', 'nonce' );
		echo gmdate( 'M j, Y H:i:s O', current_time( 'timestamp', 1 ) );
		exit();
	}	
	
	
	/**
	 * Get the server date time via AJAX
	 * Date time format: M j, Y H:i:s O ( May 11, 2014 05:56:53 +0000 )
	 * @since 1.4.3
	 */		
	function preview() {
		global $post;
		$post = new stdClass();
		$post->ID = -1;
		$post->post_content = '';			
		$loader = admin_url('images/spinner.gif');
		?>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<?php wp_head(); ?>
		</head>
		<body style="background-color:#fff;">
			<?php
				// check if this preview comes from widget or shortcode
				if( isset( $_GET['shortcode'] ) ) {					
					$atts = shortcode_atts( countdown_default_args(), get_option( 'tcp_shortcode' ) );					
					countdown_scripts( $atts, true );
					countdown_styles( $atts );
					$id = isset( $atts['id'] ) ? $atts['id'] : '';
					$class = isset( $atts['template'] ) ? $atts['template'] : 'default';	
				
				} else {
					$id = isset( $_GET['id'] ) ? $_GET['id'] : '';
					$class = isset( $_GET['class'] ) ? $_GET['class'] : 'default';
				}
		
				echo "<div id='countdown-$id' class='countdown-$class'>";
					echo "<img src='$loader' />";
				echo "</div>";
			?>
			<?php wp_footer(); ?>
		</body>
		</html>
		<?php
		exit;
	}

	
	function the_countdown_pro( $args ) {
		$options = get_option( 'the_countdown_pro' );
		$instance = wp_parse_args( (array) $args, countdown_default_args() );	// merge user arguments with defaults.

		extract( $instance, EXTR_SKIP );
		$html = '';

		if ( ! empty( $intro_text ) ) // print intro text if exist
			$html .= '<p class="'. $id . '-intro-text intro-text">' . $intro_text . '</p>';
		
		$class = $template ? strtolower( str_replace(' ', '', $template ) ) : 'default';
		$html .= "<div id='countdown-$id' class='countdown-$class'>";
			if( isset( $options['loading_text'] ) && $options['loading_text'] )
				$html .= "<div class='countdown-loading'>{$options['loading_text']}</div>";
		$html .= "</div>";

		if ( ! empty( $outro_text ) ) // print outro text if exist
			$html .= '<p class="'. $id . '-outro-text outro-text">' . $outro_text . '</p>';

		return $html;
	}


	/*
	 * Main function to generate shortcode using total_users_pro() function
	 * Shortcode does not generate the custom style and script 
	**/
	function shortcode( $atts ) {
		
		// **NOTE: this function uses strtolower()
		foreach( array( 'until', 'compactLabels', 'cLabels', 'cLabels1' ) as $i ) 
			if( isset( $atts[ strtolower($i) ] ) )
				$atts[$i] = explode( ",", $atts[ strtolower($i) ] );
		
		$atts['expiryUrl'] = isset( $atts['expiryurl'] ) ? $atts['expiryurl'] : '';
		$atts['expiryText'] = isset( $atts['expirytext'] ) ? rawurldecode( $atts['expirytext'] ) : '';
		$atts['serverSync'] = $atts['serversync'];
		$atts['alwaysExpire'] = $atts['alwaysexpire'];
		$atts['onExpiry'] = isset( $atts['onexpiry'] ) ? rawurldecode( $atts['onexpiry'] ) : '';
		$atts['onTick'] = isset( $atts['ontick'] ) ? rawurldecode( $atts['ontick'] ) : '';
		$atts['tickInterval'] = $atts['tickinterval'];					

		$atts = wp_parse_args( $atts, countdown_default_args() ); // merge with the defaults.			
		
		return $this->the_countdown_pro( $atts ) . countdown_styles( $atts, false ) . countdown_scripts( $atts, false, false ) ;
	}	

	
	/*
	 * Main function to generate shortcode using total_users_pro() function
	 * See $defaults arguments for using total_users_pro() function
	 * Shortcode does not generate the custom style and script
	 * @since 1.4.3
	 */
	function widget_shortcode($atts, $content) {
		$options = get_option( 'widget_the-countdown-pro' );
		$args = $options[$atts['id']]; 	// overwrite
		$args['id'] = $atts['id'];
		return $this->the_countdown_pro( $args );
	}


	/*
	 * Check if the post has a shortcode(s) used in the current post content with stripos PHP function
	 * Add !empty($cur_post->post_content) if the post has no content
	 * @return bool true, default false
	 * @since 1.3
	*/
	function has_shortcode() {
		global $post;
		
		if ( ! isset( $post ) || is_admin() )
			return false;

		if ( has_shortcode( $post->post_excerpt, 'countdown' ) ) // check the post content if has shortcode 
			return true;
			
		if ( has_shortcode( $post->post_content, 'countdown' ) ) // check the post content if has shortcode 
			return true;

		return false;
	}

	
	/**
	 * Load widget, require additional file and register the widget
	 * @since 1.0.0
	 */
	function load_widgets( $atts ) {
		require_once( THE_COUNTDOWN_PRO_DIR . 'widget.php' );
		register_widget( 'The_Countdown_Pro_Widget' );
	}
	

} new The_Countdown_Pro();


/**
 * A full set of countdown default argument parameters
 * @return array
 * @since 1.4.4
 */
function countdown_default_args() {
	$options = get_option( 'the_countdown_pro' );

	// Set up the default form values
	// date-time: mm jj aa hh mn
	$defaults = array(
		'id' 				=> null,
		'title' 			=> esc_attr__( 'Countdown', 'the-countdown-pro' ),
		'title_icon'		=> null,
		'counter' 			=> 'until',
		'until' 			=> array( date('m'), date('j'), date('Y'), 16, 53 ), // month,day,year,minute,second
		'cLabels' 			=> array_values( $options['labels'] ),
		'cLabels1' 			=> array_values( $options['labels1'] ),
		'compactLabels' 	=> array_values( $options['compactLabels'] ),
		'relative' 			=> '',
		'format' 			=> 'dHMS',
		'expiryUrl' 		=> '',
		'expiryText' 		=> '',
		'serverSync' 		=> true,
		'timezone' 			=> '',
		'alwaysExpire' 		=> false,
		'compact' 			=> false,
		'onExpiry' 			=> '',
		'onTick' 			=> '',
		'tickInterval' 		=> 1,
		'bg_color' 			=> '',	 // #f6f7f6
		'counter_image' 	=> null,
		'counter_color' 	=> '',	 // #444444
		'counter_bg_color' 	=> '',	 // #444444
		'label_bg_color' 	=> '',	 // #444444
		'font_size' 		=> null, // 24 deprecated
		'counter_size' 		=> '',
		'label_size' 		=> null, // 11
		'label_color' 		=> '',   // #444444
		'template' 			=> '',
		'toggle_active'		=> array( true, false, false, false, false, false ),
		'intro_text' 		=> '',
		'outro_text' 		=> '',
		'customstylescript'	=> ''
	);
	
	return $defaults;
}


/**
 * Static countdown function that can be used in template files
 * @param $args an array contain widget settings or shortcode parameters
 * @since 2.0.0
 */
function the_countdown( $args ) {
	countdown_scripts( $args );
	extract( $args );
	$class = $template ? strtolower( str_replace(' ', '', $template ) ) : 'default';
	echo "<div id='countdown-$id' class='countdown-$class'></div>";	
}


/**
 * Output slug for translation WPML
 * @since 2.0.6
 */
function tcp_translation_slug() {
	if ( defined( 'ICL_LANGUAGE_CODE' ) )
		return THE_COUNTDOWN_PRO_SLUG .'_'. ICL_LANGUAGE_CODE;

	return;
}


/**
 * Returns the countdown default arguments
 * @param $setting an array contain widget settings or shortcode parameters
 * @since 1.4.4
 */
function countdown_scripts( $args, $preview = false, $echo = true ) {
	$templates = get_option( 'countdown_templates' );
	$args = wp_parse_args( (array) $args, countdown_default_args() ); // Merge the user-selected arguments with the defaults.
	extract( $args );
	$opts = $script = array();

	if ( ! empty( $until ) ) {
	
		$datetime = $relative ? "'$relative'" : "new Date( '{$until[0]}/{$until[1]}/{$until[2]} {$until[3]}:{$until[4]}' )";

		$opts[] = "$counter: $datetime"; // until or since with fix date time or relative
		$expiryText = str_replace("'", "\'", $expiryText );

		if ( $expiryUrl ) 		$opts[] = "expiryUrl: '$expiryUrl'";
		if ( $expiryText ) 		$opts[] = "expiryText: '$expiryText'";
		if ( $serverSync ) 		$opts[] = "serverSync: tcpServerSync";				
		if ( $alwaysExpire ) 	$opts[] = "alwaysExpire: $alwaysExpire";
		if ( $format ) 			$opts[] = "format: '$format'";
		if ( $compact )			$opts[] = "compact: $compact";
		if ( $onExpiry && ! $preview )	$opts[] = "onExpiry: $onExpiry";
		if ( $onTick && ! $preview )	$opts[] = "onTick: $onTick";				
		if ( $tickInterval )	$opts[] = "tickInterval: $tickInterval"; 		
		if ( $timezone )		$opts[] = "timezone: $timezone";		
		
		if( $layout = countdown_layouts( $args ) )
			$opts[] = "layout: '$layout'";
				
		if ( $compactLabels ) {
			array_walk( $compactLabels, 'countdown_walk_callback' );
			$opts[] = "compactLabels: [". implode( ', ', $compactLabels ) ."]";
		}
		if ( $cLabels ) {
			array_walk( $cLabels, 'countdown_walk_callback' );
			$opts[] = "labels: [". implode( ', ', $cLabels )  ."]";
		}
		if ( $cLabels1 ) {
			array_walk( $cLabels1, 'countdown_walk_callback' );
			$opts[] = "labels1: [". implode( ', ', $cLabels1 ) ."]";
		}
		
		$script[] = '<script type="text/javascript">';
		$script[] = 'jQuery(document).ready( function($){';		
		$script[] = "	$('#countdown-$id').countdown({";
		$script[] = "		". implode( ",\n		", $opts );
		$script[] = "	});";
		$script[] = '});';
		$script[] = '</script>';
	}
	
	$implode = implode( "\n", $script ) . "\n";
	if( $echo ) 
		echo $implode; 
	else 
		return $implode;		
}


/**
 * Returns the countdown default arguments
 * @param $v as array value
 * @param $k as array key
 * @since 1.4.4
 */
function countdown_walk_callback( &$v, $k ) {
	$v = "'$v'";
}
?>