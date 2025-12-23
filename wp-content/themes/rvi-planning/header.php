<?php
/**
* The header for our theme.
*
* This is the template that displays all of the <head> section and everything up until <div id="content">
*
* @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
*
* @package R55 Starter
*/
// GTM ID
// enter ONLY the numbers, not the gtm- prefix!!
$gtm_id = 'XXXXXXX';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link rel="stylesheet" href="https://use.typekit.net/vkb5hgp.css">

	<style>
		/* insert critical font styles here to prevent fout */
		/* body, headings, etc */
	</style>

	<?php wp_head(); ?>

	<?php
	//dont track from staging - enter your production hostname
	if ( $_SERVER['HTTP_HOST'] == 'www.YOURHOSTNAME.com' ) : ?>
	<!-- Google Tag Manager -->
	<script>
	(function(w, d, s, l, i) {
		w[l] = w[l] || [];
		w[l].push({
			'gtm.start': new Date().getTime(),
			event: 'gtm.js'
		});
		var f = d.getElementsByTagName(s)[0],
			j = d.createElement(s),
			dl = l != 'dataLayer' ? '&l=' + l : '';
		j.async = true;
		j.src =
			'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
		f.parentNode.insertBefore(j, f);
	})(window, document, 'script', 'dataLayer', 'GTM-<?php echo esc_attr( $gtm_id ); ?>');
	</script>
	<!-- End Google Tag Manager -->
	<?php endif; ?>

</head>

<body <?php body_class( 'site-wrapper' ); ?>>

	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-<?php echo esc_attr( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->

	<a class="skip-link visually-hidden-focusable" href="#main"><?php esc_html_e( 'Skip to content', 'rocket55' ); ?></a>

	<header class="site-header">
		<?php
		$notification = get_field( 'notification_content', 'options' );
		if ( $notification ): ?>
			<div class="notification-bar">
				<div class="container-xl">
					<div class="row">
						<div class="col-12">
							<?php echo wp_kses_post( $notification ); ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<nav class="navbar navbar-expand-md">
			<div class="container-xl">
				<div class="navbar-brand">
					<?php the_custom_logo(); ?>
				</div>

				<?php if ( has_nav_menu( 'primary' )) : ?>
					<button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu" aria-label="Toggle navigation">
						<span class="toggler-bar"></span>
						<span class="toggler-bar"></span>
					</button>

					<!-- Desktop Navigation -->
					<div class="d-none d-md-flex justify-content-center flex-grow-1">
						<?php
						wp_nav_menu(array(
							'theme_location' => 'primary',
							'container' => false,
							'menu_class' => '',
							'fallback_cb' => '__return_false',
							'items_wrap' => '<ul id="%1$s" class="navbar-nav mb-2 mb-md-0 %2$s">%3$s</ul>',
							'depth' => 2,
							'walker' => new bootstrap_5_wp_nav_menu_walker()
						));
						?>
					</div>
				<?php endif; ?>
				<a href="" class="btn btn-primary btn-clipped d-none d-md-block">Let's Talk</a>
			</div>
		</nav>

	</header><!-- .site-header-->

	<!-- Mobile Offcanvas Navigation -->
	<?php if ( has_nav_menu( 'primary' )) : ?>
		<div class="offcanvas offcanvas-start w-100" id="offcanvasMenu" tabindex="-1" aria-labelledby="offcanvasMenuLabel" data-bs-backdrop="false">
			<div class="offcanvas-header">
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
			<div class="offcanvas-body">
				<?php
				wp_nav_menu(array(
					'theme_location' => 'primary',
					'container' => false,
					'menu_class' => '',
					'fallback_cb' => '__return_false',
					'items_wrap' => '<ul id="%1$s" class="navbar-nav %2$s">%3$s</ul>',
					'depth' => 2,
					'walker' => new bootstrap_5_wp_nav_menu_walker()
				));
				?>
				<a href="/contact/" class="btn btn-primary btn-clipped d-md-none position-absolute bottom-0 start-0 d-block w-100">Let's Talk</a>
			</div>
		</div>
	<?php endif; ?>
