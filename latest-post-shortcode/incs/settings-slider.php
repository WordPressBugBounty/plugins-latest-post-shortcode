<?php //phpcs:ignore Generic.Files.LineEndings.InvalidEOLChar
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

<div id="lps_display_slider">
	<div class="settings-block">
		<h3><?php esc_html_e( 'Slider Settings', 'lps' ); ?></h3><hr>
		<table>
			<tr>
				<th><?php esc_html_e( 'Wrapper Element', 'lps' ); ?></th>
				<td>
					<select name="lps_sliderwrap" id="lps_sliderwrap" data-default="div" onchange="lpsRefresh()">
						<?php foreach ( self::$slider_wrap_tags as $st ) : ?>
							<option value="<?php echo esc_attr( $st ); ?>"><?php echo esc_html( $st ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Slides Transition', 'lps' ); ?></th>
				<td>
					<select name="lps_slidermode" id="lps_slidermode" data-default="horizontal" onchange="lpsRefresh()">
						<option value="horizontal"><?php esc_html_e( 'horizontal', 'lps' ); ?></option>
						<option value="vertical"><?php esc_html_e( 'vertical', 'lps' ); ?></option>
						<option value="fade"><?php esc_html_e( 'fade', 'lps' ); ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<th><?php esc_html_e( 'Center Mode', 'lps' ); ?></th>
				<td>
					<select name="lps_centermode" id="lps_centermode" data-default="" onchange="lpsRefresh()">
						<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
						<option value="true"><?php esc_html_e( 'center mode', 'lps' ); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<div id="lps_centermode_options">
			<table>
				<tr>
					<th></th>
					<td><p class="comment lps-update-blink"><?php esc_html_e( 'The center mode is intended to works for the cases when you are showing more slides per row, and works better when you use an odd number of slides (3, 5, etc.), so, if your slider is presenting one slide at a time, you might want to switch this option off.', 'lps' ); ?></p></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Center Mode Padding', 'lps' ); ?></th>
					<td>
						<input type="number" name="lps_centerpadd" id="lps_centerpadd" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="32" min="0" max="100">
						<p class="comment lps-update-blink"><?php esc_html_e( 'This is the value in pixels.', 'lps' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<table>
			<tr>
				<th><?php esc_html_e( 'Auto Play', 'lps' ); ?></th>
				<td>
					<select name="lps_sliderauto" id="lps_sliderauto" data-default="" onchange="lpsRefresh()">
						<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
						<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
					</select>
				</td>
			</tr>
			<tbody id="lps_sliderauto_options">
				<tr>
					<th><?php esc_html_e( 'Speed', 'lps' ); ?></th>
					<td>
						<input type="number" name="lps_sliderspeed" id="lps_sliderspeed" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="3000" min="1000" max="20000">
						<p class="comment lps-update-blink"><?php esc_html_e( 'Autoplay speed in milliseconds.', 'lps' ); ?></p>
					</td>
				</tr>
			</tbody>
			<tr>
				<th><?php esc_html_e( 'Card Aspect Ratio', 'lps' ); ?></th>
				<td>
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
					<p class="comment"><?php esc_html_e( 'In order to get a more consistent appearance, it is reccommended to use aspect ratio options, instead of the height options.', 'lps' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Height', 'lps' ); ?></th>
				<td>
					<select name="lps_sliderheight" id="lps_sliderheight"
						data-default="" onchange="lpsRefresh()">
						<option value=""><?php esc_html_e( 'adaptive', 'lps' ); ?></option>
						<option value="fixed"><?php esc_html_e( 'fixed', 'lps' ); ?></option>
					</select>
					<p class="comment"><?php esc_html_e( 'This depends on the image size you select', 'lps' ); ?></p>
				</td>
			</tr>
			<tbody id="lps_sliderheight_options">
				<tr>
					<th><?php esc_html_e( 'Maximum Height', 'lps' ); ?></th>
					<td>
						<input type="number" name="lps_slidermaxheight" id="lps_slidermaxheight"
							onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="280" min="1" max="1200">
						<p class="comment lps-update-blink"><?php esc_html_e( 'Provide the value in pixels for the maximum height of the slider.', 'lps' ); ?></p>
					</td>
				</tr>
			</tbody>
			<tr>
				<th><?php esc_html_e( 'Show Slide Overlay', 'lps' ); ?></th>
				<td>
					<select name="lps_slideoverlay" id="lps_slideoverlay"
						data-default="" onchange="lpsRefresh()">
						<option value=""><?php esc_html_e( 'title', 'lps' ); ?> + <?php esc_html_e( 'trimmed excerpt', 'lps' ); ?></option>
						<option value="title"><?php esc_html_e( 'title', 'lps' ); ?></option>
						<option value="text"><?php esc_html_e( 'trimmed excerpt', 'lps' ); ?></option>
						<option value="no"><?php esc_html_e( 'no overlay, just the image', 'lps' ); ?></option>
					</select>
					<p class="comment"><?php esc_html_e( 'The slide will display over the image the title and few chars from the excerpt (what you define for the Post Appearance > Chars Limit below).', 'lps' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Slides Gaps', 'lps' ); ?></th>
				<td>
					<input type="number" name="lps_slidegap" id="lps_slidegap"
						onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="32" min="0" max="100">
					<p class="comment"><?php esc_html_e( 'This is the value in pixels.', 'lps' ); ?> <?php esc_html_e( 'This is useful when you want to display more visible slides in the row.', 'lps' ); ?></p>
				</td>
			</tr>
		</table>

		<table>
			<tr>
				<th>
					<?php esc_html_e( 'Wide', 'lps' ); ?>
					<p class="comment">(<?php esc_html_e( 'default', 'lps' ); ?>)</p>
				</th>
				<td>
					<?php esc_html_e( 'next/previous', 'lps' ); ?>
					<select name="lps_slidercontrols" id="lps_slidercontrols"
						data-default="" onchange="lpsRefresh()">
						<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
						<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
					</select>
				</td>
				<td>
					<?php esc_html_e( 'responsive', 'lps' ); ?>
					<select name="lps_slidersponsive" id="lps_slidersponsive"
						data-default="" onchange="lpsRefresh()">
						<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
						<option value="yes"><?php esc_html_e( 'yes', 'lps' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<?php esc_html_e( 'slides to show', 'lps' ); ?>
					<input type="number" name="lps_slideslides" id="lps_slideslides"
						onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
				</td>
				<td>
					<?php esc_html_e( 'slides to scroll', 'lps' ); ?>
					<input type="number" name="lps_slidescroll" id="lps_slidescroll"
						onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<?php esc_html_e( 'show dots', 'lps' ); ?>
					<select name="lps_sliderdots" id="lps_sliderdots" onchange="lpsRefresh()">
						<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
						<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
					</select>
				</td>
				<td>
					<?php esc_html_e( 'infinite scroll', 'lps' ); ?>
					<select name="lps_sliderinfinite" id="lps_sliderinfinite"
						data-default="" onchange="lpsRefresh()">
						<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
						<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
					</select>
				</td>
			</tr>
			<tbody id="lps_slidersponsive_options">
				<tr>
					<th></th>
					<td colspan="2">
						<?php esc_html_e( 'respond to', 'lps' ); ?>
						<select name="lps_respondto" id="lps_respondto"
							data-default="window" onchange="lpsRefresh()">
							<option value="window"><?php esc_html_e( 'window', 'lps' ); ?></option>
							<option value="slider"><?php esc_html_e( 'slider', 'lps' ); ?></option>
							<option value=""><?php esc_html_e( 'min', 'lps' ); ?></option>
						</select>
						<p class="comment lps-update-blink">
							<?php esc_html_e( 'Width that responsive object responds to, the min (default) option means the smaller of the window or slider.', 'lps' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Medium', 'lps' ); ?>
						<p class="comment">(<?php esc_html_e( 'resolution', 'lps' ); ?>)</p>
					</th>
					<td colspan="2">
						<?php esc_html_e( 'breakpoint', 'lps' ); ?>
						<input type="number" name="lps_sliderbreakpoint_tablet" id="lps_sliderbreakpoint_tablet" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1024" min="680" max="1200">
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php esc_html_e( 'slides to show', 'lps' ); ?>
						<input type="number" name="lps_slideslides_tablet" id="lps_slideslides_tablet" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
					</td>
					<td>
						<?php esc_html_e( 'slides to scroll', 'lps' ); ?>
						<input type="number" name="lps_slidescroll_tablet" id="lps_slidescroll_tablet" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php esc_html_e( 'show dots', 'lps' ); ?>
						<select name="lps_sliderdots_tablet" id="lps_sliderdots_tablet" onchange="lpsRefresh()">
							<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
							<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
						</select>
					</td>
					<td>
						<?php esc_html_e( 'infinite scroll', 'lps' ); ?>
						<select name="lps_sliderinfinite_tablet" id="lps_sliderinfinite_tablet" data-default="" onchange="lpsRefresh()">
							<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
							<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Small', 'lps' ); ?>
						<p class="comment">(<?php esc_html_e( 'resolution', 'lps' ); ?>)</p>
					</th>
					<td colspan="2">
						<?php esc_html_e( 'breakpoint', 'lps' ); ?>
						<input type="number" name="lps_sliderbreakpoint_mobile" id="lps_sliderbreakpoint_mobile" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="680" min="320" max="1024">
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php esc_html_e( 'slides to show', 'lps' ); ?>
						<input type="number" name="lps_slideslides_mobile" id="lps_slideslides_mobile" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
					</td>
					<td>
						<?php esc_html_e( 'slides to scroll', 'lps' ); ?>
						<input type="number" name="lps_slidescroll_mobile" id="lps_slidescroll_mobile" onchange="lpsRefresh()" onkeyup="lpsRefresh()" value="1" min="1" max="12">
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php esc_html_e( 'show dots', 'lps' ); ?>
						<select name="lps_sliderdots_mobile" id="lps_sliderdots_mobile"
							onchange="lpsRefresh()">
							<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
							<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
						</select>
					</td>
					<td>
						<?php esc_html_e( 'infinite scroll', 'lps' ); ?>
						<select name="lps_sliderinfinite_mobile" id="lps_sliderinfinite_mobile"
							data-default="" onchange="lpsRefresh()">
							<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
							<option value="true"><?php esc_html_e( 'yes', 'lps' ); ?></option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
