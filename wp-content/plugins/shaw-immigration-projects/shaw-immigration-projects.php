<?php
/**
 * Plugin Name: Shaw Immigration Projects
 * Plugin URI: https://shawglobal.com
 * Description: Custom Post Type for Immigration Projects with Country and Category taxonomies, AJAX filtering support
 * Version: 1.0.0
 * Author: Shaw Global
 * Author URI: https://shawglobal.com
 * Text Domain: shaw-immigration-projects
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('SHAW_IMMIGRATION_VERSION', '1.0.0');
define('SHAW_IMMIGRATION_PATH', plugin_dir_path(__FILE__));
define('SHAW_IMMIGRATION_URL', plugin_dir_url(__FILE__));

class Shaw_Immigration_Projects {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomies'));

        // Register ACF fields (basic fields only)
        add_action('acf/init', array($this, 'register_acf_fields'), 10);
        add_action('plugins_loaded', array($this, 'register_acf_fields'), 15);
        add_action('after_setup_theme', array($this, 'register_acf_fields'), 10);

        // Register Meta Box (gallery) fields + normalize legacy data
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_box_fields'));
        add_filter('rwmb_project_gallery_meta', array($this, 'normalize_project_gallery_meta'), 10, 2);
        add_filter('rwmb_project_gallery_sanitize', array($this, 'sanitize_project_gallery_meta'), 10, 2);

        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Disabled: Use Elementor Theme Builder instead of PHP template
        // add_filter('single_template', array($this, 'load_custom_template'));

        // Debug: Track template loading
        add_action('template_redirect', array($this, 'debug_template_loading'), 1);
        add_filter('template_include', array($this, 'log_template_include'), 9999);

        // Ensure Elementor recognizes this CPT for Theme Builder
        add_filter('elementor/theme/need_override_location', array($this, 'force_elementor_override'), 10, 2);

        // Add admin menu for debug info
        add_action('admin_menu', array($this, 'add_debug_menu'));

        // Register Elementor Widget
        add_action('elementor/widgets/register', array($this, 'register_elementor_widgets'));
        add_action('elementor/frontend/after_enqueue_scripts', array($this, 'enqueue_widget_scripts'));

        // Include required files
        $this->include_files();
    }

    /**
     * Include required files
     */
    private function include_files() {
        // Include template renderer
        require_once SHAW_IMMIGRATION_PATH . 'includes/class-template-renderer.php';

        // Include AJAX handler
        require_once SHAW_IMMIGRATION_PATH . 'includes/class-ajax-handler.php';
    }

    /**
     * Register Elementor Widgets
     */
    public function register_elementor_widgets($widgets_manager) {
        // Include widget file
        require_once SHAW_IMMIGRATION_PATH . 'includes/widgets/immigration-projects-widget.php';

        // Register widget
        $widgets_manager->register(new Shaw_Immigration_Projects_Widget());
    }

    /**
     * Enqueue Widget Scripts and Styles
     */
    public function enqueue_widget_scripts() {
        // Widget styles
        wp_enqueue_style(
            'shaw-immigration-widget',
            SHAW_IMMIGRATION_URL . 'assets/css/widget-style.css',
            array(),
            SHAW_IMMIGRATION_VERSION
        );

        // Widget script
        wp_enqueue_script(
            'shaw-immigration-widget',
            SHAW_IMMIGRATION_URL . 'assets/js/widget-frontend.js',
            array('jquery'),
            SHAW_IMMIGRATION_VERSION,
            true
        );

        // Localize script with AJAX URL and nonce
        wp_localize_script('shaw-immigration-widget', 'shawImmigrationWidget', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('shaw_immigration_nonce'),
        ));
    }
    
    /**
     * Register Custom Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Immigration Projects', 'Post Type General Name', 'shaw-immigration-projects'),
            'singular_name'         => _x('Immigration Project', 'Post Type Singular Name', 'shaw-immigration-projects'),
            'menu_name'             => __('Immigration Projects', 'shaw-immigration-projects'),
            'name_admin_bar'        => __('Immigration Project', 'shaw-immigration-projects'),
            'archives'              => __('Project Archives', 'shaw-immigration-projects'),
            'attributes'            => __('Project Attributes', 'shaw-immigration-projects'),
            'parent_item_colon'     => __('Parent Project:', 'shaw-immigration-projects'),
            'all_items'             => __('All Projects', 'shaw-immigration-projects'),
            'add_new_item'          => __('Add New Project', 'shaw-immigration-projects'),
            'add_new'               => __('Add New', 'shaw-immigration-projects'),
            'new_item'              => __('New Project', 'shaw-immigration-projects'),
            'edit_item'             => __('Edit Project', 'shaw-immigration-projects'),
            'update_item'           => __('Update Project', 'shaw-immigration-projects'),
            'view_item'             => __('View Project', 'shaw-immigration-projects'),
            'view_items'            => __('View Projects', 'shaw-immigration-projects'),
            'search_items'          => __('Search Project', 'shaw-immigration-projects'),
            'not_found'             => __('Not found', 'shaw-immigration-projects'),
            'not_found_in_trash'    => __('Not found in Trash', 'shaw-immigration-projects'),
        );
        
        $args = array(
            'label'                 => __('Immigration Project', 'shaw-immigration-projects'),
            'description'           => __('Immigration Projects and Programs', 'shaw-immigration-projects'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'),
            'taxonomies'            => array('project_country', 'project_category'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-admin-site-alt3',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'immigration-projects',  // Custom archive slug
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'immigration-projects',
            'rewrite'               => array('slug' => 'immigration-project'),  // Single post slug
        );
        
        register_post_type('immigration_project', $args);
    }
    
    /**
     * Register Taxonomies
     */
    public function register_taxonomies() {
        // Register Country Taxonomy
        $country_labels = array(
            'name'                       => _x('Countries', 'Taxonomy General Name', 'shaw-immigration-projects'),
            'singular_name'              => _x('Country', 'Taxonomy Singular Name', 'shaw-immigration-projects'),
            'menu_name'                  => __('Countries', 'shaw-immigration-projects'),
            'all_items'                  => __('All Countries', 'shaw-immigration-projects'),
            'parent_item'                => __('Parent Country', 'shaw-immigration-projects'),
            'parent_item_colon'          => __('Parent Country:', 'shaw-immigration-projects'),
            'new_item_name'              => __('New Country Name', 'shaw-immigration-projects'),
            'add_new_item'               => __('Add New Country', 'shaw-immigration-projects'),
            'edit_item'                  => __('Edit Country', 'shaw-immigration-projects'),
            'update_item'                => __('Update Country', 'shaw-immigration-projects'),
            'view_item'                  => __('View Country', 'shaw-immigration-projects'),
            'separate_items_with_commas' => __('Separate countries with commas', 'shaw-immigration-projects'),
            'add_or_remove_items'        => __('Add or remove countries', 'shaw-immigration-projects'),
            'choose_from_most_used'      => __('Choose from the most used', 'shaw-immigration-projects'),
            'popular_items'              => __('Popular Countries', 'shaw-immigration-projects'),
            'search_items'               => __('Search Countries', 'shaw-immigration-projects'),
            'not_found'                  => __('Not Found', 'shaw-immigration-projects'),
        );
        
        $country_args = array(
            'labels'                     => $country_labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'project-countries',
            'rewrite'                    => array('slug' => 'project-country'),
        );
        
        register_taxonomy('project_country', array('immigration_project'), $country_args);
        
        // Register Category Taxonomy
        $category_labels = array(
            'name'                       => _x('Project Categories', 'Taxonomy General Name', 'shaw-immigration-projects'),
            'singular_name'              => _x('Project Category', 'Taxonomy Singular Name', 'shaw-immigration-projects'),
            'menu_name'                  => __('Categories', 'shaw-immigration-projects'),
            'all_items'                  => __('All Categories', 'shaw-immigration-projects'),
            'parent_item'                => __('Parent Category', 'shaw-immigration-projects'),
            'parent_item_colon'          => __('Parent Category:', 'shaw-immigration-projects'),
            'new_item_name'              => __('New Category Name', 'shaw-immigration-projects'),
            'add_new_item'               => __('Add New Category', 'shaw-immigration-projects'),
            'edit_item'                  => __('Edit Category', 'shaw-immigration-projects'),
            'update_item'                => __('Update Category', 'shaw-immigration-projects'),
            'view_item'                  => __('View Category', 'shaw-immigration-projects'),
            'separate_items_with_commas' => __('Separate categories with commas', 'shaw-immigration-projects'),
            'add_or_remove_items'        => __('Add or remove categories', 'shaw-immigration-projects'),
            'choose_from_most_used'      => __('Choose from the most used', 'shaw-immigration-projects'),
            'popular_items'              => __('Popular Categories', 'shaw-immigration-projects'),
            'search_items'               => __('Search Categories', 'shaw-immigration-projects'),
            'not_found'                  => __('Not Found', 'shaw-immigration-projects'),
        );
        
        $category_args = array(
            'labels'                     => $category_labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'project-categories',
            'rewrite'                    => array('slug' => 'project-category'),
        );
        
        register_taxonomy('project_category', array('immigration_project'), $category_args);
    }
    
    /**
     * Register ACF Fields (programmatically)
     */
    public function register_acf_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        // Check if already registered to prevent duplicates
        static $registered = false;
        if ($registered) {
            return;
        }
        $registered = true;
        
        acf_add_local_field_group(array(
            'key' => 'group_immigration_project_details',
            'title' => 'Project Details',
            'fields' => array(
                // Overview Section
                array(
                    'key' => 'field_processing_period',
                    'label' => 'Processing Period',
                    'name' => 'processing_period',
                    'type' => 'text',
                    'instructions' => 'e.g., 20 months',
                    'default_value' => '',
                ),
                array(
                    'key' => 'field_identity_type',
                    'label' => 'Identity Type',
                    'name' => 'identity_type',
                    'type' => 'text',
                    'instructions' => 'e.g., Permanent Resident',
                    'default_value' => '',
                ),
                array(
                    'key' => 'field_investment_amount',
                    'label' => 'Investment Amount',
                    'name' => 'investment_amount',
                    'type' => 'text',
                    'instructions' => 'e.g., No mandatory requirements',
                    'default_value' => '',
                ),
                array(
                    'key' => 'field_residential_requirements',
                    'label' => 'Residential Requirements',
                    'name' => 'residential_requirements',
                    'type' => 'text',
                    'instructions' => 'e.g., Five years, with two years fully completed',
                    'default_value' => '',
                ),
                array(
                    'key' => 'field_language',
                    'label' => 'Language Requirements',
                    'name' => 'language',
                    'type' => 'text',
                    'instructions' => 'e.g., CLB5',
                    'default_value' => '',
                ),
                // Featured Image for Card
                array(
                    'key' => 'field_card_image',
                    'label' => 'Card Featured Image',
                    'name' => 'card_image',
                    'type' => 'image',
                    'instructions' => 'Image displayed in project card on listing page',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
                // Short Description for Card
                array(
                    'key' => 'field_short_description',
                    'label' => 'Short Description',
                    'name' => 'short_description',
                    'type' => 'textarea',
                    'instructions' => 'Brief description shown on project card',
                    'rows' => 3,
                ),
                // Project Overview
                array(
                    'key' => 'field_project_overview',
                    'label' => 'Project Overview',
                    'name' => 'project_overview',
                    'type' => 'wysiwyg',
                    'instructions' => 'Detailed project overview content',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ),
                // NOTE: Gallery now handled by Meta Box (ACF free doesn't support gallery)
                // Project Advantages
                array(
                    'key' => 'field_advantages',
                    'label' => 'Project Advantages',
                    'name' => 'advantages',
                    'type' => 'wysiwyg',
                    'instructions' => 'List all project advantages',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 0,
                ),
                // Application Requirements
                array(
                    'key' => 'field_application_requirements',
                    'label' => 'Application Requirements',
                    'name' => 'application_requirements',
                    'type' => 'wysiwyg',
                    'instructions' => 'List all application requirements',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                ),
                // Application Process
                array(
                    'key' => 'field_application_process',
                    'label' => 'Application Process',
                    'name' => 'application_process',
                    'type' => 'wysiwyg',
                    'instructions' => 'Describe the application process steps',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                ),
                // About Life Section
                array(
                    'key' => 'field_about_life',
                    'label' => 'About Life',
                    'name' => 'about_life',
                    'type' => 'wysiwyg',
                    'instructions' => 'Information about life in the destination',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                ),
                // NOTE: Life Media repeater removed for now (ACF free limitation)
                // Featured/Highlight flag
                array(
                    'key' => 'field_is_featured',
                    'label' => 'Featured Project',
                    'name' => 'is_featured',
                    'type' => 'true_false',
                    'instructions' => 'Mark this project as featured',
                    'default_value' => 0,
                    'ui' => 1,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'immigration_project',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ));
    }
    
    /**
     * Register REST API Routes
     */
    public function register_rest_routes() {
        register_rest_route('shaw-immigration/v1', '/projects', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_filtered_projects'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('shaw-immigration/v1', '/filters', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_filter_options'),
            'permission_callback' => '__return_true',
        ));
    }
    
    /**
     * Get filtered projects via REST API
     */
    public function get_filtered_projects($request) {
        $country = $request->get_param('country');
        $category = $request->get_param('category');
        $posts_per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 9;
        $paged = $request->get_param('page') ? intval($request->get_param('page')) : 1;
        
        $args = array(
            'post_type' => 'immigration_project',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'post_status' => 'publish',
        );
        
        $tax_query = array('relation' => 'AND');
        
        if ($country && $country !== 'all') {
            $tax_query[] = array(
                'taxonomy' => 'project_country',
                'field' => 'slug',
                'terms' => $country,
            );
        }
        
        if ($category && $category !== 'all') {
            $tax_query[] = array(
                'taxonomy' => 'project_category',
                'field' => 'slug',
                'terms' => $category,
            );
        }
        
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
        
        $query = new WP_Query($args);
        
        $projects = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                
                $projects[] = array(
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'excerpt' => get_the_excerpt(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url($post_id, 'large'),
                    'card_image' => get_field('card_image', $post_id),
                    'short_description' => get_field('short_description', $post_id),
                    'processing_period' => get_field('processing_period', $post_id),
                    'identity_type' => get_field('identity_type', $post_id),
                    'countries' => wp_get_post_terms($post_id, 'project_country', array('fields' => 'names')),
                    'categories' => wp_get_post_terms($post_id, 'project_category', array('fields' => 'names')),
                );
            }
        }
        
        wp_reset_postdata();
        
        return new WP_REST_Response(array(
            'projects' => $projects,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'current_page' => $paged,
        ), 200);
    }
    
    /**
     * Get filter options (countries and categories)
     */
    public function get_filter_options($request) {
        $countries = get_terms(array(
            'taxonomy' => 'project_country',
            'hide_empty' => true,
        ));
        
        $categories = get_terms(array(
            'taxonomy' => 'project_category',
            'hide_empty' => true,
        ));
        
        $country_options = array();
        foreach ($countries as $country) {
            $country_options[] = array(
                'slug' => $country->slug,
                'name' => $country->name,
                'count' => $country->count,
            );
        }
        
        $category_options = array();
        foreach ($categories as $category) {
            $category_options[] = array(
                'slug' => $category->slug,
                'name' => $category->name,
                'count' => $category->count,
            );
        }
        
        return new WP_REST_Response(array(
            'countries' => $country_options,
            'categories' => $category_options,
        ), 200);
    }
    
    /**
     * Enqueue Scripts and Styles
     */
    public function enqueue_scripts() {
        if (is_post_type_archive('immigration_project') || is_singular('immigration_project') || is_tax(array('project_country', 'project_category'))) {
            // Main listing styles
            wp_enqueue_style(
                'shaw-immigration-projects',
                SHAW_IMMIGRATION_URL . 'assets/css/style.css',
                array(),
                SHAW_IMMIGRATION_VERSION
            );
            
            // Single project styles
            if (is_singular('immigration_project')) {
                wp_enqueue_style(
                    'shaw-immigration-single',
                    SHAW_IMMIGRATION_URL . 'assets/css/single-project.css',
                    array('shaw-immigration-projects'),
                    SHAW_IMMIGRATION_VERSION
                );
            }
            
            wp_enqueue_script(
                'shaw-immigration-projects',
                SHAW_IMMIGRATION_URL . 'assets/js/ajax-filter.js',
                array('jquery'),
                SHAW_IMMIGRATION_VERSION,
                true
            );
            
            wp_localize_script('shaw-immigration-projects', 'shawImmigration', array(
                'ajaxUrl' => rest_url('shaw-immigration/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
            ));
        }
    }
    
    /**
     * Register Meta Box gallery so MB Elementor Integrator can read it directly.
     */
    public function register_meta_box_fields($meta_boxes) {
        $meta_boxes[] = array(
            'id'         => 'project_gallery_metabox',
            'title'      => __('Project Gallery', 'shaw-immigration-projects'),
            'post_types' => array('immigration_project'),
            'context'    => 'normal',
            'priority'   => 'high',
            'autosave'   => true,
            'fields'     => array(
                array(
                    'id'               => 'project_gallery',
                    'name'             => __('Gallery Images', 'shaw-immigration-projects'),
                    'type'             => 'image_advanced',
                    'max_file_uploads' => 25,
                    'image_size'       => 'thumbnail',
                    'clone'            => false,
                    'desc'             => __('Upload or select multiple images for the Elementor carousel.', 'shaw-immigration-projects'),
                ),
            ),
        );
        
        return $meta_boxes;
    }
    
    /**
     * Ensure legacy CMB2 gallery data is converted to attachment IDs for Meta Box UI.
     */
    public function normalize_project_gallery_meta($meta, $field = array()) {
        return $this->prepare_project_gallery_ids($meta);
    }
    
    /**
     * Make sure anything saved back to the DB is a clean array of attachment IDs.
     */
    public function sanitize_project_gallery_meta($meta, $field = array()) {
        return $this->prepare_project_gallery_ids($meta);
    }
    
    /**
     * Convert mixed gallery meta into attachment ID arrays.
     */
    private function prepare_project_gallery_ids($meta) {
        if (empty($meta)) {
            return array();
        }
        
        $ids = array();
        
        if (is_array($meta)) {
            foreach ($meta as $key => $value) {
                if (is_numeric($value)) {
                    $ids[] = (int) $value;
                    continue;
                }
                
                if (is_array($value)) {
                    $maybe_id = $this->extract_attachment_id_from_array($value);
                    if ($maybe_id) {
                        $ids[] = $maybe_id;
                        continue;
                    }
                }
                
                if (is_string($value) && is_numeric($key)) {
                    // Legacy CMB2 format: attachment ID is the array key, value is a URL string.
                    $ids[] = (int) $key;
                }
            }
        } elseif (is_numeric($meta)) {
            $ids[] = (int) $meta;
        }
        
        $ids = array_values(array_unique(array_filter($ids)));
        
        return $ids;
    }
    
    /**
     * Try to pull an attachment ID from a mixed data array.
     */
    private function extract_attachment_id_from_array($value) {
        $candidates = array('attachment_id', 'id', 'ID');
        foreach ($candidates as $candidate) {
            if (isset($value[$candidate]) && is_numeric($value[$candidate])) {
                return (int) $value[$candidate];
            }
        }
        return 0;
    }
    
    /**
     * Load custom single template
     */
    public function load_custom_template($template) {
        if (is_singular('immigration_project')) {
            $custom_template = SHAW_IMMIGRATION_PATH . 'templates/single-immigration-project.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }
    
    /**
     * Debug: Track what template is being loaded
     */
    public function debug_template_loading() {
        if (!is_singular('immigration_project')) {
            return;
        }
        
        // 创建调试日志
        $debug_info = array(
            'timestamp' => current_time('mysql'),
            'post_type' => get_post_type(),
            'post_id' => get_the_ID(),
            'is_singular' => is_singular('immigration_project'),
            'elementor_location' => $this->get_elementor_location_templates(),
            'active_plugins' => array(
                'elementor' => defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : 'Not Active',
                'elementor_pro' => defined('ELEMENTOR_PRO_VERSION') ? ELEMENTOR_PRO_VERSION : 'Not Active',
            ),
        );
        
        // 保存到临时选项（方便在后台查看）
        update_option('shaw_immigration_debug_last', $debug_info);
        
        // 同时输出到 PHP 错误日志
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log('Shaw Immigration Projects - Template Debug: ' . print_r($debug_info, true));
        }
    }
    
    /**
     * Log which template file is actually being loaded
     */
    public function log_template_include($template) {
        if (!is_singular('immigration_project')) {
            return $template;
        }
        
        $template_info = array(
            'template_file' => $template,
            'template_type' => $this->identify_template_type($template),
        );
        
        // 更新调试信息
        $debug_info = get_option('shaw_immigration_debug_last', array());
        $debug_info['template_info'] = $template_info;
        update_option('shaw_immigration_debug_last', $debug_info);
        
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log('Shaw Immigration Projects - Template Include: ' . print_r($template_info, true));
        }
        
        return $template;
    }
    
    /**
     * Get Elementor location templates for current post
     */
    private function get_elementor_location_templates() {
        if (!class_exists('\ElementorPro\Modules\ThemeBuilder\Module')) {
            return 'Elementor Pro not active';
        }
        
        try {
            $location_manager = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager();
            
            // 尝试获取 single location（新版可能返回数组）
            $location = $location_manager->get_location('single');
            
            $template_id = null;
            
            // 检查 $location 的类型
            if (is_object($location) && method_exists($location, 'get_template_id')) {
                $template_id = $location->get_template_id();
            } elseif (is_array($location) && isset($location['template_id'])) {
                $template_id = $location['template_id'];
            }
            
            // 如果没有找到，尝试直接通过条件查找
            if (!$template_id) {
                $template_id = $this->find_matching_elementor_template();
            }
            
            return array(
                'template_id' => $template_id,
                'template_title' => $template_id ? get_the_title($template_id) : 'None',
                'conditions' => $template_id ? get_post_meta($template_id, '_elementor_conditions', true) : array(),
                'location_type' => is_object($location) ? 'object' : (is_array($location) ? 'array' : gettype($location)),
            );
        } catch (\Exception $e) {
            return array(
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            );
        }
    }
    
    /**
     * Find matching Elementor template by checking conditions
     */
    private function find_matching_elementor_template() {
        if (!is_singular('immigration_project')) {
            return null;
        }
        
        // 查询所有 Elementor 模板
        $templates_query = new \WP_Query(array(
            'post_type' => 'elementor_library',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_elementor_template_type',
                    'value' => array('single', 'single-post', 'single-page'), // 支持新旧版本
                    'compare' => 'IN',
                ),
            ),
        ));
        
        $matched_template = null;
        
        if ($templates_query->have_posts()) {
            while ($templates_query->have_posts()) {
                $templates_query->the_post();
                $template_id = get_the_ID();
                $conditions = get_post_meta($template_id, '_elementor_conditions', true);
                
                if (empty($conditions)) {
                    continue;
                }
                
                // 检查条件是否匹配 immigration_project
                foreach ($conditions as $condition) {
                    if (is_string($condition) && strpos($condition, 'immigration_project') !== false) {
                        $matched_template = $template_id;
                        break 2;
                    } elseif (is_array($condition)) {
                        $condition_string = implode('/', $condition);
                        if (strpos($condition_string, 'immigration_project') !== false) {
                            $matched_template = $template_id;
                            break 2;
                        }
                    }
                }
            }
            wp_reset_postdata();
        }
        
        return $matched_template;
    }
    
    /**
     * Identify what type of template is being used
     */
    private function identify_template_type($template_path) {
        if (strpos($template_path, 'elementor') !== false) {
            return 'Elementor Theme Builder';
        } elseif (strpos($template_path, 'shaw-immigration-projects') !== false) {
            return 'Plugin Custom Template';
        } elseif (strpos($template_path, 'single-immigration_project.php') !== false) {
            return 'Theme Single CPT Template';
        } elseif (strpos($template_path, 'single.php') !== false) {
            return 'Theme Single Template';
        } else {
            return 'Unknown: ' . basename($template_path);
        }
    }
    
    /**
     * Force Elementor to check conditions for immigration_project
     */
    public function force_elementor_override($need_override, $location) {
        if (is_singular('immigration_project') && $location === 'single') {
            return true;
        }
        return $need_override;
    }
    
    /**
     * Add debug menu to admin
     */
    public function add_debug_menu() {
        add_submenu_page(
            'edit.php?post_type=immigration_project',
            'Template Debug Info',
            'Debug Info',
            'manage_options',
            'shaw-immigration-debug',
            array($this, 'render_debug_page')
        );
    }
    
    /**
     * Render debug page
     */
    public function render_debug_page() {
        $debug_info = get_option('shaw_immigration_debug_last', array());
        
        // 获取所有 Elementor 模板（支持新旧版本）
        $elementor_templates = array();
        if (class_exists('\ElementorPro\Modules\ThemeBuilder\Module') || class_exists('\ElementorPro\Modules\ThemeBuilder\Classes\Conditions_Manager')) {
            $templates_query = new \WP_Query(array(
                'post_type' => 'elementor_library',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_elementor_template_type',
                        'value' => array('single', 'single-post', 'single-page'), // 支持新旧版本
                        'compare' => 'IN',
                    ),
                ),
            ));
            
            if ($templates_query->have_posts()) {
                while ($templates_query->have_posts()) {
                    $templates_query->the_post();
                    $template_id = get_the_ID();
                    $conditions = get_post_meta($template_id, '_elementor_conditions', true);
                    $template_type = get_post_meta($template_id, '_elementor_template_type', true);
                    
                    $elementor_templates[] = array(
                        'id' => $template_id,
                        'title' => get_the_title(),
                        'type' => $template_type,
                        'conditions' => $conditions,
                        'edit_url' => admin_url('post.php?post=' . $template_id . '&action=elementor'),
                    );
                }
                wp_reset_postdata();
            }
        }
        
        ?>
        <div class="wrap">
            <h1>Shaw Immigration Projects - Template Debug Info</h1>
            
            <div class="notice notice-info">
                <p><strong>Instructions:</strong> Visit any Immigration Project detail page, then return here to view debug information.</p>
            </div>
            
            <?php if (empty($debug_info)): ?>
                <div class="notice notice-warning">
                    <p>No debug information yet. Please visit an Immigration Project detail page first.</p>
                </div>
            <?php else: ?>
                <div class="card" style="max-width: 100%; margin-top: 20px;">
                    <h2>Last Visited Page Info</h2>
                    <table class="widefat">
                        <tr>
                            <th style="width: 200px;">Visit Time</th>
                            <td><?php echo esc_html($debug_info['timestamp'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Post Type</th>
                            <td><code><?php echo esc_html($debug_info['post_type'] ?? 'N/A'); ?></code></td>
                        </tr>
                        <tr>
                            <th>Post ID</th>
                            <td>
                                <?php 
                                $post_id = $debug_info['post_id'] ?? null;
                                if ($post_id) {
                                    echo esc_html($post_id);
                                    echo ' - <a href="' . get_permalink($post_id) . '" target="_blank">View Page</a>';
                                    echo ' | <a href="' . get_edit_post_link($post_id) . '" target="_blank">Edit</a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Is Singular</th>
                            <td><?php echo $debug_info['is_singular'] ? 'Yes' : 'No'; ?></td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>
            
            <div class="card" style="max-width: 100%; margin-top: 20px;">
                <h2>All Elementor Single Templates</h2>
                <p style="color: #666;">
                    <strong>Note:</strong> Custom post types (like Immigration Project) should appear in <strong>Single Post</strong> conditions.
                </p>
                <?php if (empty($elementor_templates)): ?>
                    <div class="notice notice-warning inline">
                        <p>No Elementor Single templates found. Possible reasons:</p>
                        <ul>
                            <li>Elementor Pro is not activated</li>
                            <li>No Single templates have been created yet</li>
                            <li>Templates are not published</li>
                        </ul>
                        <p>
                            <a href="<?php echo admin_url('edit.php?post_type=elementor_library&tabs_group=theme'); ?>" class="button button-primary">
                                Create Single Template
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 120px;">Template Type</th>
                                <th>Template Name</th>
                                <th>Conditions</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($elementor_templates as $template): ?>
                                <tr>
                                    <td><?php echo esc_html($template['id']); ?></td>
                                    <td>
                                        <span style="background: #e7f5ff; padding: 3px 8px; border-radius: 3px; font-size: 11px;">
                                            <?php 
                                            $type_display = $template['type'] ?? 'single';
                                            echo esc_html(strtoupper(str_replace('-', ' ', $type_display))); 
                                            ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo esc_html($template['title']); ?></strong></td>
                                    <td>
                                        <?php if (!empty($template['conditions'])): ?>
                                            <?php 
                                            // 检查是否包含或排除 immigration_project
                                            $has_include = false;
                                            $has_exclude = false;
                                            
                                            foreach ($template['conditions'] as $condition) {
                                                $condition_str = is_array($condition) ? implode('/', $condition) : $condition;
                                                if (stripos($condition_str, 'immigration_project') !== false) {
                                                    if (stripos($condition_str, 'include') !== false) {
                                                        $has_include = true;
                                                    } elseif (stripos($condition_str, 'exclude') !== false) {
                                                        $has_exclude = true;
                                                    }
                                                }
                                            }
                                            
                                            $is_active = ($template['id'] == ($debug_info['elementor_location']['template_id'] ?? 0));
                                            ?>
                                            <?php if ($is_active): ?>
                                                <span style="background: #00a32a; color: white; padding: 3px 10px; border-radius: 3px; font-weight: bold;">Currently Active</span><br>
                                            <?php endif; ?>
                                            <?php if ($has_include): ?>
                                                <span style="color: green; font-weight: bold;">Includes Immigration Project</span><br>
                                            <?php endif; ?>
                                            <?php if ($has_exclude): ?>
                                                <span style="color: orange; font-weight: bold;">Excludes Immigration Project</span><br>
                                            <?php endif; ?>
                                            <pre style="background: #f5f5f5; padding: 5px; font-size: 11px; max-height: 100px; overflow: auto;"><?php print_r($template['conditions']); ?></pre>
                                        <?php else: ?>
                                            <em style="color: #999;">No conditions set</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url($template['edit_url']); ?>" class="button button-primary button-small" target="_blank">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <style>
            .card { padding: 20px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .card h2 { margin-top: 0; }
            .card table th { text-align: left; font-weight: 600; }
            .card pre { margin: 0; }
        </style>
        <?php
    }
}

// Initialize the plugin
function shaw_immigration_projects_init() {
    return Shaw_Immigration_Projects::get_instance();
}

add_action('plugins_loaded', 'shaw_immigration_projects_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    shaw_immigration_projects_init();
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});
