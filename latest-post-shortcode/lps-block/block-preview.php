<?php // phpcs:ignore
/**
 * Latest Post Shortcode Block.
 * Text Domain: lps
 *
 * @package lps
 */
namespace LPS\Block;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

\add_action( 'rest_api_init', __NAMESPACE__ . '\\lps_api_routes' );

/**
 * Register REST API route.
 */
function lps_api_routes() {
	\register_rest_route( 'lps/v1', '/assess-exclusion', [
		'methods'             => 'POST',
		'callback'            => __NAMESPACE__ . '\\assess_exclusion',
		'permission_callback' => '__return_true',
	] );
}

/**
 * Assess exclusion.
 *
 * @param  \WP_REST_Request $request REST API request.
 * @return \WP_REST_Response
 */
function assess_exclusion( \WP_REST_Request $request ) {
	$post_id  = $request->get_param( 'post_id' );
	$position = $request->get_param( 'position' );
	$content  = $request->get_param( 'content' );
	if ( ! $post_id || ! $position || ! \current_user_can( 'edit_post', $post_id ) ) {
		return \rest_ensure_response( [
			'success' => true,
			'data'    => [],
		] );
	}

	return \rest_ensure_response( [
		'success' => true,
		'data'    => assess_exclusion_by_content( $post_id, $content, $position ),
	] );
}

/**
 * Assess exclusion by content.
 *
 * @param  int    $post_id  Post ID.
 * @param  string $content  Post content.
 * @param  int    $position Block position in the post content.
 * @return array
 */
function assess_exclusion_by_content( $post_id, $content, $position ) {
	$hash = md5( print_r( $content, true ) ); // phpcs:ignore
	$test = map_client_id_to_exposed_ids( $post_id, $content, $position );
	$hash = $test['hash'] ?? '';
	$prev = \get_post_meta( $post_id, '_lps-block-hash', true );
	$map  = \get_post_meta( $post_id, '_lps-block-ids', true );
	if ( $hash !== $prev || empty( $map ) ) {
		$map = $test['list'];
		\update_post_meta( $post_id, '_lps-block-ids', $map );
		\update_post_meta( $post_id, '_lps-block-hash', $hash );
	}

	$exp = [];
	$nmb = 0;
	$cll = '';
	foreach ( $map as $cid => $ids ) {
		$cll = $cid;
		if ( $nmb === (int) $position ) {
			break;
		}
		$exp = $ids;
		++$nmb;
	}

	return [
		'clientId' => $cll,
		'position' => $position,
		'exclude'  => $exp,
	];
}

/**
 * Processes the inner blocks and collect the postId attributes.
 *
 * @param  object $block A block object.
 * @return array
 **/
function get_block_client_ids( $block ) {
	$result = [];

	if ( ! empty( $block['clientId'] ) && ! empty( $block['attributes']['lpsContent'] ) ) {
		// Collect the current block's attribute.
		$result[ $block['clientId'] ] = $block['attributes']['lpsContent'];
	}

	// Check for inner blocks and recurse if necessary.
	if ( ! empty( $block['innerBlocks'] ) ) {
		foreach ( $block['innerBlocks'] as $inner_block ) {
			$inner  = get_block_client_ids( $inner_block );
			$result = array_merge( $result, $inner );
		}
	}

	return $result;
}

/**
 * Map client id to exposed IDs.
 *
 * @param  int   $post_id  Post ID.
 * @param  mixed $blocks   Blocks list.
 * @param  int   $position Block position in the post content.
 * @return array
 */
function map_client_id_to_exposed_ids( $post_id, $blocks, $position = 0 ) {
	$client_ids = [];
	if ( ! empty( $blocks ) ) {
		foreach ( $blocks as $block ) {
			$client_ids = array_merge( $client_ids, get_block_client_ids( $block ) );
		}
	}

	$hash = md5( 'lps-' . print_r( $client_ids, true ) ); // phpcs:ignore
	$list = [];
	if ( ! empty( $client_ids ) ) {
		global $lps_current_post_embedded_item_ids;
		foreach ( $client_ids as $cid => $lps ) {
			\do_shortcode( $lps );
			$list[ $cid ] = $lps_current_post_embedded_item_ids ?? [];
		}
	}

	return [
		'list' => $list,
		'hash' => $hash,
	];
}

/**
 * Get excluded IDs for block with specified position.
 *
 * @param  int $post_id  Post ID.
 * @param  int $position Block position inside the post content.
 * @return array
 */
function get_position_excludes( $post_id, $position ) {
	$map = \get_post_meta( $post_id, '_lps-block-ids', true );
	$exp = [];
	$nmb = 0;
	if ( ! empty( $map ) ) {
		foreach ( $map as $cid => $ids ) {
			if ( $nmb === (int) $position ) {
				break;
			}
			$exp = $ids;
			++$nmb;
		}
	}

	return $exp;
}

/**
 * Maybe handle the editor preview.
 *
 * @param  string $instance_id Unique instance id.
 * @param  array  $attributes  Block attributes.
 * @return string
 */
function maybe_preview( $instance_id, $attributes ) {
	$post_id   = (int) $attributes['postId'] ?? 0;
	$client_id = $attributes['clientId'] ?? '';
	$block_ord = (int) $attributes['nthOfType'] ?? 0;
	$rendered  = get_position_excludes( $post_id, $block_ord );

	global $lps_current_post_embedded_item_ids, $lps_instance, $lps_current_queried_object_id;
	$lps_current_post_embedded_item_ids = $rendered;
	$lps_current_queried_object_id      = $post_id;
	$lps_instance::$editor_type         = 'gutenberg';

	// Compute here the content.
	$content = str_replace(
		'[latest-selected-content ',
		'[latest-selected-content lps_instance_id="' . $instance_id . '" ',
		$attributes['lpsContent']
	);

	$content = \do_shortcode( $attributes['lpsContent'] );
	if ( $lps_instance ) {
		$lps_instance::execute_lps_cache_reset();
	}

	if ( empty( $content )
		|| ( ! substr_count( $content, '<section ' ) && ! substr_count( $content, 'lps-slider' ) ) ) {
		$content = '<div class="lps-placeholder">' . \wp_kses_post( \__( 'The shortcode found no results. If you provided a fallback message that will be shown on the front-end.', 'lps' ) ) . '</div>';
	}

	return '<div class="lps-block-preview">' . $content . '</div>';
}
