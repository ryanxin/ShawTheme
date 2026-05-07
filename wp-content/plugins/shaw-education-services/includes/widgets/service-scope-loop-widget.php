<?php
/**
 * Service scope loop widget.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Service_Scope_Loop_Widget extends Shaw_Service_Loop_Widget_Base
{
    public function get_name()
    {
        return 'shaw_service_scope_loop';
    }

    public function get_title()
    {
        return __('Shaw Service Scope Loop', 'shaw-education-services');
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['service', 'scope', 'loop', 'shaw'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content Settings', 'shaw-education-services'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->register_common_controls();

        $this->add_control(
            'columns',
            [
                'label' => __('Columns', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => '2',
                    '3' => '3',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $post_id = $this->resolve_service_id($settings);

        if (!$post_id) {
            $this->render_empty_state(__('Select a preview service or use this widget on a service single template.', 'shaw-education-services'));
            return;
        }

        if (!$this->get_section_enabled($post_id, 'show_scope_section')) {
            return;
        }

        $items = $this->get_group_items($post_id, 'scope_items');
        if (empty($items)) {
            return;
        }

        $classes = $this->get_loop_class($settings, 'scope');
        ?>
        <div class="<?php echo esc_attr($classes); ?>">
            <div class="shaw-service-scope-grid" data-columns="<?php echo esc_attr($settings['columns']); ?>">
                <?php foreach ($items as $item) : ?>
                    <?php $image_html = $this->get_file_image_html($item['image'] ?? '', 'large', 'shaw-service-scope-card-image'); ?>
                    <article class="shaw-service-scope-card<?php echo $image_html ? ' has-image' : ' is-text-only'; ?>">
                        <?php if ($image_html) : ?>
                            <div class="shaw-service-scope-card-media">
                                <?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        <?php endif; ?>
                        <div class="shaw-service-scope-card-body">
                            <?php if (!empty($item['title'])) : ?>
                                <h3 class="shaw-service-scope-card-title"><?php echo esc_html($item['title']); ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($item['description'])) : ?>
                                <p class="shaw-service-scope-card-text"><?php echo esc_html($item['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
