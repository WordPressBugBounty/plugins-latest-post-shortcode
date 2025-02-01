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
	<h3>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter By %s', 'lps' ), __( 'IDs', 'lps' ) ) );
		?>
	</h3>

	<h4><?php esc_html_e( 'Post', 'lps' ); ?></h4>
	<p >
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter the result by %s.', 'lps' ), __( 'IDs', 'lps' ) ) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'IDs', 'lps' ) ) );
		?>
	</p>
	<div class="row">
		<label for="lps_post_id"><?php esc_attr_e( 'list of IDs', 'lps' ); ?></label>
		<input type="text" name="lps_post_id" id="lps_post_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Ex: 1,2,3', 'lps' ); ?>">
	</div>

	<h4><label for="lps_dparent"><?php esc_html_e( 'Parent', 'lps' ); ?></label></h4>
	<p>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter the result by %s.', 'lps' ), __( 'parents', 'lps' ) ) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'IDs', 'lps' ) ) );
		?>
	</p>
	<select name="lps_dparent" id="lps_dparent" data-default="" onchange="lpsRefresh()" class="lps-update-blink">
		<option value="">
			<?php esc_html_e( 'static', 'lps' ); ?>,
			<?php esc_html_e( 'use the selection', 'lps' ); ?>
		</option>
		<option value="yes">
			<?php esc_html_e( 'dynamic', 'lps' ); ?>,
			<?php esc_html_e( 'use the current post context', 'lps' ); ?>
		</option>
	</select>
	<div class="row lps-update-blink" data-cond="#lps_dparent" data-comp="">
		<label for="lps_parent_id"><?php esc_html_e( 'list of IDs', 'lps' ); ?></label>
		<input type="text" name="lps_parent_id" id="lps_parent_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Ex: 1,2,3', 'lps' ); ?>">
	</div>

	<h4><label for="lps_dauthor"><?php esc_html_e( 'Author', 'lps' ); ?></label></h4>
	<p>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Filter the result by %s.', 'lps' ), __( 'authors', 'lps' ) ) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'IDs', 'lps' ) ) );
		?>
	</p>
	<select name="lps_dauthor" id="lps_dauthor" data-default="" onchange="lpsRefresh()" class="lps-update-blink">
		<option value="">
			<?php esc_html_e( 'static', 'lps' ); ?>,
			<?php esc_html_e( 'use the selection', 'lps' ); ?>
		</option>
		<option value="yes">
			<?php esc_html_e( 'dynamic', 'lps' ); ?>,
			<?php esc_html_e( 'use the current post context', 'lps' ); ?>
		</option>
	</select>
	<div class="row lps-update-blink" data-cond="#lps_dauthor" data-comp="">
		<label for="lps_author_id"><?php esc_html_e( 'list of IDs', 'lps' ); ?></label>
		<input type="text" name="lps_author_id" id="lps_author_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Ex: 1,2,3', 'lps' ); ?>">
	</div>
</div>

<div class="settings-block">
	<h3><?php esc_html_e( 'Exclude Content', 'lps' ); ?></h3>

	<h4><?php esc_html_e( 'Current', 'lps' ); ?></h4>
	<label>
		<input type="checkbox" name="lps_show_extra_current_id" id="lps_show_extra_current_id" value="current_id" checked="checked" disabled="disabled" readonly="readonly">
		<?php esc_html_e( 'the current post', 'lps' ); ?>
	</label>

	<h4><?php esc_html_e( 'Dynamic', 'lps' ); ?></h4>
	<p><?php esc_html_e( 'Filter the results so that the posts already embedded by previous shortcodes on this page will not repeat.', 'lps' ); ?></p>
	<label>
		<input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_exclude_previous_content" value="exclude_previous_content" onclick="lpsRefresh()" class="lps_show_extra">
		<?php esc_html_e( 'previous shortcodes', 'lps' ); ?>
	</label>

	<h4><label for="lps_excludepost_id"><?php esc_html_e( 'Post', 'lps' ); ?></label></h4>
	<p>
		<?php
		echo esc_html( sprintf( // Translators: %s - type of filtering.
			__( 'Exclude posts from the results by %s.', 'lps' ),
			__( 'IDs', 'lps' )
		) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'IDs', 'lps' ) ) );
		?>
	</p>
	<input type="text" name="lps_excludepost_id" id="lps_excludepost_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Ex: 1,2,3', 'lps' ); ?>">

	<h4><label for="lps_excludeauthor_id"><?php esc_html_e( 'Author', 'lps' ); ?></label></h4>
	<p>
		<?php
		echo esc_html( sprintf( // Translators: %s - type of filtering.
			__( 'Exclude posts from the results by %s.', 'lps' ),
			__( 'authors', 'lps' )
		) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'IDs', 'lps' ) ) );
		?>
	</p>
	<input type="text" name="lps_excludeauthor_id" id="lps_excludeauthor_id" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Ex: 1,2,3', 'lps' ); ?>">

	<h4><label for="lps_exclude_tags"><?php esc_html_e( 'Tag', 'lps' ); ?></label></h4>
	<p>
		<?php
		echo esc_html( sprintf( // Translators: %s - type of filtering.
			__( 'Exclude posts from the results by %s.', 'lps' ),
			__( 'tags', 'lps' )
		) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'slugs', 'lps' ) ) );
		?>
	</p>
	<input type="text" name="lps_exclude_tags" id="lps_exclude_tags" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Ex: white,grey', 'lps' ); ?>">

	<h4><label for="lps_exclude_categories"><?php esc_html_e( 'Category', 'lps' ); ?></label></h4>
	<p>
		<?php
		echo esc_html( sprintf( // Translators: %s - type of filtering.
			__( 'Exclude posts from the results by %s.', 'lps' ),
			__( 'categories', 'lps' )
		) );
		?>
		<?php
		// Translators: %s - type of filtering.
		echo esc_html( sprintf( __( 'Use a comma separated list of %s.', 'lps' ), __( 'slugs', 'lps' ) ) );
		?>
	</p>
	<input type="text" name="lps_exclude_categories" id="lps_exclude_categories" onchange="lpsRefresh()" onkeyup="lpsRefresh()" placeholder="<?php esc_attr_e( 'Ex: white,grey', 'lps' ); ?>">
</div>
