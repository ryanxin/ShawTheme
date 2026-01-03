<?php
/**
 * Template Renderer Class
 *
 * Renders Elementor templates or default HTML for immigration projects
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Immigration_Template_Renderer
{

    /**
     * Render a single project using Elementor template or default HTML
     *
     * @param int $post_id Project post ID
     * @param int $template_id Elementor template ID (optional)
     * @return string Rendered HTML
     */
    public static function render_project($post_id, $template_id = 0)
    {
        if (!$post_id || !get_post($post_id)) {
            return '';
        }

        // If template ID is provided and Elementor is available, use it
        if ($template_id && class_exists('\Elementor\Plugin')) {
            return self::render_with_elementor_template($post_id, $template_id);
        }

        // Otherwise, use default HTML
        return self::render_default_html($post_id);
    }

    /**
     * Render project using Elementor template
     *
     * @param int $post_id Project post ID
     * @param int $template_id Elementor template ID
     * @return string Rendered HTML
     */
    private static function render_with_elementor_template($post_id, $template_id)
    {
        if (!class_exists('\Elementor\Plugin')) {
            return self::render_default_html($post_id);
        }

        // Switch to the project post context
        global $post;
        $original_post = $post;
        $post = get_post($post_id);
        setup_postdata($post);

        // Get Elementor instance
        $elementor = \Elementor\Plugin::instance();

        // Check if template exists
        $template_post = get_post($template_id);
        if (!$template_post || $template_post->post_type !== 'elementor_library') {
            wp_reset_postdata();
            $post = $original_post;
            return self::render_default_html($post_id);
        }

        // Render the template
        ob_start();

        try {
            // Use Elementor's frontend to render the template
            $content = $elementor->frontend->get_builder_content($template_id, true);

            if (empty($content)) {
                // Fallback if content is empty
                echo self::render_default_html($post_id);
            } else {
                // Wrap in a container
                echo '<div class="shaw-project-item" data-post-id="' . esc_attr($post_id) . '">';
                echo $content;
                echo '</div>';
            }
        } catch (Exception $e) {
            // If rendering fails, use default HTML
            echo self::render_default_html($post_id);
        }

        $output = ob_get_clean();

        // Restore original post context
        wp_reset_postdata();
        $post = $original_post;

        return $output;
    }

    /**
     * Render project using default HTML template
     *
     * @param int $post_id Project post ID
     * @return string Rendered HTML
     */
    private static function render_default_html($post_id)
    {
        // Get project data
        $title = get_the_title($post_id);
        $permalink = get_permalink($post_id);
        $card_image = get_field('card_image', $post_id);
        $short_description = get_field('short_description', $post_id);
        $processing_period = get_field('processing_period', $post_id);
        $processing_period_link = get_field('processing_period_link', $post_id);
        $identity_type = get_field('identity_type', $post_id);
        $investment_amount = get_field('investment_amount', $post_id);
        $residential_requirements = get_field('residential_requirements', $post_id);
        $language = get_field('language', $post_id);

        // Get taxonomies
        $countries = wp_get_post_terms($post_id, 'project_country');
        $categories = wp_get_post_terms($post_id, 'project_category');

        // Get image URL
        $image_url = '';
        if ($card_image && is_array($card_image)) {
            $image_url = $card_image['url'] ?? '';
        } elseif ($card_image) {
            $image_url = wp_get_attachment_image_url($card_image, 'large');
        }

        // Fallback to featured image
        if (!$image_url) {
            $image_url = get_the_post_thumbnail_url($post_id, 'large');
        }

        // Build HTML
        ob_start();
        ?>
        <div class="shaw-project-card" data-post-id="<?php echo esc_attr($post_id); ?>">

            <?php if ($image_url): ?>
                <div class="shaw-project-image">
                    <a href="<?php echo esc_url($permalink); ?>">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>

                    <?php if (!empty($categories)): ?>
                        <div class="shaw-project-category-badge">
                            <?php echo esc_html($categories[0]->name); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="shaw-project-content">

                <h3 class="shaw-project-title">
                    <a href="<?php echo esc_url($permalink); ?>">
                        <?php echo esc_html($title); ?>
                    </a>
                </h3>

                <?php if ($short_description): ?>
                    <div class="shaw-project-description">
                        <?php echo wp_kses_post(wpautop($short_description)); ?>
                    </div>
                <?php endif; ?>

                <div class="shaw-project-info">
                    <?php if ($processing_period): ?>
                        <div class="shaw-info-item">
                            <span class="shaw-info-label"><?php echo esc_html(Shaw_Immigration_Projects::get_text('processing_period')); ?></span>
                            <span class="shaw-info-value">
                                <?php if ($processing_period_link): ?>
                                    <a href="<?php echo esc_url($processing_period_link); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($processing_period); ?></a>
                                <?php else: ?>
                                    <?php echo esc_html($processing_period); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ($identity_type): ?>
                        <div class="shaw-info-item">
                            <span class="shaw-info-label"><?php echo esc_html(Shaw_Immigration_Projects::get_text('identity_type')); ?></span>
                            <span class="shaw-info-value"><?php echo esc_html($identity_type); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($investment_amount): ?>
                        <div class="shaw-info-item">
                            <span class="shaw-info-label"><?php echo esc_html(Shaw_Immigration_Projects::get_text('investment_amount')); ?></span>
                            <span class="shaw-info-value"><?php echo esc_html($investment_amount); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($residential_requirements): ?>
                        <div class="shaw-info-item">
                            <span class="shaw-info-label"><?php echo esc_html(Shaw_Immigration_Projects::get_text('residential_req')); ?></span>
                            <span class="shaw-info-value"><?php echo esc_html($residential_requirements); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($language): ?>
                        <div class="shaw-info-item">
                            <span class="shaw-info-label"><?php echo esc_html(Shaw_Immigration_Projects::get_text('language_req')); ?></span>
                            <span class="shaw-info-value"><?php echo esc_html($language); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="shaw-project-footer">
                    <a href="<?php echo esc_url($permalink); ?>" class="shaw-project-btn">
                        <?php echo esc_html(Shaw_Immigration_Projects::get_text('inquire_price')); ?>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M8 1l7 7-7 7M1 8h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>

            </div>

        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render multiple projects
     *
     * @param array $post_ids Array of post IDs
     * @param int $template_id Elementor template ID (optional)
     * @return string Rendered HTML
     */
    public static function render_projects($post_ids, $template_id = 0)
    {
        if (empty($post_ids)) {
            return '';
        }

        $html = '';
        foreach ($post_ids as $post_id) {
            $html .= self::render_project($post_id, $template_id);
        }

        return $html;
    }

    /**
     * Get projects query based on filters
     *
     * @param array $args Query arguments
     * @return WP_Query
     */
    public static function get_projects_query($args = [])
    {
        $defaults = [
            'post_type' => 'immigration_project',
            'post_status' => 'publish',
            'posts_per_page' => 9,
            'paged' => 1,
        ];

        $args = wp_parse_args($args, $defaults);

        // Build tax query
        $tax_query = ['relation' => 'AND'];

        if (!empty($args['country']) && $args['country'] !== 'all') {
            $tax_query[] = [
                'taxonomy' => 'project_country',
                'field' => 'slug',
                'terms' => $args['country'],
            ];
        }

        if (!empty($args['category']) && $args['category'] !== 'all') {
            $tax_query[] = [
                'taxonomy' => 'project_category',
                'field' => 'slug',
                'terms' => $args['category'],
            ];
        }

        if (!empty($args['type']) && $args['type'] !== 'all') {
            $tax_query[] = [
                'taxonomy' => 'project_type',
                'field' => 'slug',
                'terms' => $args['type'],
            ];
        }

        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }

        // Remove custom args
        unset($args['country']);
        unset($args['category']);
        unset($args['type']);

        return new WP_Query($args);
    }
}
