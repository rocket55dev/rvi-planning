<?php
/**
 * The template for three column footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package R55 Starter
 */

?>
<!-- footer three column -->
<footer class="site-footer">
	<div class="container-xl">
		<div class="row align-items-center justify-content-lg-between bott-line">
			<div class="col-lg-3">
				<?php the_secondary_logo(); ?>
				<?php display_social_media_links(array(
					'show_wrapper' => true,
					'wrapper_class' => 'pt-4'
				)); ?>
			</div>
			<div class="col-lg-2">
				<h3>1st Column</h3>
				<!-- Add 1st WP Menu -->
			</div>
			<div class="col-lg-2">
				<h3>2nd Column</h3>
				<!-- Add 2nd WP Menu -->
			</div>
			<div class="col-lg-2">
				<h3>3rd Column</h3>
				<!-- Add 3rd WP Menu -->
			</div>
		</div>
		<div class="row align-items-center justify-content-md-between">
			<div class="site-info col-lg-6 mt-3">
				&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.
			</div><!-- .site-info -->
		</div>
	</div>
</footer>
