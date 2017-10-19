<?php

defined( 'ABSPATH' ) || exit;

// Prevent users from changing passwords
add_action( 'init','bc_custom_disable_profile_fields' );

function bc_custom_disable_profile_fields() {
	if ( ! current_user_can( 'setup_network' ) ) {
		add_filter( 'show_password_fields', '__return_false' );
		add_filter( 'allow_password_reset', '__return_false' );
	}
}

// Enqueue script to disable profile fields
add_action( 'admin_enqueue_scripts', 'bc_custom_disable_profile_fields_js' );

function bc_custom_disable_profile_fields_js( $hook ) {
	if ( 'profile.php' == $hook && ! current_user_can( 'setup_network' ) ) {
		wp_enqueue_script( 'disable_profile_fields', plugin_dir_url( dirname( __FILE__ ) ) . 'js/disable-profile-fields.js', array(), '1.0' );
	}
}