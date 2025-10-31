/**
 * ShawGlobal Theme - 自定义 JavaScript
 * 
 * 这个文件包含所有自定义交互脚本
 */

(function($) {
	'use strict';

	/**
	 * 头部滚动效果
	 */
	function initHeaderScroll() {
		const $header = $('.site-header, .elementor-location-header');
		let lastScroll = 0;

		$(window).on('scroll', function() {
			const currentScroll = $(window).scrollTop();
			
			if (currentScroll > 100) {
				$header.addClass('scrolled');
			} else {
				$header.removeClass('scrolled');
			}
			
			lastScroll = currentScroll;
		});
	}

	/**
	 * 返回顶部按钮
	 */
	function initBackToTop() {
		const $backToTop = $('<div class="back-to-top" aria-label="返回顶部">↑</div>');
		$('body').append($backToTop);

		$(window).on('scroll', function() {
			if ($(window).scrollTop() > 300) {
				$backToTop.addClass('show');
			} else {
				$backToTop.removeClass('show');
			}
		});

		$backToTop.on('click', function() {
			$('html, body').animate({
				scrollTop: 0
			}, 600);
		});
	}

	/**
	 * 移动端菜单切换
	 */
	function initMobileMenu() {
		$('.menu-toggle, .mobile-menu-toggle').on('click', function(e) {
			e.preventDefault();
			$(this).toggleClass('active');
			$('.main-navigation, .site-navigation').toggleClass('active');
		});
	}

	/**
	 * 锚点平滑滚动
	 */
	function initSmoothScroll() {
		$('a[href^="#"]').on('click', function(e) {
			const target = $(this.getAttribute('href'));
			
			if (target.length) {
				e.preventDefault();
				$('html, body').animate({
					scrollTop: target.offset().top - 100
				}, 600);
			}
		});
	}

	/**
	 * 项目筛选 URL 参数处理
	 */
	function initProjectFilters() {
		const urlParams = new URLSearchParams(window.location.search);
		const country = urlParams.get('country');
		const category = urlParams.get('category');

		if (country) {
			$('#filter-country').val(country);
		}
		if (category) {
			$('#filter-category').val(category);
		}

		$('#filter-country, #filter-category').on('change', function() {
			const params = new URLSearchParams();
			const selectedCountry = $('#filter-country').val();
			const selectedCategory = $('#filter-category').val();

			if (selectedCountry) {
				params.set('country', selectedCountry);
			}
			if (selectedCategory) {
				params.set('category', selectedCategory);
			}

			window.location.search = params.toString();
		});
	}

	/**
	 * 初始化所有功能
	 */
	function init() {
		initHeaderScroll();
		initBackToTop();
		initMobileMenu();
		initSmoothScroll();
		initProjectFilters();
	}

	// DOM 加载完成后执行
	$(document).ready(function() {
		init();
	});

	// 兼容 Elementor 编辑器
	if (typeof elementorFrontend !== 'undefined') {
		elementorFrontend.hooks.addAction('frontend/element_ready/global', init);
	}

})(jQuery);

