<?php
 /*
	Total Administration Page Framework 0.0.1
	http://zourbuth.com
	An advanced framework for plugins and themes administration panel.
	License Under GPL2

	Copyright 2013 zourbuth (email: zourbuth@gmail.com)

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
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! defined( 'TOTAL_VERSION' ) ) {

	define( 'TOTAL_VERSION', '0.0.1' );
	define( 'TOTAL_DIR', plugin_dir_path( __FILE__ ) );
	define( 'TOTAL_URL', plugin_dir_url( __FILE__ ) );
	
	require_once( TOTAL_DIR . 'options.php' );
	
	add_action( 'admin_init', 'total_admin_init' );
	
	/**
	 * Set constant path to the members plugin directory.
	 * Gets the filesystem directory path (with trailing slash) for the file passed in.
	 * Registers scripts and styles file in WordPress to be linked to a page
	 * @since 1.0.0
	 */
	function total_admin_init() {
		wp_register_style( 'total-dialog', TOTAL_URL . 'dialog.css', array('wp-color-picker'), TOTAL_VERSION, 'all' );
		wp_register_script( 'total-dialog', TOTAL_URL . 'jquery.dialog.js', array('jquery','wp-color-picker'), TOTAL_VERSION, false );
	}	
}
?>