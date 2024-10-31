<?php
/**
 * Responsive Select Menu Main Class
 *
 * Credits Chris Mavricos, SevenSpark
 *
 * Version 1.0.1
 */
class RMS_Main {

	/**
	 * Declare enabled
	 *
	 * @var enabled
	 */
	public $enabled;
	/**
	 * Declare enabled_determined
	 *
	 * @var enabled_determined
	 */
	public $enabled_determined;

	/**
	 * Constructor for class RMS_Main
	 */
	public function __construct() {

		$this->settings           = $this->rms_options_menu();
		$this->enabled_determined = false;

		if ( ! is_admin() ) {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}
	}

	/**
	 * Init filters
	 */
	public function init() {

		$this->rms_load_assets();

		// Filters.
		add_filter( 'wp_nav_menu_args', array( $this, 'rms_add_filter' ), 2100 );      // filters arguments passed to wp_nav_menu.

		add_filter( 'wp_nav_menu_args', array( $this, 'rms_filter' ), 2200 );         // second call, to print select menu.

	}

	/**
	 * Determine whether we should load the responsive select on these pages
	 * and cache the result.
	 */
	public function rms_is_enabled() {

		if ( $this->enabled_determined ) {
			return $this->enabled;
		}

		$this->enabled_determined = true;
		$this->enabled            = false;

		if ( ! $this->settings->op( 'display_only' ) ) {
			$this->enabled = true;
		} else {
			$list = $this->settings->op( 'display_only' );
			$list = str_replace( ' ', '', $list );
			$ids  = explode( ',', $list );

			global $post;
			if ( $post && in_array( $post->ID, $ids, true ) ) {
				$this->enabled = true;
			} else {
				$this->enabled = false;
			}
		}
		return $this->enabled;
	}

	/**
	 * Determine whether this particular menu location should be activated
	 *
	 * @param array $args wp_nav args.
	 */
	public function rms_is_activated( $args ) {

		// Activate All?
		if ( $this->settings->op( 'activate_theme_locations_all' ) ) {
			return true;
		}

		// Activate this theme_location specifically?
		if ( isset( $args['theme_location'] ) ) {
			$location               = $args['theme_location'];
			$active_theme_locations = $this->settings->op( 'active_theme_locations' );

			if ( is_array( $active_theme_locations ) && in_array( $location, $active_theme_locations, true ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Load CSS and head code
	 */
	public function rms_load_assets() {

		if ( ! is_admin() ) {
			add_action( 'wp_print_styles', array( $this, 'rms_load_css' ) );
			add_action( 'wp_head', array( $this, 'rms_insert_header_code' ), 110 );
		}

	}
	/**
	 * Load CSS
	 */
	public function rms_load_css() {
		if ( $this->rms_is_enabled() ) {
			wp_enqueue_script( 'jquery' );
		}
	}
	/**
	 * Inject header code
	 */
	public function rms_insert_header_code() {
		if ( $this->rms_is_enabled() ) {
			?>

<!-- Responsive Select CSS 
================================================================ -->
<style type="text/css" id="responsive-select-css">
.responsiveSelectContainer select.responsiveMenuSelect, select.responsiveMenuSelect{
	display:none;
}

@media (max-width: <?php echo $this->settings->op( 'max-menu-width' ); ?>px) {
	.responsiveSelectContainer{
		border:none !important;
		background:none !important;
		box-shadow:none !important;
		height:auto !important;
		max-height:none !important;
		visibility:visible !important;
	}
	.responsiveSelectContainer ul, ul.responsiveSelectFullMenu, #megaMenu ul.megaMenu.responsiveSelectFullMenu{
		display: none !important;
	}
	.responsiveSelectContainer select.responsiveMenuSelect, select.responsiveMenuSelect { 
		display: inline-block; 
		width: auto;
		z-index: 99;
		position: relative;
	}
	#et-top-navigation select.responsiveMenuSelect {
		margin-bottom: 20px;
	}
}	
</style>
<!-- end Responsive Select CSS -->

<!-- Responsive Select JS
================================================================ -->
<script type="text/javascript">
jQuery(document).ready( function($){
	$( '.responsiveMenuSelect' ).change(function() {
		var loc = $(this).find( 'option:selected' ).val();
		if( loc != '' && loc != '#' ) window.location = loc;
	});

	updateResponsiveSelect();
	$(window).resize(function() {
		updateResponsiveSelect();
	});
	function updateResponsiveSelect() {
		var maxWidth = <?php echo $this->settings->op( 'max-menu-width' ); ?>;
		var $containerWidth = $(window).width();
		if ($containerWidth <= maxWidth) {
			$('select.responsiveMenuSelect').removeAttr("aria-hidden");
		}
		if ($containerWidth > maxWidth) {
			$('select.responsiveMenuSelect').attr("aria-hidden", "true");
		}
	}
});

</script>
<!-- end Responsive Select JS -->
		
			<?php
		}
	}


	public function rms_add_filter( $args ) {

		if ( $this->rms_is_enabled() && $this->rms_is_activated( $args ) ) {

			// Don't add it twice (when it gets called again by select_nav_menu() ).
			if ( isset( $args['responsiveMenuSelect'] ) && $args['responsiveMenuSelect'] == true ) {
				return $args;
			}

			$select_nav = $this->select_nav_menu( $args );

			$args['container_class'] .= ( $args['container_class'] == '' ? '' : ' ' ) . 'responsiveSelectContainer';
			$args['menu_class']      .= ( $args['menu_class'] == '' ? '' : ' ' ) . 'responsiveSelectFullMenu';

			// This line would add a container if it doesn't exist, but has the potential to break certain theme menus
			// if( $args['container'] != 'nav' ) $args['container'] = 'div'; //make sure there's a container to add class to

			$args['items_wrap'] = '<ul id="%1$s" class="%2$s">%3$s</ul>' . $select_nav;

		}

		return $args;

	}

	function select_nav_menu( $args ) {
		$args['responsiveMenuSelect'] = true;
		$select                       = wp_nav_menu( $args );
		return $select;
	}

	function rms_filter( $args ) {

		if ( $this->rms_is_enabled() ) {

			if ( ! isset( $args['responsiveMenuSelect'] ) ) {
				return $args;
			}

			$item_name = $this->settings->op( 'first_item' );
			$selected  = $this->settings->op( 'current_selected' ) ? '' : 'selected="selected"';
			$first_op  = '<option value="" ' . $selected . '>' . apply_filters( 'rsm_first_item_text', $item_name, $args ) . '</option>';

			$args['container']  = false;
			$args['menu_class'] = 'responsiveMenuSelect';
			$args['menu_id']    = '';
			$args['walker']     = new RMS_Walker();
			$args['echo']       = false;
			$args['items_wrap'] = '<select class="%2$s" title="' . esc_attr__( 'Mobile Menu', 'rms' ) . '" aria-hidden="true">' . $first_op . '%3$s</select>';

			$args['depth'] = $this->settings->op( 'max-menu-depth' );

		}

		return $args;

	}

	/**
	 * Create the Options Panel and Settings object
	 */
	public function rms_options_menu() {

		$rms_ops = new RMS_Options_Panel(
			RMS_SETTINGS,
			// Menu Page.
			array(
				'parent_slug' => 'themes.php',
				'page_title'  => __( 'Responsive Mobile Select Menu', 'rms' ),
				'menu_title'  => __( 'Responsive Menu', 'rms' ),
				'menu_slug'   => 'responsive-mobile-select',
			),
			// Links.
			array()
		);

		/*
		 * Basic Config Panel
		 */
		$basic = 'basic-config';
		$rms_ops->register_panel( $basic, __( 'General Settings', 'rms' ) );

		$rms_ops->add_hidden( $basic, 'current-panel-id', $basic );

		$rms_ops->add_text_input(
			$basic,
			'max-menu-width',
			__( 'Maximum Menu Width', 'rms' ),
			__( 'Show the select box when the viewport is less than this width. This field accepts number input only.', 'rms' ),
			960,
			'spark-minitext',
			'px',
			'[0-9]*'
		);

		$rms_ops->add_text_input(
			$basic,
			'max-menu-depth',
			esc_attr__( 'Menu Depth Limit', 'rms' ),
			esc_attr__( 'The maximum number of levels of menu items to include in the select menu. Set to 0 for no limit. This fields accepts number input only.', 'rms' ),
			0,
			'spark-minitext',
			'',
			'[0-9]*'
		);

		$rms_ops->add_text_input(
			$basic,
			'spacer',
			esc_attr__( 'Sub Item Spacer', 'rms' ),
			esc_attr__( 'The character to use to indent sub items.', 'rms' ),
			'&ndash; ',
			'spark-minitext',
			'',
			''
		);

		$rms_ops->add_checkbox(
			$basic,
			'exclude-hashes',
			esc_attr__( 'Exclude Items Without Links', 'rms' ),
			__( 'Exclude any items where the URL is set to "#" or blank', 'rms' ),
			'on'
		);

		$rms_ops->add_text_input(
			$basic,
			'first_item',
			esc_attr__( 'First Item Name', 'rms' ),
			__( 'Text to display for the first "dummy" item.', 'rms' ),
			__( '&rArr; Navigate', 'rms' ),
			'',
			'',
			''
		);

		$rms_ops->add_checkbox(
			$basic,
			'current_selected',
			esc_attr__( 'Show currently selected item', 'rms' ),
			__( 'Enable to show the currently selected item, rather than the first "dummy" item, when the page loads.', 'rms' ),
			'off'
		);

		$rms_ops->add_sub_header(
			$basic,
			'activate_theme_locations_header',
			esc_attr__( 'Activate Theme Locations', 'rms' )
		);

		$rms_ops->add_checkbox(
			$basic,
			'activate_theme_locations_all',
			esc_attr__( 'Activate All Theme Locations', 'rms' ),
			esc_attr__( 'Apply the responsive select menu to all menus', 'rms' ),
			'on'
		);

		$rms_ops->add_checklist(
			$basic,
			'active_theme_locations',
			esc_attr__( 'Selectively Activate Theme Locations', 'rms' ),
			__( 'Disable the above and activate only the theme locations you want.  These theme locations correspond to the Theme Locations Meta Box in Appearance > Menus', 'rms' ),
			'get_registered_nav_menus'
		);

		$advanced = 'advanced-config';
		$rms_ops->register_panel( $advanced, __( 'Advanced Settings', 'rms' ) );

		$rms_ops->add_text_input(
			$advanced,
			'display_only',
			esc_attr__( 'Enable only on', 'rms' ),
			esc_attr__( 'IDs of pages to enable responsive select menu on.  Other pages will use the standard theme menu.  Enter as a comma-separated list.', 'rms' ),
			'',
			'',
			'',
			''
		);

		return $rms_ops;
	}


	function get_settings() {
		return $this->settings;
	}
}
