<?php

defined( 'ABSPATH' ) || exit;

// Remove WordPress logo and menu
add_action( 'admin_bar_menu', 'bc_custom_remove_wp_logo', 999 );

function bc_custom_remove_wp_logo( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-logo' );
}

// Add BC logo link to replace it
add_action( 'admin_bar_menu', 'bc_custom_admin_menus', 10 );

function bc_custom_admin_menus( $wp_admin_bar ) {

	// Add main BC menu
	$bc_menu_args = array(
		'id'	=> 'bc_menu',
		'title' => '<img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'img/bc-logo.png" alt="Bellevue College Content Management System" />',
		'href'  => 'https://www.bellevuecollege.edu',
	);
	$wp_admin_bar->add_node( $bc_menu_args );

	// Add Homepage submenu
	$bc_menu_home_args = array(
		'parent' => 'bc_menu',
		'id'	 => 'bc_menu_home',
		'title'  => 'Bellevue College Homepage',
		'href'   => 'https://www.bellevuecollege.edu',
	);
	$wp_admin_bar->add_node( $bc_menu_home_args );

	// Add Service Desk submenu
	$bc_menu_sd_args = array(
		'parent' => 'bc_menu',
		'id'	 => 'bc_menu_sd_args',
		'title'  => 'Get Help - BC Service Desk',
		'href'   => 'https://www.bellevuecollege.edu/servicedesk/',
	);
	$wp_admin_bar->add_node( $bc_menu_sd_args );
}

// Change 'Howdy' to 'Hello'
add_filter( 'admin_bar_menu', 'bc_custom_replace_howdy', 25 );

function bc_custom_replace_howdy( $wp_admin_bar ) {
	$my_account = $wp_admin_bar->get_node( 'my-account' );
	$newtitle = str_replace( 'Howdy,', 'Hello', $my_account->title );
	$wp_admin_bar->add_node( array(
		'id' => 'my-account',
		'title' => $newtitle,
	) );
}

// From 'Hide Admin Bar Search' plugin by Helen Hou-Sandi - http://www.helenhousandi.com
if ( ! function_exists( 'bc_custom_hide_admin_bar_search' ) ) {
	function bc_custom_hide_admin_bar_search() {
	?>
		<style type="text/css">
		#wpadminbar #adminbarsearch {
			display: none;
		}
		</style>
		<?php
	}
	add_action( 'admin_head', 'bc_custom_hide_admin_bar_search' );
	add_action( 'wp_head', 'bc_custom_hide_admin_bar_search' );
}


// Reconstruct My Sites menu, only listing sites where users can read private posts

/**
 * De-register the native WP Admin Bar My Sites function.
 *
 * Loading all sites menu for large multisite is inefficient and bad news. This de-registers the native WP function so it can be replaced with a more efficient one.
 */
add_action( 'add_admin_bar_menus', 'bc_custom_remove_my_sites', 10, 0 );

function bc_custom_remove_my_sites() {
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
}

/*
* Rebuild my-sites-list menu. Based on WP Core code
* Heavily inspired by https://wpartisan.me/tutorials/multisite-speed-improvements-admin-bar
*/
add_action( 'admin_bar_menu', 'bc_custom_wp_admin_bar_my_sites_menu' );

function bc_custom_wp_admin_bar_my_sites_menu( $wp_admin_bar ) {
	// Don't show for logged out users or single site mode.
	if ( ! is_user_logged_in() || ! is_multisite() )
		return;

	$wp_admin_bar->add_menu( array(
		'id'    => 'my-sites',
		'title' => __( 'My Sites' ),
		'href'  => $my_sites_url,
	) );

	// Menu for network admins
	if ( current_user_can( 'manage_network' ) ) {
		$wp_admin_bar->add_group( array(
			'parent' => 'my-sites',
			'id'     => 'my-sites-super-admin',
		) );

		$wp_admin_bar->add_menu( array(
			'parent' => 'my-sites-super-admin',
			'id'     => 'network-admin',
			'title'  => __('Network Admin'),
			'href'   => network_admin_url(),
		) );

		$wp_admin_bar->add_menu( array(
			'parent' => 'network-admin',
			'id'     => 'network-admin-d',
			'title'  => __( 'Dashboard' ),
			'href'   => network_admin_url(),
		) );
		$wp_admin_bar->add_menu( array(
			'parent' => 'network-admin',
			'id'     => 'network-admin-s',
			'title'  => __( 'Sites' ),
			'href'   => network_admin_url( 'sites.php' ),
		) );
		$wp_admin_bar->add_menu( array(
			'parent' => 'network-admin',
			'id'     => 'network-admin-u',
			'title'  => __( 'Users' ),
			'href'   => network_admin_url( 'users.php' ),
		) );
		$wp_admin_bar->add_menu( array(
			'parent' => 'network-admin',
			'id'     => 'network-admin-t',
			'title'  => __( 'Themes' ),
			'href'   => network_admin_url( 'themes.php' ),
		) );
		$wp_admin_bar->add_menu( array(
			'parent' => 'network-admin',
			'id'     => 'network-admin-p',
			'title'  => __( 'Plugins' ),
			'href'   => network_admin_url( 'plugins.php' ),
		) );
		$wp_admin_bar->add_menu( array(
			'parent' => 'network-admin',
			'id'     => 'network-admin-o',
			'title'  => __( 'Settings' ),
			'href'   => network_admin_url( 'settings.php' ),
		) );


		// Current Site Info
		$wp_admin_bar->add_menu( array(
			'id'    => 'bc-custom-current-site-info',
			'parent' => 'my-sites',
			'title' => 'Current Site Info',
			'href'  => admin_url(),
		));

		// Current Blog Name
		$wp_admin_bar->add_menu( array(
			'id'    => 'bc-custom-current-site-name',
			'parent' => 'bc-custom-current-site-info',
			'title' => get_bloginfo( 'name' ),
			'href'  => admin_url(),
		));

		// Current Blog ID
		$wp_admin_bar->add_menu( array(
			'id'    => 'bc-custom-current-site-id',
			'parent' => 'bc-custom-current-site-info',
			'title' => 'Current Site ID: ' . get_current_blog_id(),
			'href'  => network_admin_url( 'site-info.php?id=' . get_current_blog_id() ),
		));

		// Site Admins submenu
		$wp_admin_bar->add_menu( array(
			'id'    => 'bc-custom-current-site-user',
			'parent' => 'bc-custom-current-site-info',
			'title' => 'Site Administrators',
			'href'  => admin_url( 'users.php?role=dept-site-owner' ),
		));

		// Get Dept Site Owners
		$user_list = get_users( array(
			'role'    => 'dept-site-owner',
			'orderby' => 'display_name',
			)
		);

		// Loop through
		foreach ( $user_list as $user ) {
			$wp_admin_bar->add_menu( array(
				'id'     => 'bc-custom-current-site-user-' . $user->ID,
				'parent' => 'bc-custom-current-site-user',
				'title'  => esc_html( $user->display_name ),
				'href'   => admin_url( 'user-edit.php?user_id=' . $user->ID ),
			));
		}
	} else {
		// Standard site menus 
		$wp_admin_bar->add_group( array(
			'parent' => 'my-sites',
			'id'     => 'my-sites-list',
			'meta'   => array(
				'class' => is_super_admin() ? 'ab-sub-secondary' : '',
			),
		) );

		// Loop through sites that would display by default (all sites where user has any role)
		foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {

			// Query site user table to see if user has a non-Subscriber role
			$props = array(
				'blog_id'      => $blog->userblog_id,
				'role__in'     => array(
										'author',
										'contributor',
										'dept-site-owner',
										'editor',
										'gf-view-entries',
										'calendar-contributor',
									),    
				'include'      => array( get_current_user_id() ),
				'fields'       => array( 'ID' ),
			);
			$users = get_users( $props );
	
			// If a user is returned (has role), proceed
			if ( isset( $users[0] ) ) {
			
				$blavatar = '
		<div class="blavatar"></div>
 
		';

				$blogname = $blog->blogname;

				if ( ! $blogname ) {
					$blogname = preg_replace( '#^(https?://)?(www.)?#', '', $blog->siteurl );
				}

				$menu_id  = 'blog-' . $blog->userblog_id;

				$admin_url = $blog->siteurl . '/wp-admin';

				$wp_admin_bar->add_menu( array(
					'parent'    => 'my-sites-list',
					'id'        => $menu_id,
					'title'     => $blavatar . $blogname,
					'href'      => $admin_url,
				) );

				$wp_admin_bar->add_menu( array(
					'parent' => $menu_id,
					'id'     => $menu_id . '-d',
					'title'  => __( 'Dashboard' ),
					'href'   => $admin_url,
				) );

				$wp_admin_bar->add_menu( array(
					'parent' => $menu_id,
					'id'     => $menu_id . '-v',
					'title'  => __( 'Visit Site' ),
					'href'   => $blog->siteurl,
				) );
			}
		}
	}
}
