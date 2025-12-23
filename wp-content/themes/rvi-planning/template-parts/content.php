<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package R55 Starter
 */

?>

	<article <?php post_class(); ?>>

		<div class="entry-content mb-5">
				<div class="row">
					<div class="col-12">
						<?php
						$blog_page_id = get_option( 'page_for_posts' );
						$blog_url = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
						?>
						<div class="corner-tag d-inline-flex align-items-center mt-5 mb-4 py-2 px-4 text-white text-uppercase fw-bold">
							<a href="<?php echo esc_url( $blog_url ); ?>">Blog</a>
						</div>
						<h1 class="pb-2"><?php the_title(); ?></h1>
						<?php
						$author_id = get_the_author_meta( 'ID' );
						$author_name = get_the_author_meta( 'display_name' );
						$post_date = get_the_date( 'm/d/Y' );
						$post_tags = get_the_tags();
						$categories = get_the_category();
						$category = $categories ? $categories[0] : null;
						?>
						<div class="posts-filter-meta d-flex align-items-center flex-wrap mb-4">
							<?php echo get_avatar( $author_id, 32, '', $author_name, ['class' => 'rounded-1 me-2'] ); ?>
							<span class="posts-filter-author"><?php echo esc_html( $author_name ); ?></span>
							<span class="posts-filter-dot mx-2"></span>
							<span class="posts-filter-date"><?php echo esc_html( $post_date ); ?></span>
							<?php if ( $category ) : ?>
								<span class="posts-filter-dot posts-filter-dot--teal mx-2"></span>
								<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="posts-filter-category"><?php echo esc_html( $category->name ); ?></a>
							<?php endif; ?>
							<?php if ( $post_tags ) : ?>
								<span class="posts-filter-dot posts-filter-dot--gold mx-2"></span>
								<span class="posts-filter-tags">Tagged Under:
									<?php
									$tag_links = array();
									foreach ( $post_tags as $tag ) {
										$tag_links[] = '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a>';
									}
									echo implode( ', ', $tag_links );
									?>
								</span>
							<?php endif; ?>
						</div>
					</div>
					<?php if( has_post_thumbnail() ) : ?>
						<div class="col-12 mb-4">
							<?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid w-100' ) ); ?>
						</div>
					<?php endif; ?>
				</div>

			<?php
				the_content(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. */
							__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'rocket55' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					)
				);

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rocket55' ),
						'after'  => '</div>',
					)
				);
			?>
		</div><!-- .entry-content -->

		<?php
		$author_id = get_the_author_meta( 'ID' );
		$author_name = get_the_author_meta( 'display_name' );
		$author_bio = get_the_author_meta( 'description' );
		?>
		<div class="post-author-box d-flex p-4">
			<div class="post-author-avatar me-4">
				<?php echo get_avatar( $author_id, 100, '', $author_name, ['class' => 'rounded-1'] ); ?>
			</div>
			<div class="post-author-info">
				<span class="post-author-label text-uppercase fw-bold d-block mb-2">Written By</span>
				<span class="post-author-name h4 d-block mb-2"><?php echo esc_html( $author_name ); ?></span>
				<?php if ( $author_bio ) : ?>
					<p class="post-author-bio mb-0"><?php echo esc_html( $author_bio ); ?></p>
				<?php endif; ?>
			</div>
		</div>

	</article><!-- #post-## -->
