<?php
/**
 * Immigration Calculator Elementor Widget
 *
 * Allows users to insert calculators created in the backend
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Immigration_Calculator_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'shaw_immigration_calculator';
    }

    public function get_title() {
        return __('Immigration Calculator', 'shaw-immigration-projects');
    }

    public function get_icon() {
        return 'eicon-code';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_keywords() {
        return ['immigration', 'calculator', 'tool', 'shaw'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {

        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Calculator Settings', 'shaw-immigration-projects'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'calculator_id',
            [
                'label' => __('Select Calculator', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_calculators(),
                'default' => '',
                'description' => __('Select a calculator to display. Create calculators under Immigration Projects > Calculators.', 'shaw-immigration-projects'),
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Container Style', 'shaw-immigration-projects'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __('Padding', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .shaw-immigration-calculator' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_margin',
            [
                'label' => __('Margin', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .shaw-immigration-calculator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get list of available calculators
     */
    protected function get_calculators() {
        $calculators = array(
            '' => __('-- Select Calculator --', 'shaw-immigration-projects'),
        );

        $query = new WP_Query(array(
            'post_type' => 'immigration_calc',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $calculators[get_the_ID()] = get_the_title();
            }
            wp_reset_postdata();
        }

        return $calculators;
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $calculator_id = intval($settings['calculator_id']);

        if (!$calculator_id) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; text-align: center;">';
                echo __('Please select a calculator from the widget settings.', 'shaw-immigration-projects');
                echo '</div>';
            }
            return;
        }

        // Use the main plugin class to render the calculator
        $plugin = Shaw_Immigration_Projects::get_instance();
        echo $plugin->render_calculator($calculator_id);
    }

    /**
     * Render widget output in the editor (same as frontend)
     */
    protected function content_template() {
        // Leave empty - we use PHP rendering for both editor and frontend
    }
}
