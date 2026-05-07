<?php
/**
 * Service compliance loop widget.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Service_Compliance_Loop_Widget extends Shaw_Service_Loop_Widget_Base
{
    public function get_name()
    {
        return 'shaw_service_compliance_loop';
    }

    public function get_title()
    {
        return __('Shaw Service Compliance Loop', 'shaw-education-services');
    }

    public function get_icon()
    {
        return 'eicon-info-box';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['service', 'compliance', 'standards', 'loop', 'shaw'];
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

        if (!$this->get_section_enabled($post_id, 'show_compliance_section')) {
            return;
        }

        $items = $this->get_group_items($post_id, 'compliance_items');
        if (empty($items)) {
            return;
        }

        $classes = $this->get_loop_class($settings, 'compliance');
        ?>
        <div class="<?php echo esc_attr($classes); ?>">
            <div class="shaw-service-compliance-grid" data-columns="<?php echo esc_attr($settings['columns']); ?>">
                <?php foreach ($items as $item) : ?>
                    <article class="shaw-service-compliance-item">
                        <?php if (!empty($item['text'])) : ?>
                            <p class="shaw-service-compliance-text"><?php echo esc_html($item['text']); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
