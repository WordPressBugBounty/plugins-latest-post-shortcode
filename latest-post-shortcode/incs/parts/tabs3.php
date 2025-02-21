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

<div id="tabs-3" class="settings-group">
	<h1>4. <?php esc_html_e( 'Display Settings', 'lps' ); ?></h1>

	<?php self::output_slider_configuration(); // Introduce the slider extension options. ?>

	<div class="settings-block big">
		<h3><?php esc_html_e( 'Appearance', 'lps' ); ?></h3>

		<div class="wrap block-use available-for-tiles">
			<h4><label for="lps_display"><?php esc_html_e( 'Card Elements', 'lps' ); ?></label></h4>
			<select name="lps_display" id="lps_display" data-default="title" onchange="lpsRefresh()">
				<?php
				foreach ( $display_posts_list as $k => $v ) :
					$key = array_keys( self::$tile_pattern, '[' . $k . ']', true );
					$key = ! empty( $key ) ? reset( $key ) : '';
					?>
					<option value="<?php echo esc_attr( $k ); ?>" data-template-id="<?php echo esc_attr( $key ); ?>" <?php selected( 'title', $k ); ?>><?php echo esc_html( $v ); ?> </option>
				<?php endforeach; ?>
			</select>
		</div>

		<div id="lps_display_titletag" class="wrap">
			<h4>
				<label for="lps_titletag">
				<?php
				// Translators: %s - element name.
				echo esc_html( sprintf( __( 'HTML Tag for the %s', 'lps' ), __( 'title', 'lps' ) ) );
				?>
				</label>
			</h4>
			<p class="lps-update-blink">
				<?php
				// Translators: %s - default value.
				echo esc_html( sprintf( __( 'The default value is %s.', 'lps' ), 'h3' ) );
				?>
			</p>
			<select name="lps_titletag" id="lps_titletag" data-default="h3" onchange="lpsRefresh()">
				<?php foreach ( self::$title_tags as $tt ) : ?>
					<option value="<?php echo esc_attr( $tt ); ?>"><?php echo esc_html( $tt ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div id="lps_display_limit" class="wrap">
			<h4><label for="lps_chrlimit"><?php esc_html_e( 'Chars Limit', 'lps' ); ?></label></h4>
			<p class="lps-update-blink"><?php esc_html_e( 'Maximum number of chars from excerpt / content to be displayed (the text will be truncated, but will not break words).', 'lps' ); ?></p>
			<input type="text" name="lps_chrlimit" id="lps_chrlimit" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="Ex: 120" value="120" size="5">

			<h4><?php esc_html_e( 'Trim type', 'lps' ); ?></h4>
			<p class="lps-update-blink"><?php esc_html_e( 'Apply the chars limit to title and excerpt/content together (the excerpt/content length will be computed by subtracting the title length from the chars limit).', 'lps' ); ?></p>
			<label>
				<input type="checkbox" name="lps_show_extra_trim[]" id="lps_show_extra_trim" value="trim" onclick="lpsRefresh()" class="lps_show_extra">
				<?php esc_html_e( 'limit the title and text together', 'lps' ); ?>
			</label>

			<h4><label for="lps_more"><?php esc_html_e( '\'More\' Suffix', 'lps' ); ?></label></h4>
			<p class="lps-update-blink"><?php esc_html_e( 'The extra chars to be appended at the end of the trimmed strings.', 'lps' ); ?></p>
			<input type="text" name="lps_more" id="lps_more" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="Ex: …" value="">
		</div>

		<div id="lps_display_raw" class="wrap">
			<h4><?php esc_html_e( 'Raw Content', 'lps' ); ?></h4>
			<p class="lps-update-blink"><?php esc_html_e( 'This option is forcing the content output without stripping the markup. This might produce unexpected content layout on the front end, use wisely.', 'lps' ); ?></p>
			<label>
				<input type="checkbox" name="lps_show_extra_raw[]" id="lps_show_extra_raw" value="raw" onclick="lpsRefresh()" class="lps_show_extra">
				<?php esc_html_e( 'show raw content', 'lps' ); ?>
			</label>
		</div>

		<div id="lps_display_date_diff" class="wrap">
			<h4><?php esc_html_e( 'Date Option', 'lps' ); ?></h4>
			<p class="lps-update-blink"><?php esc_html_e( 'This options shows the date in the card (if that is included) as a date difference (ex: 2 hours ago, 1 day ago, etc.).', 'lps' ); ?></p>
			<label>
				<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_date_diff" value="date_diff" onclick="lpsRefresh()" class="lps_show_extra">
				<?php esc_html_e( 'as date difference', 'lps' ); ?>
			</label>
		</div>

		<?php $app_sizes = get_intermediate_image_sizes(); ?>
		<div id="lps_image_wrap" class="wrap">
			<h4><label for="lps_image"><?php esc_html_e( 'Use Image', 'lps' ); ?></label></h4>
			<select name="lps_image" id="lps_image" data-default="" onchange="lpsRefresh()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<?php if ( ! empty( $app_sizes ) ) : ?>
					<?php foreach ( $app_sizes as $as ) : ?>
						<option value="<?php echo esc_attr( $as ); ?>"><?php echo esc_html( $as ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
				<option value="full"><?php esc_html_e( 'full (original size)', 'lps' ); ?></option>
			</select>

			<div id="lps_image_placeholder_wrap" class="wrap lps-update-blink">
				<h4><label for="lps_image_placeholder"><?php esc_html_e( 'Image Placeholder', 'lps' ); ?></label></h4>
				<p><?php esc_html_e( 'Define an image to be used for the posts that do not have a featured image.', 'lps' ); ?> <?php esc_html_e( 'If you specify a list of images separated by comma, a random one from the list will be picked for each article that does not have a featured image.', 'lps' ); ?>
					<?php
					// Translators: %s - string replacer.
					echo esc_html( sprintf( __( 'Insert the word %s, if you want to use the default placeholders.', 'lps' ), '`auto`' ) );
					?>
				</p>
				<input type="text" name="lps_image_placeholder" id="lps_image_placeholder" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
			</div>
		</div>

		<div id="lps_url_wrap" class="wrap">
			<h4><label for="lps_url"><?php esc_html_e( 'Use Post URL', 'lps' ); ?></label></h4>
			<p id="lps_url_options" class="lps-update-blink"><?php esc_html_e( 'See below the available card patterns and select to one you want.', 'lps' ); ?></p>
			<select name="lps_url" id="lps_url" data-default="" onchange="lpsRefresh()">
				<option value=""><?php esc_html_e( 'no link to the post', 'lps' ); ?></option>
				<option value="yes"><?php esc_html_e( 'link to the post', 'lps' ); ?></option>
				<option value="yes_blank">
					<?php esc_html_e( 'link to the post', 'lps' ); ?>
					(<?php esc_html_e( '_blank', 'lps' ); ?>)
				</option>
				<option value="yes_media">
					<?php esc_html_e( 'link to the media file', 'lps' ); ?>
				</option>
				<option value="yes_media_blank">
					<?php esc_html_e( 'link to the media file', 'lps' ); ?>
					(<?php esc_html_e( '_blank', 'lps' ); ?>)
				</option>
				<option value="yes_media_lightbox" disabled>
					<?php esc_html_e( 'link to the media file with lightbox', 'lps' ); ?>
				</option>
			</select>

			<div id="lps_url_options_read" class="wrap">
				<h4><label for="lps_linktext"><?php esc_html_e( 'Custom \'Read more\' message', 'lps' ); ?></label></h4>
				<p class="lps-update-blink"><?php esc_html_e( 'Do not use brackets, these are shortcode delimiters.', 'lps' ); ?></p>
				<input type="text" name="lps_linktext" id="lps_linktext" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_html_e( 'Custom \'Read more\' message', 'lps' ); ?>" size="32">
			</div>

			<div class="wrap block-use available-for-tiles">
				<div id="lps_lightbox_options" class="wrap lps-experimental lps-update-blink">
					<h4><?php esc_html_e( 'Lightbox Attributes', 'lps' ); ?></h4>
					<p><?php esc_html_e( 'If you want to use a lightbox for the images, you can setup below the sub-size and the selector.', 'lps' ); ?></p>

					<h4><?php esc_html_e( 'Lightbox Image', 'lps' ); ?></h4>
					<select name="lps_lightbox_size" id="lps_lightbox_size" data-default="full" onchange="lpsRefresh()">
						<option value="full">
							<?php esc_html_e( 'full (original size)', 'lps' ); ?>
						</option>
						<?php $app_sizes = get_intermediate_image_sizes(); ?>
						<?php if ( ! empty( $app_sizes ) ) : ?>
							<?php foreach ( $app_sizes as $as ) : ?>
								<option value="<?php echo esc_attr( $as ); ?>"><?php echo esc_html( $as ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>

					<h4><?php esc_html_e( 'Selector attribute and value', 'lps' ); ?></h4>
					<input type="text" name="lps_lightbox_attr" id="lps_lightbox_attr" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_html_e( 'Ex: class', 'lps' ); ?>" size="32">
					<input type="text" name="lps_lightbox_val" id="lps_lightbox_val" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_html_e( 'Ex: fancybox image', 'lps' ); ?>" size="32">
					<p><?php esc_html_e( 'This feature has been tested and is recommended to be used with Easy FancyBox plugin (>=1.8) or FooBox Image Lightbox plugin (>=2.6).', 'lps' ); ?></p>
				</div>
			</div>
		</div>

		<div class="wrap block-use available-for-tiles">
			<h4><?php esc_html_e( 'Card Pattern', 'lps' ); ?></h4>
			<p id="tile_description_wrap" class="lps-update-blink"><?php esc_html_e( 'The icons suggest a default order of the HTML tags, the links are marked with red.', 'lps' ); ?></p>
			<p id="custom_tile_description_wrap" class="wrap lps-update-blink"><?php esc_html_e( 'You are using a custom output, the markup is handled programatically, in your custom code.', 'lps' ); ?></p>

			<input type="hidden" name="lps_elements" id="lps_elements" value="0" onchange="lpsRefresh()">
			<div class="half auto">
				<?php
				foreach ( self::$tile_pattern as $k => $p ) :
					$cl  = in_array( $k, self::$tile_pattern_links, true ) ? 'with-link' : 'without-link';
					$cl .= in_array( $k, self::$tile_pattern_ver2, true ) ? ' ver2' : '';
					$cl  = self::tile_markup_is_custom( $p ) ? 'custom-type wide' : $cl;
					?>
					<label class="<?php echo esc_attr( $cl ); ?> lps-update-blink" onclick="LPS_generator.updateElements('<?php echo esc_attr( $k ); ?>');">
						<?php if ( self::tile_markup_is_custom( $p ) ) : ?>
							<input type="radio" name="lps_elements_img" id="lps_elements_img_<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $k ); ?>" readonly="readonly">
							<span><?php echo esc_html( $display_posts_list[ str_replace( ']', '', str_replace( '[', '', $p ) ) ] ); ?> <?php esc_html_e( 'markup', 'lps' ); ?></span>
						<?php else : ?>
							<input type="radio" name="lps_elements_img" id="lps_elements_img_<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $k ); ?>">
							<img src="<?php echo esc_url( LPS_PLUGIN_URL . 'assets/images/tiles/' . esc_attr( $k ) . '.png' ); ?>" title="<?php echo esc_attr( str_replace( '[a-r]', '[a]', $p ) ); ?>">
						<?php endif; ?>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<hr>
		<div id="lps_fallback_wrap" class="wrap">
			<h4><label for="lps_fallback"><?php esc_html_e( 'Content Fallback', 'lps' ); ?></label></h4>
			<p><?php esc_html_e( 'Add a custom text to be displayed if no content matches the settings.', 'lps' ); ?></p>
			<input type="text" name="lps_fallback" id="lps_fallback" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
		</div>
	</div>
</div>
