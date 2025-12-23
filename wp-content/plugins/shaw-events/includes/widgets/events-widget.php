<?php
/**
 * Shaw Events Elementor Widget
 *
 * Custom widget for displaying upcoming events
 */

if (!defined('ABSPATH')) {
  exit;
}

class Shaw_Events_Widget extends \Elementor\Widget_Base
{

  public function get_name()
  {
    return 'shaw_events';
  }

  public function get_title()
  {
    return __('Shaw Events List', 'shaw-events');
  }

  public function get_icon()
  {
    return 'eicon-calendar';
  }

  public function get_categories()
  {
    return ['general'];
  }

  public function get_keywords()
  {
    return ['events', 'calendar', 'shaw', 'upcoming'];
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
        'label' => __('Content Settings', 'shaw-events'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'posts_per_page',
      [
        'label' => __('Number of Events', 'shaw-events'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => 10,
        'min' => 1,
        'max' => 50,
      ]
    );

    $this->add_control(
      'show_past_events',
      [
        'label' => __('Show Past Events', 'shaw-events'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Yes', 'shaw-events'),
        'label_off' => __('No', 'shaw-events'),
        'return_value' => 'yes',
        'default' => '',
        'description' => __('By default, only upcoming events are shown', 'shaw-events'),
      ]
    );

    $this->end_controls_section();

    // Style Section - Card
    $this->start_controls_section(
      'card_style_section',
      [
        'label' => __('Card Style', 'shaw-events'),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'card_gap',
      [
        'label' => __('Card Spacing', 'shaw-events'),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'size_units' => ['px'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
          ],
        ],
        'default' => [
          'unit' => 'px',
          'size' => 24,
        ],
        'selectors' => [
          '{{WRAPPER}} .shaw-events-list' => 'gap: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'card_border_radius',
      [
        'label' => __('Border Radius', 'shaw-events'),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'size_units' => ['px'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 30,
          ],
        ],
        'default' => [
          'unit' => 'px',
          'size' => 12,
        ],
        'selectors' => [
          '{{WRAPPER}} .shaw-event-card' => 'border-radius: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    // Style Section - Colors
    $this->start_controls_section(
      'color_style_section',
      [
        'label' => __('Colors', 'shaw-events'),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'primary_color',
      [
        'label' => __('Primary Color', 'shaw-events'),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#1A1448',
        'selectors' => [
          '{{WRAPPER}} .shaw-event-title' => 'color: {{VALUE}};',
          '{{WRAPPER}} .shaw-event-date-badge' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'accent_color',
      [
        'label' => __('Accent Color', 'shaw-events'),
        'type' => \Elementor\Controls_Manager::COLOR,
        'default' => '#E19A58',
        'selectors' => [
          '{{WRAPPER}} .shaw-event-signup-btn' => 'background-color: {{VALUE}};',
          '{{WRAPPER}} .shaw-event-date-badge' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Render widget output on the frontend
   */
  protected function render()
  {
    $settings = $this->get_settings_for_display();

    $today = date('Y-m-d');

    // Build query args for upcoming events
    $query_args = array(
      'post_type' => 'shaw_event',
      'posts_per_page' => $settings['posts_per_page'],
      'post_status' => 'publish',
      'meta_key' => 'event_date',
      'orderby' => 'meta_value',
      'order' => 'ASC',
      'meta_query' => array(
        array(
          'key' => 'event_date',
          'value' => $today,
          'compare' => '>=',
          'type' => 'DATE',
        ),
      ),
    );

    $events_query = new WP_Query($query_args);

    // Query for past events if enabled
    $past_events_query = null;
    if ($settings['show_past_events'] === 'yes') {
      $past_query_args = array(
        'post_type' => 'shaw_event',
        'posts_per_page' => $settings['posts_per_page'],
        'post_status' => 'publish',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value',
        'order' => 'DESC', // Most recent past events first
        'meta_query' => array(
          array(
            'key' => 'event_date',
            'value' => $today,
            'compare' => '<',
            'type' => 'DATE',
          ),
        ),
      );
      $past_events_query = new WP_Query($past_query_args);
    }

    // Helper function to render event card
    $render_event_card = function ($post_id, $is_expired = false) use ($today) {
      $event_date = get_field('event_date', $post_id);
      $start_time = get_field('event_start_time', $post_id);
      $end_time = get_field('event_end_time', $post_id);
      $location = get_field('event_location', $post_id);
      $speaker = get_field('event_speaker', $post_id);
      $language = get_field('event_language', $post_id);
      $event_image = get_field('event_image', $post_id);
      $signup_url = get_field('event_signup_url', $post_id);
      $post_link = get_field('event_post_link', $post_id);

      // Check if post has content
      $post_content = get_post_field('post_content', $post_id);
      $has_content = !empty(trim($post_content));
      $has_custom_link = !empty($post_link);

      // Determine if card should be clickable
      $is_clickable = $has_custom_link || $has_content;
      $card_link = '';
      if ($is_clickable) {
        $card_link = $has_custom_link ? $post_link : get_permalink($post_id);
      }

      // Parse date
      $date_obj = $event_date ? DateTime::createFromFormat('Y-m-d', $event_date) : null;
      $day = $date_obj ? $date_obj->format('d') : '';
      $month = $date_obj ? $date_obj->format('F') : '';
      $year = $date_obj ? $date_obj->format('Y') : '';

      // Format time display
      $time_display = '';
      if ($start_time) {
        $time_display = $start_time;
        if ($end_time) {
          $time_display .= ' - ' . $end_time;
        }
      }

      // Language display
      $language_label = ($language === 'english') ? 'ENGLISH' : 'CHINESE';
      $language_class = ($language === 'english') ? 'english' : 'chinese';

      // Has image
      $has_image = !empty($event_image) && !empty($event_image['url']);

      // Card classes
      $card_classes = 'shaw-event-card';
      $card_classes .= $has_image ? ' has-image' : ' no-image';
      $card_classes .= $is_expired ? ' is-expired' : '';
      $card_classes .= !$is_clickable ? ' no-click' : '';
      ?>
      <article class="<?php echo esc_attr($card_classes); ?>"
               <?php if ($is_clickable): ?>data-link="<?php echo esc_url($card_link); ?>" <?php endif; ?>>

        <!-- Left: Image/Date Section -->
        <div class="shaw-event-image-section">
          <?php if ($has_image): ?>
            <div class="shaw-event-image-wrapper">
              <img src="<?php echo esc_url($event_image['sizes']['large'] ?? $event_image['url']); ?>"
                   alt="<?php echo esc_attr($event_image['alt'] ?? get_the_title()); ?>"
                   data-full-src="<?php echo esc_url($event_image['url']); ?>"
                   class="shaw-event-image shaw-lightbox-trigger">
              <div class="shaw-event-image-overlay"></div>
            </div>
          <?php endif; ?>

          <!-- Date Badge -->
          <div class="shaw-event-date-badge <?php echo $has_image ? 'on-image' : 'standalone'; ?>">
            <span class="date-day"><?php echo esc_html($day); ?></span>
            <span class="date-month"><?php echo esc_html($month); ?></span>
            <span class="date-year"><?php echo esc_html($year); ?></span>
          </div>

          <?php if ($is_expired): ?>
            <div class="shaw-event-expired-badge">Expired</div>
          <?php endif; ?>
        </div>

        <!-- Right: Content Section -->
        <div class="shaw-event-content">
          <!-- Language Tag -->
          <div class="shaw-event-language <?php echo esc_attr($language_class); ?>">
            <?php echo esc_html($language_label); ?>
          </div>

          <!-- Title -->
          <h3 class="shaw-event-title"><?php echo esc_html(get_the_title()); ?></h3>

          <!-- Meta Info -->
          <div class="shaw-event-meta">
            <?php if ($time_display): ?>
              <div class="shaw-event-time">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12,6 12,12 16,14"></polyline>
                </svg>
                <span><?php echo esc_html($time_display); ?></span>
              </div>
            <?php endif; ?>

            <?php if ($location): ?>
              <div class="shaw-event-location">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span><?php echo esc_html($location); ?></span>
              </div>
            <?php endif; ?>

            <?php if ($speaker): ?>
              <div class="shaw-event-speaker">
                <strong>Speaker:</strong> <?php echo esc_html($speaker); ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Sign Up Button (hide for expired events) -->
          <?php if ($signup_url && !$is_expired): ?>
            <a href="<?php echo esc_url($signup_url); ?>"
               class="shaw-event-signup-btn"
               target="_blank"
               rel="noopener noreferrer"
               onclick="event.stopPropagation();">
              SIGN UP
            </a>
          <?php endif; ?>
        </div>

      </article>
      <?php
    };

    ?>
    <div class="shaw-events-widget" data-widget-id="<?php echo $this->get_id(); ?>">

      <?php if ($events_query->have_posts() || ($past_events_query && $past_events_query->have_posts())): ?>
        <div class="shaw-events-list">
          <?php
          // Render upcoming events first
          while ($events_query->have_posts()):
            $events_query->the_post();
            $render_event_card(get_the_ID(), false);
          endwhile;

          // Render past events after
          if ($past_events_query && $past_events_query->have_posts()):
            while ($past_events_query->have_posts()):
              $past_events_query->the_post();
              $render_event_card(get_the_ID(), true);
            endwhile;
          endif;
          ?>
        </div>
      <?php else: ?>
        <div class="shaw-events-empty">
          <p><?php _e('No upcoming events.', 'shaw-events'); ?></p>
        </div>
      <?php endif; ?>

      <?php wp_reset_postdata(); ?>

    </div>

    <!-- Lightbox Modal -->
    <div class="shaw-events-lightbox" style="display: none;">
      <div class="shaw-lightbox-backdrop"></div>
      <div class="shaw-lightbox-content">
        <button class="shaw-lightbox-close">&times;</button>
        <img src="" alt="" class="shaw-lightbox-image">
      </div>
    </div>
    <?php
  }

  /**
   * Render widget output in the editor
   */
  protected function content_template()
  {
    ?>
    <div class="shaw-events-widget">
      <div class="shaw-events-list">
        <!-- Preview Card 1 -->
        <article class="shaw-event-card has-image">
          <div class="shaw-event-image-section">
            <div class="shaw-event-image-wrapper">
              <div style="width: 180px; height: 140px; background: #f0f0f0; border-radius: 8px;"></div>
              <div class="shaw-event-image-overlay"></div>
            </div>
            <div class="shaw-event-date-badge on-image">
              <span class="date-day">06</span>
              <span class="date-month">December</span>
              <span class="date-year">2025</span>
            </div>
          </div>
          <div class="shaw-event-content">
            <div class="shaw-event-language chinese">CHINESE</div>
            <h3 class="shaw-event-title">Sample Event Title</h3>
            <div class="shaw-event-meta">
              <div class="shaw-event-time">🕐 10:30 - 11:30 AM</div>
              <div class="shaw-event-location">📍 308 - 5811 Cooney Rd, Richmond BC</div>
              <div class="shaw-event-speaker"><strong>Speaker:</strong> Cellia Xiao</div>
            </div>
            <a href="#" class="shaw-event-signup-btn">SIGN UP</a>
          </div>
        </article>

        <!-- Preview Card 2 -->
        <article class="shaw-event-card no-image">
          <div class="shaw-event-image-section">
            <div class="shaw-event-date-badge standalone">
              <span class="date-day">15</span>
              <span class="date-month">January</span>
              <span class="date-year">2026</span>
            </div>
          </div>
          <div class="shaw-event-content">
            <div class="shaw-event-language english">ENGLISH</div>
            <h3 class="shaw-event-title">Another Event Without Image</h3>
            <div class="shaw-event-meta">
              <div class="shaw-event-time">🕐 2:00 - 4:00 PM</div>
              <div class="shaw-event-location">📍 Location TBD</div>
            </div>
            <a href="#" class="shaw-event-signup-btn">SIGN UP</a>
          </div>
        </article>
      </div>
    </div>
    <?php
  }
}
