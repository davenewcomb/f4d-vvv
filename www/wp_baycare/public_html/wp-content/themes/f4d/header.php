<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="maincontentcontainer">
 *
 * @package F4D
 * @since F4D 1.0
 */
?><!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->


<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta http-equiv="cleartype" content="on">

	<!-- Responsive and mobile friendly stuff -->
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="wrapper" class="hfeed site">
<div class="top-bar">
<div class="top-bar-inner">
<?php 
$nap = get_field('display_nap', 'option');
$nap_loc = get_field('nap_location', 'option');

if ($nap == 'yes' && $nap_loc == 'top') {
	$nap_name = get_field('nap_name', 'option');
	$nap_add = get_field('nap_address', 'option');
	$nap_add_url = get_field('nap_address_url', 'option');
	$nap_phone = get_field('nap_phone', 'option');
	$nap_email = get_field('nap_email', 'option');
	?>
    
    <div id="NAP">
    <?php if ( !empty( $nap_name ) ) { ?>
    	<div id="NAP-name">
        <?php echo $nap_name; ?>
        </div>
	<?php } ?>
    
    <?php if ( !empty( $nap_add ) ) { ?>
    	<div id="NAP-address">
         <?php if ( !empty( $nap_add_url ) ) {?>
         	<a href="<?php echo $nap_add_url; ?>" title="Get directions"><i class="fa fa-location-arrow" aria-hidden="true"></i> <span><?php echo $nap_add; ?></span></a>
		<?php }
		 else {?>
         <?php echo $nap_add; ?>
		<?php }?>
        </div>
	<?php } ?>
    
    <?php if ( !empty( $nap_email ) ) { ?>
    	<div id="NAP-email">
        <a href="mailto:<?php echo $nap_phone; ?>" title="Email us"><i class="fa fa-envelope" aria-hidden="true"></i> <span><?php echo $nap_email; ?></span></a>
        </div>
	<?php } ?>
    
    <?php if ( !empty( $nap_phone ) ) { ?>
    	<div id="NAP-phone">
        <a href="tel:<?php echo preg_replace("/[^0-9,.]/", "", $nap_phone); ?>" title="Call us"><i class="fa fa-phone" aria-hidden="true"></i> <span><?php echo $nap_phone; ?></span></a>
        </div>
	<?php } ?>
    </div>
<?php }

?>

<?php $socialplace = get_field('social_media_location', 'option');
					if ($socialplace == "header"){ ?>
                    <div class="social-media-icons">
						<?php echo f4d_get_social_media(); ?>
                        </div>
					<?php }?>

<?php
if ( has_nav_menu( 'top-bar-menu' ) ) {
    wp_nav_menu( array( 'theme_location' => 'top-bar-menu' ) );
} ?>
</div></div>
<?php 
				$header_function = get_field('header_function', 'option');
				
				$logo_place = get_field('logo_placement', 'option');
				
				$attachment_id = get_field('header_logo', 'option');
				if ( !empty($attachment_id)) {
				$metadata = wp_get_attachment_metadata($attachment_id);
				
				$img_size = $metadata['width'];

				$width = '' . $img_size . 'px';
				}
				
				else {
					$width = '300px';
				}
								
				$logo_url = $metadata['file'];
				
				
				if ($logo_place == 'center') {
					$width = '100%';
				}
				
				if ($logo_place == 'center') {
				$nav_loc = get_field('main_nav_location', 'option');
				}
				else {
				$nav_loc = get_field('main_nav_location_side', 'option');	
				}
				
				$mobile_nav = get_field('mobile_nav', 'option');
				
			?>
	<div id="headercontainer" class="logo-<?php echo $logo_place; ?> <?php echo $header_function ?>">
    <?php if ($nav_loc == "above") {?>
    				<nav id="site-navigation" class="main-navigation mobile-<?php echo $mobile_nav ?>" role="navigation">
					<h3 class="menu-toggle"><?php esc_html_e( 'Menu', 'f4d' ); ?></h3>
					<div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'f4d' ); ?>"><?php esc_html_e( 'Skip to content', 'f4d' ); ?></a></div>
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
				</nav> <!-- /.site-navigation.main-navigation -->
<?php } ?>
		<header id="masthead" class="site-header row" role="banner">
        <div id="masthead-inner">
            
            <div class="site-title" style="max-width: <?php echo $width; ?>">
            
            
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" rel="home">
						<?php 
						if( !empty( $attachment_id ) ) { ?>
							<img src="/wp-content/uploads/<?php echo $logo_url; ?>" alt="<?php echo get_bloginfo( 'name' ); ?> Logo" />
						<?php } 
						else {
							echo get_bloginfo( 'name' );
						} ?>
					</a>
			</div> <!-- /.site-title -->

			<div class="site-header-content">
		<?php 
if ( is_active_sidebar( 'top-widget-area' ) ) { ?>
						<div id="top-widget-area">
							<div id="top-widget-area-inner" role="complementary">
								<?php dynamic_sidebar( 'top-widget-area' ); ?>
							</div>
						</div> <!-- /top-widget-area -->
					<?php }
?>

				<?php if ($nav_loc == "inline") {?><nav id="site-navigation" class="main-navigation mobile-<?php echo $mobile_nav ?>" role="navigation">
					<h3 class="menu-toggle"><?php esc_html_e( 'Menu', 'f4d' ); ?></h3>
					<div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'f4d' ); ?>"><?php esc_html_e( 'Skip to content', 'f4d' ); ?></a></div>
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
				</nav> <!-- /.site-navigation.main-navigation -->
<?php } ?>
                
			</div> <!-- /.col.grid_7_of_12 --> 
            </div>
            <?php if ($nav_loc == "under") {?><nav id="site-navigation" class="main-navigation mobile-<?php echo $mobile_nav ?>" role="navigation">
					<h3 class="menu-toggle"><?php esc_html_e( 'Menu', 'f4d' ); ?></h3>
					<div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'f4d' ); ?>"><?php esc_html_e( 'Skip to content', 'f4d' ); ?></a></div>
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
				</nav> <!-- /.site-navigation.main-navigation -->
<?php } ?>
		</header> <!-- /#masthead.site-header.row -->

	</div> <!-- /#headercontainer -->
	<div id="bannercontainer">
		<div class="banner row">
			<?php if ( is_front_page() ) {
				// Count how many banner sidebars are active so we can work out how many containers we need
				$bannerSidebars = 0;
				for ( $x=1; $x<=2; $x++ ) {
					if ( is_active_sidebar( 'frontpage-banner' . $x ) ) {
						$bannerSidebars++;
					}
				}

				// If there's one or more one active sidebars, create a row and add them
				if ( $bannerSidebars > 0 ) { ?>
					<?php
					// Work out the container class name based on the number of active banner sidebars
					$containerClass = "grid_" . 12 / $bannerSidebars . "_of_12";

					// Display the active banner sidebars
					for ( $x=1; $x<=2; $x++ ) {
						if ( is_active_sidebar( 'frontpage-banner'. $x ) ) { ?>
							<div class="col <?php echo $containerClass?>">
								<div class="widget-area" role="complementary">
									<?php dynamic_sidebar( 'frontpage-banner'. $x ); ?>
								</div> <!-- /.widget-area -->
							</div> <!-- /.col.<?php echo $containerClass?> -->
						<?php }
					} ?>

				<?php }
			} ?>
		</div> <!-- /.banner.row -->
	</div> <!-- /#bannercontainer -->

	<div id="maincontentcontainer">
		<?php	do_action( 'f4d_before_woocommerce' ); ?>

	<?php if ( function_exists('yoast_breadcrumb') ) 
		{yoast_breadcrumb('<p id="breadcrumbs">','</p>');} ?>	