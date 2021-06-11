<?php
/**
 * Sets up refund-page block, does not format frontend
 *
 * @package blocks/refund-page
 **/

namespace PMPro\blocks\refund_page;

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
	register_block_type( 'pmpro/refund-page', [
		'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
	] );
}
add_action( 'init', __NAMESPACE__ . '\register_dynamic_block' );

/**
 * Server rendering for refund-page block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	return pmpro_loadTemplate( 'refund', 'local', 'pages' );
}

/**
 * Load preheaders/refund.php if a page has the checkout block.
 */
function load_refund_preheader() {
	if ( has_block( 'pmpro/refund-page' ) ) {
		require_once( PMPRO_DIR . "/preheaders/refund.php" );
	}
}
add_action( 'wp', __NAMESPACE__ . '\load_refund_preheader', 1 );
