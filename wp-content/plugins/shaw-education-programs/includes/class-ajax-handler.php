<?php
/**
 * AJAX Handler for Education Programs Widget
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Education_Programs_AJAX_Handler
{
    public static function init()
    {
        add_action('wp_ajax_shaw_load_education_programs', [__CLASS__, 'load_programs']);
        add_action('wp_ajax_nopriv_shaw_load_education_programs', [__CLASS__, 'load_programs']);
    }

    public static function load_programs()
    {
        check_ajax_referer('shaw_education_programs_nonce', 'nonce');

        $type = isset($_POST['program_type']) ? sanitize_text_field(urldecode(wp_unslash($_POST['program_type']))) : 'all';
        $focus = isset($_POST['program_focus']) ? sanitize_text_field(urldecode(wp_unslash($_POST['program_focus']))) : 'all';
        $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;
        $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 9;
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

        $query = Shaw_Education_Template_Renderer::get_programs_query([
            'post_type' => 'education_program',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'program_type' => $type,
            'program_focus' => $focus,
        ]);

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
            $response['success'] = true;
            $response['html'] = Shaw_Education_Template_Renderer::render_programs($post_ids, $template_id);
            $response['total'] = $query->found_posts;
            $response['pages'] = $query->max_num_pages;
            $response['message'] = sprintf(
                _n(
                    Shaw_Education_Programs::get_text('results_found_single'),
                    Shaw_Education_Programs::get_text('results_found_plural'),
                    $query->found_posts,
                    'shaw-education-programs'
                ),
                number_format_i18n($query->found_posts)
            );
        } else {
            $response['message'] = Shaw_Education_Programs::get_text('no_matching');
        }

        wp_reset_postdata();

        wp_send_json($response);
    }

    public static function get_initial_programs($args = [])
    {
        $defaults = [
            'posts_per_page' => 9,
            'template_id' => 0,
        ];

        $args = wp_parse_args($args, $defaults);
        $template_id = $args['template_id'];
        unset($args['template_id']);

        $query = Shaw_Education_Template_Renderer::get_programs_query($args);

        $result = [
            'html' => '',
            'total' => 0,
            'pages' => 0,
            'message' => '',
        ];

        if ($query->have_posts()) {
            $post_ids = wp_list_pluck($query->posts, 'ID');
            $result['html'] = Shaw_Education_Template_Renderer::render_programs($post_ids, $template_id);
            $result['total'] = $query->found_posts;
            $result['pages'] = $query->max_num_pages;
            $result['message'] = sprintf(
                _n(
                    Shaw_Education_Programs::get_text('results_found_single'),
                    Shaw_Education_Programs::get_text('results_found_plural'),
                    $query->found_posts,
                    'shaw-education-programs'
                ),
                number_format_i18n($query->found_posts)
            );
        }

        wp_reset_postdata();

        return $result;
    }
}

Shaw_Education_Programs_AJAX_Handler::init();
