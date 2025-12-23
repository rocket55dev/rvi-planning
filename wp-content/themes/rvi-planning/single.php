<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package R55 Starter
 */

get_header(); ?>

		<main id="main" class="site-main">

			<div class="container-xl">
				<div class="row">
					<div class="col-lg-9">
						<?php
						while ( have_posts() ) :
							the_post();

							get_template_part( 'template-parts/content', get_post_format() );


						endwhile; // End of the loop.
						?>
					</div>
					<aside class="col-lg-3 d-none d-lg-block in-post-nav">

					</aside>
				</div>
			</div>

		</main><!-- #main -->
<?php get_footer(); ?>
