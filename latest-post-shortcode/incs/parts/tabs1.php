<?php
/**
 * Latest Post Shortcode admin output.
 * Text Domain: lps
 *
 * @package lps
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div id="tabs-1" class="settings-group">
	<h1>2. <?php esc_html_e( 'Content & Filters', 'lps' ); ?></h1>
	<?php require_once __DIR__ . '/tabs1-type-filter.php'; ?>
	<?php require_once __DIR__ . '/tabs1-exclude.php'; ?>
</div>
