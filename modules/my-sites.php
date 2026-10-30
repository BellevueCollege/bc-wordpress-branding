<?php

defined( 'ABSPATH' ) || exit;

// Above this count we use domain/path labels instead of loading each site's blogname option (switch_to_blog).
const BC_CUSTOM_SITE_TITLE_LOOKUP_LIMIT = 20;

// get_blogs_of_user() runs in more than one place (admin bar startup, My Sites screen, and others).
// Wrap admin bar startup so we can tell when the call comes from the toolbar and not from a screen like My Sites.
add_action( 'plugins_loaded', function () {
	remove_action( 'template_redirect', '_wp_admin_bar_init', 0 );
	remove_action( 'admin_init', '_wp_admin_bar_init' );
	remove_action( 'before_signup_header', '_wp_admin_bar_init' );
	remove_action( 'activate_header', '_wp_admin_bar_init' );

	$admin_bar_init = function () {
		// Set while WP_Admin_Bar::initialize() runs; cleared before the rest of the page loads.
		$GLOBALS['bc_custom_admin_bar_initializing'] = true;
		$result                                      = _wp_admin_bar_init();
		$GLOBALS['bc_custom_admin_bar_initializing'] = false;

		return $result;
	};

	add_action( 'template_redirect', $admin_bar_init, 0 );
	add_action( 'admin_init', $admin_bar_init );
	add_action( 'before_signup_header', $admin_bar_init );
	add_action( 'activate_header', $admin_bar_init );
}, 0 );

// Build the user's site list before core runs. Core would switch_to_blog once per membership to read blogname.
add_filter( 'pre_get_blogs_of_user', function ( $sites, $user_id, $all ) {

	if ( ! is_multisite() ) {
		return null;
	}

	// During admin bar startup only: return no sites for network admins. admin-bar.php builds a short
	// Network Admin menu and never loops user->blogs, so this list is not used on ordinary page loads.
	if ( ! empty( $GLOBALS['bc_custom_admin_bar_initializing'] ) && current_user_can( 'manage_network' ) ) {
		return array();
	}

	$user_id = (int) $user_id;
	if ( empty( $user_id ) ) {
		return array();
	}

	// Network admins keep every site in their membership (same as before this plugin filtered roles).
	// Skipping the subscriber check below avoids extra work on hundreds of sites. The top bar still
	// stays minimal; only admin-bar.php controls what appears there, not the length of this array.
	$skip_role_filter     = user_can( $user_id, 'manage_network' ) || current_user_can( 'manage_network' );
	$website_access_roles = bc_custom_website_access_roles();

	$site_has_website_access_role = function ( $meta_key ) use ( $website_access_roles, $user_id ) {
		$capabilities = get_user_meta( $user_id, $meta_key, true );
		$capabilities = maybe_unserialize( $capabilities );
		if ( ! is_array( $capabilities ) ) {
			return false;
		}

		return ! empty( array_intersect( $website_access_roles, array_keys( $capabilities ) ) );
	};

	global $wpdb;

	// One meta load; no get_users() per site.
	$user_meta = get_user_meta( $user_id );
	if ( empty( $user_meta ) ) {
		return array();
	}

	$site_ids = array();

	// Step 1: Figure out which blog IDs this user belongs to, using usermeta (not a query per site).

	// Main site (blog ID 1) stores capabilities under the base prefix, not wp_2_, wp_3_, etc.
	if ( isset( $user_meta[ $wpdb->base_prefix . 'capabilities' ] ) && defined( 'MULTISITE' ) ) {
		$main_capabilities_key = $wpdb->base_prefix . 'capabilities';
		if ( $skip_role_filter || $site_has_website_access_role( $main_capabilities_key ) ) {
			$site_ids[] = 1;
		}
		unset( $user_meta[ $main_capabilities_key ] );
	}

	// Other sites: one meta row per site, named like wp_123_capabilities.
	foreach ( array_keys( $user_meta ) as $meta_key ) {
		if ( ! str_ends_with( $meta_key, 'capabilities' ) ) {
			continue;
		}
		if ( $wpdb->base_prefix && ! str_starts_with( $meta_key, $wpdb->base_prefix ) ) {
			continue;
		}

		$site_id = str_replace( array( $wpdb->base_prefix, '_capabilities' ), '', $meta_key );
		if ( ! is_numeric( $site_id ) ) {
			continue;
		}

		// Non-network users: drop sites where they only have the subscriber role.
		if ( ! $skip_role_filter && ! $site_has_website_access_role( $meta_key ) ) {
			continue;
		}

		$site_ids[] = (int) $site_id;
	}

	$site_ids = array_values( array_unique( $site_ids ) );
	if ( empty( $site_ids ) ) {
		return array();
	}

	// Step 2: Load those blogs from wp_blogs in one query.
	$load_site_titles = count( $site_ids ) <= BC_CUSTOM_SITE_TITLE_LOOKUP_LIMIT;

	$get_sites_args = array(
		'number'   => '',
		'site__in' => $site_ids,
	);
	if ( ! $all ) {
		// Same as core: leave out archived, spam, and deleted sites unless $all is true.
		$get_sites_args['archived'] = 0;
		$get_sites_args['spam']     = 0;
		$get_sites_args['deleted']  = 0;
	}

	// Step 3: Build the same object shape core uses for get_blogs_of_user().
	$sites = array();

	foreach ( get_sites( $get_sites_args ) as $site ) {
		if ( $load_site_titles ) {
			// Few sites: read the real site title and URL (loads that site's options once each).
			$blogname = $site->blogname;
			$siteurl  = $site->siteurl;
		} else {
			// Many sites: use domain and path from wp_blogs so we do not switch into every site.
			$siteurl  = untrailingslashit( set_url_scheme( 'http://' . $site->domain . $site->path ) );
			$blogname = preg_replace( '#^(https?://)?(www\.)?#', '', $siteurl );
		}

		$sites[ $site->id ] = (object) array(
			'userblog_id' => $site->id,
			'blogname'    => $blogname,
			'domain'      => $site->domain,
			'path'        => $site->path,
			'site_id'     => $site->network_id,
			'siteurl'     => $siteurl,
			'archived'    => $site->archived,
			'mature'      => $site->mature,
			'spam'        => $site->spam,
			'deleted'     => $site->deleted,
		);
	}

	return $sites;
}, 10, 3 );
