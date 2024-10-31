<?php
/**
 * Responsive Select Menu Walker Class
 *
 * Credits Chris Mavricos, SevenSpark
 *
 * Version 1.0.1
 */
class RMS_Walker extends Walker_Nav_Menu {

	private $index = 0;
	protected $menu_item_options;

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $current_object_id = 0 ) {

		global $rms_main;
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$dashes = ( $depth ) ? str_repeat( $rms_main->get_settings()->op( 'spacer' ), $depth ) : ''; // "&ndash; "

		$class_names = $value = '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		if ( ( '#' === $item->url || '' === $item->url ) && $rms_main->get_settings()->op( 'exclude-hashes' ) ) {
			return;
		}

		$item->url  = urldecode( $item->url );
		$attributes = ' value="' . esc_attr( $item->url ) . '"';

		if ( $rms_main->get_settings()->op( 'current_selected' ) && strpos( $class_names, 'current-menu-item' ) > 0 ) {
			$attributes .= ' selected="selected"';
		}

		$output .= $indent . '<option ' . $id . $attributes . '>';

		$item_output  = $args->before;
		$item_output .= $dashes . $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= $args->after;

		$output .= str_replace( '%', '%%', $item_output );

		$output .= "</option>\n";
	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		return;
	}

	/**
	 * Recursive function to remove all children
	 *
	 * @param array $children_elements Array of child elements.
	 * @param int   $id id of current item.
	 */
	public function clear_children( &$children_elements, $id ) {

		if ( empty( $children_elements[ $id ] ) ) {
			return;
		}

		foreach ( $children_elements[ $id ] as $child ) {
			$this->clear_children( $children_elements, $child->ID );
		}
		unset( $children_elements[ $id ] );
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * @param string $element  Menu item.
	 * @param array  $children_elements  Submenu items.
	 * @param int    $max_depth  Menu depth.
	 * @param int    $depth  default value.
	 * @param array  $args which fields.
	 * @param string $output output string.
	 */
	public function rms_display_element( $element, &$children_elements, $max_depth, $args, &$output, $depth = 0 ) {

		if ( ! $element ) {
			return;
		}

		global $rms_main;

		Walker_Nav_Menu::rms_display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
