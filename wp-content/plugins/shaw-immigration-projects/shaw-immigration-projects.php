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
        
        // Register CMB2 fields (Gallery and Repeater)
        add_action('cmb2_admin_init', array($this, 'register_cmb2_fields'));
        
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Disabled: Use Elementor Theme Builder instead of PHP template
        // add_filter('single_template', array($this, 'load_custom_template'));
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
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'immigration-projects',
            'rewrite'               => array('slug' => 'immigration-projects'),
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
                // NOTE: Gallery moved to CMB2 (ACF free doesn't support gallery)
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
                // NOTE: Life Media moved to CMB2 (ACF free doesn't support repeater)
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
     * Register CMB2 Fields (Gallery and Repeater)
     * CMB2 is free and fully supports these field types
     */
    public function register_cmb2_fields() {
        if (!function_exists('new_cmb2_box')) {
            return;
        }
        
        // Project Gallery (using file_list which is like gallery)
        $gallery_box = new_cmb2_box(array(
            'id'           => 'project_gallery_metabox',
            'title'        => __('Project Gallery (CMB2)', 'shaw-immigration-projects'),
            'object_types' => array('immigration_project'),
            'context'      => 'normal',
            'priority'     => 'high',
        ));
        
        $gallery_box->add_field(array(
            'name'         => __('Gallery Images', 'shaw-immigration-projects'),
            'desc'         => __('Upload or add multiple images', 'shaw-immigration-projects'),
            'id'           => 'project_gallery',
            'type'         => 'file_list',
            'preview_size' => array(100, 100),
            'query_args'   => array('type' => 'image'),
        ));
        
        // Life Media (using group repeater with image/video)
        $life_media_box = new_cmb2_box(array(
            'id'           => 'project_life_media_metabox',
            'title'        => __('Life Media - Food, School, View (CMB2)', 'shaw-immigration-projects'),
            'object_types' => array('immigration_project'),
            'context'      => 'normal',
            'priority'     => 'high',
        ));
        
        $life_media_group = $life_media_box->add_field(array(
            'id'          => 'life_media',
            'type'        => 'group',
            'description' => __('Add media items for life section (Food, School, View, etc.)', 'shaw-immigration-projects'),
            'options'     => array(
                'group_title'   => __('Media Item {#}', 'shaw-immigration-projects'),
                'add_button'    => __('Add Another Media', 'shaw-immigration-projects'),
                'remove_button' => __('Remove Media', 'shaw-immigration-projects'),
                'sortable'      => true,
            ),
        ));
        
        $life_media_box->add_group_field($life_media_group, array(
            'name' => __('Title', 'shaw-immigration-projects'),
            'desc' => __('e.g., Food, School, View', 'shaw-immigration-projects'),
            'id'   => 'title',
            'type' => 'text',
        ));
        
        $life_media_box->add_group_field($life_media_group, array(
            'name'    => __('Media Type', 'shaw-immigration-projects'),
            'id'      => 'media_type',
            'type'    => 'select',
            'options' => array(
                'image' => __('Image', 'shaw-immigration-projects'),
                'video' => __('Video', 'shaw-immigration-projects'),
            ),
            'default' => 'image',
        ));
        
        $life_media_box->add_group_field($life_media_group, array(
            'name' => __('Image', 'shaw-immigration-projects'),
            'desc' => __('Upload an image (for image type)', 'shaw-immigration-projects'),
            'id'   => 'image',
            'type' => 'file',
            'options' => array(
                'url' => false,
            ),
            'query_args' => array(
                'type' => 'image',
            ),
        ));
        
        $life_media_box->add_group_field($life_media_group, array(
            'name' => __('Video URL', 'shaw-immigration-projects'),
            'desc' => __('Enter video URL (for video type)', 'shaw-immigration-projects'),
            'id'   => 'video_url',
            'type' => 'text_url',
        ));
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

