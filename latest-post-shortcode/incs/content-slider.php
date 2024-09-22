<?php //phpcs:ignore Generic.Files.LineEndings.InvalidEOLChar
/**
 * Latest Post Shortcode slider output.
 * Text Domain: lps
 *
 * @package lps
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$cards      = '';
$imgsize    = ( empty( $args['image'] ) ) ? 'none' : $args['image'];
$url        = ( ! empty( $args['url'] ) && substr_count( $args['url'], 'yes' ) ) ? 'true' : 'false';
$chrlimit   = ( ! empty( $args['chrlimit'] ) ) ? intval( $args['chrlimit'] ) : 120;
$trimmore   = ( ! empty( $args['more'] ) ) ? $args['more'] : '';
$show_extra = ( ! empty( $args['show_extra'] ) ) ? explode( ',', $args['show_extra'] ) : [];
$use_trim   = in_array( 'trim', $show_extra, true ) ? true : false;
$extra      = ( ! empty( $args['display'] ) ) ? explode( ',', $args['display'] ) : [ 'title' ];
$overlay    = ( ! empty( $args['slideoverlay'] ) && 'no' === $args['slideoverlay'] ) ? 'false' : 'true';
$titletag   = ! empty( $args['titletag'] ) ? $args['titletag'] : 'h3';
$otype      = '';
if ( 'true' === $overlay ) {
	$otype = ! empty( $args['slideoverlay'] ) ? $args['slideoverlay'] : 'all';
}

ob_start();
$count_items = 0;
foreach ( $posts as $item ) :
	setup_postdata( $item );
	if ( ! empty( $imgsize ) ) :
		if ( 'none' === $imgsize ) {
			$image[0] = LPS_PLUGIN_URL . 'assets/images/samples/0.svg';
		} else {
			$th_id = ( 'attachment' === $item->post_type )
				? (int) $item->ID
				: get_post_thumbnail_id( (int) $item->ID );
			$image = wp_get_attachment_image_src( $th_id, $imgsize );
		}
		if ( empty( $image[0] ) && ! empty( $args['image_placeholder'] ) ) {
			$image[0] = esc_attr( self::select_random_placeholder( $args['image_placeholder'] ) );
		}
		if ( ! empty( $image[0] ) ) :
			$a_start   = '<div class="slide-inner"';
			$a_end     = '</div>';
			$title_str = self::cleanup_title( $item->post_title );
			if ( $url ) {
				$link_target = ( 'yes_blank' === $args['url'] ) ? ' target="_blank"' : '';
				$a_start     = '<a href="' . get_permalink( $item->ID ) . '"' . $link_target . ' title="' . esc_attr( $title_str ) . '" class="slide-inner">';
				$a_end       = '</a>';
			}
			++$count_items;
			?>
			<div data-lps-id="<?php echo (int) $item->ID; ?>">
				<?php echo $a_start; // phpcs:ignore ?>
				<div class="img-wrap"><img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php echo esc_attr( $title_str ); ?>"></div>

				<?php if ( ! empty( $otype ) ) : ?>
					<div class="overlay">
						<?php if ( 'all' === $otype || 'title' === $otype ) : ?>
							<<?php echo esc_attr( $titletag ); ?> class="item-title-tag"><?php echo esc_html( $title_str ); ?></<?php echo esc_attr( $titletag ); ?>>
						<?php endif; ?>
						<?php
						if ( 'all' === $otype || 'text' === $otype ) :
							$text = '';
							$lim  = $chrlimit;
							if ( $use_trim ) {
								$lim = (int) $chrlimit - mb_strlen( $title_str );
								$lim = $lim < 0 ? 0 : $lim;
							}
							if ( in_array( 'excerpt', $extra, true )
								|| in_array( 'content', $extra, true )
								|| in_array( 'content-small', $extra, true )
								|| in_array( 'excerpt-small', $extra, true )
								|| 'all' === $otype ) :
								if ( in_array( 'excerpt', $extra, true ) ) {
									$text = apply_filters( 'the_excerpt', strip_shortcodes( get_the_excerpt( $item ) ) );
								} elseif ( in_array( 'excerpt-small', $extra, true )
									|| 'all' === $otype ) {
									$text = self::get_short_text( get_the_excerpt( $item ), $lim, true, $trimmore );
								} elseif ( in_array( 'content', $extra, true ) ) {
									$text = apply_filters( 'the_content', $item->post_content );
								} elseif ( in_array( 'content-small', $extra, true ) ) {
									$text = self::get_short_text( $item->post_content, $lim, false, $trimmore );
								}
								if ( ! empty( $text ) ) {
									$text = wp_strip_all_tags( $text );
								}
								echo '<div>' . esc_html( $text ) . '</div>';
							endif;
						endif;
						?>
					</div>
				<?php endif; ?>
				<?php echo $a_end; // phpcs:ignore ?>
			</div>
			<?php
		endif;
	endif;
endforeach;
wp_reset_postdata();
$cards = ob_get_clean();

if ( empty( $cards ) ) {
	// Fail-fast, no cards to display for the slider.
	return;
}

// Render helper checkes.
$is_block_rendering = defined( 'REST_REQUEST' ) && REST_REQUEST;
$in_the_editor      = self::is_in_the_editor();
$in_the_preview     = is_preview();

$static = false;
if ( $in_the_editor && $is_block_rendering ) {
	$static = true;
} elseif ( self::$is_elementor_editor && is_admin() ) {
	$static = true;
} else {
	$static = ( $in_the_editor || $is_block_rendering || self::$is_elementor_editor ) && ! $in_the_preview && is_admin();
}

$shortcode_id = md5( wp_json_encode( $args ) . microtime() );

$css      = ! empty( $args['css'] ) ? $args['css'] : '';
$height   = ! empty( $args['slidermaxheight'] ) ? (int) $args['slidermaxheight'] : 0;
$height   = empty( $height ) && 'none' === $imgsize ? 100 : $height;
$wrap     = ! empty( $args['sliderwrap'] ) && in_array( $args['sliderwrap'], self::$slider_wrap_tags, true ) ? $args['sliderwrap'] : 'div';
$mode     = ! empty( $args['slidermode'] ) ? $args['slidermode'] : 'horizontal'; // phpcs:ignore
$auto     = ! empty( $args['sliderauto'] ) ? 'true' : 'false';
$speed    = ! empty( $args['sliderspeed'] ) ? (int) $args['sliderspeed'] : 1000;
$ctrl     = ! empty( $args['slidercontrols'] ) ? 'true' : 'false';
$slides   = ! empty( $args['slideslides'] ) ? (int) $args['slideslides'] : 1;
$scroll   = ! empty( $args['slidescroll'] ) ? (int) $args['slidescroll'] : 1;
$dots     = ! empty( $args['sliderdots'] ) ? 'true' : 'false';
$inf      = ! empty( $args['sliderinfinite'] ) ? 'true' : 'false';
$t_bp     = ! empty( $args['sliderbreakpoint_tablet'] ) ? (int) $args['sliderbreakpoint_tablet'] : 600;
$t_slides = ! empty( $args['slideslides_tablet'] ) ? (int) $args['slideslides_tablet'] : 1;
$t_scroll = ! empty( $args['slidescroll_tablet'] ) ? (int) $args['slidescroll_tablet'] : 1;
$t_dots   = ! empty( $args['sliderdots_tablet'] ) ? 'true' : 'false';
$t_inf    = ! empty( $args['sliderinfinite_tablet'] ) ? 'true' : 'false';
$m_bp     = ! empty( $args['sliderbreakpoint_mobile'] ) ? (int) $args['sliderbreakpoint_mobile'] : 460;
$m_slides = ! empty( $args['slideslides_mobile'] ) ? (int) $args['slideslides_mobile'] : 1;
$m_scroll = ! empty( $args['slidescroll_mobile'] ) ? (int) $args['slidescroll_mobile'] : 1;
$m_dots   = ! empty( $args['sliderdots_mobile'] ) ? 'true' : 'false';
$m_inf    = ! empty( $args['sliderinfinite_mobile'] ) ? 'true' : 'false';
$gaps     = ! empty( $args['slidegap'] ) ? (int) $args['slidegap'] : 0;
$center   = ! empty( $args['centermode'] ) && 'horizontal' === $mode ? 'true' : 'false';
$padd     = ! empty( $args['centerpadd'] ) ? (int) $args['centerpadd'] : 0;
$resp     = ! empty( $args['slidersponsive'] && 'yes' === $args['slidersponsive'] ) ? 'true' : 'false';
$respto   = ! empty( $args['respondto'] ) && in_array( $args['respondto'], [ 'window', 'slider' ], true ) ? $args['respondto'] : 'min';

if ( ! empty( $args['slideratio'] ) ) {
	if ( 'contain' === $args['slideratio'] ) {
		$css .= ' has-image-contain';
	} else {
		$css .= ' has-image-ratio';
	}
}

$max_height = ( ! empty( $height ) ) ? $height . 'px' : 'unset';

ob_start();

?>
{#}-wrap {
	max-width: 100%;
	width: 100%;
}

{#} {
	--slider-cols: <?php echo (int) $slides; ?>;
	--slider-gaps: <?php echo (int) $gaps; ?>px;
	--slider-diff: <?php echo (int) $padd; ?>px;
	--slide-size: calc((100% - (var(--slider-cols) - 1) * var(--slider-gaps)) / var(--slider-cols));

	box-sizing: border-box;
}

{#} * { box-sizing: border-box;}

<?php
if ( ! $static ) {
	?>
	{#} { display: none; }
	<?php
	if ( 'horizontal' === $mode ) {
		if ( 'true' !== $center ) {
			?>
			{#} {
				display: block;
				min-width: calc(100% + <?php echo 1 * (int) $gaps; ?>px) !important;
				margin-inline: <?php echo -1 / 2 * (int) $gaps; ?>px;
			}
			<?php
		}
	}
} else {
	?>
	{#} {
		display: block;
		max-height: <?php echo esc_attr( $max_height ); ?>;
		overflow: hidden;
	}
	{#}.has-radius .slide-inner { display: grid; border-radius: 0.5rem; overflow: clip;}
	{#}.has-radius [data-lps-id] {border-radius: 0.5rem; overflow: clip;}
	{#}.has-radius [data-lps-id] .overlay {border-radius: 0 0 0.5rem 0.5rem; overflow: clip;}
	<?php
	if ( 'horizontal' === $mode ) {
		if ( 'true' === $center ) {
			?>
			{#} {
				min-width: calc(100% + <?php echo 2 * (int) $padd; ?>px) !important;
				margin-inline: <?php echo -1 * (int) $padd; ?>px;
			}
			<?php
		} else {
			?>
			{#} {
				display: block;
				min-width: calc(100% + <?php echo 1 * (int) $gaps; ?>px) !important;
				margin-inline: <?php echo -1 / 2 * (int) $gaps; ?>px;
			}
			<?php
		}
	}
}

if ( ! empty( $height ) ) {
	?>
	{#} > div, {#} .img-wrap, {#} .slick-slide {
		max-height: <?php echo esc_attr( $max_height ); ?>;
		overflow: hidden;
	}
	<?php
}

if ( ! empty( $gaps ) ) {
	?>
	{#} .slick-slide {
		align-items: center;
		<?php
		if ( 'vertical' === $mode ) {
			?>
			border: 0;
			margin: <?php echo (int) ceil( (int) $gaps / 2 ); ?>px 0;
			<?php
		} elseif ( 'horizontal' === $mode ) {
			?>
			margin: 0 <?php echo (int) ceil( (int) $gaps / 2 ); ?>px;
			<?php
		}
		?>
	}
	{#} .slick-list { margin: 0; }
	<?php
}
?>

{#}.has-radius .slick-slide { border-radius: 0.5rem; overflow: clip;}

<?php
if ( 'true' === $ctrl ) {
	?>
	{#} .slick-prev {left: var(--slider-gaps);}
	{#} .slick-next {right: var(--slider-gaps);}
	<?php
}

if ( 'true' === $center ) {
	?>
	{#} .slick-track { display: flex; flex-wrap: nowrap; gap: 0; align-items: center; }
	{#} .slick-slide { display: grid; align-self: center; }
	{#} .slick-center { padding-bottom: 0 !important; }

	<?php
	if ( 'horizontal' === $mode ) {
		?>
		{#} .slick-list.draggable { margin-inline: <?php echo -2 * (int) $padd; ?>px;}
		{#}.has-radius .slick-slide:not(.slick-center) > .slide-inner { border-radius: 0.5rem; overflow: clip;}
		<?php
	}
	?>

	{#} .slick-slide {
		margin: 0;
		padding: <?php echo (int) $padd; ?>px;
		position: relative;
	}
	{#} .slick-slide .overlay {
		max-width: calc(100% - <?php echo 2 * (int) $padd; ?>px);
		margin-left: 0px;
		bottom: <?php echo (int) $padd; ?>px;
		display: none;
	}
	{#} .slick-center .overlay {
		max-width: calc(100%);
		margin-left: -<?php echo (int) $padd; ?>px;
		bottom: 0px;
		display: block;
	}
	{#} .slick-center .img-wrap {
		min-width: calc(100% + <?php echo 2 * (int) $padd; ?>px);
		max-height: auto;
		height: auto;
		margin-left: -<?php echo (int) $padd; ?>px;
		margin-top: -<?php echo (int) $padd; ?>px;
	}
	<?php
}

if ( 'true' === $dots ) {
	?>
	{#}.slick-dotted { margin-bottom: 0; padding-bottom: <?php echo ( (int) $gaps + 32 ); ?>px; }
	{#} .slick-dots { bottom: 0; height: 2rem; }
	<?php
}

if ( ! empty( $args['slideratio'] ) ) {
	?>
	{#} .slick-slide {
		aspect-ratio: <?php echo esc_attr( $args['slideratio'] ); ?>;
	}
	{#} > div img {
		aspect-ratio: <?php echo esc_attr( $args['slideratio'] ); ?>;
		object-fit: cover;
	}
	<?php
}

if ( ! $static && 'horizontal' === $mode && 'true' === $center && 'true' === $resp ) {
	if ( 1 === $t_slides ) {
		?>
		{#} .slick-list.draggable {
			@media (max-width: <?php echo (int) $t_bp; ?>px) {
				margin-inline: <?php echo -1 * (int) $padd; ?>px;
			}
		}
		<?php
	}
	if ( 1 === $m_slides ) {
		?>
		{#} .slick-list.draggable {
			@media (max-width: <?php echo (int) $m_bp; ?>px) {
				margin-inline: <?php echo -1 * (int) $padd; ?>px;
			}
		}
		<?php
	}
}

if ( $static ) {
	?>
	.latest-post-selection-slider,.latest-post-selection-slider *,.latest-post-selection-slider-wrap{box-sizing:border-box}.latest-post-selection-slider-wrap{height:auto;margin:0;max-width:100%;overflow:clip;padding:0;width:100%}.latest-post-selection-slider>div{overflow-y:hidden;position:relative}.latest-post-selection-slider>div .overlay{background:rgba(0,0,0,.5);bottom:0;color:hsla(0,0%,100%,.75);font-size:inherit;line-height:inherit;max-width:100%;overflow:hidden;padding:1rem;position:absolute;width:100%}.latest-post-selection-slider>div .overlay .item-title-tag{color:#fff;font-size:1.3em;line-height:1.3em;margin:0}.latest-post-selection-slider>div .overlay .item-title-tag+div{margin-top:.45rem}.latest-post-selection-slider>div .overlay>div{font-size:.85em}.latest-post-selection-slider>div img{box-shadow:none;width:100%}.latest-post-selection-slider .img-wrap{overflow-y:hidden}.latest-post-selection-slider .slick-slide{position:relative}

	{#} {
		align-items: center;
		display: grid;
		gap: 0px;
		<?php
		if ( 'vertical' === $mode ) {
			?>
			grid-template-columns: 100%;
			<?php
		} elseif ( 'horizontal' === $mode ) {
			?>
			grid-template-columns: repeat(<?php echo (int) $slides; ?>, 1fr);
			<?php
		}
		?>
	}

	{#} > div { display: none; }
	{#} > div:nth-child(-n+<?php echo (int) $slides; ?>) { display: block; }
	{#} > div img { display: block; }

	<?php
	if ( 'true' === $center ) {
		$nth = ceil( $slides / 2 );
		?>
		{#} > div:nth-child(-n+<?php echo (int) $slides; ?>) {
			margin: 0px;
			padding: <?php echo (int) $padd; ?>px;
			position: relative;
		}
		{#} > div:nth-child(-n+<?php echo (int) $slides; ?>):not(:nth-child(<?php echo (int) $nth; ?>)) .overlay {
			display: none;
		}
		{#} > div:nth-child(<?php echo (int) $nth; ?>) {
			padding: 0;
		}
		<?php
	} else { // phpcs:ignore
		if ( 'horizontal' === $mode ) {
			?>
			{#} > div {
				border-left: <?php echo (int) ceil( (int) $gaps / 2 ); ?>px solid transparent;
				border-right: <?php echo (int) ceil( (int) $gaps / 2 ); ?>px solid transparent;
			}
			<?php
		} elseif ( 'vertical' === $mode ) {
			?>
			{#} > div {
				border-top: <?php echo (int) ceil( (int) $gaps / 2 ); ?>px solid transparent;
				border-bottom: <?php echo (int) ceil( (int) $gaps / 2 ); ?>px solid transparent;
			}
			<?php
		}
	}
	if ( 'true' === $dots ) {
		?>
		{#}-dots {
			font-size: 2rem;
			height: 2rem;
			margin-top: <?php echo (int) $gaps; ?>px;
			position: relative;
			padding: 0 !important;
			letter-spacing: 0.5em;
			line-height: 1rem;
			text-align: center;
			width: 100%;
		}
		<?php
	}
}

$sliderstyle = ob_get_clean();
$sliderstyle = str_replace( '{#}', '#latest-post-selection-slider-' . esc_attr( $shortcode_id ), $sliderstyle );

// Normalize newlines.
$sliderstyle = self::custom_minify( $sliderstyle, true );

// Add a wrapper for better controll.
echo '<div class="lps-slider-wrap">';

// Output the inline styles.
echo '<style id="lps-slider-' . $shortcode_id . '-style">' . $sliderstyle . '</style>'; // phpcs:ignore

$css .= 'horizontal' === $mode ? ' is-horizontal' : '';

$slider = '<' . esc_attr( $wrap ) . ' class="latest-post-selection-slider-wrap" id="latest-post-selection-slider-' . esc_attr( $shortcode_id ) . '-wrap">
	<div class="latest-post-selection-slider ' . esc_attr( $css ) . '"
		id="latest-post-selection-slider-' . esc_attr( $shortcode_id ) . '">
		' . $cards . '
	</div>';
if ( $static && 'true' === $dots ) {
	$slider .= '<div id="latest-post-selection-slider-' . esc_attr( $shortcode_id ) . '-dots">' . trim( str_repeat( '. ', max( 1, $count_items ) ) ) . '</div>';
}
$slider .= '</' . esc_attr( $wrap ) . '>';

// Normalize string.
$slider = preg_replace( '/(\r\n|\r|\n)+/', ' ', $slider );
$slider = preg_replace( '/\s+/', ' ', $slider );

echo $slider; // phpcs:ignore
echo '</div>'; // Close the wrapper element.

$script = '';
ob_start();
?>
jQuery(document).ready(function(){
	jQuery('#latest-post-selection-slider-<?php echo esc_attr( $shortcode_id ); ?>').slick({
		<?php if ( 'vertical' === $mode ) : ?>
			vertical: true,
		<?php elseif ( 'horizontal' === $mode ) : ?>
			vertical: false,
		<?php elseif ( 'fade' === $mode ) : ?>
			fade: true,
		<?php endif; ?>
		lazyLoad: 'progress',
		<?php if ( empty( $height ) ) : ?>
			adaptiveHeight: true,
		<?php else : ?>
			adaptiveHeight: false,
		<?php endif; ?>
		rows: 1,
		draggable: true,
		accessibility: true,
		autoplay: <?php echo esc_attr( $auto ); ?>,
		autoplaySpeed: <?php echo (int) $speed; ?>,
		speed: 300,
		pauseOnFocus: true,
		pauseOnHover: true,
		pauseOnDotsHover: true,
		slidesToShow: <?php echo (int) $slides; ?>,
		slidesToScroll: <?php echo (int) $scroll; ?>,
		infinite: <?php echo esc_attr( $inf ); ?>,
		dots: <?php echo esc_attr( $dots ); ?>,
		arrows: <?php echo esc_attr( $ctrl ); ?>,
		<?php if ( 'true' === $resp ) : ?>
			respondTo: '<?php echo esc_attr( $respto ); ?>',
			responsive: [{
				breakpoint: 1200,
				settings: {
					slidesToShow: <?php echo (int) $slides; ?>,
					slidesToScroll: <?php echo (int) $scroll; ?>,
					infinite: <?php echo esc_attr( $inf ); ?>,
					dots: <?php echo esc_attr( $dots ); ?>
				}
			}, {
				breakpoint: <?php echo (int) $t_bp; ?>,
				settings: {
					slidesToShow: <?php echo (int) $t_slides; ?>,
					slidesToScroll: <?php echo (int) $t_scroll; ?>,
					infinite: <?php echo esc_attr( $t_inf ); ?>,
					dots: <?php echo esc_attr( $t_dots ); ?>
				}
			},{
				breakpoint: <?php echo (int) $m_bp; ?>,
				settings: {
					slidesToShow: <?php echo (int) $m_slides; ?>,
					slidesToScroll: <?php echo (int) $m_scroll; ?>,
					infinite: <?php echo esc_attr( $m_inf ); ?>,
					dots: <?php echo esc_attr( $m_dots ); ?>
				}
			}],
		<?php endif; ?>
		<?php if ( 'true' === $center ) : ?>
			centerMode: true,
			centerPadding: '<?php echo (int) $padd; ?>px',
		<?php endif; ?>
		zIndex: 1000
	});

	<?php if ( 'none' === $imgsize ) : ?>
		jQuery('#latest-post-selection-slider-<?php echo esc_attr( $shortcode_id ); ?> .overlay').css({'height': <?php echo (int) $height; ?>});
	<?php endif; ?>
	<?php if ( 'true' === $auto ) : ?>
		jQuery('#latest-post-selection-slider-<?php echo esc_attr( $shortcode_id ); ?>').on('mouseleave', function() {
			jQuery('#latest-post-selection-slider-<?php echo esc_attr( $shortcode_id ); ?>').slick('play');
		});
	<?php endif; ?>
	jQuery('#latest-post-selection-slider-<?php echo esc_attr( $shortcode_id ); ?>').show();
	jQuery('#latest-post-selection-slider-<?php echo esc_attr( $shortcode_id ); ?>').slick('refresh');
});
<?php
$script = ob_get_clean();

// Normalize newlines.
$script = self::custom_minify( $script, false );
if ( ! $static ) {
	wp_register_script( 'lps-slider-' . $shortcode_id . 'script', '', [], 1, true );
	wp_enqueue_script( 'lps-slider-' . $shortcode_id . 'script' );
	wp_add_inline_script( 'lps-slider-' . $shortcode_id . 'script', $script );
}
