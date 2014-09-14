<?php
/**
 * storefront setup functions
 *
 * @package storefront
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

/**
 * Assign the Storefront version to a var
 */
$theme 					= wp_get_theme();
$storefront_version 	= $theme['Version'];

/**
 * Declare support for the storefront customizer settings
 * Remove via child theme using remove_theme_support()
 */
add_theme_support( 'storefront-customizer-settings' );

if ( ! function_exists( 'storefront_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function storefront_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on storefront, use a find and replace
	 * to change 'storefront' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'storefront', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' 	=> __( 'Primary Menu', 'storefront' ),
		'secondary' => __( 'Secondary Menu', 'storefront' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'storefront_custom_background_args', array(
		'default-color' => apply_filters( 'storefront_default_background_color', 'fcfcfc' ),
		'default-image' => '',
	) ) );

	// Add support for the Site Logo plugin
	// https://github.com/automattic/site-logo
	add_theme_support( 'site-logo', array( 'size' => 'full' ) );

	// Declare WooCommerce support
	add_theme_support( 'woocommerce' );
}
endif; // storefront_setup

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function storefront_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'storefront' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => __( 'Header', 'storefront' ),
		'id'            => 'header-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	$footer_widget_regions = apply_filters( 'storefront_footer_widget_regions', 4 );

	for ( $i = 1; $i <= intval( $footer_widget_regions ); $i++ ) {
		register_sidebar( array(
			'name' 				=> sprintf( __( 'Footer %d', 'storefront' ), $i ),
			'id' 				=> sprintf( 'footer-%d', $i ),
			'description' 		=> sprintf( __( 'Widgetized Footer Region %d.', 'storefront' ), $i ),
			'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside>',
			'before_title' 		=> '<h3>',
			'after_title' 		=> '</h3>'
			)
		);
	}
}

/**
 * Enqueue scripts and styles.
 * @since  1.0.0
 */
function storefront_scripts() {
	global $storefront_version;

	wp_enqueue_style( 'storefront-style', get_stylesheet_uri(), '', $storefront_version );

	wp_enqueue_script( 'storefront-navigation', get_template_directory_uri() . '/js/navigation.min.js', array(), '20120206', true );

	wp_enqueue_script( 'storefront-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.min.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}