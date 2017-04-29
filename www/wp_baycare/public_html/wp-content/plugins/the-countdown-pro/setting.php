<?php
/*
    The Countdown Pro Settings
	
	Copyright 2012  zourbuth.com  (email : zourbuth@gmail.com)

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

class The_Countdown_Pro_Options extends Total_Options {
	
	var $textdomain;
	/**
	 * Construct	 
	 * @since 1.0
	 */
	function __construct() {
		$this->textdomain = THE_COUNTDOWN_PRO_LANG;
		
        parent::__construct( array(
			'sections'	=> array (
							'general'	=> __( 'General', $this->textdomain ),
							'labeling'	=> __( 'Labelling', $this->textdomain )		
						),
			'lang'		=> THE_COUNTDOWN_PRO_LANG,
			'title'		=> 'Countdown Pro',
			'slug'		=> THE_COUNTDOWN_PRO_SLUG,
		));
	}
	

	/**
	 * Settings and defaults
	 * @since 1.1
	 */
	function create_options() {
		
		/* General Settings
		===========================================*/
		$this->options['cpt'] = array(
			'section'	=> 'general',
			'title'		=> __( 'Meta Option', $this->textdomain ),
			'desc'		=> __( 'Check the custom post type(s) to enable the meta option', $this->textdomain ),
			'type'    	=> 'checkbox',
			'opts'		=> array( 'post', 'page' ) + get_post_types( array( '_builtin' => false, 'public' => true ), 'names' ),
			'std'		=> array( 'post' )
		);		
		$this->options['enable_custom'] = array(
			'section' => 'general',
			'title'   => __( 'Enable Custom', $this->textdomain ),
			'desc'    => __( 'Check this to push the style script option below', $this->textdomain ),
			'type'    => 'checkbox',
			'std'     => false
		);
		$this->options['custom'] = array(
			'section' => 'general',
			'title'   => __( 'Custom Style & Script', $this->textdomain ),
			'desc'    => __( 'Use this option to add additional styles or script with the tag included.', $this->textdomain ),
			'type'    => 'textarea',
			'std'     => ''
		);
		
		/* Labeling Settings
		===========================================*/
		$this->options['labels'] = array(
			'section'	=> 'labeling',
			'title'		=> __( 'Plural Labels', $this->textdomain ),
			'desc'		=> __( 'The countdown labels for plural counter.', $this->textdomain ),
			'type'		=> 'multitext',
			'std'		=> array(
				'years' 	=> __( 'Years', $this->textdomain ),
				'months' 	=> __( 'Months', $this->textdomain ),
				'weeks' 	=> __( 'Weeks', $this->textdomain ),
				'days' 		=> __( 'Days', $this->textdomain ),
				'hours' 	=> __( 'Hours', $this->textdomain ),
				'minutes' 	=> __( 'Minutes', $this->textdomain ),
				'seconds' 	=> __( 'Seconds', $this->textdomain )
			),
		);
		$this->options['labels1'] = array(
			'section'	=> 'labeling',
			'title'		=> __( 'Singular Labels', $this->textdomain ),
			'desc'		=> __( 'The countdown labels for single counter', $this->textdomain ),
			'type'		=> 'multitext',
			'std'		=> array(
				'year' 		=> __( 'Year', $this->textdomain ),
				'month' 	=> __( 'Month', $this->textdomain ),
				'week' 		=> __( 'Week', $this->textdomain ),
				'day' 		=> __( 'Day', $this->textdomain ),
				'hour' 		=> __( 'Hour', $this->textdomain ),
				'minute' 	=> __( 'Minute', $this->textdomain ),
				'second' 	=> __( 'Second', $this->textdomain )
			),
		);
		$this->options['compactLabels'] = array(
			'section'	=> 'labeling',
			'title'		=> __( 'Compact Labels', $this->textdomain ),
			'desc'		=> __( 'The countdown compact labels', $this->textdomain ),
			'type'		=> 'multitext',
			'std'		=> array(
				'y'	=> __( 'y', $this->textdomain ),
				'm'	=> __( 'm', $this->textdomain ),
				'w'	=> __( 'w', $this->textdomain ),
				'd'	=> __( 'd', $this->textdomain )
			),
		);
		$this->options['loading_text'] = array(
			'section' => 'labeling',
			'title'   => __( 'Loading Text', $this->textdomain ),
			'desc'    => __( 'Loading text before the countdown shows up.', $this->textdomain ),
			'type'    => 'text',
			'std'	  => __( 'Loading..', $this->textdomain )
		);		
	}
	
	function print_custom() {
		$option = get_option( $this->slug );
		if( isset( $option['enable_custom'] ) )
			echo $option['custom'];
	}
} // end class.

new The_Countdown_Pro_Options();
?>