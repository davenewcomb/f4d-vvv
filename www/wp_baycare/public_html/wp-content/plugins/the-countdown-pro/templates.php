<?php
/*
    The Countdown Pro Widget Class
    @since 2.0.0
    Copyright 2014 zourbuth.com [zourbuth@gmail.com]

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
* Returns the countdown templates name
* @return array
* @since 2.0.0
*/
function countdown_templates() {
	$textdomain = THE_COUNTDOWN_PRO_LANG;
	$templates = array(
		'default' 	=> __( 'Default', $textdomain ),
		'minimal' 	=> __( 'Minimal', $textdomain ),
		'calendar' 	=> __( 'Calendar', $textdomain ),
		'thedays' 	=> __( 'The Days', $textdomain ),
		'windows8' 	=> __( 'Windows 8', $textdomain )
	);

	return apply_filters( 'countdown_templates', $templates );
}


/**
 * Returns the countdown layout
 * Layout with opening and closing periode will be hidden if zero, example {d<}{dn} {dl}{d>}
 * @param $args
 * @since 2.0.0
 */
function countdown_layouts( $args ) {
	$layout = '';
	$show = strlen( $args['format'] );

	switch ( $args['template'] ) :		
		case 'calendar' :							
			$layout .= '<span class="countdown-row countdown-show'.$show.'">';
			$layout .= '{y<}<span class="countdown-section"><span class="countdown-period">{yl}</span><span class="countdown-amount">{ynn}</span></span>{y>}';
			$layout .= '{o<}<span class="countdown-section"><span class="countdown-period">{ol}</span><span class="countdown-amount">{onn}</span></span>{o>}';
			$layout .= '{w<}<span class="countdown-section"><span class="countdown-period">{wl}</span><span class="countdown-amount">{wnn}</span></span>{w>}';
			$layout .= '{d<}<span class="countdown-section"><span class="countdown-period">{dl}</span><span class="countdown-amount">{dnn}</span></span>{d>}';
			$layout .= '{h<}<span class="countdown-section"><span class="countdown-period">{hl}</span><span class="countdown-amount">{hnn}</span></span>{h>}';
			$layout .= '{m<}<span class="countdown-section"><span class="countdown-period">{ml}</span><span class="countdown-amount">{mnn}</span></span>{m>}';
			$layout .= '{s<}<span class="countdown-section"><span class="countdown-period">{sl}</span><span class="countdown-amount">{snn}</span></span>{s>}';
			$layout .= '</span>';
		break;

		case 'minimal' :
			$layout .= '{y<}{yn} {yl}{y>} {d<}{dn} {dl}{d>} {hnn}{sep}{mnn}{sep}{snn}';
		break;

		case 'thedays' :
			$layout .= '<span class="countdown-row countdown-show'.$show.'">';
			$layout .= '{d<}<span class="countdown-section countdown-days"><span class="countdown-days-amount">{dn}</span><span class="countdown-days-period">{dl}</span></span>{d>}';
			$layout .= '{h<}<span class="countdown-section"><span class="countdown-period">{hl}</span><span class="countdown-amount">{hnn}</span></span>{h>}';
			$layout .= '{m<}<span class="countdown-section countdown-minutes"><span class="countdown-period">{ml}</span><span class="countdown-amount">{mnn}</span></span>{m>}';
			$layout .= '{s<}<span class="countdown-section"><span class="countdown-period">{sl}</span><span class="countdown-amount">{snn}</span></span>{s>}';
			$layout .= '</span>';
		break;
	endswitch;

	return apply_filters( 'countdown_layouts', $layout, $args );
}


/**
 * Returns the countdown layout
 * Layout with opening and closing periode will be hidden if zero, example {d<}{dn} {dl}{d>}
 * @param $args
 * @since 2.0.0
 */
function countdown_styles( $args, $echo = true ) {
	extract( $args );
	$styles = '';
	$id = "countdown-$id";
	
	switch ( $template ) :		
		case 'calendar' :							
			if ( $counter_color )  		$styles .= "#$id .countdown-amount {color: $counter_color;} \n";
			if ( $label_color )  		$styles .= "#$id .countdown-period {color: $label_color;} \n";
			if ( $counter_size )		$styles .= "#$id .countdown-amount {font-size: {$counter_size}px;} \n";
			if ( $label_size ) 			$styles .= "#$id .countdown-period {font-size: {$label_size}px;} \n";
			if ( $counter_bg_color )	$styles .= "#$id .countdown-amount {background-color: {$counter_bg_color};} \n";
			if ( $label_bg_color ) 		$styles .= "#$id .countdown-period {background-color: {$label_bg_color};} \n";	
		break;

		case 'minimal' :
			if ( $counter_color )	$styles .= "#$id {color: $counter_color;} \n";
			if ( $counter_size ) 	$styles .= "#$id {font-size: {$counter_size}px; font-weight: bold;} \n";
		break;

		case 'thedays' :
			if ( $counter_color )  		$styles .= "#$id .countdown-amount {color: $counter_color;} \n";
			if ( $label_color )  		$styles .= "#$id .countdown-period {color: $label_color;} \n";
			if ( $counter_size )		$styles .= "#$id .countdown-amount {font-size: {$counter_size}px;} \n";
			if ( $label_size )			$styles .= "#$id .countdown-period {font-size: {$label_size}px;} \n";
			if ( $counter_bg_color )	$styles .= "#$id .countdown-amount {background-color: {$counter_bg_color};} \n";
			if ( $label_bg_color )		$styles .= "#$id .countdown-period {background-color: {$label_bg_color};} \n";	
		break;
		
		case 'windows8' :
			if ( $bg_color ) 		$styles .= "#$id .windows8 {background-color: $bg_color;} \n";
			if ( $counter_image ) 	$styles .= "#$id .windows8 {background-image: url('$counter_image');} \n";
			if ( $font_size ) 		$styles .= "#$id .countdown-amount {font-size: {$font_size}px;} \n";
			if ( $counter_color )  	$styles .= "#$id .countdown-amount {color: $counter_color;} \n";			
			if ( $label_size ) 		$styles .= "#$id .countdown-period {font-size: {$label_size}px;} \n";
			if ( $label_color )  	$styles .= "#$id .countdown-period {color: $label_color;} \n";
		break;
		
		default :
			if ( $counter_color )  		$styles .= "#$id .countdown-amount {color: $counter_color;}". "\n";
			if ( $label_color )  		$styles .= "#$id .countdown-period {color: $label_color;}". "\n";
			if ( $counter_size )		$styles .= "#$id .countdown-amount {font-size: {$counter_size}px;}". "\n";
			if ( $label_size ) 			$styles .= "#$id .countdown-period {font-size: {$label_size}px;}". "\n";
			if ( $counter_bg_color )	$styles .= "#$id .countdown-amount {background-color: {$counter_bg_color};}". "\n";
			if ( $label_bg_color ) 		$styles .= "#$id .countdown-period {background-color: {$label_bg_color};}". "\n";	
			if ( $counter_image ) 		$styles .= "#$id .countdown-section {background-image: url('$counter_image');}". "\n";	
		break;
	endswitch;

	$styles = apply_filters( 'countdown_styles', $styles, $args );
	if( $styles ) {
		$styles = "<style type='text/css'> \n" .$styles . "</style> \n";	
		if( $echo )
			echo $styles;
		else 
			return $styles;
	}
}
?>