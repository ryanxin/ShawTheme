/**
 * Education Programs Widget - Frontend JavaScript
 */

(function ($) {
    'use strict';

    class ShawEducationProgramsWidget {
        constructor(widgetElement) {
            this.$widget = $(widgetElement);
            this.$grid = this.$widget.find('.shaw-programs-grid');
            this.$loading = this.$widget.find('.shaw-loading-overlay');
            this.$noResults = this.$widget.find('.shaw-no-results');
            this.$pagination = this.$widget.find('.shaw-pagination');

            this.templateId = this.$widget.data('template-id') || 0;
            this.postsPerPage = this.$widget.data('posts-per-page') || 9;
            this.columns = this.$widget.data('columns') || 3;

            const $activeType = this.$widget.find('.shaw-program-type-filter .shaw-filter-tab.active');
            const $activeFocus = this.$widget.find('.shaw-program-focus-filter .shaw-filter-tab.active');

            this.filters = {
                program_type: $activeType.length ? $activeType.data('filter-value') : 'all',
                program_focus: $activeFocus.length ? $activeFocus.data('filter-value') : 'all',
                paged: 1
            };

            this.init();
        }

        init() {
            this.$widget.on('click', '.shaw-filter-tab', (event) => {
                event.preventDefault();
                this.handleFilterClick($(event.currentTarget));
            });

            this.$widget.on('click', '.shaw-page-link', (event) => {
                event.preventDefault();
                this.handlePaginationClick($(event.currentTarget));
            });
        }

        handleFilterClick($tab) {
            if ($tab.hasClass('active')) {
                return;
            }

            $tab.siblings('.shaw-filter-tab').removeClass('active');
            $tab.addClass('active');

            const filterType = $tab.data('filter-type');
            const filterValue = $tab.data('filter-value');

            if (filterType) {
                this.filters[filterType] = filterValue;
                this.filters.paged = 1;
                this.loadPrograms();
            }
        }

        handlePaginationClick($link) {
            const page = parseInt($link.data('page'), 10);

            if (!page || page === this.filters.paged) {
                return;
            }

            this.filters.paged = page;
            this.loadPrograms();
            this.scrollToTop();
        }

        loadPrograms() {
            this.showLoading();

            $.ajax({
                url: shawEducationProgramsWidget.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'shaw_load_education_programs',
                    nonce: shawEducationProgramsWidget.nonce,
                    program_type: this.filters.program_type,
                    program_focus: this.filters.program_focus,
                    template_id: this.templateId,
                    posts_per_page: this.postsPerPage,
                    paged: this.filters.paged
                },
                success: (response) => {
                    this.handleLoadSuccess(response);
                },
                error: () => {
                    this.handleLoadError();
                },
                complete: () => {
                    this.hideLoading();
                }
            });
        }

        handleLoadSuccess(response) {
            if (response.success && response.html) {
                this.$grid.html(response.html).show().attr('data-columns', this.columns);
                this.$noResults.hide();

                if (response.pages > 1) {
                    this.renderPagination(response.pages, response.current_page);
                    this.$pagination.show();
                } else {
                    this.$pagination.hide().empty();
                }

                this.$widget.trigger('shaw:education-programs-loaded', [response]);
                this.animateItems();
                return;
            }

            this.$grid.hide();
            this.$noResults.show();
            this.$pagination.hide().empty();
        }

        handleLoadError() {
            this.$grid.html('<div class="shaw-error">An error occurred. Please try again.</div>').show();
            this.$noResults.hide();
            this.$pagination.hide().empty();
        }

        renderPagination(totalPages, currentPage) {
            let html = '<div class="shaw-pagination-inner">';

            if (currentPage > 1) {
                html += `<a href="#" class="shaw-page-link shaw-page-prev" data-page="${currentPage - 1}" aria-label="Previous page">&larr;</a>`;
            }

            const maxPages = 7;
            let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
            let endPage = Math.min(totalPages, startPage + maxPages - 1);

            if (endPage - startPage < maxPages - 1) {
                startPage = Math.max(1, endPage - maxPages + 1);
            }

            if (startPage > 1) {
                html += '<a href="#" class="shaw-page-link" data-page="1">1</a>';
                if (startPage > 2) {
                    html += '<span class="shaw-page-ellipsis">...</span>';
                }
            }

            for (let page = startPage; page <= endPage; page += 1) {
                const activeClass = page === currentPage ? ' active' : '';
                html += `<a href="#" class="shaw-page-link${activeClass}" data-page="${page}">${page}</a>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<span class="shaw-page-ellipsis">...</span>';
                }
                html += `<a href="#" class="shaw-page-link" data-page="${totalPages}">${totalPages}</a>`;
            }

            if (currentPage < totalPages) {
                html += `<a href="#" class="shaw-page-link shaw-page-next" data-page="${currentPage + 1}" aria-label="Next page">&rarr;</a>`;
            }

            html += '</div>';
            this.$pagination.html(html);
        }

        showLoading() {
            this.$loading.fadeIn(150);
            this.$grid.css('opacity', '0.4');
        }

        hideLoading() {
            this.$loading.fadeOut(150);
            this.$grid.css('opacity', '1');
        }

        animateItems() {
            this.$grid.children().css('opacity', '0').each(function (index) {
                $(this).delay(index * 40).fadeTo(220, 1);
            });
        }

        scrollToTop() {
            const offsetTop = this.$widget.offset().top - 120;
            $('html, body').animate({ scrollTop: offsetTop }, 320);
        }
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/shaw_education_programs.default', function ($scope) {
            const $widget = $scope.find('.shaw-education-widget');
            if ($widget.length) {
                new ShawEducationProgramsWidget($widget[0]);
            }
        });
    });

    $(document).ready(function () {
        $('.shaw-education-widget').each(function () {
            if (!$(this).data('shaw-initialized')) {
                new ShawEducationProgramsWidget(this);
                $(this).data('shaw-initialized', true);
            }
        });
    });
})(jQuery);
