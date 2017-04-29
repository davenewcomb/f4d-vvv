<?php
/*
    The Countdown Pro Shortcode Dialog Class
    http://zourbuth.com/plugins/tcpro
    Copyright 2013  zourbuth.com  (email : zourbuth@gmail.com)

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


/*
 * Print additional styles and script to the header after wp_enqueue_scripts 
 * the 'navmenu_enqueue_scripts_shortcode' funtion to avoid wrong arrangement
 * @param no parameter
 * @since 1.4.4
 * @return javascript and CSS
 */
class  The_Countdown_Pro_Shortcode_Dialog {

	// Prefix for the widget
	var $prefix;
	var $textdomain;
	
	/*
	 * Class constructor
	 * @since 1.4.4
	**/
	function __construct() {		
		$this->slug = 'the-countdown-pro';
		$this->version = THE_COUNTDOWN_PRO_VERSION;
		$this->textdomain = THE_COUNTDOWN_PRO_LANG;
		$this->url = THE_COUNTDOWN_PRO_URL;
		$this->name = THE_COUNTDOWN_PRO_NAME;
		
		add_action( 'admin_init', array( &$this, 'add_buttons' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_footer', array( &$this, 'admin_footer' ), 1 );
		add_action( 'admin_footer-post.php', array(&$this, 'shortcode_script'), 9 );
		add_action( 'admin_print_footer_scripts', array( &$this, 'print_dialog'), 50 );
		add_action( 'wp_ajax_tcp_shortcode_dialog', array( &$this, 'tcp_shortcode_dialog' ) );
	}
		
		
	/*
	 * Shortcode dialog update and previews
	 * @since 2.0.1
	**/
	function tcp_shortcode_dialog(){
		// Security checking
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
		if ( ! wp_verify_nonce( $nonce, 'tcpsc' ) )
			die();
		
		// Save current form fields to the database for further use
		$args = array();
		if( isset( $_POST['data'] ) ) {
			parse_str( $_POST['data'], $args );
			update_option( 'tcp_shortcode', $args );
		}

		// Regenerate the shortcode form fields
		$this->shortcode_dialog( $args );
		
		exit; // Ok there, case closed!
	}
	
	
	/*
	 * Custom styles and script for the post editor screen
	 * @since 2.0.1
	**/
	function enqueue_styles() {
		global $hook_suffix;
		if( ! in_array( $hook_suffix, array('post.php','post-new.php') ) )
			return;
			
		// source: \wp-includes\css\editor.css
		?>
		<style type="text/css">
		#tcp-backdrop {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			min-height: 360px;
			background: #000;
			opacity: 0.7;
			filter: alpha(opacity=70);
			z-index: 100100;
		}	
		#tcp-wrap {
			transition: none 0s ease 0s;
			display: none;
			background-color: #fff;
			-webkit-box-shadow: 0 3px 6px rgba( 0, 0, 0, 0.3 );
			box-shadow: 0 3px 6px rgba( 0, 0, 0, 0.3 );
			width: 500px;
			overflow: hidden;
			margin-left: -250px;
			margin-top: -125px;
			position: absolute;
			top: 200px;
			left: 50%;
			z-index: 100105;
			-webkit-transition: height 0.2s, margin-top 0.2s;
			transition: height 0.2s, margin-top 0.2s;
		}
		#tcp-modal-title {
			background: #fcfcfc;
			border-bottom: 1px solid #dfdfdf;
			height: 36px;
			font-size: 14px;
			font-weight: 600;
			line-height: 36px;
			padding: 0 36px 0 16px;
			top: 0;
			right: 0;
			left: 0;
		}
		#tcp-close {
			color: #666;
			cursor: pointer;
			padding: 0;
			position: absolute;
			top: 0;
			right: 0;
			width: 36px;
			height: 36px;
			text-align: center;
		}

		#tcp-close:before {
			font: normal 20px/36px 'dashicons';
			vertical-align: top;
			speak: none;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			width: 36px;
			height: 36px;
			content: '\f158';
		}

		#tcp-close:hover,
		#tcp-close:focus {
			color: #2ea2cc;
		}
		i.mce-i-tcpsc {
			background: url("<?php echo THE_COUNTDOWN_PRO_URL . 'img/shortcode.png'; ?>") no-repeat scroll center center transparent;
		}
		</style><?php
	}
	
	
	function admin_footer() {
		wp_enqueue_script( 'wpdialogs-popup' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}
	
	
	/**
	 * Load custom style or script to the current page admin
	 * Enqueue the jQuery library including UI, colorpicker, 
	 * the popup window and some custom styles/scripts
	 * @param string $hook.
	 * @since 1.4.4
	**/
	function admin_enqueue_scripts( $hook ) {
		if( 'post.php' != $hook && 'post-new.php' != $hook )
			return;
		
		wp_enqueue_style( 'total-options', THE_COUNTDOWN_PRO_URL . 'lib/dialog.css', array('wp-color-picker'), THE_COUNTDOWN_PRO_VERSION );
		wp_enqueue_script( 'total-options', THE_COUNTDOWN_PRO_URL . 'lib/jquery.dialog.js', array( 'jquery', 'wp-color-picker' ), THE_COUNTDOWN_PRO_VERSION );
		wp_enqueue_script( 'tcpro-datepicker', THE_COUNTDOWN_PRO_URL . 'js/jquery.datepicker.js', array( 'jquery', 'wp-color-picker' ), THE_COUNTDOWN_PRO_VERSION );
		wp_enqueue_script( 'tcpro-shortcode', THE_COUNTDOWN_PRO_URL . 'js/jquery.shortcode.js', array( 'jquery', 'wp-color-picker' ), THE_COUNTDOWN_PRO_VERSION );
		wp_localize_script( 'tcpro-shortcode', 'tcpsc', array(
			'nonce'		=> wp_create_nonce( 'tcpsc' ),
			'defaults'	=> json_encode( countdown_default_args() ),
			'action'	=> 'tcp_shortcode_dialog',
			'url'		=>  THE_COUNTDOWN_PRO_URL
		));			
	}

	
	/*
	 * Create the tinyMCE button for spesific users
	 * @since 1.4.4
	 */
	function add_buttons() {		
		if ( get_user_option( 'rich_editing' ) == 'true' ) {		
			add_filter( 'mce_external_plugins',  array( &$this, 'mce_external_plugins'), 5 );
			add_filter( 'mce_buttons',  array( &$this, 'mce_buttons'), 5 );
			
			add_filter( 'mce_external_languages', array( &$this, 'mce_external_languages'), 10, 1 );
			add_action( 'admin_print_footer_scripts', array( &$this, 'the_countdown_pro_quick_tag') );			
		}
	}
		
	function mce_external_languages( $arr ) {
		$arr[] = THE_COUNTDOWN_PRO_DIR . 'tinymce-i18n.php';
		return $arr;
	}
		
		
	function the_countdown_pro_quick_tag(){ 
		if ( wp_script_is( 'quicktags' ) ) { ?>
			<script type='text/javascript'>
				QTags.addButton( 'countdown', 'countdown', countdown_callback );
				function countdown_callback() { tcpShortcode.open(); }
			</script><?php
		}
	}
	
	
	function mce_buttons( $buttons ) {
		array_push( $buttons, 'separator', 'tcpsc' );
		return $buttons;
	}


	function mce_external_plugins( $plugins ) {
		$plugins['tcpsc'] = THE_COUNTDOWN_PRO_URL . 'js/editor_plugin.js';	
		return $plugins;
	}

		
	/**
	 * Dialog for internal linking.
	 *
	 * @since 3.1.0
	 */
	function print_dialog() {
		global $hook_suffix;
		if( ! in_array( $hook_suffix, array('post.php','post-new.php') ) )
			return;		
		?>
		<div id="tcp-backdrop" style="display: none"></div>
		<div id="tcp-wrap" class="wp-core-ui" style="display: none">
			<form id="tcp-dialog" tabindex="-1">
				<div id="tcp-modal-title">
					<?php _e( 'The Countdown Pro Shortcode Editor' ) ?>
					<div id="tcp-close" tabindex="0"></div>
				</div>				
				<div class="total-shortcode" style="padding: 12px 12px 10px;">
					<div id="tcp-dialog-options">
						<?php $this->shortcode_dialog(); ?>
					</div>
					<div class="submitbox" style="height:29px;overflow:auto;padding: 5px 0;">
						<div id="tcp-dialog-update" style=" float: right;line-height: 23px;">
							<span class="spinner" style="display: none;float: left;"></span>
							<input type="submit" value="<?php esc_attr_e( 'Add Shortcode', 'tcpro' ); ?>" class="button-primary tcp-dialog-submit" />					
						</div>
						<div id="tcp-dialog-cancel" style="float:left;line-height: 25px;">
							<a class="submitdelete deletion" href="#"><?php _e( 'Cancel', 'tcpro' ); ?></a>
						</div>
					</div>
				</div>
			</form>
		</div>
		</div><?php
	}

	
	/*
	 * Print additional styles and script to the header after wp_enqueue_scripts 
	 * the 'navmenu_enqueue_scripts_shortcode' funtion to avoid wrong arrangement
	 * @param no parameter
	 * @since 1.3
	 * @return javascript and CSS
	 */
	function shortcode_dialog( $instance = array() ) {
		
		$defaults = countdown_default_args();

		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$tabs = array( 
			__( 'General', $this->textdomain ),  
			__( 'Format', $this->textdomain ),
			__( 'Customs', $this->textdomain ),
			__( 'Styling', $this->textdomain ),
			__( 'Advanced', $this->textdomain )
		);			

		// Set the default value of each widget input
		global $wp_locale;
		$time_adj = current_time('timestamp');
		$counterList = array( 'until' => __( 'Until', $this->textdomain) , 'since' => __( 'Since', $this->textdomain  ));
		
		$templates = get_option( 'countdown_templates' );
		?>

		<div class="pluginName"><?php echo $this->name; ?><span class="pluginVersion"><?php echo $this->version; ?></span></div>
		<div class="total-options tabbable tabs-left">
			<ul class="nav nav-tabs">
				<?php foreach ($tabs as $key => $tab ) : ?>
					<li class="<?php echo $instance['toggle_active'][$key] ? 'active' : '' ; ?>"><?php echo $tab; ?><input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo $instance['toggle_active'][$key]; ?>" /></li>
				<?php endforeach; ?>							
			</ul>
			<ul class="tab-content">
				<li class="tab-pane <?php if ( $instance['toggle_active'][0] ) : ?>active<?php endif; ?>">
					<ul>							
						<li>
							<input type="hidden" value="<?php echo time();	?>" name="id" id="id" class="widefat" />
							
							<div id ="until-<?php echo $instance['id']; ?>" class="curtime tc-curtime">
								<label><?php _e( 'Date Picker', $this->textdomain ); ?></label>
								<select class="smallfat" id="<?php echo $this->get_field_id( 'counter' ); ?>" name="<?php echo $this->get_field_name( 'counter' ); ?>">
									<?php foreach ( $counterList as $option_value => $option_label ) { ?>
										<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['counter'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
									<?php } ?>
								</select>
								<span class="timestamp"><span><?php echo $wp_locale->get_month_abbrev( $wp_locale->get_month( $instance['until'][0] ) ) . ' ' . $instance['until'][1] . ', ' . $instance['until'][2] . ' @ ' . $instance['until'][3] . ':' . $instance['until'][4]; ?></span></span>
								<a tabindex="4" class="edit-timestamp hide-if-no-js" href="#"><?php _e( 'Edit', $this->textdomain ); ?></a>
								<div class="hide-if-js timestampdiv">
									<div class="timestamp-wrap">
										<?php
											$month = "<select class='mm' name='" . $this->get_field_name( 'until' ) . "[]'>";
											for ( $i = 1; $i < 13; $i = $i +1 ) {
												$monthnum = zeroise($i, 2);
												$month .= "\t\t\t" . '<option value="' . $monthnum . '"';
												if ( $i == $instance['until'][0] )
													$month .= ' selected="selected"';
												/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
												$month .= '>' . sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
											}
											$month .= '</select>';
											echo $month;
										?>
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][1]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="jj" />, 
										<input type="text" autocomplete="off" tabindex="4" maxlength="4" size="4" value="<?php echo $instance['until'][2]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="aa" /> @ 
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][3]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="hh"> : 
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][4]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="mn">

										<a class="save-timestamp hide-if-no-js button" href="#"><?php _e( 'OK', $this->textdomain ); ?></a>
										<a class="cancel-timestamp hide-if-no-js" href="#"><?php _e( 'Cancel', $this->textdomain ); ?></a>
									</div>
									
									<input type="hidden" value="11" name="ss" class="ss" />
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['0'] ); ?>" name="hidden_mm" class="hidden_mm">
									<input type="hidden" value="<?php echo gmdate( 'd', $time_adj ); ?>" name="cur_mm" class="cur_mm">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['1'] ); ?>" name="hidden_jj" class="hidden_jj">
									<input type="hidden" value="<?php echo gmdate( 'm', $time_adj ); ?>" name="cur_jj" class="cur_jj">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['2'] ); ?>" name="hidden_aa" class="hidden_aa">
									<input type="hidden" value="<?php echo gmdate( 'Y', $time_adj ); ?>" name="cur_aa" class="cur_aa">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['3'] ); ?>" name="hidden_hh" class="hidden_hh">
									<input type="hidden" value="<?php echo gmdate( 'h', $time_adj ); ?>" name="cur_hh" class="cur_hh">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['4'] ); ?>" name="hidden_mn" class="hidden_mn">
									<input type="hidden" value="<?php echo gmdate( 'i', $time_adj ); ?>" name="cur_mn" class="cur_mn">
								</div>
								<span class="description"><?php _e( "new Date(year, mth - 1, day, hr, min, sec) - date/time to count up from or numeric for seconds offset, or string for unit offset(s): 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds. <b>Note</b>: save the widget instance first before using this date picker.", $this->textdomain ); ?></span>							
							
								<br />
								<label for="<?php echo $this->get_field_id( 'relative' ); ?>"><?php _e( 'Relative Time', $this->textdomain ); ?></label>
								<span class="description"><?php _e( 'A number in seconds. Otherwise use a string to specify the number and units: <em>y</em> for years, <em>o</em> months, <em>w</em> weeks, <em>d</em> days, <em>h</em> hours, <em>m</em> minutes, <em>s</em> seconds. The date time above will be not use if this is not empty.<br />Eq. Until two days time: +2d', $this->textdomain ); ?></span>	
								<input type="text" id="<?php echo $this->get_field_id( 'relative' ); ?>" name="<?php echo $this->get_field_name( 'relative' ); ?>" value="<?php echo esc_attr( $instance['relative'] ); ?>" />													
							</div>	
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'expiryUrl' ); ?>"><?php _e( 'Expiry Url', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'A URL to load upon expiry, replacing the current page', $this->textdomain ); ?></span>	
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'expiryUrl' ); ?>" name="<?php echo $this->get_field_name( 'expiryUrl' ); ?>" value="<?php echo esc_attr( $instance['expiryUrl'] ); ?>" />							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'expiryText' ); ?>"><?php _e( 'Expiry Text', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Text to display upon expiry, replacing the countdown', $this->textdomain ); ?></span>	
							<textarea class="widefat" id="<?php echo $this->get_field_id( 'expiryText' ); ?>" name="<?php echo $this->get_field_name( 'expiryText' ); ?>"><?php echo esc_attr( $instance['expiryText'] ); ?></textarea>
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][1] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Date Format', $this->textdomain ); ?></label>
							<input type="text" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>" value="<?php echo esc_attr( $instance['format'] ); ?>" />
							<span class="description"><?php _e( 'Format for display - upper case for always, lower case only if non-zero, \'Y\' years, \'O\' months, \'W\' weeks, \'D\' days, \'H\' hours, \'M\' minutes, \'S\' seconds', $this->textdomain ); ?></span>	
						</li>					
						<li>
							<label><?php _e( 'Countdown Labels', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'The display texts for the counters', $this->textdomain ); ?></span>
							<table>
								<tr>
									<td><span class="description"><?php _e( 'Years', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][0]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Year', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][0]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Months', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][1]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Month', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][1]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Weeks', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][2]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Week', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][2]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Days', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][3]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Day', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][3]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Hours', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][4]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Hour', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][4]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Minutes', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][5]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Minute', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][5]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Seconds', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels' ); ?>[]" value="<?php echo $instance['cLabels'][6]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Second', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'cLabels1' ); ?>[]" value="<?php echo $instance['cLabels1'][6]; ?>" /></td>
								</tr>
							</table>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'compactLabels' ); ?>"><?php _e( 'Compact Labels', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'The compact texts for the counters', $this->textdomain ); ?></span>
							<table>
								<tr>
									<td><span class="description"><?php _e( 'Year', $this->textdomain ); ?></span></td>
									<td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][0]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Month', $this->textdomain ); ?></span></td>
									<td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][1]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Week', $this->textdomain ); ?></span></td>
									<td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][2]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Day', $this->textdomain ); ?></span></td>
									<td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][3]; ?>" /></td>
								</tr>
							</table>							
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][2] ) : ?>active<?php endif; ?>">
					<ul>					
						<li>
							<label for="<?php echo $this->get_field_id( 'serverSync' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['serverSync'], true ); ?> id="<?php echo $this->get_field_id( 'serverSync' ); ?>" name="<?php echo $this->get_field_name( 'serverSync' ); ?>" /><?php _e( 'Server Sync', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Synchronizing to the current server time.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('timezone'); ?>"><?php _e( 'Target Timezone', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Target timezone offset from GMT in hours. Example: +10', $this->textdomain ); ?></span>														
							<input type="text" class="smallfat" id="<?php echo $this->get_field_id( 'timezone' ); ?>" name="<?php echo $this->get_field_name( 'timezone' ); ?>" value="<?php echo esc_attr( $instance['timezone'] ); ?>" />
						</li>							
						<li>
							<label for="<?php echo $this->get_field_id( 'alwaysExpire' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['alwaysExpire'], true ); ?> id="<?php echo $this->get_field_id( 'alwaysExpire' ); ?>" name="<?php echo $this->get_field_name( 'alwaysExpire' ); ?>" /><?php _e( 'Always Expire', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Check if you want to trigger onExpiry even if never counted down.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'compact' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['compact'], true ); ?> id="<?php echo $this->get_field_id( 'compact' ); ?>" name="<?php echo $this->get_field_name( 'compact' ); ?>" /><?php _e( 'Compact Version', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Display in a compact format as plain text with the compact label.', $this->textdomain ); ?></span>
						</li>	
						<li>
							<label for="<?php echo $this->get_field_id('onExpiry'); ?>"><?php _e( 'On Expiry', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'A callback function name to be invoked when the countdown reaches zero. No parameters are passed in.', $this->textdomain ); ?></span>														
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'onExpiry' ); ?>" name="<?php echo $this->get_field_name( 'onExpiry' ); ?>" value="<?php echo esc_attr( $instance['onExpiry'] ); ?>" />
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('onTick'); ?>"><?php _e( 'On Tick', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'A callback function name to be invoked for every ticking. Contain array of current countdown periods (int[7] - based on the format setting) is passed as a parameter: [0] is years, [1] is months, [2] is weeks, [3] is days, [4] is hours, [5] is minutes, and [6] is seconds.', $this->textdomain ); ?></span>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'onTick' ); ?>" name="<?php echo $this->get_field_name( 'onTick' ); ?>" value="<?php echo esc_attr( $instance['onTick'] ); ?>" />							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'tickInterval' ); ?>"><?php _e( 'Tick Interval', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Interval (seconds) between onTick callbacks', $this->textdomain ); ?></span>	
							<input type="text" class="smallfat" id="<?php echo $this->get_field_id( 'tickInterval' ); ?>" name="<?php echo $this->get_field_name( 'tickInterval' ); ?>" value="<?php echo esc_attr( $instance['tickInterval'] ); ?>" />							
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][3] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template', $this->textdomain ); ?></label> 
							<span class="description"><?php _e( 'Select the countdown template', $this->textdomain ); ?></span>
							<select onchange="tcpShortcode.update();" class="smallfat" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>">
								<?php foreach ( countdown_templates() as $t => $template ) { ?>
									<option value="<?php echo esc_attr( $t ); ?>" <?php selected( $instance['template'], $t ); ?>><?php echo esc_html( $template ); ?></option>
								<?php } ?>
							</select>
						</li>
						<li>
							<label><?php _e( 'Preview', $this->textdomain ); ?></label> 
							<span class="description"><?php _e( 'A sample preview, can be different in the front end.', $this->textdomain ); ?> <a id="tcp-reload" href="#"><?php _e( 'Reload', $this->textdomain ); ?></a></span><br />							
							<?php $url =  esc_url( add_query_arg( array( 'action' => 'countdown-preview', 'shortcode' => true, 'class' => $instance['template'] ), admin_url('admin-ajax.php') ) ); ?>
							<iframe src="<?php echo $url; ?>" frameborder="0" scrolling="no" onload="javascript:resizeiframe(this);"></iframe>											
						</li>						
						<li>
							<label for="<?php echo $this->get_field_id( 'counter_size' ); ?>"><?php _e( 'Font Size', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Counter and label font size in pixels unit.', $this->textdomain ); ?></span>	
							<input type="text" class="smallfat" placeholder="24" id="<?php echo $this->get_field_id( 'counter_size' ); ?>" name="<?php echo $this->get_field_name( 'counter_size' ); ?>" value="<?php echo $instance['counter_size']; ?>" />							
							<input type="text" class="smallfat" placeholder="10" id="<?php echo $this->get_field_id( 'label_size' ); ?>" name="<?php echo $this->get_field_name( 'label_size' ); ?>" value="<?php echo $instance['label_size']; ?>" />							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'counter_color' ); ?>"><?php _e( 'Font Color', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Counter and label color.', $this->textdomain ); ?></span>
							<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'counter_color' ); ?>" name="<?php echo $this->get_field_name( 'counter_color' ); ?>" value="<?php echo esc_attr( $instance['counter_color'] ); ?>">						
							<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'label_color' ); ?>" name="<?php echo $this->get_field_name( 'label_color' ); ?>" value="<?php echo esc_attr( $instance['label_color'] ); ?>">
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'counter_bg_color' ); ?>"><?php _e( 'Background Color', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Counter & label background color.', $this->textdomain ); ?></span>
							<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'counter_bg_color' ); ?>" name="<?php echo $this->get_field_name( 'counter_bg_color' ); ?>" value="<?php echo esc_attr( $instance['counter_bg_color'] ); ?>">
							<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'label_bg_color' ); ?>" name="<?php echo $this->get_field_name( 'label_bg_color' ); ?>" value="<?php echo esc_attr( $instance['label_bg_color'] ); ?>">
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'counter_image' ); ?>"><?php _e( 'Background Image', $this->textdomain ); ?></label>
							<img alt="" class="optionImage" src="<?php echo esc_attr( $instance['counter_image'] ); ?>">
							<a href="#" class="add-image button"><?php _e( 'Add Image', $this->textdomain ); ?></a>
							<a class="<?php if ( empty($instance['counter_image'] ) ) : ?>hidden <?php endif; ?>remove-image button" href="#"><?php _e('Remove', $this->textdomain); ?></a>
							<input type="hidden" id="<?php echo $this->get_field_id( 'counter_image' ); ?>" name="<?php echo $this->get_field_name( 'counter_image' ); ?>" value="<?php echo esc_attr( $instance['counter_image'] ); ?>" />
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][4] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id('intro_text'); ?>"><?php _e( 'Intro Text', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'This option will display addtional text before the widget content and HTML supports.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'intro_text' ); ?>" id="<?php echo $this->get_field_id( 'intro_text' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['intro_text']); ?></textarea>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('outro_text'); ?>"><?php _e( 'Outro Text', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'This option will display addtional text after widget and HTML supports.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'outro_text' ); ?>" id="<?php echo $this->get_field_id( 'outro_text' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['outro_text']); ?></textarea>
							
						</li>				
					</ul>
				</li>
			</ul>
		</div><?php
	}
		

	/**
	 * Preview iframe height resizer
	 * @since 2.0.0
	 */		
	function shortcode_script() { ?>
		<script type='text/javascript'>
			function resizeiframe( obj ) {
				if( obj.contentWindow.document.body.scrollHeight )
					obj.style.minHeight = obj.contentWindow.document.body.scrollHeight + 'px';
			}

			jQuery(document).ready( function($){
				$( "body" ).on( "click", "#tcp-reload", function(e) {
					e.preventDefault();
					tcpShortcode.update();
				});
			});			
		</script><?php
	}	
	
	
	function get_field_id( $id ) {
		return isset( $this->ids[$id] ) ? $this->ids[$id] : $id;
	}
	
	
	function get_field_name( $name ) {
		return isset( $this->names[$name] ) ? $this->names[$name] : $name;
	}
}

new The_Countdown_Pro_Shortcode_Dialog();
?>