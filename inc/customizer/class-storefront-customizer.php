<?php
/**
 * Storefront Customizer Class
 *
 * @author   WooThemes
 * @package  storefront
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Storefront_Customizer' ) ) :

	/**
	 * The Storefront Customizer class
	 */
	class Storefront_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'customize_preview_init',          array( $this, 'customize_preview_js' ), 10 );
			add_action( 'customize_register',              array( $this, 'customize_register' ), 10 );
			add_filter( 'body_class',                      array( $this, 'layout_class' ) );
			add_action( 'wp_enqueue_scripts',              array( $this, 'add_customizer_css' ), 130 );
			add_action( 'after_setup_theme',               array( $this, 'custom_header_setup' ) );
			add_action( 'customize_controls_print_styles', array( $this, 'customizer_custom_control_css' ) );
			add_action( 'init',                            array( $this, 'default_theme_mod_values' ), 10 );

			add_action( 'after_switch_theme',              array( $this, 'set_storefront_style_theme_mods' ) );
			add_action( 'customize_save_after',            array( $this, 'set_storefront_style_theme_mods' ) );
		}

		/**
		 * Returns an array of the desired default Storefront Options
		 *
		 * @return array
		 */
		public static function get_storefront_default_setting_values() {
			return apply_filters( 'storefront_setting_default_values', $args = array(
				'storefront_heading_color'               => '#484c51',
				'storefront_text_color'                  => '#60646c',
				'storefront_accent_color'                => '#2c2d33',
				'storefront_header_background_color'     => '#2c2d33',
				'storefront_header_text_color'           => '#9aa0a7',
				'storefront_header_link_color'           => '#cccccc',
				'storefront_footer_background_color'     => '#f0f0f0',
				'storefront_footer_heading_color'        => '#494c50',
				'storefront_footer_text_color'           => '#61656b',
				'storefront_footer_link_color'           => '#2c2d33',
				'storefront_button_background_color'     => '#60646c',
				'storefront_button_text_color'           => '#ffffff',
				'storefront_button_alt_background_color' => '#2c2d33',
				'storefront_button_alt_text_color'       => '#ffffff',
				'storefront_layout'                      => 'right',
				'background_color'                       => '#f5f5f5',
			) );
		}

		/**
		 * Adds a value to each Storefront setting if one isn't already present.
		 *
		 * @uses get_storefront_default_setting_values()
		 * @return void
		 */
		public function default_theme_mod_values() {
			foreach ( Storefront_Customizer::get_storefront_default_setting_values() as $mod => $val ) {
				add_filter( 'theme_mod_' . $mod, function( $setting ) use ( $val ) {
					return $setting ? $setting : $val;
				}, 10 );
			}
		}

		/**
		 * Setup the WordPress core custom header feature.
		 *
		 * @uses storefront_header_style()
		 * @uses storefront_admin_header_style()
		 * @uses storefront_admin_header_image()
		 */
		public function custom_header_setup() {
			add_theme_support( 'custom-header', apply_filters( 'storefront_custom_header_args', array(
				'default-image' => '',
				'header-text'   => false,
				'width'         => 1950,
				'height'        => 500,
				'flex-width'    => true,
				'flex-height'   => true,
			) ) );
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer along with several other settings.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since  1.0.0
		 */
		public function customize_register( $wp_customize ) {
			$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
			$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

			// Move background color setting alongside background image.
			$wp_customize->get_control( 'background_color' )->section   = 'background_image';
			$wp_customize->get_control( 'background_color' )->priority  = 20;

			// Change background image section title & priority.
			$wp_customize->get_section( 'background_image' )->title     = __( 'Background', 'storefront' );
			$wp_customize->get_section( 'background_image' )->priority  = 30;

			// Change header image section title & priority.
			$wp_customize->get_section( 'header_image' )->title         = __( 'Header', 'storefront' );
			$wp_customize->get_section( 'header_image' )->priority      = 25;

			/**
			 * Custom controls
			 */
			require_once dirname( __FILE__ ) . '/class-storefront-customizer-control-radio-image.php';
			require_once dirname( __FILE__ ) . '/class-storefront-customizer-control-arbitrary.php';

			if ( apply_filters( 'storefront_customizer_more', true ) ) {
				require_once dirname( __FILE__ ) . '/class-storefront-customizer-control-more.php';
			}

			/**
			 * Add the typography section
		     */
			$wp_customize->add_section( 'storefront_typography' , array(
				'title'      			=> __( 'Typography', 'storefront' ),
				'priority'   			=> 45,
			) );

			/**
			 * Heading color
			 */
			$wp_customize->add_setting( 'storefront_heading_color', array(
				'default'           	=> apply_filters( 'storefront_default_heading_color', '#484c51' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport'				=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_heading_color', array(
				'label'	   				=> __( 'Heading color', 'storefront' ),
				'section'  				=> 'storefront_typography',
				'settings' 				=> 'storefront_heading_color',
				'priority' 				=> 20,
			) ) );

			/**
			 * Text Color
			 */
			$wp_customize->add_setting( 'storefront_text_color', array(
				'default'           	=> apply_filters( 'storefront_default_text_color', '#60646c' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport'				=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_text_color', array(
				'label'					=> __( 'Text color', 'storefront' ),
				'section'				=> 'storefront_typography',
				'settings'				=> 'storefront_text_color',
				'priority'				=> 30,
			) ) );

			/**
			 * Accent Color
			 */
			$wp_customize->add_setting( 'storefront_accent_color', array(
				'default'           	=> apply_filters( 'storefront_default_accent_color', '#2c2d33' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_accent_color', array(
				'label'	   				=> __( 'Link / accent color', 'storefront' ),
				'section'  				=> 'storefront_typography',
				'settings' 				=> 'storefront_accent_color',
				'priority' 				=> 40,
			) ) );

			/**
			 * Logo
			 */
			if ( ! class_exists( 'Jetpack' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_logo_heading', array(
					'section'  			=> 'header_image',
					'type' 				=> 'heading',
					'label'				=> __( 'Logo', 'storefront' ),
					'priority' 			=> 2,
				) ) );

				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_logo_info', array(
					'section'  			=> 'header_image',
					'type' 				=> 'text',
					'description'		=> sprintf( __( 'Looking to add a logo? Install the %sJetpack%s plugin! %sRead more%s.', 'storefront' ), '<a href="https://wordpress.org/plugins/jetpack/">', '</a>', '<a href="http://docs.woothemes.com/document/storefront-faq/#section-1">', '</a>' ),
					'priority' 			=> 3,
				) ) );

				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_logo_divider_after', array(
					'section'  			=> 'header_image',
					'type' 				=> 'divider',
					'priority' 			=> 4,
				) ) );
			}

			$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'storefront_header_image_heading', array(
				'section'  				=> 'header_image',
				'type' 					=> 'heading',
				'label'					=> __( 'Header background image', 'storefront' ),
				'priority' 				=> 6,
			) ) );

			/**
			 * Header Background
			 */
			$wp_customize->add_setting( 'storefront_header_background_color', array(
				'default'           	=> apply_filters( 'storefront_default_header_background_color', '#2c2d33' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport'				=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_header_background_color', array(
				'label'	   				=> __( 'Background color', 'storefront' ),
				'section'  				=> 'header_image',
				'settings' 				=> 'storefront_header_background_color',
				'priority' 				=> 15,
			) ) );

			/**
			 * Header text color
			 */
			$wp_customize->add_setting( 'storefront_header_text_color', array(
				'default'           	=> apply_filters( 'storefront_default_header_text_color', '#9aa0a7' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport'				=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_header_text_color', array(
				'label'	   				=> __( 'Text color', 'storefront' ),
				'section'  				=> 'header_image',
				'settings' 				=> 'storefront_header_text_color',
				'priority' 				=> 20,
			) ) );

			/**
			 * Header link color
			 */
			$wp_customize->add_setting( 'storefront_header_link_color', array(
				'default'           	=> apply_filters( 'storefront_default_header_link_color', '#cccccc' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport'				=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_header_link_color', array(
				'label'	   				=> __( 'Link color', 'storefront' ),
				'section'  				=> 'header_image',
				'settings' 				=> 'storefront_header_link_color',
				'priority' 				=> 30,
			) ) );

			/**
			 * Footer section
			 */
			$wp_customize->add_section( 'storefront_footer' , array(
				'title'      			=> __( 'Footer', 'storefront' ),
				'priority'   			=> 28,
				'description' 			=> __( 'Customise the look & feel of your web site footer.', 'storefront' ),
			) );

			/**
			 * Footer Background
			 */
			$wp_customize->add_setting( 'storefront_footer_background_color', array(
				'default'           	=> apply_filters( 'storefront_default_footer_background_color', '#f0f0f0' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport'				=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_footer_background_color', array(
				'label'	   				=> __( 'Background color', 'storefront' ),
				'section'  				=> 'storefront_footer',
				'settings' 				=> 'storefront_footer_background_color',
				'priority'				=> 10,
			) ) );

			/**
			 * Footer heading color
			 */
			$wp_customize->add_setting( 'storefront_footer_heading_color', array(
				'default'           	=> apply_filters( 'storefront_default_footer_heading_color', '#494c50' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport' 			=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_footer_heading_color', array(
				'label'	   				=> __( 'Heading color', 'storefront' ),
				'section'  				=> 'storefront_footer',
				'settings' 				=> 'storefront_footer_heading_color',
				'priority'				=> 20,
			) ) );

			/**
			 * Footer text color
			 */
			$wp_customize->add_setting( 'storefront_footer_text_color', array(
				'default'           	=> apply_filters( 'storefront_default_footer_text_color', '#61656b' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport'				=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_footer_text_color', array(
				'label'	   				=> __( 'Text color', 'storefront' ),
				'section'  				=> 'storefront_footer',
				'settings' 				=> 'storefront_footer_text_color',
				'priority'				=> 30,
			) ) );

			/**
			 * Footer link color
			 */
			$wp_customize->add_setting( 'storefront_footer_link_color', array(
				'default'           	=> apply_filters( 'storefront_default_footer_link_color', '#2c2d33' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
				'transport'				=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_footer_link_color', array(
				'label'	   				=> __( 'Link color', 'storefront' ),
				'section'  				=> 'storefront_footer',
				'settings' 				=> 'storefront_footer_link_color',
				'priority'				=> 40,
			) ) );

			/**
			 * Buttons section
			 */
			$wp_customize->add_section( 'storefront_buttons' , array(
				'title'      			=> __( 'Buttons', 'storefront' ),
				'priority'   			=> 45,
				'description' 			=> __( 'Customise the look & feel of your web site buttons.', 'storefront' ),
			) );

			/**
			 * Button background color
			 */
			$wp_customize->add_setting( 'storefront_button_background_color', array(
				'default'           	=> apply_filters( 'storefront_default_button_background_color', '#60646c' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_button_background_color', array(
				'label'	   				=> __( 'Background color', 'storefront' ),
				'section'  				=> 'storefront_buttons',
				'settings' 				=> 'storefront_button_background_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Button text color
			 */
			$wp_customize->add_setting( 'storefront_button_text_color', array(
				'default'           	=> apply_filters( 'storefront_default_button_text_color', '#ffffff' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_button_text_color', array(
				'label'	   				=> __( 'Text color', 'storefront' ),
				'section'  				=> 'storefront_buttons',
				'settings' 				=> 'storefront_button_text_color',
				'priority' 				=> 20,
			) ) );

			/**
			 * Button alt background color
			 */
			$wp_customize->add_setting( 'storefront_button_alt_background_color', array(
				'default'           	=> apply_filters( 'storefront_default_button_alt_background_color', '#2c2d33' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_button_alt_background_color', array(
				'label'	   				=> __( 'Alternate button background color', 'storefront' ),
				'section'  				=> 'storefront_buttons',
				'settings' 				=> 'storefront_button_alt_background_color',
				'priority' 				=> 30,
			) ) );

			/**
			 * Button alt text color
			 */
			$wp_customize->add_setting( 'storefront_button_alt_text_color', array(
				'default'           	=> apply_filters( 'storefront_default_button_alt_text_color', '#ffffff' ),
				'sanitize_callback' 	=> 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_button_alt_text_color', array(
				'label'	   				=> __( 'Alternate button text color', 'storefront' ),
				'section'  				=> 'storefront_buttons',
				'settings' 				=> 'storefront_button_alt_text_color',
				'priority' 				=> 40,
			) ) );

			/**
			 * Layout
			 */
			$wp_customize->add_section( 'storefront_layout' , array(
				'title'      			=> __( 'Layout', 'storefront' ),
				'priority'   			=> 50,
			) );

			$wp_customize->add_setting( 'storefront_layout', array(
				'default'    			=> apply_filters( 'storefront_default_layout', $layout = is_rtl() ? 'left' : 'right' ),
				'sanitize_callback' 	=> 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new Storefront_Custom_Radio_Image_Control( $wp_customize, 'storefront_layout', array(
				'settings'				=> 'storefront_layout',
				'section'				=> 'storefront_layout',
				'label'					=> __( 'General Layout', 'storefront' ),
				'priority'				=> 1,
				'choices'				=> array(
											'right' => get_template_directory_uri() . '/assets/images/customizer/controls/2cr.png',
											'left'  => get_template_directory_uri() . '/assets/images/customizer/controls/2cl.png',
				),
			) ) );

			/**
			 * More
			 */
			if ( apply_filters( 'storefront_customizer_more', true ) ) {
				$wp_customize->add_section( 'storefront_more' , array(
					'title'      		=> __( 'More', 'storefront' ),
					'priority'   		=> 999,
				) );

				$wp_customize->add_setting( 'storefront_more', array(
					'default'    		=> null,
					'sanitize_callback' => 'sanitize_text_field',
				) );

				$wp_customize->add_control( new More_Storefront_Control( $wp_customize, 'storefront_more', array(
					'label'    			=> __( 'Looking for more options?', 'storefront' ),
					'section'  			=> 'storefront_more',
					'settings' 			=> 'storefront_more',
					'priority' 			=> 1,
				) ) );
			}
		}

		/**
		 * Get all of the Storefront theme mods.
		 *
		 * @return array $storefront_theme_mods The Storefront Theme Mods.
		 */
		public function get_storefront_theme_mods() {
			$storefront_theme_mods = array(
				'background_color'            => storefront_get_content_background_color(),
				'accent_color'                => get_theme_mod( 'storefront_accent_color' ),
				'header_background_color'     => get_theme_mod( 'storefront_header_background_color' ),
				'header_link_color'           => get_theme_mod( 'storefront_header_link_color' ),
				'header_text_color'           => get_theme_mod( 'storefront_header_text_color' ),
				'footer_background_color'     => get_theme_mod( 'storefront_footer_background_color' ),
				'footer_link_color'           => get_theme_mod( 'storefront_footer_link_color' ),
				'footer_heading_color'        => get_theme_mod( 'storefront_footer_heading_color' ),
				'footer_text_color'           => get_theme_mod( 'storefront_footer_text_color' ),
				'text_color'                  => get_theme_mod( 'storefront_text_color' ),
				'heading_color'               => get_theme_mod( 'storefront_heading_color' ),
				'button_background_color'     => get_theme_mod( 'storefront_button_background_color' ),
				'button_text_color'           => get_theme_mod( 'storefront_button_text_color' ),
				'button_alt_background_color' => get_theme_mod( 'storefront_button_alt_background_color' ),
				'button_alt_text_color'       => get_theme_mod( 'storefront_button_alt_text_color' ),
			);

			return apply_filters( 'storefront_theme_mods', $storefront_theme_mods );
		}

		/**
		 * Get Customizer css.
		 *
		 * @see get_storefront_theme_mods()
		 * @return array $styles the css
		 */
		public function get_css() {
			$storefront_theme_mods = $this->get_storefront_theme_mods();
			$brighten_factor       = apply_filters( 'storefront_brighten_factor', 25 );
			$darken_factor         = apply_filters( 'storefront_darken_factor', -25 );

			$styles                = '
			.main-navigation ul li a,
			.site-title a,
			ul.menu li a,
			.site-branding h1 a,
			.site-footer .storefront-handheld-footer-bar a:not(.button),
			button.menu-toggle,
			button.menu-toggle:hover {
				color: ' . $storefront_theme_mods['header_link_color'] . ';
			}

			button.menu-toggle,
			button.menu-toggle:hover {
				border-color: ' . $storefront_theme_mods['header_link_color'] . ';
			}

			.main-navigation ul li a:hover,
			.main-navigation ul li:hover > a,
			.site-title a:hover,
			a.cart-contents:hover,
			.site-header-cart .widget_shopping_cart a:hover,
			.site-header-cart:hover > li > a,
			ul.menu li.current-menu-item > a {
				color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['header_link_color'], 50 ) . ';
			}

			.site-header,
			.secondary-navigation ul ul,
			.main-navigation ul.menu > li.menu-item-has-children:after,
			.secondary-navigation ul.menu ul,
			.main-navigation ul.menu ul,
			.main-navigation ul.nav-menu ul,
			.storefront-handheld-footer-bar,
			.storefront-handheld-footer-bar ul li > a,
			.storefront-handheld-footer-bar ul li.search .site-search,
			button.menu-toggle,
			button.menu-toggle:hover {
				background-color: ' . $storefront_theme_mods['header_background_color'] . ';
			}

			p.site-description,
			.site-header,
			.storefront-handheld-footer-bar {
				color: ' . $storefront_theme_mods['header_text_color'] . ';
			}

			.storefront-handheld-footer-bar ul li.cart .count,
			button.menu-toggle:after,
			button.menu-toggle:before,
			button.menu-toggle span:before {
				background-color: ' . $storefront_theme_mods['header_link_color'] . ';
			}

			.storefront-handheld-footer-bar ul li.cart .count {
				color: ' . $storefront_theme_mods['header_background_color'] . ';
			}

			.storefront-handheld-footer-bar ul li.cart .count {
				border-color: ' . $storefront_theme_mods['header_background_color'] . ';
			}

			h1, h2, h3, h4, h5, h6 {
				color: ' . $storefront_theme_mods['heading_color'] . ';
			}

			.widget h1 {
				border-bottom-color: ' . $storefront_theme_mods['header_color'] . ';
			}

			body,
			.secondary-navigation a,
			.widget-area .widget a,
			.onsale,
			#comments .comment-list .reply a,
			.pagination .page-numbers li .page-numbers:not(.current), .woocommerce-pagination .page-numbers li .page-numbers:not(.current) {
				color: ' . $storefront_theme_mods['text_color'] . ';
			}

			a  {
				color: ' . $storefront_theme_mods['accent_color'] . ';
			}

			a:focus,
			.button:focus,
			.button.alt:focus,
			.button.added_to_cart:focus,
			.button.wc-forward:focus,
			button:focus,
			input[type="button"]:focus,
			input[type="reset"]:focus,
			input[type="submit"]:focus {
				outline-color: ' . $storefront_theme_mods['accent_color'] . ';
			}

			button, input[type="button"], input[type="reset"], input[type="submit"], .button, .added_to_cart, .widget-area .widget a.button, .site-header-cart .widget_shopping_cart a.button {
				background-color: ' . $storefront_theme_mods['button_background_color'] . ';
				border-color: ' . $storefront_theme_mods['button_background_color'] . ';
				color: ' . $storefront_theme_mods['button_text_color'] . ';
			}

			button:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover, .button:hover, .added_to_cart:hover, .widget-area .widget a.button:hover, .site-header-cart .widget_shopping_cart a.button:hover {
				background-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['button_background_color'], $darken_factor ) . ';
				border-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['button_background_color'], $darken_factor ) . ';
				color: ' . $storefront_theme_mods['button_text_color'] . ';
			}

			button.alt, input[type="button"].alt, input[type="reset"].alt, input[type="submit"].alt, .button.alt, .added_to_cart.alt, .widget-area .widget a.button.alt, .added_to_cart, .pagination .page-numbers li .page-numbers.current, .woocommerce-pagination .page-numbers li .page-numbers.current {
				background-color: ' . $storefront_theme_mods['button_alt_background_color'] . ';
				border-color: ' . $storefront_theme_mods['button_alt_background_color'] . ';
				color: ' . $storefront_theme_mods['button_alt_text_color'] . ';
			}

			button.alt:hover, input[type="button"].alt:hover, input[type="reset"].alt:hover, input[type="submit"].alt:hover, .button.alt:hover, .added_to_cart.alt:hover, .widget-area .widget a.button.alt:hover, .added_to_cart:hover {
				background-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['button_alt_background_color'], $darken_factor ) . ';
				border-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['button_alt_background_color'], $darken_factor ) . ';
				color: ' . $storefront_theme_mods['button_alt_text_color'] . ';
			}

			.site-footer {
				background-color: ' . $storefront_theme_mods['footer_background_color'] . ';
				color: ' . $storefront_theme_mods['footer_text_color'] . ';
			}

			.site-footer a:not(.button) {
				color: ' . $storefront_theme_mods['footer_link_color'] . ';
			}

			.site-footer h1, .site-footer h2, .site-footer h3, .site-footer h4, .site-footer h5, .site-footer h6 {
				color: ' . $storefront_theme_mods['footer_heading_color'] . ';
			}

			#order_review {
				background-color: ' . storefront_get_content_background_color() . ';
			}

			@media screen and ( min-width: 768px ) {
				.main-navigation ul.menu > li > ul {
					border-top-color: ' . $storefront_theme_mods['header_background_color'] . ';
				}

				.secondary-navigation ul.menu a:hover {
					color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['header_text_color'], $brighten_factor ) . ';
				}

				.main-navigation ul.menu ul,
				.main-navigation ul ul {
					background-color: ' . $storefront_theme_mods['header_background_color'] . ';
				}

				.secondary-navigation ul.menu a {
					color: ' . $storefront_theme_mods['text_text_color'] . ';
				}
			}';

			return apply_filters( 'storefront_customizer_css', $styles );
		}

		/**
		 * Get Customizer css associated with WooCommerce.
		 *
		 * @see get_storefront_theme_mods()
		 * @return array $woocommerce_styles the WooCommerce css
		 */
		public function get_woocommerce_css() {
			$storefront_theme_mods = $this->get_storefront_theme_mods();
			$brighten_factor       = apply_filters( 'storefront_brighten_factor', 25 );
			$darken_factor         = apply_filters( 'storefront_darken_factor', -25 );

			$woocommerce_styles    = '
			a.cart-contents,
			.site-header-cart .widget_shopping_cart a {
				color: ' . $storefront_theme_mods['header_link_color'] . ';
			}

			table.cart td.product-remove,
			table.cart td.actions {
				border-top-color: ' . $storefront_theme_mods['background_color'] . ';
			}

			.site-header-cart .widget_shopping_cart {
				background-color: ' . $storefront_theme_mods['header_background_color'] . ';
			}

			.woocommerce-tabs ul.tabs li.active a,
			ul.products li.product .price,
			.onsale,
			.widget_search form:before,
			.widget_product_search form:before {
				color: ' . $storefront_theme_mods['text_color'] . ';
			}

			.onsale {
				border-color: ' . $storefront_theme_mods['text_color'] . ';
			}

			.star-rating span:before,
			.widget-area .widget a:hover,
			.product_list_widget a:hover,
			.quantity .plus, .quantity .minus,
			p.stars a:hover:after,
			p.stars a:after,
			.star-rating span:before {
				color: ' . $storefront_theme_mods['accent_color'] . ';
			}

			.widget_price_filter .ui-slider .ui-slider-range,
			.widget_price_filter .ui-slider .ui-slider-handle {
				background-color: ' . $storefront_theme_mods['accent_color'] . ';
			}

			#order_review_heading, #order_review {
				border-color: ' . $storefront_theme_mods['accent_color'] . ';
			}

			.woocommerce-breadcrumb {
				background-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods['background_color'], 7 ) . ';
			}

			@media screen and ( min-width: 768px ) {
				.site-header-cart .widget_shopping_cart,
				.site-header .product_list_widget li .quantity {
					color: ' . $storefront_theme_mods['header_text_color'] . ';
				}
			}';

			return apply_filters( 'storefront_customizer_woocommerce_css', $woocommerce_styles );
		}

		/**
		 * Assign Storefront styles to individual theme mods.
		 *
		 * @return void
		 */
		public function set_storefront_style_theme_mods() {
			set_theme_mod( 'storefront_styles', $this->get_css() );
			set_theme_mod( 'storefront_woocommerce_styles', $this->get_woocommerce_css() );
		}

		/**
		 * Add CSS in <head> for styles handled by the theme customizer
		 * If the Customizer is active pull in the raw css. Otherwise pull in the prepared theme_mods.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_customizer_css() {
			if ( is_customize_preview() ) {
				wp_add_inline_style( 'storefront-style', $this->get_css() );
				wp_add_inline_style( 'storefront-woocommerce-style', $this->get_woocommerce_css() );
			} else {
				wp_add_inline_style( 'storefront-style', get_theme_mod( 'storefront_styles' ) );
				wp_add_inline_style( 'storefront-woocommerce-style', get_theme_mod( 'storefront_woocommerce_styles' ) );
			}
		}

		/**
		 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 *
		 * @since  1.0.0
		 */
		public function customize_preview_js() {
			wp_enqueue_script( 'storefront-customizer', get_template_directory_uri() . '/assets/js/customizer/customizer.min.js', array( 'customize-preview' ), '1.16', true );
		}

		/**
		 * Layout classes
		 * Adds 'right-sidebar' and 'left-sidebar' classes to the body tag
		 *
		 * @param  array $classes current body classes.
		 * @return string[]          modified body classes
		 * @since  1.0.0
		 */
		public function layout_class( $classes ) {
			$left_or_right = get_theme_mod( 'storefront_layout' );

			$classes[] = $left_or_right . '-sidebar';

			return $classes;
		}

		/**
		 * Add CSS for custom controls
		 *
		 * This function incorporates CSS from the Kirki Customizer Framework
		 *
		 * The Kirki Customizer Framework, Copyright Aristeides Stathopoulos (@aristath),
		 * is licensed under the terms of the GNU GPL, Version 2 (or later)
		 *
		 * @link https://github.com/reduxframework/kirki/
		 * @since  1.5.0
		 */
		public function customizer_custom_control_css() {
			?>
			<style>
			.customize-control-radio-image .image.ui-buttonset input[type=radio] {
				height: auto;
			}

			.customize-control-radio-image .image.ui-buttonset label {
				display: inline-block;
				width: 48%;
				padding: 1%;
				box-sizing: border-box;
			}

			.customize-control-radio-image .image.ui-buttonset label.ui-state-active {
				background: none;
			}

			.customize-control-radio-image .customize-control-radio-buttonset label {
				background: #f7f7f7;
				line-height: 35px;
			}

			.customize-control-radio-image label img {
				opacity: 0.5;
			}

			#customize-controls .customize-control-radio-image label img {
				height: auto;
			}

			.customize-control-radio-image label.ui-state-active img {
				background: #dedede;
				opacity: 1;
			}

			.customize-control-radio-image label.ui-state-hover img {
				opacity: 1;
				box-shadow: 0 0 0 3px #f6f6f6;
			}
			</style>
			<?php
		}
	}

endif;

return new Storefront_Customizer();
