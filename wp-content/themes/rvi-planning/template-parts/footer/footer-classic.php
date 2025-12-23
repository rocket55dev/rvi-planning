<?php
/**
 * The template for classic footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package R55 Starter
 */
$newsletter_signup_text = get_field('newsletter_signup_text', 'option');
$newsletter_signup_form = get_field('newsletter_signup_form', 'option');
$footer_text = get_field('footer_text', 'option');
?>
<!-- footer classic -->
<footer class="site-footer position-relative overflow-hidden">
	<div class="newsletter-signup py-5 position-relative z-2 border-bottom">
		<div class="container-xl">
			<div class="row">
				<div class="col-md-6 mb-4 mb-m-0">
					<?php if ( $newsletter_signup_text ) : ?>
						<?php echo wp_kses_post( $newsletter_signup_text ); ?>
					<?php endif; ?>
				</div>
				<div class="col-md-6 newsletter form">
					<?php if ( $newsletter_signup_form ) : ?>
						<?php echo do_shortcode( $newsletter_signup_form ); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="main-footer pt-5 position-relative z-2 pb-4">
		<div class="container-xl">
			<div class="row bott-line">
				<div class="col-md-6 col-lg-3 mb-4 mb-md-0">
					<?php the_secondary_logo(); ?>
					<!-- Add a WP Menu -->
					<?php if($footer_text): ?>
						<div class="footer-text mt-3">
							<?php echo wp_kses_post( $footer_text ); ?>
						</div>
					<?php endif; ?>
					<div>
						<a href="/contact/" class="btn btn-primary btn-clipped mt-3 d-none d-md-inline-block">Contact Us</a>
					</div>
				</div>
				<div class="col-md-6 col-lg-3">
					<h3 class="h4">Quick Links</h3>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'nav flex-column',
						'link_class'     => 'pt-0 ps-0',  // ← Add custom classes here
						'depth'          => 1,
						'walker'         => new Simple_Bootstrap_Nav_Walker(),
					) );
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="bottom-footer position-relative z-2 py-3 border-top border-bottom">
		<div class="container-xl">
			<div class="row">
				<div class="col-md-4">
					<?php
					display_social_media_links(array(
						'wrapper_class' => 'col-lg-6 d-flex justify-content-lg-end'
					));
					?>
				</div>
				<div class="col-md-8 d-flex justify-content-md-end">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer-terms',
						'container'      => false,
						'menu_class'     => 'nav',
						'link_class'     => 'pt-0',  // ← Add custom classes here
						'depth'          => 1,
						'walker'         => new Simple_Bootstrap_Nav_Walker(),
					) );
					?>
					<p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</p>
				</div>
			</div>
		</div>
	</div>
	<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/footer-creek.svg" alt="" class="footer-creek-img position-absolute end-0" loading="lazy" aria-hidden="true" <?php sal('fade', 500); ?>>
</footer>
