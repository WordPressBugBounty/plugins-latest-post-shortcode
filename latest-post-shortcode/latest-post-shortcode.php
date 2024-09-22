<?php // phpcs:ignore
/**
 * Plugin Name: Latest Post Shortcode
 * Plugin URI:  https://iuliacazan.ro/latest-post-shortcode/
 * Description: This plugin allows you to create a dynamic content selection from your posts, pages and custom post types that can be embedded with a UI configurable shortcode. When used with WordPress >= 5.0 + Gutenberg, the plugin shortcode can be configured from the LPS block or any Classic block, using the plugin button.
 * Text Domain: lps
 * Domain Path: /langs
 * Version:     13.0.3
 * Author:      Iulia Cazan
 * Author URI:  https://profiles.wordpress.org/iulia-cazan
 * Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JJA37EHZXWUTJ
 * License:     GPL2
 *
 * @package LPS
 *
 * Copyright (C) 2015-2024 Iulia Cazan
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Define the plugin version.
define( 'LPS_PLUGIN_VERSION', 13.03 );
define( 'LPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LPS_PLUGIN_SLUG', 'lps' );

require_once __DIR__ . '/incs/assets.php';

/**
 * Class for Latest Post Shortcode.
 */
class Latest_Post_Shortcode {

	const PLUGIN_NAME        = 'Latest Post Shortcode';
	const PLUGIN_SUPPORT_URL = 'https://wordpress.org/support/plugin/latest-post-shortcode/';
	const PLUGIN_TRANSIENT   = 'lps-plugin-notice';
	const ASSETS_VERSION     = 'lps_asset_version';

	/**
	 * Class instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Check that configurator wrapper was set or not.
	 *
	 * @var array
	 */
	public static $wrapper_was_set = false;

	/**
	 * Tile pattern.
	 *
	 * @var array
	 */
	public static $tile_pattern = [];

	/**
	 * Tile pattern for ver 2 only.
	 *
	 * @var array
	 */
	public static $tile_pattern_ver2 = [];

	/**
	 * Tile content.
	 *
	 * @var string
	 */
	public static $tile_content = '';

	/**
	 * Taxonomy positions.
	 *
	 * @var array
	 */
	public static $tax_positions = [];

	/**
	 * Pugin tags replaceable.
	 *
	 * @var array
	 */
	public static $replaceable_tags = [ 'date', 'title', 'text', 'image', 'read_more_text', 'author', 'category', 'tags', 'show_mime', 'caption' ];

	/**
	 * Pugin order by options.
	 *
	 * @var array
	 */
	public static $orderby_options = [];

	/**
	 * Tile pattern links.
	 *
	 * @var array
	 */
	public static $tile_pattern_links;

	/**
	 * Tile pattern with no links.
	 *
	 * @var array
	 */
	public static $tile_pattern_nolinks;

	/**
	 * Title tags.
	 *
	 * @var array
	 */
	public static $title_tags = [];

	/**
	 * Slider wrap tags.
	 *
	 * @var array
	 */
	public static $slider_wrap_tags = [];

	/**
	 * Date limit units.
	 *
	 * @var array
	 */
	public static $date_limit_units = [];

	/**
	 * Current query statuses list.
	 *
	 * @var array
	 */
	public static $current_query_statuses_list = [];

	/**
	 * True if the Elementor editor is active.
	 *
	 * @var boolean
	 */
	public static $is_elementor_editor = false;

	/**
	 * Editor type.
	 *
	 * @var string
	 */
	public static $editor_type = '';

	/**
	 * The current assets version.
	 *
	 * @var string
	 */
	public static $assets_version = '';

	/**
	 * Get active object instance
	 *
	 * @return object
	 */
	public static function get_instance(): object {
		if ( ! self::$instance ) {
			self::$instance = new Latest_Post_Shortcode();
		}
		return self::$instance;
	}

	/**
	 * Class constructor. Includes constants and init methods.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Run action and filter hooks.
	 */
	private function init() {
		$class = get_called_class();

		add_action( 'init', [ $class, 'tile_pattern_setup' ], 1 ); // Hook into tile patterns.
		add_action( 'plugins_loaded', [ $class, 'load_textdomain' ] ); // Text domain load.
		add_shortcode( 'latest-selected-content', [ $class, 'latest_selected_content' ] );

		if ( is_admin() ) {
			add_action( 'admin_footer', [ $class, 'add_shortcode_popup_container' ] );
			add_action( 'admin_enqueue_scripts', [ $class, 'load_admin_assets' ] );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $class, 'plugin_action_links' ] );
		} else {
			add_action( 'wp_enqueue_scripts', [ $class, 'load_assets' ] );
			add_action( 'wp_enqueue_scripts', [ $class, 'load_slider_assets' ] );
		}

		add_action( 'wp_insert_post', [ $class, 'execute_lps_cache_reset' ] );
		add_action( 'post_updated', [ $class, 'execute_lps_cache_reset' ] );
		add_action( 'wp_trash_post', [ $class, 'execute_lps_cache_reset' ] );
		add_action( 'before_delete_post', [ $class, 'execute_lps_cache_reset' ] );

		add_action( 'wp_ajax_nopriv_lps_navigate_to_page', [ $class, 'lps_navigate_callback' ] );
		add_action( 'wp_ajax_lps_navigate_to_page', [ $class, 'lps_navigate_callback' ] );
		add_action( 'wp_ajax_lps_reset_cache', [ $class, 'lps_reset_cache' ] );

		add_action( 'admin_notices', [ $class, 'plugin_admin_notices' ] );
		add_action( 'wp_ajax_plugin-deactivate-notice-lps', [ $class, 'plugin_admin_notices_cleanup' ] );
		add_action( 'plugins_loaded', [ $class, 'plugin_ver_check' ] );

		// Attempt to do one last filter of the assets.
		add_action( 'admin_init', [ $class, 'lps_assets_options' ] );
		add_action( 'wp_enqueue_scripts', [ $class, 'lps_filter_plugin_assets' ], 999 );

		// Attempt to fix the pagination for single pages.
		add_action( 'parse_query', [ $class, 'fix_request_redirect' ] );
	}

	/**
	 * Set the assets version from the DB, if possible.
	 */
	public static function get_assets_version() {
		self::$assets_version = get_option( self::ASSETS_VERSION, LPS_PLUGIN_VERSION );
	}

	/**
	 * Returns the assets version to be used.
	 */
	public static function ver() {
		return LPS_PLUGIN_VERSION . self::$assets_version;
	}

	/**
	 * Define the tile patterns.
	 *
	 * @return void
	 */
	public static function tile_pattern_setup() {
		self::get_assets_version();

		if ( class_exists( 'Latest_Post_Shortcode_Slider' ) ) {
			// Deactivate the extension, it is no longer supported.
			if ( function_exists( 'deactivate_plugins' ) ) {
				deactivate_plugins( '/latest-post-shortcode-slider-extension/latest-post-shortcode-slider.php' );
			}

			// Mention to the user that the extension is not supported anymore.
			add_action( 'admin_notices', function () {
				$class   = 'notice notice-error';
				$message = __( 'The Latest Post Shortcode Slider Extension is no longer supported, and it has been deactivated. If you need to display posts as a slider you can find the settings integrated in the Latest Post Shortcode plugin', 'lps' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}, 99 );
		}

		self::$tile_pattern = [
			// No link.
			0  => '[image][title][text][read_more_text]',
			1  => '[title][image][text][read_more_text]',
			2  => '[title][text][image][read_more_text]',
			18 => '[title][text][read_more_text][image]',

			// Full link.
			3  => '[a][image][title][text][read_more_text][/a]',
			11 => '[a][title][image][text][read_more_text][/a]',
			14 => '[a][title][text][image][read_more_text][/a]',
			19 => '[a][title][text][read_more_text][image][/a]',

			// Partial link.
			13 => '[title][image][text][a-r][read_more_text][/a]',
			17 => '[title][text][image][a-r][read_more_text][/a]',
			25 => '[image][a][title][/a][text][read_more_text]',
			26 => '[image][a][title][/a][text][a-r][read_more_text][/a]',
			27 => '[a][image][title][/a][text][read_more_text]',
			5  => '[image][title][text][a-r][read_more_text][/a]',
			28 => '[a][image][title][/a][text][a-r][read_more_text][/a]',
			22 => '[title][text][a-r][read_more_text][/a][image]',
		];

		// Allow to hook into tile patterns.
		self::$tile_pattern = apply_filters( 'lps/override_card_patterns', self::$tile_pattern );
		self::$tile_pattern = apply_filters_deprecated( 'lps_filter_tile_patterns', [ self::$tile_pattern ], '11.4.0', 'lps/override_card_patterns' );

		self::$tile_pattern_links   = [];
		self::$tile_pattern_nolinks = [];
		self::$tile_pattern_ver2    = [ 0, 5, 18, 22, 25, 26 ];
		self::$title_tags           = [ 'h3', 'h2', 'h1', 'h4', 'h5', 'h6', 'b', 'strong', 'em', 'p', 'div', 'span' ];
		self::$date_limit_units     = [
			'months' => esc_html__( 'months', 'lps' ),
			'weeks'  => esc_html__( 'weeks', 'lps' ),
			'days'   => esc_html__( 'days', 'lps' ),
			'hours'  => esc_html__( 'hours', 'lps' ),
		];
		self::$slider_wrap_tags     = [ 'div', 'p', 'span', 'section' ];

		foreach ( self::$tile_pattern as $k => $v ) {
			if ( substr_count( $v, '[a]' ) !== 0 || substr_count( $v, '[a-r]' ) !== 0 ) {
				array_push( self::$tile_pattern_links, $k );
			} else {
				array_push( self::$tile_pattern_nolinks, $k );
			}
		}

		self::$orderby_options = [
			'dateD'         => [
				'title'   => '▼ ' . esc_html__( 'date', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'date',
			],
			'dateA'         => [
				'title'   => '▲ ' . esc_html__( 'date', 'lps' ),
				'order'   => 'ASC',
				'orderby' => 'date',
			],
			'datemD'        => [
				'title'   => '▼ ' . esc_html__( 'modified date', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'modified',
			],
			'datemA'        => [
				'title'   => '▲ ' . esc_html__( 'modified date', 'lps' ),
				'order'   => 'ASC',
				'orderby' => 'modified',
			],
			'menuD'         => [
				'title'   => '▼ ' . esc_html__( 'menu order', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'menu_order',
			],
			'menuA'         => [
				'title'   => '▲ ' . esc_html__( 'menu order', 'lps' ),
				'order'   => 'ASC',
				'orderby' => 'menu_order',
			],
			'titleD'        => [
				'title'   => '▼ ' . esc_html__( 'title', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'title',
			],
			'titleA'        => [
				'title'   => '▲ ' . esc_html__( 'title', 'lps' ),
				'order'   => 'ASC',
				'orderby' => 'title',
			],
			'idD'           => [
				'title'   => '▼ ' . esc_html__( 'ID', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'ID',
			],
			'idA'           => [
				'title'   => '▲ ' . esc_html__( 'ID', 'lps' ),
				'order'   => 'ASC',
				'orderby' => 'ID',
			],
			'metaValueD'    => [
				'title'   => '▼ ' . esc_html__( 'text meta value', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'meta_value',
			],
			'metaValueA'    => [
				'title'   => '▲ ' . esc_html__( 'text meta value', 'lps' ),
				'order'   => 'ASC',
				'orderby' => 'meta_value',
			],
			'metaValueNumD' => [
				'title'   => '▼ ' . esc_html__( 'numeric meta value', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'meta_value_num',
			],
			'metaValueNumA' => [
				'title'   => '▲ ' . esc_html__( 'numeric meta value', 'lps' ),
				'order'   => 'ASC',
				'orderby' => 'meta_value_num',
			],
			'random'        => [
				'title'   => esc_html__( 'random *', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'rand',
			],
			'relevance'     => [
				'title'   => esc_html__( 'relevance *', 'lps' ),
				'order'   => 'DESC',
				'orderby' => 'relevance',
			],
		];

		self::$tax_positions = [
			'before-title'          => esc_html__( 'before title', 'lps' ),
			'after-title'           => esc_html__( 'after title', 'lps' ),
			'before-image'          => esc_html__( 'before image', 'lps' ),
			'after-image'           => esc_html__( 'after image', 'lps' ),
			'before-text'           => esc_html__( 'before text', 'lps' ),
			'after-text'            => esc_html__( 'after text', 'lps' ),
			'before-read_more_text' => esc_html__( 'before `read more`', 'lps' ),
			'after-read_more_text'  => esc_html__( 'after `read more`', 'lps' ),
			'before-date'           => esc_html__( 'before date', 'lps' ),
			'after-date'            => esc_html__( 'after date', 'lps' ),
		];

		$filtered_tax = self::filtered_taxonomies();
		if ( ! empty( $filtered_tax ) ) {
			foreach ( $filtered_tax as $key => $value ) {
				array_push( self::$replaceable_tags, $key );
			}
		}

		self::$replaceable_tags = array_unique( self::$replaceable_tags );

		$_date      = esc_html__( 'date', 'lps' );
		$_title     = esc_html__( 'title', 'lps' );
		$_excerpt   = esc_html__( 'excerpt', 'lps' );
		$_content   = esc_html__( 'content', 'lps' );
		$_excerpt_s = esc_html__( 'trimmed excerpt', 'lps' );
		$_content_s = esc_html__( 'trimmed content', 'lps' );

		$display_posts_list = [
			'title'                     => $_title,
			'title,excerpt'             => $_title . ' + ' . $_excerpt,
			'title,content'             => $_title . ' + ' . $_content,
			'title,excerpt-small'       => $_title . ' + ' . $_excerpt_s,
			'title,content-small'       => $_title . ' + ' . $_content_s,
			'date'                      => $_date,
			'title,date'                => $_title . ' + ' . $_date,
			'title,date,excerpt'        => $_title . ' + ' . $_date . ' + ' . $_excerpt,
			'title,date,content'        => $_title . ' + ' . $_date . ' + ' . $_content,
			'title,date,excerpt-small'  => $_title . ' + ' . $_date . ' + ' . $_excerpt_s,
			'title,date,content-small'  => $_title . ' + ' . $_date . ' + ' . $_content_s,
			'date,title'                => $_date . ' + ' . $_title,
			'date,title,excerpt'        => $_date . ' + ' . $_title . ' + ' . $_excerpt,
			'date,title,content'        => $_date . ' + ' . $_title . ' + ' . $_content,
			'date,title,excerpt-small'  => $_date . ' + ' . $_title . ' + ' . $_excerpt_s,
			'date,title,content-small'  => $_date . ' + ' . $_title . ' + ' . $_content_s,
			'date,title,excerptcontent' => $_date . ' + ' . $_title . ' + ' . $_excerpt . ' + ' . $_content,
			'date,title,contentexcerpt' => $_date . ' + ' . $_title . ' + ' . $_content . ' + ' . $_excerpt,
		];

		// Maybe apply custom extra type.
		$display_posts_list = apply_filters( 'lps/override_card_display', $display_posts_list );
		$display_posts_list = apply_filters_deprecated( 'lps_filter_display_posts_list', [ $display_posts_list ], '11.4.0', 'lps/override_card_display' );

		self::$tile_content = $display_posts_list;
	}

	/**
	 * Load text domain for internalization.
	 */
	public static function load_textdomain() {
		load_plugin_textdomain( 'lps', false, basename( __DIR__ ) . '/langs' );
	}

	/**
	 * Enqueue the plugin assets.
	 */
	public static function load_assets() {
		LPS\use_style_legacy();
		LPS\use_style_main();
	}

	/**
	 * Load the admin assets.
	 */
	public static function load_admin_assets() {
		LPS\use_style_legacy();
		LPS\use_style_main();
		LPS\use_style_modal();
		LPS\use_script_modal();
	}

	/**
	 * Assess if the current user role maches the restriction settings.
	 *
	 * @return bool
	 */
	public static function allow_icon_for_roles() {
		$opt = get_option( 'lps-classic-exclude-role', '' );
		if ( empty( $opt ) ) {
			// No restriction.
			return true;
		}

		if ( ! is_user_logged_in() ) {
			// No access for visitors.
			return false;
		}

		$user    = wp_get_current_user();
		$u_roles = (array) $user->roles;
		$opt     = preg_replace( '/\s+/', '', $opt );
		$roles   = explode( ',', $opt );
		$matched = array_intersect( $roles, $u_roles );

		if ( ! empty( $matched ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Load the slider assets from local files instead of CDN, to make it faster, and available offline.
	 *
	 * @param  bool $forced Load the assets without checking if it's necessary.
	 * @return void
	 */
	public static function load_slider_assets( bool $forced = false ): void {
		$is_block_rendering = defined( 'REST_REQUEST' ) && REST_REQUEST;
		$in_the_editor      = self::is_in_the_editor();
		$in_the_preview     = is_preview();

		if ( $in_the_editor || $is_block_rendering ) {
			// Fail-fast, not loading the assets.
			return;
		}

		$load = false;
		if ( $in_the_preview || $forced ) {
			$load = true;
		}

		if ( ! $load ) {
			if ( ! $forced && 'elementor' !== self::$editor_type ) {
				global $post, $lps_assess_cpa;
				if ( empty( $lps_assess_cpa ) ) {
					self::lps_assess_page_content();
				}

				$text  = ( ! empty( $post->post_content ) ) ? $post->post_content : '';
				$text .= $lps_assess_cpa;
				$text .= serialize( get_option( 'widget_text' ) ) . serialize( get_option( 'widget_custom_html' ) ); // phpcs:ignore
				$text  = str_replace( '\u0022', '"', $text );

				if ( empty( $text ) ) {
					return;
				}

				if ( ! ( str_contains( $text, '[latest-selected-content' )
					|| str_contains( $text, 'wp:latest-post-shortcode' ) )
					|| ! str_contains( $text, 'output="slider"' ) ) {
					return;
				}
			}
		}

		LPS\use_style_slider();
		LPS\use_script_slider();
	}

	/**
	 * Return all private and all public statuses defined.
	 *
	 * @return array
	 */
	public static function get_statuses(): array {
		global $wp_post_statuses;
		$arr = [
			'public'  => [],
			'private' => [],
		];

		if ( ! empty( $wp_post_statuses ) ) {
			$exclude = [ 'auto-draft', 'request-confirmed', 'request-pending', 'request-failed', 'request-completed', 'trash', 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed', 'wc-checkout-draft', 'flamingo-spam', 'in-progress', 'failed' ];
			foreach ( $wp_post_statuses as $t => $v ) {
				if ( $v->public ) {
					$arr['public'][ $t ] = $v->label;
				} elseif ( ! in_array( $t, $exclude, true ) ) {
						$arr['private'][ $t ] = $v->label;
				}
			}
		}
		self::get_ctps();

		return $arr;
	}

	/**
	 * Return the defined and filtered CPTs.
	 *
	 * @return array
	 */
	public static function get_ctps(): array {
		$cpts   = [];
		$ptypes = get_post_types( [], 'objects' );
		if ( ! empty( $ptypes ) ) {
			$exclude = [ 'revision', 'nav_menu_item', 'oembed_cache', 'custom_css', 'customize_changeset', 'user_request', 'wp_block', 'wpcf7_contact_form', 'amp_validated_url', 'scheduled-action', 'shop_order', 'shop_order_refund', 'shop_coupon', 'shop_order_placehold', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation', 'e-landing-page', 'elementor_library', 'patterns_ai_data', 'wp_font_family', 'wp_font_face' ];
			foreach ( $ptypes as $t => $v ) {
				if ( ! in_array( $t, $exclude, true ) ) {
					$cpts[ $t ] = $v->label;
				}
			}
		}
		return $cpts;
	}

	/**
	 * Return the available sites.
	 *
	 * @return array
	 */
	public static function get_sites(): array {
		if ( ! is_multisite() ) {
			return [];
		}

		$list = [];
		$args = \apply_filters( 'lps/filter_sites_list', [ 'public' => '1' ] );
		foreach ( \get_sites( $args ) as $site ) {
			if ( ! empty( $site->blog_id ) ) {
				$info = \get_blog_details( $site->blog_id );

				$list[ $site->blog_id ] = $info->blogname ?? $site->path;
			}
		}

		return $list;
	}

	/**
	 * The custom patterns start with _custom_.
	 *
	 * @param  string $tile_pattern A tile pattern.
	 * @return bool
	 */
	public static function tile_markup_is_custom( string $tile_pattern = '' ): bool {
		$use_custom_markup = false;
		if ( '_custom_' === substr( $tile_pattern, 1, 8 ) ) {
			$use_custom_markup = true;
		}
		return $use_custom_markup;
	}

	/**
	 * Get the filtered card output types.
	 *
	 * @return array
	 */
	public static function get_card_output_types() {
		$list = \apply_filters( 'lps/card_output_types', [
			''             => esc_html__( '-- unspecified --', 'lps' ),
			'as-column'    => esc_html__( 'vertical card', 'lps' ),
			'h-image-info' => esc_html__( 'horizontal card: image + info', 'lps' ),
			'h-info-image' => esc_html__( 'horizontal card: info + image', 'lps' ),
			'as-overlay'   => esc_html__( 'overlay', 'lps' ),
		] );

		if ( ! is_array( $list ) ) {
			return [];
		}

		return $list;
	}

	/**
	 * Get the filtered card output types.
	 *
	 * @param  array $args Shortcode arguments.
	 * @return string
	 */
	public static function get_card_output_type_from_args( array $args = [] ): string {
		if ( empty( $args['css'] ) ) {
			// Fail-fast, nothing to compare.
			return '';
		}

		$css     = explode( ' ', $args['css'] );
		$options = array_keys( self::get_card_output_types() );
		$match   = array_unique( array_intersect( $options, $css ) );
		return trim( implode( '', $match ) );
	}

	/**
	 * The list of filtered taxonomies.
	 *
	 * @return array
	 */
	public static function filtered_taxonomies(): array {
		$list = [];
		$tax  = get_taxonomies( [], 'objects' );
		if ( ! empty( $tax ) ) {
			$exclude = [ 'post_tag', 'nav_menu', 'link_category', 'post_format', 'amp_template', 'elementor_library_type', 'elementor_library_category', 'elementor_library', 'wp_theme' ];
			foreach ( $tax as $k => $v ) {
				if ( ! in_array( $k, $exclude, true ) ) {
					if ( ! empty( $v->public ) ) {
						$list[ $k ] = $v;
					}
				}
			}
		}
		return $list;
	}

	/**
	 * Add some content to the bottom of the page.
	 * This will be shown in the inline modal.
	 */
	public static function add_shortcode_popup_container() {
		if ( true === self::$wrapper_was_set ) {
			// Fail-fast, this was used.
			return;
		}

		self::$wrapper_was_set = true;
		$display_posts_list    = self::$tile_content;

		include_once __DIR__ . '/incs/settings-popup.php';
	}

	/**
	 * Add the slider configuration.
	 */
	public static function output_slider_configuration() {
		include_once __DIR__ . '/incs/settings-slider.php';
	}

	/**
	 * Get short text of maximum x chars.
	 *
	 * @param  string $text       Text.
	 * @param  int    $limit      Limit of chars.
	 * @param  bool   $is_excerpt True if this represents an excerpt.
	 * @param  string $trimmore   Maybe some trailing extra chars for truncated string.
	 * @return string
	 */
	public static function get_short_text( $text, $limit, $is_excerpt = false, $trimmore = '' ) { // phpcs:ignore
		if ( empty( $text ) ) {
			// Fail-fast.
			return '';
		}

		$filter = ( $is_excerpt ) ? 'the_excerpt' : 'the_content';

		$text = wp_strip_all_tags( $text );
		$text = preg_replace( '~\[[^\]]+\]~', '', $text );
		$text = strip_shortcodes( $text );
		$text = apply_filters( $filter, strip_shortcodes( $text ) );
		$text = preg_replace( '~\[[^\]]+\]~', '', $text );

		if ( empty( $text ) ) {
			// Fail-fast.
			return '';
		}

		$text = wp_strip_all_tags( $text );
		$text = preg_replace( '~\[[^\]]+\]~', '', $text );
		/** This is a trick to replace the unicode whitespace :) */
		$text = preg_replace( '/\xA0/u', ' ', $text );
		$text = str_replace( '&nbsp;', ' ', $text );
		$text = preg_replace( '/\s\s+/', ' ', $text );
		$text = preg_replace( '/\s+/', ' ', $text );
		$text = trim( $text );

		if ( empty( $text ) ) {
			// Fail-fast.
			return '';
		}

		$init_len = mb_strlen( $text );
		if ( $init_len <= $limit ) {
			// The text length is smaller than the limit.
			$text = apply_filters( $filter, $text );
			$text = str_replace( ']]>', ']]&gt;', $text );
			return $text;
		}

		$content = explode( ' ', $text );

		$len  = 0;
		$i    = 0;
		$max  = count( $content );
		$text = '';
		while ( $len < $limit ) {
			$text .= $content[ $i ] . ' ';
			++$i;
			$len = mb_strlen( $text );
			if ( $i >= $max || $len >= $limit ) {
				break;
			}
		}

		if ( ! empty( $text ) ) {
			$text = trim( $text );
			$text = preg_replace( '/\[.+\]/', '', $text );
			$text = self::cleanup_tralining_punctuation( $text );
			$text = trim( $text );
			if ( ! empty( $trimmore ) && ! empty( $text ) && mb_strlen( $text ) !== $init_len ) {
				$text .= $trimmore;
				$text  = trim( $text );
			}

			$text = apply_filters( $filter, $text );
			$text = str_replace( ']]>', ']]&gt;', $text );
		}

		return $text;
	}

	/**
	 * Cleanup tralining punctuation.
	 *
	 * @param  string $text Initial string.
	 * @return string
	 */
	public static function cleanup_tralining_punctuation( $text = '' ) { //phpcs:ignore
		if ( ! empty( $text ) && is_string( $text ) ) {
			$text = trim( $text, " \t\n\r\0\x0B-.,:|?!-_`'…" );
		}
		return $text;
	}

	/**
	 * Execute the reset of shortcodes cache in the database.
	 */
	public static function purge_site_lps_cache() {
		global $wpdb;
		// Remove all the transients records in one query.
		$tmp_query = $wpdb->prepare(
			' DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE %s OR option_name LIKE %s ',
			$wpdb->esc_like( '_transient_lps-' ) . '%',
			$wpdb->esc_like( '_transient_timeout_lps-' ) . '%'
		);
		$wpdb->query( $tmp_query ); // phpcs:ignore
	}

	/**
	 * Execute the reset of shortcodes cache.
	 */
	public static function execute_lps_cache_reset() {
		self::purge_site_lps_cache();

		if ( is_multisite() ) {
			$sites = self::get_sites();
			if ( ! empty( $sites ) ) {
				foreach ( $sites as $id => $name ) {
					switch_to_blog( $id );
					self::purge_site_lps_cache();
					restore_current_blog();
				}
			}
		}

		remove_action( 'wp_insert_post', [ get_called_class(), 'execute_lps_cache_reset' ] );
		remove_action( 'post_updated', [ get_called_class(), 'execute_lps_cache_reset' ] );
		remove_action( 'wp_trash_post', [ get_called_class(), 'execute_lps_cache_reset' ] );
		remove_action( 'before_delete_post', [ get_called_class(), 'execute_lps_cache_reset' ] );
	}

	/**
	 * Reset the shortcodes cache.
	 */
	public static function lps_reset_cache() {
		$get = filter_input( INPUT_GET, 'no-cache', FILTER_DEFAULT );
		if ( ! empty( $get ) ) {
			self::execute_lps_cache_reset();
			echo 'OK';
			die();
		}
	}

	/**
	 * Return the content generated after an ajax call for the pagination.
	 */
	public static function lps_navigate_callback() {
		$args    = filter_input( INPUT_POST, 'args', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$current = filter_input( INPUT_POST, 'current', FILTER_DEFAULT );
		$shid    = filter_input( INPUT_POST, 'id', FILTER_DEFAULT );
		if ( ! empty( $args ) ) {
			if ( ! empty( $current ) ) {
				if ( empty( $args['archive'] ) ) {
					if ( empty( $args['excludeid'] ) ) {
						$args['excludeid'] = (int) $current;
					} else {
						$args['excludeid'] .= ',' . (int) $current;
					}
				}
			}
			header( 'Content-type: text/html; charset=utf-8' );
			$_args = $args;
			if ( is_array( $args ) ) {
				foreach ( $args as $key => $value ) {
					$args[ $key ] = sanitize_text_field( $value );
				}
			} else {
				$_args = stripslashes( stripslashes( $args ) );
				$args  = ( ! empty( $_args ) ) ? json_decode( $_args ) : false;
			}

			$ppage = filter_input( INPUT_POST, 'page', FILTER_DEFAULT );
			if ( ! empty( $ppage ) && $args ) {
				$args = (array) $args;
				if ( ! empty( $args['linktext'] ) ) {
					$args['linktext'] = preg_replace( '/u([0-9a-z]{4})+/', '&#x$1;', $args['linktext'] );
				}
				set_query_var( 'page', (int) $ppage );

				global $is_lps_ajax_call, $is_ajax_shortcode_id, $lps_current_queried_object_id;
				$is_lps_ajax_call              = true;
				$is_ajax_shortcode_id          = str_replace( '-wrap', '', $shid );
				$lps_current_queried_object_id = (int) $current;
				echo self::latest_selected_content( $args ); // phpcs:ignore
			}
		}
		die();
	}

	/**
	 * Return the content generated for plugin pagination with the specific arguments.
	 *
	 * @param  int    $total         Total of records.
	 * @param  int    $per_page      How many per page.
	 * @param  int    $range         Range size.
	 * @param  string $shortcode_id  Shortcode id (element selector).
	 * @param  string $class         Pagination CSS class.
	 * @param  array  $args          Load more text, total text, show total.
	 * @param  int    $maxpg         Maximum number of total pages (leave 0 for default).
	 * @param  int    $site_initial  Initial site.
	 * @param  int    $site_expected Expected/requested site.
	 * @return string
	 */
	public static function lps_pagination( $total = 1, $per_page = 10, $range = 4, $shortcode_id = '', $class = '', $args = [], $maxpg = 0, $site_initial = 0, $site_expected = 0 ) { // phpcs:ignore
		$current_page = self::get_current_page();
		wp_reset_postdata();

		if ( is_multisite() && $site_initial !== $site_expected ) {
			switch_to_blog( $site_initial );
		}

		$body     = '';
		$total    = (int) $total;
		$all      = $total;
		$per_page = ( ! empty( $per_page ) ) ? (int) $per_page : 1;
		$range    = abs( (int) $range );
		$range    = ( empty( $range ) ) ? 1 : $range;
		$total    = ceil( $total / $per_page );
		if ( ! empty( $maxpg ) && $maxpg < $total ) {
			$total = $maxpg;
		}

		$is_prevnext = 1 === $range;

		if ( $total > 1 ) {
			if ( 0 === ( $current_page % $range ) ) {
				$start = $current_page - $range + 1;
			} else {
				$start = $current_page - $current_page % $range + 1;
			}
			$start = ( $start <= 1 ) ? 1 : $start;
			$end   = $start + $range - 1;
			if ( $end >= $total ) {
				$end = $total;
			}

			$more_text  = ! empty( $args['more_text'] ) ? $args['more_text'] : '';
			$total_text = ! empty( $args['total_text'] ) ? esc_attr( $args['total_text'] ) : '';
			$show_total = ! empty( $args['show_total'] ) && ! empty( $total_text ) && substr_count( $total_text, '%d' );

			if ( substr_count( $class, ' lps-load-more' ) ) {
				$body .= '<ul class="latest-post-selection pages ' . esc_attr( trim( $class ) ) . ' ' . esc_attr( $shortcode_id ) . '">';
				if ( $show_total ) {
					$body .= '<li class="pages-info total-info">' . sprintf( $total_text, $all ) . '</li>';
				}

				if ( ! $show_total && ! empty( $args['hide_more'] ) ) {
					$class .= ' without-size';
				}
				if ( $current_page < $total ) {
					if ( empty( $more_text ) && ! empty( $args['loadtext'] ) ) {
						$more_text = $args['loadtext'];
					}

					$more_class = ! empty( $args['hide_more'] ) ? ' hide-more' : '';

					$text  = ( ! empty( $more_text ) ) ? $more_text : __( 'Load more', 'lps' );
					$body .= '<li class="go-to-next lps-load-more' . $more_class . '"><a class="page-item" href="' . get_pagenum_link( $current_page + 1 ) . '" data-page="' . ( $current_page + 1 ) . '" title="' . esc_attr( $text ) . '">' . esc_html( $text ) . '</a></li>';
				}
				$body .= '</ul>';
			} else {
				if ( $is_prevnext ) {
					$class = trim( $class . ' with-prev-next' );
				}
				$root_url = get_pagenum_link( 0 );
				$body    .= '<ul class="latest-post-selection pages ' . esc_attr( trim( $class ) ) . ' ' . esc_attr( $shortcode_id ) . '">';

				$body_total = '';
				$body_pags  = '';
				$body_prev  = '';
				$body_list  = '';
				$body_next  = '';

				if ( $show_total ) {
					$body_total .= '<li class="pages-info total-info">' . sprintf( $total_text, $all ) . '</li>';
				}

				if ( $is_prevnext ) {
					$body_pags .= '<li class="pages-info current-info"><span>' . $current_page . '</span><span> / </span><span>' . $total . '</span></li>';
				} else {
					$body_pags .= '<li class="pages-info">' . __( 'Page', 'lps' ) . ' ' . $current_page . ' ' . __( 'of', 'lps' ) . ' ' . $total . '</li>';
				}

				$item_text_first = apply_filters( 'lps/override_pagination_display/first', '&lsaquo;&nbsp;' );
				$item_icon_first = apply_filters( 'lps/override_pagination_display/first_icon', '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><path fill="currentColor" d="M224 128a8 8 0 0 1-8 8H59.31l58.35 58.34a8 8 0 0 1-11.32 11.32l-72-72a8 8 0 0 1 0-11.32l72-72a8 8 0 0 1 11.32 11.32L59.31 120H216a8 8 0 0 1 8 8"/></svg>' );

				$item_text_prev = apply_filters( 'lps/override_pagination_display/prev', '&laquo;' );
				$item_icon_prev = apply_filters( 'lps/override_pagination_display/prev_icon', '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><path fill="currentColor" d="M165.66 202.34a8 8 0 0 1-11.32 11.32l-80-80a8 8 0 0 1 0-11.32l80-80a8 8 0 0 1 11.32 11.32L91.31 128Z"/></svg>' );

				$item_text_next = apply_filters( 'lps/override_pagination_display/next', '&raquo;' );
				$item_icon_next = apply_filters( 'lps/override_pagination_display/next_icon', '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><path fill="currentColor" d="m181.66 133.66l-80 80a8 8 0 0 1-11.32-11.32L164.69 128L90.34 53.66a8 8 0 0 1 11.32-11.32l80 80a8 8 0 0 1 0 11.32"/></svg>' );

				$item_text_last = apply_filters( 'lps/override_pagination_display/last', '&nbsp;&rsaquo;' );
				$item_icon_last = apply_filters( 'lps/override_pagination_display/last_icon', '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><path fill="currentColor" d="m221.66 133.66l-72 72a8 8 0 0 1-11.32-11.32L196.69 136H40a8 8 0 0 1 0-16h156.69l-58.35-58.34a8 8 0 0 1 11.32-11.32l72 72a8 8 0 0 1 0 11.32"/></svg>' );

				if ( ! $is_prevnext ) {
					if ( $total > $range && $start > $range ) {
						$body_prev .= '<li class="go-to-first"><a class="page-item" href="' . $root_url . '" data-page="1" title="' . esc_attr__( 'First', 'lps' ) . '">' . $item_text_first . '</a></li>';
					} elseif ( $total > $range ) {
						$body_prev .= '<li class="go-to-first disabled"><a class="page-item" data-page="' . $current_page . '" title="' . esc_attr__( 'First', 'lps' ) . '">' . $item_text_first . '</a></li>';
					}
				}

				$prev = ! $is_prevnext ? $item_text_prev : $item_icon_prev;
				if ( $current_page > 1 ) {
					if ( 2 === $current_page ) {
						$body_prev .= '<li class="go-to-prev"><a class="page-item" href="' . $root_url . '" data-page="1" title="' . esc_attr__( 'Previous', 'lps' ) . '">' . $prev . '</a></li>';
					} else {
						$body_prev .= '<li class="go-to-prev"><a class="page-item" href="' . get_pagenum_link( $current_page - 1 ) . '" data-page="' . ( $current_page - 1 ) . '" title="' . esc_attr__( 'Previous', 'lps' ) . '">' . $prev . '</a></li>';
					}
				} else {
					$body_prev .= '<li class="go-to-prev disabled"><a class="page-item" data-page="' . $current_page . '" title="' . esc_attr__( 'Previous', 'lps' ) . '">' . $prev . '</a></li>';
				}

				if ( $is_prevnext ) {
					$first_cl  = $current_page > 1 ? '' : ' disabled';
					$body_prev = '<li class="go-to-prev go-to-first' . $first_cl . '"><a class="page-item" href="' . $root_url . '" data-page="1" title="' . esc_attr__( 'First', 'lps' ) . '">' . $item_icon_first . '</a></li>' . $body_prev;
				}

				if ( ! $is_prevnext ) {
					for ( $i = $start; $i <= $end; $i++ ) {
						if ( 1 === $i ) {
							if ( $current_page === $i ) {
								$body_list .= '<li class="current"><a class="page-item item-number" href="' . $root_url . '" data-page="1" title="' . esc_attr__( 'First', 'lps' ) . '">' . $i . '</a></li>';
							} else {
								$body_list .= '<li><a class="page-item item-number" href="' . $root_url . '" data-page="1" title="' . esc_attr__( 'First', 'lps' ) . '">' . $i . '</a></li>';
							}
						} elseif ( $current_page === $i ) {
								$body_list .= '<li class="current"><a class="page-item item-number" data-page="' . $i . '" title="' . esc_attr__( 'Page', 'lps' ) . ' ' . $i . '">' . $i . '</a></li>';
						} else {
							$body_list .= '<li><a class="page-item item-number" href="' . get_pagenum_link( $i ) . '" data-page="' . $i . '" title="' . esc_attr__( 'Page', 'lps' ) . ' ' . $i . '">' . $i . '</a></li>';
						}
					}
				}

				$next = ! $is_prevnext ? $item_text_next : $item_icon_next;
				if ( $current_page < $total ) {
					$body_next .= '<li class="go-to-next"><a class="page-item" href="' . get_pagenum_link( $current_page + 1 ) . '" data-page="' . ( $current_page + 1 ) . '" title="' . esc_attr__( 'Next', 'lps' ) . '">' . $next . '</a></li>';
				} else {
					$body_next .= '<li class="go-to-next disabled"><a class="page-item" data-page="' . $current_page . '" title="' . esc_attr__( 'Next', 'lps' ) . '">' . $next . '</a></li>';
				}

				if ( ! $is_prevnext ) {
					if ( $end < $total ) {
						$body_next .= '<li class="go-to-last"><a class="page-item" href="' . get_pagenum_link( $total ) . '" data-page="' . $total . '" title="' . esc_attr__( 'Last', 'lps' ) . '">' . $item_text_last . '</a></li>';
					} elseif ( $total > $range ) {
						$body_next .= '<li class="go-to-last disabled"><a class="page-item" data-page="' . $current_page . '" title="' . esc_attr__( 'Last', 'lps' ) . '">' . $item_text_last . '</a></li>';
					}
				}

				if ( $is_prevnext ) {
					$last_cl    = $end < $total ? '' : ' disabled';
					$body_next .= '<li class="go-to-next go-to-last' . $last_cl . '"><a class="page-item" href="' . get_pagenum_link( $total ) . '" data-page="' . $total . '" title="' . esc_attr__( 'Last', 'lps' ) . '">' . $item_icon_last . '</a></li>';
				}

				if ( ! $is_prevnext ) {
					$body .= $body_total . $body_pags . $body_prev . $body_list . $body_next;
				} else {
					$body .= $body_total . $body_prev . $body_pags . $body_next;
				}
				$body .= '</ul>';
			}

			if ( ! empty( $body ) ) {
				$body = '<div class="lps-pagination-wrap ' . trim( $class ) . '">' . $body . '</div>';
			}
		}

		if ( is_multisite() && $site_initial !== $site_expected ) {
			switch_to_blog( $site_expected );
		}

		return $body;
	}

	/**
	 * Dynamic relative time.
	 *
	 * @param  int $id The post ID.
	 * @return string
	 */
	public static function relative_time( $id = null ) { // phpcs:ignore
		if ( function_exists( 'current_datetime' ) ) {
			$date = current_datetime();
			$now  = ( ! empty( $date->date ) ) ? strtotime( $date->date ) : current_time( 'timestamp' ); // phpcs:ignore
		} else {
			$now = current_time( 'timestamp' ); // phpcs:ignore
		}

		return sprintf(
			// Translators: %s the date difference.
			_x( '%s ago', '%s = human-readable time difference', 'lps' ),
			strtolower( human_time_diff( get_the_time( 'U', $id ), (int) $now ) )
		);
	}

	/**
	 * Get the current page for pagination.
	 *
	 * @return int
	 */
	public static function get_current_page(): int {
		global $wp;

		$maybe_var   = get_query_var( 'paged' );
		$maybe_paged = filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT );
		$maybe_page  = filter_input( INPUT_GET, 'page', FILTER_VALIDATE_INT );
		if ( empty( $maybe_paged ) && empty( $maybe_page ) ) {
			$maybe_paged = filter_input( INPUT_POST, 'paged', FILTER_VALIDATE_INT );
			$maybe_page  = filter_input( INPUT_POST, 'page', FILTER_VALIDATE_INT );
		}

		if ( empty( $maybe_paged ) && empty( $maybe_page ) && ! empty( $maybe_var ) ) {
			$maybe_paged = $maybe_var;
		}

		if ( empty( $maybe_paged ) && ! empty( $wp->query_vars['paged'] ) ) {
			$maybe_paged = (int) $wp->query_vars['paged'];
		}

		$paged = 1;
		if ( ! empty( $maybe_paged ) ) {
			$paged = $maybe_paged;
		} elseif ( ! empty( $maybe_page ) ) {
			$paged = $maybe_page;
		}
		$paged = abs( intval( $paged ) );

		return $paged;
	}

	/**
	 * Returns true if the execution is triggered in the editor.
	 *
	 * @return bool
	 */
	public static function is_in_the_editor(): bool {
		// phpcs:disable
		$context = $_REQUEST['context'] ?? '';
		$action  = $_REQUEST['action'] ?? '';
		// phpcs:enable

		$in_the_editor = defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $context )
			&& ( 'edit' === $context || 'edit' === $action ); // phpcs:ignore

		if ( empty( $in_the_editor ) ) {
			$pagination_link = get_pagenum_link( 1 );
			if ( substr_count( $pagination_link, '/post.php?' ) || substr_count( $pagination_link, 'autosaves?' ) ) {
				$in_the_editor = true;
			}
		}

		return $in_the_editor;
	}

	/**
	 * Back up current filters and remove the targetted ones.
	 */
	public static function maybe_remove_post_class_filters() {
		global $wp_filter, $lps_backup_wp_filters;

		// Back up current filters.
		if ( ! empty( $wp_filter['post_class'] ) ) {
			$lps_backup_wp_filters = $wp_filter['post_class'];
			unset( $wp_filter['post_class'] );
		}
	}

	/**
	 * Restore the filters that were previously removed.
	 */
	public static function maybe_restore_post_class_filters() {
		global $wp_filter, $lps_backup_wp_filters;

		// Restore the previous filters.
		if ( ! empty( $lps_backup_wp_filters ) ) {
			$wp_filter['post_class'] = $lps_backup_wp_filters; // phpcs:ignore
		}
	}

	/**
	 * Return the content generated by a shortcode with the specific arguments.
	 *
	 * @param  array $args Array of shortcode arguments.
	 * @return string
	 */
	public static function latest_selected_content( $args ) { // phpcs:ignore
		if ( empty( $args ) ) {
			// Fail-fast, too bad, this is used wrong, there is no argument.
			return '';
		}

		$args = wp_parse_args( $args, [
			'lps_instance_id'         => '',
			'ver'                     => 1,
			'output'                  => '',
			'limit'                   => '',
			'perpage'                 => '',
			'id'                      => '',
			'excludeid'               => '',
			'parent'                  => '',
			'dparent'                 => '',
			'author'                  => '',
			'dauthor'                 => '',
			'excludeauthor'           => '',
			'type'                    => 'any',
			'site_id'                 => '',
			'titletag'                => '',
			'chrlimit'                => 120,
			'more'                    => '',
			'display'                 => '',
			'url'                     => '',
			'lightbox_size'           => '',
			'lightbox_attr'           => '',
			'lightbox_val'            => '',
			'linktext'                => '',
			'elements'                => '',
			'default_height'          => '',
			'default_padding'         => '',
			'default_gap'             => '',
			'default_overlay_padding' => '',
			'tablet_height'           => '',
			'tablet_padding'          => '',
			'tablet_gap'              => '',
			'tablet_overlay_padding'  => '',
			'mobile_height'           => '',
			'mobile_padding'          => '',
			'mobile_gap'              => '',
			'mobile_overlay_padding'  => '',
			'color_text'              => '',
			'color_title'             => '',
			'color_bg'                => '',
			'size_text'               => '',
			'size_title'              => '',
			'size_image'              => '',
			'image_opacity'           => '',
			'image_ratio'             => '',
			'card_ratio'              => '',
			'css'                     => '',
			'show_extra'              => '',
			'status'                  => '',
			'orderby'                 => '',
			'orderby_meta'            => '',
			'archive'                 => '',
			'archive_s'               => '',
			'archive_tax'             => '',
			'archive_id'              => '',
			'search'                  => '',
			'offset'                  => '',
			'tag'                     => '',
			'dtag'                    => '',
			'taxonomy'                => '',
			'term'                    => '',
			'taxonomy2'               => '',
			'term2'                   => '',
			'exclude_tags'            => '',
			'exclude_categories'      => '',
			'date_limit'              => '',
			'date_start'              => '',
			'date_start_type'         => '',
			'date_after'              => '',
			'date_before'             => '',
			'showpages'               => '',
			// Translators: %d - total value.
			'total_text'              => __( 'Total items: %d', 'lps' ),
			'loadtext'                => '',
			'pagespos'                => '',
			'fallback'                => '',
			'image'                   => '',
			'image_placeholder'       => '',
			'slidermode'              => '',
			'centermode'              => '',
			'centerpadd'              => '',
			'sliderauto'              => '',
			'sliderspeed'             => '',
			'slidersponsive'          => '',
			'respondto'               => '',
			'sliderwrap'              => '',
			'slideslides'             => '',
			'slidescroll'             => '',
			'sliderdots'              => '',
			'sliderinfinite'          => '',
			'slideoverlay'            => '',
			'slidegap'                => '',
			'sliderbreakpoint_tablet' => '',
			'slideslides_tablet'      => '',
			'slidescroll_tablet'      => '',
			'sliderdots_tablet'       => '',
			'sliderinfinite_tablet'   => '',
			'sliderbreakpoint_mobile' => '',
			'slideslides_mobile'      => '',
			'slidescroll_mobile'      => '',
			'sliderdots_mobile'       => '',
			'sliderinfinite_mobile'   => '',
			'sliderheight'            => '',
			'slidermaxheight'         => '',
			'slidercontrols'          => '',
			'slideratio'              => '',
		] );

		global $post, $lps_current_post_embedded_item_ids, $is_lps_ajax_call, $is_ajax_shortcode_id, $lps_current_queried_object_id;
		if ( empty( $is_lps_ajax_call ) ) {
			$is_lps_ajax_call = false;
		}

		if ( empty( $lps_current_post_embedded_item_ids ) ) {
			$lps_current_post_embedded_item_ids = [];
		}

		if ( $is_lps_ajax_call ) {
			$exclude = filter_input( INPUT_POST, 'exclude' );
			if ( ! empty( $exclude ) ) {
				$exclude = explode( ',', $exclude );
				$exclude = array_filter( $exclude, 'is_numeric' );

				$lps_current_post_embedded_item_ids = $exclude;
			}
		}

		$lps_current_post_embedded_item_ids = apply_filters( 'lps/exclude_ids', $lps_current_post_embedded_item_ids );
		$lps_current_post_embedded_item_ids = apply_filters_deprecated( 'lps_filter_exclude_previous_content_ids', [ $lps_current_post_embedded_item_ids ], '12.1.0', 'lps/exclude_ids' );

		if ( ! empty( $lps_current_queried_object_id ) ) {
			$current_object = get_post( $lps_current_queried_object_id );
		} else {
			$current_object = get_queried_object();
		}

		// Maybe filter some more shortcode arguments.
		$args = apply_filters( 'lps/shortcode_arguments', $args );
		$args = apply_filters_deprecated( 'lps_filter_use_custom_shortcode_arguments', [ $args ], '12.1.0', 'lps/shortcode_arguments' );

		$args['ver'] = isset( $args['ver'] ) ? abs( (int) $args['ver'] ) : 1;
		$args['ver'] = $args['ver'] >= 2 ? 2 : 1;

		// Maybe use the site id.
		$args['site_id'] = is_multisite() && ! empty( $args['site_id'] ) ? (int) $args['site_id'] : 0;

		$site_switched = false;
		$site_initial  = 0;
		$site_expected = 0;
		if ( ! empty( $args['site_id'] ) && \get_current_blog_id() !== $args['site_id'] ) {
			$site_initial  = \get_current_blog_id();
			$site_expected = $args['site_id'];
			$site_switched = true;
			\switch_to_blog( $args['site_id'] );
		}

		$maxpg = 0;
		if ( empty( $args['output'] ) && ! empty( $args['limit'] ) && ! empty( $args['perpage'] ) ) {
			// Limit pagination items.
			$paged = get_query_var( 'paged' ) ? abs( intval( get_query_var( 'paged' ) ) ) : 1;
			$maxpg = ceil( (int) $args['limit'] / (int) $args['perpage'] );
			if ( $paged > $maxpg ) {
				// No further computation, the pagination limit was reached.
				return false;
			}
		}

		// Get the post arguments from shortcode arguments.
		$ids         = ( ! empty( $args['id'] ) ) ? explode( ',', $args['id'] ) : [];
		$exclude_ids = ( ! empty( $args['excludeid'] ) ) ? explode( ',', $args['excludeid'] ) : [];
		if ( ! empty( $args['dparent'] ) ) {
			$parent = ! empty( $current_object->post_parent ) ? [ (int) $current_object->post_parent ] : [ -9999 ];
		} else {
			$parent = ( ! empty( $args['parent'] ) ) ? explode( ',', $args['parent'] ) : [];
		}

		if ( ! empty( $args['dauthor'] ) ) {
			$author = ! empty( $current_object->post_author ) ? [ (int) $current_object->post_author ] : [ -9999 ];
		} else {
			$author = ( ! empty( $args['author'] ) ) ? explode( ',', $args['author'] ) : [];
		}
		$exclude_authors = ( ! empty( $args['excludeauthor'] ) ) ? explode( ',', $args['excludeauthor'] ) : [];

		$type = ( ! empty( $args['type'] ) ) ? $args['type'] : 'post';
		if ( substr_count( $type, ',' ) ) {
			$type = explode( ',', $type );
		}

		$titletag      = ( ! empty( $args['titletag'] ) && in_array( $args['titletag'], self::$title_tags, true ) ) ? $args['titletag'] : 'h3';
		$chrlimit      = ( ! empty( $args['chrlimit'] ) ) ? intval( $args['chrlimit'] ) : 120;
		$trimmore      = ( ! empty( $args['more'] ) ) ? $args['more'] : '';
		$extra_display = ( ! empty( $args['display'] ) ) ? explode( ',', $args['display'] ) : [ 'title' ];
		$linkurl       = ( ! empty( $args['url'] ) && ( 'yes' === $args['url'] || 'yes_blank' === $args['url'] ) ) ? true : false;
		$linkmedia     = ( ! empty( $args['url'] ) && ( 'yes_media' === $args['url'] || 'yes_media_blank' === $args['url'] || 'yes_media_lightbox' === $args['url'] ) ) ? true : false;
		$lightbox_size = ( $linkmedia && ! empty( $args['lightbox_size'] ) ) ? $args['lightbox_size'] : '';
		$lightbox_attr = ( $linkmedia && ! empty( $args['lightbox_attr'] ) ) ? $args['lightbox_attr'] : '';
		$lightbox_val  = ( $linkmedia && ! empty( $args['lightbox_val'] ) ) ? $args['lightbox_val'] : '';
		$linkblank     = ( ! empty( $args['url'] ) && ( 'yes_blank' === $args['url'] || 'yes_media_blank' === $args['url'] || 'yes_media_lightbox' === $args['url'] ) ) ? true : false;

		$tile_type = 0;
		if ( $linkurl || $linkmedia ) {
			$linktext = ( ! empty( $args['linktext'] ) ) ? $args['linktext'] : '';
		}

		$tile_type = ( ! empty( $args['elements'] ) && ! empty( self::$tile_pattern[ $args['elements'] ] ) ) ? $args['elements'] : 0;

		if ( $args['ver'] >= 2 ) {
			// Version >= 2 markup.
			if ( ! substr_count( '_custom_', $tile_type )
				&& ! in_array( $tile_type, self::$tile_pattern_ver2, true ) ) {
				$tile_type = 0;
			}
		}

		$tile_pattern = ( ! empty( self::$tile_pattern[ $tile_type ] ) ) ? self::$tile_pattern[ $tile_type ] : 'title';

		$link_class      = ' class="main-link"';
		$read_more_class = ' class="read-more"';

		$tiles_custom_style_vars = '';
		if ( ! empty( $args['default_height'] ) ) {
			$tiles_custom_style_vars .= ' --default-tile-height: ' . esc_attr( $args['default_height'] ) . ';';
		}
		if ( ! empty( $args['default_padding'] ) ) {
			$tiles_custom_style_vars .= ' --default-tile-padding: ' . esc_attr( $args['default_padding'] ) . ';';
		}
		if ( ! empty( $args['default_gap'] ) ) {
			$tiles_custom_style_vars .= ' --default-tile-gap: ' . esc_attr( $args['default_gap'] ) . ';';
		}
		if ( ! empty( $args['default_overlay_padding'] ) ) {
			$tiles_custom_style_vars .= ' --default-overlay-padding: ' . esc_attr( $args['default_overlay_padding'] ) . ';';
		}

		if ( ! empty( $args['tablet_height'] ) ) {
			$tiles_custom_style_vars .= ' --tablet-tile-height: ' . esc_attr( $args['tablet_height'] ) . ';';
		}
		if ( ! empty( $args['tablet_padding'] ) ) {
			$tiles_custom_style_vars .= ' --tablet-tile-padding: ' . esc_attr( $args['tablet_padding'] ) . ';';
		}
		if ( ! empty( $args['tablet_gap'] ) ) {
			$tiles_custom_style_vars .= ' --tablet-tile-gap: ' . esc_attr( $args['tablet_gap'] ) . ';';
		}
		if ( ! empty( $args['tablet_overlay_padding'] ) ) {
			$tiles_custom_style_vars .= ' --tablet-overlay-padding: ' . esc_attr( $args['tablet_overlay_padding'] ) . ';';
		}

		if ( ! empty( $args['mobile_height'] ) ) {
			$tiles_custom_style_vars .= ' --mobile-tile-height: ' . esc_attr( $args['mobile_height'] ) . ';';
		}
		if ( ! empty( $args['mobile_padding'] ) ) {
			$tiles_custom_style_vars .= ' --mobile-tile-padding: ' . esc_attr( $args['mobile_padding'] ) . ';';
		}
		if ( ! empty( $args['mobile_gap'] ) ) {
			$tiles_custom_style_vars .= ' --mobile-tile-gap: ' . esc_attr( $args['mobile_gap'] ) . ';';
		}
		if ( ! empty( $args['mobile_overlay_padding'] ) ) {
			$tiles_custom_style_vars .= ' --mobile-overlay-padding: ' . esc_attr( $args['mobile_overlay_padding'] ) . ';';
		}

		if ( ! empty( $args['color_text'] ) ) {
			$tiles_custom_style_vars .= ' --article-text-color: ' . esc_attr( $args['color_text'] ) . ';';
		}
		if ( ! empty( $args['color_title'] ) ) {
			$tiles_custom_style_vars .= ' --article-title-color: ' . esc_attr( $args['color_title'] ) . ';';
		}
		if ( ! empty( $args['color_bg'] ) ) {
			$tiles_custom_style_vars .= ' --article-bg-color: ' . esc_attr( $args['color_bg'] ) . ';';
		}
		if ( ! empty( $args['size_text'] ) ) {
			$tiles_custom_style_vars .= ' --article-size-text: ' . esc_attr( $args['size_text'] ) . ';';
		}
		if ( ! empty( $args['size_title'] ) ) {
			$tiles_custom_style_vars .= ' --article-size-title: ' . esc_attr( $args['size_title'] ) . ';';
		}
		if ( ! empty( $args['image_opacity'] ) ) {
			$tiles_custom_style_vars .= ' --article-image-opacity: ' . esc_attr( $args['image_opacity'] ) . ';';
		}
		if ( ! empty( $args['size_image'] ) ) {
			$args['css'] .= ' has-image-size';
			$args['css']  = trim( $args['css'] );

			$tiles_custom_style_vars .= ' --article-image-size: ' . esc_attr( $args['size_image'] ) . ';';
		}
		if ( ! empty( $args['image_ratio'] ) ) {
			if ( 'contain' === $args['image_ratio'] ) {
				$args['css'] .= ' has-image-contain';
			} else {
				$args['css'] .= ' has-image-ratio';

				$tiles_custom_style_vars .= ' --article-image-ratio: ' . esc_attr( $args['image_ratio'] ) . ';';
			}

			$args['css'] = trim( $args['css'] );
		}
		if ( ! empty( $args['card_ratio'] ) ) {
			$tiles_custom_style_vars .= ' --article-ratio: ' . esc_attr( $args['card_ratio'] ) . ';';
		}

		$lightbox_extra = '';
		if ( in_array( (int) $tile_type, [ 3, 11, 14, 19 ], true ) ) {
			$link_class      = ' class="main-link read-more-wrap"';
			$read_more_class = '';
		}
		if ( ! empty( $lightbox_attr ) ) {
			if ( 'class' === $lightbox_attr ) {
				if ( $link_class ) {
					$link_class = str_replace( 'class="', 'class="' . esc_attr( $lightbox_val ) . ' ', $link_class );
				}
				if ( $read_more_class ) {
					$read_more_class = str_replace( 'class="', 'class="' . esc_attr( $lightbox_val ) . ' ', $read_more_class );
				}
			} else {
				$lightbox_extra = ' ' . esc_attr( $lightbox_attr ) . '="' . esc_attr( $lightbox_val ) . '"';
			}
		}

		$show_extra  = ( ! empty( $args['show_extra'] ) ) ? explode( ',', $args['show_extra'] ) : [];
		$raw_content = ( in_array( 'raw', $show_extra, true ) ) ? true : false;
		$trim_text   = ( in_array( 'trim', $show_extra, true ) ) ? true : false;
		$is_scroller = ( in_array( 'scroller', $show_extra, true ) ) ? true : false;

		if ( ! empty( $is_scroller ) ) {
			$args['css']  = str_replace( ' hover-highlight', '', $args['css'] );
			$args['css'] .= ' scroller';
			if ( in_array( 'with_counter', $show_extra, true ) ) {
				$args['css'] .= ' with-counter';

				if ( in_array( 'reverse_counter', $show_extra, true ) ) {
					$args['css'] .= ' reverse-counter';
				}
			}
			$args['css'] = trim( $args['css'] );
		}

		$reset_post_css = in_array( 'reset_css', $show_extra, true ) ? true : false;
		$inherit_style  = in_array( 'inherit_style', $show_extra, true ) ? true : false;

		$qargs = [
			'numberposts' => 1,
			'post_status' => 'publish',
		];

		if ( ! empty( $args['status'] ) ) {
			$qargs['post_status'] = explode( ',', trim( $args['status'] ) );
			if ( in_array( 'private', $qargs['post_status'], true ) ) {
				if ( ! is_user_logged_in() ) {
					$pkey = array_search( 'private', $qargs['post_status'], true );
					if ( false !== $pkey ) {
						unset( $qargs['post_status'][ $pkey ] );
					}
				}
			}
		}
		if ( empty( $qargs['post_status'] ) ) {
			return;
		}

		self::$current_query_statuses_list = $qargs['post_status'];

		$orderby          = ( ! empty( $args['orderby'] ) ) ? $args['orderby'] : 'dateD';
		$qargs['order']   = 'DESC';
		$qargs['orderby'] = 'date';
		if ( ! empty( $orderby ) && ! empty( self::$orderby_options[ $orderby ] ) ) {
			$qargs['order']   = self::$orderby_options[ $orderby ]['order'];
			$qargs['orderby'] = self::$orderby_options[ $orderby ]['orderby'];
			if ( substr_count( $qargs['orderby'], 'meta_value' ) ) {
				$qargs['meta_key'] = $args['orderby_meta']; // phpcs:ignore
			}
		}

		$is_lps_archive = ! empty( $args['archive'] ) || ! empty( $args['archive_s'] )
			|| ! empty( $args['archive_tax'] );
		$is_lps_search  = ! empty( $args['search'] );

		// Make sure we do not loop in the current page.
		if ( ! ( $is_lps_archive || $is_lps_search ) ) {
			if ( ! empty( $post->ID ) ) {
				$qargs['post__not_in'] = [ $post->ID ];
			}
		}

		// Exclude specified post IDs.
		if ( ! empty( $exclude_ids ) ) {
			if ( ! empty( $qargs['post__not_in'] ) ) {
				$qargs['post__not_in'] = array_merge( $qargs['post__not_in'], $exclude_ids );
			} else {
				$qargs['post__not_in'] = $exclude_ids;
			}
		}

		if ( ! empty( $show_extra ) && in_array( 'exclude_previous_content', $show_extra, true ) ) {
			// Exclude the previous ID embedded through the plugin shortcodes on this page.
			if ( ! isset( $qargs['post__not_in'] ) ) {
				$qargs['post__not_in'] = [];
			}

			if ( is_scalar( $qargs['post__not_in'] ) ) {
				$qargs['post__not_in'] = [ $qargs['post__not_in'] ];
			}
			if ( empty( $lps_current_post_embedded_item_ids ) ) {
				$lps_current_post_embedded_item_ids = [];
			}
			$qargs['post__not_in'] = array_merge( $qargs['post__not_in'], $lps_current_post_embedded_item_ids );
		}

		if ( ! empty( $author ) ) {
			$qargs['author__in'] = $author;
		}

		if ( ! empty( $exclude_authors ) ) {
			$qargs['author__not_in'] = $exclude_authors;
		}

		if ( ! empty( $args['limit'] ) ) {
			$qargs['numberposts']    = ( ! empty( $args['limit'] ) ) ? intval( $args['limit'] ) : 1;
			$qargs['posts_per_page'] = ( ! empty( $args['limit'] ) ) ? intval( $args['limit'] ) : 1;
		}
		if ( empty( $args['output'] ) ) {
			$use_offset = true;
			if ( ! empty( $args['perpage'] ) ) {
				$qargs['posts_per_page'] = ( ! empty( $args['perpage'] ) ) ? intval( $args['perpage'] ) : 0;

				$current_page   = self::get_current_page();
				$qargs['paged'] = $current_page;
				$qargs['page']  = $current_page;

				$diff = $qargs['numberposts'] - $current_page * $qargs['posts_per_page'];
				if ( $diff <= 0 && $maxpg >= $current_page ) {
					// This is the forced limit on pagination.
					$qargs['posts_per_page'] = $qargs['posts_per_page'] - abs( $diff );
					$qargs['offset']         = abs( $qargs['numberposts'] - abs( $diff ) );
					if ( ! empty( $args['offset'] ) ) {
						$qargs['offset'] += intval( $args['offset'] );
					}
					$use_offset = false;
				}
			}
			if ( ! empty( $args['offset'] ) && true === $use_offset ) {
				$qargs['offset'] = ( ! empty( $args['offset'] ) ) ? intval( $args['offset'] ) : 0;
				if ( ! empty( $qargs['paged'] ) && $qargs['paged'] > 1 ) {
					$qargs['offset'] = abs( $current_page - 1 ) * $qargs['posts_per_page'] + $args['offset'];
				}
			}
		}

		$force_type = true;
		if ( ! empty( $ids ) && is_array( $ids ) ) {
			foreach ( $ids as $k => $v ) {
				$ids[ $k ] = intval( $v );
			}
			$qargs['post__in'] = $ids;
			$force_type        = false;
		}

		if ( $force_type ) {
			$qargs['post_type'] = $type;
		} elseif ( ! empty( $args['type'] ) ) {
				$qargs['post_type'] = $args['type'];
		}

		if ( $is_lps_archive || $is_lps_search ) {
			if ( ! empty( $args['type'] ) ) {
				$qargs['post_type'] = $args['type'];
			} else {
				$qargs['post_type'] = 'any';
			}
		}

		if ( empty( $qargs['post_type'] ) ) {
			$qargs['post_type'] = '';
		}

		if ( ! empty( $parent ) ) {
			$qargs['post_parent__in'] = $parent;
		}

		if ( ! empty( $args['search'] ) ) {
			$qargs['s'] = wp_strip_all_tags( esc_html( $args['search'] ) );
		}

		$qargs['tax_query'] = []; // phpcs:ignore

		if ( $is_lps_archive ) {
			$option_per_page         = get_option( 'posts_per_page' );
			$qargs['posts_per_page'] = (int) $option_per_page;
			$search_query            = get_search_query();
			if ( empty( $search_query ) ) {
				$maybe_args   = filter_input( INPUT_POST, 'args', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
				$search_query = $maybe_args['archive_s'] ?? '';
			}
			if ( ! empty( $search_query ) ) {
				$args['archive_s'] = wp_strip_all_tags( $search_query );
				$qargs['s']        = wp_strip_all_tags( $search_query );
			} else {
				$archive_object = \get_queried_object();
				if ( ! empty( $archive_object->taxonomy ) && ! empty( $archive_object->term_id ) ) {
					$archive_taxonomy = $archive_object->taxonomy;
					$archive_term_id  = $archive_object->term_id;
				} else {
					$maybe_args       = filter_input( INPUT_POST, 'args', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
					$archive_taxonomy = $maybe_args['archive_tax'] ?? '';
					$archive_term_id  = $maybe_args['archive_id'] ?? '';
				}

				if ( ! empty( $archive_taxonomy ) && ! empty( $archive_term_id ) ) {
					$args['archive_tax'] = wp_strip_all_tags( $archive_taxonomy );
					$args['archive_id']  = (int) $archive_term_id;

					array_push(
						$qargs['tax_query'],
						[
							'relation' => 'AND',
						]
					);

					array_push(
						$qargs['tax_query'],
						[
							'taxonomy' => $archive_taxonomy,
							'field'    => 'term_id',
							'terms'    => [ (int) $archive_term_id ],
						]
					);
				}
			}
		} else {
			if ( ! empty( $args['tag'] ) ) {
				array_push(
					$qargs['tax_query'],
					[
						'taxonomy' => 'post_tag',
						'field'    => 'slug',
						'terms'    => ( ! empty( $args['tag'] ) ) ? explode( ',', $args['tag'] ) : 'homenews',
					]
				);
			}
			if ( ! empty( $args['dtag'] ) && ! empty( $post->ID ) ) {
				$tag_ids = wp_get_post_tags(
					$post->ID,
					[
						'fields' => 'ids',
					]
				);
				if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
					if ( ! empty( $qargs['tax_query'] ) ) {
						array_push(
							$qargs['tax_query'],
							[
								'relation' => 'AND',
							]
						);
					}
					array_push(
						$qargs['tax_query'],
						[
							'taxonomy' => 'post_tag',
							'field'    => 'term_id',
							'terms'    => $tag_ids,
							'operator' => 'IN',
						]
					);
				}
			}
			if ( ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
				$include_children = true;
				if ( ! empty( $show_extra ) && in_array( 'term_strict', $show_extra, true ) ) {
					$include_children = false;
				}
				if ( ! empty( $qargs['tax_query'] ) ) {
					array_push(
						$qargs['tax_query'],
						[
							'relation' => 'AND',
						]
					);
				}
				array_push(
					$qargs['tax_query'],
					[
						'taxonomy'         => $args['taxonomy'],
						'field'            => 'slug',
						'terms'            => explode( ',', $args['term'] ),
						'include_children' => $include_children,
					]
				);
			}
			if ( ! empty( $args['taxonomy2'] ) && ! empty( $args['term2'] ) ) {
				$include_children = true;
				if ( ! empty( $show_extra ) && in_array( 'term2_strict', $show_extra, true ) ) {
					$include_children = false;
				}
				if ( ! empty( $qargs['tax_query'] ) ) {
					array_push(
						$qargs['tax_query'],
						[
							'relation' => 'AND',
						]
					);
				}
				array_push(
					$qargs['tax_query'],
					[
						'taxonomy'         => $args['taxonomy2'],
						'field'            => 'slug',
						'terms'            => explode( ',', $args['term2'] ),
						'include_children' => $include_children,
					]
				);
			}
		}

		if ( ! empty( $args['exclude_tags'] ) ) {
			if ( ! empty( $qargs['tax_query'] ) ) {
				array_push(
					$qargs['tax_query'],
					[
						'relation' => 'AND',
					]
				);
			}
			array_push(
				$qargs['tax_query'],
				[
					'taxonomy' => 'post_tag',
					'field'    => 'slug',
					'terms'    => explode( ',', $args['exclude_tags'] ),
					'operator' => 'NOT IN',
				]
			);
		}
		if ( ! empty( $args['exclude_categories'] ) ) {
			if ( ! empty( $qargs['tax_query'] ) ) {
				array_push(
					$qargs['tax_query'],
					[
						'relation' => 'AND',
					]
				);
			}
			array_push(
				$qargs['tax_query'],
				[
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => explode( ',', $args['exclude_categories'] ),
					'operator' => 'NOT IN',
				]
			);
		}

		if ( ! empty( $args['date_limit'] ) && ( ! empty( $args['date_start'] ) || ! empty( $args['date_start_type'] ) ) ) {
			$drange = ( ! empty( $args['date_start_type'] ) && in_array( $args['date_start_type'], self::$date_limit_units, true ) ) ? $args['date_start_type'] : 'months';
			$s_date = strtotime( gmdate( 'Y-m-d' ) . ' -' . abs( (int) $args['date_start'] ) . $drange );
			if ( ! empty( $s_date ) ) {
				$args['date_after'] = gmdate( 'Y-m-d', $s_date );
			}
		}
		if ( ! empty( $args['date_after'] ) || ! empty( $args['date_before'] ) ) {
			$drange = [];
			if ( ! empty( $args['date_after'] ) ) {
				$drange['after'] = esc_attr( $args['date_after'] );
			}
			if ( ! empty( $args['date_before'] ) ) {
				$drange['before'] = esc_attr( $args['date_before'] );
			}
			$qargs['date_query'] = [
				[
					array_merge(
						$drange,
						[
							'inclusive' => true,
						]
					),
				],
			];
		}

		if ( ! empty( $show_extra ) ) {
			if ( in_array( 'nosticky', $show_extra, true ) ) {
				$qargs['ignore_sticky_posts'] = true;

				$sticky_ids = get_option( 'sticky_posts' );
				if ( ! empty( $sticky_ids ) ) {
					if ( ! empty( $qargs['post__not_in'] ) ) {
						$qargs['post__not_in'] = array_merge( $qargs['post__not_in'], $sticky_ids );
					} else {
						$qargs['post__not_in'] = $sticky_ids;
					}
				}
			} elseif ( in_array( 'sticky', $show_extra, true ) ) {
				$qargs['ignore_sticky_posts'] = false;

				$sticky_ids = get_option( 'sticky_posts' );
				if ( ! empty( $sticky_ids ) ) {
					if ( ! empty( $qargs['post__in'] ) ) {
						$qargs['post__in'] = array_merge( $qargs['post__in'], $sticky_ids );
					} else {
						$qargs['post__in'] = $sticky_ids;
					}
				}
			}
		}

		$qargs = array_filter( $qargs );

		$qargs['post_type']        = $qargs['post_type'] ?? '';
		$qargs['suppress_filters'] = false;

		// Maybe use some custom query arguments.
		$qargs = apply_filters( 'lps/query_arguments', $qargs );
		$qargs = apply_filters_deprecated( 'lps_filter_use_custom_query_arguments', [ $qargs ], '12.1.0', 'lps/query_arguments' );
		if ( 'attachment' === $qargs['post_type'] ) {
			add_filter( 'posts_where', [ get_called_class(), 'attachment_custom_where' ], 50, 2 );
			add_filter( 'posts_join', [ get_called_class(), 'attachment_custom_join' ], 50, 2 );
		}

		$use_cache     = ( in_array( 'cache', $show_extra, true ) ) ? true : false;
		$in_the_editor = self::is_in_the_editor();
		if ( ! empty( $use_cache ) ) {
			// Maybe cache the results.
			$trans_id = 'lps-cache-' . ( $in_the_editor ? 'editor-' : '' ) . md5( self::get_assets_version() . wp_json_encode( $qargs ) . ( $args['lps_instance_id'] ?? '' ) );
			$lpstrans = get_transient( $trans_id );
			if ( false !== $lpstrans && ! empty( $lpstrans ) ) {
				return $lpstrans;
			}
		}

		$posts = get_posts( $qargs );

		// If the slider extension is enabled and the shortcode is configured to output the slider, let's do that and return.
		if ( ! empty( $posts ) && ! empty( $args['output'] ) && 'slider' === $args['output'] ) {
			ob_start();
			if ( $reset_post_css ) {
				self::maybe_remove_post_class_filters();
			}

			self::latest_selected_content_slider( $posts, $args, $use_cache );
			wp_reset_postdata();
			$result = ob_get_clean();
			if ( ! empty( $use_cache ) && empty( $in_the_editor ) ) {
				set_transient( $trans_id, $result, 30 * DAY_IN_SECONDS );
			}

			if ( $site_switched ) {
				restore_current_blog();
			}

			if ( $reset_post_css ) {
				self::maybe_restore_post_class_filters();
			}

			return $result;
		}

		$is_lps_ajax = (int) filter_input( INPUT_POST, 'lps_ajax', FILTER_DEFAULT );
		if ( empty( $is_lps_ajax ) ) {
			$is_lps_ajax = (int) filter_input( INPUT_GET, 'lps_ajax', FILTER_DEFAULT );
		}

		$shortcode_id = ! empty( $is_ajax_shortcode_id ) ? $is_ajax_shortcode_id : 'lps-' . md5( wp_json_encode( $args ) . microtime() );

		ob_start();

		$forced_end  = '';
		$closing_tag = '';
		if ( ! empty( $qargs['posts_per_page'] ) && ! empty( $args['showpages'] ) ) {
			$pagination_class  = in_array( 'pagination_all', $show_extra, true ) ? 'all-elements' : '';
			$pagination_class .= ( 'more' === $args['showpages'] ) ? ' lps-load-more' : '';
			$pagination_class .= ( 'scroll' === $args['showpages'] ) ? ' lps-load-more-scroll' : '';
			if ( ! empty( $args['css'] ) ) {
				if ( substr_count( $args['css'], 'pagination-center' ) ) {
					$pagination_class .= ' pagination-center';
				} elseif ( substr_count( $args['css'], 'pagination-right' ) ) {
					$pagination_class .= ' pagination-right';
				} elseif ( substr_count( $args['css'], 'pagination-space-between' ) ) {
					$pagination_class .= ' pagination-space-between';
				}
			}

			if ( ! empty( $lightbox_attr ) ) {
				$pagination_class .= ' lps-lightbox';
			}

			$counter     = new WP_Query( $qargs );
			$found_posts = ( ! empty( $counter->found_posts ) ) ? (int) $counter->found_posts : 0;
			if ( ! empty( $args['limit'] ) && $found_posts > $args['limit'] ) {
				$found_posts = (int) $args['limit'];
			}

			$pagination_per_page = ! empty( $qargs['posts_per_page'] ) ? $qargs['posts_per_page'] : 1;
			$pagination_html     = self::lps_pagination(
				$found_posts,
				$pagination_per_page,
				intval( $args['showpages'] ),
				$shortcode_id,
				$pagination_class,
				[
					'loadtext'   => ! empty( $args['loadtext'] ) ? $args['loadtext'] : '',
					'total_text' => ! empty( $args['total_text'] ) ? $args['total_text'] : '',
					'show_total' => in_array( 'show_total', $show_extra, true ),
					'hide_more'  => in_array( 'hide_more', $show_extra, true ),
				],
				$maxpg,
				$site_initial,
				$site_expected
			);

			if ( ! empty( $is_lps_ajax ) ) { // phpcs:ignore
				// No need to put again the top level.
			} else {
				$use_data_args = false;
				$closing_tag   = '</div><!-- lps/end -->';
				if ( ! empty( $args['showpages'] )
					&& ( 'more' === $args['showpages'] || 'scroll' === $args['showpages'] ) ) {
					$use_data_args = true;
				}
				if ( in_array( 'ajax_pagination', $show_extra, true ) && ! empty( $args ) && is_array( $args ) ) {
					$use_data_args = true;
				}

				if ( true === $use_data_args ) {
					$maybe_spinner = '';
					if ( in_array( 'light_spinner', $show_extra, true ) ) {
						$maybe_spinner = ' light_spinner';
					} elseif ( in_array( 'dark_spinner', $show_extra, true ) ) {
						$maybe_spinner = ' dark_spinner';
					}

					$min_args         = array_filter( $args );
					$data_args_string = wp_json_encode( $min_args, JSON_UNESCAPED_UNICODE );
					if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
						$data_args_string = wp_json_encode( $min_args );
					}

					if ( is_multisite() && $site_initial !== $site_expected ) {
						switch_to_blog( $site_initial );
					}

					$data_exclude = '';
					if ( ! empty( $show_extra ) && in_array( 'exclude_previous_content', $show_extra, true ) ) {
						global $lps_current_post_embedded_item_ids;

						$data_exclude = ' data-exclude="' . esc_js( implode( ',', $lps_current_post_embedded_item_ids ) ) . '"';
					}

					echo '<!-- lps/start --><div id="' . esc_attr( $shortcode_id ) . '-wrap" data-args="' . esc_js( $data_args_string ) . '" data-current="' . get_the_ID() . '" data-perpage="' . $pagination_per_page . '" data-total="' . $found_posts . '" class="lps-top-section-wrap' . $maybe_spinner . '" data-url="' . esc_url( \get_pagenum_link( 1 ) ) . '"' . $data_exclude . '>'; // phpcs:ignore

					if ( is_multisite() && $site_initial !== $site_expected ) {
						switch_to_blog( $site_expected );
					}
				} else {
					echo '<!-- lps/start --><div id="' . esc_attr( $shortcode_id ) . '-wrap" class="lps-top-section-wrap">';
				}
			}

			if ( empty( $args['pagespos'] ) || ( ! empty( $args['pagespos'] ) && 2 === (int) $args['pagespos'] ) ) {
				echo str_replace( 'lps-pagination-wrap', 'before lps-pagination-wrap', $pagination_html ); // phpcs:ignore
			}
		}

		if ( $reset_post_css ) {
			self::maybe_remove_post_class_filters();
		}

		if ( ! empty( $posts ) ) {
			if ( in_array( 'date', $extra_display, true ) ) {
				$date_format = get_option( 'date_format' ) . ' \<\i\>' . get_option( 'time_format' ) . '\<\/\i\>';
			}

			$class  = ( ! empty( $args['css'] ) ) ? ' ' . $args['css'] : '';
			$class .= ( ! empty( $args['ver'] ) && 2 === $args['ver'] ) ? ' ver2' : '';
			if ( in_array( 'ajax_pagination', $show_extra, true ) ) {
				$class .= ' ajax_pagination';
			}

			$use_custom_markup   = false;
			$filter_element_type = 'elements-' . (int) $args['elements'];

			$maybe_custom = trim( str_replace( '[', '', str_replace( ']', '', str_replace( '][', '_', $args['display'] ) ) ) );
			if ( '_custom_' === substr( $maybe_custom, 0, 8 ) ) {
				$use_custom_markup   = true;
				$filter_element_type = $maybe_custom;
			}

			if ( in_array( 'with_counter', $show_extra, true ) ) {
				$is_reverse = in_array( 'reverse_counter', $show_extra, true );
				if ( ! empty( $pagination_per_page ) ) {
					// Has some pagination.
					if ( 'more' !== $args['showpages'] && 'scroll' !== $args['showpages'] ) {
						// The inline pagination responses.
						$pagination_current_page = self::get_current_page();
						if ( $is_reverse ) {
							$tiles_custom_style_vars .= ' --lpscontor-start: ' . ( $found_posts + 1 - intval( ( $pagination_current_page - 1 ) * $pagination_per_page ) ) . ';';
						} else {
							$tiles_custom_style_vars .= ' --lpscontor-start: ' . intval( ( $pagination_current_page - 1 ) * $pagination_per_page ) . ';';
						}
					} elseif ( $is_reverse ) {
						$tiles_custom_style_vars .= ' --lpscontor-start: ' . ( $found_posts + 1 ) . ';';
					}
				} elseif ( $is_reverse ) {
					// No pagination involved but using reverse counter.
					$max = ! empty( $found_posts ) ? $found_posts : (int) $args['limit'];

					$tiles_custom_style_vars .= ' --lpscontor-start: ' . ( $max + 1 ) . ';';
				}
			}

			// Section start.
			$section_start = apply_filters(
				'lps/override_section_start',
				'<section class="latest-post-selection' . esc_attr( $class ) . '" id="' . esc_attr( $shortcode_id ) . '" style="' . $tiles_custom_style_vars . '">',
				$shortcode_id,
				$class,
				$filter_element_type,
				$is_lps_ajax_call,
				$args
			);

			if ( $use_custom_markup && $args['ver'] < 2 ) {
				// Legacy markup.
				$start = apply_filters_deprecated( 'lps_filter_use_custom_section_markup_start', [ $tile_pattern, $shortcode_id, $class, $args ], '11.4.0', 'lps/override_section_start' );
				if ( ! substr_count( $start, esc_attr( $shortcode_id ) ) ) {
					$start       = '<div id="' . esc_attr( $shortcode_id ) . '" class="' . trim( esc_attr( $class ) ) . '">' . $start;
					$forced_end .= '</div>';
				}
				echo $start; // phpcs:ignore
			}

			if ( $is_lps_ajax_call ) {
				// Nothing to output for the section start.
				$section_start = '';
			}
			echo $section_start; // phpcs:ignore

			$tile_pattern      = self::positions_from_extra( $show_extra, $tile_pattern, $args, $extra_display );
			$tile_elements     = (int) $args['elements'];
			$markup_info_start = '#1$*#';
			$markup_info_end   = '#3$*#';
			if ( $args['ver'] >= 2 ) {

				// Version >= 2 markup.
				if ( substr_count( $tile_pattern, '[image][' ) ) {
					// Image first, info second.
					$tile_pattern = str_replace( '[image][', '[image]' . $markup_info_start . '[', $tile_pattern ) . $markup_info_end;
				} elseif ( substr_count( $tile_pattern, '][image]' ) ) {
					// Info first, image second.
					$tile_pattern = $markup_info_start . str_replace( '][image]', ']' . $markup_info_end . '[image]', $tile_pattern );
				}
			}

			$markup_sep     = '#7$*#';
			$tile_keep_tags = [];
			foreach ( self::$title_tags as $k ) {
				$tile_keep_tags[ $k ] = [
					'class' => 1,
					'id'    => 1,
				];
			}
			$tile_keep_tags[ $titletag ] = [
				'class' => 1,
				'id'    => 1,
			];

			$tile_keep_tags['br'] = [];

			$card_output_type_from_args = self::get_card_output_type_from_args( $args );

			global $last_tiles_img;
			foreach ( $posts as $postobj ) {
				$post = $postobj; // phpcs:ignore
				// Collect the IDs for the current page from the shortcode results.
				array_push( $lps_current_post_embedded_item_ids, $postobj->ID );
				$tile = $tile_pattern;

				if ( $use_custom_markup ) {
					if ( 1 === $args['ver'] ) {
						// Legacy markup.
						echo apply_filters_deprecated( 'lps_filter_use_custom_tile_markup', [ $tile_pattern, $postobj, $args ], '11.4.0', 'lps/override_card' ); // phpcs:ignore
					} else {
						// Card markup.
						$card_markup = apply_filters(
							'lps/override_card',
							'',
							$filter_element_type,
							$postobj,
							$args,
							$card_output_type_from_args
						);

						echo $card_markup; // phpcs:ignore
					}
				} else {
					$a_start   = '';
					$ar_start  = '';
					$a_end     = '';
					$title_str = self::cleanup_title( $postobj->post_title );

					if ( $linkurl || $linkmedia || substr_count( $class, 'as-overlay' ) ) {
						$link_target = ( ! empty( $linkblank ) ) ? ' target="_blank"' : '';
						if ( $linkmedia ) {
							$mediaurl = wp_get_attachment_image_src( $postobj->ID, $lightbox_size );
							$mediaurl = ( ! empty( $mediaurl[0] ) ) ? $mediaurl[0] : '';
							$hr       = ( ! empty( $mediaurl ) ) ? ' href="' . esc_url( $mediaurl ) . '"' : '';
						} else {
							$hr = ( ! empty( $linkurl ) ) ? ' href="' . esc_url( get_permalink( $postobj->ID ) ) . '"' : '';
						}

						if ( ! empty( $lightbox_attr ) ) {
							$hr .= ' rel="' . $shortcode_id . '"';
						}

						$a_start  = '<a' . $hr . $link_class . $lightbox_extra . $link_target . ' title="' . esc_attr( $title_str ) . '">';
						$ar_start = '<a' . $hr . $read_more_class . $lightbox_extra . $link_target . ' title="' . esc_attr( $title_str ) . '">';
						$a_end    = '</a>';
					}

					if ( $args['ver'] < 2 ) {
						// Legacy markup.
						$tile = str_replace( '[a]', $a_start, $tile );
						$tile = str_replace( '[a-r]', $ar_start, $tile );
						$tile = str_replace( '[/a]', $a_end, $tile );
					}

					// Tile replace image markup.
					$tile = self::set_tile_image( $postobj, $args, $tile );

					// Tile date markup.
					if ( in_array( 'date', $extra_display, true ) ) {
						if ( in_array( 'date_diff', $show_extra, true ) ) {
							$date_value = self::relative_time( $postobj->ID );
						} else {
							$date_value = date_i18n( $date_format, strtotime( $postobj->post_date ), true );
						}
						$tile = str_replace( '[date]', $markup_sep . '<em class="item-date">' . $date_value . '</em>', $tile );
					}
					$tile = str_replace( '[date]', '', $tile );

					// Tile tags markup.
					if ( in_array( 'tags', $show_extra, true ) ) {
						$one_term = in_array( 'oneterm_tags', $show_extra, true );
						$no_label = in_array( 'nolabel_tags', $show_extra, true );
						$no_link  = in_array( 'nolink_tags', $show_extra, true );
						$tags     = self::get_post_visible_term( (int) $postobj->ID, 'post_tag', $one_term, false, $no_label, $no_link, $class );

						if ( ! empty( $tags ) ) {
							$tags = str_replace( 'post_tag', 'post_tag tags', $tags );
							$tags = $markup_sep . '<span class="lps-tags-wrap">' . $tags . '</span>';
							$tags = apply_filters( 'lps/override_card_terms', $tags, (int) $postobj->ID, 'post_tag', $shortcode_id );
							$tile = str_replace( '[tags]', $tags, $tile );
						}
					}
					$tile = str_replace( '[tags]', '', $tile );

					// Tile author markup.
					if ( in_array( 'author', $show_extra, true ) ) {
						$no_label = in_array( 'nolabel_author', $show_extra, true );
						$no_link  = in_array( 'nolink_author', $show_extra, true );
						$author   = $markup_sep . '<div class="lps-author-wrap">';
						if ( ! $no_label ) {
							$author .= '<span class="lps-author">' . esc_html__( 'By', 'lps' ) . '</span> ';
						}
						if ( $no_link ) {
							$author .= esc_html( get_the_author_meta( 'display_name', $postobj->post_author ) );
						} else {
							$author .= '<a href="' . esc_url( get_author_posts_url( $postobj->post_author ) ) . '" class="lps-author-link">' . esc_html( get_the_author_meta( 'display_name', $postobj->post_author ) ) . '</a>';
						}
						$author .= '</div>';
						$tile    = str_replace( '[author]', $author, $tile );
					}
					$tile = str_replace( '[author]', '', $tile );

					if ( ( 'product' === $post->post_type || 'product_variation' === $post->post_type ) && function_exists( '\wc_get_product' ) ) {
						// Tile price markup.
						if ( in_array( 'price', $show_extra, true ) ) {
							$prod = \wc_get_product( (int) $post->ID );
							$info = $markup_sep . '<div class="lps-price-wrap">' . $prod->get_price_html() . '</div>';
							$tile = str_replace( '[price]', $info, $tile );
						}

						// Tile add to cart markup.
						if ( in_array( 'add_to_cart', $show_extra, true ) ) {
							$info = $markup_sep . '<div class="lps-add_to_cart-wrap">' . \do_shortcode( '[add_to_cart id="' . (int) $post->ID . '" style="" show_price="false"]' ) . '</div>';
							$tile = str_replace( '[add_to_cart]', $info, $tile );
						}

						// Tile price + add to cart markup.
						if ( in_array( 'price_add_to_cart', $show_extra, true ) ) {
							$info = $markup_sep . '<div class="lps-add_to_cart-wrap">' . \do_shortcode( '[add_to_cart id="' . (int) $post->ID . '" style=""]' ) . '</div>';
							$tile = str_replace( '[price_add_to_cart]', $info, $tile );
						}
					}
					$tile = str_replace( '[price]', '', $tile );
					$tile = str_replace( '[add_to_cart]', '', $tile );
					$tile = str_replace( '[price_add_to_cart]', '', $tile );

					$mime_css = '';
					if ( 'attachment' === $postobj->post_type ) {
						// Attachment tile mime type markup.
						if ( in_array( 'show_mime', $show_extra, true ) ) {
							$mime     = trim( strstr( $postobj->post_mime_type, '/' ), '/' );
							$mime_css = 'item-mime-type mime-' . esc_attr( $mime ) . ' mime-' . str_replace( '/', '-', esc_attr( $postobj->post_mime_type ) );

							$mime_label = ! in_array( 'nolabel_show_mime', $show_extra, true )
								? '<span>' . esc_html__( 'Mime Type', 'lps' ) . ':</span> ' : '';

							$tile = str_replace( '[show_mime]', '<span class="' . $mime_css . '">' . $mime_label . $mime . '</span>', $tile );
						}

						// Maybe prepare the mime type class.
						if ( in_array( 'show_mime_class', $show_extra, true ) ) {
							if ( empty( $mime_css ) ) {
								$mime     = trim( strstr( $postobj->post_mime_type, '/' ), '/' );
								$mime_css = 'item-mime-type mime-' . esc_attr( $mime ) . ' mime-' . str_replace( '/', '-', esc_attr( $postobj->post_mime_type ) );
							}
						} else {
							$mime_css = '';
						}

						// Attachment tile caption type markup.
						if ( in_array( 'caption', $show_extra, true ) ) {
							$caption = wp_get_attachment_caption( $postobj->ID );
							if ( ! empty( $caption ) ) {
								$caption = $markup_sep . '<div class="lps-caption-wrap"><span>' . esc_html__( 'Caption', 'lps' ) . ':</span> ' . esc_html( $caption ) . '</div>';
							}
							$tile = str_replace( '[caption]', $caption, $tile );
						}
					}
					$tile = str_replace( '[show_mime]', '', $tile );
					$tile = str_replace( '[caption]', '', $tile );

					// Tile taxonomies markup.
					$taxonomies = array_diff( $show_extra, [ 'tags', 'author', 'show_mime', 'caption', 'ajax_pagination', 'hide_uncategorized_category', 'show_total' ] );
					if ( ! empty( $taxonomies ) ) {
						foreach ( $taxonomies as $tax ) {
							$one_term = in_array( 'oneterm_' . $tax, $show_extra, true );
							$no_label = in_array( 'nolabel_' . $tax, $show_extra, true );
							$no_uncat = 'category' === $tax && in_array( 'hide_uncategorized_category', $show_extra, true );
							$no_link  = in_array( 'nolink_' . $tax, $show_extra, true );

							$terms = self::get_post_visible_term( (int) $postobj->ID, $tax, $one_term, $no_uncat, $no_label, $no_link, $class );
							$terms = apply_filters( 'lps/override_card_terms', $terms, (int) $postobj->ID, $tax, $shortcode_id );
							$tile  = str_replace( '[' . $tax . ']', $markup_sep . $terms, $tile );
						}
					}

					// Tile title markup.
					if ( in_array( 'title', $extra_display, true ) ) {
						if ( $args['ver'] >= 2 ) {
							// Version >= 2 markup.

							$visible_title_str = $title_str;
							if ( $trim_text ) {
								$visible_title_str = self::get_short_text( $visible_title_str, $chrlimit, false, $trimmore );
								if ( ! empty( $visible_title_str ) ) {
									$visible_title_str = wp_strip_all_tags( $visible_title_str );
								}
							}

							$tile = empty( $args['url'] ) ? str_replace( '[a][title][/a]', '[title]', $tile ) : $tile;
							if ( ! empty( $args['url'] ) ) {
								if ( 5 !== (int) $tile_elements && 22 !== (int) $tile_elements ) {
									$tile = str_replace( '[title]', '<' . $titletag . ' class="item-title-tag">' . $a_start . $visible_title_str . $a_end . '</' . $titletag . '>', $tile );
								}
							}
							// Fallback to no link on title.
							$tile = str_replace( '[title]', '<' . $titletag . ' class="item-title-tag">' . $visible_title_str . '</' . $titletag . '>', $tile );
						} else {
							// Legacy markup.
							$tile = str_replace( '[title]', '<' . $titletag . ' class="item-title-tag">' . $title_str . '</' . $titletag . '>', $tile );
						}
					}
					$tile = str_replace( '[title]', '', $tile );

					// Tile text markup.
					$text = '';
					if ( ! empty( $args['display'] )
						&& ( substr_count( $args['display'], 'content' ) || substr_count( $args['display'], 'excerpt' ) ) ) {
						$lim = $chrlimit;
						if ( $trim_text ) {
							$lim -= mb_strlen( $title_str );
							if ( $lim < 0 ) {
								$lim = 0;
							}
						}
						$text = '<div class="item-text">' . self::compute_tile_text( $postobj, $extra_display, $lim, $raw_content, $trimmore ) . '</div>';
					}
					$tile = str_replace( '[text]', $markup_sep . $text, $tile );

					if ( ! empty( $linktext ) ) {
						if ( $args['ver'] >= 2 ) {
							// Version >= 2 markup.
							if ( ! empty( $args['url'] ) ) {
								if ( 5 === (int) $tile_elements
									|| 22 === (int) $tile_elements
									|| 26 === (int) $tile_elements ) {

									if ( substr_count( $tile, 'main-link' ) ) {
										$tile = str_replace( '[read_more_text]', $markup_sep . '<span class="read-more">' . str_replace( 'main-link', '', $a_start ) . $linktext . $a_end . '</span>', $tile );
									} else {
										$tile = str_replace( '[read_more_text]', $markup_sep . '<span class="read-more">' . $a_start . $linktext . $a_end . '</span>', $tile );
									}
								}
							}
							// Fallback to replacing just the string.
							$tile = str_replace( '[read_more_text]', $markup_sep . '<span class="read-more">' . $linktext . '</span>', $tile );
						} else {
							// Legacy markup.
							$tile = str_replace( '[read_more_text]', $markup_sep . '<span class="read-more">' . $linktext . '</span>', $tile );
						}
					} else {
						$tile = str_replace( '[read_more_text]', '', $tile );
					}

					// Cleanup the remanining tags.
					$maybe_tile  = str_replace( $markup_info_start, '<div class="article__info">', $tile );
					$maybe_tile  = str_replace( $markup_info_end, '</div>', $maybe_tile );
					$maybe_tile  = preg_replace( '/\[(.*)\]/', '', $maybe_tile );
					$card_markup = '';

					$article_class = get_post_class( $mime_css, $postobj->ID );
					$article_class = apply_filters( 'lps/override_post_class', $article_class, $shortcode_id, $args, (int) $postobj->ID );

					if ( substr_count( $maybe_tile, 'main-link' ) ) {
						$article_class[] = 'has-link';
					}
					$article_class = ( ! empty( $article_class ) ) ? ' class="' . implode( ' ', $article_class ) . '"' : '';
					if ( substr_count( $class, 'as-overlay' ) && $args['ver'] < 2 ) {
						// Legacy markup.
						if ( ! empty( $last_tiles_img ) ) {
							$last_tiles_img = esc_url( $last_tiles_img );
						}
						$maybe_tile = str_replace( $markup_sep, ' ', $maybe_tile );
						$maybe_tile = wp_kses( $maybe_tile, $tile_keep_tags );

						$card_markup = '<article' . $article_class . ' style="background-image:url(\'' . $last_tiles_img . '\')" data-lps-id="' . (int) $postobj->ID . '"><div class="lps-ontopof-overlay">' . $a_start . $maybe_tile . $a_end . '</div></article>';
					} else {
						$maybe_tile  = str_replace( $markup_sep, '', $maybe_tile );
						$card_markup = '<article' . $article_class . ' data-lps-id="' . (int) $postobj->ID . '">' . $maybe_tile . '</article>';
					}

					if ( $args['ver'] >= 2 && $a_start && ! substr_count( $tile, $a_start ) ) {
						// Version >= 2 markup.
						$card_markup = str_replace( '<div class="article__info">', '<div class="article__info">' . str_replace( 'main-link', 'main-link hidden', $a_start ) . $a_end, $card_markup );
					}

					// Card markup.
					$card_markup = apply_filters(
						'lps/override_card',
						$card_markup,
						$tile_pattern,
						$postobj,
						$args,
						$card_output_type_from_args
					);

					if ( substr_count( $class, 'content-first-top' ) ) {
						$card_markup = self::maybe_info_row_template( $card_markup, 'first' );
					} elseif ( substr_count( $class, 'content-last-bottom' ) ) {
						$card_markup = self::maybe_info_row_template( $card_markup, 'last' );
					}

					echo $card_markup; // phpcs:ignore
				}
			}

			// Section end.
			$section_end = apply_filters(
				'lps/override_section_end',
				'</section>',
				$shortcode_id,
				$class,
				$filter_element_type,
				$is_lps_ajax_call,
				$args
			);

			if ( $use_custom_markup && $args['ver'] < 2 ) {
				// Legacy markup.
				echo apply_filters_deprecated( 'lps_filter_use_custom_section_markup_end', [ $tile_pattern, $shortcode_id, $class, $args ], '11.4.0', 'lps/override_section_end' ); // phpcs:ignore
				if ( ! empty( $forced_end ) ) {
					echo $forced_end; // phpcs:ignore
				}
			}

			if ( $is_lps_ajax_call ) {
				// Nothing to output for the section end.
				$section_end = '';
			}
			echo $section_end; // phpcs:ignore
		} elseif ( ! empty( $args['fallback'] ) ) {
				echo '<div class="lps-placeholder">' . wp_kses_post( $args['fallback'] ) . '</div>';
		}
		if ( ! empty( $qargs['posts_per_page'] ) && ! empty( $args['showpages'] ) ) {
			if ( ! empty( $args['pagespos'] ) && ( 1 === (int) $args['pagespos'] || 2 === (int) $args['pagespos'] ) ) {
				echo str_replace( 'lps-pagination-wrap', 'after lps-pagination-wrap', $pagination_html ); // phpcs:ignore
			}
		}

		echo $closing_tag; // phpcs:ignore

		$result = ob_get_clean();
		wp_reset_postdata(); // Previously wp_reset_query(), but new CS now.

		if ( $reset_post_css ) {
			self::maybe_restore_post_class_filters();
		}

		if ( ! empty( $use_cache ) && ! empty( $trans_id ) ) {
			if ( $in_the_editor ) {
				$result = str_replace( 'lps-top-section-wrap', 'lps-top-section-wrap lps-cached', $result );
				$result = str_replace( 'latest-post-selection-slider', 'latest-post-selection-slider lps-cached', $result );
			} else {
				set_transient( $trans_id, $result, 30 * DAY_IN_SECONDS );
			}
		}

		if ( $site_switched ) {
			restore_current_blog();
		}
		return $result;
	}

	/**
	 * Maybe append info row template.
	 *
	 * @param  string $html Initial card markup.
	 * @param  string $type Alignment type.
	 * @return string
	 */
	public static function maybe_info_row_template( $html, $type = 'first' ) {
		if ( empty( $html ) ) {
			// Fail-fast.
			return '';
		}

		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_use_internal_errors( false );

		$changed  = false;
		$xpath    = new DOMXPath( $dom );
		$elements = $xpath->query( '//div[contains(@class, "article__info")]' );
		foreach ( $elements as $el ) {
			// Note: $el->childNodes->length does not work properly with … .
			$count = 0;
			if ( $el->childNodes ) { // phpcs:ignore
				foreach ( $el->childNodes as $child ) { // phpcs:ignore
					if ( ! empty( $child->tagName ) ) { // phpcs:ignore
						++$count;
					}
				}
			}

			if ( $count > 1 ) {
				if ( 'first' === $type ) {
					$template = '--info-rows-template: 1fr' . str_repeat( ' auto', $count - 1 );
				} else {
					$repeat    = 2 === $count ? 2 : $count - 2;
					$template  = '--info-rows-align: space-between;';
					$template .= '--info-rows-template: ' . str_repeat( 'auto ', $repeat ) . '1fr';
				}

				$changed = true;
				$el->setAttribute( 'style', $template );
			}
		}

		if ( $changed ) {
			// Get the modified HTML.
			$html = $dom->saveHTML();
		}

		$html = str_replace( '<?xml encoding="UTF-8">', '', $html );
		return $html;
	}

	/**
	 * Alter the query where for attachment use.
	 *
	 * @param  string $where The where statement.
	 * @param  object $obj   The query object.
	 * @return string
	 */
	public static function attachment_custom_where( $where, $obj ) { // phpcs:ignore
		global $wpdb;
		if ( is_scalar( self::$current_query_statuses_list ) ) {
			$list = explode( ',', self::$current_query_statuses_list );
		} else {
			$list = self::$current_query_statuses_list;
		}
		$que = '';
		foreach ( $list as $k => $value ) {
			$que  .= ( ! empty( $que ) ) ? ' OR ' : '';
			$que  .= 'p2.post_status = \'' . $value . '\'';
			$where = str_replace( 'AND (p2.post_status = \'' . $value . '\')', '', $where );
			$where = str_replace( ' OR p2.post_status = \'' . $value . '\'', '', $where );
			$where = str_replace( 'p2.post_status = \'' . $value . '\'', '', $where );
		}
		$where = str_replace( ' AND ()', '', $where );
		$where = str_replace( 'AND (' . $que . ')', '', $where );

		return $where;
	}

	/**
	 * Alter the query join for attachment use.
	 *
	 * @param  string $join The join statement.
	 * @param  object $obj  The query object.
	 * @return string
	 */
	public static function attachment_custom_join( $join, $obj ) { // phpcs:ignore
		global $wpdb;
		$join = str_replace( 'LEFT JOIN ' . $wpdb->posts . ' AS p2 ON (' . $wpdb->posts . '.post_parent = p2.ID) ', '', $join );
		return $join;
	}

	/**
	 * Return empty for the attachment paragraph that embeds the image in the content.
	 *
	 * @param  string $p The paragraph.
	 * @return string
	 */
	public static function remove_attachment_content_p( $p ) { // phpcs:ignore
		return '';
	}

	/**
	 * Compute a post usable excerpt.
	 *
	 * @param  object $post The post object.
	 * @param  bool   $raw  Use or not raw content.
	 * @return string
	 */
	public static function maybe_post_excerpt( $post, $raw = false ) { // phpcs:ignore
		if ( $raw ) {
			$excerpt = wp_kses_post( strip_shortcodes( $post->post_excerpt ) );
		} else {
			$excerpt = apply_filters( 'the_excerpt', strip_shortcodes( $post->post_excerpt ) );
		}
		return $excerpt;
	}

	/**
	 * Compute a post usable content.
	 *
	 * @param  object $post The post object.
	 * @param  bool   $raw  Use or not raw content.
	 * @return string
	 */
	public static function maybe_post_content( $post, $raw = false ) { // phpcs:ignore
		if ( $raw ) {
			$content = wp_kses_post( $post->post_content );
		} else {
			$content = apply_filters( 'the_content', $post->post_content );
		}

		return $content;
	}

	/**
	 * Strip out possible quotation marks and quotation-like characters
	 *
	 * @param  string $text Initial text.
	 * @return string
	 */
	public static function strip_quotes( $text ) {
		// phpcs:disable
		$quotes = [
			// "'", '"', // Straight quotes.
			'‘', '’', '“', '”', // Curly quotes.
			'‹', '›', '«', '»', // Angle quotes.
			'′', '″', '‵', '‶', '˵', '˶', // Prime marks and modifier letters.
			'「', '」', '『', '』', // Japanese quotation marks.
			'《', '》', // Chinese double angle quotation marks.
			'„', // Low double quotes.
			'＂', '＇' // Fullwidth quotation marks.
		];
		// phpcs:enable

		$result = str_replace( $quotes, '', $text );
		return $result;
	}

	/**
	 * Compute a item text.
	 *
	 * @param  object $post     The post object.
	 * @param  array  $extra    The elements display list.
	 * @param  int    $limit    Chars limit.
	 * @param  bool   $raw      Use or not raw content.
	 * @param  string $trimmore Maybe some trailing extra chars for truncated string.
	 * @return string
	 */
	public static function compute_tile_text( $post, $extra = [], $limit = 120, $raw = false, $trimmore = '' ) { // phpcs:ignore
		if ( 'attachment' === $post->post_type ) {
			add_filter( 'prepend_attachment', [ get_called_class(), 'remove_attachment_content_p' ] );
		}

		if ( in_array( 'excerpt-small', $extra, true ) ) {
			$text = ! empty( $post->post_excerpt )
				? $post->post_excerpt
				: wp_strip_all_tags( get_the_excerpt( $post->ID ) );
			$text = str_replace( '[&hellip;]', '', $text );
			$text = self::strip_quotes( $text );
			return self::get_short_text( $text, $limit, true, $trimmore );
		} elseif ( in_array( 'excerpt', $extra, true ) ) {
			return self::maybe_post_excerpt( $post );
		} elseif ( in_array( 'content', $extra, true ) ) {
			return self::maybe_post_content( $post, $raw );
		} elseif ( in_array( 'content-small', $extra, true ) ) {
			if ( $raw ) {
				return wp_kses_post( self::trim_html_to_length( $post->post_content, $limit ) );
			} else {
				return self::get_short_text( $post->post_content, $limit, false, $trimmore );
			}
		} elseif ( in_array( 'excerptcontent', $extra, true ) ) {
			return '<div class="lps-excerpt">' . self::maybe_post_excerpt( $post, $raw ) . '</div><div class="lps-content">' . self::maybe_post_content( $post, $raw ) . '</div>';
		} elseif ( in_array( 'contentexcerpt', $extra, true ) ) {
			return '<div class="lps-content">' . self::maybe_post_content( $post, $raw ) . '</div><div class="lps-excerpt">' . self::maybe_post_excerpt( $post, $raw ) . '</div>';
		}
		return '';
	}

	/**
	 * Trim a HTML string to length, keeping the tags.
	 *
	 * @param  string $title   String to be trimmed.
	 * @param  int    $max_len Max chars.
	 * @param  string $end     The ending string.
	 * @return string
	 */
	public static function trim_html_to_length( $title, $max_len = 30, $end = '...' ) { // phpcs:ignore
		$current_len = 0;

		$title = strip_shortcodes( $title );
		$title = str_replace( '&nbsp;', ' ', $title );
		$title = preg_replace( '/\s\s+/', ' ', trim( $title ) );
		$title = preg_replace( '/<!--(.*?)-->/', '', $title );
		$title = html_entity_decode( $title );
		if ( strlen( $title ) <= $max_len ) {
			return $title;
		}

		$words_tags = preg_split( '/(<[^>]*[^\/]>)|(<[^>]*\/>)/i', $title, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
		$new_title  = '';
		$text       = '';
		$starts     = [];
		$ends       = [];
		if ( ! empty( $words_tags ) ) {
			foreach ( $words_tags as $elem ) {
				$current_len = strlen( $new_title );
				$remaining   = $max_len - $current_len;
				if ( $remaining <= 0 ) {
					// Stop here, do not iterate further.
					break;
				}

				if ( '</' === substr( $elem, 0, 2 ) ) {
					// Tag ending.
					array_unshift( $ends, $elem );
					$text  .= $elem;
					$maybes = rtrim( ltrim( $elem, '</' ), '>' );
					if ( ! empty( $starts ) ) {
						if ( substr( reset( $starts ), 0, strlen( $maybes ) + 1 ) === '<' . $maybes ) {
							// This is closed now, remove the most recent starting match.
							array_shift( $starts );
						}
					}
				} elseif ( '<' === substr( $elem, 0, 1 ) ) {
					// Tag start, append it at the beginning of stack.
					array_unshift( $starts, $elem );
					$text .= $elem;
				} else {
					// Text.
					if ( strlen( $elem ) > $remaining ) {
						$pos = stripos( $elem, ' ', $remaining );
						if ( ! empty( $pos ) ) {
							$elem = trim( substr( $elem, 0, $pos ) );
						} else {
							$w  = explode( ' ', $elem );
							$el = '';
							foreach ( $w as $wk ) {
								if ( strlen( $el ) >= $remaining ) {
									break;
								}
								$el .= ' ' . $wk;
							}
							$elem = trim( $el );
						}
					}
					$new_title .= $elem . ' ';
					$text      .= $elem . ' ';
				}
			}
		}

		// Remove the trailing punctuation if possible.
		$text = trim( $text );
		$text = preg_replace( '/\PL\z/', '', $text );
		$text = trim( $text );

		if ( ! empty( $end ) ) {
			$text .= $end;
		}
		if ( ! empty( $starts ) ) {
			$starts = implode( '', $starts );
			$text  .= str_replace( '<', '</', $starts );
		}

		return $text;
	}

	/**
	 * Get the post terms list.
	 *
	 * @param  int    $post_id The post ID.
	 * @param  string $tax     Taxonomy slug.
	 * @param  bool   $one     Get only one term.
	 * @param  bool   $uncat   Exclude the uncategorizes term.
	 * @param  bool   $label   Use the taxonomy name in front of the list.
	 * @param  bool   $nolink  Use the terms links.
	 * @param  string $css     Styles.
	 * @return string
	 */
	public static function get_post_visible_term( int $post_id = 0, string $tax = '', bool $one = false, bool $uncat = false, bool $label = true, bool $nolink = false, string $css = '' ): string {
		if ( empty( $post_id ) || empty( $tax ) ) {
			return '';
		}

		$count = 1;
		if ( ! empty( $css ) ) {
			switch ( $tax ) {
				case 'post_tag':
					if ( substr_count( $css, 'two-tags' ) ) {
						$count = 2;
					} elseif ( substr_count( $css, 'three-tags' ) ) {
						$count = 3;
					}
					break;

				case 'category':
					if ( substr_count( $css, 'two-categories' ) ) {
						$count = 2;
					} elseif ( substr_count( $css, 'three-categories' ) ) {
						$count = 3;
					}
					break;

				default:
					break;
			}
		}

		$tax_obj = get_taxonomy( $tax );
		if ( ! empty( $tax_obj ) && ! is_wp_error( $tax_obj ) ) {
			$terms_list = get_the_term_list( $post_id, $tax, '<span class="lps-terms ' . esc_attr( $tax ) . '">', ', ', '</span>' );

			if ( ! empty( $nolink ) && $terms_list ) {
				$terms_list = strip_tags( $terms_list, '<span>' );
			}

			$maybe_one = false;
			if ( ! empty( $uncat ) ) {
				$list = explode( ', ', $terms_list );
				foreach ( $list as $k => $term ) {
					if ( substr_count( $term, 'uncategorized' ) ) {
						unset( $list[ $k ] );
					}
				}
				if ( ! empty( $one ) ) {
					if ( ! empty( $list ) ) {
						$terms_list = implode( ', ', array_slice( $list, 0, $count ) );
					}
					$maybe_one = true;
				} else {
					$terms_list = implode( ', ', $list );
				}
			}

			if ( $one && ! $maybe_one && ! empty( $terms_list ) ) {
				$list       = explode( ', ', $terms_list );
				$terms_list = implode( ', ', array_slice( $list, 0, $count ) );
			}

			if ( ! empty( $terms_list ) ) {
				$before = empty( $label ) ? '<span class="lps-taxonomy ' . esc_attr( $tax ) . '">' . esc_html( $tax_obj->label ) . ':</span> ' : '';

				return '<div class="lps-taxonomy-wrap ' . esc_attr( $tax ) . ( $one ? ' one-term' : '' ) . ( ! $before ? ' no-label' : '' ) . '">' . $before . $terms_list . '</div>';
			}
		}

		return '';
	}

	/**
	 * Compute the position for the extra elements.
	 *
	 * @param  string $show_extra    The extra element list.
	 * @param  string $tile_pattern  The tile pattern.
	 * @param  array  $args          The shortcode arguments.
	 * @param  array  $extra_display The extra elements to be shown.
	 * @return string
	 */
	public static function positions_from_extra( $show_extra = '', $tile_pattern = '', $args = [], $extra_display = [] ) { // phpcs:ignore
		if ( in_array( 'date', $extra_display, true ) ) {
			if ( in_array( 'title', $extra_display, true ) ) {
				if ( ! empty( $args['display'] ) && substr_count( $args['display'], 'date,title' ) ) {
					$tile_pattern = str_replace( '[title]', '[date][title]', $tile_pattern );
				} else {
					$tile_pattern = str_replace( '[title]', '[title][date]', $tile_pattern );
				}
			} else {
				$tile_pattern = str_replace( '[title]', '[date]', $tile_pattern );
			}
		}

		if ( ! is_array( $show_extra ) ) {
			$show_extra = explode( ',', $show_extra );
		}
		if ( ! empty( $show_extra ) ) {
			foreach ( $show_extra as $extra_tag ) {
				if ( substr_count( $extra_tag, 'taxpos_' ) ) {
					preg_match_all( '/taxpos\_(.*)\_(before|after)\-(.*)/', $extra_tag, $matches );
					if ( ! empty( $matches[1][0] ) && in_array( $matches[1][0], $show_extra, true )
						&& ! empty( $matches[2][0] ) ) {
						if ( 'before' === $matches[2][0] ) {
							$tile_pattern = str_replace( '[' . $matches[3][0] . ']', '[' . $matches[1][0] . '][' . $matches[3][0] . ']', $tile_pattern );
						} else {
							$tile_pattern = str_replace( '[' . $matches[3][0] . ']', '[' . $matches[3][0] . '][' . $matches[1][0] . ']', $tile_pattern );
						}
					}
				}
			}
		}

		// Set the default positions for.
		foreach ( self::$replaceable_tags as $tag ) {
			if ( ! substr_count( $tile_pattern, '[' . $tag . ']' ) ) {
				if ( in_array( $tag, $show_extra, true ) ) {
					$tile_pattern = str_replace( '[text]', '[text][' . $tag . ']', $tile_pattern );
				}
			}
		}

		return $tile_pattern;
	}

	/**
	 * Clean the tile title.
	 *
	 * @param  string $str The string.
	 * @return string
	 */
	public static function cleanup_title( $str ) { // phpcs:ignore
		return ( ! empty( $str ) ) ? str_replace( ']', '', str_replace( '[', '', $str ) ) : '';
	}

	/**
	 * Select a random placeholder.
	 *
	 * @param  string $string The list of placeholders separated by comma.
	 * @return string
	 */
	public static function select_random_placeholder( $string = '' ) { // phpcs:ignore
		if ( empty( $string ) ) {
			return '';
		}

		if ( 'auto' === $string ) {
			$uri    = LPS_PLUGIN_URL . 'assets/images/samples/';
			$string = $uri . '1.svg,' . $uri . '2.svg,' . $uri . '3.svg,' . $uri . '4.svg,' . $uri . '5.svg';
		}

		global $select_random_placeholder;
		$list   = ( ! is_array( $string ) ) ? explode( ',', $string ) : $string;
		$usable = $list;
		if ( empty( $select_random_placeholder ) ) {
			$select_random_placeholder = [];
		} else {
			$diff = array_diff( $list, $select_random_placeholder );
			if ( ! empty( $diff ) ) {
				$list = array_values( $diff );
			} else {
				$list = $usable;

				$select_random_placeholder = [];
			}
		}
		$index = array_rand( $list, 1 );
		$item  = ( ! empty( $list[ $index ] ) ) ? $list[ $index ] : $usable[0];

		$select_random_placeholder[] = $item;
		return $item;
	}

	/**
	 * Compute the tile image for a post, based on the arguments.
	 *
	 * @param  object $post The WP_Post object.
	 * @param  array  $args The shortcode arguments.
	 * @param  string $tile The tile pattern.
	 * @return string
	 */
	public static function set_tile_image( $post, $args, $tile ) { // phpcs:ignore
		global $last_tiles_img;
		if ( empty( $post ) ) {
			return;
		}
		$last_tiles_img = '';

		// Tile image markup.
		if ( ! empty( $args['image'] ) ) {
			$img_html = '';
			$attr     = [
				'class'   => 'lps-custom-' . $args['image'],
				'loading' => 'lazy',
			];

			if ( 'attachment' === $post->post_type ) {
				$th_id       = $post->ID;
				$attr['alt'] = get_post_meta( $th_id, '_wp_attachment_image_alt', true );
				if ( empty( $attr['alt'] ) ) {
					$attr['alt'] = self::cleanup_title( $post->post_title );
				}
			} else {
				$th_id       = get_post_thumbnail_id( (int) $post->ID );
				$attr['alt'] = self::cleanup_title( $post->post_title );
			}

			$image     = wp_get_attachment_image_src( $th_id, $args['image'] );
			$img_url   = '';
			$is_native = false;
			if ( ! empty( $image[0] ) ) {
				$img_url        = $image[0];
				$is_native      = true;
				$attr['width']  = $image[1];
				$attr['height'] = $image[2];
			} elseif ( ! empty( $args['image_placeholder'] ) ) {
				$img_url = self::select_random_placeholder( $args['image_placeholder'] );
			}

			if ( ! empty( $img_url ) ) {
				if ( true === $is_native ) {
					$srcset = wp_get_attachment_image_srcset( $th_id, $args['image'] );
					if ( ! empty( $srcset ) ) {
						$attr['srcset'] = $srcset;
					}
				}

				$attributes = '';
				foreach ( $attr as $k => $v ) {
					if ( 'class' === $k ) {
						$v = 'lps-tile-main-image ' . $v;
					}
					$attributes .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
				}

				if ( $args['ver'] < 2 ) {
					// Legacy markup.
					$img_html = '<img src="' . esc_url( $img_url ) . '"' . $attributes . '>';
				} else {
					// Ver >= 2 markup.
					$img_html = '<figure class="article__image"><img src="' . esc_url( $img_url ) . '"' . $attributes . '></figure>';
				}

				$last_tiles_img = $img_url;
			}
			$tile = str_replace( '[image]', $img_html, $tile );
		}
		$tile = str_replace( '[image]', '', $tile );
		return $tile;
	}

	/**
	 * Generate the slider output from the selected posts and shortcode settings.
	 *
	 * @param array $posts     List of WP_Post objects.
	 * @param array $args      Shortcode settings.
	 * @param bool  $use_cache Use cache for the slider shortcode.
	 */
	public static function latest_selected_content_slider( $posts, $args, $use_cache = false ) { // phpcs:ignore
		if ( empty( $posts ) ) {
			return;
		}

		include __DIR__ . '/incs/content-slider.php';
	}

	/**
	 * Custom minify content.
	 *
	 * @param  string $content String to be minified.
	 * @param  bool   $is_css  String to CSS or not.
	 * @return string
	 */
	public static function custom_minify( $content, $is_css = false ) { // phpcs:ignore
		// Minify the output.
		$content = trim( $content );

		// Remove space after colons.
		$content = str_replace( ': ', ':', $content );
		$content = str_replace( ': ', ':', $content );

		// Remove whitespace.
		$content = str_replace( [ "\r\n", "\r", "\n", "\t" ], '', $content );

		// Remove spaces that might still be left where we know they aren't needed.
		$content = preg_replace( '/\s*([\{\}>~:;,])\s*/', '$1', $content );

		if ( true === $is_css ) {
			// Remove last semi-colon in a block.
			$content = preg_replace( '/;\}/', '}', $content );
		} else {
			$content = str_replace( '","', '", "', $content );
			$content = str_replace( '":"', '": "', $content );
		}

		return $content;
	}

	/**
	 * Maybe rebuild the front end assets.
	 *
	 * @param bool $rebuild True to rebuild.
	 */
	public static function maybe_rebuild_assets( $rebuild ) { // phpcs:ignore
		if ( true === $rebuild ) {
			update_option( self::ASSETS_VERSION, gmdate( 'Ymd.Hi' ) );
		}

		$original = __DIR__ . '/assets/sources/modal.js';
		$script1  = __DIR__ . '/assets/modal.js';
		if ( ( true === $rebuild && file_exists( $original ) ) || ! file_exists( $script1 ) ) {
			$content = @file_get_contents( $original ); // phpcs:ignore
			$content = self::custom_minify( $content );
			@file_put_contents( $script1, $content ); // phpcs:ignore
		}
	}

	/**
	 * Plugin action link.
	 *
	 * @param  array $links Plugin links.
	 * @return array
	 */
	public static function plugin_action_links( $links ) { // phpcs:ignore
		$all   = [];
		$all[] = '<a href="https://iuliacazan.ro/latest-post-shortcode">' . esc_html__( 'Plugin URL', 'lps' ) . '</a>';
		$all   = array_merge( $all, $links );
		return $all;
	}

	/**
	 * The actions to be executed when the plugin is updated.
	 */
	public static function plugin_ver_check() {
		$opt = str_replace( '-', '_', self::PLUGIN_TRANSIENT ) . '_db_ver';
		$dbv = get_option( $opt, 0 );
		if ( LPS_PLUGIN_VERSION !== (float) $dbv ) {
			update_option( $opt, LPS_PLUGIN_VERSION );
			self::activate_plugin();
		}
	}

	/**
	 * The actions to be executed when the plugin is activated.
	 */
	public static function activate_plugin() {
		set_transient( self::PLUGIN_TRANSIENT, true );
		self::maybe_rebuild_assets( true );
	}

	/**
	 * The actions to be executed when the plugin is deactivated.
	 */
	public static function deactivate_plugin() {
		self::plugin_admin_notices_cleanup( false );
	}

	/**
	 * Execute notices cleanup.
	 *
	 * @param bool $ajax Is AJAX call.
	 */
	public static function plugin_admin_notices_cleanup( $ajax = true ) { // phpcs:ignore
		// Delete transient, only display this notice once.
		delete_transient( self::PLUGIN_TRANSIENT );

		if ( true === $ajax ) {
			// No need to continue.
			wp_die();
		}
	}

	/**
	 * Donate text.
	 *
	 * @return string
	 */
	public static function donate_text() {
		$ptitle = __( 'Latest Post Shortcode', 'lps' );
		$donate = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JJA37EHZXWUTJ&item_name=Support for development and maintenance (' . rawurlencode( $ptitle ) . ')';
		$thanks = __( 'A huge thanks in advance!', 'lps' );

		return sprintf(
				// Translators: %1$s - donate URL, %2$s - rating, %3$s - thanks.
			__( 'If you find the plugin useful and would like to support my work, please consider making a <a href="%1$s" target="_blank">donation</a>. It would make me very happy if you would leave a %2$s rating. %3$s', 'lps' ),
			$donate,
			'<a href="' . self::PLUGIN_SUPPORT_URL . 'reviews/?rate=5#new-post" class="rating" target="_blank" rel="noreferrer" title="' . esc_attr( $thanks ) . '">★★★★★</a>',
			$thanks
		);
	}

	/**
	 * Admin notices.
	 */
	public static function plugin_admin_notices() {
		if ( apply_filters( 'lps/remove_update_info', false ) ) {
			return;
		}

		if ( apply_filters_deprecated( 'lps_filter_remove_update_info', [ false ], '11.0.0', 'lps/remove_update_info' ) ) {
			return;
		}

		$maybe_trans = get_transient( self::PLUGIN_TRANSIENT );
		if ( ! empty( $maybe_trans ) ) {
			$slug   = md5( LPS_PLUGIN_SLUG );
			$ptitle = __( 'Latest Post Shortcode', 'lps' );
			$donate = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JJA37EHZXWUTJ&item_name=Support for development and maintenance (' . rawurlencode( $ptitle ) . ')';

			// Translators: %1$s - plugin name.
			$activated = sprintf( __( '%1$s plugin was activated!', 'lps' ), '<b>' . $ptitle . '</b>' );

			$other_notice = sprintf(
				// Translators: %1$s - plugins URL, %2$s - heart, %3$s - extensions URL, %4$s - star, %5$s - pro.
				__( '%5$sCheck out my other <a href="%1$s" target="_blank" rel="noreferrer">%2$s free plugins</a> on WordPress.org and the <a href="%3$s" target="_blank" rel="noreferrer">%4$s other extensions</a> available!', 'lps' ),
				'https://profiles.wordpress.org/iulia-cazan/#content-plugins',
				'<span class="dashicons dashicons-heart"></span>',
				'https://iuliacazan.ro/shop/',
				'<span class="dashicons dashicons-star-filled"></span>',
				''
			);
			?>
			<div id="item-<?php echo esc_attr( $slug ); ?>" class="notice is-dismissible">
				<div class="content">
					<a class="icon" href="<?php echo esc_url( admin_url( 'options-reading.php#lps-settings' ) ); ?>"><img src="<?php echo esc_url( LPS_PLUGIN_URL . 'assets/images/icon-128x128.gif' ); ?>"></a>
					<div class="details">
						<div>
							<h3><?php echo wp_kses_post( $activated ); ?></h3>
							<div class="notice-other-items"><?php echo wp_kses_post( $other_notice ); ?></div>
						</div>
						<div><?php echo wp_kses_post( self::donate_text() ); ?></div>
						<a class="notice-plugin-donate" href="<?php echo esc_url( $donate ); ?>" target="_blank"><img src="<?php echo esc_url( LPS_PLUGIN_URL . 'assets/images/buy-me-a-coffee.png?v=' . LPS_PLUGIN_VERSION ); ?>" width="200"></a>
					</div>
				</div>
				<button type="button" class="notice-dismiss" onclick="dismiss_notice_for_<?php echo esc_attr( $slug ); ?>()"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'lps' ); ?></span></button>
			</div>
			<?php
			$style = '#trans123super{--color-bg:rgba(63,77,183,.1); --color-border:rgb(63,77,183); border-left-color:var(--color-border);padding:0 38px 0 0!important}#trans123super *{margin:0}#trans123super .dashicons{color:var(--color-border)}#trans123super a{text-decoration:none}#trans123super img{display:flex;}#trans123super .content,#trans123super .details{display:flex;gap:1rem;padding-block:.5em}#trans123super .details{align-items:center;flex-wrap:wrap;padding-block:0}#trans123super .details>*{flex:1 1 35rem}#trans123super .details .notice-plugin-donate{flex:1 1 auto}#trans123super .details .notice-plugin-donate img{max-width:100%}#trans123super .icon{background:var(--color-bg);flex:0 0 4rem;margin:-.5em 0;padding:1rem}#trans123super .icon img{display:flex;height:auto;width:4rem} #trans123super h3{margin-bottom:0.5rem;text-transform:none}';
			$style = str_replace( '#trans123super', '#item-' . esc_attr( $slug ), $style );
			echo '<style>' . $style . '</style>'; // phpcs:ignore
			?>
			<script>function dismiss_notice_for_<?php echo esc_attr( $slug ); ?>() { document.getElementById( 'item-<?php echo esc_attr( $slug ); ?>' ).style='display:none'; fetch( '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>?action=plugin-deactivate-notice-<?php echo esc_attr( LPS_PLUGIN_SLUG ); ?>' ); }</script>
			<?php
		}
	}

	/**
	 * Maybe donate or rate.
	 */
	public static function show_donate_text() {
		if ( apply_filters( 'lps/remove_donate_info', false ) ) {
			return;
		}
		?>
		<hr>
		<table class="inline-donate-notice">
			<tbody><tr>
				<td valign="middle">
					<img src="<?php echo esc_url( LPS_PLUGIN_URL . 'assets/images/icon-128x128.png' ); ?>" width="38" height="38">
					<?php echo wp_kses_post( self::donate_text() ); ?>
					<br><em>Iulia</em>
				</td>
			</tr></tbody>
		</table>
		<hr class="sep">
		<?php
	}

	/**
	 * Dequeue the scripts and styles, and additionally for the front-end pages
	 * that do not use the LPS functionality.
	 */
	public static function lps_filter_plugin_assets() {
		$always_load = get_option( 'lps-assets-all', '' );
		$always_load = apply_filters( 'lps/load_assets_on_page', $always_load );
		if ( 'yes' === $always_load || \is_admin() || self::$is_elementor_editor ) {
			// Fail-fast.
			return;
		}

		if ( ! self::lps_current_page_contains( 'latest-selected-content' )
			&& ! self::lps_current_page_contains( 'latest-post-selection' )
			&& ! self::lps_current_page_contains( 'wp:latest-post-shortcode' ) ) {
			// Dequeue the styles.
			\wp_dequeue_style( 'lps-style-legacy' );
			\wp_dequeue_style( 'lps-style' );

			// Dequeue the scripts.
			\wp_dequeue_script( 'lps-slick' );
		}
	}

	/**
	 * Assess the current rendering page content.
	 */
	public static function lps_assess_page_content() {
		global $lps_assess_cpa, $_wp_current_template_content;
		if ( empty( $lps_assess_cpa ) ) {
			$the_object      = \get_queried_object();
			$lps_assess_cpa  = $the_object->post_content ?? '';
			$lps_assess_cpa .= $the_object->description ?? '';
			$lps_assess_cpa .= $_wp_current_template_content ?? '';
			$lps_assess_cpa .= \wp_json_encode( \get_option( 'widget_text' ) );
			$lps_assess_cpa .= \wp_json_encode( \get_option( 'widget_custom_html' ) );
		}
	}

	/**
	 * Assess if the current rendering page contains a specific string.
	 *
	 * @param  string $something What to check.
	 * @return bool
	 */
	public static function lps_current_page_contains( string $something = '' ): bool {
		global $lps_assess_cpa;
		if ( empty( $something ) ) {
			return false;
		}
		if ( empty( $lps_assess_cpa ) ) {
			self::lps_assess_page_content();
		}

		$text = $lps_assess_cpa;
		if ( empty( $text ) ) {
			return false;
		}
		if ( substr_count( $text, $something ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Register a custom setting for the plugin.
	 */
	public static function lps_assets_options() {
		// Add an option to Settings -> Reading.
		register_setting( 'reading', 'lps-assets-all', [ 'sanitize_callback' => 'sanitize_text_field' ] );
		register_setting( 'reading', 'lps-classic-exclude-role', [ 'sanitize_callback' => 'sanitize_text_field' ] );
		register_setting( 'reading', 'lps-legacy', [ 'sanitize_callback' => 'sanitize_text_field' ] );

		$allowed_options = [
			'reading' => [
				'lps-assets-all',
				'lps-classic-exclude-role',
				'lps-legacy',
			],
		];
		if ( function_exists( 'add_allowed_options' ) ) {
			add_allowed_options( $allowed_options );
		} else {
			// Fallback to old function.
			add_option_whitelist( $allowed_options ); // phpcs:ignore
		}

		add_settings_field(
			'lps-assets-all',
			__( 'Latest Post Shortcode', 'lps' ),
			[ get_called_class(), 'lps_assets_all' ],
			'reading'
		);
	}

	/**
	 * Custom setting output callback handler.
	 */
	public static function lps_assets_all() {
		$value   = get_option( 'lps-assets-all', '' );
		$legacy  = get_option( 'lps-legacy', '' );
		$exclude = get_option( 'lps-classic-exclude-role', '' );
		?>
		<ul id="lps-settings" class="lps-assets-all-options">
			<li>
				<input type="radio" name="lps-assets-all" id="lps_assets_all_no" value="" <?php checked( empty( $value ), true ); ?> />
				<label for="lps_assets_all_no"><?php \esc_html_e( 'let WordPress decide when to load the LPS assets', 'lps' ); ?></label>
				<em><?php \esc_html_e( '(recommended)', 'lps' ); ?></em>
			</li>
			<li>
				<input type="radio" name="lps-assets-all" id="lps_assets_all_yes" value="yes" <?php checked( 'yes' === $value, true ); ?> />
				<label for="lps_assets_all_yes"><?php \esc_html_e( 'always load the LPS assets', 'lps' ); ?></label>
				<em><?php \esc_html_e( '(sometimes needed for compatibility with other editors)', 'lps' ); ?></em>
			</li>
			<li>
				<input type="checkbox" name="lps-legacy" id="lps-legacy" value="yes" <?php checked( 'yes' === $legacy, true ); ?> />
				<label for="lps-legacy"><?php \esc_html_e( 'load the legacy styles', 'lps' ); ?></label>
				<em><?php \esc_html_e( '(version 1 is deprecated, used it only for backward compatibility)', 'lps' ); ?></em>
			</li>
			<li>
				<input type="text" name="lps-classic-exclude-role" id="lps-exclude-role" value="<?php echo esc_html( $exclude ); ?>" />
				<label for="lps-exclude-role">
					<?php
					echo wp_kses_post( sprintf(
						// Translators: %s - plugin icon.
						__( 'hide the %s LPS button in the Classic editor for the specified roles (if multiple, separate by comma)', 'lps' ),
						'<img src="' . LPS_PLUGIN_URL . 'assets/images/icon-purple.svg" style="vertical-align: middle">'
					) );
					?>
				</label>
			</li>
		</ul>
		<?php
	}

	/**
	 * Fix the pagination in the single pages.
	 *
	 * @param  WP_Query $request Current request object.
	 * @return WP_Query
	 */
	public static function fix_request_redirect( $request ) {
		if ( ( true === $request->is_singular || true === $request->is_single )
			&& - 1 === $request->current_post && true === $request->is_paged ) {
			add_filter( 'redirect_canonical', '__return_false' );
		}
		return $request;
	}
}

// Instantiate the class.
$lps_instance = Latest_Post_Shortcode::get_instance();

// Register activation and deactivation actions.
register_activation_hook( __FILE__, [ $lps_instance, 'activate_plugin' ] );
register_deactivation_hook( __FILE__, [ $lps_instance, 'deactivate_plugin' ] );

// Allow the text widget to render the Latest Post Shortcode.
add_filter( 'widget_text', 'do_shortcode', 11 );

if ( class_exists( 'Elementor\\Plugin' ) ) { // Add Elementor support.
	require_once __DIR__ . '/incs/elementor/class-elementor-lps-extension.php';
}

if ( function_exists( 'register_block_type' ) ) { // Gutenberg is active.
	require_once __DIR__ . '/lps-block/block.php';
}

if ( file_exists( __DIR__ . '/tests/lps-tests.php' ) ) { // Maybe shortcode tests.
	include_once __DIR__ . '/tests/lps-tests.php';
}
