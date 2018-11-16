<?php
/*
Plugin Name: BC WordPress Branding
Plugin URI: https://github.com/BellevueCollege/bc-wordpress-branding
Description: Add BC Branding customizations to WordPress dashboard
Author: Bellevue College Integration Team
Version: 1.2.1.1 #{versionStamp}#
Author URI: http://www.bellevuecollege.edu
GitHub Plugin URI: BellevueCollege/bc-wordpress-branding
*/

defined( 'ABSPATH' ) || exit;

// Include plugin assets
include_once( plugin_dir_path( __FILE__ ) . 'modules/admin-bar.php' );
include_once( plugin_dir_path( __FILE__ ) . 'modules/dashboard-widgets.php' );
include_once( plugin_dir_path( __FILE__ ) . 'modules/profile-fields.php' );
