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


// Reconstruct My Sites menu, only listing sites that users can both edit and read

/*
* Disable My Sites List submenu in toolbar
* from http://wpjourno.com/my-sites-toolbar-menu-wordpress-multisite/
*/
add_action( 'admin_bar_menu', 'bc_custom_remove_my_sites', 999 );

function bc_custom_remove_my_sites( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'my-sites-list' );

}

/*
* Rebuild my-sites-list menu. Based on WP Core code
*/
add_action( 'admin_bar_menu', 'bc_custom_wp_admin_bar_my_sites_menu', 30 );

function bc_custom_wp_admin_bar_my_sites_menu( $wp_admin_bar ) {

	// Add site links
	$wp_admin_bar->add_group( array(
		'parent' => 'my-sites',
		'id'     => 'bc-custom-my-sites-list',
		'meta'   => array(
			'class' => current_user_can( 'manage_network' ) ? 'ab-sub-secondary' : '',
		),
	) );

	// Standard site menus 
	foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
		switch_to_blog( $blog->userblog_id );

		// Only output under certain conditions
		if ( current_user_can( 'edit_posts' ) 
			&& current_user_can( 'read' ) 
			&& ( ! current_user_can( 'manage_network' ) || is_main_site( $blog->userblog_id ) ) ) {
			
			$blavatar = '<div class="blavatar"></div>';
			$blogname = $blog->blogname;
			if ( ! $blogname ) {
				$blogname = preg_replace( '#^(https?://)?(www.)?#', '', get_home_url() );
			}
			$menu_id  = 'bc-custom-blog-' . $blog->userblog_id;
			$wp_admin_bar->add_menu( array(
				'parent'    => 'bc-custom-my-sites-list',
				'id'        => $menu_id,
				'title'     => $blavatar . $blogname,
				'href'      => admin_url(),
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => $menu_id,
				'id'     => $menu_id . '-d',
				'title'  => __( 'Dashboard' ),
				'href'   => admin_url(),
			) );
			if ( current_user_can( get_post_type_object( 'post' )->cap->create_posts ) ) {
				$wp_admin_bar->add_menu( array(
					'parent' => $menu_id,
					'id'     => $menu_id . '-n',
					'title'  => __( 'New Post' ),
					'href'   => admin_url( 'post-new.php' ),
				) );
			}
			$wp_admin_bar->add_menu( array(
				'parent' => $menu_id,
				'id'     => $menu_id . '-v',
				'title'  => __( 'Visit Site' ),
				'href'   => home_url( '/' ),
			) );
		} // Added
		restore_current_blog();
	}

	// Menu for network admins
	if ( current_user_can( 'manage_network' ) ) {

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
	}
}
