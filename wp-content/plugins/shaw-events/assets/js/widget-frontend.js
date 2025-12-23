/**
 * Shaw Events Widget - Frontend JavaScript
 */
(function($) {
    'use strict';

    /**
     * Initialize Event Widget
     */
    function initShawEventsWidget() {
        // Card click handler
        initCardClicks();
        
        // Lightbox functionality
        initLightbox();
    }

    /**
     * Handle card clicks (navigate to event detail link)
     */
    function initCardClicks() {
        $(document).on('click', '.shaw-event-card', function(e) {
            // Don't trigger if clicking on image (for lightbox) or signup button
            if ($(e.target).closest('.shaw-lightbox-trigger').length || 
                $(e.target).closest('.shaw-event-signup-btn').length) {
                return;
            }
            
            var link = $(this).data('link');
            if (link) {
                window.location.href = link;
            }
        });
    }

    /**
     * Lightbox for event images
     */
    function initLightbox() {
        var $lightbox = $('.shaw-events-lightbox');
        
        // Create lightbox if it doesn't exist in DOM
        if ($lightbox.length === 0) {
            $('body').append(
                '<div class="shaw-events-lightbox" style="display: none;">' +
                    '<div class="shaw-lightbox-backdrop"></div>' +
                    '<div class="shaw-lightbox-content">' +
                        '<button class="shaw-lightbox-close">&times;</button>' +
                        '<img src="" alt="" class="shaw-lightbox-image">' +
                    '</div>' +
                '</div>'
            );
            $lightbox = $('.shaw-events-lightbox');
        }
        
        // Open lightbox on image click
        $(document).on('click', '.shaw-lightbox-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var fullSrc = $(this).data('full-src') || $(this).attr('src');
            var alt = $(this).attr('alt') || '';
            
            $lightbox.find('.shaw-lightbox-image').attr('src', fullSrc).attr('alt', alt);
            $lightbox.fadeIn(200);
            
            // Prevent body scroll
            $('body').css('overflow', 'hidden');
        });
        
        // Close lightbox on backdrop click
        $(document).on('click', '.shaw-lightbox-backdrop, .shaw-lightbox-close', function(e) {
            e.preventDefault();
            closeLightbox();
        });
        
        // Close on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $lightbox.is(':visible')) {
                closeLightbox();
            }
        });
        
        function closeLightbox() {
            $lightbox.fadeOut(200, function() {
                $lightbox.find('.shaw-lightbox-image').attr('src', '');
            });
            $('body').css('overflow', '');
        }
    }

    // Initialize on DOM ready
    $(document).ready(function() {
        initShawEventsWidget();
    });

    // Also initialize when Elementor frontend is ready (for live preview)
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/shaw_events.default', function() {
                initShawEventsWidget();
            });
        }
    });

})(jQuery);
