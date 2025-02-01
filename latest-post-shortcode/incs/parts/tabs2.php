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

<div id="tabs-2" class="settings-group">
	<h1>3. <?php esc_html_e( 'Limit & Pagination', 'lps' ); ?></h1>
	<?php require_once __DIR__ . '/tabs2-pagination.php'; ?>
</div>
