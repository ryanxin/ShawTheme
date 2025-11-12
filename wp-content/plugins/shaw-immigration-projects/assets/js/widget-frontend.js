/**
 * Immigration Projects Widget - Frontend JavaScript
 *
 * Handles AJAX filtering and pagination for the immigration projects widget
 */

(function($) {
    'use strict';

    class ShawImmigrationWidget {
        constructor(widgetElement) {
            this.$widget = $(widgetElement);
            this.$grid = this.$widget.find('.shaw-projects-grid');
            this.$loading = this.$widget.find('.shaw-loading-overlay');
            this.$noResults = this.$widget.find('.shaw-no-results');
            this.$pagination = this.$widget.find('.shaw-pagination');

            // Get widget settings
            this.templateId = this.$widget.data('template-id') || 0;
            this.postsPerPage = this.$widget.data('posts-per-page') || 9;
            this.columns = this.$widget.data('columns') || 3;

            // Current filter state
            this.filters = {
                country: 'all',
                category: 'all',
                paged: 1
            };

            // Initialize
            this.init();
        }

        init() {
            // Bind filter tab clicks
            this.$widget.on('click', '.shaw-filter-tab', (e) => {
                e.preventDefault();
                this.handleFilterClick($(e.currentTarget));
            });

            // Bind pagination clicks
            this.$widget.on('click', '.shaw-page-link', (e) => {
                e.preventDefault();
                this.handlePaginationClick($(e.currentTarget));
            });

            // Don't load projects on init - they're already server-rendered
            // Just render initial pagination if pagination container is visible
            if (this.$pagination.is(':visible')) {
                // Extract pagination info from widget data if available
                // For now, pagination will be rendered on first filter/page change
            }
        }

        handleFilterClick($tab) {
            // Don't reload if already active
            if ($tab.hasClass('active')) {
                return;
            }

            // Update active state
            $tab.siblings('.shaw-filter-tab').removeClass('active');
            $tab.addClass('active');

            // Get filter type and value
            const filterType = $tab.data('filter-type');
            const filterValue = $tab.data('filter-value');

            // Update filter state
            if (filterType === 'country') {
                this.filters.country = filterValue;
            } else if (filterType === 'category') {
                this.filters.category = filterValue;
            }

            // Reset to page 1 when filter changes
            this.filters.paged = 1;

            // Load projects
            this.loadProjects();
        }

        handlePaginationClick($link) {
            const page = $link.data('page');

            if (!page || page === this.filters.paged) {
                return;
            }

            this.filters.paged = page;
            this.loadProjects();

            // Scroll to top of widget
            this.scrollToTop();
        }

        loadProjects(isInitial = false) {
            // Don't show loading overlay when filtering
            // this.showLoading();

            // Prepare AJAX data
            const data = {
                action: 'shaw_load_projects',
                nonce: shawImmigrationWidget.nonce,
                country: this.filters.country,
                category: this.filters.category,
                template_id: this.templateId,
                posts_per_page: this.postsPerPage,
                paged: this.filters.paged
            };

            // Make AJAX request
            $.ajax({
                url: shawImmigrationWidget.ajaxUrl,
                type: 'POST',
                data: data,
                success: (response) => {
                    this.handleLoadSuccess(response, isInitial);
                },
                error: (xhr, status, error) => {
                    this.handleLoadError(error);
                },
                complete: () => {
                    // this.hideLoading();
                }
            });
        }

        handleLoadSuccess(response, isInitial) {
            if (response.success && response.html) {
                // Update grid with new HTML
                this.$grid.html(response.html);

                // Update grid columns
                this.$grid.attr('data-columns', this.columns);

                // Show grid, hide no results
                this.$grid.show();
                this.$noResults.hide();

                // Update pagination if needed
                if (response.pages > 1) {
                    this.renderPagination(response.pages, response.current_page);
                    this.$pagination.show();
                } else {
                    this.$pagination.hide();
                }

                // Trigger custom event for other scripts
                this.$widget.trigger('shaw:projects-loaded', [response]);

                // Animate items in
                if (!isInitial) {
                    this.animateItems();
                }
            } else {
                // No results
                this.$grid.hide();
                this.$noResults.show();
                this.$pagination.hide();
            }
        }

        handleLoadError(error) {
            console.error('Shaw Immigration Widget: Load error', error);
            this.$grid.html('<div class="shaw-error">An error occurred. Please try again.</div>');
        }

        renderPagination(totalPages, currentPage) {
            let html = '<div class="shaw-pagination-inner">';

            // Previous button
            if (currentPage > 1) {
                html += `<a href="#" class="shaw-page-link shaw-page-prev" data-page="${currentPage - 1}">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>`;
            }

            // Page numbers
            const maxPages = 7; // Maximum page numbers to show
            let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
            let endPage = Math.min(totalPages, startPage + maxPages - 1);

            // Adjust start if we're near the end
            if (endPage - startPage < maxPages - 1) {
                startPage = Math.max(1, endPage - maxPages + 1);
            }

            // First page + ellipsis
            if (startPage > 1) {
                html += `<a href="#" class="shaw-page-link" data-page="1">1</a>`;
                if (startPage > 2) {
                    html += '<span class="shaw-page-ellipsis">...</span>';
                }
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? ' active' : '';
                html += `<a href="#" class="shaw-page-link${activeClass}" data-page="${i}">${i}</a>`;
            }

            // Last page + ellipsis
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<span class="shaw-page-ellipsis">...</span>';
                }
                html += `<a href="#" class="shaw-page-link" data-page="${totalPages}">${totalPages}</a>`;
            }

            // Next button
            if (currentPage < totalPages) {
                html += `<a href="#" class="shaw-page-link shaw-page-next" data-page="${currentPage + 1}">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>`;
            }

            html += '</div>';

            this.$pagination.html(html);
        }

        showLoading() {
            this.$loading.fadeIn(200);
            this.$grid.css('opacity', '0.5');
        }

        hideLoading() {
            this.$loading.fadeOut(200);
            this.$grid.css('opacity', '1');
        }

        animateItems() {
            const $items = this.$grid.children();

            $items.css({
                'opacity': '0',
                'transform': 'translateY(20px)'
            });

            $items.each(function(index) {
                $(this).delay(index * 50).animate({
                    'opacity': 1
                }, 300, function() {
                    $(this).css('transform', 'translateY(0)');
                });
            });
        }

        scrollToTop() {
            const offsetTop = this.$widget.offset().top - 100;

            $('html, body').animate({
                scrollTop: offsetTop
            }, 400);
        }
    }

    // Initialize widgets on page load
    $(window).on('elementor/frontend/init', function() {
        // For Elementor frontend
        elementorFrontend.hooks.addAction('frontend/element_ready/shaw_immigration_projects.default', function($scope) {
            const $widget = $scope.find('.shaw-immigration-widget');
            if ($widget.length) {
                new ShawImmigrationWidget($widget[0]);
            }
        });
    });

    // Also initialize on regular DOM ready (for non-Elementor pages)
    $(document).ready(function() {
        $('.shaw-immigration-widget').each(function() {
            // Check if not already initialized (avoid double init in Elementor)
            if (!$(this).data('shaw-initialized')) {
                new ShawImmigrationWidget(this);
                $(this).data('shaw-initialized', true);
            }
        });
    });

})(jQuery);
