<?php
/**
 * Immigration Projects Manager
 *
 * @package           immigration-projects-manager
 * @author            Shaw Global
 * @copyright         2025 Shaw Global
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Immigration Projects Manager
 * Plugin URI:        https://shawglobal.com
 * Description:       Manages immigration project custom post types, taxonomies, ACF fields and REST API endpoints
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Shaw Global
 * Author URI:        https://shawglobal.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       immigration-projects-manager
 * Domain Path:       /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants.
define( 'IMMIGRATION_PROJECTS_MANAGER_VERSION', '1.0.0' );
define( 'IMMIGRATION_PROJECTS_MANAGER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IMMIGRATION_PROJECTS_MANAGER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin class
 */
class Immigration_Projects_Manager {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	/**
	 * Load plugin dependencies
	 */
	private function load_dependencies() {
		require_once IMMIGRATION_PROJECTS_MANAGER_PLUGIN_DIR . 'includes/class-post-type.php';
		require_once IMMIGRATION_PROJECTS_MANAGER_PLUGIN_DIR . 'includes/class-taxonomies.php';
		require_once IMMIGRATION_PROJECTS_MANAGER_PLUGIN_DIR . 'includes/class-acf-fields.php';
		require_once IMMIGRATION_PROJECTS_MANAGER_PLUGIN_DIR . 'includes/class-rest-api.php';
	}

	/**
	 * Register hooks
	 */
	private function register_hooks() {
		add_action( 'init', array( $this, 'init_plugin' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Initialize plugin
	 */
	public function init_plugin() {
		// Register custom post type
		$post_type = new Immigration_Projects_Post_Type();
		$post_type->register();

		// Register taxonomies
		$taxonomies = new Immigration_Projects_Taxonomies();
		$taxonomies->register();

		// Initialize ACF fields
		$acf_fields = new Immigration_Projects_ACF_Fields();
		$acf_fields->register();
	}

	/**
	 * Register REST API routes
	 */
	public function register_rest_routes() {
		$rest_api = new Immigration_Projects_REST_API();
		$rest_api->register_routes();
	}
}

/**
 * Initialize the plugin
 */
function immigration_projects_manager_init() {
	new Immigration_Projects_Manager();
}
add_action( 'plugins_loaded', 'immigration_projects_manager_init' );

/**
 * Activation hook
 */
function immigration_projects_manager_activate() {
	// Flush rewrite rules on activation
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'immigration_projects_manager_activate' );

/**
 * Deactivation hook
 */
function immigration_projects_manager_deactivate() {
	// Flush rewrite rules on deactivation
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'immigration_projects_manager_deactivate' );
