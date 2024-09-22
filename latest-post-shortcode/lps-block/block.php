<?php // phpcs:ignore
/**
 * Latest Post Shortcode Block.
 * Text Domain: lps
 *
 * @package lps
 */
namespace LPS\Block;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '/block-preview.php';

\add_action( 'after_setup_theme', __NAMESPACE__ . '\\theme_support' );
\add_action( 'init', __NAMESPACE__ . '\\block_init' );
\add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_assets' );
\add_action( 'init', __NAMESPACE__ . '\\enqueue_assets' );
\add_action( 'init', __NAMESPACE__ . '\\script_translations', 30 );
\add_filter( 'load_script_translation_file', __NAMESPACE__ . '\\fix_translation_location', 10, 3 );

/**
 * Add theme support.
 */
function theme_support() {
	\add_theme_support( 'wp-block-styles' );
	\add_theme_support( 'align-wide' );
}

/**
 * Register block type.
 */
function block_init(): void {
	\register_block_type_from_metadata( __DIR__ . '/build', [
		'render_callback' => __NAMESPACE__ . '\\render',
	] );
}

/**
 * Server-side render handler.
 *
 * @param  array     $attributes Block attributes.
 * @param  string    $content    Block content.
 * @param  \WP_Block $block      Block.
 * @return string
 */
function render( array $attributes, string $content, \WP_Block $block ): string {
	$instance_id = \wp_unique_id( 'lps-block-' );
	$attributes  = \wp_parse_args( $attributes, [
		'postId'      => ! empty( $block->context['postId'] ) ? (int) $block->context['postId'] : 0,
		'clientId'    => '',
		'nthOfType'   => 0,
		'lpsContent'  => '',
		'constrained' => false,
	] );

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		if ( ! empty( $block->name ) && 'latest-post-shortcode/lps-block' === $block->name ) {
			return maybe_preview( $instance_id, $attributes );
		}
	}

	$is_deprecated_ver = ! empty( $content ) && str_contains( $content, '[latest-selected-content' );

	if ( $is_deprecated_ver ) {
		$wrapper_attributes = \get_block_wrapper_attributes();

		$content = str_replace(
			'[latest-selected-content ',
			'[latest-selected-content lps_instance_id="' . $instance_id . '" ',
			$content
		);

	} else {
		$layout = ! empty( $attributes['constrained'] ) ? ' is-layout-constrained' : '';

		$wrapper_attributes = \get_block_wrapper_attributes( [
			'class' => 'latest-selected-content lps-block' . $layout,
		] );

		if ( ! empty( $content ) ) {
			$check = \wp_strip_all_tags( $content );
			if ( ! empty( $check ) ) {
				$content = '<div class="lps-block__intro">' . $content . '</div>';
			} else {
				$content = '';
			}
		}

		$attributes['lpsContent'] = str_replace(
			'[latest-selected-content ',
			'[latest-selected-content lps_instance_id="' . $instance_id . '" ',
			$attributes['lpsContent']
		);

		// Compute here the content.
		$content .= \do_shortcode( $attributes['lpsContent'] );
	}

	return sprintf( '<div %1$s>%2$s</div>', $wrapper_attributes, $content );
}

/**
 * Registers all block assets so that they can be enqueued through the block
 * editor in the corresponding context.
 */
function enqueue_assets() {
	global $lps_instance;
	if ( empty( $lps_instance ) ) {
		return;
	}

	\LPS\use_style_legacy();
	\LPS\use_script_block_editor();
	\LPS\use_style_block_editor();
}

/**
 * Set script translations.
 */
function script_translations() {
	\wp_set_script_translations( 'lps-block-editor-script', 'lps', LPS_PLUGIN_DIR . '/langs' );
}

/**
 * Fix translation location for the editor.
 *
 * @param  string $file   File.
 * @param  string $handle Handle.
 * @param  string $domain Text domain.
 *
 * @return string
 */
function fix_translation_location( string $file, string $handle, string $domain ): string {
	if ( 'lps' !== $domain ) {
		return $file;
	}

	if ( strpos( $handle, 'lps-block-editor-script' ) !== false ) {
		$file = str_replace( WP_LANG_DIR . '/plugins', LPS_PLUGIN_DIR . '/langs', $file );
	}
	return $file;
}
