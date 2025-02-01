<?php //phpcs:ignore Generic.Files.LineEndings.InvalidEOLChar
/**
 * Latest Post Shortcode deprecated extension.
 * Text Domain: lps
 *
 * @package lps
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Deactivate the extension, it is no longer supported.
if ( function_exists( 'deactivate_plugins' ) ) {
	deactivate_plugins( '/latest-post-shortcode-slider-extension/latest-post-shortcode-slider.php' );
}

// Mention to the user that the extension is not supported anymore.
add_action( 'admin_notices', function () {
	$class   = 'notice notice-error';
	$message = __( 'The Latest Post Shortcode Slider Extension is no longer supported, and it has been deactivated. If you need to display posts as a slider you can find the settings integrated in the Latest Post Shortcode plugin', 'lps' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}, 99 );
