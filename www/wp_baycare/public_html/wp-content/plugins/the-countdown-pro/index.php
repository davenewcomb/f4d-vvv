<?php
/*
    Plugin Name: The Countdown Pro
    Plugin URI: http://codecanyon.net/item/the-countdown-pro/3228499?ref=zourbuth
    Description: A complete post shortcode, meta options and powerfull widget to use counter in your site. The countdown functionality can easily be added to a content or sidebar area and let your users know the counts. With counting down and up functionality, gives you a full control to your counter. <strong>Note</strong>: Please deactivate all other countdown plugins before activating this plugin.
    Version: 2.1.0
    Author: zourbuth
    Author URI: http://zourbuth.com
    License: GPL2
    
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


/**
 * Exit if accessed directly
 * @since 1.4.2
 */
if ( ! defined( 'ABSPATH' ) )
	exit;


// Set constant
define( 'THE_COUNTDOWN_PRO', true );
define( 'THE_COUNTDOWN_PRO_VERSION', '2.1.0' );
define( 'THE_COUNTDOWN_PRO_DIR', plugin_dir_path( __FILE__ ) );
define( 'THE_COUNTDOWN_PRO_URL', plugin_dir_url( __FILE__ ) );
define( 'THE_COUNTDOWN_PRO_NAME', 'The Countdown Pro' );
define( 'THE_COUNTDOWN_PRO_SLUG', 'the_countdown_pro' );
define( 'THE_COUNTDOWN_PRO_LANG', 'the-countdown-pro' );


// Launch the plugin
register_activation_hook( __FILE__, 'the_countdown_pro_activation_hook' );
add_action( 'plugins_loaded', 'the_countdown_pro_plugin_loaded', 9 );


/**
 * Save plugin version on activation
 * @since 1.4.2
 */
function the_countdown_pro_activation_hook() {
	add_option( 'tcpro_version', THE_COUNTDOWN_PRO_VERSION );
}


/**
 * Initializes the plugin and it's features
 * Load necessary plugin files and add action to widget init
 * @since 1.0.0
 */
function the_countdown_pro_plugin_loaded() {	
	require_once( THE_COUNTDOWN_PRO_DIR . 'lib/total.php' );
	require_once( THE_COUNTDOWN_PRO_DIR . 'countdown.php' );
	require_once( THE_COUNTDOWN_PRO_DIR . 'templates.php' );
	require_once( THE_COUNTDOWN_PRO_DIR . 'setting.php' );
	require_once( THE_COUNTDOWN_PRO_DIR . 'shortcode.php' );
}
?>