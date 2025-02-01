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

$the_tax = self::filtered_taxonomies();
?>

<div id="lps_shortcode_popup_container" style="display:none;" aria-label="<?php esc_html_e( 'Latest Post Shortcode', 'lps' ); ?> <?php esc_html_e( 'Settings Modal', 'lps' ); ?>">
	<div class="lps_maintable_buttons">
		<button type="button" id="lps-link-close" onclick="lpsClose();" aria-label="<?php esc_html_e( 'Close', 'lps' ); ?>"></button>
		<div class="lps_maintable">
			<div class="shortcode-preview">
				<div class="inner">
					<h1 id="lps_shortcode_popup_container_title">
						<?php esc_html_e( 'Latest Post Shortcode', 'lps' ); ?>
					</h1>

					<div class="row v-center space-between">
						<h3><?php esc_html_e( 'Preview', 'lps' ); ?></h3>
						<button id="lps-embed-button" class="button embed" onclick="lpsEmbed();"><?php esc_html_e( 'Embed', 'lps' ); ?></button>
					</div>

					<div id="lps-preview">
						<div id="lps_preview_embed_shortcode" aria-label="<?php esc_html_e( 'Preview', 'lps' ); ?>">[latest-selected-content ver="2" type="post" limit="1" tag="news"]</div>
					</div>

					<button id="lps_reset_cache" class="button lps-update-blink" aria-label="<?php esc_html_e( 'Reset cache', 'lps' ); ?>" style="display: none;" onclick="lpsResetCache()"><?php esc_html_e( 'Reset cache', 'lps' ); ?></button>

					<div class="no-mobile">
						<h3><?php esc_html_e( 'Settings', 'lps' ); ?></h3>
						<ul class="lps-ui-menu">
							<li id="menu-tabs-0" class="selected"><a onclick="lpsMenu('#tabs-0');" tabindex="0"><?php esc_html_e( 'Output Type', 'lps' ); ?></a></li>
							<li id="menu-tabs-1"><a onclick="lpsMenu('#tabs-1');" tabindex="0"><?php esc_html_e( 'Content & Filters', 'lps' ); ?></a></li>
							<li id="menu-tabs-2"><a onclick="lpsMenu('#tabs-2');" tabindex="0"><?php esc_html_e( 'Limit & Pagination', 'lps' ); ?></a></li>
							<li id="menu-tabs-3"><a onclick="lpsMenu('#tabs-3');" tabindex="0"><?php esc_html_e( 'Display Settings', 'lps' ); ?></a></li>
							<li id="menu-tabs-4"><a onclick="lpsMenu('#tabs-4');" tabindex="0"><?php esc_html_e( 'Extra Options', 'lps' ); ?></a></li>
							<li id="menu-tabs-5"><a onclick="lpsMenu('#tabs-5');" tabindex="0"><?php esc_html_e( 'Style', 'lps' ); ?></a></li>
						</ul>
						<input type="hidden" name="lps_shortcode_popup_container_current_menu" id="lps_shortcode_popup_container_current_menu" value="#menu-tabs-0">
					</div>

					<?php self::show_donate_text(); ?>
				</div>
			</div>
			<div class="shortcode-settings">
				<div class="inner">
					<?php require_once __DIR__ . '/parts/tabs0.php'; // Output type. ?>
					<?php require_once __DIR__ . '/parts/tabs1.php'; // Type, Order, Filters, Exclude. ?>
					<?php require_once __DIR__ . '/parts/tabs2.php'; // Pagination. ?>
					<?php require_once __DIR__ . '/parts/tabs3.php'; // Appearance. ?>
					<?php require_once __DIR__ . '/parts/tabs4.php'; // Extra. ?>
					<?php require_once __DIR__ . '/parts/tabs5.php'; // Styles, CSS. ?>
				</div>
			</div>
		</div>
		<button type="button" class="no-mobile" id="lps-link-up" onclick="lpsMenu('#tabs-0');" aria-label="<?php esc_html_e( 'Scroll up', 'lps' ); ?>"></button>
		<button type="button" class="no-desktop" id="lps-link-up-mobile" onclick="lpsMenu('top');" aria-label="<?php esc_html_e( 'Scroll up', 'lps' ); ?>"></button>
	</div>
</div>
