<?php
/*
	The Countdown Pro Version Update Checker
	Author: zourbuth
	Author URI: http://zourbuth.com
	License: Under GPL2
	@since 1.4.4

	Copyright 2013 zourbuth (email : zourbuth@gmail.com)

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


class The_Countdown_Pro_Plugin_Update {
	
	function __construct() {
		add_filter( 'pre_set_site_transient_update_plugins', array(&$this, 'check_update') );
		add_action( 'admin_bar_menu', array(&$this, 'update_notifier') );
		add_action( 'admin_head', array(&$this, 'admin_head') );
	}


	/**
	 * Create update notification the admin section
	 * Get the current and new plugin version for a set of seconds
	 * @param none
	**/		
	function update_notifier() {

		if ( ! is_super_admin() || ! is_admin_bar_showing() )
			return;
		
		global $wp_admin_bar;
		$version = get_option( 'tcpro_version' );
		$new_version = get_option( 'tcpro_new_version' );

		if( $new_version && version_compare( $version, $new_version ) == -1 ) {
			$wp_admin_bar->add_menu( array( 
				'id' 	=> 'update_notifier', 
				'title' => __( 'The Countdown Pro <span class="zpversion">new version available</span>', 'the-countdown-pro' ), 
				'href' 	=> 'http://codecanyon.net/item/the-countdown-pro/3228499?ref=zourbuth'
			));
		}
	}


	/**
	 * Create update notification the admin section
	 * Get the current and new plugin version for a set of seconds
	 * @param none
	**/		
	function admin_head()  {
		?><style type="text/css">
		#wpadminbar span.zpversion {
			background-color: #BFBFBF;
			border: 1px solid #000000;
			border-radius: 2px 2px 2px 2px;
			color: #464646;
			font-size: 11px;
			font-weight: bold;
			padding: 1px 3px;
			text-shadow: 0 -1px 0 #C5C5C5;
		}
		#wpadminbar a:hover span.zpversion {
			background-color: #f2f2f2;
			text-shadow: none;
		}</style><?php
	}


	/**
	 * Get the update version with the amount of time
	 * Get the current and new plugin version for a set of seconds
	 * @param none
	**/		
	function check_update( $transient ) {

		// Set the update url for retrieving the new version
		$update_url = 'http://zourbuth.com/updates/tcpro.php';
		
		// Let check if curl enable, if not, use the file_get_contents()
		if( function_exists( 'curl_init' ) ) {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $update_url );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			$data = curl_exec($ch);
			curl_close($ch);
		} else {
			$data = file_get_contents( $update_url );
		}
			
		if ( FALSE !== $data ) {
			$new_version = json_decode( $data );
			update_option( 'tcpro_new_version', $new_version );			
		}
		
		return $transient;
	}
}

new The_Countdown_Pro_Plugin_Update();
?>