<?php
/**
 * Immigration Projects Elementor Widget
 *
 * Custom widget with AJAX filtering and Elementor template support
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_Immigration_Projects_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'shaw_immigration_projects';
    }

    public function get_title()
    {
        return __('Immigration Projects Filter', 'shaw-immigration-projects');
    }

    public function get_icon()
    {
        return 'eicon-filter';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['immigration', 'projects', 'filter', 'ajax', 'shaw'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls()
    {

        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content Settings', 'shaw-immigration-projects'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Elementor Template Selection
        $this->add_control(
            'loop_template',
            [
                'label' => __('Loop Item Template', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_elementor_templates(),
                'default' => '',
                'description' => __('Select an Elementor template to use for each project card. Leave empty to use default HTML.', 'shaw-immigration-projects'),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 9,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columns', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
            ]
        );

        $this->add_control(
            'show_filters',
            [
                'label' => __('Show Filters', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'shaw-immigration-projects'),
                'label_off' => __('No', 'shaw-immigration-projects'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_country_filter',
            [
                'label' => __('Show Country Filter', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'shaw-immigration-projects'),
                'label_off' => __('No', 'shaw-immigration-projects'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_filters' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_category_filter',
            [
                'label' => __('Show Category Filter', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'shaw-immigration-projects'),
                'label_off' => __('No', 'shaw-immigration-projects'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_filters' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Filters
        $this->start_controls_section(
            'filter_style_section',
            [
                'label' => __('Filter Style', 'shaw-immigration-projects'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_filters' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'filter_spacing',
            [
                'label' => __('Filter Spacing', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .shaw-filter-section' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_typography',
                'selector' => '{{WRAPPER}} .shaw-filter-tab',
            ]
        );

        $this->add_control(
            'filter_normal_color',
            [
                'label' => __('Normal Color', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .shaw-filter-tab' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_active_color',
            [
                'label' => __('Active Color', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1448',
                'selectors' => [
                    '{{WRAPPER}} .shaw-filter-tab.active' => 'color: {{VALUE}}; border-bottom-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Grid
        $this->start_controls_section(
            'grid_style_section',
            [
                'label' => __('Grid Style', 'shaw-immigration-projects'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'grid_gap',
            [
                'label' => __('Grid Gap', 'shaw-immigration-projects'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .shaw-projects-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get available Elementor templates
     */
    private function get_elementor_templates()
    {
        $templates = ['' => __('Default (Built-in HTML)', 'shaw-immigration-projects')];

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

    /**
     * Render widget output on the frontend
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Get filter options
        $countries = get_terms([
            'taxonomy' => 'project_country',
            'hide_empty' => true,
        ]);

        $categories = get_terms([
            'taxonomy' => 'project_category',
            'hide_empty' => true,
        ]);

        // Get initial projects (server-side render for better SEO and initial load)
        // Check for URL parameters for deep linking
        $active_country = isset($_GET['project_country']) ? sanitize_text_field($_GET['project_country']) : 'all';
        $active_category = isset($_GET['project_category']) ? sanitize_text_field($_GET['project_category']) : 'all';

        $initial_data = Shaw_Immigration_AJAX_Handler::get_initial_projects([
            'posts_per_page' => $settings['posts_per_page'],
            'template_id' => $settings['loop_template'],
            'country' => $active_country,
            'category' => $active_category,
        ]);

        ?>
        <div class="shaw-immigration-widget"
             data-widget-id="<?php echo $this->get_id(); ?>"
             data-template-id="<?php echo esc_attr($settings['loop_template']); ?>"
             data-posts-per-page="<?php echo esc_attr($settings['posts_per_page']); ?>"
             data-columns="<?php echo esc_attr($settings['columns']); ?>">

            <?php if ($settings['show_filters'] === 'yes'): ?>
                <!-- Filters Section -->
                <div class="shaw-filters-wrapper">

                    <?php if ($settings['show_country_filter'] === 'yes' && !empty($countries)): ?>
                        <!-- Country Filter -->
                        <div class="shaw-filter-section shaw-country-filter">
                            <div class="shaw-filter-tabs">
                                <div class="shaw-filter-tab <?php echo ($active_country === 'all') ? 'active' : ''; ?>" data-filter-type="country" data-filter-value="all">
                                    全部
                                </div>
                                <?php foreach ($countries as $country): ?>
                                    <div class="shaw-filter-tab <?php echo ($active_country === $country->slug) ? 'active' : ''; ?>" data-filter-type="country" data-filter-value="<?php echo esc_attr($country->slug); ?>">
                                        <?php echo esc_html($country->name); ?>
                                        <span class="count">(<?php echo $country->count; ?>)</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($settings['show_category_filter'] === 'yes' && !empty($categories)): ?>
                        <!-- Category Filter -->
                        <div class="shaw-filter-section shaw-category-filter">
                            <div class="shaw-filter-tabs">
                                <div class="shaw-filter-tab <?php echo ($active_category === 'all') ? 'active' : ''; ?>" data-filter-type="category" data-filter-value="all">
                                    全部
                                </div>
                                <?php foreach ($categories as $category): ?>
                                    <div class="shaw-filter-tab <?php echo ($active_category === $category->slug) ? 'active' : ''; ?>" data-filter-type="category" data-filter-value="<?php echo esc_attr($category->slug); ?>">
                                        <?php echo esc_html($category->name); ?>
                                        <span class="count">(<?php echo $category->count; ?>)</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

            <!-- Loading Indicator -->
            <div class="shaw-loading-overlay" style="display: none;">
                <div class="shaw-spinner"></div>
            </div>

            <!-- Projects Grid -->
            <div class="shaw-projects-grid" data-columns="<?php echo esc_attr($settings['columns']); ?>">
                <?php
                // Display initial projects
                if (!empty($initial_data['html'])) {
                    echo $initial_data['html'];
                } else {
                    echo '<div class="shaw-no-results"><p>没有找到项目。</p></div>';
                }
                ?>
            </div>

            <!-- Pagination -->
            <div class="shaw-pagination" style="<?php echo ($initial_data['pages'] > 1) ? '' : 'display: none;'; ?>">
                <!-- Pagination will be inserted here by JavaScript -->
            </div>

            <!-- No Results Message -->
            <div class="shaw-no-results" style="display: none;">
                <p>没有找到符合条件的项目。</p>
            </div>

        </div>
        <?php
    }

    /**
     * Render widget output in the editor (Elementor preview)
     */
    protected function content_template()
    {
        ?>
        <#
           var widgetId='shaw-widget-' + Math.random().toString(36).substr(2, 9);
           #>
            <div class="shaw-immigration-widget" data-widget-id="{{ widgetId }}">

                <# if (settings.show_filters==='yes' ) { #>
                    <div class="shaw-filters-wrapper">

                        <# if (settings.show_country_filter==='yes' ) { #>
                            <div class="shaw-filter-section shaw-country-filter">
                                <div class="shaw-filter-tabs">
                                    <div class="shaw-filter-tab active">全部</div>
                                    <div class="shaw-filter-tab">加拿大 <span class="count">(15)</span></div>
                                    <div class="shaw-filter-tab">土耳其 <span class="count">(8)</span></div>
                                    <div class="shaw-filter-tab">希腊 <span class="count">(5)</span></div>
                                </div>
                            </div>
                            <# } #>

                                <# if (settings.show_category_filter==='yes' ) { #>
                                    <div class="shaw-filter-section shaw-category-filter">
                                        <div class="shaw-filter-tabs">
                                            <div class="shaw-filter-tab active">全部</div>
                                            <div class="shaw-filter-tab">企业家移民 <span class="count">(12)</span></div>
                                            <div class="shaw-filter-tab">技术移民 <span class="count">(10)</span></div>
                                        </div>
                                    </div>
                                    <# } #>

                    </div>
                    <# } #>

                        <div class="shaw-projects-grid" data-columns="{{ settings.columns }}">
                            <div style="text-align: center; padding: 60px 20px; color: #999;">
                                <p style="font-size: 16px;">Preview: Projects will be loaded here</p>
                                <p style="font-size: 14px;">Template: {{ settings.loop_template || 'Default HTML' }}</p>
                                <p style="font-size: 14px;">Posts per page: {{ settings.posts_per_page }}</p>
                                <p style="font-size: 14px;">Columns: {{ settings.columns }}</p>
                            </div>
                        </div>

            </div>
            <?php
    }
}
