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

<div id="lps_shortcode_popup_container_bg" style="display:none;"></div>
<div id="lps_shortcode_popup_container" style="display:none;">
	<div class="lps_maintable_buttons">
		<button type="button" id="lps-link-close" onclick="lpsClose();">
			<span class="dashicons dashicons-no"></span>
		</button>
		<button type="button" id="lps-link-up" onclick="lpsMenu('#tabs-0');">
			<span class="dashicons dashicons-arrow-up-alt"></span>
		</button>
		<button type="button" id="lps-link-up-mobile" onclick="lpsMenu('top');">
			<span class="dashicons dashicons-arrow-up-alt"></span>
		</button>
		<table width="100%" cellpadding="0" cellspacing="0" class="lps_maintable">
			<tr>
				<td class="shortcode-preview">
					<div class="inner">
						<h1 class="lps_shortcode_popup_container_title">
							<?php require_once dirname( __DIR__ ) . '/lps-block/icon.svg'; ?>
							<span><?php esc_html_e( 'Latest Post Shortcode', 'lps' ); ?></span>
						</h1>
						<div class="clear"></div>
						<hr class="sep">

						<div class="as-row space-between">
							<h3><?php esc_html_e( 'Preview', 'lps' ); ?></h3>
							<button id="lps-embed-button" class="button embed float-right" onclick="lpsEmbed();"><?php esc_html_e( 'Embed Shortcode', 'lps' ); ?></button>
						</div>

						<div id="lps-preview">
							<div id="lps_preview_embed_shortcode">[latest-selected-content ver="2" type="post" limit="1" tag="news"]</div>
						</div>
						<div class="clear"></div>
						<div id="lps_reset_cache-wrap">
							<button id="lps_reset_cache"
								class="button outline float-right"
								onclick="lpsResetCache()">
								<span class="dashicons dashicons-update"></span>
								<?php esc_html_e( 'Reset cache', 'lps' ); ?>
							</button>
						</div>
						<div class="no-mobile">
							<hr class="sep">
							<h3><?php esc_html_e( 'Settings', 'lps' ); ?></h3>
							<ul class="lps-ui-menu">
								<li id="menu-tabs-0" class="selected"><a onclick="lpsMenu('#tabs-0');" tabindex="0"><?php esc_html_e( 'Output Type', 'lps' ); ?></a></li>
								<li id="menu-tabs-1"><a onclick="lpsMenu('#tabs-1');" tabindex="0"><?php esc_html_e( 'Content & Filters', 'lps' ); ?></a></li>
								<li id="menu-tabs-2"><a onclick="lpsMenu('#tabs-2');" tabindex="0"><?php esc_html_e( 'Limit & Pagination', 'lps' ); ?></a></li>
								<li id="menu-tabs-3"><a onclick="lpsMenu('#tabs-3');" tabindex="0"><?php esc_html_e( 'Display Settings', 'lps' ); ?></a></li>
								<li id="menu-tabs-4"><a onclick="lpsMenu('#tabs-4');" tabindex="0"><?php esc_html_e( 'Extra Options', 'lps' ); ?></a></li>
							</ul>
							<input type="hidden" name="lps_shortcode_popup_container_current_menu" id="lps_shortcode_popup_container_current_menu" value="#menu-tabs-0">
							<div class="clear"></div>
							<?php self::show_donate_text(); ?>
							<div class="clear"></div>
						</div>
					</div>
				</td>
				<td class="shortcode-settings">
					<div class="inner">
						<div id="tabs-0" class="settings-group">
							<h1>1. <?php esc_html_e( 'Output Type', 'lps' ); ?></h1>

							<div class="settings-block">
								<table>
									<tr>
										<th><?php esc_html_e( 'Version', 'lps' ); ?></th>
										<td>
											<select name="lps_ver" id="lps_ver" data-default="2" onchange="lpsRefresh()">
												<option value="" disabled><?php esc_html_e( '1 (deprecated)', 'lps' ); ?></option>
												<option value="2"><?php esc_html_e( '2 (recommended starting with 11.0.0)', 'lps' ); ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Display as', 'lps' ); ?></th>
										<td>
											<div class="as-grid">
												<img src="<?php echo esc_url( LPS_PLUGIN_URL . 'assets/images/types/grid.jpg' ); ?>" onclick="selectTypeImg('');" width="100%">

												<img src="<?php echo esc_url( LPS_PLUGIN_URL . 'assets/images/types/slider.jpg' ); ?>" onclick="selectTypeImg('slider')" width="100%">
											</div>
										</td>
									</tr>
									<tr>
										<th></th>
										<td>
											<select name="lps_output" id="lps_output" data-default="" onchange="lpsRefresh()">
												<option value=""><?php esc_html_e( 'post grid/list/tiles', 'lps' ); ?></option>
												<option value="slider"><?php esc_html_e( 'slider', 'lps' ); ?></option>
											</select>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<hr class="sep">
						<div id="tabs-1" class="settings-group">
							<h1>2. <?php esc_html_e( 'Content & Filters', 'lps' ); ?></h1>
							<div class="settings-block"><h3>
								<?php esc_html_e( 'Post Types, Status & Order', 'lps' ); ?></h3><hr>
								<table>
									<?php
									if ( is_multisite() ) {
										$sites = self::get_sites();
										if ( ! empty( $sites ) ) :
											?>
											<tr>
												<th><?php esc_html_e( 'Site ID', 'lps' ); ?></th>
												<td>
													<select name="lps_site_id" id="lps_site_id" data-default="" onchange="lpsRefresh()">
														<option value="">~ <?php esc_html_e( 'current site', 'lps' ); ?>~ </option>
														<?php foreach ( $sites as $k => $v ) : ?>
															<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?></option>
														<?php endforeach; ?>
													</select>
												</td>
											</tr>
											<?php
										endif;
									}
									?>
									<tr>
										<th><?php esc_html_e( 'Post Type', 'lps' ); ?></th>
										<td>
											<?php $post_types = self::get_ctps(); ?>
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
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Post Status', 'lps' ); ?></th>
										<td>
											<?php $st = self::get_statuses(); ?>
											<?php foreach ( $st['public'] as $pu => $pul ) : ?>
												<label><input type="checkbox" name="lps_status[]" id="lps_status_<?php echo esc_attr( $pu ); ?>" value="<?php echo esc_attr( $pu ); ?>" onclick="lpsRefresh()" class="lps_status"><b><?php echo esc_html( $pul ); ?> (<?php echo esc_html( $pu ); ?>)</b></label>
											<?php endforeach; ?>
											<?php foreach ( $st['private'] as $pr => $prl ) : ?>
												<label><input type="checkbox" name="lps_status[]" id="lps_status_<?php echo esc_attr( $pr ); ?>" value="<?php echo esc_attr( $pr ); ?>" onclick="lpsRefresh()" class="lps_status"><em><?php echo esc_html( $prl ); ?> (<?php echo esc_html( $pr ); ?>)</em></label>
											<?php endforeach; ?>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Sticky Posts', 'lps' ); ?></th>
										<td>
											<select name="lps_show_extra[]" id="lps_show_extra_sticky" data-default="" onchange="lpsRefresh()" class="lps_show_extra">
												<option value=""><?php esc_html_e( 'no restriction', 'lps' ); ?></option>
												<option value="sticky"><?php esc_html_e( 'only sticky posts', 'lps' ); ?></option>
												<option value="nosticky"><?php esc_html_e( 'no sticky posts', 'lps' ); ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Order by', 'lps' ); ?></th>
										<td>
											<select name="lps_orderby" id="lps_orderby" data-default="dateD" onchange="lpsRefresh()">
												<?php foreach ( self::$orderby_options as $k => $v ) : ?>
													<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v['title'] ); ?></option>
												<?php endforeach; ?>
											</select>

											<div id="lps_orderby_meta_wrap" class="lps-update-blink">
												<input type="text" name="lps_orderby_meta" id="lps_orderby_meta" placeholder="<?php esc_attr_e( 'Post meta (ex: _price)', 'lps' ); ?>" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
												<p class="comment lps-update-blink"><?php esc_html_e( '* Please note that ordering the items by post meta might present performance risks, please use this careful. Additionally, the output will be filtered only to the posts that have the specified post meta.', 'lps' ); ?></p>
											</div>
											<div id="lps_orderby_random_wrap">
												<p class="comment lps-update-blink"><?php esc_html_e( '* Please note that ordering the items by random might present performance risks, please use this careful.', 'lps' ); ?><span class="block-use available-for-tiles"> <?php esc_html_e( 'Also, using a random order and pagination will output unexpected and potentially redundant content.', 'lps' ); ?></span></p>
											</div>

											<?php esc_attr_e( 'descending', 'lps' ); ?> = ▼, <?php esc_attr_e( 'ascending', 'lps' ); ?> = ▲
										</td>
									</tr>
								</table>
							</div>

							<div id="lps_filter_tax_wrapper" class="settings-block">
								<h3><?php esc_html_e( 'Filter By Taxonomy', 'lps' ); ?></h3><hr>
								<table>
									<tr>
										<th><?php esc_html_e( 'Taxonomy', 'lps' ); ?></th>
										<td>
											<select name="lps_taxonomy" id="lps_taxonomy" data-default="" onchange="lpsRefresh()">
												<option value=""><?php esc_html_e( 'any', 'lps' ); ?></option>
												<?php if ( ! empty( $the_tax ) ) : ?>
													<?php foreach ( $the_tax as $k => $v ) : ?>
														<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v->labels->name ); ?> (<?php echo esc_attr( $k ); ?>)</option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Term', 'lps' ); ?></th>
										<td>
											<input type="text" name="lps_term" id="lps_term" placeholder="<?php esc_attr_e( 'Term slug (ex: news)', 'lps' ); ?>" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
											<label class="wide">
												<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_term_strict" value="term_strict" onclick="lpsRefresh()" class="lps_show_extra">
												<?php esc_html_e( 'exclude children', 'lps' ); ?>
											</label>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Taxonomy', 'lps' ); ?> 2</th>
										<td>
											<select name="lps_taxonomy2" id="lps_taxonomy2" data-default="" onchange="lpsRefresh()">
												<option value=""><?php esc_html_e( 'any', 'lps' ); ?></option>
												<?php if ( ! empty( $the_tax ) ) : ?>
													<?php foreach ( $the_tax as $k => $v ) : ?>
														<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v->labels->name ); ?> (<?php echo esc_attr( $k ); ?>)</option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Term', 'lps' ); ?> 2</th>
										<td>
											<input type="text" name="lps_term2" id="lps_term2"  placeholder="<?php esc_attr_e( 'Term slug (ex: news)', 'lps' ); ?>" onchange="lpsRefresh()" onkeyup="lpsRefresh()">

											<label class="wide">
												<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_term2_strict" value="term2_strict" onclick="lpsRefresh()" class="lps_show_extra">
												<?php esc_html_e( 'exclude children', 'lps' ); ?>
											</label>
										</td>
									</tr>
								</table>
							</div>

							<div id="lps_filter_tag_wrapper" class="settings-block">
								<h3><?php esc_html_e( 'Filter By Tag', 'lps' ); ?></h3><hr>
								<table>
									<tr>
										<th><?php esc_html_e( 'Tag', 'lps' ); ?></b></th>
										<td><input type="text" name="lps_tag" id="lps_tag" onchange="lpsRefresh()" onkeyup="lpsRefresh()"></td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Dynamic', 'lps' ); ?></th>
										<td>
											<select name="lps_dtag" id="lps_dtag" data-default="" onchange="lpsRefresh()">
												<option value="">
													<?php esc_html_e( 'no', 'lps' ); ?>,
													<?php esc_html_e( 'use the selected ones', 'lps' ); ?>
												</option>
												<option value="yes">
													<?php esc_html_e( 'yes', 'lps' ); ?>,
													<?php esc_html_e( 'use the current post tags', 'lps' ); ?>
												</option>
											</select>
										</td>
									</tr>
								</table>
							</div>

							<div class="settings-block">
								<h3><?php esc_html_e( 'Search & Archive Filter', 'lps' ); ?></h3><hr>
								<table>
									<tbody id="lps_search_wrapper" class="lps-update-blink">
										<tr>
											<th><?php esc_html_e( 'Search Key', 'lps' ); ?></b></th>
											<td><input type="text" name="lps_search" id="lps_search" onchange="lpsRefresh()" onkeyup="lpsRefresh()"></td>
										</tr>
									</tbody>
									<tbody id="lps_archive_wrapper" class="lps-update-blink">
										<tr>
											<th><?php esc_html_e( 'Use as Archive', 'lps' ); ?></th>
											<td>
												<select name="lps_archive" id="lps_archive" data-default="" onchange="lpsRefresh()">
													<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
													<option value="yes">
														<?php esc_html_e( 'yes', 'lps' ); ?>,
														<?php esc_html_e( 'use the current search key or taxonomy term', 'lps' ); ?>
													</option>
												</select>
												<div id="lps_archive_comment_wrapper">
													<p class="comment lps-update-blink"><?php esc_html_e( 'If you enable this option, the rest of taxonomies filters will not apply. This option is only intended to mimic the native archives (categories, tags, etc.) or the search result. If you are using pagination, the number of posts per page is inherited from the site reading settings.', 'lps' ); ?>
													</p>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="settings-block">
								<h3><?php esc_html_e( 'Filter By Specific IDs', 'lps' ); ?></h3><hr>
								<table>
									<tr>
										<th><?php esc_html_e( 'Post ID', 'lps' ); ?></th>
										<td>
											<input type="text" name="lps_post_id" id="lps_post_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Separate IDs with comma', 'lps' ); ?>"><p class="comment"><?php esc_attr_e( 'Show only objects with the selected IDs.', 'lps' ); ?></p></td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Parent ID', 'lps' ); ?></th>
										<td>
											<select name="lps_dparent" id="lps_dparent" data-default="" onchange="lpsRefresh()" class="lps-update-blink">
												<option value="">
													<?php esc_html_e( 'static', 'lps' ); ?>,
													<?php esc_html_e( 'use the specified IDs', 'lps' ); ?>
												</option>
												<option value="yes">
													<?php esc_html_e( 'dynamic', 'lps' ); ?>,
													<?php esc_html_e( 'use the current post attributes', 'lps' ); ?>
												</option>
											</select>

											<input type="text" name="lps_parent_id" id="lps_parent_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Separate IDs with comma', 'lps' ); ?>">

											<p class="comment"><?php esc_attr_e( 'Show only objects with specific parents.', 'lps' ); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Author ID', 'lps' ); ?></th>
										<td>
											<select name="lps_dauthor" id="lps_dauthor" data-default="" onchange="lpsRefresh()" class="lps-update-blink">
												<option value="">
													<?php esc_html_e( 'static', 'lps' ); ?>,
													<?php esc_html_e( 'use the specified IDs', 'lps' ); ?>
												</option>
												<option value="yes">
													<?php esc_html_e( 'dynamic', 'lps' ); ?>
													<?php esc_html_e( 'use the current post attributes', 'lps' ); ?>
												</option>
											</select>

											<input type="text" name="lps_author_id" id="lps_author_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Separate IDs with comma', 'lps' ); ?>">
											<p class="comment"><?php esc_attr_e( 'Show only objects with specific authors.', 'lps' ); ?></p>
										</td>
									</tr>
								</table>
							</div>

							<div class="settings-block">
								<h3><?php esc_html_e( 'Exclude Content', 'lps' ); ?></h3><hr>
								<table>
									<tr>
										<th><?php esc_html_e( 'Current', 'lps' ); ?></th>
										<td>
											<label class="wide"><input type="checkbox" name="lps_show_extra_current_id" id="lps_show_extra_current_id" value="current_id" checked="checked" disabled="disabled" readonly="readonly"> <?php esc_html_e( 'the current post', 'lps' ); ?></label>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'Dynamic', 'lps' ); ?></th>
										<td>
											<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_exclude_previous_content" value="exclude_previous_content" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'previous shortcodes*', 'lps' ); ?></label>
											<p class="comment"><?php esc_html_e( '* The exclude content dynamic option will filter the content so that the posts that were already embedded by previous shortcodes on this page will not show up (so that the content does not repeat).', 'lps' ); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'By Post ID', 'lps' ); ?></th>
										<td>
											<input type="text" name="lps_excludepost_id" id="lps_excludepost_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Separate IDs with comma', 'lps' ); ?>">
											<p class="comment"><?php esc_attr_e( 'Exclude the objects with the selected IDs.', 'lps' ); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'By Author ID', 'lps' ); ?></th>
										<td>
											<input type="text" name="lps_excludeauthor_id" id="lps_excludeauthor_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Separate IDs with comma', 'lps' ); ?>">
											<p class="comment"><?php esc_attr_e( 'Exclude the objects with the selected author IDs.', 'lps' ); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'By Tags', 'lps' ); ?></th>
										<td>
											<input type="text" name="lps_exclude_tags" id="lps_exclude_tags" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Separate slugs with comma', 'lps' ); ?>">
											<p class="comment"><?php esc_attr_e( 'Exclude the objects with the selected tags.', 'lps' ); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e( 'By Categories', 'lps' ); ?></th>
										<td>
											<input type="text" name="lps_exclude_categories" id="lps_exclude_categories" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Separate slugs with comma', 'lps' ); ?>">
											<p class="comment"><?php esc_attr_e( 'Exclude the objects with the selected categories.', 'lps' ); ?></p>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<hr class="sep">
						<div id="tabs-2" class="settings-group">
							<h1>3. <?php esc_html_e( 'Limit & Pagination', 'lps' ); ?></h1>
							<div class="settings-block">
								<h3><?php esc_html_e( 'Posts Limit', 'lps' ); ?></h3><hr>
								<table>
									<tr>
										<th><?php esc_html_e( 'Number of Posts', 'lps' ); ?></th>
										<td>
											<input type="number" name="lps_limit" id="lps_limit" value="" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="5">
											<p class="comment"><?php esc_html_e( 'This is the maximum number of posts the shortcode will expose.', 'lps' ); ?></p>
										</td>
									</tr>
								</table>

								<h3><?php esc_html_e( 'Date Limit', 'lps' ); ?></h3><hr>
								<table class="fixed">
									<tr>
										<th><?php esc_html_e( 'Date Limit Type', 'lps' ); ?></th>
										<td colspan="4">
											<select name="lps_date_limit" id="lps_date_limit" data-default="" onchange="lpsRefresh()">
												<option value=""><?php esc_html_e( 'date range', 'lps' ); ?></option>
												<option value="1"><?php esc_html_e( 'dynamic date', 'lps' ); ?></option>
											</select>
										</td>
									</tr>
									<tbody id="lps_date_limit_options_0" class="lps-update-blink">
										<tr>
											<th></th>
											<td colspan="2"><?php esc_html_e( 'published after', 'lps' ); ?></td>
											<td colspan="2">
												<input type="date" name="lps_date_after" id="lps_date_after" value="" onchange="lpsRefresh()">
											</td>
										</tr>
										<tr>
											<th></th>
											<td colspan="2"><?php esc_html_e( 'published before', 'lps' ); ?></td>
											<td colspan="2">
												<input type="date" name="lps_date_before" id="lps_date_before" value="" onchange="lpsRefresh()">
											</td>
										</tr>
									</tbody>
									<tbody id="lps_date_limit_options_1" class="lps-update-blink">
										<tr>
											<th></th>
											<td><?php esc_html_e( 'since', 'lps' ); ?></td>
											<td>
												<input type="number" name="lps_date_start" id="lps_date_start" value="0" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="2">
											</td>
											<td colspan="2">
												<select name="lps_date_start_type" id="lps_date_start_type" data-default="" onchange="lpsRefresh()">
													<?php foreach ( self::$date_limit_units as $k => $v ) : ?>
														<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?> </option>
													<?php endforeach; ?>
												</select>
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="settings-block">
								<h3><?php esc_html_e( 'Pagination Settings', 'lps' ); ?></h3><hr>
								<div class="block-use available-for-tiles">
									<table>
										<tr>
											<th><?php esc_html_e( 'Use Pagination', 'lps' ); ?></th>
											<td>
												<select name="lps_use_pagination" id="lps_use_pagination" data-default="" onchange="lpsRefresh()">
													<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
													<option value="yes">
														<?php esc_html_e( 'yes', 'lps' ); ?>,
														<?php esc_html_e( 'paginate results', 'lps' ); ?>
													</option>
												</select>
												<div id="lps_pagination_limit">
													<p class="comment lps-update-blink"><?php esc_html_e( 'Please note that paginated items are limited to the number of posts specified above. If you do not want to limit the result, just remove the value from the number of posts.', 'lps' ); ?></p>
												</div>
											</td>
										</tr>
									</table>

									<div id="lps_pagination_options">
										<table>
											<tr>
												<th><?php esc_html_e( 'Records Per Page', 'lps' ); ?></th>
												<td>
													<input type="text" name="lps_per_page" id="lps_per_page" value="0" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="5">
												</td>
											</tr>
											<tr>
												<th><?php esc_html_e( 'Offset', 'lps' ); ?></th>
												<td>
													<input type="text" name="lps_offset" id="lps_offset" value="0" onchange="lpsRefresh()" onkeyup="lpsRefresh()" size="5">
												</td>
											</tr>
											<tr>
												<th><?php esc_html_e( 'Visibility', 'lps' ); ?></th>
												<td>
													<select name="lps_showpages" id="lps_showpages" data-default="" onchange="lpsRefresh()">
														<option value="">
															<?php esc_html_e( 'hide navigation', 'lps' ); ?>
														</option>
														<option value="1">
															<?php esc_html_e( 'show navigation', 'lps' ); ?>
															(<?php esc_html_e( 'prev / next', 'lps' ); ?>)
														</option>
														<option value="4">
															<?php esc_html_e( 'show navigation', 'lps' ); ?>
															(<?php esc_html_e( 'range of 4', 'lps' ); ?>)
														</option>
														<option value="5">
															<?php esc_html_e( 'show navigation', 'lps' ); ?>
															(<?php esc_html_e( 'range of 5', 'lps' ); ?>)
														</option>
														<option value="10">
															<?php esc_html_e( 'show navigation', 'lps' ); ?>
															(<?php esc_html_e( 'range of 10', 'lps' ); ?>)
														</option>
														<option value="more">
															<?php esc_html_e( 'show navigation', 'lps' ); ?>
															(<?php esc_html_e( 'load more button', 'lps' ); ?>)
														</option>
														<option value="scroll">
															<?php esc_html_e( 'infinite scroll', 'lps' ); ?>
															(<?php esc_html_e( 'load more on scroll', 'lps' ); ?>)
														</option>
													</select>
												</td>
											</tr>
											<tbody id="lps_showpages_options">
												<tr>
													<th><?php esc_html_e( 'Load More Text', 'lps' ); ?></th>
													<td>
														<input type="text" name="lps_loadtext" id="lps_loadtext" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_html_e( 'Custom \'Load more\' button text', 'lps' ); ?>" value="<?php esc_html_e( 'Load more', 'lps' ); ?>" size="32">
														<p class="comment lps-update-blink"><?php esc_html_e( 'This is the text that will be displayed on the button on the front-end. Do not use brackets for the custom load more message, these are shortcodes delimiters.', 'lps' ); ?></p>
													</td>
												</tr>
											</tbody>
											<tr>
												<th><?php esc_html_e( 'Position', 'lps' ); ?></th>
												<td>
													<select name="lps_showpages_pos" id="lps_showpages_pos" onchange="lpsRefresh()">
														<option value=""><?php esc_html_e( 'above the results', 'lps' ); ?></option>
														<option value="1"><?php esc_html_e( 'below the results', 'lps' ); ?></option>
														<option value="2"><?php esc_html_e( 'above & below the result', 'lps' ); ?></option>
													</select>
												</td>
											</tr>
											<tr>
												<th><?php esc_html_e( 'AJAX Pagination', 'lps' ); ?></th>
												<td>
													<label><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_ajax_pagination" value="ajax_pagination" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'yes', 'lps' ); ?></label>

													<select name="lps_show_extra[]" id="lps_show_extra_spinner" data-default="" onchange="lpsRefresh()" class="lps_show_extra">
														<option value=""><?php esc_html_e( 'no spinner', 'lps' ); ?></option>
														<option value="light_spinner"><?php esc_html_e( 'light spinner', 'lps' ); ?></option>
														<option value="dark_spinner"><?php esc_html_e( 'dark spinner', 'lps' ); ?></option>
													</select>
												</td>
											</tr>
											<tr>
												<th><?php esc_html_e( 'Pagination Style', 'lps' ); ?></th>
												<td>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_pagination_all" value="pagination_all" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'all pagination elements', 'lps' ); ?> </label>
													<p class="comment"><?php esc_html_e( 'Tick this option if you need to display the pagination elements all the time, including the disabled elements like: go to first, previous, next, and last page, even if these are disabled.', 'lps' ); ?></p>

													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_show_total" value="show_total" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'show total items', 'lps' ); ?></label>
													<div id="lps_show_total_options" class="lps-update-blink">
														<input type="text" name="lps_total_text" id="lps_total_text" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php /* Translators: %d - total value. */ esc_html_e( 'Custom \'Total items: %d\' text', 'lps' ); ?>" value="<?php esc_html_e( 'Total items: %d', 'lps' ); ?>" size="32">
														<p class="comment"><?php /* Translators: %d - total value. */ esc_html_e( 'Write the total items text, %d will be replaced by the total value. Leave empty for the default.', 'lps' ); ?></p>
													</div>
												</td>
											</tr>
										</table>
									</div>
								</div>
								<div class="block-use available-for-slider">
									<p class="comment lps-update-blink"><?php esc_html_e( 'The pagination is not available for sliders, only for list/tiles output.', 'lps' ); ?></p>
								</div>
							</div>
						</div>
						<hr class="sep">
						<div id="tabs-3" class="settings-group">
							<h1>4. <?php esc_html_e( 'Display Settings', 'lps' ); ?></h1>

							<?php
							// Introduce the slider extension options.
							self::output_slider_configuration();
							?>

							<div class="settings-block">
								<h3><?php esc_html_e( 'Post Appearance', 'lps' ); ?></h3><hr>
								<div class="block-use available-for-tiles">
									<table>
										<tr>
											<th><?php esc_html_e( 'Display Post', 'lps' ); ?></th>
											<td>
												<select name="lps_display" id="lps_display" data-default="title" onchange="lpsRefresh()">
													<?php foreach ( $display_posts_list as $k => $v ) : ?>
														<?php
														$key = array_keys( self::$tile_pattern, '[' . $k . ']', true );
														if ( ! empty( $key ) ) {
															$key = reset( $key );
														} else {
															$key = '';
														}
														?>
														<option value="<?php echo esc_attr( $k ); ?>" data-template-id="<?php echo esc_attr( $key ); ?>" <?php selected( 'title', $k ); ?>><?php echo esc_html( $v ); ?> </option>
													<?php endforeach; ?>
												</select>
											</td>
										</tr>
									</table>
								</div>
								<div id="lps_display_titletag">
									<table>
										<tr>
											<th><?php esc_html_e( 'Title Wrap', 'lps' ); ?></th>
											<td>
												<select name="lps_titletag" id="lps_titletag" data-default="h3" onchange="lpsRefresh()">
													<?php foreach ( self::$title_tags as $tt ) : ?>
														<option value="<?php echo esc_attr( $tt ); ?>"><?php echo esc_html( $tt ); ?></option>
													<?php endforeach; ?>
												</select>
												<p class="comment lps-update-blink"><?php esc_html_e( 'This is the HTML tag used to wrap the post title in the output (defaults to h3).', 'lps' ); ?></p>
											</td>
										</tr>
									</table>
								</div>
								<div id="lps_display_limit">
									<table>
										<tr>
											<th><?php esc_html_e( 'Chars Limit', 'lps' ); ?></th>
											<td>
												<input type="text" name="lps_chrlimit" id="lps_chrlimit" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="Ex: 120" value="120" size="5">
												<p class="comment lps-update-blink"><?php esc_html_e( 'Maximum number of chars from excerpt / content to be displayed (the text will be truncated, but will not break words).', 'lps' ); ?></p>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Trim type', 'lps' ); ?></th>
											<td>
												<label class="wide"><input type="checkbox" name="lps_show_extra_trim[]" id="lps_show_extra_trim" value="trim" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'limit the title and text together', 'lps' ); ?></label>
												<p class="comment lps-update-blink"><?php esc_html_e( 'Apply the chars limit to title and excerpt/content together (the excerpt/content length will be computed by subtracting the title length from the chars limit).', 'lps' ); ?></p>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'More Suffix', 'lps' ); ?></th>
											<td>
												<input type="text" name="lps_more" id="lps_more" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="Ex: …" value="">
												<p class="comment lps-update-blink"><?php esc_html_e( 'The extra chars to be appended at the end of the trimmed strings.', 'lps' ); ?></p>
											</td>
										</tr>
									</table>
								</div>
								<div id="lps_display_raw">
									<table>
										<tr>
											<th><?php esc_html_e( 'Raw Content', 'lps' ); ?></th>
											<td>
												<label class="wide"><input type="checkbox" name="lps_show_extra_raw[]" id="lps_show_extra_raw" value="raw" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'show raw content', 'lps' ); ?></label>
												<p class="comment lps-update-blink"><?php esc_html_e( 'This option is forcing the content output without stripping the markup. This might produce unexpected content layout on the front end, use wisely.', 'lps' ); ?></p>
											</td>
										</tr>
									</table>
								</div>
								<div id="lps_display_date_diff">
									<table>
										<tr>
											<th><?php esc_html_e( 'Date Option', 'lps' ); ?></th>
											<td>
												<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_date_diff" value="date_diff" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'as date difference', 'lps' ); ?></label>
												<p class="comment lps-update-blink"><?php esc_html_e( 'If you check this option, the date for the tile (if that is included in the tile format) will display in date difference format (like 2 hours ago or 1 day ago, etc.).', 'lps' ); ?></p>
											</td>
										</tr>
									</table>
								</div>

								<div id="lps_url_wrap">
									<table>
										<tr>
											<th><?php esc_html_e( 'Use Post URL', 'lps' ); ?></th>
											<td>
												<select name="lps_url" id="lps_url" data-default="" onchange="lpsRefresh()">
													<option value="">
														<?php esc_html_e( 'no link to the post', 'lps' ); ?>
													</option>
													<option value="yes">
														<?php esc_html_e( 'link to the post', 'lps' ); ?>
													</option>
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
												<p id="lps_url_options" class="comment lps-update-blink"><?php esc_html_e( 'See below the available tile patterns and select to one you want.', 'lps' ); ?></p>
											</td>
										</tr>
									</table>
									<div id="lps_url_options_read">
										<table>
											<tr>
												<th><?php esc_html_e( 'Custom \'Read more\' message', 'lps' ); ?></th>
												<td>
													<input type="text" name="lps_linktext" id="lps_linktext" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_html_e( 'Custom \'Read more\' message', 'lps' ); ?>" size="32">
													<p class="comment lps-update-blink"><?php esc_html_e( 'Do not use brackets for the custom read more message, these are shortcodes delimiters.', 'lps' ); ?></p>
												</td>
											</tr>
										</table>
									</div>
									<div class="block-use available-for-tiles">
										<div id="lps_lightbox_options" class="lps-experimental lps-update-blink">
											<table>
												<tr>
													<th><?php esc_html_e( 'Lightbox Attributes', 'lps' ); ?></th>
													<td colspan="2">
														<p class="comment"><?php esc_html_e( 'If you want to use a lightbox for the images, you can setup below the image size to be available in the lightbox and the selector.', 'lps' ); ?></p>
													</td>
												</tr>
												<tr>
													<th><?php esc_html_e( 'Lightbox Image', 'lps' ); ?></th>
													<td colspan="2">
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
													</td>
												</tr>
												<tr>
													<th><?php esc_html_e( 'Selector attribute and value', 'lps' ); ?></th>
													<td>
														<input type="text" name="lps_lightbox_attr" id="lps_lightbox_attr" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_html_e( 'Ex: class', 'lps' ); ?>" size="32">
													</td>
													<td>
														<input type="text" name="lps_lightbox_val" id="lps_lightbox_val" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_html_e( 'Ex: fancybox image', 'lps' ); ?>" size="32">
													</td>
												</tr>
												<tr>
													<th></th>
													<td colspan="2">
														<p class="comment"><?php esc_html_e( 'This feature has been tested and is recommended to be used with Easy FancyBox plugin (>=1.8) or FooBox Image Lightbox plugin (>=2.6).', 'lps' ); ?></p>
													</td>
												</tr>
											</table>
										</div>
									</div>
								</div>
								<?php $app_sizes = get_intermediate_image_sizes(); ?>
								<div id="lps_image_wrap">
									<table>
										<tr>
											<th><?php esc_html_e( 'Use Image', 'lps' ); ?></th>
											<td>
												<select name="lps_image" id="lps_image" data-default="" onchange="lpsRefresh()">
													<option value="">
														<?php esc_html_e( 'no', 'lps' ); ?>
													</option>
													<?php if ( ! empty( $app_sizes ) ) : ?>
														<?php foreach ( $app_sizes as $as ) : ?>
															<option value="<?php echo esc_attr( $as ); ?>"><?php echo esc_html( $as ); ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
													<option value="full">
														<?php esc_html_e( 'full (original size)', 'lps' ); ?>
													</option>
												</select>
											</td>
										</tr>
									</table>
									<div id="lps_image_placeholder_wrap" class="lps-update-blink">
										<table>
											<tr>
												<th><?php esc_html_e( 'Image Placeholder', 'lps' ); ?></th>
												<td>
													<input type="text" name="lps_image_placeholder" id="lps_image_placeholder" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
													<p class="comment"><?php esc_html_e( 'Define an image to be used for the posts that do not have a featured image.', 'lps' ); ?> <?php esc_html_e( 'If you specify a list of images separated by comma, a random one from the list will be picked for each article that does not have a featured image.', 'lps' ); ?> <?php esc_html_e( 'If you want to use the plugin default placeholder, input the word `auto`.', 'lps' ); ?></p>
												</td>
											</tr>
										</table>
									</div>
									<div id="lps_fallback_wrap">
										<table>
											<tr>
												<th><?php esc_html_e( 'Content Fallback', 'lps' ); ?></th>
												<td>
													<input type="text" name="lps_fallback" id="lps_fallback" onchange="lpsRefresh()" onkeyup="lpsRefresh()">
													<p class="comment"><?php esc_html_e( 'Add a custom text to be displayed if no content matches the settings.', 'lps' ); ?></p>
												</td>
											</tr>
										</table>
									</div>
								</div>

								<div class="block-use available-for-tiles">
									<table>
										<tr>
											<th><?php esc_html_e( 'Tile Pattern', 'lps' ); ?></th>
											<td>
												<div id="tile_description_wrap" class="lps-update-blink"><?php esc_html_e( 'The icons suggest a default order of the HTML tags, the links are marked with red.', 'lps' ); ?></div>
												<div id="custom_tile_description_wrap" class="lps-update-blink"><?php esc_html_e( 'You are using a custom output, the markup is handled programatically, in your custom code.', 'lps' ); ?></div>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<input type="hidden" name="lps_elements" id="lps_elements" value="0" onchange="lpsRefresh()">
												<div class="as-grid cols4">
													<?php
													foreach ( self::$tile_pattern as $k => $p ) :
														$cl  = ( in_array( $k, self::$tile_pattern_links, true ) ) ? 'with-link' : 'without-link';
														$cl .= ( in_array( $k, self::$tile_pattern_ver2, true ) ) ? ' ver2' : '';
														$cl  = ( self::tile_markup_is_custom( $p ) ) ? 'custom-type wide' : $cl;
														?>
														<label class="<?php echo esc_attr( $cl ); ?> lps-update-blink" onclick="LPS_generator.updateElements('<?php echo esc_attr( $k ); ?>');">
															<?php if ( self::tile_markup_is_custom( $p ) ) : ?>
																<input type="radio" name="lps_elements_img" id="lps_elements_img_<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $k ); ?>" readonly="readonly">
																<span><?php echo esc_html( $display_posts_list[ str_replace( ']', '', str_replace( '[', '', $p ) ) ] ); ?> <?php esc_html_e( 'markup', 'lps' ); ?></span>
															<?php else : ?>
																<img src="<?php echo esc_url( LPS_PLUGIN_URL . 'assets/images/tiles/' . esc_attr( $k ) . '.png' ); ?>"
																	title="<?php echo esc_attr( str_replace( '[a-r]', '[a]', $p ) ); ?>">
																<input type="radio" name="lps_elements_img" id="lps_elements_img_<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $k ); ?>">
															<?php endif; ?>
														</label>
													<?php endforeach; ?>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<hr class="sep">
						<div id="tabs-4" class="settings-group">
							<h1>5. <?php esc_html_e( 'Extra Options', 'lps' ); ?></h1>
							<div class="block-use available-for-tiles">
								<div class="settings-block"><p class="comment"><?php esc_html_e( 'Please note that if you are using a custom output template defined in your theme, the author, the taxonomies and the tags extra options will not function, since your custom template is overriding the output and the default behavior.', 'lps' ); ?></p></div>

								<?php
								$ttax   = wp_list_pluck( $the_tax, 'label', 'name' );
								$alltax = [
									'author'            => esc_html__( 'Author', 'lps' ),
									'caption'           => esc_html__( 'Caption', 'lps' ) . ' ' . esc_html__( '(only for attachments)', 'lps' ),
									'show_mime'         => esc_html__( 'Mime Type', 'lps' ) . ' ' . esc_html__( '(only for attachments)', 'lps' ),
									'price'             => esc_html__( 'Price', 'lps' ) . ' ' . esc_html__( '(only for products)', 'lps' ),
									'add_to_cart'       => esc_html__( 'Add to cart', 'lps' ) . ' ' . esc_html__( '(only for products)', 'lps' ),
									'price_add_to_cart' => esc_html__( 'Price + Add to cart', 'lps' ) . ' ' . esc_html__( '(only for products)', 'lps' ),
								];
								if ( ! empty( $ttax ) ) {
									$alltax = array_merge( $alltax, $ttax );
								}

								$alltax['tags'] = esc_html__( 'Tags', 'lps' );
								?>
								<?php if ( ! empty( $alltax ) ) : ?>
									<?php foreach ( $alltax as $slug => $name ) : ?>
										<?php
										$theslug = ( ! in_array( $slug, [ 'author', 'caption', 'show_mime', 'price', 'add_to_cart', 'price_add_to_cart' ], true ) ) ? '(' . $slug . ')' : '';
										?>
										<div id="lps-extra-<?php echo esc_html( $slug ); ?>" class="settings-block-item terms-options">
											<h3>
												<label class="title wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra lps-is-taxonomy">
												<?php echo esc_html( $name ); ?></label>
											</h3>
											<div id="lps_show_extra_<?php echo esc_attr( $slug ); ?>_pos_wrap" class="extra-options-wrap lps-update-blink">
												<?php if ( 'author' === $slug ) : ?>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolabel_<?php echo esc_attr( $slug ); ?>" value="nolabel_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra"> <b><?php esc_html_e( 'hide the label', 'lps' ); ?></b></label>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolink_<?php echo esc_attr( $slug ); ?>" value="nolink_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra"> <b><?php esc_html_e( 'no link for the author', 'lps' ); ?></b></label><hr>
												<?php endif; ?>
												<?php if ( 'category' === $slug ) : ?>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_hide_uncategorized_<?php echo esc_attr( $slug ); ?>" value="hide_uncategorized_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra"> <b><?php esc_html_e( 'do not display Uncategorized term', 'lps' ); ?></b></label>
												<?php endif; ?>
												<?php if ( 'show_mime' === $slug ) : ?>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolabel_<?php echo esc_attr( $slug ); ?>" value="nolabel_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra"> <b><?php esc_html_e( 'hide the label', 'lps' ); ?></b></label><hr>
												<?php endif; ?>
												<?php if ( ! empty( $theslug ) ) : ?>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_oneterm_<?php echo esc_attr( $slug ); ?>" value="oneterm_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra"> <b><?php esc_html_e( 'show only one term', 'lps' ); ?></b></label>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolabel_<?php echo esc_attr( $slug ); ?>" value="nolabel_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra"> <b><?php esc_html_e( 'hide the taxonomy name from the list', 'lps' ); ?></b></label>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_nolink_<?php echo esc_attr( $slug ); ?>" value="nolink_<?php echo esc_attr( $slug ); ?>" onclick="lpsRefresh();" class="lps_show_extra"> <b><?php esc_html_e( 'no link for the terms', 'lps' ); ?></b></label>
													<hr>
												<?php endif; ?>
												<b><?php esc_html_e( 'Display position', 'lps' ); ?></b>
												<hr>
												<label class="show-options"><input type="radio" name="lps_show_extra_pos_<?php echo esc_attr( $slug ); ?>" id="lps_show_extra_taxpos_<?php echo esc_attr( $slug ); ?>_default" value="" checked="checked" onclick="lpsRefresh();" class="lps_show_extra"><?php esc_html_e( 'default', 'lps' ); ?>,</label>
												<?php foreach ( self::$tax_positions as $pos => $pos_title ) : ?>
													<label class="show-options"><input type="radio" name="lps_show_extra_pos_<?php echo esc_attr( $slug ); ?>" id="lps_show_extra_taxpos_<?php echo esc_attr( $slug ); ?>_<?php echo esc_attr( $pos ); ?>" value="taxpos_<?php echo esc_attr( $slug ); ?>_<?php echo esc_attr( $pos ); ?>" onclick="lpsRefresh();" class="lps_show_extra"><?php echo esc_html( $pos_title ); ?>,</label>
												<?php endforeach; ?>
											</div>
										</div>
									<?php endforeach; ?>

									<div class="settings-block">
										<table>
											<tr>
												<th><?php esc_html_e( 'Mime Type', 'lps' ); ?></th>
												<td>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_show_mime_class" value="show_mime_class" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'show mime type as CSS class', 'lps' ); ?> </label>
													<p class="comment"><?php esc_html_e( 'The extra options will apply only to attachment post type.', 'lps' ); ?></p>
												</td>
											</tr>
											<tr>
												<th><?php esc_html_e( 'Line Break', 'lps' ); ?></th>
												<td>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_clearall" value="linebreak" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'clear the content below', 'lps' ); ?> </label>
													<p class="comment"><?php esc_html_e( 'The extra options will clear the content below by adding a line break after the shorcode.', 'lps' ); ?></p>
												</td>
											</tr>

										</table>
									</div>
								<?php endif; ?>
							</div>

							<div class="settings-block">
								<h3><?php esc_html_e( 'Cache', 'lps' ); ?></h3><hr>
								<table>
									<tr>
										<th><?php esc_html_e( 'Shortcode Cache', 'lps' ); ?></th>
										<td>
											<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_cache" value="cache" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'cache the shortcode result', 'lps' ); ?> </label>
											<p class="comment"><?php esc_html_e( 'The cache can help you speed up the page load. The default duration of the shortcode cache is 30 days. If you need to reset the shortcode cache, use the reset button.', 'lps' ); ?></p>
										</td>
									</tr>
								</table>
							</div>

							<div class="settings-block">
								<h3><?php esc_html_e( 'Style', 'lps' ); ?></h3><hr>
								<p class="comment block-use available-for-tiles">
									<?php
									echo esc_html(
										sprintf(
											// Translators: %1$s - class, %2$s - class, %3$s - class, %4$s - class, %5$s - class, %6$s - class, %7$s - class, %8$s - class.
											__( 'Currently, the plugin offers out of the box support for two, three, four, five and six columns for the tiles and the overlay option. If, for example, you would like to display the tiles as four columns and the images as backgrounds, you can use `%1$s`. The content of the tiles can be aligned with `%6$s`, `%7$s` or `%8$s`. Extra options for the overlay usage: `%2$s` (for the overlay color), `%3$s` (to make the height of the tiles a bit bigger, so it fits more content). For the pagination alignment, there are two CSS classes that allow to center or align right the element: `%4$s` and `%5$s`.', 'lps' ),
											'four-columns as-overlay',
											'light',
											'tall',
											'pagination-center',
											'pagination-right',
											'align-left',
											'align-center',
											'align-right'
										)
									);
									?>
								</p>
								<hr>
								<table>
									<tr>
										<th><?php esc_html_e( 'CSS Class', 'lps' ); ?></th>
										<td>
											<input type="text" name="lps_css" id="lps_css" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Ex: two-columns, three-columns, four-columns', 'lps' ); ?>" size="32">
											<p class="comment"><?php esc_html_e( 'The CSS class/classes you can use to customize the appearance of the shortcode output.', 'lps' ); ?></p>
										</td>
									</tr>
								</table>

								<div class="block-use available-for-tiles">
									<table>
										<tr>
											<th><?php esc_html_e( 'Columns Helper', 'lps' ); ?></th>
											<td>
												<select id="lps_style_helper_columns" data-default="" onchange="lpsStyleHelper()">
													<option value="one-column">1</option>
													<option value="two-columns">2</option>
													<option value="three-columns">3</option>
													<option value="four-columns">4</option>
													<option value="five-columns">5</option>
													<option value="six-columns">6</option>
												</select>
											</td>
										</tr>
										<tr>
											<?php $card_styles = self::get_card_output_types(); ?>
											<th><?php esc_html_e( 'Output Helper', 'lps' ); ?></th>
											<td>
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
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Scroller', 'lps' ); ?></th>
											<td>
												<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_scroller" value="scroller" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'show as inline scroller', 'lps' ); ?></label>
												<p class="comment"><?php esc_html_e( 'This option enables the modern snap to grid inline scroller output.', 'lps' ); ?></p>

												<div id="lps_display_show_extra_with_counter" class="lps-update-blink">
													<div class="lps-experimental">
														<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_with_counter" value="with_counter" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'show counter on the card', 'lps' ); ?></label>
														<p class="comment"><?php esc_html_e( 'This option displays a small dynamic counter on top of the card.', 'lps' ); ?></p>
														<div id="lps_display_show_extra_reverse_counter">
															<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_reverse_counter" value="reverse_counter" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'use reverse order for the counter', 'lps' ); ?> </label>
														</div>
													</div>
													<label class="wide"><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_hide_more" value="hide_more" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'hide the load more button', 'lps' ); ?></label>
													<p class="comment"><?php esc_html_e( 'This option will hide the button when using infinite scroll type of pagination.', 'lps' ); ?></p>
												</div>
											</td>
										</tr>
									</table>

									<table id="lps_style_helper_pags_tr" class="lps-update-blink">
										<tr>
											<th><?php esc_html_e( 'Pagination alignment', 'lps' ); ?></th>
											<td>
												<select id="lps_style_helper_pags" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'left', 'lps' ); ?></option>
													<option value="pagination-center"><?php esc_html_e( 'center', 'lps' ); ?></option>
													<option value="pagination-right"><?php esc_html_e( 'right', 'lps' ); ?></option>
													<option value="pagination-space-between"><?php esc_html_e( 'space between', 'lps' ); ?></option>
												</select>
											</td>
										</tr>
									</table>
								</div>

								<div class="block-use available-for-tiles">
									<br>
									<hr>
									<h3><?php esc_html_e( 'Tiles Grid Options', 'lps' ); ?></h3>
									<hr>
									<p class="comment"><?php esc_html_e( 'These options allow to specify the gap between tiles and the height for these. Use px, %, vw or vh as units.', 'lps' ); ?> <?php esc_html_e( 'Leave empty if you want to use the defaults.', 'lps' ); ?></p>
									<table class="fixed">
										<tr>
											<th>
												<?php esc_html_e( 'Wide', 'lps' ); ?>
												(<?php esc_html_e( 'default', 'lps' ); ?>)
												<p class="comment">> 1024px</p>
											</th>
											<td>
												<?php esc_html_e( 'height', 'lps' ); ?>
												<input type="text" name="lps_default_height" id="lps_default_height" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td class="not-for-overlay lps-update-blink">
												<?php esc_html_e( 'padding', 'lps' ); ?>
												<input type="text" name="lps_default_padding" id="lps_default_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td class="for-overlay lps-update-blink">
												<?php esc_html_e( 'padding', 'lps' ); ?>
												<input type="text" name="lps_default_overlay_padding" id="lps_default_overlay_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td>
												<?php esc_html_e( 'gap', 'lps' ); ?>
												<input type="text" name="lps_default_gap" id="lps_default_gap" onchange="lpsRefresh()" placeholder="1rem" onkeyup="lpsRefresh()" size="32">
											</td>
										</tr>
										<tr>
											<th>
												<?php esc_html_e( 'Medium', 'lps' ); ?>
												<p class="comment">> 600px & <= 1024px</p>
											</th>
											<td>
												<?php esc_html_e( 'height', 'lps' ); ?>
												<input type="text" name="lps_tablet_height" id="lps_tablet_height" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td class="not-for-overlay lps-update-blink">
												<?php esc_html_e( 'padding', 'lps' ); ?>
												<input type="text" name="lps_tablet_padding" id="lps_tablet_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td class="for-overlay lps-update-blink">
												<?php esc_html_e( 'padding', 'lps' ); ?>
												<input type="text" name="lps_tablet_overlay_padding" id="lps_tablet_overlay_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td>
												<?php esc_html_e( 'gap', 'lps' ); ?>
												<input type="text" name="lps_tablet_gap" id="lps_tablet_gap" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
										</tr>
										<tr>
											<th>
												<?php esc_html_e( 'Small', 'lps' ); ?>
												<p class="comment"><= 600px</p>
											</th>
											<td>
												<?php esc_html_e( 'height', 'lps' ); ?>
												<input type="text" name="lps_mobile_height" id="lps_mobile_height" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td class="not-for-overlay lps-update-blink">
												<?php esc_html_e( 'padding', 'lps' ); ?>
												<input type="text" name="lps_mobile_padding" id="lps_mobile_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td class="for-overlay lps-update-blink">
												<?php esc_html_e( 'padding', 'lps' ); ?>
												<input type="text" name="lps_mobile_overlay_padding" id="lps_mobile_overlay_padding" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
											<td>
												<?php esc_html_e( 'gap', 'lps' ); ?>
												<input type="text" name="lps_mobile_gap" id="lps_mobile_gap" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="1rem" size="32">
											</td>
										</tr>
									</table>
									<hr>
								</div>
							</div>

							<div class="settings-block">
								<div class="block-use available-for-tiles">
									<h3><?php esc_html_e( 'Card options', 'lps' ); ?></h3><hr>
									<table class="fixed">
										<tr>
											<th><?php esc_html_e( 'Cleanup', 'lps' ); ?></th>
											<td colspan="6">
												<label class="wide">
													<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_reset_css" value="reset_css" onclick="lpsRefresh()" class="lps_show_extra"> <?php esc_html_e( 'keep only the core CSS classes when outputting the cards, and remove the third-party ones (added through `post_class` filter)', 'lps' ); ?>
												</label>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Aignment', 'lps' ); ?></th>
											<td colspan="3">
												<?php esc_html_e( 'horizontal', 'lps' ); ?>
												<select id="lps_style_helper_align" data-default="" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'left', 'lps' ); ?></option>
													<option value="align-center"><?php esc_html_e( 'center', 'lps' ); ?></option>
													<option value="align-right"><?php esc_html_e( 'right', 'lps' ); ?></option>
												</select>
											</td>
											<td colspan="3">
												<?php esc_html_e( 'vertical', 'lps' ); ?>
												<select id="lps_style_helper_valign" data-default="" onchange="lpsStyleHelper()">
													<option value="content-center"><?php esc_html_e( 'center', 'lps' ); ?></option>
													<option value="content-start"><?php esc_html_e( 'start', 'lps' ); ?></option>
													<option value="content-end"><?php esc_html_e( 'end', 'lps' ); ?></option>
													<option value="content-space-between"><?php esc_html_e( 'space between', 'lps' ); ?></option>
													<option value="content-auto"><?php esc_html_e( 'auto', 'lps' ); ?></option>
													<option value="content-first-top"><?php esc_html_e( 'first top', 'lps' ); ?></option>
													<option value="content-last-bottom"><?php esc_html_e( 'last bottom', 'lps' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Font Size', 'lps' ); ?></th>
											<td colspan="3">
												<?php esc_html_e( 'text', 'lps' ); ?>
												<input type="text" name="lps_size_text" id="lps_size_text" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
											</td>
											<td colspan="3">
												<?php esc_html_e( 'title', 'lps' ); ?>
												<input type="text" name="lps_size_title" id="lps_size_title" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
											</td>
										</tr>
										<tr>
											<th></th>
											<td colspan="6">
												<p class="comment"><?php esc_html_e( 'Ex: 1rem, 24px, 2em, clamp(1rem, 0.6rem + 1.25vw, 1.4rem), etc.', 'lps' ); ?> <?php esc_html_e( 'Use px, %, vw or vh as units.', 'lps' ); ?> <?php esc_html_e( 'Leave empty if you want to use the defaults.', 'lps' ); ?></p>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Colors', 'lps' ); ?></th>
											<td colspan="2">
												<?php esc_html_e( 'text', 'lps' ); ?>
												<div class="lps-color-wrapper">
													<input type="text" name="lps_color_text" id="lps_color_text" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
													<input type="color" id="lps_color_text_field" onchange="lpsRefreshColor(this)">
												</div>
											</td>
											<td colspan="2">
												<?php esc_html_e( 'title', 'lps' ); ?>
												<div class="lps-color-wrapper">
													<input type="text" name="lps_color_title" id="lps_color_title" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
													<input type="color" id="lps_color_title_field" onchange="lpsRefreshColor(this)">
												</div>
											</td>
											<td colspan="2">
												<?php esc_html_e( 'background', 'lps' ); ?>
												<div class="lps-color-wrapper">
													<input type="text" name="lps_color_bg" id="lps_color_bg" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?>" size="32">
													<input type="color" id="lps_color_bg_field" onchange="lpsRefreshColor(this)">
												</div>
											</td>
										</tr>
										<tr>
											<th></th>
											<td colspan="6">
												<p class="comment"><?php esc_html_e( 'Ex: #fff, rbga(255,255,255, 0.5), etc.', 'lps' ); ?>
												<?php esc_html_e( 'Leave empty if you want to use the defaults.', 'lps' ); ?> <?php esc_html_e( 'Also, if you want to apply the colors, you should remove the card generic aspect.', 'lps' ); ?></p>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Shadow', 'lps' ); ?></th>
											<td colspan="3">
												<?php esc_html_e( 'card shadow', 'lps' ); ?>
												<select id="lps_style_has_shadow" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
													<option value="has-shadow"><?php esc_html_e( 'yes', 'lps' ); ?></option>
												</select>
											</td>
											<td colspan="3">
												<?php esc_html_e( 'title shadow', 'lps' ); ?>
												<select id="lps_style_has_title_shadow" data-default="" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
													<option value="has-title-shadow"><?php esc_html_e( 'yes', 'lps' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Hover Effect', 'lps' ); ?></th>
											<td colspan="3">
												<?php esc_html_e( 'image zoom', 'lps' ); ?>
												<select id="lps_style_has_zoom" data-default="" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
													<option value="hover-zoom"><?php esc_html_e( 'yes', 'lps' ); ?></option>
												</select>
											</td>
											<td colspan="3">
												<?php esc_html_e( 'card highlight', 'lps' ); ?>
												<select id="lps_style_has_highlight" data-default="" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
													<option value="hover-highlight"><?php esc_html_e( 'yes', 'lps' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Title Options', 'lps' ); ?></th>
											<td colspan="3">
												<?php esc_html_e( 'no decoration', 'lps' ); ?>
												<select id="lps_style_has_title_nodecoration" data-default="" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'inherit', 'lps' ); ?></option>
													<option value="has-title-nodecoration"><?php esc_html_e( 'yes', 'lps' ); ?></option>
												</select>
											</td>
											<td colspan="3">
												<?php esc_html_e( 'uppercase', 'lps' ); ?>
												<select id="lps_style_has_title_uppercase" data-default="" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'inherit', 'lps' ); ?></option>
													<option value="has-title-uppercase"><?php esc_html_e( 'yes', 'lps' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Aspect', 'lps' ); ?></th>
											<td colspan="3">
												<?php esc_html_e( 'generic aspect', 'lps' ); ?>
												<select id="lps_style_has_aspect" data-default="" onchange="lpsStyleHelper()">
													<option value="">
														<?php esc_html_e( '-- unspecified --', 'lps' ); ?>
													</option>
													<option value="dark">
														<?php esc_html_e( 'dark', 'lps' ); ?>
													</option>
													<option value="light">
														<?php esc_html_e( 'light', 'lps' ); ?>
													</option>
													<option id="lps-option-clear-image" value="clear-image">
														<?php esc_html_e( 'no overlay', 'lps' ); ?>
													</option>
												</select>
											</td>
											<td colspan="3">
												<?php esc_html_e( 'border radius', 'lps' ); ?>
												<select id="lps_style_has_radius" data-default="" onchange="lpsStyleHelper()">
													<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
													<option value="has-radius"><?php esc_html_e( 'yes', 'lps' ); ?></option>
												</select>
											</td>
										</tr>
										<tbody id="lps_image_opacity_tr" class="lps-update-blink">
											<tr>
												<th><?php esc_html_e( 'Image Opacity', 'lps' ); ?></th>
												<td colspan="6">
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
												</td>
											</tr>
										</tbody>
										<tbody id="lps_style_has_tall_tr" class="lps-update-blink">
											<tr>
												<th><?php esc_html_e( 'Tall Card', 'lps' ); ?></th>
												<td colspan="6">
													<select id="lps_style_has_tall" data-default="" onchange="lpsStyleHelper()">
														<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
														<option value="tall"><?php esc_html_e( 'yes', 'lps' ); ?></option>
													</select>
												</td>
											</tr>
										</tbody>
										<tbody id="lps_style_has_img_spacing_tr" class="lps-update-blink">
											<tr>
												<th><?php esc_html_e( 'Image Spacing', 'lps' ); ?></th>
												<td colspan="6">
													<select id="lps_style_has_img_spacing" data-default="" onchange="lpsStyleHelper()">
														<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
														<option value="has-img-spacing"><?php esc_html_e( 'yes', 'lps' ); ?></option>
													</select>
												</td>
											</tr>
										</tbody>
										<tbody id="lps_style_has_stacked_tr" class="lps-update-blink">
											<tr>
												<th><?php esc_html_e( 'Stacked on Mobile', 'lps' ); ?></th>
												<td colspan="6">
													<select id="lps_style_has_stacked" data-default="" onchange="lpsStyleHelper()">
														<option value=""><?php esc_html_e( 'no', 'lps' ); ?></option>
														<option value="has-stacked"><?php esc_html_e( 'yes', 'lps' ); ?></option>
													</select>
												</td>
											</tr>
										</tbody>
										<tbody id="lps_size_image_tr" class="lps-update-blink">
											<tr>
												<th><?php esc_html_e( 'Image Size', 'lps' ); ?></th>
												<td colspan="6">
													<input type="text" name="lps_size_image" id="lps_size_image" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'inherit', 'lps' ); ?> (50%)" size="32">
												</td>
											</tr>
										</tbody>
										<tbody id="lps_card_ratio_tr" class="lps-update-blink">
											<tr>
												<th><?php esc_html_e( 'Card Aspect Ratio', 'lps' ); ?></th>
												<td colspan="6">
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
												</td>
											</tr>
										</tbody>
										<tbody id="lps_image_ratio_tr" class="lps-update-blink">
											<tr>
												<th><?php esc_html_e( 'Image Aspect Ratio', 'lps' ); ?></th>
												<td colspan="6">
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
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
