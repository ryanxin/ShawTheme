<?php
/**
 * Template renderer for education programs.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Education_Template_Renderer
{
    public static function render_program($post_id, $template_id = 0)
    {
        if (!$post_id || !get_post($post_id)) {
            return '';
        }

        if ($template_id && class_exists('\Elementor\Plugin')) {
            return self::render_with_elementor_template($post_id, $template_id);
        }

        return self::render_default_html($post_id);
    }

    public static function render_programs($post_ids, $template_id = 0)
    {
        if (empty($post_ids)) {
            return '';
        }

        $html = '';
        foreach ($post_ids as $post_id) {
            $html .= self::render_program($post_id, $template_id);
        }

        return $html;
    }

    public static function get_programs_query($args = [])
    {
        $defaults = [
            'post_type' => 'education_program',
            'post_status' => 'publish',
            'posts_per_page' => 9,
            'paged' => 1,
        ];

        $args = wp_parse_args($args, $defaults);
        $tax_query = ['relation' => 'AND'];

        if (!empty($args['program_type']) && $args['program_type'] !== 'all') {
            $tax_query[] = [
                'taxonomy' => 'program_type',
                'field' => 'slug',
                'terms' => $args['program_type'],
            ];
        }

        if (!empty($args['program_focus']) && $args['program_focus'] !== 'all') {
            $tax_query[] = [
                'taxonomy' => 'program_focus',
                'field' => 'slug',
                'terms' => $args['program_focus'],
            ];
        }

        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }

        unset($args['program_type'], $args['program_focus']);

        return new WP_Query($args);
    }

    private static function render_with_elementor_template($post_id, $template_id)
    {
        if (!class_exists('\Elementor\Plugin')) {
            return self::render_default_html($post_id);
        }

        global $post;
        $original_post = $post;
        $post = get_post($post_id);
        setup_postdata($post);

        $template_post = get_post($template_id);
        if (!$template_post || $template_post->post_type !== 'elementor_library') {
            wp_reset_postdata();
            $post = $original_post;
            return self::render_default_html($post_id);
        }

        ob_start();

        try {
            $content = \Elementor\Plugin::instance()->frontend->get_builder_content($template_id, true);
            if (empty($content)) {
                echo self::render_default_html($post_id);
            } else {
                echo '<div class="shaw-program-item" data-post-id="' . esc_attr($post_id) . '">';
                echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</div>';
            }
        } catch (Exception $exception) {
            echo self::render_default_html($post_id);
        }

        $output = ob_get_clean();
        wp_reset_postdata();
        $post = $original_post;

        return $output;
    }

    private static function render_default_html($post_id)
    {
        $title = get_the_title($post_id);
        $permalink = get_permalink($post_id);
        $brief = get_field('brief', $post_id);
        $location = get_field('location', $post_id);
        $program_length_summary = get_field('program_length_summary', $post_id);
        $level = get_field('level', $post_id);
        $class_size = get_field('class_size', $post_id);
        $delivery_type = get_field('delivery_type', $post_id);
        $admission_requirements_summary = get_field('admission_requirements_summary', $post_id);
        $button_label = get_field('button_label', $post_id);

        $image_url = get_the_post_thumbnail_url($post_id, 'large');

        $delivery_type_label = '';
        if (is_string($delivery_type) && '' !== $delivery_type) {
            $delivery_map = [
                'inperson' => 'In Person',
                'online' => 'Online',
                'hybrid' => 'Mix / Hybrid',
            ];
            $delivery_type_label = isset($delivery_map[$delivery_type]) ? $delivery_map[$delivery_type] : $delivery_type;
        }

        $meta_lines = array_filter([
            $location,
            $program_length_summary,
            $level,
            $class_size,
            $delivery_type_label,
            $admission_requirements_summary,
        ]);

        ob_start();
        ?>
        <article class="shaw-program-card" data-post-id="<?php echo esc_attr($post_id); ?>">
            <?php if ($image_url) : ?>
                <a class="shaw-program-image" href="<?php echo esc_url($permalink); ?>">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">
                </a>
            <?php endif; ?>

            <div class="shaw-program-body">
                <h3 class="shaw-program-title">
                    <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                </h3>

                <?php if ($brief) : ?>
                    <div class="shaw-program-description">
                        <?php echo wp_kses_post(wpautop($brief)); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($meta_lines)) : ?>
                    <ul class="shaw-program-meta">
                        <?php foreach ($meta_lines as $line) : ?>
                            <li><?php echo esc_html($line); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="shaw-program-footer">
                    <a class="shaw-program-button" href="<?php echo esc_url($permalink); ?>">
                        <?php echo esc_html($button_label ? $button_label : Shaw_Education_Programs::get_text('view_program')); ?>
                    </a>
                </div>
            </div>
        </article>
        <?php

        return ob_get_clean();
    }
}
