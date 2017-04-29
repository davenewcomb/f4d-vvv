<?php
/*
    The Countdown Pro Widget Class
    @since 1.0.0
    Copyright 2014 zourbuth.com  (email : zourbuth@gmail.com)

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

class The_Countdown_Pro_Widget extends WP_Widget {

	// Setup class variables
	var $slug;
	var $version;
	var $url;
	var $textdomain;
	var $name;
	var $countdown;
	var $templates;
	
	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.0.0
	 */
	function __construct() {

		$this->slug = 'the-countdown-pro';
		$this->version = THE_COUNTDOWN_PRO_VERSION;
		$this->textdomain = THE_COUNTDOWN_PRO_LANG;
		$this->url = THE_COUNTDOWN_PRO_URL;
		$this->name = THE_COUNTDOWN_PRO_NAME;
		
		// Set up the widget options
		$widget_options = array(
			'classname' => 'the-countdown-pro',
			'description' => esc_html__( '[+] Advanced widget gives you total control over the countdown.', $this->textdomain )
		);

		// Set up the widget control options
		$control_options = array(
			'width' => 460,
			'height' => 350,
			'id_base' => $this->slug
		);

		// Create the widget
		parent::__construct( $this->slug, esc_attr__( 'The Countdown Pro', $this->textdomain ), $widget_options, $control_options );
		
		// Load the widget stylesheet for the widgets admin screen
		add_action( 'load-widgets.php', array(&$this, 'load_widgets') );
		add_action( 'admin_footer-widgets.php', array(&$this, 'iframe_script') );
		add_action( 'admin_print_styles-widgets.php', array(&$this, 'admin_print_styles') );		
				
		// Print the user costum style sheet, countdown script and localize will be pushed via the main class
		$preview = isset( $_GET['action'] ) && $_GET['action'] == 'countdown-preview' ? true : false;
		if ( ( is_active_widget( false, false, $this->id_base, false ) && ! is_admin() ) || $preview ) {
			add_action( 'wp_head', array( &$this, 'print_scripts') );
			add_action( 'wp_head', array( &$this, 'print_styles'), 9 );			
		}
		
		$this->templates = get_option( 'countdown_templates' );
	}
	
	
	/**
	 * Print the widget template custom scripts
	 * @since 1.0.0
	 */	
	function print_scripts() {
		$settings = $this->get_settings();
		
		foreach ( $settings as $key => $setting ) {
			$setting = wp_parse_args( (array) $setting, countdown_default_args() ); // merge
			$setting['id'] = $key;							
			
			countdown_scripts( $setting ); // Print countdown script
			
			// Print the custom style and script
			if ( ! empty( $setting['customstylescript'] ) ) 
				echo $setting['customstylescript']. "\n";
		}
	}
	
		
	/**
	 * Print the widget template styles
	 * @since 1.0.0
	 */	
	function print_styles() {
		$settings = $this->get_settings();
		
		foreach ( $settings as $key => $setting ) {
			$setting = wp_parse_args( (array) $setting, countdown_default_args() ); // merge
			$setting['id'] = $key;

			countdown_styles( $setting ); // Print custom layout styles
		}
	}

	
	/**
	 * Push the widget stylesheet widget.css into widget admin page
	 * @since 1.0.0
	 */	
	function load_widgets() {		
		wp_enqueue_media();
		wp_enqueue_style( 'total-dialog' );
		wp_enqueue_script( 'countdown-dialog', $this->url . 'js/jquery.datepicker.js', array( 'jquery', 'total-dialog' ), $this->version );
	}
	
	
	/**
	 * Preview iframe height resizer
	 * @since 2.0.0
	 */		
	function iframe_script() { ?>
		<script type='text/javascript'>
			function resizeiframe(obj) {
				if( obj.contentWindow.document.body.scrollHeight )
					obj.style.minHeight = obj.contentWindow.document.body.scrollHeight + 'px';
			}
		</script><?php
	}
	
	
	/**
	 * Push the widget stylesheet widget.css into widget admin page
	 * @since 1.4.4
	 */	
	function admin_print_styles() { ?>
		<style type="text/css">
			.total-options .timestamp:before { 
				display: inline-block;
				font: 400 20px/1 dashicons;
				left: -1px;
				padding: 0 5px 0 3px;
				position: relative;
				text-decoration: none !important;
				top: 0;
				vertical-align: top;
				content: '\f145';
				top: 3px;			
				color: #888;
			}
		</style><?php		
	}

	
	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 1.0.0
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		$instance = wp_parse_args( (array) $instance, countdown_default_args() );

		// Output the theme's widget wrapper
		echo $before_widget;
		
		$icon = ! empty( $instance['title_icon'] ) ? '<img class="title-icon" alt="icon" src="' . $instance['title_icon'] . '" />' : '';
		
		// If a title was input by the user, display it
		if ( ! empty( $instance['title'] ) )
			echo $before_title . $icon . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		// Print intro text if exist
		if ( ! empty( $instance['intro_text'] ) )
			echo '<div class="'. $this->id . '-intro-text intro-text">' . $instance['intro_text'] . '</div>';

		echo "<div id='countdown-{$this->number}' class='countdown-{$instance['template']}'></div>";
		
		// Print outro text if exist
		if ( ! empty( $instance['outro_text'] ) )
			echo '<div class="'. $this->id . '-outro-text outro-text">' . $instance['outro_text'] . '</div>';

		// Close the theme's widget wrapper
		echo $after_widget;
	}

	
	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 1.0.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Set the instance to the new instance.
		$instance = $new_instance;
		
		// If new template is chosen, reset includes and excludes.
		if ( $instance['template'] !== $old_instance['template'] && '' !== $old_instance['template'] ) {
			$instance['counter_image'] 		= '';
			$instance['bg_color'] 			= ''; // deprecated
			$instance['counter_bg_color']	= '';
			$instance['label_bg_color']		= '';
			$instance['counter_color'] 		= '';
			$instance['font_size'] 			= ''; // deprecated
			$instance['counter_size']		= '';
			$instance['label_size'] 		= '';
			$instance['label_color'] 		= '';
		}		

		$instance['title'] 				= strip_tags( $new_instance['title'] );
		$instance['title_icon']			= strip_tags( $new_instance['title_icon'] );
		$instance['counter'] 			= $new_instance['counter'];
		$instance['until'] 				= $new_instance['until'];
		$instance['cLabels'] 			= $new_instance['cLabels'];
		$instance['cLabels1'] 			= $new_instance['cLabels1'];
		$instance['compactLabels'] 		= $new_instance['compactLabels'];
		$instance['relative'] 			= $new_instance['relative'];
		$instance['format'] 			= $new_instance['format'];
		$instance['expiryUrl'] 			= strip_tags( $new_instance['expiryUrl'] );
		$instance['expiryText'] 		= $new_instance['expiryText'];	// allow HTML tag saved
		$instance['serverSync'] 		= isset( $new_instance['serverSync'] ) ? 1 : 0;
		$instance['alwaysExpire'] 		= isset( $new_instance['alwaysExpire'] ) ? 1 : 0;
		$instance['compact'] 			= isset( $new_instance['compact'] ) ? 1 : 0;
		$instance['onExpiry'] 			= $new_instance['onExpiry'];
		$instance['onTick'] 			= $new_instance['onTick'];
		$instance['tickInterval'] 		= strip_tags( $new_instance['tickInterval'] );
		
		$instance['template'] 			= $new_instance['template'];
		$instance['toggle_active'] 		= $new_instance['toggle_active'];
		$instance['intro_text'] 		= $new_instance['intro_text'];
		$instance['outro_text'] 		= $new_instance['outro_text'];
		$instance['customstylescript']	= $new_instance['customstylescript'];
		
		return $instance;
	}

	
	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 1.0.0
	 */
	function form( $instance ) {		
		
		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( (array) $instance, countdown_default_args() );
		$instance['id'] = $this->number;
		
		$tabs = array( 
			__( 'General', $this->textdomain ),  
			__( 'Format', $this->textdomain ),
			__( 'Customs', $this->textdomain ),
			__( 'Styling', $this->textdomain ),
			__( 'Advanced', $this->textdomain ),
			__( 'Supports', $this->textdomain )
		);			

		// Set the default value of each widget input
		global $wp_locale;
		$time_adj = current_time('timestamp');
		$counterList = array( 'until' => __( 'Until', $this->textdomain) , 'since' => __( 'Since', $this->textdomain  ));
		?>

		<div class="pluginName"><?php echo $this->name; ?><span class="pluginVersion"><?php echo $this->version; ?></span></div>
		<div id="tcp-<?php echo $this->id ; ?>" class="total-options tabbable tabs-left">
			<ul class="nav nav-tabs">
				<?php foreach ($tabs as $key => $tab ) : ?>
					<li class="<?php echo $instance['toggle_active'][$key] ? 'active' : '' ; ?>"><?php echo $tab; ?><input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo $instance['toggle_active'][$key]; ?>" /></li>
				<?php endforeach; ?>							
			</ul>
			<ul class="tab-content">
				<li class="tab-pane <?php if ( $instance['toggle_active'][0] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Give this widget a title, or leave empty for no title.', $this->textdomain ); ?></span>	
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />												
						</li>						
						<li>
							<div id ="until-<?php echo $this->id; ?>" class="curtime tc-curtime">
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
										<a class="cancel-timestamp hide-if-no-js button-cancel" href="#"><?php _e( 'Cancel', $this->textdomain ); ?></a>
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
							<span class="description"><?php _e( 'Text to display upon expiry, replacing the countdown.', $this->textdomain ); ?></span>	
							<textarea name="<?php echo $this->get_field_name( 'expiryText' ); ?>" id="<?php echo $this->get_field_id( 'expiryText' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['expiryText']); ?></textarea>						
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][1] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Date Format', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Format for display - upper case for always, lower case only if non-zero, \'Y\' years, \'O\' months, \'W\' weeks, \'D\' days, \'H\' hours, \'M\' minutes, \'S\' seconds', $this->textdomain ); ?></span>	
							<input type="text" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>" value="<?php echo esc_attr( $instance['format'] ); ?>" />							
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
									<td><span class="description"><?php _e( 'Year', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][0]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Month', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][1]; ?>" /></td>
								</tr>
								<tr>
									<td><span class="description"><?php _e( 'Week', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][2]; ?>" /></td>
									<td class="separator"></td>
									<td><span class="description"><?php _e( 'Day', $this->textdomain ); ?></span></td><td><input type="text" class="smallfat" name="<?php echo $this->get_field_name( 'compactLabels' ); ?>[]" value="<?php echo $instance['compactLabels'][3]; ?>" /></td>
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
							<input type="text" class="smallfat" id="<?php echo $this->get_field_id( 'tickInterval' ); ?>" name="<?php echo $this->get_field_name( 'tickInterval' ); ?>" value="<?php echo esc_attr( $instance['tickInterval'] ); ?>" />
							<span class="description"><?php _e( 'Interval (seconds) between onTick callbacks', $this->textdomain ); ?></span>	
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][3] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template', $this->textdomain ); ?></label> 
							<span class="description"><?php _e( 'Select the countdown template', $this->textdomain ); ?></span>
							<select onchange="wpWidgets.save(jQuery(this).closest('div.widget'),0,1,0);" class="smallfat" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>">
								<?php foreach ( countdown_templates() as $t => $template ) { ?>
									<option value="<?php echo esc_attr( $t ); ?>" <?php selected( $instance['template'], $t ); ?>><?php echo esc_html( $template ); ?></option>
								<?php } ?>
							</select>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Preview', $this->textdomain ); ?></label> 
							<span class="description"><?php _e( 'A sample preview, can be different in the front end.', $this->textdomain ); ?></span><br />
							<?php $url =  esc_url( add_query_arg( array( 'action' => 'countdown-preview', 'id' => $instance['id'], 'class' => $instance['template'] ), admin_url('admin-ajax.php') ) ); ?>
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
							<label><?php _e( 'Shortcode & Function', $this->textdomain ) ; ?></label>
							<span class="description">								
								<?php _e( '<strong>Note</strong>: Drag this widget to the "Inactive Widgets" at the bottom of this page if you want to use this as a shortcode to your content or PHP function in your template with the codes above.', $this->textdomain ); ?>
								<span class="shortcode">
									<?php _e( 'Widget Shortcode: ', $this->textdomain ); ?><?php echo '[countdown-widget id="' . $this->number . '"]'; ?><br />
									<?php _e( 'PHP Function: ', $this->textdomain ); ?><?php echo '&lt;?php echo do_shortcode(\'[countdown-widget id="' . $this->number . '"]\'); ?&gt;'; ?>						
								</span>
							</span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'title_icon' ); ?>"><?php _e( 'Widget Title Icon', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Attach an image or icon for the widget title', $this->textdomain ); ?></span>
							<img alt="" class="optionImage" src="<?php echo esc_attr( $instance['title_icon'] ); ?>">
							<a href="#" class="add-image button"><?php _e( 'Add Image', $this->textdomain ); ?></a>
							<a class="<?php if ( empty($instance['title_icon'] ) ) : ?>hidden <?php endif; ?>remove-image button" href="#"><?php _e('Remove', $this->textdomain); ?></a>
							<input type="hidden" id="<?php echo $this->get_field_id( 'title_icon' ); ?>" name="<?php echo $this->get_field_name( 'title_icon' ); ?>" value="<?php echo esc_attr( $instance['title_icon'] ); ?>" />
						</li>
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
						<li>
							<label for="<?php echo $this->get_field_id('customstylescript'); ?>"><?php _e( 'Custom Script & Stylesheet', $this->textdomain ) ; ?></label>
							<span class="description"><?php _e( 'Use this box for additional widget CSS style of custom javascript. Current widget selector: ', $this->textdomain ); ?><?php echo '<tt>#' . $this->id . '</tt>'; ?></span>
							<textarea name="<?php echo $this->get_field_name( 'customstylescript' ); ?>" id="<?php echo $this->get_field_id( 'customstylescript' ); ?>" rows="3" class="widefat code"><?php echo htmlentities($instance['customstylescript']); ?></textarea>
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][5] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<h3>Support and Contribute</h3>
							<p>Please ask us for supports or discussing new features for the next updates.<p>
							<ul>
								<li>
									<a href="http://codecanyon.net/user/zourbuth#message?ref=zourbuth"><strong>CodeCanyon Author Profile Page</strong></a>
									<span class="description"><?php _e( 'Send private mail message via codecanyon.net', $this->textdomain ); ?></span>
								</li>
								<li>
									<a href="http://codecanyon.net/item/the-countdown-pro/3228499/comments?ref=zourbuth"><strong>Plugin Discussion</strong></a>
									<span class="description"><?php _e( 'Discuss or post query in the item dicussion forum.', $this->textdomain ); ?></span>
								</li>
								<li>
									<p style="margin-bottom: 5px;"><a href="javascript: void(0)"><strong>Tweet to Get Supports</strong></a></p>
									<a href="https://twitter.com/intent/tweet?screen_name=zourbuth" class="twitter-mention-button" data-related="zourbuth">Tweet to @zourbuth</a>
									<a href="https://twitter.com/zourbuth" class="twitter-follow-button" data-show-count="false">Follow @zourbuth</a>									
								</li>
								<li>
									<span class="description"><?php _e( 'Help us to share this plugin.', $this->textdomain ); ?></span>
									<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://codecanyon.net/item/the-countdown-pro/3228499" data-text="Check out this WordPress Plugin 'The Countdown Pro'">Tweet</a>
									
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
									<script>if( typeof(twttr) !== 'undefined' ) twttr.widgets.load()</script>								
								</li>
							</ul>
						</li>
					</ul>
				</li>					
			</ul>
		</div>
	<?php
	}
}
?>