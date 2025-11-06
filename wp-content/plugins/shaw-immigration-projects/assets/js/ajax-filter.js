/**
 * Shaw Immigration Projects - AJAX Filter
 * Handles dual-layer tab filtering without page reload
 */

(function($) {
    'use strict';

    const ShawImmigrationFilter = {
        
        $container: null,
        $countryTabs: null,
        $categoryTabs: null,
        $resultsContainer: null,
        $loadingIndicator: null,
        currentCountry: 'all',
        currentCategory: 'all',
        currentPage: 1,
        isLoading: false,

        init: function() {
            this.$container = $('.shaw-immigration-filter');
            
            if (!this.$container.length) {
                return;
            }

            this.$countryTabs = $('.shaw-country-tabs');
            this.$categoryTabs = $('.shaw-category-tabs');
            this.$resultsContainer = $('.shaw-projects-grid');
            this.$loadingIndicator = $('.shaw-loading');
            
            this.bindEvents();
            // Load initial projects
            this.loadProjects();
        },

        bindEvents: function() {
            const self = this;
            
            // Country tab click
            $(document).on('click', '.shaw-country-tab', function(e) {
                e.preventDefault();
                if (self.isLoading) return;
                
                const country = $(this).data('country');
                
                if (country === self.currentCountry) {
                    return; // Already selected
                }
                
                // Update active state
                self.$countryTabs.find('.shaw-country-tab').removeClass('active');
                $(this).addClass('active');
                
                self.currentCountry = country;
                self.currentPage = 1;
                self.loadProjects();
            });
            
            // Category tab click
            $(document).on('click', '.shaw-category-tab', function(e) {
                e.preventDefault();
                if (self.isLoading) return;
                
                const category = $(this).data('category');
                
                if (category === self.currentCategory) {
                    return; // Already selected
                }
                
                // Update active state
                self.$categoryTabs.find('.shaw-category-tab').removeClass('active');
                $(this).addClass('active');
                
                self.currentCategory = category;
                self.currentPage = 1;
                self.loadProjects();
            });
            
            // Pagination
            $(document).on('click', '.shaw-pagination a', function(e) {
                e.preventDefault();
                if (self.isLoading) return;
                
                const page = $(this).data('page');
                self.currentPage = page;
                self.loadProjects(true);
            });
        },

        loadProjects: function(keepPosition = false) {
            const self = this;
            
            if (this.isLoading) {
                return;
            }
            
            this.isLoading = true;
            this.showLoading();
            
            const params = {
                country: this.currentCountry,
                category: this.currentCategory,
                page: this.currentPage,
                per_page: 9
            };
            
            $.ajax({
                url: shawImmigration.ajaxUrl + 'projects',
                type: 'GET',
                data: params,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', shawImmigration.nonce);
                },
                success: function(response) {
                    self.renderProjects(response);
                    
                    if (!keepPosition) {
                        // Scroll to results
                        $('html, body').animate({
                            scrollTop: self.$resultsContainer.offset().top - 100
                        }, 300);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading projects:', error);
                    self.$resultsContainer.html('<div class="shaw-error">Error loading projects. Please try again.</div>');
                },
                complete: function() {
                    self.isLoading = false;
                    self.hideLoading();
                }
            });
        },

        renderProjects: function(response) {
            const projects = response.projects;
            let html = '';
            
            if (projects.length === 0) {
                html = '<div class="shaw-no-results"><p>No projects found matching your criteria.</p></div>';
            } else {
                html = '<div class="shaw-projects-row">';
                
                projects.forEach(function(project) {
                    const imageUrl = project.card_image ? project.card_image.url : (project.thumbnail || '');
                    const shortDesc = project.short_description || project.excerpt;
                    
                    html += `
                        <div class="shaw-project-card">
                            <div class="shaw-project-card-inner">
                                ${imageUrl ? `<div class="shaw-project-image">
                                    <img src="${imageUrl}" alt="${project.title}" />
                                    <div class="shaw-image-overlay"></div>
                                </div>` : ''}
                                <div class="shaw-project-content">
                                    <h3 class="shaw-project-title">${project.title}</h3>
                                    ${shortDesc ? `<div class="shaw-project-desc">${shortDesc}</div>` : ''}
                                    <div class="shaw-project-meta">
                                        ${project.processing_period ? `<div class="shaw-meta-item">
                                            <span class="meta-label">Processing:</span>
                                            <span class="meta-value">${project.processing_period}</span>
                                        </div>` : ''}
                                        ${project.identity_type ? `<div class="shaw-meta-item">
                                            <span class="meta-label">Identity:</span>
                                            <span class="meta-value">${project.identity_type}</span>
                                        </div>` : ''}
                                    </div>
                                    <a href="${project.permalink}" class="shaw-project-btn">Get a Quote</a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
            }
            
            // Add pagination
            if (response.pages > 1) {
                html += this.renderPagination(response.current_page, response.pages);
            }
            
            this.$resultsContainer.html(html);
        },

        renderPagination: function(currentPage, totalPages) {
            let html = '<div class="shaw-pagination">';
            
            // Previous
            if (currentPage > 1) {
                html += `<a href="#" class="shaw-page-link shaw-prev" data-page="${currentPage - 1}">« Previous</a>`;
            } else {
                html += `<span class="shaw-page-link shaw-prev disabled">« Previous</span>`;
            }
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === currentPage) {
                    html += `<span class="shaw-page-link active">${i}</span>`;
                } else if (
                    i === 1 || 
                    i === totalPages || 
                    (i >= currentPage - 1 && i <= currentPage + 1)
                ) {
                    html += `<a href="#" class="shaw-page-link" data-page="${i}">${i}</a>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<span class="shaw-page-link dots">…</span>`;
                }
            }
            
            // Next
            if (currentPage < totalPages) {
                html += `<a href="#" class="shaw-page-link shaw-next" data-page="${currentPage + 1}">Next »</a>`;
            } else {
                html += `<span class="shaw-page-link shaw-next disabled">Next »</span>`;
            }
            
            html += '</div>';
            
            return html;
        },

        showLoading: function() {
            if (this.$loadingIndicator.length) {
                this.$loadingIndicator.fadeIn(200);
            }
            this.$resultsContainer.css('opacity', '0.5');
        },

        hideLoading: function() {
            if (this.$loadingIndicator.length) {
                this.$loadingIndicator.fadeOut(200);
            }
            this.$resultsContainer.css('opacity', '1');
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        ShawImmigrationFilter.init();
    });

    // Re-initialize on Elementor preview
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function() {
            ShawImmigrationFilter.init();
        });
    });

})(jQuery);

