<?php
/**
 * Plugin Name: Shaw Events
 * Plugin URI: https://shawglobal.com
 * Description: Custom Post Type for Events with Elementor widget support, date-based sorting, and automatic expiry
 * Version: 1.0.1
 * Author: Shaw Global
 * Author URI: https://shawglobal.com
 * Text Domain: shaw-events
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
  exit;
}

// Define plugin constants
define('SHAW_EVENTS_VERSION', '1.0.1');
define('SHAW_EVENTS_PATH', plugin_dir_path(__FILE__));
define('SHAW_EVENTS_URL', plugin_dir_url(__FILE__));

class Shaw_Events
{

  private static $instance = null;

  public static function get_instance()
  {
    if (null === self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct()
  {
    $this->init_hooks();
  }

  private function init_hooks()
  {
    add_action('init', array($this, 'register_post_type'));

    // Register ACF fields
    add_action('acf/init', array($this, 'register_acf_fields'), 10);
    add_action('plugins_loaded', array($this, 'register_acf_fields'), 15);
    add_action('after_setup_theme', array($this, 'register_acf_fields'), 10);

    // Enqueue scripts
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

    // Register Elementor Widget
    add_action('elementor/widgets/register', array($this, 'register_elementor_widgets'));
    add_action('elementor/frontend/after_enqueue_scripts', array($this, 'enqueue_widget_scripts'));
  }

  /**
   * Register Custom Post Type
   */
  public function register_post_type()
  {
    $labels = array(
      'name' => _x('Events', 'Post Type General Name', 'shaw-events'),
      'singular_name' => _x('Event', 'Post Type Singular Name', 'shaw-events'),
      'menu_name' => __('Events', 'shaw-events'),
      'name_admin_bar' => __('Event', 'shaw-events'),
      'archives' => __('Event Archives', 'shaw-events'),
      'attributes' => __('Event Attributes', 'shaw-events'),
      'parent_item_colon' => __('Parent Event:', 'shaw-events'),
      'all_items' => __('All Events', 'shaw-events'),
      'add_new_item' => __('Add New Event', 'shaw-events'),
      'add_new' => __('Add New', 'shaw-events'),
      'new_item' => __('New Event', 'shaw-events'),
      'edit_item' => __('Edit Event', 'shaw-events'),
      'update_item' => __('Update Event', 'shaw-events'),
      'view_item' => __('View Event', 'shaw-events'),
      'view_items' => __('View Events', 'shaw-events'),
      'search_items' => __('Search Event', 'shaw-events'),
      'not_found' => __('Not found', 'shaw-events'),
      'not_found_in_trash' => __('Not found in Trash', 'shaw-events'),
    );

    $args = array(
      'label' => __('Event', 'shaw-events'),
      'description' => __('Events and Activities', 'shaw-events'),
      'labels' => $labels,
      'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'custom-fields'),
      'hierarchical' => false,
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_position' => 6,
      'menu_icon' => 'dashicons-calendar-alt',
      'show_in_admin_bar' => true,
      'show_in_nav_menus' => true,
      'can_export' => true,
      'has_archive' => 'events',
      'exclude_from_search' => false,
      'publicly_queryable' => true,
      'capability_type' => 'post',
      'show_in_rest' => true,
      'rest_base' => 'shaw-events',
      'rewrite' => array('slug' => 'event'),
    );

    register_post_type('shaw_event', $args);
  }

  /**
   * Register ACF Fields
   */
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

    acf_add_local_field_group(array(
      'key' => 'group_shaw_event_details',
      'title' => 'Event Details',
      'fields' => array(
        // Event Date
        array(
          'key' => 'field_event_date',
          'label' => 'Event Date',
          'name' => 'event_date',
          'type' => 'date_picker',
          'instructions' => 'Select the event date',
          'required' => 1,
          'display_format' => 'Y-m-d',
          'return_format' => 'Y-m-d',
          'first_day' => 1,
        ),
        // Start Time
        array(
          'key' => 'field_event_start_time',
          'label' => 'Start Time',
          'name' => 'event_start_time',
          'type' => 'text',
          'instructions' => 'e.g., 10:30 AM',
          'placeholder' => '10:30 AM',
        ),
        // End Time
        array(
          'key' => 'field_event_end_time',
          'label' => 'End Time',
          'name' => 'event_end_time',
          'type' => 'text',
          'instructions' => 'e.g., 11:30 AM',
          'placeholder' => '11:30 AM',
        ),
        // Location
        array(
          'key' => 'field_event_location',
          'label' => 'Location',
          'name' => 'event_location',
          'type' => 'text',
          'instructions' => 'Event venue address',
          'placeholder' => 'e.g., 308 - 5811 Cooney Rd, Richmond BC V6X 3M1',
        ),
        // Speaker
        array(
          'key' => 'field_event_speaker',
          'label' => 'Speaker',
          'name' => 'event_speaker',
          'type' => 'text',
          'instructions' => 'Speaker name',
          'placeholder' => 'e.g., Cellia Xiao',
        ),
        // Language
        array(
          'key' => 'field_event_language',
          'label' => 'Language',
          'name' => 'event_language',
          'type' => 'select',
          'instructions' => 'Event language',
          'choices' => array(
            'chinese' => 'CHINESE',
            'english' => 'ENGLISH',
          ),
          'default_value' => 'chinese',
          'allow_null' => 0,
          'multiple' => 0,
          'ui' => 1,
        ),
        // Event Image
        array(
          'key' => 'field_event_image',
          'label' => 'Event Image',
          'name' => 'event_image',
          'type' => 'image',
          'instructions' => 'Left side image for event card (optional)',
          'return_format' => 'array',
          'preview_size' => 'medium',
        ),
        // Signup URL
        array(
          'key' => 'field_event_signup_url',
          'label' => 'Sign Up URL',
          'name' => 'event_signup_url',
          'type' => 'url',
          'instructions' => 'URL for Sign Up button',
          'placeholder' => 'https://...',
        ),
        // Post Link
        array(
          'key' => 'field_event_post_link',
          'label' => 'Event Detail Link',
          'name' => 'event_post_link',
          'type' => 'url',
          'instructions' => 'URL when clicking the entire event card (leave empty to use event permalink)',
          'placeholder' => 'https://...',
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'shaw_event',
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
   * Register Elementor Widgets
   */
  public function register_elementor_widgets($widgets_manager)
  {
    require_once SHAW_EVENTS_PATH . 'includes/widgets/events-widget.php';
    $widgets_manager->register(new Shaw_Events_Widget());
  }

  /**
   * Enqueue Widget Scripts and Styles
   */
  public function enqueue_widget_scripts()
  {
    // Widget styles
    wp_enqueue_style(
      'shaw-events-widget',
      SHAW_EVENTS_URL . 'assets/css/widget-style.css',
      array(),
      SHAW_EVENTS_VERSION
    );

    // Widget script
    wp_enqueue_script(
      'shaw-events-widget',
      SHAW_EVENTS_URL . 'assets/js/widget-frontend.js',
      array('jquery'),
      SHAW_EVENTS_VERSION,
      true
    );
  }

  /**
   * Enqueue scripts for archive/single pages
   */
  public function enqueue_scripts()
  {
    if (is_post_type_archive('shaw_event') || is_singular('shaw_event')) {
      wp_enqueue_style(
        'shaw-events',
        SHAW_EVENTS_URL . 'assets/css/widget-style.css',
        array(),
        SHAW_EVENTS_VERSION
      );
    }
  }

  /**
   * Get upcoming events
   */
  public static function get_upcoming_events($args = array())
  {
    $today = date('Y-m-d');

    $default_args = array(
      'post_type' => 'shaw_event',
      'posts_per_page' => 10,
      'post_status' => 'publish',
      'meta_key' => 'event_date',
      'orderby' => 'meta_value',
      'order' => 'ASC',
      'meta_query' => array(
        array(
          'key' => 'event_date',
          'value' => $today,
          'compare' => '>=',
          'type' => 'DATE',
        ),
      ),
    );

    $query_args = wp_parse_args($args, $default_args);

    return new WP_Query($query_args);
  }
}

// Initialize the plugin
function shaw_events_init()
{
  return Shaw_Events::get_instance();
}

add_action('plugins_loaded', 'shaw_events_init');

// Activation hook
register_activation_hook(__FILE__, function () {
  shaw_events_init();
  flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function () {
  flush_rewrite_rules();
});
