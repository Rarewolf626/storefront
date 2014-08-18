<?php
/**
 * Template functions used for the site header.
 *
 * @package storefront
 */

/**
 * Display header widget region
 * @since  1.0.0
 */
function storefront_header_widget_region() {
	?>
	<div class="header-widget-region">
		<div class="col-full">
			<?php dynamic_sidebar( 'header-1' ); ?>
		</div>
	</div>
	<?php
}

/**
 * Display Site Branding
 * @since  1.0.0
 * @return void
 */
function storefront_site_branding() {
	if ( function_exists( 'has_site_logo' ) && has_site_logo() ) {
		the_site_logo();
	} else {
	?>
		<div class="site-branding">
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<p class="site-description"><?php bloginfo( 'description' ); ?></p>
		</div>
	<?php }
}

/**
 * Display Primary Navigation
 * @since  1.0.0
 * @return void
 */
function storefront_primary_navigation() {
	?>
	<nav id="site-navigation" class="main-navigation" role="navigation">
		<button class="menu-toggle"><?php _e( 'Primary Menu', 'storefront' ); ?></button>
		<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
	</nav><!-- #site-navigation -->
	<?php
}

/**
 * Display Secondary Navigation
 * @since  1.0.0
 * @return void
 */
function storefront_secondary_navigation() {
	?>
	<nav class="secondary-navigation" role="navigation">
		<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'fallback_cb' => '' ) ); ?>
	</nav><!-- #site-navigation -->
	<?php
}