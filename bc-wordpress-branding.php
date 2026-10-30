<?php
/*
Plugin Name: BC WordPress Branding
Plugin URI: https://github.com/BellevueCollege/bc-wordpress-branding
Description: Add BC Branding customizations to WordPress dashboard
Author: Bellevue College Integration Team
Version: 1.6.1 #{versionStamp}#
Author URI: https://www.bellevuecollege.edu
Requires at least: 7.0
Tested up to: 7.0
Requires PHP: 7.4
GitHub Plugin URI: BellevueCollege/bc-wordpress-branding
*/

defined( 'ABSPATH' ) || exit;

// Include plugin assets
require_once( plugin_dir_path( __FILE__ ) . 'modules/my-sites.php' );
require_once( plugin_dir_path( __FILE__ ) . 'modules/admin-bar.php' );
require_once( plugin_dir_path( __FILE__ ) . 'modules/dashboard-widgets.php' );
require_once( plugin_dir_path( __FILE__ ) . 'modules/profile-fields.php' );
