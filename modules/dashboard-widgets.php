<?php

defined( 'ABSPATH' ) || exit;


// Remove core dashboard widgets for users who only need the BC welcome box (not editors/uploaders).
// Must run on wp_dashboard_setup: widgets are not registered yet during admin_init.
add_action( 'wp_dashboard_setup', 'bc_custom_remove_dashboard_meta' );
add_action( 'wp_user_dashboard_setup', 'bc_custom_remove_dashboard_meta' );

function bc_custom_remove_dashboard_meta() {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'upload_files' ) ) {
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
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
		<p>Content posted on the BC CMS is subject to the <a href="https://www.bellevuecollege.edu/faculty-staff/style-guide/" target="_blank" rel="noopener noreferrer">BC Style Guide</a>. 
		If you run in to issues, please submit a ticket through the <a href="https://www.bellevuecollege.edu/servicedesk/" target="_blank" rel="noopener noreferrer">Service Desk</a>.</p>';
}

/**
 * Add dashboard user list meta box
 * 
 * List admin users on the dashboard
 */
add_action( 'wp_dashboard_setup', 'bc_user_list_meta_box' );

function bc_user_list_meta_box() {
	if ( current_user_can( 'edit_posts' ) || current_user_can( 'upload_files' ) ) {
		wp_add_dashboard_widget(
			'bc_user_list_meta_box_widget',  // Widget slug.
			'Users with Website Access',     // Title.
			'bc_user_list_meta_box_function' // Display function.
		);
	}
}

// Meta box content
function bc_user_list_meta_box_function() {

	// Same role list as My Sites (see bc_custom_website_access_roles in the main plugin file).
	$user_list = get_users( array(
		'role__in' => bc_custom_website_access_roles(),
		'orderby'  => 'display_name',
		)
	);

	// Build output
	$output = '';
	$output .= '<ul>';

	// Output user list
	foreach ( $user_list as $user ) {
		$output .= '<li>';
		$output .= '<strong><a href="' . esc_url( 'mailto:' . $user->user_email ) . '">' . esc_html( $user->display_name ) . '</a></strong>';
		$output .= '<ul>';
		$output .= '<li>Role(s): ';

		foreach ( (array) $user->roles as $role ) {
			$output .= esc_html( $role ) . ' ';
		}
		$output .= '</li>';

		if ( function_exists( 'pp_get_groups_for_user' ) ) {
			$groups = pp_get_groups_for_user( $user->ID, 'pp_group', array( 'metagroup_type' => null, 'status' => 'active' ) );
			if ( ! empty( $groups ) ) {
				$output .= '<li>Groups: ';
				foreach ( $groups as $group ) {
					if ( empty( $group->group_name ) ) {
						continue;
					}
					$output .= esc_html( $group->group_name ) . ', ';
				}
				$output .= '</li>';
			}
		}
		$output .= '</ul>';
		$output .= '</li>';
	}
	$output .= '</ul><hr />';

	$output .= '<p>To request changes to website managers, please submit a <a href="https://www.bellevuecollege.edu/servicedesk/" target="_blank" rel="noopener noreferrer">Service Desk ticket</a>.</p>';

	// Echo output
	echo $output;
}
