<?php
/*
Plugin Name: WordPress Social Login
Plugin URI: http://miled.github.io/wordpress-social-login/
Description: Allow your visitors to comment and login with social networks such as Twitter, Facebook, Google, Yahoo and more.
Version: 2.2.3-rc.2
Author: Miled
Author URI: https://github.com/miled
License: MIT License
Text Domain: wordpress-social-login
Domain Path: /languages
*/

/*
*
*  Hi and thanks for taking the time to check out WSL code.
*
*  Please, don't hesitate to:
*
*   - Report bugs and issues.
*   - Contribute: code, reviews, ideas and design.
*   - Point out stupidity, smells and inconsistencies in the code.
*   - Criticize.
*
*  If you want to contribute, please consider these general "guide lines":
*
*   - Don't hesitate to delete code that doesn't make sense or looks redundant.
*   - Feel free to create new functions and files when needed.
*   - Avoid using 'switch' and 'for'.
*   - Avoid over-commenting.
*
*  Coding Style :
*
*   - Readable code.
*   - Clear indentations (4 chars).
*   - Same name convention of WordPress: those long long and self-explanatory functions and variables.
*
*  To keep the code accessible to everyone and easy to maintain for me, WordPress Social Login is programmed in
*  procedural PHP and will be kept that way.
*
*  If you have fixed, improved or translated something in WSL, Please consider contributing back to the project
*  and the WordPress community by submitting a Pull Request at https://github.com/hybridauth/WordPress-Social-Login
*
*  If you want to translate this plugin into your language (or to improve the current translation), see
*  wordpress-social-login/languages/readme.txt
*
*  Peace.
*
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// --------------------------------------------------------------------

session_id() or session_start();

$WORDPRESS_SOCIAL_LOGIN_VERSION = "2.2.3";

$_SESSION["wsl::plugin"] = "WordPress Social Login " . $WORDPRESS_SOCIAL_LOGIN_VERSION;

// --------------------------------------------------------------------

/**
* This file might be used to :
*     1. Redefine WSL constants, so you can move WSL folder around.
*     2. Define wsl_load_plugin_textdomain(), in case you want to use a custom translation.
*     3. Customize wsl_render_notice_page() and wsl_render_error_page().
*     4. Customize wsl_render_redirect_to_provider_loading_screen() and wsl_render_return_from_provider_loading_screen().
*     5. Implement your WSL hooks.
*/
if( file_exists( WP_PLUGIN_DIR . '/wp-social-login-custom.php' ) )
{
	include_once( WP_PLUGIN_DIR . '/wp-social-login-custom.php' );
}

// --------------------------------------------------------------------

/**
* Define WSL constants, if not already defined
*/
defined( 'WORDPRESS_SOCIAL_LOGIN_ABS_PATH' ) 
	|| define( 'WORDPRESS_SOCIAL_LOGIN_ABS_PATH', WP_PLUGIN_DIR . '/wordpress-social-login' );

defined( 'WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL' ) 
	|| define( 'WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL', plugins_url() . '/wordpress-social-login' );

defined( 'WORDPRESS_SOCIAL_LOGIN_HYBRIDAUTH_ENDPOINT_URL' ) 
	|| define( 'WORDPRESS_SOCIAL_LOGIN_HYBRIDAUTH_ENDPOINT_URL', WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL . '/hybridauth/' );

// --------------------------------------------------------------------

/**
* Check technical requirements before activating the plugin. 
*
* Wordpress 3.0 or newer required
*/
function wsl_activate()
{
	if( ! function_exists( 'register_post_status' ) )
	{
		deactivate_plugins( basename( dirname( __FILE__ ) ) . '/' . basename (__FILE__) );

		wp_die( __( "This plugin requires WordPress 3.0 or newer. Please update your WordPress installation to activate this plugin.", 'wordpress-social-login' ) );
	}
}

register_activation_hook( __FILE__, 'wsl_activate' );

// --------------------------------------------------------------------

/**
* Add a settings to plugin_action_links
*/
function wsl_add_plugin_action_links( $links, $file )
{
	static $this_plugin;

	if( ! $this_plugin )
	{
		$this_plugin = plugin_basename( __FILE__ );
	}

	if( $file == $this_plugin )
	{
		$wsl_links  = '<a href="options-general.php?page=wordpress-social-login">' . __( "Settings" ) . '</a>';
		array_unshift( $links, $wsl_links );
	}

	return $links;
}

add_filter( 'plugin_action_links', 'wsl_add_plugin_action_links', 10, 2 );

// --------------------------------------------------------------------

/**
* Add faq and user guide links to plugin_row_meta
*/
function wsl_add_plugin_row_meta( $links, $file )
{
	static $this_plugin;

	if( ! $this_plugin )
	{
		$this_plugin = plugin_basename( __FILE__ );
	}

	if( $file == $this_plugin )
	{
		$wsl_links = array(
			'<a href="http://miled.github.io/wordpress-social-login/">'             . __( "Manual" )            . '</a>',
			'<a href="http://miled.github.io/wordpress-social-login/faq.html">'     . __( "FAQ" )               . '</a>',
			'<a href="http://miled.github.io/wordpress-social-login/support.html">' . __( "Suppot" )            . '</a>',
			'<a href="https://github.com/miled/wordpress-social-login">'            . __( "Fork me on Github" ) . '</a>',
		);

		return array_merge( $links, $wsl_links );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'wsl_add_plugin_row_meta', 10, 2 );

// --------------------------------------------------------------------

/**
* Loads the plugin's translated strings.
*
* http://codex.wordpress.org/Function_Reference/load_plugin_textdomain
*/
if( ! function_exists( 'wsl_load_plugin_textdomain' ) )
{
	function wsl_load_plugin_textdomain()
	{
		load_plugin_textdomain( 'wordpress-social-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
	}
}

add_action( 'plugins_loaded', 'wsl_load_plugin_textdomain' );

// -------------------------------------------------------------------- 

/* includes */

# WSL Settings
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/settings/wsl.providers.php'            ); // List of provider supported by WSL (provided by hybridauth library) 
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/settings/wsl.database.php'             ); // Functions & utilities related to WSL database installation and migrations
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/settings/wsl.initialization.php'       ); // Check WSL requirements and register WSL settings
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/settings/wsl.compatibilities.php'      ); // Check and upgrade WSL database/settings (for older WSL versions)

# Services & Utilities
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/services/wsl.authentication.php'       ); // Authenticate users via social networks. <- that's the most important script.
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/services/wsl.mail.notification.php'    ); // Emails and notifications.
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/services/wsl.user.avatar.php'          ); // Displaying the user avatar when available on the comment section
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/services/wsl.user.data.php'            ); // User data functions (database related)
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/services/wsl.utilities.php'            ); // Few utilities and functions 
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/services/wsl.watchdog.php'             ); // Logging agent

# WSL Widget and GUIs generators
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/widgets/wsl.auth.widget.php'           ); // Authentication widget generators (where WSL widget/icons are displayed)
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/widgets/wsl.complete.registration.php' ); // Force users to complete their profile after they register.
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/widgets/wsl.account.linking.php'       ); // Planned for WSL 2.3.
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/widgets/wsl.error.pages.php'           ); // Generate WSL notices end errors pages.
require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/widgets/wsl.loading.screens.php'       ); // Generate WSL loading screens.

# WSL Admin UI
if( is_admin() )
{
	require_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/includes/admin/wsl.admin.ui.php'        ); // The entry point to WSL Admin interfaces 
}

// --------------------------------------------------------------------

/* hooks */

// registers wsl_database_migration_hook() to be run when the WSL is activated.
// this will create/update wslusersprofiles and wsluserscontacts and register/unregister few wp options 
register_activation_hook( __FILE__, 'wsl_database_migration_hook' );

// --------------------------------------------------------------------
