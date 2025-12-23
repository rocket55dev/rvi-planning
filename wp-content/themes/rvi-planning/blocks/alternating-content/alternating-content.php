<?php
/**
 * Alternating Content
 *
 * @param array $block The block settings and attributes.
 */

// ACF data
$content_position_field = get_field( 'content_position' );
$content = get_field( 'content' );
$content_size = get_field( 'content_size' );
$link = get_field( 'link' );
$text_link = get_field( 'text_link' );

// Media fields
$media_type = get_field( 'media_type' );
$image = get_field( 'image' );
$video = get_field( 'video' );
$shortcode = get_field( 'shortcode' );

// View options
$row_direction = ( 'left' === $content_position_field ) ? '' : ' flex-row-reverse';

$col_media_class = ( 'one-third' === $content_size ) ? 'col-md-8' : ( ( 'two-thirds' === $content_size ) ? 'col-md-4' : 'col-md-6' );
$col_content_class = ( 'one-third' === $content_size ) ? 'col-md-4' : ( ( 'two-thirds' === $content_size ) ? 'col-md-8' : 'col-md-6' );

// Block wrapper attributes - automatically adds block class, anchor, and bottom_margin
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if(is_admin()): ?>
		<span class="badge badge-success position-absolute mr-3">alternating-content</span>
	<?php endif; ?>
	<div class="container-xl">
		<div class="row<?php echo esc_attr( $row_direction ); ?>">
			<div class="alternating-content-content <?php echo esc_attr( $col_content_class ); ?> mb-lg-0 mb-4 d-flex align-items-center">
				<div class="alt-content-wrap" <?php sal('slide-up', 500); ?>>
					<?php if( $content ) : ?>
						<?php echo wp_kses_post( $content ); ?>
					<?php endif; ?>
					<?php if( $link ):
							$link_url = $link['url'];
							$link_title = $link['title'];
							$link_target = $link['target'] ? $link['target'] : '_self';
							?>
							<a class="btn btn-primary btn-clipped mr-3" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
					<?php endif; ?>
					<?php if( $text_link ):
							$text_link_url = $text_link['url'];
							$text_link_title = $text_link['title'];
							$text_link_target = $text_link['target'] ? $text_link['target'] : '_self';
							?>
							<a class="btn text-link" href="<?php echo esc_url( $text_link_url ); ?>" target="<?php echo esc_attr( $text_link_target ); ?>"><?php echo esc_html( $text_link_title ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<div class="alternating-content-media <?php echo esc_attr( $col_media_class ); ?>" <?php sal('fade', 500); ?>>
				<?php if ( 'video' === $media_type && $video ) : ?>
					<div class="alternating-content-video ratio ratio-16x9">
						<?php echo $video; ?>
					</div>
				<?php elseif ( 'shortcode' === $media_type && $shortcode ) : ?>
					<div class="alternating-content-shortcode">
						<?php echo do_shortcode( $shortcode ); ?>
					</div>
				<?php elseif ( $image ) : ?>
					<?php echo wp_get_attachment_image( $image, 'full', false, ['class' => 'alt-content-img'] ); ?>
				<?php else : ?>
					<?php if ( is_admin() ) : ?>
						<div class="bg-secondary d-flex align-items-center justify-content-center" style="min-height: 300px;">
							<span class="text-white">No media selected</span>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
