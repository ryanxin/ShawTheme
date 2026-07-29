<?php
/**
 * External page widget.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Shaw_External_Page_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'shaw_external_page';
    }

    public function get_title()
    {
        return __('Shaw External Page', 'shaw-education-services');
    }

    public function get_icon()
    {
        return 'eicon-editor-external-link';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_keywords()
    {
        return ['shaw', 'external', 'iframe', 'embed', 'page'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('External Page Settings', 'shaw-education-services'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'external_url',
            [
                'label' => __('External URL', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://example.com/page',
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'frame_title',
            [
                'label' => __('Frame Title', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('External Page', 'shaw-education-services'),
                'placeholder' => __('External Page', 'shaw-education-services'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'min_height_desktop',
            [
                'label' => __('Desktop Min Height', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1200,
                'min' => 200,
                'step' => 10,
            ]
        );

        $this->add_control(
            'min_height_tablet',
            [
                'label' => __('Tablet Min Height', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 900,
                'min' => 200,
                'step' => 10,
            ]
        );

        $this->add_control(
            'min_height_mobile',
            [
                'label' => __('Mobile Min Height', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 700,
                'min' => 200,
                'step' => 10,
            ]
        );

        $this->add_control(
            'full_width',
            [
                'label' => __('Full Width', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'shaw-education-services'),
                'label_off' => __('No', 'shaw-education-services'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'height_mode',
            [
                'label' => __('Height Mode', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'viewport',
                'options' => [
                    'viewport' => __('Viewport Height', 'shaw-education-services'),
                    'content' => __('Tall Content Frame', 'shaw-education-services'),
                ],
                'description' => __('Viewport Height reduces nested scrolling. Tall Content Frame gives the iframe more page height.', 'shaw-education-services'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render_empty_state()
    {
        echo '<div class="shaw-external-page__empty">' .
            esc_html__('Add an External URL to display the page here.', 'shaw-education-services') .
            '</div>';
    }

    protected function get_external_url($settings)
    {
        $url = '';

        if (!empty($settings['external_url'])) {
            if (is_array($settings['external_url']) && !empty($settings['external_url']['url'])) {
                $url = $settings['external_url']['url'];
            } elseif (is_string($settings['external_url'])) {
                $url = $settings['external_url'];
            }
        }

        return esc_url($url);
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $url = $this->get_external_url($settings);

        if (!$url) {
            $this->render_empty_state();
            return;
        }

        $frame_title = !empty($settings['frame_title']) ? $settings['frame_title'] : __('External Page', 'shaw-education-services');
        $desktop_height = !empty($settings['min_height_desktop']) ? max(200, (int) $settings['min_height_desktop']) : 1200;
        $tablet_height = !empty($settings['min_height_tablet']) ? max(200, (int) $settings['min_height_tablet']) : 900;
        $mobile_height = !empty($settings['min_height_mobile']) ? max(200, (int) $settings['min_height_mobile']) : 700;
        $is_full_width = !empty($settings['full_width']) && 'yes' === $settings['full_width'];
        $height_mode = !empty($settings['height_mode']) ? $settings['height_mode'] : 'viewport';
        $embed_id = $this->get_id();
        $iframe_url = add_query_arg(
            [
                'shaw_embed' => '1',
                'shaw_embed_id' => $embed_id,
            ],
            $url
        );
        $parsed_url = wp_parse_url($url);
        $external_origin = '';
        if (!empty($parsed_url['scheme']) && !empty($parsed_url['host'])) {
            $external_origin = $parsed_url['scheme'] . '://' . $parsed_url['host'];
            if (!empty($parsed_url['port'])) {
                $external_origin .= ':' . $parsed_url['port'];
            }
        }

        $wrapper_classes = 'shaw-external-page';
        if (!$is_full_width) {
            $wrapper_classes .= ' shaw-external-page--boxed';
        }
        if ('viewport' === $height_mode) {
            $wrapper_classes .= ' shaw-external-page--viewport';
        } else {
            $wrapper_classes .= ' shaw-external-page--content';
        }

        $wrapper_style = sprintf(
            '--shaw-external-page-min-height-desktop:%1$dpx;--shaw-external-page-min-height-tablet:%2$dpx;--shaw-external-page-min-height-mobile:%3$dpx;',
            $desktop_height,
            $tablet_height,
            $mobile_height
        );
        ?>
        <div class="<?php echo esc_attr($wrapper_classes); ?>" style="<?php echo esc_attr($wrapper_style); ?>">
            <iframe
                class="shaw-external-page__frame"
                src="<?php echo esc_url($iframe_url); ?>"
                title="<?php echo esc_attr($frame_title); ?>"
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"
                data-embed-id="<?php echo esc_attr($embed_id); ?>"
                data-external-origin="<?php echo esc_attr($external_origin); ?>"
            ></iframe>
        </div>
        <?php
    }
}
