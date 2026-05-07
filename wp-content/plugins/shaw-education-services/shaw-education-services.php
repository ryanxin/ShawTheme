<?php
/**
 * Plugin Name: Shaw Education Services
 * Plugin URI: https://shawsedu.com
 * Description: Service pages CPT, structured fields, and Elementor loop widgets for ShawEdu.
 * Version: 1.0.0
 * Author: ShawsEdu
 * Author URI: https://shawsedu.com
 * Text Domain: shaw-education-services
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SHAW_EDU_SERVICES_VERSION', '1.0.0');
define('SHAW_EDU_SERVICES_PATH', plugin_dir_path(__FILE__));
define('SHAW_EDU_SERVICES_URL', plugin_dir_url(__FILE__));

class Shaw_Education_Services
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function activate()
    {
        $plugin = self::get_instance();
        $plugin->register_post_type();

        $supported_post_types = get_option('elementor_cpt_support', ['page', 'post']);
        if (!is_array($supported_post_types)) {
            $supported_post_types = ['page', 'post'];
        }

        if (!in_array('education_service', $supported_post_types, true)) {
            $supported_post_types[] = 'education_service';
            update_option('elementor_cpt_support', array_values(array_unique($supported_post_types)));
        }

        flush_rewrite_rules();
    }

    private function __construct()
    {
        add_action('init', [$this, 'register_post_type']);
        add_action('acf/init', [$this, 'register_acf_fields']);
        add_action('plugins_loaded', [$this, 'register_acf_fields'], 20);
        add_action('cmb2_admin_init', [$this, 'register_cmb2_fields']);
        add_action('elementor/widgets/register', [$this, 'register_elementor_widgets']);
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_widget_assets']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_widget_assets']);
    }

    private function include_widget_files()
    {
        require_once SHAW_EDU_SERVICES_PATH . 'includes/class-service-loop-widget-base.php';
        require_once SHAW_EDU_SERVICES_PATH . 'includes/widgets/service-scope-loop-widget.php';
        require_once SHAW_EDU_SERVICES_PATH . 'includes/widgets/service-audience-loop-widget.php';
        require_once SHAW_EDU_SERVICES_PATH . 'includes/widgets/service-process-loop-widget.php';
        require_once SHAW_EDU_SERVICES_PATH . 'includes/widgets/service-compliance-loop-widget.php';
    }

    public function enqueue_widget_assets()
    {
        wp_enqueue_style(
            'shaw-education-services-widget',
            SHAW_EDU_SERVICES_URL . 'assets/css/widget-style.css',
            [],
            SHAW_EDU_SERVICES_VERSION
        );
    }

    public function register_elementor_widgets($widgets_manager)
    {
        if (!class_exists('\Elementor\Widget_Base')) {
            return;
        }

        $this->include_widget_files();

        $widgets_manager->register(new Shaw_Service_Scope_Loop_Widget());
        $widgets_manager->register(new Shaw_Service_Audience_Loop_Widget());
        $widgets_manager->register(new Shaw_Service_Process_Loop_Widget());
        $widgets_manager->register(new Shaw_Service_Compliance_Loop_Widget());
    }

    public function register_post_type()
    {
        $labels = [
            'name' => _x('Services', 'Post Type General Name', 'shaw-education-services'),
            'singular_name' => _x('Service', 'Post Type Singular Name', 'shaw-education-services'),
            'menu_name' => __('Services', 'shaw-education-services'),
            'name_admin_bar' => __('Service', 'shaw-education-services'),
            'archives' => __('Service Archives', 'shaw-education-services'),
            'attributes' => __('Service Attributes', 'shaw-education-services'),
            'all_items' => __('All Services', 'shaw-education-services'),
            'add_new_item' => __('Add New Service', 'shaw-education-services'),
            'add_new' => __('Add New', 'shaw-education-services'),
            'new_item' => __('New Service', 'shaw-education-services'),
            'edit_item' => __('Edit Service', 'shaw-education-services'),
            'update_item' => __('Update Service', 'shaw-education-services'),
            'view_item' => __('View Service', 'shaw-education-services'),
            'view_items' => __('View Services', 'shaw-education-services'),
            'search_items' => __('Search Service', 'shaw-education-services'),
            'not_found' => __('Not found', 'shaw-education-services'),
            'not_found_in_trash' => __('Not found in Trash', 'shaw-education-services'),
        ];

        $args = [
            'label' => __('Service', 'shaw-education-services'),
            'description' => __('Structured service introduction pages for ShawEdu', 'shaw-education-services'),
            'labels' => $labels,
            'supports' => ['title', 'thumbnail', 'revisions', 'custom-fields'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 7,
            'menu_icon' => 'dashicons-portfolio',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => 'services',
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rest_base' => 'education-services',
            'rewrite' => ['slug' => 'service'],
        ];

        register_post_type('education_service', $args);
    }

    public function register_acf_fields()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        static $registered = false;
        if ($registered) {
            return;
        }
        $registered = true;

        acf_add_local_field_group([
            'key' => 'group_shaw_education_service_details',
            'title' => 'Service Details',
            'fields' => [
                [
                    'key' => 'field_shaw_service_title',
                    'label' => 'Service Title',
                    'name' => 'service_title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_shaw_service_title_highlight',
                    'label' => 'Service Title Highlight',
                    'name' => 'service_title_highlight',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_shaw_service_subtitle',
                    'label' => 'Service Subtitle',
                    'name' => 'service_subtitle',
                    'type' => 'textarea',
                    'rows' => 2,
                ],
                [
                    'key' => 'field_shaw_service_overview_body',
                    'label' => 'Overview Body',
                    'name' => 'overview_body',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 0,
                ],
                [
                    'key' => 'field_shaw_service_show_scope_section',
                    'label' => 'Show Scope Section',
                    'name' => 'show_scope_section',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ],
                [
                    'key' => 'field_shaw_service_show_audience_section',
                    'label' => 'Show Audience Section',
                    'name' => 'show_audience_section',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ],
                [
                    'key' => 'field_shaw_service_show_process_section',
                    'label' => 'Show Process Section',
                    'name' => 'show_process_section',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ],
                [
                    'key' => 'field_shaw_service_show_compliance_section',
                    'label' => 'Show Compliance Section',
                    'name' => 'show_compliance_section',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'education_service',
                    ],
                ],
            ],
            'position' => 'acf_after_title',
            'style' => 'default',
            'active' => true,
        ]);
    }

    public function register_cmb2_fields()
    {
        if (!function_exists('new_cmb2_box')) {
            return;
        }

        $box = new_cmb2_box([
            'id' => 'shaw_service_loop_content',
            'title' => __('Service Loop Content', 'shaw-education-services'),
            'object_types' => ['education_service'],
            'context' => 'normal',
            'priority' => 'high',
            'show_names' => true,
        ]);

        $scope_group_id = $box->add_field([
            'id' => 'scope_items',
            'type' => 'group',
            'description' => __('Loop items for the service content / scope section.', 'shaw-education-services'),
            'options' => [
                'group_title' => __('Scope Item {#}', 'shaw-education-services'),
                'add_button' => __('Add Scope Item', 'shaw-education-services'),
                'remove_button' => __('Remove Scope Item', 'shaw-education-services'),
                'sortable' => true,
                'closed' => true,
            ],
        ]);

        $box->add_group_field($scope_group_id, [
            'name' => __('Title', 'shaw-education-services'),
            'id' => 'title',
            'type' => 'text',
        ]);
        $box->add_group_field($scope_group_id, [
            'name' => __('Description', 'shaw-education-services'),
            'id' => 'description',
            'type' => 'textarea_small',
        ]);
        $box->add_group_field($scope_group_id, [
            'name' => __('Image', 'shaw-education-services'),
            'id' => 'image',
            'type' => 'file',
            'options' => ['url' => false],
            'text' => ['add_upload_file_text' => __('Add Image', 'shaw-education-services')],
            'query_args' => ['type' => 'image'],
        ]);

        $audience_group_id = $box->add_field([
            'id' => 'audience_items',
            'type' => 'group',
            'description' => __('Loop items for the suitable-for audience section.', 'shaw-education-services'),
            'options' => [
                'group_title' => __('Audience Item {#}', 'shaw-education-services'),
                'add_button' => __('Add Audience Item', 'shaw-education-services'),
                'remove_button' => __('Remove Audience Item', 'shaw-education-services'),
                'sortable' => true,
                'closed' => true,
            ],
        ]);

        $box->add_group_field($audience_group_id, [
            'name' => __('Image', 'shaw-education-services'),
            'id' => 'image',
            'type' => 'file',
            'options' => ['url' => false],
            'text' => ['add_upload_file_text' => __('Add Image', 'shaw-education-services')],
            'query_args' => ['type' => 'image'],
        ]);
        $box->add_group_field($audience_group_id, [
            'name' => __('Text', 'shaw-education-services'),
            'id' => 'text',
            'type' => 'textarea_small',
        ]);

        $process_group_id = $box->add_field([
            'id' => 'process_steps',
            'type' => 'group',
            'description' => __('Loop items for the service process section.', 'shaw-education-services'),
            'options' => [
                'group_title' => __('Process Step {#}', 'shaw-education-services'),
                'add_button' => __('Add Process Step', 'shaw-education-services'),
                'remove_button' => __('Remove Process Step', 'shaw-education-services'),
                'sortable' => true,
                'closed' => true,
            ],
        ]);

        $box->add_group_field($process_group_id, [
            'name' => __('Step Number', 'shaw-education-services'),
            'id' => 'step_number',
            'type' => 'text_small',
            'attributes' => ['placeholder' => '01'],
        ]);
        $box->add_group_field($process_group_id, [
            'name' => __('Title', 'shaw-education-services'),
            'id' => 'title',
            'type' => 'text',
        ]);
        $box->add_group_field($process_group_id, [
            'name' => __('Description', 'shaw-education-services'),
            'id' => 'description',
            'type' => 'textarea_small',
        ]);

        $compliance_group_id = $box->add_field([
            'id' => 'compliance_items',
            'type' => 'group',
            'description' => __('Loop items for the compliance / standards section.', 'shaw-education-services'),
            'options' => [
                'group_title' => __('Compliance Item {#}', 'shaw-education-services'),
                'add_button' => __('Add Compliance Item', 'shaw-education-services'),
                'remove_button' => __('Remove Compliance Item', 'shaw-education-services'),
                'sortable' => true,
                'closed' => true,
            ],
        ]);

        $box->add_group_field($compliance_group_id, [
            'name' => __('Text', 'shaw-education-services'),
            'id' => 'text',
            'type' => 'textarea_small',
        ]);
    }
}

Shaw_Education_Services::get_instance();
register_activation_hook(__FILE__, ['Shaw_Education_Services', 'activate']);
