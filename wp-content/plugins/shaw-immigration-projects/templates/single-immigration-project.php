<?php
/**
 * Single Immigration Project Template
 * 
 * This template can be overridden by copying it to yourtheme/shaw-immigration/single-immigration-project.php
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) : the_post();
    $post_id = get_the_ID();
    
    // Get ACF fields
    $processing_period = get_field('processing_period');
    $identity_type = get_field('identity_type');
    $investment_amount = get_field('investment_amount');
    $residential_requirements = get_field('residential_requirements');
    $language = get_field('language');
    $project_overview = get_field('project_overview');
    $project_gallery = get_field('project_gallery');
    $advantages = get_field('advantages');
    $application_requirements = get_field('application_requirements');
    $application_process = get_field('application_process');
    $about_life = get_field('about_life');
    $life_media = get_field('life_media');
    
    // Get taxonomies
    $countries = get_the_terms($post_id, 'project_country');
    $categories = get_the_terms($post_id, 'project_category');
?>

<div class="shaw-single-project-wrapper">
    
    <!-- Hero Section -->
    <div class="shaw-project-hero">
        <div class="shaw-hero-overlay"></div>
        <?php if (has_post_thumbnail()) : ?>
            <div class="shaw-hero-image">
                <?php the_post_thumbnail('full'); ?>
            </div>
        <?php endif; ?>
        <div class="shaw-hero-content">
            <div class="shaw-hero-container">
                <h1 class="shaw-hero-title"><?php the_title(); ?></h1>
                <?php if ($short_desc = get_field('short_description')) : ?>
                    <div class="shaw-hero-desc"><?php echo esc_html($short_desc); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="shaw-project-content-wrapper">
        <div class="shaw-container">
            <div class="shaw-content-row">
                
                <!-- Main Content Column -->
                <div class="shaw-main-content">
                    
                    <!-- Quick Info Card -->
                    <?php if ($processing_period || $identity_type || $investment_amount || $residential_requirements || $language) : ?>
                    <div class="shaw-info-card">
                        <div class="shaw-info-grid">
                            <?php if ($processing_period) : ?>
                                <div class="shaw-info-item">
                                    <span class="info-label">Processing Period:</span>
                                    <span class="info-value"><?php echo esc_html($processing_period); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($identity_type) : ?>
                                <div class="shaw-info-item">
                                    <span class="info-label">Identity Type:</span>
                                    <span class="info-value"><?php echo esc_html($identity_type); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($investment_amount) : ?>
                                <div class="shaw-info-item">
                                    <span class="info-label">Investment Amount:</span>
                                    <span class="info-value"><?php echo esc_html($investment_amount); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($residential_requirements) : ?>
                                <div class="shaw-info-item">
                                    <span class="info-label">Residential Requirements:</span>
                                    <span class="info-value"><?php echo esc_html($residential_requirements); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($language) : ?>
                                <div class="shaw-info-item">
                                    <span class="info-label">Language:</span>
                                    <span class="info-value"><?php echo esc_html($language); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a href="#contact-form" class="shaw-cta-btn">Get a Quote</a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Tab Navigation -->
                    <div class="shaw-detail-tabs">
                        <a href="#overview" class="shaw-detail-tab active">Project Overview</a>
                        <a href="#advantages" class="shaw-detail-tab">Project Advantages</a>
                        <a href="#requirements" class="shaw-detail-tab">Application Requirements</a>
                        <a href="#process" class="shaw-detail-tab">Application Process</a>
                        <a href="#life" class="shaw-detail-tab">About Life</a>
                    </div>
                    
                    <!-- Project Overview -->
                    <?php if ($project_overview) : ?>
                    <div id="overview" class="shaw-content-section">
                        <h2 class="shaw-section-title">Project Overview</h2>
                        <div class="shaw-section-content">
                            <?php echo wp_kses_post($project_overview); ?>
                        </div>
                        
                        <?php if ($project_gallery) : ?>
                        <div class="shaw-gallery">
                            <?php foreach ($project_gallery as $image) : ?>
                                <div class="shaw-gallery-item">
                                    <img src="<?php echo esc_url($image['sizes']['large']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Project Advantages -->
                    <?php if ($advantages) : ?>
                    <div id="advantages" class="shaw-content-section">
                        <h2 class="shaw-section-title">Project Advantages</h2>
                        <div class="shaw-advantages-list">
                            <?php foreach ($advantages as $advantage) : ?>
                                <div class="shaw-advantage-item">
                                    <svg class="shaw-checkmark" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="10" r="10" fill="#E19A58"/>
                                        <path d="M6 10L9 13L14 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span><?php echo esc_html($advantage['advantage_text']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Application Requirements -->
                    <?php if ($application_requirements) : ?>
                    <div id="requirements" class="shaw-content-section">
                        <h2 class="shaw-section-title">Application Requirements</h2>
                        <div class="shaw-section-content">
                            <?php echo wp_kses_post($application_requirements); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Application Process -->
                    <?php if ($application_process) : ?>
                    <div id="process" class="shaw-content-section">
                        <h2 class="shaw-section-title">Application Process</h2>
                        <div class="shaw-section-content">
                            <?php echo wp_kses_post($application_process); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- About Life -->
                    <?php if ($about_life || $life_media) : ?>
                    <div id="life" class="shaw-content-section">
                        <h2 class="shaw-section-title">About Life</h2>
                        <?php if ($about_life) : ?>
                        <div class="shaw-section-content">
                            <?php echo wp_kses_post($about_life); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($life_media) : ?>
                        <div class="shaw-life-media">
                            <?php foreach ($life_media as $media) : ?>
                                <div class="shaw-life-media-item">
                                    <?php if ($media['media_type'] === 'image' && !empty($media['image'])) : ?>
                                        <img src="<?php echo esc_url($media['image']['sizes']['large']); ?>" alt="<?php echo esc_attr($media['title']); ?>" />
                                    <?php elseif ($media['media_type'] === 'video' && !empty($media['video_url'])) : ?>
                                        <video controls>
                                            <source src="<?php echo esc_url($media['video_url']); ?>" type="video/mp4">
                                        </video>
                                    <?php endif; ?>
                                    <?php if (!empty($media['title'])) : ?>
                                        <div class="shaw-media-title"><?php echo esc_html($media['title']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Sidebar -->
                <aside class="shaw-sidebar">
                    
                    <!-- Contact Form -->
                    <div id="contact-form" class="shaw-sidebar-widget shaw-contact-widget">
                        <h3 class="widget-title">Contact us</h3>
                        <p class="widget-desc">Please answer these questions carefully. Your truthful responses are crucial for a successful application.</p>
                        
                        <!-- You can use any form plugin here, like Contact Form 7 or Elementor Form -->
                        <div class="shaw-form-placeholder">
                            <?php 
                            // Example: Contact Form 7
                            // echo do_shortcode('[contact-form-7 id="123"]');
                            
                            // Or use Elementor template
                            // echo do_shortcode('[elementor-template id="456"]');
                            
                            // Placeholder form
                            ?>
                            <form class="shaw-contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                                <input type="hidden" name="action" value="shaw_project_inquiry">
                                <input type="hidden" name="project_id" value="<?php echo esc_attr($post_id); ?>">
                                <?php wp_nonce_field('shaw_project_inquiry', 'shaw_inquiry_nonce'); ?>
                                
                                <div class="form-group">
                                    <label>Full name</label>
                                    <input type="text" name="full_name" placeholder="Your full name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input type="email" name="email" placeholder="Your E-mail" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>What is your primary contact method?</label>
                                    <div class="form-checkboxes">
                                        <label><input type="radio" name="contact_method" value="phone"> Phone</label>
                                        <label><input type="radio" name="contact_method" value="whatsapp"> WhatsApp</label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Please enter your contact number</label>
                                    <input type="tel" name="phone" placeholder="Your contact number">
                                </div>
                                
                                <div class="form-group">
                                    <label>What is your nationality?</label>
                                    <input type="text" name="nationality" placeholder="Your nationality">
                                </div>
                                
                                <div class="form-group">
                                    <label>Please briefly describe your situation</label>
                                    <textarea name="message" rows="6" placeholder="Your message"></textarea>
                                </div>
                                
                                <button type="submit" class="shaw-submit-btn">SUBMIT</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Related Projects -->
                    <?php
                    $related_args = array(
                        'post_type' => 'immigration_project',
                        'posts_per_page' => 4,
                        'post__not_in' => array($post_id),
                        'orderby' => 'rand',
                    );
                    
                    if ($countries && !is_wp_error($countries)) {
                        $country_ids = wp_list_pluck($countries, 'term_id');
                        $related_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'project_country',
                                'field' => 'term_id',
                                'terms' => $country_ids,
                            ),
                        );
                    }
                    
                    $related_query = new WP_Query($related_args);
                    
                    if ($related_query->have_posts()) :
                    ?>
                    <div class="shaw-sidebar-widget shaw-related-widget">
                        <h3 class="widget-title">You might be interested in</h3>
                        <div class="shaw-related-projects">
                            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                <a href="<?php the_permalink(); ?>" class="shaw-related-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="related-image">
                                            <?php the_post_thumbnail('medium'); ?>
                                            <div class="related-overlay"></div>
                                        </div>
                                    <?php endif; ?>
                                    <h4 class="related-title"><?php the_title(); ?></h4>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php
                    endif;
                    wp_reset_postdata();
                    ?>
                    
                </aside>
                
            </div>
        </div>
    </div>
    
</div>

<?php
endwhile;

get_footer();

