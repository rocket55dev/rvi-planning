<?php
/**
 * The template for mega footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package R55 Starter
 */
$info = get_field('info', 'options');
$phone_info = get_field('phone', 'options');
$email_info = get_field('email', 'options');
?>
<!-- footer mega -->
<footer class="site-footer">
	<div class="container-xl">
		<div class="row bott-line wysiwyg">
			<div class="col-lg-4 pb-4">
				<?php if (!empty($info)) : ?>
					<?php echo wp_kses_post($info); ?>
				<?php endif; ?>
				<?php if ($phone_info) :
					$phone_info_target = $phone_info['target'] ? $phone_info['target'] : '_self'; ?>
					<div class="d-flex align-items-baseline">
						<i class="fa-solid fa-mobile-screen"></i>
						<a href="<?php echo esc_url($phone_info['url']); ?>"
						   target="<?php echo esc_attr($phone_info_target); ?>"><?php echo esc_attr($phone_info['title']); ?></a>
					</div>
				<?php endif; ?>
				<?php if (!empty($email_info)) : ?>
					<div class="d-flex align-items-baseline pb-4">
						<i class="fa-solid fa-envelope"></i>
						<a href="mailto:<?php echo esc_attr($email_info); ?>"
						   target="_blank"><?php echo esc_attr($email_info); ?></a>
					</div>
				<?php endif; ?>
				<?php display_social_media_links(); ?>
				<?php the_secondary_logo(); ?>
			</div>
			<div class="col-lg-4">
				<h2>Add a Formshortcode</h2>
				<!-- Add a Formshortcode -->
			</div>
			<div class="col-lg-4">
				<h2>Add a WP Menu</h2>
				<!-- Add a WP Menu -->
			</div>
		</div>
		<div class="row align-items-center justify-content-md-between">
			<div class="site-info col-md-6 mt-3">
				&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.
			</div><!-- .site-info -->
		</div>
	</div>
</footer>
