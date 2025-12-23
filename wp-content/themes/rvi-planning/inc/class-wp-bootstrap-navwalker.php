<?php

class Simple_Bootstrap_Nav_Walker extends Walker_Nav_Menu {
    
	/**
	 * Starts the element output.
	 */
	function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		// Start with nav-link
		$link_classes = array('nav-link');

		// Add custom link classes from wp_nav_menu args (WordPress 5.9+)
		if (isset($args->link_class)) {
			$link_classes[] = $args->link_class;
		}

		// Check if current/active
		if (in_array('current-menu-item', $item->classes) || 
			in_array('current_page_item', $item->classes) ||
			in_array('current-menu-parent', $item->classes)) {
			$link_classes[] = 'active';
		}

		// Build output
		$output .= $indent . '<li class="nav-item">';

		$attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
		$attributes .= ' class="' . esc_attr(implode(' ', $link_classes)) . '"';

		if (in_array('current-menu-item', $item->classes) || 
			in_array('current_page_item', $item->classes)) {
			$attributes .= ' aria-current="page"';
		}

		$item_output = '<a' . $attributes . '>';
		$item_output .= apply_filters('the_title', $item->title, $item->ID);
		$item_output .= '</a>';

		$output .= $item_output;
	}

	/**
	 * Ends the element output.
	 */
	function end_el(&$output, $item, $depth = 0, $args = null) {
		$output .= "</li>\n";
	}
}

// bootstrap 5 wp_nav_menu walker
class bootstrap_5_wp_nav_menu_walker extends Walker_Nav_menu
{
  private $current_item;
  private $dropdown_menu_alignment_values = [
	'dropdown-menu-start',
	'dropdown-menu-end',
	'dropdown-menu-sm-start',
	'dropdown-menu-sm-end',
	'dropdown-menu-md-start',
	'dropdown-menu-md-end',
	'dropdown-menu-lg-start',
	'dropdown-menu-lg-end',
	'dropdown-menu-xl-start',
	'dropdown-menu-xl-end',
	'dropdown-menu-xxl-start',
	'dropdown-menu-xxl-end'
  ];

  function start_lvl(&$output, $depth = 0, $args = null)
  {
	$dropdown_menu_class[] = '';
	foreach($this->current_item->classes as $class) {
	  if(in_array($class, $this->dropdown_menu_alignment_values)) {
		$dropdown_menu_class[] = $class;
	  }
	}
	$indent = str_repeat("\t", $depth);
	$submenu = ($depth > 0) ? ' sub-menu' : '';
	$output .= "\n$indent<ul class=\"dropdown-menu$submenu " . esc_attr(implode(" ",$dropdown_menu_class)) . " depth_$depth\">\n";
  }

  function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
  {
	$this->current_item = $item;

	$indent = ($depth) ? str_repeat("\t", $depth) : '';

	$li_attributes = '';
	$class_names = $value = '';

	$classes = empty($item->classes) ? array() : (array) $item->classes;

	$classes[] = ($args->walker->has_children) ? 'dropdown' : '';
	$classes[] = 'nav-item';
	$classes[] = 'nav-item-' . $item->ID;
	if ($depth && $args->walker->has_children) {
	  $classes[] = 'dropdown-menu dropdown-menu-end';
	}

	$class_names =  join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
	$class_names = ' class="' . esc_attr($class_names) . '"';

	$id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
	$id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

	$output .= $indent . '<li ' . $id . $value . $class_names . $li_attributes . '>';

	$attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
	$attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
	$attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
	if ( ! $args->walker->has_children ) {
		// don't add href if it's a button
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
	}
	$active_class = ($item->current || $item->current_item_ancestor || in_array("current_page_parent", $item->classes, true) || in_array("current-post-ancestor", $item->classes, true)) ? 'active' : '';
	$nav_link_class = ( $depth > 0 ) ? 'dropdown-item ' : 'nav-link ';
	$attributes .= ( $args->walker->has_children ) ? ' class="'. $nav_link_class . $active_class . ' dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"' : ' class="'. $nav_link_class . $active_class . '"';

	$item_output = $args->before;
	$item_output .= '<a' . $attributes . '>';
	$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
	$item_output .= '</a>';
	$item_output .= $args->after;

	$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
  }
}
