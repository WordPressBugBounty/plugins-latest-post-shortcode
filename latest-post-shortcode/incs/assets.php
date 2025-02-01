<?php //phpcs:ignore Generic.Files.LineEndings.InvalidEOLChar
/**
 * Latest Post Shortcode slider output.
 * Text Domain: lps
 *
 * @package lps
 */

declare( strict_types = 1 );
namespace LPS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

\add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\use_script_inline', 0 );
\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\use_script_inline', 0 );
\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\fix_assets_ver', 90 );

/**
 * Returns the assets version to be used.
 */
function ver() {
	return 'lpsv' . LPS_PLUGIN_VERSION . \get_option( 'lps_asset_version', LPS_PLUGIN_VERSION );
}

/**
 * Set the inline variables.
 */
function use_script_inline() {
	if ( ! \wp_script_is( 'lps-vars' ) ) {
		\wp_register_script( 'lps-vars', '', [], ver(), false );
		\wp_enqueue_script( 'lps-vars' );
		\wp_add_inline_script(
			'lps-vars',
			'const lpsSettings = {"ajaxUrl": "' . \esc_url( \admin_url( 'admin-ajax.php' ) ) . '"};'
		);
	}

	$path = LPS_PLUGIN_DIR . 'lps-block/build/view.asset.php';
	if ( file_exists( $path ) && ! \wp_script_is( 'latest-post-shortcode-lps-block-view-script' ) ) {
		\wp_enqueue_script(
			'latest-post-shortcode-lps-block-view-script',
			LPS_PLUGIN_URL . 'lps-block/build/view.js',
			[],
			ver(),
			false
		);
	}
}

/**
 * Load block & shortcode legacy styles (v1), only if the settings require it.
 */
function use_style_legacy() {
	$legacy = \get_option( 'lps-legacy', '' );
	if ( ! \wp_style_is( 'lps-style-legacy' ) && ! empty( $legacy ) ) {
		\wp_enqueue_style( 'lps-style-legacy', LPS_PLUGIN_URL . 'assets/legacy.css', [], ver(), false );
	}
}

/**
 * Enqueue the main styles.
 */
function use_style_main() {
	if ( ! \wp_style_is( 'latest-post-shortcode-lps-block-style' ) ) {
		\wp_enqueue_style( 'latest-post-shortcode-lps-block-style', LPS_PLUGIN_URL . 'lps-block/build/style-view.css', [], ver(), false );
	}
}

/**
 * Enqueue the modal styles.
 */
function use_style_modal() {
	if ( ! \wp_style_is( 'lps-admin-style' ) ) {
		\wp_enqueue_style( 'lps-admin-style', LPS_PLUGIN_URL . 'assets/modal.css', [], ver(), false );
	}
}

/**
 * Enqueue the modal script.
 */
function use_script_modal() {
	if ( ! \wp_script_is( 'lps-admin-shortcode-button' ) ) {
		$lps = \Latest_Post_Shortcode::get_instance();

		\wp_register_script(
			'lps-admin-shortcode-button',
			LPS_PLUGIN_URL . 'assets/modal.js',
			[ 'jquery' ],
			ver(),
			false
		);
		\wp_localize_script(
			'lps-admin-shortcode-button',
			'lpsGenVars',
			[
				'ajaxUrl'     => \admin_url( 'admin-ajax.php' ),
				'icon'        => LPS_PLUGIN_URL . 'assets/images/icon-purple.svg',
				'title'       => \esc_html__( 'Latest Post Shortcode', 'lps' ),
				'outputTypes' => implode( ' ', array_filter( array_keys( $lps::get_card_output_types() ) ) ),
				'allowIcon'   => $lps::allow_icon_for_roles(),
			]
		);
		\wp_enqueue_script( 'lps-admin-shortcode-button' );
	}
}

/**
 * Enqueue the block editor script.
 */
function use_script_block_editor() {
	$path = LPS_PLUGIN_DIR . '/build/index.asset.php';
	if ( file_exists( $path ) && ! \wp_script_is( 'lps-block-editor-script' ) ) {
		$deps = require $path;
		\wp_register_script(
			'lps-block-editor-script',
			LPS_PLUGIN_URL . 'build/index.js',
			$deps['dependencies'],
			ver(),
			false
		);
	}
}

/**
 * Enqueue the block editor style.
 */
function use_style_block_editor() {
	if ( file_exists( LPS_PLUGIN_DIR . '/build/editor.css' ) && ! \wp_style_is( 'lps-block-editor-style' ) ) {
		\wp_register_style( 'lps-block-editor-style', LPS_PLUGIN_URL . 'build/editor.css', [], ver() );
	}
}

/**
 * Enqueue the slider style.
 */
function use_style_slider() {
	if ( ! \wp_style_is( 'lps-slick' ) ) {
		\wp_enqueue_style( 'lps-slick', LPS_PLUGIN_URL . 'assets/slick.css', [], ver(), false );
	}
}

/**
 * Enqueue the slider script.
 */
function use_script_slider() {
	if ( ! \wp_script_is( 'lps-slick' ) ) {
		\wp_enqueue_script( 'lps-slick', LPS_PLUGIN_URL . 'assets/slick.js', [ 'jquery' ], ver(), false );
	}
}

/**
 * Register back the assets with proper version, because core version is not
 * reflecting the blocks versions anymore.
 */
function fix_assets_ver() {
	$styles = [
		'latest-post-shortcode-lps-block-style' => LPS_PLUGIN_URL . 'lps-block/build/style-view.css',
		'lps-slick'                             => LPS_PLUGIN_URL . 'assets/slick.css',
	];

	$scripts = [
		'latest-post-shortcode-lps-block-view-script' => LPS_PLUGIN_URL . 'lps-block/build/view.js',
	];

	foreach ( $styles as $handle => $file ) {
		if ( \wp_style_is( $handle, 'registered' ) ) {
			\wp_deregister_style( $handle );
			\wp_register_style( $handle, $file, [], ver() );
			\wp_enqueue_style( $handle );
		}
	}

	foreach ( $scripts as $handle => $file ) {
		if ( \wp_script_is( $handle, 'registered' ) ) {
			\wp_deregister_script( $handle );
			\wp_register_script( $handle, $file, [], ver(), false );
			\wp_enqueue_script( $handle );
		}
	}
}
