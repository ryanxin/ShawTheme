<?php
/**
 * Education Programs Elementor Widget
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Education_Programs_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'shaw_education_programs';
    }

    public function get_title()
    {
        return __('Education Programs Filter', 'shaw-education-programs');
    }

    public function get_icon()
    {
        return 'eicon-post-list';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['education', 'programs', 'courses', 'filter', 'ajax', 'shaw'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content Settings', 'shaw-education-programs'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'loop_template',
            [
                'label' => __('Loop Item Template', 'shaw-education-programs'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_elementor_templates(),
                'default' => '',
                'description' => __('Select a loop template for each program card, or use the built-in card layout.', 'shaw-education-programs'),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'shaw-education-programs'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 9,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columns', 'shaw-education-programs'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
            ]
        );

        $this->add_control(
            'show_type_filter',
            [
                'label' => __('Show Program Type Filter', 'shaw-education-programs'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'shaw-education-programs'),
                'label_off' => __('No', 'shaw-education-programs'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_focus_filter',
            [
                'label' => __('Show Program Focus Filter', 'shaw-education-programs'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'shaw-education-programs'),
                'label_off' => __('No', 'shaw-education-programs'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'filter_style_section',
            [
                'label' => __('Filter Style', 'shaw-education-programs'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'filter_active_color',
            [
                'label' => __('Active Color', 'shaw-education-programs'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1D252C',
                'selectors' => [
                    '{{WRAPPER}} .shaw-filter-tab.active' => 'color: {{VALUE}}; border-bottom-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_muted_color',
            [
                'label' => __('Muted Color', 'shaw-education-programs'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .shaw-filter-tab' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_elementor_templates()
    {
        $templates = ['' => __('Default (Built-in Card)', 'shaw-education-programs')];

        if (!class_exists('\Elementor\Plugin')) {
            return $templates;
        }

        $query = new \WP_Query([
            'post_type' => 'elementor_library',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => '_elementor_template_type',
                    'value' => ['loop-item', 'section', 'container'],
                    'compare' => 'IN',
                ],
            ],
        ]);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $template_type = get_post_meta(get_the_ID(), '_elementor_template_type', true);
                $templates[get_the_ID()] = get_the_title() . ' (' . $template_type . ')';
            }
            wp_reset_postdata();
        }

        return $templates;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $types = get_terms([
            'taxonomy' => 'program_type',
            'hide_empty' => true,
        ]);

        $focus_terms = get_terms([
            'taxonomy' => 'program_focus',
            'hide_empty' => true,
        ]);

        $active_type = isset($_GET['program_type']) ? sanitize_text_field(urldecode(wp_unslash($_GET['program_type']))) : 'all';
        $active_focus = isset($_GET['program_focus']) ? sanitize_text_field(urldecode(wp_unslash($_GET['program_focus']))) : 'all';

        $initial_data = Shaw_Education_Programs_AJAX_Handler::get_initial_programs([
            'posts_per_page' => $settings['posts_per_page'],
            'template_id' => $settings['loop_template'],
            'program_type' => $active_type,
            'program_focus' => $active_focus,
        ]);

        ?>
        <div
            class="shaw-education-widget"
            data-widget-id="<?php echo esc_attr($this->get_id()); ?>"
            data-template-id="<?php echo esc_attr($settings['loop_template']); ?>"
            data-posts-per-page="<?php echo esc_attr($settings['posts_per_page']); ?>"
            data-columns="<?php echo esc_attr($settings['columns']); ?>"
        >
            <div class="shaw-filters-wrapper">
                <?php if ('yes' === $settings['show_type_filter'] && !empty($types) && !is_wp_error($types)) : ?>
                    <div class="shaw-filter-section shaw-program-type-filter">
                        <div class="shaw-filter-tabs shaw-filter-tabs-primary">
                            <button class="shaw-filter-tab <?php echo ('all' === $active_type) ? 'active' : ''; ?>" type="button" data-filter-type="program_type" data-filter-value="all">
                                <?php echo esc_html(Shaw_Education_Programs::get_text('all_programs')); ?>
                            </button>
                            <?php foreach ($types as $type) : ?>
                                <button class="shaw-filter-tab <?php echo ($active_type === $type->slug) ? 'active' : ''; ?>" type="button" data-filter-type="program_type" data-filter-value="<?php echo esc_attr($type->slug); ?>">
                                    <?php echo esc_html($type->name); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ('yes' === $settings['show_focus_filter'] && !empty($focus_terms) && !is_wp_error($focus_terms)) : ?>
                    <div class="shaw-filter-section shaw-program-focus-filter">
                        <div class="shaw-filter-tabs shaw-filter-tabs-secondary">
                            <button class="shaw-filter-tab <?php echo ('all' === $active_focus) ? 'active' : ''; ?>" type="button" data-filter-type="program_focus" data-filter-value="all">
                                <?php echo esc_html(Shaw_Education_Programs::get_text('all')); ?>
                            </button>
                            <?php foreach ($focus_terms as $focus_term) : ?>
                                <button class="shaw-filter-tab <?php echo ($active_focus === $focus_term->slug) ? 'active' : ''; ?>" type="button" data-filter-type="program_focus" data-filter-value="<?php echo esc_attr($focus_term->slug); ?>">
                                    <?php echo esc_html($focus_term->name); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="shaw-loading-overlay" style="display: none;">
                <div class="shaw-spinner"></div>
            </div>

            <div class="shaw-programs-grid" data-columns="<?php echo esc_attr($settings['columns']); ?>">
                <?php
                if (!empty($initial_data['html'])) {
                    echo $initial_data['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                } else {
                    echo '<div class="shaw-no-results is-static"><p>' . esc_html(Shaw_Education_Programs::get_text('no_programs')) . '</p></div>';
                }
                ?>
            </div>

            <div class="shaw-pagination" style="<?php echo ($initial_data['pages'] > 1) ? '' : 'display: none;'; ?>"></div>

            <div class="shaw-no-results" style="display: none;">
                <p><?php echo esc_html(Shaw_Education_Programs::get_text('no_matching')); ?></p>
            </div>
        </div>
        <?php
    }

    protected function content_template()
    {
        ?>
        <div class="shaw-education-widget">
            <div class="shaw-filters-wrapper">
                <div class="shaw-filter-section">
                    <div class="shaw-filter-tabs shaw-filter-tabs-primary">
                        <button class="shaw-filter-tab active" type="button">All Programs</button>
                        <button class="shaw-filter-tab" type="button">Open Studies</button>
                        <button class="shaw-filter-tab" type="button">Certificate Programs</button>
                        <button class="shaw-filter-tab" type="button">Pathway Programs</button>
                    </div>
                </div>
                <div class="shaw-filter-section">
                    <div class="shaw-filter-tabs shaw-filter-tabs-secondary">
                        <button class="shaw-filter-tab active" type="button">All</button>
                        <button class="shaw-filter-tab" type="button">English</button>
                        <button class="shaw-filter-tab" type="button">French</button>
                        <button class="shaw-filter-tab" type="button">IELTS Preparation</button>
                    </div>
                </div>
            </div>

            <div class="shaw-programs-grid" data-columns="{{ settings.columns }}">
                <div class="shaw-program-card">
                    <div class="shaw-program-image" style="background:#d9dde1; min-height:300px;"></div>
                    <div class="shaw-program-body">
                        <h3 class="shaw-program-title">English Language Program</h3>
                        <div class="shaw-program-description"><p>Improve communication skills through immersive and structured English training.</p></div>
                        <ul class="shaw-program-meta">
                            <li>Vancouver, Canada</li>
                            <li>8 Weeks</li>
                            <li>Beginner – Intermediate</li>
                            <li>Small Class · No Study Permit Required</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
