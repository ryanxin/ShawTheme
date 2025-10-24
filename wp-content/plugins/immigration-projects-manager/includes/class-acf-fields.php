<?php
/**
 * Immigration Projects ACF Fields
 *
 * @package immigration-projects-manager
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register ACF fields for immigration projects
 */
class Immigration_Projects_ACF_Fields {
	/**
	 * Register ACF fields
	 */
	public function register() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$this->register_basic_info_fields();
		$this->register_details_fields();
		$this->register_banner_fields();
	}

	/**
	 * Register basic information fields
	 */
	private function register_basic_info_fields() {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_project_basic_info',
				'title'                 => __( 'Basic Information', 'immigration-projects-manager' ),
				'fields'                => array(
					array(
						'key'           => 'field_processing_period',
						'label'         => __( 'Processing Period', 'immigration-projects-manager' ),
						'name'          => 'processing_period',
						'type'          => 'text',
						'required'      => 0,
						'placeholder'   => __( 'e.g., 20 months', 'immigration-projects-manager' ),
					),
					array(
						'key'           => 'field_identity_type',
						'label'         => __( 'Identity Type', 'immigration-projects-manager' ),
						'name'          => 'identity_type',
						'type'          => 'text',
						'required'      => 0,
						'placeholder'   => __( 'e.g., Permanent Resident', 'immigration-projects-manager' ),
					),
					array(
						'key'           => 'field_investment_amount',
						'label'         => __( 'Investment Amount', 'immigration-projects-manager' ),
						'name'          => 'investment_amount',
						'type'          => 'text',
						'required'      => 0,
						'placeholder'   => __( 'e.g., No mandatory requirements', 'immigration-projects-manager' ),
					),
					array(
						'key'           => 'field_residential_requirement',
						'label'         => __( 'Residential Requirement', 'immigration-projects-manager' ),
						'name'          => 'residential_requirement',
						'type'          => 'text',
						'required'      => 0,
						'placeholder'   => __( 'e.g., Free years, with two years fully completed', 'immigration-projects-manager' ),
					),
					array(
						'key'           => 'field_language_requirement',
						'label'         => __( 'Language Requirement', 'immigration-projects-manager' ),
						'name'          => 'language_requirement',
						'type'          => 'text',
						'required'      => 0,
						'placeholder'   => __( 'e.g., CLBS', 'immigration-projects-manager' ),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'immigration_project',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);
	}

	/**
	 * Register project details fields
	 */
	private function register_details_fields() {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_project_details',
				'title'                 => __( 'Project Details', 'immigration-projects-manager' ),
				'fields'                => array(
					array(
						'key'           => 'field_project_overview',
						'label'         => __( 'Project Overview', 'immigration-projects-manager' ),
						'name'          => 'project_overview',
						'type'          => 'wysiwyg',
						'required'      => 0,
						'tabs'          => 'all',
						'toolbar'       => 'full',
						'media_upload'  => 1,
						'delay'         => 0,
					),
					array(
						'key'           => 'field_project_advantages',
						'label'         => __( 'Project Advantages', 'immigration-projects-manager' ),
						'name'          => 'project_advantages',
						'type'          => 'repeater',
						'required'      => 0,
						'min'           => 0,
						'max'           => 0,
						'layout'        => 'table',
						'button_label'  => __( 'Add Advantage', 'immigration-projects-manager' ),
						'sub_fields'    => array(
							array(
								'key'   => 'field_advantage_icon',
								'label' => __( 'Icon', 'immigration-projects-manager' ),
								'name'  => 'icon',
								'type'  => 'image',
								'required' => 0,
								'return_format' => 'array',
								'preview_size' => 'thumbnail',
								'library' => 'all',
							),
							array(
								'key'   => 'field_advantage_title',
								'label' => __( 'Title', 'immigration-projects-manager' ),
								'name'  => 'title',
								'type'  => 'text',
								'required' => 0,
							),
							array(
								'key'   => 'field_advantage_description',
								'label' => __( 'Description', 'immigration-projects-manager' ),
								'name'  => 'description',
								'type'  => 'textarea',
								'required' => 0,
								'rows'  => 3,
							),
						),
					),
					array(
						'key'           => 'field_application_requirements',
						'label'         => __( 'Application Requirements', 'immigration-projects-manager' ),
						'name'          => 'application_requirements',
						'type'          => 'wysiwyg',
						'required'      => 0,
						'tabs'          => 'all',
						'toolbar'       => 'full',
						'media_upload'  => 1,
						'delay'         => 0,
					),
					array(
						'key'           => 'field_application_process',
						'label'         => __( 'Application Process', 'immigration-projects-manager' ),
						'name'          => 'application_process',
						'type'          => 'repeater',
						'required'      => 0,
						'min'           => 0,
						'max'           => 0,
						'layout'        => 'table',
						'button_label'  => __( 'Add Step', 'immigration-projects-manager' ),
						'sub_fields'    => array(
							array(
								'key'   => 'field_process_step_number',
								'label' => __( 'Step Number', 'immigration-projects-manager' ),
								'name'  => 'step_number',
								'type'  => 'number',
								'required' => 0,
								'min'   => 1,
							),
							array(
								'key'   => 'field_process_content',
								'label' => __( 'Content', 'immigration-projects-manager' ),
								'name'  => 'content',
								'type'  => 'textarea',
								'required' => 0,
								'rows'  => 3,
							),
						),
					),
					array(
						'key'           => 'field_about_life',
						'label'         => __( 'About Life', 'immigration-projects-manager' ),
						'name'          => 'about_life',
						'type'          => 'gallery',
						'required'      => 0,
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'insert'        => 'append',
						'library'       => 'all',
						'min'           => 0,
						'max'           => 0,
						'mime_types'    => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'immigration_project',
						),
					),
				),
				'menu_order'            => 10,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);
	}

	/**
	 * Register banner fields
	 */
	private function register_banner_fields() {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_project_banner',
				'title'                 => __( 'Banner', 'immigration-projects-manager' ),
				'fields'                => array(
					array(
						'key'           => 'field_banner_image',
						'label'         => __( 'Banner Image', 'immigration-projects-manager' ),
						'name'          => 'banner_image',
						'type'          => 'image',
						'required'      => 0,
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
					),
					array(
						'key'           => 'field_banner_subtitle',
						'label'         => __( 'Banner Subtitle', 'immigration-projects-manager' ),
						'name'          => 'banner_subtitle',
						'type'          => 'text',
						'required'      => 0,
						'placeholder'   => __( 'Subtitle for banner', 'immigration-projects-manager' ),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'immigration_project',
						),
					),
				),
				'menu_order'            => 20,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);
	}
}
