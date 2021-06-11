<?php
/**
 * Sets up consult-page block, does not format frontend
 *
 * @package blocks/consult-page
 **/

namespace PMPro\blocks\consult_page;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

// Only load if Gutenberg is available.
if ( ! function_exists( 'register_block_type' ) ) {
	return;
}

/**
 * Register the dynamic block.
 *
 * @since 2.1.0
 *
 * @return void
 */
function register_dynamic_block() {
	// Hook server side rendering into render callback.
	register_block_type( 'pmpro/consult-page', [
		'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
	] );
}
add_action( 'init', __NAMESPACE__ . '\register_dynamic_block' );

/**
 * Server rendering for consult-page block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	return pmpro_loadTemplate( 'consult', 'local', 'pages' );
}

/**
 * Load preheaders/consult.php if a page has the checkout block.
 */
function load_consult_preheader() {
	if ( has_block( 'pmpro/consult-page' ) ) {
		require_once( PMPRO_DIR . "/preheaders/consult.php" );
	}
}
add_action( 'wp', __NAMESPACE__ . '\load_consult_preheader', 1 );
