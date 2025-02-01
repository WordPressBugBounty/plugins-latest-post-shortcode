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

<div id="tabs-4" class="settings-group">
	<h1>5. <?php esc_html_e( 'Extra Options', 'lps' ); ?></h1>

	<div class="settings-block big block-use available-for-tiles">
		<p><?php esc_html_e( 'Please note that if you are using a custom output template defined in your theme, the author, the taxonomies and the tags extra options will not function, since your custom template is overriding the output and the default behavior.', 'lps' ); ?></p>

		<?php
		$ttax   = wp_list_pluck( $the_tax, 'label', 'name' );
		$alltax = [
			'author'            => esc_html__( 'Author', 'lps' ),
			'caption'           => esc_html__( 'Caption', 'lps' ),
			'show_mime'         => esc_html__( 'Mime Type', 'lps' ),
			'price'             => esc_html__( 'Price', 'lps' ),
			'add_to_cart'       => esc_html__( 'Add to cart', 'lps' ),
			'price_add_to_cart' => esc_html__( 'Price + Add to cart', 'lps' ),
		];
		if ( ! empty( $ttax ) ) {
			$alltax = array_merge( $alltax, $ttax );
		}

		$alltax['tags'] = esc_html__( 'Tags', 'lps' );
		?>

		<?php if ( ! empty( $alltax ) ) : ?>
			<?php foreach ( $alltax as $slug => $name ) : ?>
				<?php
				$theslug = ! in_array( $slug, [ 'author', 'caption', 'show_mime', 'price', 'add_to_cart', 'price_add_to_cart' ], true ) ? '(' . $slug . ')' : '';

				$data_attrs = [];
				if ( in_array( $slug, [ 'caption', 'show_mime' ], true ) ) {
					$data_attrs[] = 'data-cond="#lps_post_type"';
					$data_attrs[] = 'data-comp="attachment"';
				}
				if ( in_array( $slug, [ 'price', 'add_to_cart', 'price_add_to_cart' ], true ) ) {
					$data_attrs[] = 'data-cond="#lps_post_type"';
					$data_attrs[] = 'data-comp-in="product,product_variation"';
				}
				?>
				<div id="lps-extra-<?php echo esc_html( $slug ); ?>"
					class="wrap settings-block-item terms-options" <?php echo implode( ' ', $data_attrs ); // phpcs:ignore; ?>>
					<h4>
						<label>
							<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra lps-is-taxonomy">
							<span><?php echo esc_html( $name ); ?></span>
						</label>
					</h4>

					<div id="lps_show_extra_<?php echo esc_attr( $slug ); ?>_pos_wrap" class="wrap indent extra-options-wrap lps-update-blink">
						<?php if ( 'author' === $slug ) : ?>
							<label>
								<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolabel_<?php echo esc_attr( $slug ); ?>" value="nolabel_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
								<b><?php esc_html_e( 'hide the label', 'lps' ); ?></b>
							</label>
							<hr>

							<label>
								<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolink_<?php echo esc_attr( $slug ); ?>" value="nolink_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
								<b><?php esc_html_e( 'no link for the author', 'lps' ); ?></b>
							</label>
							<hr>
						<?php endif; ?>
						<?php if ( 'category' === $slug ) : ?>
							<label>
								<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_hide_uncategorized_<?php echo esc_attr( $slug ); ?>" value="hide_uncategorized_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
								<b><?php esc_html_e( 'do not display Uncategorized term', 'lps' ); ?></b>
							</label>
							<hr>
						<?php endif; ?>
						<?php if ( 'caption' === $slug ) : ?>
							<label>
								<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolabel_<?php echo esc_attr( $slug ); ?>" value="nolabel_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
								<b><?php esc_html_e( 'hide the label', 'lps' ); ?></b>
							</label>
							<hr>
						<?php endif; ?>
						<?php if ( 'show_mime' === $slug ) : ?>
							<label>
								<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolabel_<?php echo esc_attr( $slug ); ?>" value="nolabel_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
								<b><?php esc_html_e( 'hide the label', 'lps' ); ?></b>
							</label>
							<hr>
						<?php endif; ?>
						<?php if ( ! empty( $theslug ) ) : ?>
							<label>
								<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_oneterm_<?php echo esc_attr( $slug ); ?>" value="oneterm_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
								<b><?php esc_html_e( 'show only one term', 'lps' ); ?></b>
							</label>
							<hr>

							<label>
								<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolabel_<?php echo esc_attr( $slug ); ?>" value="nolabel_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
								<b><?php esc_html_e( 'hide the taxonomy name from the list', 'lps' ); ?></b>
							</label>
							<hr>

							<label>
								<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolink_<?php echo esc_attr( $slug ); ?>" value="nolink_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
								<b><?php esc_html_e( 'no link for the terms', 'lps' ); ?></b>
							</label>
							<hr>
						<?php endif; ?>

						<b><?php esc_html_e( 'Position', 'lps' ); ?></b>
						<div class="half">
							<label class="show-options">
								<input type="radio" name="lps_show_extra_pos_<?php echo esc_attr( $slug ); ?>" id="lps_show_extra_taxpos_<?php echo esc_attr( $slug ); ?>_default" value="" checked="checked" onclick="lpsRefresh();" class="lps_show_extra"><?php esc_html_e( 'default', 'lps' ); ?>,
							</label>
							<?php foreach ( self::$tax_positions as $pos => $pos_title ) : ?>
								<label class="show-options">
									<input type="radio" name="lps_show_extra_pos_<?php echo esc_attr( $slug ); ?>" id="lps_show_extra_taxpos_<?php echo esc_attr( $slug ); ?>_<?php echo esc_attr( $pos ); ?>" value="taxpos_<?php echo esc_attr( $slug ); ?>_<?php echo esc_attr( $pos ); ?>" onclick="lpsRefresh();" class="lps_show_extra">
									<?php echo esc_html( $pos_title ); ?>,
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>

			<div class="wrap lps-update-blink" data-cond="#lps_post_type" data-comp="attachment">
				<hr>
				<h4><?php esc_html_e( 'Mime Type', 'lps' ); ?></h4>
				<p><?php esc_html_e( 'The extra options will apply only to attachment post type.', 'lps' ); ?></p>
				<label>
					<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_show_mime_class" value="show_mime_class" onclick="lpsRefresh()" class="lps_show_extra">
					<?php esc_html_e( 'show mime type as CSS class', 'lps' ); ?>
				</label>
			</div>

			<hr>
			<h4><?php esc_html_e( 'Line Break', 'lps' ); ?></h4>
			<p><?php esc_html_e( 'Adding a line break after the shorcode output.', 'lps' ); ?></p>
			<label>
				<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_clearall" value="linebreak" onclick="lpsRefresh()" class="lps_show_extra">
				<?php esc_html_e( 'clear the content below', 'lps' ); ?>
			</label>
		<?php endif; ?>
	</div>

	<div class="settings-block big">
		<h3><?php esc_html_e( 'Cache', 'lps' ); ?></h3>

		<h4><?php esc_html_e( 'Shortcode Cache', 'lps' ); ?></h4>
		<p><?php esc_html_e( 'The cache can help you speed up the page load (the default duration is 30 days). Use the reset button, if you want to clear the cache.', 'lps' ); ?></p>
		<label>
			<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_cache" value="cache" onclick="lpsRefresh()" class="lps_show_extra">
			<?php esc_html_e( 'cache the shortcode result', 'lps' ); ?>
		</label>
	</div>
</div>
