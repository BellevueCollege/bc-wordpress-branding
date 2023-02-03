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

/**
 * Add dashboard user list meta box
 * 
 * List admin users on the dashboard
 */
add_action( 'wp_dashboard_setup', 'bc_user_list_meta_box' );

function bc_user_list_meta_box() {
	if ( current_user_can( 'edit_posts' ) ) {
		wp_add_dashboard_widget(
			'bc_user_list_meta_box_widget',  // Widget slug.
			'Users with Website Access',     // Title.
			'bc_user_list_meta_box_function' // Display function.
		);
	}
}

// Meta box content
function bc_user_list_meta_box_function() {

	// Get list of users in used roles
	$user_list = get_users( array(

		// Include these roles - this should move to configuration eventually
		'role__in' => array(
			'author',
			'contributor',
			'dept-site-owner',
			'editor',
			'gf-view-entries',
			'calendar-contributor',
			'core_site_editor',
			'core-site-editor',
		),
		'orderby' => 'display_name',
		)
	);

	// Build output
	$output = '';
	$output .= '<dl>';

	// Output user list
	foreach ( $user_list as $user ) {
		$output .= '<dt>';
		$output .= '<strong><a href="mailto:' . esc_html( $user->user_email ) . '">' . esc_html( $user->display_name ) . '</a></strong>';
		$output .= '</dt>';
		$output .= '<dd>Roles: ';

		// Output roles, space seperated
		foreach ( $user->roles as $role ) {
			$output .= $role . ' ';
		}
		$output .= '</dd>';
	}
	$output .= '</dl><hr />';

	// Output instructions
	$output .= '<p>To request changes to website managers, please submit a <a href="//www.bellevuecollege.edu/servicedesk/" target="_blank">Service Desk ticket</a>.</p>';

	// Echo output
	echo $output;
}
