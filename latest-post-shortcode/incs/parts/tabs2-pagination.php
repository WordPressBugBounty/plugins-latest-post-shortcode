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

<div class="settings-block">
	<h3><?php esc_html_e( 'Posts Limit', 'lps' ); ?></h3>

	<h4><label for="lps_limit"><?php esc_html_e( 'Number of Posts', 'lps' ); ?></label></h4>
	<p><?php esc_html_e( 'This is the maximum number of posts the shortcode will expose.', 'lps' ); ?></p>
	<input type="number" name="lps_limit" id="lps_limit" value="" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="5">
</div>

<div class="settings-block">
	<h3><?php esc_html_e( 'Pagination Settings', 'lps' ); ?></h3>

	<div class="wrap block-use available-for-tiles">
		<h4><label for="lps_use_pagination"><?php esc_html_e( 'Pagination', 'lps' ); ?></label></h4>
		<div id="lps_pagination_limit" class="wrap">
			<p class="lps-update-blink"><?php esc_html_e( 'The pagination limits the results to the number specified above. Remove the number of posts value if you do not want to limit the results.', 'lps' ); ?></p>
		</div>
		<select name="lps_use_pagination" id="lps_use_pagination" data-default="" onchange="lpsRefresh()">
			<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
			<option value="yes"><?php esc_html_e( 'yes', 'lps' ); ?></option>
		</select>

		<div class="wrap lps-update-blink" data-cond="#lps_use_pagination" data-comp="yes">
			<h4><label for="lps_per_page"><?php esc_html_e( 'Records Per Page', 'lps' ); ?></label></h4>
			<input type="text" name="lps_per_page" id="lps_per_page" value="0" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="5">

			<h4><label for="lps_offset"><?php esc_html_e( 'Offset', 'lps' ); ?></label></h4>
			<input type="text" name="lps_offset" id="lps_offset" value="0" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="5">

			<h4><label for="lps_showpages"><?php esc_html_e( 'Visibility', 'lps' ); ?></label></h4>
			<select name="lps_showpages" id="lps_showpages" data-default="" onchange="lpsRefresh()">
				<option value=""><?php esc_html_e( 'hide', 'lps' ); ?></option>
				<option value="1">
					<?php echo esc_html( __( 'show', 'lps' ) . ' ' . __( 'prev / next', 'lps' ) ); ?>
				</option>
				<option value="4">
					<?php
					echo esc_html( // Translators: %s - range size.
						__( 'show', 'lps' ) . ' ' . sprintf( __( 'range of %s', 'lps' ), 4 )
					);
					?>
				</option>
				<option value="5">
					<?php
					echo esc_html( // Translators: %s - range size.
						__( 'show', 'lps' ) . ' ' . sprintf( __( 'range of %s', 'lps' ), 5 )
					);
					?>
				</option>
				<option value="10">
					<?php
					echo esc_html( // Translators: %s - range size.
						__( 'show', 'lps' ) . ' ' . sprintf( __( 'range of %s', 'lps' ), 10 )
					);
					?>
				</option>
				<option value="more">
					<?php echo esc_html( __( 'show', 'lps' ) . ' ' . __( '`load more` button', 'lps' ) ); ?>
				</option>
				<option value="scroll">
					<?php echo esc_html( __( 'infinite scroll', 'lps' ) . ' - ' . __( 'load more on scroll', 'lps' ) ); ?>
				</option>
			</select>

			<div class="wrap lps-update-blink" data-cond="#lps_showpages" data-comp-in="more,scroll">
				<h4><label for="lps_loadtext"><?php esc_html_e( '`Load more` Text', 'lps' ); ?></label></h4>
				<p><?php esc_html_e( 'This is the text that will be displayed on the button.', 'lps' ); ?> <?php esc_html_e( 'Do not use brackets, these are shortcode delimiters.', 'lps' ); ?></p>
				<input type="text" name="lps_loadtext" id="lps_loadtext" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_html_e( 'Custom button text', 'lps' ); ?>" value="<?php esc_html_e( 'Load more', 'lps' ); ?>" size="32">
			</div>

			<h4><label for="lps_showpages_pos"><?php esc_html_e( 'Position', 'lps' ); ?></label></h4>
			<select name="lps_showpages_pos" id="lps_showpages_pos" onchange="lpsRefresh()">
				<option value=""><?php esc_html_e( 'above the results', 'lps' ); ?></option>
				<option value="1"><?php esc_html_e( 'below the results', 'lps' ); ?></option>
				<option value="2"><?php esc_html_e( 'above & below the result', 'lps' ); ?></option>
			</select>

			<div class="wrap lps-update-blink" data-cond="#lps_use_pagination" data-comp="yes">
				<h4><label for="lps_style_helper_pags"><?php esc_html_e( 'Alignment', 'lps' ); ?></label></h4>
				<select id="lps_style_helper_pags" onchange="lpsStyleHelper()">
					<option value=""><?php esc_html_e( 'left', 'lps' ); ?></option>
					<option value="pagination-center"><?php esc_html_e( 'center', 'lps' ); ?></option>
					<option value="pagination-right"><?php esc_html_e( 'right', 'lps' ); ?></option>
					<option value="pagination-space-between"><?php esc_html_e( 'space between', 'lps' ); ?></option>
				</select>
			</div>

			<h4><label for="lps_show_extra_spinner"><?php esc_html_e( 'AJAX', 'lps' ); ?></label></h4>
			<div class="row">
				<label>
					<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_ajax_pagination" value="ajax_pagination" onclick="lpsRefresh()" class="lps_show_extra">
					<?php esc_html_e( 'yes', 'lps' ); ?>
				</label>
				<select name="lps_show_extra[]" id="lps_show_extra_spinner" data-default="" onchange="lpsRefresh()" class="lps_show_extra">
					<option value=""><?php esc_html_e( 'no spinner', 'lps' ); ?></option>
					<option value="light_spinner"><?php esc_html_e( 'light spinner', 'lps' ); ?></option>
					<option value="dark_spinner"><?php esc_html_e( 'dark spinner', 'lps' ); ?></option>
				</select>
			</div>

			<h4><?php esc_html_e( 'Elements Style', 'lps' ); ?></h4>
			<p><?php esc_html_e( 'Check this option if you want to show pagination elements all the time, including disabled elements such as going to the: first page, previous page, next page, last page.', 'lps' ); ?></p>
			<label>
				<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_pagination_all" value="pagination_all" onclick="lpsRefresh()" class="lps_show_extra">
				<?php esc_html_e( 'all pagination elements', 'lps' ); ?>
			</label>

			<h4><label for="lps_total_text"><?php esc_html_e( 'Total', 'lps' ); ?></label></h4>
			<label>
				<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_show_total" value="show_total" onclick="lpsRefresh()" class="lps_show_extra">
				<?php esc_html_e( 'show total items', 'lps' ); ?>
			</label>

			<div class="wrap lps-update-blink" data-cond="#lps_show_extra_show_total" data-comp-checked="true">
				<p>
					<?php
					/* Translators: %s - the placeholder string. */
					echo esc_html( sprintf( __( 'Write the text for the total items. Place %s where the total value should appear.', 'lps' ), '`%d`' ) );
					?>
					<?php esc_html_e( 'Leave empty if you want to use the default values.', 'lps' ); ?>
				</p>
				<input type="text" name="lps_total_text" id="lps_total_text" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php /* Translators: %d - total value. */ esc_html_e( 'Custom `Total items: %d` text', 'lps' ); ?>" value="<?php esc_html_e( 'Total items: %d', 'lps' ); ?>" size="32">
			</div>
		</div>
	</div>

	<div class="wrap block-use available-for-slider">
		<p class="lps-update-blink"><?php esc_html_e( 'The pagination is not available for the slider output type.', 'lps' ); ?></p>
	</div>
</div>
