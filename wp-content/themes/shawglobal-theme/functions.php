<?php
/**
 * Shaw Global Immigration Theme
 * 
 * Theme setup and configuration
 *
 * @package shawglobal-theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme constants
 */
define( 'SHAWGLOBAL_THEME_VERSION', '1.0.0' );
define( 'SHAWGLOBAL_THEME_DIR', get_template_directory() );
define( 'SHAWGLOBAL_THEME_URI', get_template_directory_uri() );

/**
 * Theme Setup
 */
function shawglobal_theme_setup() {
	// Load text domain for translations
	load_theme_textdomain( 'shawglobal-theme', SHAWGLOBAL_THEME_DIR . '/languages' );

	// Enable support for post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Register navigation menus
	register_nav_menus(
		array(
			'primary'   => __( 'Primary Menu', 'shawglobal-theme' ),
			'secondary' => __( 'Secondary Menu', 'shawglobal-theme' ),
			'footer'    => __( 'Footer Menu', 'shawglobal-theme' ),
		)
	);

	// Enable title tag support
	add_theme_support( 'title-tag' );

	// Enable HTML5 support
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		)
	);

	// Enable responsive embeds
	add_theme_support( 'responsive-embeds' );
}
add_action( 'after_setup_theme', 'shawglobal_theme_setup' );

/**
 * Enqueue theme styles and scripts
 */
function shawglobal_theme_enqueue_scripts() {
	// Enqueue main stylesheet (compiled from src/styles)
	$style_path = SHAWGLOBAL_THEME_DIR . '/build/style.css';
	if ( file_exists( $style_path ) ) {
		wp_enqueue_style(
			'shawglobal-main',
			SHAWGLOBAL_THEME_URI . '/build/style.css',
			array(),
			filemtime( $style_path )
		);
	}

	// Enqueue main script (compiled from src/scripts)
	$script_path = SHAWGLOBAL_THEME_DIR . '/build/main.js';
	if ( file_exists( $script_path ) ) {
		wp_enqueue_script(
			'shawglobal-main',
			SHAWGLOBAL_THEME_URI . '/build/main.js',
			array( 'wp-element', 'wp-blocks', 'wp-editor' ),
			filemtime( $script_path ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'shawglobal_theme_enqueue_scripts' );

/**
 * Enqueue editor styles
 */
function shawglobal_theme_enqueue_editor_styles() {
	// Enqueue editor stylesheet
	$editor_path = SHAWGLOBAL_THEME_DIR . '/build/editor.css';
	if ( file_exists( $editor_path ) ) {
		wp_enqueue_style(
			'shawglobal-editor',
			SHAWGLOBAL_THEME_URI . '/build/editor.css',
			array( 'wp-edit-blocks' ),
			filemtime( $editor_path )
		);
	}
}
add_action( 'enqueue_block_editor_assets', 'shawglobal_theme_enqueue_editor_styles' );

/**
 * Excerpt more text
 */
function shawglobal_excerpt_more( $more ) {
	return ' ... <a href="' . get_permalink() . '">' . __( 'Read more', 'shawglobal-theme' ) . '</a>';
}
add_filter( 'excerpt_more', 'shawglobal_excerpt_more' );

/**
 * Register custom image sizes
 */
function shawglobal_theme_add_image_sizes() {
	add_image_size( 'project-thumbnail', 400, 300, true );
	add_image_size( 'project-featured', 800, 500, true );
	add_image_size( 'banner-large', 1920, 600, true );
}
add_action( 'init', 'shawglobal_theme_add_image_sizes' );

/**
 * Custom logo support
 */
function shawglobal_theme_custom_logo() {
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
}
add_action( 'after_setup_theme', 'shawglobal_theme_custom_logo' );

/**
 * Disable comments on pages and posts by default
 */
function shawglobal_theme_disable_comments() {
	// Disable comments
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'page', 'comments' );
}
add_action( 'init', 'shawglobal_theme_disable_comments' );

/**
 * Register blocks
 */
function shawglobal_theme_register_blocks() {
	// Register all blocks in the build/blocks directory
	if ( file_exists( SHAWGLOBAL_THEME_DIR . '/build/blocks' ) ) {
		$blocks = array(
			'project-card',
			'projects-filter',
		);

		foreach ( $blocks as $block ) {
			$block_path = SHAWGLOBAL_THEME_DIR . '/build/blocks/' . $block;
			if ( file_exists( $block_path ) ) {
				register_block_type( $block_path );
			}
		}
	}
}
add_action( 'init', 'shawglobal_theme_register_blocks' );
