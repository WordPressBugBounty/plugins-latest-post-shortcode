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

<div id="tabs-0" class="settings-group">
	<h1>1. <?php esc_html_e( 'Output Type', 'lps' ); ?></h1>

	<div class="settings-block">
		<h4><label for="lps_ver"><?php esc_html_e( 'Version', 'lps' ); ?></label></h4>
		<select name="lps_ver" id="lps_ver" data-default="2" onchange="lpsRefresh()">
			<option value="" disabled><?php esc_html_e( '1 (deprecated)', 'lps' ); ?></option>
			<option value="2"><?php esc_html_e( '2 (recommended starting with 11.0.0)', 'lps' ); ?></option>
		</select>

		<h4><label for="lps_output"><?php esc_html_e( 'Display as', 'lps' ); ?></label></h4>
		<select name="lps_output" id="lps_output" data-default="" onchange="lpsRefresh()">
			<option value=""><?php esc_html_e( 'grid/list/card', 'lps' ); ?></option>
			<option value="slider"><?php esc_html_e( 'slider', 'lps' ); ?></option>
		</select>
	</div>
</div>
