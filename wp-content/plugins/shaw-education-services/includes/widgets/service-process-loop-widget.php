<?php
/**
 * Service process loop widget.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Service_Process_Loop_Widget extends Shaw_Service_Loop_Widget_Base
{
    public function get_name()
    {
        return 'shaw_service_process_loop';
    }

    public function get_title()
    {
        return __('Shaw Service Process Loop', 'shaw-education-services');
    }

    public function get_icon()
    {
        return 'eicon-number-field';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['service', 'process', 'steps', 'loop', 'shaw'];
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
                'default' => '4',
                'options' => [
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
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

        if (!$this->get_section_enabled($post_id, 'show_process_section')) {
            return;
        }

        $items = $this->get_group_items($post_id, 'process_steps');
        if (empty($items)) {
            return;
        }

        $classes = $this->get_loop_class($settings, 'process');
        ?>
        <div class="<?php echo esc_attr($classes); ?>">
            <div class="shaw-service-process-grid" data-columns="<?php echo esc_attr($settings['columns']); ?>">
                <?php foreach ($items as $index => $item) : ?>
                    <?php $step_number = !empty($item['step_number']) ? $item['step_number'] : sprintf('%02d', $index + 1); ?>
                    <article class="shaw-service-process-item">
                        <div class="shaw-service-process-number"><?php echo esc_html($step_number); ?></div>
                        <?php if (!empty($item['title'])) : ?>
                            <h3 class="shaw-service-process-title"><?php echo esc_html($item['title']); ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($item['description'])) : ?>
                            <p class="shaw-service-process-text"><?php echo esc_html($item['description']); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
