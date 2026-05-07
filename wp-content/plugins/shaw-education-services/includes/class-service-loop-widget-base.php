<?php
/**
 * Shared helpers for service loop widgets.
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class Shaw_Service_Loop_Widget_Base extends \Elementor\Widget_Base
{
    protected function register_common_controls()
    {
        $this->add_control(
            'accent_mode',
            [
                'label' => __('Accent Mode', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'red',
                'options' => [
                    'red' => __('Red', 'shaw-education-services'),
                    'blue' => __('Blue', 'shaw-education-services'),
                    'alternate' => __('Alternate', 'shaw-education-services'),
                ],
            ]
        );

        $this->add_control(
            'preview_service_id',
            [
                'label' => __('Preview Service', 'shaw-education-services'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_service_options(),
                'default' => '',
                'description' => __('Optional: choose a service when editing outside a service single preview.', 'shaw-education-services'),
            ]
        );
    }

    protected function get_service_options()
    {
        $options = ['' => __('Auto Detect', 'shaw-education-services')];

        $posts = get_posts([
            'post_type' => 'education_service',
            'post_status' => ['publish', 'draft', 'private'],
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        foreach ($posts as $post) {
            $options[$post->ID] = $post->post_title ? $post->post_title : sprintf(__('Service #%d', 'shaw-education-services'), $post->ID);
        }

        return $options;
    }

    protected function resolve_service_id($settings)
    {
        $post_id = 0;

        if (is_singular('education_service')) {
            $post_id = get_queried_object_id();
        }

        if (!$post_id) {
            $current_id = get_the_ID();
            if ($current_id && 'education_service' === get_post_type($current_id)) {
                $post_id = $current_id;
            }
        }

        if (!$post_id && !empty($settings['preview_service_id'])) {
            $post_id = absint($settings['preview_service_id']);
        }

        return $post_id;
    }

    protected function get_group_items($post_id, $meta_key)
    {
        $items = get_post_meta($post_id, $meta_key, true);
        if (!is_array($items)) {
            return [];
        }

        return array_values(array_filter($items, static function ($item) {
            return is_array($item) && !empty(array_filter($item, static function ($value) {
                if (is_array($value)) {
                    return !empty($value);
                }

                return '' !== trim((string) $value);
            }));
        }));
    }

    protected function get_section_enabled($post_id, $field_name)
    {
        $value = get_field($field_name, $post_id);

        return false !== $value && '0' !== (string) $value && '' !== (string) $value;
    }

    protected function get_loop_class($settings, $block_name)
    {
        $accent_mode = !empty($settings['accent_mode']) ? $settings['accent_mode'] : 'red';

        return sprintf(
            'shaw-service-loop shaw-service-loop--%1$s shaw-service-loop--%2$s',
            sanitize_html_class($block_name),
            sanitize_html_class($accent_mode)
        );
    }

    protected function render_empty_state($message)
    {
        echo '<div class="shaw-service-loop-empty">' . esc_html($message) . '</div>';
    }

    protected function get_file_image_html($value, $size = 'large', $class_name = '')
    {
        if (empty($value)) {
            return '';
        }

        $attachment_id = 0;
        $url = '';

        if (is_array($value)) {
            if (!empty($value['id'])) {
                $attachment_id = (int) $value['id'];
            }
            if (!empty($value['url'])) {
                $url = $value['url'];
            } elseif (!empty($value[0]) && is_string($value[0])) {
                $url = $value[0];
            }
        } elseif (is_numeric($value)) {
            $attachment_id = (int) $value;
        } elseif (is_string($value)) {
            $url = $value;
        }

        if ($attachment_id) {
            return wp_get_attachment_image($attachment_id, $size, false, ['class' => $class_name]);
        }

        if ($url) {
            return sprintf('<img src="%1$s" alt="" class="%2$s">', esc_url($url), esc_attr($class_name));
        }

        return '';
    }
}
