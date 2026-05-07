<?php
/**
 * Plugin Name: Shaw Education Programs
 * Plugin URI: https://shawsedu.com
 * Description: Education programs CPT, ACF fields, and Elementor AJAX filter widget for ShawEdu.
 * Version: 1.0.0
 * Author: ShawsEdu
 * Author URI: https://shawsedu.com
 * Text Domain: shaw-education-programs
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SHAW_EDU_PROGRAMS_VERSION', '1.0.0');
define('SHAW_EDU_PROGRAMS_PATH', plugin_dir_path(__FILE__));
define('SHAW_EDU_PROGRAMS_URL', plugin_dir_url(__FILE__));

class Shaw_Education_Programs
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
        $plugin->register_taxonomies();
        $plugin->seed_default_terms();
        flush_rewrite_rules();
    }

    private function __construct()
    {
        $this->include_files();

        add_action('init', [$this, 'register_post_type']);
        add_action('init', [$this, 'register_taxonomies']);
        add_action('acf/init', [$this, 'register_acf_fields']);
        add_action('plugins_loaded', [$this, 'register_acf_fields'], 20);
        add_action('elementor/widgets/register', [$this, 'register_elementor_widgets']);
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'enqueue_widget_assets']);
    }

    public static function get_text($key)
    {
        $texts = [
            'all' => __('All', 'shaw-education-programs'),
            'all_programs' => __('All Programs', 'shaw-education-programs'),
            'no_programs' => __('No programs found.', 'shaw-education-programs'),
            'no_matching' => __('No matching programs found.', 'shaw-education-programs'),
            'view_program' => __('View Program', 'shaw-education-programs'),
            'results_found_single' => __('%s program found', 'shaw-education-programs'),
            'results_found_plural' => __('%s programs found', 'shaw-education-programs'),
        ];

        return isset($texts[$key]) ? $texts[$key] : $key;
    }

    private function include_files()
    {
        require_once SHAW_EDU_PROGRAMS_PATH . 'includes/class-template-renderer.php';
        require_once SHAW_EDU_PROGRAMS_PATH . 'includes/class-ajax-handler.php';
    }

    public function register_elementor_widgets($widgets_manager)
    {
        require_once SHAW_EDU_PROGRAMS_PATH . 'includes/widgets/education-programs-widget.php';
        $widgets_manager->register(new Shaw_Education_Programs_Widget());
    }

    public function enqueue_widget_assets()
    {
        wp_enqueue_style(
            'shaw-education-programs-widget',
            SHAW_EDU_PROGRAMS_URL . 'assets/css/widget-style.css',
            [],
            SHAW_EDU_PROGRAMS_VERSION
        );

        wp_enqueue_script(
            'shaw-education-programs-widget',
            SHAW_EDU_PROGRAMS_URL . 'assets/js/widget-frontend.js',
            ['jquery'],
            SHAW_EDU_PROGRAMS_VERSION,
            true
        );

        wp_localize_script(
            'shaw-education-programs-widget',
            'shawEducationProgramsWidget',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('shaw_education_programs_nonce'),
            ]
        );
    }

    public function register_post_type()
    {
        $labels = [
            'name' => _x('Education Programs', 'Post Type General Name', 'shaw-education-programs'),
            'singular_name' => _x('Education Program', 'Post Type Singular Name', 'shaw-education-programs'),
            'menu_name' => __('Education Programs', 'shaw-education-programs'),
            'name_admin_bar' => __('Education Program', 'shaw-education-programs'),
            'archives' => __('Program Archives', 'shaw-education-programs'),
            'attributes' => __('Program Attributes', 'shaw-education-programs'),
            'all_items' => __('All Programs', 'shaw-education-programs'),
            'add_new_item' => __('Add New Program', 'shaw-education-programs'),
            'add_new' => __('Add New', 'shaw-education-programs'),
            'new_item' => __('New Program', 'shaw-education-programs'),
            'edit_item' => __('Edit Program', 'shaw-education-programs'),
            'update_item' => __('Update Program', 'shaw-education-programs'),
            'view_item' => __('View Program', 'shaw-education-programs'),
            'view_items' => __('View Programs', 'shaw-education-programs'),
            'search_items' => __('Search Program', 'shaw-education-programs'),
            'not_found' => __('Not found', 'shaw-education-programs'),
            'not_found_in_trash' => __('Not found in Trash', 'shaw-education-programs'),
        ];

        $args = [
            'label' => __('Education Program', 'shaw-education-programs'),
            'description' => __('Courses and programs for ShawEdu', 'shaw-education-programs'),
            'labels' => $labels,
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'],
            'taxonomies' => ['program_type', 'program_focus'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => 'programs',
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rest_base' => 'education-programs',
            'rewrite' => ['slug' => 'program'],
        ];

        register_post_type('education_program', $args);
    }

    public function register_taxonomies()
    {
        $type_labels = [
            'name' => _x('Program Types', 'Taxonomy General Name', 'shaw-education-programs'),
            'singular_name' => _x('Program Type', 'Taxonomy Singular Name', 'shaw-education-programs'),
            'menu_name' => __('Program Types', 'shaw-education-programs'),
            'all_items' => __('All Program Types', 'shaw-education-programs'),
            'parent_item' => __('Parent Program Type', 'shaw-education-programs'),
            'parent_item_colon' => __('Parent Program Type:', 'shaw-education-programs'),
            'new_item_name' => __('New Program Type Name', 'shaw-education-programs'),
            'add_new_item' => __('Add New Program Type', 'shaw-education-programs'),
            'edit_item' => __('Edit Program Type', 'shaw-education-programs'),
            'update_item' => __('Update Program Type', 'shaw-education-programs'),
            'view_item' => __('View Program Type', 'shaw-education-programs'),
            'search_items' => __('Search Program Types', 'shaw-education-programs'),
            'not_found' => __('Not Found', 'shaw-education-programs'),
        ];

        register_taxonomy(
            'program_type',
            ['education_program'],
            [
                'labels' => $type_labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
                'show_in_rest' => true,
                'rest_base' => 'program-types',
                'rewrite' => ['slug' => 'program-type'],
            ]
        );

        $focus_labels = [
            'name' => _x('Program Focus', 'Taxonomy General Name', 'shaw-education-programs'),
            'singular_name' => _x('Program Focus', 'Taxonomy Singular Name', 'shaw-education-programs'),
            'menu_name' => __('Program Focus', 'shaw-education-programs'),
            'all_items' => __('All Program Focus', 'shaw-education-programs'),
            'parent_item' => __('Parent Program Focus', 'shaw-education-programs'),
            'parent_item_colon' => __('Parent Program Focus:', 'shaw-education-programs'),
            'new_item_name' => __('New Program Focus Name', 'shaw-education-programs'),
            'add_new_item' => __('Add New Program Focus', 'shaw-education-programs'),
            'edit_item' => __('Edit Program Focus', 'shaw-education-programs'),
            'update_item' => __('Update Program Focus', 'shaw-education-programs'),
            'view_item' => __('View Program Focus', 'shaw-education-programs'),
            'search_items' => __('Search Program Focus', 'shaw-education-programs'),
            'not_found' => __('Not Found', 'shaw-education-programs'),
        ];

        register_taxonomy(
            'program_focus',
            ['education_program'],
            [
                'labels' => $focus_labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
                'show_in_rest' => true,
                'rest_base' => 'program-focus',
                'rewrite' => ['slug' => 'program-focus'],
            ]
        );
    }

    public function seed_default_terms()
    {
        $default_types = [
            'Open Studies',
            'Certificate Programs',
            'Pathway Programs',
            'Language Programs',
            'Seasonal Programs',
        ];

        foreach ($default_types as $term_name) {
            if (!term_exists($term_name, 'program_type')) {
                wp_insert_term($term_name, 'program_type');
            }
        }

        $default_focus = [
            'English',
            'French',
            'Japanese',
            'IELTS Preparation',
        ];

        foreach ($default_focus as $term_name) {
            if (!term_exists($term_name, 'program_focus')) {
                wp_insert_term($term_name, 'program_focus');
            }
        }
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
            'key' => 'group_shaw_education_program_details',
            'title' => 'Education Program Details',
            'fields' => [
                [
                    'key' => 'field_sep_tab_shared',
                    'label' => 'Shared Program Info',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_sep_brief',
                    'label' => 'Brief',
                    'name' => 'brief',
                    'type' => 'textarea',
                    'rows' => 3,
                    'instructions' => 'Used on the list card and as the short intro on the detail page.',
                ],
                [
                    'key' => 'field_sep_location',
                    'label' => 'Location',
                    'name' => 'location',
                    'type' => 'text',
                    'instructions' => 'Example: Vancouver, Canada',
                ],
                [
                    'key' => 'field_sep_program_length_summary',
                    'label' => 'Program Length Summary',
                    'name' => 'program_length_summary',
                    'type' => 'text',
                    'instructions' => 'Example: 8 Weeks',
                ],
                [
                    'key' => 'field_sep_level',
                    'label' => 'Level',
                    'name' => 'level',
                    'type' => 'text',
                    'instructions' => 'Example: Beginner – Intermediate',
                ],
                [
                    'key' => 'field_sep_class_size',
                    'label' => 'Class Size',
                    'name' => 'class_size',
                    'type' => 'text',
                    'instructions' => 'Example: Small Class',
                ],
                [
                    'key' => 'field_sep_delivery_type',
                    'label' => 'Delivery Type',
                    'name' => 'delivery_type',
                    'type' => 'select',
                    'choices' => [
                        'inperson' => 'In Person',
                        'online' => 'Online',
                        'hybrid' => 'Mix / Hybrid',
                    ],
                    'allow_null' => 1,
                    'ui' => 1,
                ],
                [
                    'key' => 'field_sep_admission_requirements_summary',
                    'label' => 'Admission Requirements Summary',
                    'name' => 'admission_requirements_summary',
                    'type' => 'textarea',
                    'rows' => 3,
                    'instructions' => 'Short version for list card and quick facts.',
                ],
                [
                    'key' => 'field_sep_button_label',
                    'label' => 'Card Button Label',
                    'name' => 'button_label',
                    'type' => 'text',
                    'default_value' => 'View Program',
                ],
                [
                    'key' => 'field_sep_tab_detail',
                    'label' => 'Detail Content',
                    'type' => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key' => 'field_sep_price',
                    'label' => 'Price',
                    'name' => 'price',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_sep_overview_content',
                    'label' => 'Overview',
                    'name' => 'overview_content',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ],
                [
                    'key' => 'field_sep_highlights_content',
                    'label' => 'Highlights',
                    'name' => 'highlights_content',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
                [
                    'key' => 'field_sep_suitable_for_content',
                    'label' => 'Suitable For',
                    'name' => 'suitable_for_content',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
                [
                    'key' => 'field_sep_program_length_detail',
                    'label' => 'Program Length Detail',
                    'name' => 'program_length_detail',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
                [
                    'key' => 'field_sep_admission_requirements_detail',
                    'label' => 'Admission Requirements Detail',
                    'name' => 'admission_requirements_detail',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
                [
                    'key' => 'field_sep_curriculum_content',
                    'label' => 'Curriculum',
                    'name' => 'curriculum_content',
                    'type' => 'wysiwyg',
                    'instructions' => 'Use headings and bullet lists to represent semesters/modules from the design.',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 0,
                ],
                [
                    'key' => 'field_sep_student_support_content',
                    'label' => 'Student Support',
                    'name' => 'student_support_content',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
                [
                    'key' => 'field_sep_enrollment_content',
                    'label' => 'Enrollment',
                    'name' => 'enrollment_content',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'education_program',
                    ],
                ],
            ],
            'position' => 'acf_after_title',
            'style' => 'default',
            'active' => true,
        ]);
    }
}

Shaw_Education_Programs::get_instance();
register_activation_hook(__FILE__, ['Shaw_Education_Programs', 'activate']);
