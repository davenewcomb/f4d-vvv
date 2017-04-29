<?php
//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

delete_option('post_expiration_visit');
delete_option('post_expiration_visit');
delete_post_meta_by_key( 'the_countdown_pro' );
delete_post_meta_by_key( '_post_expiration_visit' );
delete_post_meta_by_key( '_post_expiration_elapse' );
?>