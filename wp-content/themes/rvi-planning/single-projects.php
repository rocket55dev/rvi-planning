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

	<?php
	while (have_posts()) :
		the_post();

		get_template_part('template-parts/content', 'projects');


	endwhile; // End of the loop.
	?>

</main><!-- #main -->
<?php get_footer(); ?>