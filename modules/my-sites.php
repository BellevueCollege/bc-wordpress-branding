<?php

add_filter( 'get_blogs_of_user', 'bc_custom_get_blogs_filter', 10, 3 );

function bc_custom_get_blogs_filter( $sites, $user_id, $all ) {

	if ( ! current_user_can( 'manage_network' ) &&  is_multisite() ) {
	
		// Transient Name
		$transient_name = 'bc-my-sites-' . wp_get_current_user()->user_login;
	
		// This is a heavy function, so we want to cache it using a transient
		$sites_cached = get_site_transient( $transient_name );

		// Is there anything in the cache?
		if ( false === $sites_cached ) {
		
			foreach ( (array) $sites as $key => $site ) {

				// Query site user table to see if user has a non-Subscriber role
				$props = array(
					'blog_id'      => $site->userblog_id,
					'role__in'     => array(
											'author',
											'contributor',
											'dept-site-owner',
											'editor',
											'gf-view-entries',
											'calendar-contributor',
											'core_site_editor',
											'core-site-editor',
										),
					'include'      => array( get_current_user_id() ),
					'fields'       => array( 'ID' ),
				);
				$users = get_users( $props );

				// Remove sites without the user from the sites array
				if ( ! isset( $users[0] ) ) {
					unset( $sites[ $key ] );
				}
			}

			// Cache this in a transient for 3 seconds
			set_site_transient( $transient_name, $sites, 3 );
			return $sites;
		}
		return $sites_cached;
	}
	return $sites;
}
