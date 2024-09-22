=== Latest Post Shortcode ===
Contributors: Iulia Cazan
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JJA37EHZXWUTJ
Tags: posts grid, posts shortcode, Gutenberg block, paginated posts, configurable shortcode with UI
Requires at least: 5.5.0
Tested up to: 6.6
Stable tag: 13.0.3
Requires PHP: 7.3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The "Latest Post Shortcode" allows you to create a dynamic content selection from your posts by combining, limiting, and filtering what you need.

== Description ==
The "Latest Post Shortcode" helps you display a list or grid of the posts or pages in a page/sidebar, without having to code or know PHP. The output parameters are extremely flexible, allowing you to choose the way your selected content will be displayed. You can embed as many shortcodes in a page as you need, each shortcode configured differently. The shortcode for displaying the latest posts is `[latest-selected-content]` and can be generated very easily, the plugin will add a block or a shortcode button in the editor area.

You can write your own "read more" replacement, choose whether to show/hide featured images, you can even sort the items by several options, and paginate the output (also AJAX pagination). This plugin works with any modern theme. When used with WordPress >= 5.0 + Gutenberg, the plugin shortcode can be configured from the LPS block or any Classic block, using the plugin button. The plugin can be used with Elementor page builder.

== Demo ==
https://youtu.be/er5wnGolfw8

= Usage example =
Example of a simple grid with 4 cards per row, with AJAX pagination:
`[latest-selected-content ver="2" perpage="4" showpages="4" display="title,date,excerpt-small" titletag="h3" chrlimit="120" more="…" url="yes" linktext="Read more" image="thumbnail" image_placeholder="auto" elements="25" image_opacity="0.3" css="four-columns as-overlay content-end pagination-space-between light tall" type="post" status="publish" orderby="dateD" show_extra="ajax_pagination,pagination_all,trim,date_diff,category,hide_uncategorized_category,oneterm_category,light_spinner"]`

Example of a simple grid with 4 cards (2 per row), filtered by a category (sample term):
`[latest-selected-content ver="2" limit="4" display="title,content-small" titletag="h3" chrlimit="50" image="full" image_placeholder="auto" elements="0" css="two-columns as-column has-shadow content-center" type="post" taxonomy="category" term="sample" orderby="dateA"]`

Starting with version 8.0.0, the plugin has a new UI and some new cool features. With this version, the output of the shortcode can be configured also as a slider, with responsive and different modes options. In this way, if you previously used the Latest Post Shortcode Extension, this is no longer needed, the plugin handles it all by itself.

Starting with version 7.0.0, the plugin implements new hooks that allow for defining and managing your custom output, through your theme or your plugins. Check more hook details and code samples at https://iuliacazan.ro/latest-post-shortcode/.

== Hooks ==
* Custom cards output filters: `lps/override_card_patterns`, `lps/override_card`, `lps/override_card_terms`, `lps/override_post_class`, `lps/override_card_display`, `lps/override_section_start`, `lps/override_section_end`

* Pagination filters: `lps/override_pagination_display/first`, `lps/override_pagination_display/first_icon`, `lps/override_pagination_display/prev`, `lps/override_pagination_display/prev_icon`, `lps/override_pagination_display/next`, `lps/override_pagination_display/next_icon`, `lps/override_pagination_display/last`, `lps/override_pagination_display/last_icon`

* Additional filters: `lps/filter_sites_list`, `lps/card_output_types`, `lps/remove_donate_info`, `lps/load_assets_on_page`, `lps/exclude_ids`, `lps/shortcode_arguments`, `lps/query_arguments`

* Marked as deprecated: `lps_filter_tile_patterns`, `lps_filter_display_posts_list`, `lps_filter_remove_update_info`, `lps_filter_use_custom_section_markup_end`, `lps_filter_use_custom_section_markup_start`, `lps_filter_use_custom_tile_markup`, `lps_filter_exclude_previous_content_ids`, `lps_filter_use_custom_shortcode_arguments`, `lps_filter_use_custom_query_arguments`

== Screenshots ==
1. Example of horizontal cards (info + image) with prev/next pagination.
2. Example of 3 columns grid of overlay cards.
3. Example of 3 columns grid of vertical cards.
4. Example of 2 columns grid of horizontal cards (image + info).
5. Example of horizontal cards (image + info) as an inline scroller.
6. Example of slider with center mode.

== Frequently Asked Questions ==
= How to use the block =
You can use the LPS block in page/post content, in templates, and also in widgets area. Click in the editor and type `/lps`, or `/latest`, or `/card`. In the list of results you can see `Overlay Cards`, `Vertical Cards`, and `Horizontal Cards`. Just pick one as a starting point, any of the initial variations you select can be configured later as you need.

= Where can I find the button for configuring the shortcode =
The button for configuring the shortcode is displayed as an icon or as the LPS button, depending on the mode you use when adding/updating content (the posts, pages, widgets, etc.):

* in the Visual mode of the editor, the button appears in the toolbar as an icon
* in the Text mode of the editor, the button appears in the toolbar as the LPS button

The button for the shortcode configurator can be used:

* on adding/editing posts, pages, text widgets
* in the Classic block for Gutenberg
* for version >= 8.7 the LPS widget is available in Elementor

== Upgrade Notice ==
No mentions

== Changelog ==
= 13.0.3 =
* Fixed the card elements elevation

See the [changelog](changelog.txt) for detailed information on changes made in the earlier versions.
