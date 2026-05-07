<?php
/**
 * ShawsEdu Theme Functions
 *
 * @package ShawsEdu
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * 定义主题路径常量
 */
define('SHAWSEDU_THEME_VERSION', '1.1.0');
define('SHAWSEDU_THEME_PATH', get_stylesheet_directory());
define('SHAWSEDU_THEME_URL', get_stylesheet_directory_uri());
define('SHAWSEDU_THEME_ASSETS_PATH', SHAWSEDU_THEME_PATH . '/assets/');
define('SHAWSEDU_THEME_ASSETS_URL', SHAWSEDU_THEME_URL . '/assets/');

// Backward-compatible aliases for older snippets in this child theme.
defined('SHAWGLOBAL_THEME_VERSION') || define('SHAWGLOBAL_THEME_VERSION', SHAWSEDU_THEME_VERSION);
defined('SHAWGLOBAL_THEME_PATH') || define('SHAWGLOBAL_THEME_PATH', SHAWSEDU_THEME_PATH);
defined('SHAWGLOBAL_THEME_URL') || define('SHAWGLOBAL_THEME_URL', SHAWSEDU_THEME_URL);
defined('SHAWGLOBAL_THEME_ASSETS_PATH') || define('SHAWGLOBAL_THEME_ASSETS_PATH', SHAWSEDU_THEME_ASSETS_PATH);
defined('SHAWGLOBAL_THEME_ASSETS_URL') || define('SHAWGLOBAL_THEME_ASSETS_URL', SHAWSEDU_THEME_ASSETS_URL);

/**
 * 加载父主题样式
 */
function shawglobal_theme_enqueue_styles()
{
	// 加载父主题样式
	wp_enqueue_style(
		'hello-elementor-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		SHAWGLOBAL_THEME_VERSION
	);

	// 加载主题自定义样式
	wp_enqueue_style(
		'shawsedu-theme-style',
		SHAWSEDU_THEME_ASSETS_URL . 'css/custom.css',
		array('hello-elementor-parent-style'),
		SHAWSEDU_THEME_VERSION
	);
}
add_action('wp_enqueue_scripts', 'shawglobal_theme_enqueue_styles');

/**
 * 加载自定义 JavaScript
 */
function shawglobal_theme_enqueue_scripts()
{
	wp_enqueue_script(
		'shawsedu-theme-script',
		SHAWSEDU_THEME_ASSETS_URL . 'js/custom.js',
		array('jquery'),
		SHAWSEDU_THEME_VERSION,
		true
	);
}
add_action('wp_enqueue_scripts', 'shawglobal_theme_enqueue_scripts');

/**
 * 自动导入 Elementor 模板（从 JSON 文件）
 * 
 * 使用方法：
 * 1. 在 Elementor Theme Builder 中设计并保存模板
 * 2. 在 Elementor > Templates 中导出模板为 JSON
 * 3. 将 JSON 文件放入：/wp-content/themes/shawglobal-theme/templates/
 * 4. 在 functions.php 中注册模板（见下面的示例）
 * 
 * 或者使用 Elementor 的 API 直接导入：
 */
function shawglobal_theme_import_templates()
{
	// 检查 Elementor 是否激活
	if (!did_action('elementor/loaded')) {
		return;
	}

	$templates_dir = SHAWGLOBAL_THEME_PATH . '/templates/';

	if (!is_dir($templates_dir)) {
		return;
	}

	// 获取所有 JSON 模板文件
	$template_files = glob($templates_dir . '*.json');

	if (empty($template_files)) {
		return;
	}

	// 注意：这里只是示例，实际导入逻辑需要更复杂的处理
	// 推荐使用 Elementor 的导入功能手动导入，或使用插件如 "Elementor Templates Import/Export"
	foreach ($template_files as $file) {
		// 可以在这里添加自动导入逻辑
		// 但建议通过 Elementor 后台手动导入
	}
}
// add_action( 'after_setup_theme', 'shawglobal_theme_import_templates' );

/**
 * 注册 Elementor 自定义 Widget（如果需要）
 */
function shawglobal_theme_register_widgets($widgets_manager)
{
	// 在这里可以注册自定义 Elementor Widget
	// 示例：
	// require_once SHAWGLOBAL_THEME_PATH . '/includes/widgets/custom-widget.php';
	// $widgets_manager->register( new \ShawGlobal\Custom_Widget() );
}
// add_action( 'elementor/widgets/register', 'shawglobal_theme_register_widgets' );

/**
 * 注册导航菜单
 */
function shawglobal_theme_register_menus()
{
	register_nav_menus(array(
		'header-menu' => __('头部菜单', 'shawsedu'),
		'footer-menu' => __('底部菜单', 'shawsedu'),
	));
}
add_action('after_setup_theme', 'shawglobal_theme_register_menus');

/**
 * 主题设置
 */
function shawglobal_theme_setup()
{
	// 添加主题支持
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');
	add_theme_support('custom-logo');
	add_theme_support('html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));
}
add_action('after_setup_theme', 'shawglobal_theme_setup');

/**
 * Customize Rank Math Breadcrumbs for Immigration Projects
 * 
 * Replace "Immigration Projects" with "移民项目" in breadcrumbs
 */
function shawglobal_customize_rankmath_breadcrumb($crumbs, $class)
{
	foreach ($crumbs as $key => $crumb) {
		// Check if this breadcrumb item contains "Immigration Projects"
		if (isset($crumb[0]) && $crumb[0] === 'Immigration Projects') {
			$crumbs[$key][0] = '移民项目';
		}
	}
	return $crumbs;
}
add_filter('rank_math/frontend/breadcrumb/items', 'shawglobal_customize_rankmath_breadcrumb', 10, 2);
