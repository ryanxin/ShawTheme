<?php
/**
 * AJAX Handler for Immigration Projects Widget
 *
 * Handles AJAX requests for filtering and loading projects
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Immigration_AJAX_Handler {

    /**
     * Initialize AJAX handlers
     */
    public static function init() {
        // For logged-in users
        add_action('wp_ajax_shaw_load_projects', [__CLASS__, 'load_projects']);

        // For non-logged-in users
        add_action('wp_ajax_nopriv_shaw_load_projects', [__CLASS__, 'load_projects']);
    }

    /**
     * Load projects via AJAX
     */
    public static function load_projects() {
        // Verify nonce for security
        check_ajax_referer('shaw_immigration_nonce', 'nonce');

        // Get parameters
        $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : 'all';
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
        $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
        $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 9;
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

        // Prepare query arguments
        $query_args = [
            'post_type' => 'immigration_project',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'country' => $country,
            'category' => $category,
        ];

        // Get projects
        $query = Shaw_Immigration_Template_Renderer::get_projects_query($query_args);

        $response = [
            'success' => false,
            'html' => '',
            'total' => 0,
            'pages' => 0,
            'current_page' => $paged,
            'message' => '',
        ];

        if ($query->have_posts()) {
            $post_ids = wp_list_pluck($query->posts, 'ID');

            // Render projects using template
            $html = Shaw_Immigration_Template_Renderer::render_projects($post_ids, $template_id);

            $response['success'] = true;
            $response['html'] = $html;
            $response['total'] = $query->found_posts;
            $response['pages'] = $query->max_num_pages;
            $response['message'] = sprintf(
                _n(
                    '%s project found',
                    '%s projects found',
                    $query->found_posts,
                    'shaw-immigration-projects'
                ),
                number_format_i18n($query->found_posts)
            );
        } else {
            $response['message'] = __('No projects found matching your criteria.', 'shaw-immigration-projects');
        }

        wp_reset_postdata();

        wp_send_json($response);
    }

    /**
     * Get initial projects (for widget initial load)
     *
     * @param array $args Query arguments
     * @return array
     */
    public static function get_initial_projects($args = []) {
        $defaults = [
            'posts_per_page' => 9,
            'template_id' => 0,
        ];

        $args = wp_parse_args($args, $defaults);
        $template_id = $args['template_id'];
        unset($args['template_id']);

        // Get projects
        $query = Shaw_Immigration_Template_Renderer::get_projects_query($args);

        $result = [
            'html' => '',
            'total' => 0,
            'pages' => 0,
        ];

        if ($query->have_posts()) {
            $post_ids = wp_list_pluck($query->posts, 'ID');

            $result['html'] = Shaw_Immigration_Template_Renderer::render_projects($post_ids, $template_id);
            $result['total'] = $query->found_posts;
            $result['pages'] = $query->max_num_pages;
        }

        wp_reset_postdata();

        return $result;
    }
}

// Initialize AJAX handlers
Shaw_Immigration_AJAX_Handler::init();
