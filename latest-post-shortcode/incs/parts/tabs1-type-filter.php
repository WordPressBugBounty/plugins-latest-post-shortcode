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
	<h3><?php esc_html_e( 'Post Types, Status & Order', 'lps' ); ?></h3>

	<?php
	if ( is_multisite() ) {
		$sites = self::get_sites();
		if ( ! empty( $sites ) ) :
			?>
			<h4><label for="lps_site_id"><?php esc_html_e( 'Site ID', 'lps' ); ?></label></h4>
			<select name="lps_site_id" id="lps_site_id" data-default="" onchange="lpsRefresh()">
				<option value="">~ <?php esc_html_e( 'current site', 'lps' ); ?>~ </option>
				<?php foreach ( $sites as $k => $v ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php
		endif;
	}
	?>

	<h4><label for="lps_post_type"><?php esc_html_e( 'Type', 'lps' ); ?></label></h4>
	<?php $post_types = self::get_cpts(); ?>
	<select name="lps_post_type" id="lps_post_type" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'any', 'lps' ); ?></option>
		<?php if ( ! empty( $post_types ) ) : ?>
			<?php foreach ( $post_types as $k => $v ) : ?>
				<?php if ( ! in_array( $k, [ 'revision', 'nav_menu_item', 'oembed_cache', 'custom_css', 'customize_changeset', 'user_request', 'wp_block', 'wpcf7_contact_form', 'amp_validated_url', 'scheduled-action', 'shop_order', 'shop_order_refund', 'shop_coupon' ], true ) ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?> (<?php echo esc_attr( $k ); ?>)</option>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>

	<h4><?php esc_html_e( 'Status', 'lps' ); ?></h4>
	<div class="half">
		<?php $st = self::get_statuses(); ?>
		<?php foreach ( $st['public'] as $pu => $pul ) : ?>
			<label>
				<input type="checkbox" name="lps_status[]" id="lps_status_<?php echo esc_attr( $pu ); ?>" value="<?php echo esc_attr( $pu ); ?>" onclick="lpsRefresh()" class="lps_status">
				<b><?php echo esc_html( $pul ); ?> (<?php echo esc_html( $pu ); ?>)</b>
			</label>
		<?php endforeach; ?>
		<?php foreach ( $st['private'] as $pr => $prl ) : ?>
			<label>
				<input type="checkbox" name="lps_status[]" id="lps_status_<?php echo esc_attr( $pr ); ?>" value="<?php echo esc_attr( $pr ); ?>" onclick="lpsRefresh()" class="lps_status">
				<em><?php echo esc_html( $prl ); ?> (<?php echo esc_html( $pr ); ?>)</em>
			</label>
		<?php endforeach; ?>
	</div>

	<h4><label for="lps_show_extra_sticky"><?php esc_html_e( 'Sticky', 'lps' ); ?></label></h4>
	<select name="lps_show_extra[]" id="lps_show_extra_sticky" data-default="" onchange="lpsRefresh()" class="lps_show_extra">
		<option value=""><?php esc_html_e( 'no restriction', 'lps' ); ?></option>
		<option value="sticky"><?php esc_html_e( 'only sticky posts', 'lps' ); ?></option>
		<option value="nosticky"><?php esc_html_e( 'no sticky posts', 'lps' ); ?></option>
	</select>

	<h4><label for="lps_orderby"><?php esc_html_e( 'Order by', 'lps' ); ?></label></h4>
	<p><?php esc_attr_e( 'descending', 'lps' ); ?> = ▼, <?php esc_attr_e( 'ascending', 'lps' ); ?> = ▲</p>
	<select name="lps_orderby" id="lps_orderby" data-default="dateD" onchange="lpsRefresh()">
		<?php foreach ( self::$orderby_options as $k => $v ) : ?>
			<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v['title'] ); ?></option>
		<?php endforeach; ?>
	</select>
	<div class="wrap lps-update-blink" data-cond="#lps_orderby"
		data-comp-in="metaValueA,metaValueD,metaValueNumA,metaValueNumD">
		<p><?php esc_html_e( 'Please note that using this option has some performance risks and you should use it carefully.', 'lps' ); ?>
			<?php esc_html_e( 'The result will only contain posts for which the post meta exists.', 'lps' ); ?></p>
		<div class="row">
			<label for="lps_orderby_meta"><?php esc_html_e( 'metadata key', 'lps' ); ?></label>
			<input type="text" name="lps_orderby_meta" id="lps_orderby_meta" placeholder="<?php esc_attr_e( 'Post meta (ex: _price)', 'lps' ); ?>" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
		</div>
	</div>
	<p class="lps-update-blink" data-cond="#lps_orderby" data-comp="random"><?php esc_html_e( 'Please note that using this option has some performance risks and you should use it carefully.', 'lps' ); ?><span class="block-use available-for-tiles"> <?php esc_html_e( 'Combining random order with pagination will result in unexpected and potentially redundant content being displayed.', 'lps' ); ?></span></p>
</div>

<div class="settings-block">
	<h3>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter By %s', 'lps' ), __( 'Date', 'lps' ) ) );
		?>
	</h3>

	<h4><label for="lps_date_limit"><?php esc_html_e( 'date limit type', 'lps' ); ?></label></h4>
	<select name="lps_date_limit" id="lps_date_limit" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'range', 'lps' ); ?></option>
		<option value="1"><?php esc_html_e( 'dynamic', 'lps' ); ?></option>
	</select>

	<div class="wrap lps-update-blink" data-cond="#lps_date_limit" data-comp="">
		<div class="row">
			<label for="lps_date_after"><?php esc_html_e( 'published after', 'lps' ); ?></label>
			<input type="date" name="lps_date_after" id="lps_date_after" value="" onchange="lpsRefresh()">
		</div>
		<div class="row">
			<label for="lps_date_before"><?php esc_html_e( 'published before', 'lps' ); ?></label>
			<input type="date" name="lps_date_before" id="lps_date_before" value="" onchange="lpsRefresh()">
		</div>
	</div>
	<div class="wrap lps-update-blink" data-cond="#lps_date_limit" data-comp="1">
		<div class="row">
			<label for="lps_date_start"><?php esc_html_e( 'since', 'lps' ); ?></label>
			<input type="number" name="lps_date_start" id="lps_date_start" value="0" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="2">
			<select name="lps_date_start_type" id="lps_date_start_type" data-default="" onchange="lpsRefresh()">
				<?php foreach ( self::$date_limit_units as $k => $v ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?> </option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
</div>

<div class="settings-block lps-update-blink" data-cond="#lps_archive" data-comp="">
	<h3>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter By %s', 'lps' ), __( 'Taxonomy', 'lps' ) ) );
		?>
	</h3>

	<h4><label for="lps_taxonomy"><?php esc_html_e( 'Taxonomy', 'lps' ); ?></label></h4>
	<select name="lps_taxonomy" id="lps_taxonomy" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'any', 'lps' ); ?></option>
		<?php if ( ! empty( $the_tax ) ) : ?>
			<?php foreach ( $the_tax as $k => $v ) : ?>
				<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v->labels->name ); ?> (<?php echo esc_attr( $k ); ?>)</option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>

	<h4><label for="lps_term"><?php esc_html_e( 'Term', 'lps' ); ?></label></h4>
	<p>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter the result by %s.', 'lps' ), __( 'terms', 'lps' ) ) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'slugs', 'lps' ) ) );
		?>
	</p>
	<div class="row">
		<label for="lps_term"><?php esc_attr_e( 'list of terms', 'lps' ); ?></label>
		<input type="text" name="lps_term" id="lps_term" placeholder="<?php esc_attr_e( 'Ex: white,grey', 'lps' ); ?>" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
	</div>
	<label>
		<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_term_strict" value="term_strict" onclick="lpsRefresh()" class="lps_show_extra">
		<?php esc_html_e( 'exclude children', 'lps' ); ?>
	</label>

	<h4><label for="lps_taxonomy2"><?php esc_html_e( 'Taxonomy', 'lps' ); ?> 2</label></h4>
	<select name="lps_taxonomy2" id="lps_taxonomy2" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'any', 'lps' ); ?></option>
		<?php if ( ! empty( $the_tax ) ) : ?>
			<?php foreach ( $the_tax as $k => $v ) : ?>
				<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v->labels->name ); ?> (<?php echo esc_attr( $k ); ?>)</option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>

	<h4><label for="lps_term2"><?php esc_html_e( 'Term', 'lps' ); ?> 2</label></h4>
	<p>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter the result by %s.', 'lps' ), __( 'terms', 'lps' ) ) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'slugs', 'lps' ) ) );
		?>
	</p>
	<div class="row">
		<label for="lps_term2"><?php esc_attr_e( 'list of terms', 'lps' ); ?></label>
		<input type="text" name="lps_term2" id="lps_term2" placeholder="<?php esc_attr_e( 'Ex: white,grey', 'lps' ); ?>" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
	</div>
	<label>
		<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_term2_strict" value="term2_strict" onclick="lpsRefresh()" class="lps_show_extra">
		<?php esc_html_e( 'exclude children', 'lps' ); ?>
	</label>
</div>

<div class="settings-block lps-update-blink" data-cond="#lps_archive" data-comp="">
	<h3>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter By %s', 'lps' ), __( 'Tag', 'lps' ) ) );
		?>
	</h3>
	<h4><label for="lps_dtag"><?php esc_html_e( 'Dynamic', 'lps' ); ?></label></h4>
	<select name="lps_dtag" id="lps_dtag" data-default="" onchange="lpsRefresh()">
		<option value="">
			<?php esc_html_e( 'no', 'lps' ); ?>,
			<?php esc_html_e( 'use the selection', 'lps' ); ?>
		</option>
		<option value="yes">
			<?php esc_html_e( 'yes', 'lps' ); ?>,
			<?php esc_html_e( 'use the current post context', 'lps' ); ?>
		</option>
	</select>
	<div class="wrap lps-update-blink" data-cond="#lps_dtag" data-comp="">
		<h4><?php esc_html_e( 'Tag', 'lps' ); ?></h4>
		<p>
			<?php
			// Translators: %s - type of filtering.
			echo esc_html( sprintf( __( 'Filter the result by %s.', 'lps' ), __( 'tag', 'lps' ) ) );
			?>
			<?php
			// Translators: %s - type of filtering.
			echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'slugs', 'lps' ) ) );
			?>
		</p>
		<div class="row">
			<label for="lps_tag"><?php esc_attr_e( 'list of terms', 'lps' ); ?></label>
			<input type="text" name="lps_tag" id="lps_tag" placeholder="<?php esc_attr_e( 'Ex: white,grey', 'lps' ); ?>" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
		</div>
	</div>
</div>

<div class="settings-block">
	<h3><?php esc_html_e( 'Search & Archive Filter', 'lps' ); ?></h3>
	<h4><label for="lps_archive"><?php esc_html_e( 'As Archive', 'lps' ); ?></label></h4>
	<p class="lps-update-blink" data-cond="#lps_archive" data-comp="yes"><?php esc_html_e( 'This option overrides taxonomies filters and if you use pagination, the number of posts per page is inherited from the site reading settings. The output mimics native archives (categories, tags, etc.) or search results.', 'lps' ); ?>
	</p>
	<select name="lps_archive" id="lps_archive" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
		<option value="yes">
			<?php esc_html_e( 'yes', 'lps' ); ?>,
			<?php esc_html_e( 'use the current search key or taxonomy term', 'lps' ); ?>
		</option>
	</select>

	<div class="wrap lps-update-blink" data-cond="#lps_archive" data-comp="">
		<h4><label for="lps_search"><?php esc_html_e( 'Search Key', 'lps' ); ?></label></h4>
		<p>
			<?php
			// Translators: %s - type of filtering.
			echo esc_html( sprintf( __( 'Filter the result by %s.', 'lps' ), __( 'search key', 'lps' ) ) );
			?>
		</p>
		<input type="text" name="lps_search" id="lps_search" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
	</div>
</div>
