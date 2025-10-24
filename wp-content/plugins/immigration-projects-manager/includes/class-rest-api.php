<?php
/**
 * Immigration Projects REST API
 *
 * @package immigration-projects-manager
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register REST API endpoints for immigration projects
 */
class Immigration_Projects_REST_API {
	/**
	 * Register REST API routes
	 */
	public function register_routes() {
		register_rest_route(
			'immigration/v1',
			'/projects',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_projects' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'country'  => array(
						'type'    => 'string',
						'default' => '',
					),
					'category' => array(
						'type'    => 'string',
						'default' => '',
					),
					'per_page' => array(
						'type'    => 'integer',
						'default' => 12,
					),
					'paged'    => array(
						'type'    => 'integer',
						'default' => 1,
					),
				),
			)
		);

		register_rest_route(
			'immigration/v1',
			'/projects/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_project' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'immigration/v1',
			'/filters',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_filters' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get filtered projects
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public function get_projects( $request ) {
		$country  = $request->get_param( 'country' );
		$category = $request->get_param( 'category' );
		$per_page = intval( $request->get_param( 'per_page' ) );
		$paged    = intval( $request->get_param( 'paged' ) );

		$args = array(
			'post_type'      => 'immigration_project',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		// Add tax query for country
		if ( ! empty( $country ) && $country !== 'all' ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'project_country',
				'field'    => 'slug',
				'terms'    => $country,
			);
		}

		// Add tax query for category
		if ( ! empty( $category ) && $category !== 'all' ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'project_category',
				'field'    => 'slug',
				'terms'    => $category,
			);
		}

		// Set tax_query relation if multiple terms
		if ( ! empty( $args['tax_query'] ) && count( $args['tax_query'] ) > 1 ) {
			$args['tax_query']['relation'] = 'AND';
		}

		$query = new WP_Query( $args );

		$projects = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$projects[] = $this->format_project( get_the_ID() );
			}
			wp_reset_postdata();
		}

		return rest_ensure_response(
			array(
				'data'       => $projects,
				'total'      => $query->found_posts,
				'total_pages' => $query->max_num_pages,
				'current_page' => $paged,
			)
		);
	}

	/**
	 * Get single project
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public function get_project( $request ) {
		$id = intval( $request->get_param( 'id' ) );

		$post = get_post( $id );

		if ( ! $post || 'immigration_project' !== $post->post_type ) {
			return new WP_Error(
				'not_found',
				__( 'Project not found', 'immigration-projects-manager' ),
				array( 'status' => 404 )
			);
		}

		return rest_ensure_response( $this->format_project( $id ) );
	}

	/**
	 * Get available filters (countries and categories)
	 *
	 * @return WP_REST_Response
	 */
	public function get_filters() {
		$countries = get_terms(
			array(
				'taxonomy'   => 'project_country',
				'hide_empty' => false,
			)
		);

		$categories = get_terms(
			array(
				'taxonomy'   => 'project_category',
				'hide_empty' => false,
			)
		);

		$formatted_countries = array();
		if ( is_array( $countries ) ) {
			foreach ( $countries as $country ) {
				$formatted_countries[] = array(
					'id'   => $country->term_id,
					'name' => $country->name,
					'slug' => $country->slug,
				);
			}
		}

		$formatted_categories = array();
		if ( is_array( $categories ) ) {
			foreach ( $categories as $category ) {
				$formatted_categories[] = array(
					'id'   => $category->term_id,
					'name' => $category->name,
					'slug' => $category->slug,
				);
			}
		}

		return rest_ensure_response(
			array(
				'countries'  => $formatted_countries,
				'categories' => $formatted_categories,
			)
		);
	}

	/**
	 * Format project data for REST response
	 *
	 * @param int $post_id The post ID.
	 * @return array
	 */
	private function format_project( $post_id ) {
		$post = get_post( $post_id );

		$banner_image = get_field( 'banner_image', $post_id );
		$banner_image_url = '';
		if ( is_array( $banner_image ) && ! empty( $banner_image['url'] ) ) {
			$banner_image_url = $banner_image['url'];
		}

		// Get categories and countries
		$countries  = get_the_terms( $post_id, 'project_country' );
		$categories = get_the_terms( $post_id, 'project_category' );

		$formatted_countries = array();
		if ( is_array( $countries ) ) {
			foreach ( $countries as $country ) {
				$formatted_countries[] = array(
					'id'   => $country->term_id,
					'name' => $country->name,
					'slug' => $country->slug,
				);
			}
		}

		$formatted_categories = array();
		if ( is_array( $categories ) ) {
			foreach ( $categories as $category ) {
				$formatted_categories[] = array(
					'id'   => $category->term_id,
					'name' => $category->name,
					'slug' => $category->slug,
				);
			}
		}

		return array(
			'id'                      => $post_id,
			'title'                   => get_the_title( $post_id ),
			'excerpt'                 => get_the_excerpt( $post_id ),
			'content'                 => $post->post_content,
			'featured_image'          => get_the_post_thumbnail_url( $post_id, 'full' ),
			'banner_image'            => $banner_image_url,
			'banner_subtitle'         => get_field( 'banner_subtitle', $post_id ),
			'processing_period'       => get_field( 'processing_period', $post_id ),
			'identity_type'           => get_field( 'identity_type', $post_id ),
			'investment_amount'       => get_field( 'investment_amount', $post_id ),
			'residential_requirement' => get_field( 'residential_requirement', $post_id ),
			'language_requirement'    => get_field( 'language_requirement', $post_id ),
			'project_overview'        => get_field( 'project_overview', $post_id ),
			'project_advantages'      => get_field( 'project_advantages', $post_id ),
			'application_requirements' => get_field( 'application_requirements', $post_id ),
			'application_process'     => get_field( 'application_process', $post_id ),
			'about_life'              => get_field( 'about_life', $post_id ),
			'countries'               => $formatted_countries,
			'categories'              => $formatted_categories,
			'permalink'               => get_permalink( $post_id ),
		);
	}
}
