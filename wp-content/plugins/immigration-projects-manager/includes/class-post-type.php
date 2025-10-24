<?php
/**
 * Immigration Projects Post Type
 *
 * @package immigration-projects-manager
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom post type for immigration projects
 */
class Immigration_Projects_Post_Type {
	/**
	 * Register the post type
	 */
	public function register() {
		register_post_type(
			'immigration_project',
			array(
				'labels'              => array(
					'name'                     => __( 'Immigration Projects', 'immigration-projects-manager' ),
					'singular_name'            => __( 'Immigration Project', 'immigration-projects-manager' ),
					'menu_name'                => __( 'Immigration Projects', 'immigration-projects-manager' ),
					'all_items'                => __( 'All Projects', 'immigration-projects-manager' ),
					'edit_item'                => __( 'Edit Project', 'immigration-projects-manager' ),
					'view_item'                => __( 'View Project', 'immigration-projects-manager' ),
					'view_items'               => __( 'View Projects', 'immigration-projects-manager' ),
					'add_new_item'             => __( 'Add New Project', 'immigration-projects-manager' ),
					'new_item'                 => __( 'New Project', 'immigration-projects-manager' ),
					'parent_item_colon'        => __( 'Parent Project:', 'immigration-projects-manager' ),
					'search_items'             => __( 'Search Projects', 'immigration-projects-manager' ),
					'not_found'                => __( 'No projects found.', 'immigration-projects-manager' ),
					'not_found_in_trash'       => __( 'No projects found in trash.', 'immigration-projects-manager' ),
					'archives'                 => __( 'Project Archives', 'immigration-projects-manager' ),
					'attributes'               => __( 'Project Attributes', 'immigration-projects-manager' ),
					'insert_into_item'         => __( 'Insert into project', 'immigration-projects-manager' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this project', 'immigration-projects-manager' ),
					'featured_image'           => __( 'Featured Image', 'immigration-projects-manager' ),
					'set_featured_image'       => __( 'Set featured image', 'immigration-projects-manager' ),
					'remove_featured_image'    => __( 'Remove featured image', 'immigration-projects-manager' ),
					'use_featured_image'       => __( 'Use as featured image', 'immigration-projects-manager' ),
					'filter_items_list'        => __( 'Filter projects list', 'immigration-projects-manager' ),
					'items_list_navigation'    => __( 'Projects list navigation', 'immigration-projects-manager' ),
					'items_list'               => __( 'Projects list', 'immigration-projects-manager' ),
					'item_published'           => __( 'Project published.', 'immigration-projects-manager' ),
					'item_published_privately' => __( 'Project published privately.', 'immigration-projects-manager' ),
					'item_reverted_to_draft'   => __( 'Project reverted to draft.', 'immigration-projects-manager' ),
					'item_scheduled'           => __( 'Project scheduled.', 'immigration-projects-manager' ),
					'item_updated'             => __( 'Project updated.', 'immigration-projects-manager' ),
				),
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'query_var'           => true,
				'rewrite'             => array( 'slug' => 'projects' ),
				'capability_type'     => 'post',
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_rest'        => true,
				'rest_base'           => 'immigration-projects',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
				'supports'            => array(
					'title',
					'editor',
					'excerpt',
					'thumbnail',
					'custom-fields',
					'revisions',
					'author',
				),
				'menu_icon'           => 'dashicons-location-alt',
				'taxonomies'          => array(),
			)
		);
	}
}
