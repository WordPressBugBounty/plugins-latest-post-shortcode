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

<div id="tabs-5" class="settings-group">
	<h1>6. <?php esc_html_e( 'Style', 'lps' ); ?></h1>

	<div class="settings-block big">
		<h3><?php esc_html_e( 'Style', 'lps' ); ?></h3>
		<p class="block-use available-for-tiles"><?php esc_html_e( 'The plugin supports layouts with 1 to 6 columns. The available card types are: vertical, horizontal (image + info, info + image), overlay. If you use programmatic hooks to register custom card types, these will also appear below, in the dropdown.', 'lps' ); ?></p>

		<h4><label for="lps_css"><?php esc_html_e( 'CSS Class', 'lps' ); ?></label></h4>
		<p><?php esc_html_e( 'The appearance of the shortcode output can be customized with the CSS class.', 'lps' ); ?></p>
		<input type="text" name="lps_css" id="lps_css" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="32">
		<label>
			<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_reset_css" value="reset_css" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'keep only the core CSS classes when outputting the cards, and remove the third-party ones (added through `post_class` filter)', 'lps' ); ?>
		</label>

		<div class="wrap block-use available-for-tiles">
			<hr class="spacer">
			<h4><label for="lps_style_helper_columns"><?php esc_html_e( 'Columns', 'lps' ); ?></label></h4>
			<select id="lps_style_helper_columns" data-default="" onchange="lpsStyleHelper()">
				<option value="one-column">1</option>
				<option value="two-columns">2</option>
				<option value="three-columns">3</option>
				<option value="four-columns">4</option>
				<option value="five-columns">5</option>
				<option value="six-columns">6</option>
			</select>

			<?php $card_styles = self::get_card_output_types(); ?>
			<h4><label for="lps_style_helper_overlay"><?php esc_html_e( 'Card Type', 'lps' ); ?></label></h4>
			<select id="lps_style_helper_overlay" data-default="" onchange="lpsStyleHelper()">
				<?php
				if ( ! empty( $card_styles ) ) {
					foreach ( $card_styles as $ss => $nn ) {
						?>
						<option value="<?php echo esc_attr( sanitize_title( $ss ) ); ?>"><?php echo esc_html( $nn ); ?></option>
						<?php
					}
				}
				?>
			</select>

			<hr class="spacer">
			<h4><?php esc_html_e( 'Scroller', 'lps' ); ?></h4>
			<p><?php esc_html_e( 'This option enables the modern snap to grid inline scroller output.', 'lps' ); ?></p>
			<label>
				<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_scroller" value="scroller" onclick="lpsRefresh()" class="lps_show_extra">
				<?php esc_html_e( 'show as inline scroller', 'lps' ); ?>
			</label>

			<div class="wrap indent lps-update-blink" data-cond="#lps_show_extra_scroller" data-comp-checked="true">
				<div class="lps-experimental wrap">
					<p><?php esc_html_e( 'This option displays a small dynamic counter on top of the card.', 'lps' ); ?></p>
					<label>
						<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_with_counter" value="with_counter" onclick="lpsRefresh()" class="lps_show_extra">
						<?php esc_html_e( 'show counter on the card', 'lps' ); ?>
					</label>
					<div class="wrap" data-cond="#lps_show_extra_with_counter" data-comp-checked="true">
						<hr>
						<label>
							<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_reverse_counter" value="reverse_counter" onclick="lpsRefresh()" class="lps_show_extra">
							<?php esc_html_e( 'use reverse order for the counter', 'lps' ); ?>
						</label>
					</div>
				</div>

				<p><?php esc_html_e( 'This option hides the button when using infinite scroll pagination.', 'lps' ); ?></p>
				<label>
					<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_hide_more" value="hide_more" onclick="lpsRefresh()" class="lps_show_extra">
					<?php esc_html_e( 'hide the `load more` button', 'lps' ); ?>
				</label>
			</div>
		</div>
	</div>

	<div class="settings-block wrap block-use available-for-tiles">
		<h3><?php esc_html_e( 'Card Aspect', 'lps' ); ?></h3>
		<p><?php esc_html_e( 'Use the options below to configure content alignment, colors, spacing, and other settings.', 'lps' ); ?></p>
		<div class="row">
			<label for="lps_style_helper_align"><?php esc_html_e( 'horizontal alignment', 'lps' ); ?></label>
			<select id="lps_style_helper_align" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'left', 'lps' ); ?></option>
				<option value="align-center"><?php esc_html_e( 'center', 'lps' ); ?></option>
				<option value="align-right"><?php esc_html_e( 'right', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row">
			<label for="lps_style_helper_valign"><?php esc_html_e( 'vertical alignment', 'lps' ); ?></label>
			<select id="lps_style_helper_valign" data-default="" onchange="lpsStyleHelper()">
				<option value="content-center"><?php esc_html_e( 'center', 'lps' ); ?></option>
				<option value="content-start"><?php esc_html_e( 'start', 'lps' ); ?></option>
				<option value="content-end"><?php esc_html_e( 'end', 'lps' ); ?></option>
				<option value="content-space-between"><?php esc_html_e( 'space between', 'lps' ); ?></option>
				<option value="content-auto"><?php esc_html_e( 'auto', 'lps' ); ?></option>
				<option value="content-first-top"><?php esc_html_e( 'first top', 'lps' ); ?></option>
				<option value="content-last-bottom"><?php esc_html_e( 'last bottom', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row lps-update-blink" data-cond="#lps_style_helper_overlay" data-comp="as-overlay">
			<label for="lps_card_ratio"><?php esc_html_e( 'aspect ratio', 'lps' ); ?></label>
			<select id="lps_card_ratio" onchange="lpsRefresh();">
				<option value=""><?php esc_html_e( 'auto', 'lps' ); ?></option>
				<option value="1">1:1 (<?php esc_html_e( 'square', 'lps' ); ?>)</option>
				<optgroup label="<?php esc_html_e( 'landscape', 'lps' ); ?>">
					<option value="16/9">16:9</option>
					<option value="4/3">4:3</option>
					<option value="3/2">3:2</option>
				</optgroup>
				<optgroup label="<?php esc_html_e( 'portrait', 'lps' ); ?>">
					<option value="5/9">5:9</option>
					<option value="4/5">4:5</option>
				</optgroup>
			</select>
		</div>
		<div class="row">
			<label for="lps_style_has_radius"><?php esc_html_e( 'border radius', 'lps' ); ?></label>
			<select id="lps_style_has_radius" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="has-radius"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row">
			<label for="lps_style_has_shadow"><?php esc_html_e( 'shadow', 'lps' ); ?></label>
			<select id="lps_style_has_shadow" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="has-shadow"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row">
			<label for="lps_style_has_highlight"><?php esc_html_e( 'highlight', 'lps' ); ?> <span class="comment">(<?php esc_html_e( 'hover effect', 'lps' ); ?>)</span></label>
			<select id="lps_style_has_highlight" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="hover-highlight"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row lps-update-blink" data-cond="#lps_color_bg" data-comp="">
			<label for="lps_style_has_aspect"><?php esc_html_e( 'default mood', 'lps' ); ?></label>
			<select id="lps_style_has_aspect" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( '-- unspecified --', 'lps' ); ?></option>
				<option value="dark"><?php esc_html_e( 'dark', 'lps' ); ?></option>
				<option value="light"><?php esc_html_e( 'light', 'lps' ); ?></option>
				<option id="lps-option-clear-image" value="clear-image">
					<?php esc_html_e( 'no overlay', 'lps' ); ?>
				</option>
			</select>
		</div>
		<div class="row lps-update-blink" data-cond="#lps_style_helper_overlay" data-comp="as-overlay">
			<label for="lps_style_has_tall"><?php esc_html_e( 'make the card taller', 'lps' ); ?></label>
			<select id="lps_style_has_tall" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="tall"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>


		<div class="wrap lps-update-blink" data-cond="#lps_style_helper_overlay" data-comp-in="h-image-info,h-info-image">
			<h4><label for="lps_style_has_stacked"><?php esc_html_e( 'Stacked on Mobile', 'lps' ); ?></label></h4>
			<select id="lps_style_has_stacked" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="has-stacked"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>

		<h4><?php esc_html_e( 'Font Size', 'lps' ); ?></h4>
		<p><?php esc_html_e( 'Ex: 1rem, 24px, 2em, clamp(1rem, 0.6rem + 1.25vw, 1.4rem), etc.', 'lps' ); ?> <?php esc_html_e( 'Use px, %, vw or vh as units.', 'lps' ); ?> <?php esc_html_e( 'Leave empty if you want to use the default values.', 'lps' ); ?></p>
		<div class="row">
			<label for="lps_size_text"><?php esc_html_e( 'text', 'lps' ); ?></label>
			<input type="text" name="lps_size_text" id="lps_size_text" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
		</div>
		<div class="row">
			<label for="lps_size_title"><?php esc_html_e( 'title', 'lps' ); ?></label>
			<input type="text" name="lps_size_title" id="lps_size_title" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
		</div>

		<h4><?php esc_html_e( 'Colors', 'lps' ); ?></h4>
		<p>
			<?php esc_html_e( 'Ex: #fff, rbga(255,255,255, 0.5), etc.', 'lps' ); ?> <?php esc_html_e( 'If you want to apply the colors, you should remove the card generic aspect.', 'lps' ); ?> <?php esc_html_e( 'Leave empty if you want to use the default values.', 'lps' ); ?>
		</p>
		<div class="row">
			<label for="lps_color_text"><?php esc_html_e( 'text', 'lps' ); ?></label>
			<div class="lps-color-wrapper">
				<input type="text" name="lps_color_text" id="lps_color_text" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
				<input type="color" id="lps_color_text_field" onchange="lpsRefreshColor(this)">
				<button onclick="lpsResetColor('text')" onkeyup="lpsResetColor('text')" aria-label="<?php esc_html_e( 'Reset', 'lps' ); ?>"></button>
			</div>
		</div>
		<div class="row">
			<label for="lps_color_title"><?php esc_html_e( 'title', 'lps' ); ?></label>
			<div class="lps-color-wrapper">
				<input type="text" name="lps_color_title" id="lps_color_title" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
				<input type="color" id="lps_color_title_field" onchange="lpsRefreshColor(this)">
				<button onclick="lpsResetColor('title')" onkeyup="lpsResetColor('title')" aria-label="<?php esc_html_e( 'Reset', 'lps' ); ?>"></button>
			</div>
		</div>
		<div class="row">
			<label for="lps_color_bg"><?php esc_html_e( 'background', 'lps' ); ?></label>
			<div class="lps-color-wrapper">
				<input type="text" name="lps_color_bg" id="lps_color_bg" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
				<input type="color" id="lps_color_bg_field" onchange="lpsRefreshColor(this)">
				<button onclick="lpsResetColor('bg')" onkeyup="lpsResetColor('bg')" aria-label="<?php esc_html_e( 'Reset', 'lps' ); ?>"></button>
			</div>
		</div>

		<h4><?php esc_html_e( 'Title Style', 'lps' ); ?></h4>
		<div class="row">
			<label for="lps_style_has_title_nodecoration"><?php esc_html_e( 'no decoration', 'lps' ); ?></label>
			<select id="lps_style_has_title_nodecoration" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'inherit', 'lps' ); ?></option>
				<option value="has-title-nodecoration"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row">
			<label for="lps_style_has_title_uppercase"><?php esc_html_e( 'uppercase', 'lps' ); ?></label>
			<select id="lps_style_has_title_uppercase" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'inherit', 'lps' ); ?></option>
				<option value="has-title-uppercase"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row">
			<label for="lps_style_has_title_shadow"><?php esc_html_e( 'text shadow', 'lps' ); ?></label>
			<select id="lps_style_has_title_shadow" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="has-title-shadow"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>

		<h4><?php esc_html_e( 'Image Style', 'lps' ); ?></h4>
		<div class="row lps-update-blink" data-cond="#lps_style_helper_overlay" data-comp-not="as-overlay">
			<label for="lps_image_ratio"><?php esc_html_e( 'aspect ratio', 'lps' ); ?></label>
			<select id="lps_image_ratio" onchange="lpsRefresh();">
				<option value=""><?php esc_html_e( 'auto', 'lps' ); ?></option>
				<option value="contain"><?php esc_html_e( 'none', 'lps' ); ?></option>
				<option value="1">1:1 (<?php esc_html_e( 'square', 'lps' ); ?>)</option>
				<optgroup label="<?php esc_html_e( 'landscape', 'lps' ); ?>">
					<option value="16/9">16:9</option>
					<option value="4/3">4:3</option>
					<option value="3/2">3:2</option>
				</optgroup>
				<optgroup label="<?php esc_html_e( 'portrait', 'lps' ); ?>">
					<option value="5/9">5:9</option>
					<option value="4/5">4:5</option>
				</optgroup>
			</select>
		</div>
		<div class="row lps-update-blink" data-cond="#lps_style_helper_overlay" data-comp-not="as-overlay">
			<label for="lps_style_has_img_spacing"><?php esc_html_e( 'spacing', 'lps' ); ?></label>
			<select id="lps_style_has_img_spacing" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="has-img-spacing"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>

		<div class="row lps-update-blink" data-cond="#lps_style_helper_overlay" data-comp-in="h-image-info,h-info-image">
			<label for="lps_size_image"><?php esc_html_e( 'size', 'lps' ); ?></label>
			<input type="text" name="lps_size_image" id="lps_size_image" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?> (50%)" size="32">
		</div>

		<div class="row lps-update-blink" data-cond="#lps_style_helper_overlay" data-comp="as-overlay">
			<label for="lps_image_opacity"><?php esc_html_e( 'opacity', 'lps' ); ?></label>
			<select id="lps_image_opacity" data-default="" onchange="lpsStyleHelper()">
				<?php
				foreach ( range( 100, 0, -5 ) as $nr ) {
					$val = ( 0 === $nr ) ? 0 : $nr / 100;
					?>
					<option value="<?php echo esc_attr( $val ); ?>"><?php echo (int) $nr; ?>%</option>
					<?php
				}
				?>
			</select>
		</div>
		<div class="row">
			<label for="lps_style_has_zoom"><?php esc_html_e( 'zoom', 'lps' ); ?> <span class="comment">(<?php esc_html_e( 'hover effect', 'lps' ); ?>)</span></label>
			<select id="lps_style_has_zoom" data-default="" onchange="lpsStyleHelper()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="hover-zoom"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
	</div>

	<div class="settings-block block-use available-for-tiles">
		<h3><?php esc_html_e( 'Spacing Options', 'lps' ); ?></h3>
		<p><?php esc_html_e( 'These options allow to specify the card padding and minimum height, and the gap between the elements inside the card. Use px, %, vw or vh as units.', 'lps' ); ?> <?php esc_html_e( 'Leave empty if you want to use the default values.', 'lps' ); ?></p>

		<h4>
			<?php esc_html_e( 'Wide', 'lps' ); ?> / <?php esc_html_e( 'default', 'lps' ); ?>
			(<span class="comment">&gt;1024px</span>)
		</h4>
		<div class="row">
			<label for="lps_default_height"><?php esc_html_e( 'height', 'lps' ); ?></label>
			<input type="text" name="lps_default_height" id="lps_default_height" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row not-for-overlay lps-update-blink">
			<label for="lps_default_padding"><?php esc_html_e( 'padding', 'lps' ); ?></label>
			<input type="text" name="lps_default_padding" id="lps_default_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row for-overlay lps-update-blink">
			<label for="lps_default_overlay_padding"><?php esc_html_e( 'padding', 'lps' ); ?></label>
			<input type="text" name="lps_default_overlay_padding" id="lps_default_overlay_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row">
			<label for="lps_default_gap"><?php esc_html_e( 'gap', 'lps' ); ?></label>
			<input type="text" name="lps_default_gap" id="lps_default_gap" onchange="lpsRefresh()" placeholder="1rem" onkeyup="lpsRefresh()" size="32">
		</div>

		<h4>
			<?php esc_html_e( 'Medium', 'lps' ); ?>
			(<span class="comment">&gt;600px, &lt;= 1024px</span>)
		</h4>
		<div class="row">
			<label for="lps_tablet_height"><?php esc_html_e( 'height', 'lps' ); ?></label>
			<input type="text" name="lps_tablet_height" id="lps_tablet_height" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row not-for-overlay lps-update-blink">
			<label for="lps_tablet_padding"><?php esc_html_e( 'padding', 'lps' ); ?></label>
			<input type="text" name="lps_tablet_padding" id="lps_tablet_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row for-overlay lps-update-blink">
			<label for="lps_tablet_overlay_padding"><?php esc_html_e( 'padding', 'lps' ); ?></label>
			<input type="text" name="lps_tablet_overlay_padding" id="lps_tablet_overlay_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row">
			<label for="lps_tablet_gap"><?php esc_html_e( 'gap', 'lps' ); ?></label>
			<input type="text" name="lps_tablet_gap" id="lps_tablet_gap" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>

		<h4>
			<?php esc_html_e( 'Small', 'lps' ); ?>
			(<span class="comment">&lt;=600px</span>)
		</h4>
		<div class="row">
			<label for="lps_mobile_height"><?php esc_html_e( 'height', 'lps' ); ?></label>
			<input type="text" name="lps_mobile_height" id="lps_mobile_height" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row not-for-overlay lps-update-blink">
			<label for="lps_mobile_padding"><?php esc_html_e( 'padding', 'lps' ); ?></label>
			<input type="text" name="lps_mobile_padding" id="lps_mobile_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row for-overlay lps-update-blink">
			<label for="lps_mobile_overlay_padding"><?php esc_html_e( 'padding', 'lps' ); ?></label>
			<input type="text" name="lps_mobile_overlay_padding" id="lps_mobile_overlay_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
		<div class="row">
			<label for="lps_mobile_gap"><?php esc_html_e( 'gap', 'lps' ); ?></label>
			<input type="text" name="lps_mobile_gap" id="lps_mobile_gap" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
		</div>
	</div>
</div>
