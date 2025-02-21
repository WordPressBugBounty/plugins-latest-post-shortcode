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
<div id="lps_display_slider" class="settings-block big">
	<h3><?php esc_html_e( 'Slider Settings', 'lps' ); ?></h3>

	<h4>
		<label for="lps_sliderwrap">
		<?php
		// Translators: %s - element name.
		echo esc_html( sprintf( __( 'HTML Tag for the %s', 'lps' ), __( 'slider', 'lps' ) ) );
		?>
		</label>
	</h4>
	<p class="lps-update-blink" data-cond="#lps_centermode" data-comp="true">
		<?php
		// Translators: %s - default value.
		echo esc_html( sprintf( __( 'The default value is %s.', 'lps' ), 'div' ) );
		?>
	</p>
	<select name="lps_sliderwrap" id="lps_sliderwrap" data-default="div" onchange="lpsRefresh()">
		<?php foreach ( self::$slider_wrap_tags as $st ) : ?>
			<option value="<?php echo esc_attr( $st ); ?>"><?php echo esc_html( $st ); ?></option>
		<?php endforeach; ?>
	</select>

	<h4><label for="lps_slidermode"><?php esc_html_e( 'Transition', 'lps' ); ?></label></h4>
	<select name="lps_slidermode" id="lps_slidermode" data-default="horizontal" onchange="lpsRefresh()">
		<option value="horizontal"><?php esc_html_e( 'horizontal', 'lps' ); ?></option>
		<option value="vertical"><?php esc_html_e( 'vertical', 'lps' ); ?></option>
		<option value="fade"><?php esc_html_e( 'fade', 'lps' ); ?></option>
	</select>

	<h4><label for="lps_centermode"><?php esc_html_e( 'Center Mode', 'lps' ); ?></label></h4>
	<p class="lps-update-blink" data-cond="#lps_centermode" data-comp="true"><?php esc_html_e( 'The center mode works for odd number of cards (3, 5, etc.).', 'lps' ); ?></p>
	<select name="lps_centermode" id="lps_centermode" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
		<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
	</select>
	<div class="wrap lps-update-blink" data-cond="#lps_centermode" data-comp="true">
		<h4><label for="lps_centerpadd"><?php esc_html_e( 'Center Mode Padding', 'lps' ); ?></label></h4>
		<p><?php esc_html_e( 'This is the value in pixels.', 'lps' ); ?></p>
		<input type="number" name="lps_centerpadd" id="lps_centerpadd" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="32" min="0" max="100">
	</div>

	<h4><label for="lps_sliderauto"><?php esc_html_e( 'Auto Play', 'lps' ); ?></label></h4>
	<select name="lps_sliderauto" id="lps_sliderauto" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
		<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
	</select>
	<div class="wrap lps-update-blink" data-cond="#lps_sliderauto" data-comp="true">
		<h4><label for="lps_sliderspeed"><?php esc_html_e( 'Speed', 'lps' ); ?></label></h4>
		<p><?php esc_html_e( 'Autoplay speed in milliseconds.', 'lps' ); ?></p>
		<input type="number" name="lps_sliderspeed" id="lps_sliderspeed" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="3000" min="1000" max="20000">
	</div>

	<h4><label for="lps_slideratio"><?php esc_html_e( 'Card Aspect Ratio', 'lps' ); ?></label></h4>
	<p><?php esc_html_e( 'For a more consistent appearance, it is recommended to use the aspect ratio options instead of the height options.', 'lps' ); ?></p>
	<select id="lps_slideratio" onchange="lpsRefresh();">
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

	<h4><label for="lps_sliderheight"><?php esc_html_e( 'Height', 'lps' ); ?></label></h4>
	<p><?php esc_html_e( 'This depends on the selected sub-size.', 'lps' ); ?></p>
	<select name="lps_sliderheight" id="lps_sliderheight"
		data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'auto', 'lps' ); ?></option>
		<option value="fixed"><?php esc_html_e( 'fixed', 'lps' ); ?></option>
	</select>
	<div class="wrap lps-update-blink" data-cond="#lps_sliderheight" data-comp="fixed">
		<h4><label for="lps_slidermaxheight"><?php esc_html_e( 'Maximum Height', 'lps' ); ?></label></h4>
		<p><?php esc_html_e( 'This is the value in pixels.', 'lps' ); ?></p>
		<input type="number" name="lps_slidermaxheight" id="lps_slidermaxheight" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="280" min="1" max="1200">
	</div>

	<h4><label for="lps_slideoverlay"><?php esc_html_e( 'Card Elements', 'lps' ); ?></label></h4>
	<select name="lps_slideoverlay" id="lps_slideoverlay" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'title', 'lps' ); ?> + <?php esc_html_e( 'trimmed excerpt', 'lps' ); ?></option>
		<option value="title"><?php esc_html_e( 'title', 'lps' ); ?></option>
		<option value="text"><?php esc_html_e( 'trimmed excerpt', 'lps' ); ?></option>
		<option value="no"><?php esc_html_e( 'no overlay, just the image', 'lps' ); ?></option>
	</select>

	<h4><label for="lps_slidegap"><?php esc_html_e( 'Gap', 'lps' ); ?></label></h4>
	<p><?php esc_html_e( 'This is the value in pixels.', 'lps' ); ?> <?php esc_html_e( 'The gap between the visible cards in the row.', 'lps' ); ?></p>
	<input type="number" name="lps_slidegap" id="lps_slidegap" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="32" min="0" max="100">

	<h4><?php esc_html_e( 'Wide', 'lps' ); ?> (<?php esc_html_e( 'default', 'lps' ); ?>)</h4>
	<div class="row">
		<label for="lps_slidercontrols"><?php esc_html_e( 'next/previous', 'lps' ); ?></label>
		<select name="lps_slidercontrols" id="lps_slidercontrols" data-default="" onchange="lpsRefresh()">
			<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
			<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
		</select>
	</div>
	<div class="row">
		<label for="lps_slideslides"><?php esc_html_e( 'cards to show', 'lps' ); ?></label>
		<input type="number" name="lps_slideslides" id="lps_slideslides" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
	</div>
	<div class="row">
		<label for="lps_slidescroll"><?php esc_html_e( 'cards to scroll', 'lps' ); ?></label>
		<input type="number" name="lps_slidescroll" id="lps_slidescroll" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
	</div>
	<div class="row">
		<label for="lps_sliderdots"><?php esc_html_e( 'show dots', 'lps' ); ?></label>
		<select name="lps_sliderdots" id="lps_sliderdots" onchange="lpsRefresh()">
			<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
			<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
		</select>
	</div>
	<div class="row">
		<label for="lps_sliderinfinite"><?php esc_html_e( 'infinite scroll', 'lps' ); ?></label>
		<select name="lps_sliderinfinite" id="lps_sliderinfinite" data-default="" onchange="lpsRefresh()">
			<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
			<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
		</select>
	</div>

	<h4><label for="lps_slidersponsive"><?php esc_html_e( 'Responsive', 'lps' ); ?></label></h4>
	<select name="lps_slidersponsive" id="lps_slidersponsive" data-default="" onchange="lpsRefresh()">
		<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
		<option value="yes"><?php esc_html_e( 'yes', 'lps' ); ?></option>
	</select>
	<div class="wrap indent lps-update-blink" data-cond="#lps_slidersponsive" data-comp="yes">
		<p><?php esc_html_e( 'This represents the width that the responsive object responds to. The minimum (default) option applies the smaller value between the window and the slider.', 'lps' ); ?></p>
		<div class="row">
			<label for="lps_respondto"><?php esc_html_e( 'responds to', 'lps' ); ?></label>
			<select name="lps_respondto" id="lps_respondto"
				data-default="window" onchange="lpsRefresh()">
				<option value="window"><?php esc_html_e( 'window', 'lps' ); ?></option>
				<option value="slider"><?php esc_html_e( 'slider', 'lps' ); ?></option>
				<option value=""><?php esc_html_e( 'minimum', 'lps' ); ?></option>
			</select>
		</div>

		<h4><?php esc_html_e( 'Medium', 'lps' ); ?> <span class="comment">(<?php esc_html_e( 'resolution', 'lps' ); ?>)</span></h4>
		<div class="row">
			<label for="lps_sliderbreakpoint_tablet"><?php esc_html_e( 'breakpoint', 'lps' ); ?></label>
			<input type="number" name="lps_sliderbreakpoint_tablet" id="lps_sliderbreakpoint_tablet" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1024" min="680" max="1200">
		</div>

		<div class="row">
			<label for="lps_slideslides_tablet"><?php esc_html_e( 'cards to show', 'lps' ); ?></label>
			<input type="number" name="lps_slideslides_tablet" id="lps_slideslides_tablet" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
		</div>
		<div class="row">
			<label for="lps_slidescroll_tablet"><?php esc_html_e( 'cards to scroll', 'lps' ); ?></label>
			<input type="number" name="lps_slidescroll_tablet" id="lps_slidescroll_tablet" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
		</div>
		<div class="row">
			<label for="lps_sliderdots_tablet"><?php esc_html_e( 'show dots', 'lps' ); ?></label>
			<select name="lps_sliderdots_tablet" id="lps_sliderdots_tablet" onchange="lpsRefresh()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row">
			<label for="lps_sliderinfinite_tablet"><?php esc_html_e( 'infinite scroll', 'lps' ); ?></label>
			<select name="lps_sliderinfinite_tablet" id="lps_sliderinfinite_tablet" data-default="" onchange="lpsRefresh()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>

		<h4><?php esc_html_e( 'Small', 'lps' ); ?> <span class="comment">(<?php esc_html_e( 'resolution', 'lps' ); ?>)</span></h4>
		<div class="row">
			<label for="lps_sliderbreakpoint_mobile"><?php esc_html_e( 'breakpoint', 'lps' ); ?></label>
			<input type="number" name="lps_sliderbreakpoint_mobile" id="lps_sliderbreakpoint_mobile" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="680" min="320" max="1024">
		</div>
		<div class="row">
			<label for="lps_slideslides_mobile"><?php esc_html_e( 'cards to show', 'lps' ); ?></label>
			<input type="number" name="lps_slideslides_mobile" id="lps_slideslides_mobile" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
		</div>
		<div class="row">
			<label for="lps_slidescroll_mobile"><?php esc_html_e( 'cards to scroll', 'lps' ); ?></label>
			<input type="number" name="lps_slidescroll_mobile" id="lps_slidescroll_mobile" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
		</div>
		<div class="row">
			<label for="lps_sliderdots_mobile"><?php esc_html_e( 'show dots', 'lps' ); ?></label>
			<select name="lps_sliderdots_mobile" id="lps_sliderdots_mobile"
				onchange="lpsRefresh()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
		<div class="row">
			<label for="lps_sliderinfinite_mobile"><?php esc_html_e( 'infinite scroll', 'lps' ); ?></label>
			<select name="lps_sliderinfinite_mobile" id="lps_sliderinfinite_mobile"
				data-default="" onchange="lpsRefresh()">
				<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
				<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
			</select>
		</div>
	</div>
</div>
