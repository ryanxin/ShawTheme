<?php
/**
 * Immigration Projects Taxonomies
 *
 * @package immigration-projects-manager
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register taxonomies for immigration projects
 */
class Immigration_Projects_Taxonomies {
	/**
	 * Register all taxonomies
	 */
	public function register() {
		$this->register_country_taxonomy();
		$this->register_category_taxonomy();
	}

	/**
	 * Register country taxonomy
	 */
	private function register_country_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Countries', 'taxonomy general name', 'immigration-projects-manager' ),
			'singular_name'              => _x( 'Country', 'taxonomy singular name', 'immigration-projects-manager' ),
			'search_items'               => __( 'Search Countries', 'immigration-projects-manager' ),
			'popular_items'              => __( 'Popular Countries', 'immigration-projects-manager' ),
			'all_items'                  => __( 'All Countries', 'immigration-projects-manager' ),
			'edit_item'                  => __( 'Edit Country', 'immigration-projects-manager' ),
			'update_item'                => __( 'Update Country', 'immigration-projects-manager' ),
			'add_new_item'               => __( 'Add New Country', 'immigration-projects-manager' ),
			'new_item_name'              => __( 'New Country Name', 'immigration-projects-manager' ),
			'separate_items_with_commas' => __( 'Separate countries with commas', 'immigration-projects-manager' ),
			'add_or_remove_items'        => __( 'Add or remove countries', 'immigration-projects-manager' ),
			'choose_from_most_used'      => __( 'Choose from the most used countries', 'immigration-projects-manager' ),
			'not_found'                  => __( 'No countries found.', 'immigration-projects-manager' ),
			'no_terms'                   => __( 'No countries', 'immigration-projects-manager' ),
			'items_list_navigation'      => __( 'Countries list navigation', 'immigration-projects-manager' ),
			'items_list'                 => __( 'Countries list', 'immigration-projects-manager' ),
			'most_used'                  => _x( 'Most Used', 'project_country', 'immigration-projects-manager' ),
			'back_to_items'              => __( '← Back to Countries', 'immigration-projects-manager' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'rest_base'         => 'project-countries',
			'rewrite'           => array( 'slug' => 'country' ),
		);

		register_taxonomy( 'project_country', array( 'immigration_project' ), $args );
	}

	/**
	 * Register project category taxonomy
	 */
	private function register_category_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Project Categories', 'taxonomy general name', 'immigration-projects-manager' ),
			'singular_name'              => _x( 'Project Category', 'taxonomy singular name', 'immigration-projects-manager' ),
			'search_items'               => __( 'Search Project Categories', 'immigration-projects-manager' ),
			'popular_items'              => __( 'Popular Project Categories', 'immigration-projects-manager' ),
			'all_items'                  => __( 'All Project Categories', 'immigration-projects-manager' ),
			'edit_item'                  => __( 'Edit Project Category', 'immigration-projects-manager' ),
			'update_item'                => __( 'Update Project Category', 'immigration-projects-manager' ),
			'add_new_item'               => __( 'Add New Project Category', 'immigration-projects-manager' ),
			'new_item_name'              => __( 'New Project Category Name', 'immigration-projects-manager' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'immigration-projects-manager' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'immigration-projects-manager' ),
			'choose_from_most_used'      => __( 'Choose from the most used categories', 'immigration-projects-manager' ),
			'not_found'                  => __( 'No categories found.', 'immigration-projects-manager' ),
			'no_terms'                   => __( 'No categories', 'immigration-projects-manager' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'immigration-projects-manager' ),
			'items_list'                 => __( 'Categories list', 'immigration-projects-manager' ),
			'most_used'                  => _x( 'Most Used', 'project_category', 'immigration-projects-manager' ),
			'back_to_items'              => __( '← Back to Project Categories', 'immigration-projects-manager' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'rest_base'         => 'project-categories',
			'rewrite'           => array( 'slug' => 'project-category' ),
		);

		register_taxonomy( 'project_category', array( 'immigration_project' ), $args );
	}

	/**
	 * Create default terms during plugin activation
	 */
	public static function create_default_terms() {
		// Countries
		$countries = array(
			'Canada'              => __( 'Canada', 'immigration-projects-manager' ),
			'Turkey'              => __( 'Turkey', 'immigration-projects-manager' ),
			'Antigua and Barbuda' => __( 'Antigua and Barbuda', 'immigration-projects-manager' ),
			'Greece'              => __( 'Greece', 'immigration-projects-manager' ),
			'Singapore'           => __( 'Singapore', 'immigration-projects-manager' ),
			'Japan'               => __( 'Japan', 'immigration-projects-manager' ),
			'United States'       => __( 'United States', 'immigration-projects-manager' ),
			'Philippines'         => __( 'Philippines', 'immigration-projects-manager' ),
			'Portugal'            => __( 'Portugal', 'immigration-projects-manager' ),
			'All'                 => __( 'All', 'immigration-projects-manager' ),
		);

		foreach ( $countries as $slug => $name ) {
			if ( ! term_exists( $slug, 'project_country' ) ) {
				wp_insert_term( $name, 'project_country', array( 'slug' => strtolower( str_replace( ' ', '-', $slug ) ) ) );
			}
		}

		// Project Categories
		$categories = array(
			'Entrepreneurship'      => __( 'Entrepreneurship Immigration', 'immigration-projects-manager' ),
			'Technology'            => __( 'Technology Immigration', 'immigration-projects-manager' ),
			'Investment'            => __( 'Investment Immigration', 'immigration-projects-manager' ),
			'Property'              => __( 'Property Immigration', 'immigration-projects-manager' ),
		);

		foreach ( $categories as $slug => $name ) {
			if ( ! term_exists( $slug, 'project_category' ) ) {
				wp_insert_term( $name, 'project_category', array( 'slug' => strtolower( str_replace( ' ', '-', $slug ) ) ) );
			}
		}
	}
}

// Hook to create default terms on plugin activation
register_activation_hook( IMMIGRATION_PROJECTS_MANAGER_PLUGIN_DIR . 'immigration-projects-manager.php', array( 'Immigration_Projects_Taxonomies', 'create_default_terms' ) );
