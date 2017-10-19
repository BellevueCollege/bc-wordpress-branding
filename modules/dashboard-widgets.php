<?php

defined( 'ABSPATH' ) || exit;


// Remove dashboard meta boxes
add_action( 'admin_init', 'bc_custom_remove_dashboard_meta' );

function bc_custom_remove_dashboard_meta() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' ); //since 3.8
	}
}

/**
 * Add dashboard intro meta box
 * 
 * Welcome message and introductory content
 */
add_action( 'wp_dashboard_setup', 'bc_intro_meta_box' );

function bc_intro_meta_box() {
	wp_add_dashboard_widget(
		'bc_intro_meta_box_widget',                // Widget slug.
		'Welcome to Bellevue College\'s CMS',      // Title.
		'bc_intro_meta_box_function'               // Display function.
	);
}
// Meta box content
function bc_intro_meta_box_function() {
	echo '<p>The Bellevue College CMS is used to manage content across the Bellevue College website.</p>
		<p>Content posted on the BC CMS is subject to the <a href="//www.bellevuecollege.edu/styleguide/" target="_blank">BC Style Guide</a>. 
		If you run in to issues, please submit a ticket through the <a href="//www.bellevuecollege.edu/servicedesk/" target="_blank">Service Desk</a>.</p>';
}

