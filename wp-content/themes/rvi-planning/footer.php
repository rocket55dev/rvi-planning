<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package R55 Starter
 */

?>
<?php
// set default value for footer layout
$footer_layout = get_theme_mod('footer_layout', 'classic');
// Add new footer template parts to array
$allowed = ['classic', 'three-column', 'mega'];
if (!in_array($footer_layout, $allowed, true)) {
	$footer_layout = 'classic';
}
get_template_part('template-parts/footer/footer', $footer_layout);

wp_footer();
?>

</body>
</html>
