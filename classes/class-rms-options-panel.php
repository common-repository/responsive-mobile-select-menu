<?php
/**
 * Options Panel Framework
 *
 * Credits Chris Mavricos, SevenSpark
 *
 * Version 1.0.1
 */
class RMS_Options_Panel {

	public $id;
	public $title;
	public $menu_page;
	public $menu_type;
	public $parent_slug;
	public $page_title;
	public $menu_title;
	public $capability;
	public $menu_slug;

	public $panels;     // array.
	public $ops;        // array.


	public $settings;
	public $updated;
	public $options_key;

	public $notification;
	public $warning;

	public $config;

	public $tour;


	public function __construct( $id, $config = array(), $links = array() ) {

		$this->id     = $id;
		$this->config = $config;

		if ( is_admin() ) {
			// $this->initialize_menu_page( $id, $config );
			add_action( 'admin_menu', array( $this, 'update_settings' ), 100 );
			add_action( 'admin_menu', array( $this, 'initialize_menu_page' ), 101 );

		}

		$this->panels = array();
		$this->ops    = array();

		$this->options_key = self::generate_options_key( $this->id );

		$this->links = $links;

	}

	public function initialize_menu_page() {

		extract(
			wp_parse_args(
				$this->config,
				array(

					'type'        => 'submenu_page',
					'parent_slug' => 'options-general.php',
					'page_title'  => __( 'Options', 'rms' ),
					'menu_title'  => __( 'Options', 'rms' ),
					'capability'  => 'manage_options',
					'menu_slug'   => $this->id,

				)
			)
		);

		$this->title       = $menu_title;
		$this->menu_type   = $type;
		$this->parent_slug = $parent_slug;
		$this->page_title  = $page_title;
		$this->menu_title  = $menu_title;
		$this->menu_slug   = $menu_slug;
		$this->capability  = $capability;

		switch ( $this->menu_type ) {

			case 'submenu_page':
				$this->menu_page = add_submenu_page(
					$this->parent_slug,
					$this->page_title, // 'rmsoptions'.
					$this->menu_title, // 'rmsoptions'.
					$this->capability,
					$this->menu_slug,
					array( $this, 'show' )
				);

				break;
		}
		$this->load_assets();
	}

	function load_assets() {
		add_action( "admin_print_styles-{$this->menu_page}", array( $this, 'load_css' ) );
		add_action( "admin_print_styles-{$this->menu_page}", array( $this, 'load_js' ) );
	}
	function load_css() {
		global $rms_panel_css;
		wp_enqueue_style( 'rmsoptions-css', $rms_panel_css, false, '1.0', 'all' );
		do_action( 'rmsoptions_load_css_' . $this->id );

	}
	function load_js() {
		global $rms_panel_js;
		wp_enqueue_script( 'jquery' );  // Load jQuery.
		wp_enqueue_script( 'rmsoptions-js', $rms_panel_js, false, '1.0', 'all' );
		do_action( 'rmsoptions_load_js_' . $this->id );
	}


	function show() {

		if ( ! current_user_can( $this->capability ) ) {
			wp_die( esc_attr_e( 'You do not have sufficient permissions to access this page.', 'rms' ) );
		}

		?>
<div class="wrap">
	<h1></h1>
		<div class="wrap spark-controlPanel">
			<?php do_action( 'rmsoptions_before_settings_panel_' . $this->id ); ?>
			<div class="spark-settings-panel">
				<div class="spark-nav">			
					<h2><?php esc_attr_e( $this->title, 'rms' ); ?></h2>
					<h5><?php esc_attr_e( 'Control Panel', 'rms' ); ?></h5>			
					<ul>
					<?php
					foreach ( $this->panels as $panel_id => $config ) {
						?>
						<li><a href="#spark-<?php echo esc_attr( $panel_id ); ?>"><?php esc_attr_e( $config['name'], 'rms' ); ?></a></li>
							<?php
					}
					?>
					</ul>			
					<br/>
					<div class="spark-nav-footer">					
						<?php
						foreach ( $this->links as $l ) :
							?>
													
							<a href="<?php echo esc_attr( $l['href'] ); ?>" 
								class="<?php echo esc_attr( $l['class'] ); ?>" 
								title="<?php echo esc_attr( $l['title'] ); ?>"
								target="_blank" ><?php esc_attr_e( $l['text'], 'rms' ); ?></a>
							<?php
							endforeach;
						if ( $this->tour ) {
							echo esc_attr( $this->tour->resetTourButton() );
						}
						?>
						<a href="https://www.saskialund.de/" rel="nofollow" target="_blank"><?php esc_attr_e( 'by ', 'rms' ); ?>Saskia Lund</a>
					</div>
				</div>
				<div class="spark-panels">
					<form method="post" id="spark-options">
						<?php

						$class       = '';
						$start_panel = isset( $this->settings['current-panel-id'] ) ? $this->op( 'current-panel-id' ) : 'basic-config';

						foreach ( $this->panels as $panel_id => $config ) {
							?>
						<div id="spark-<?php esc_attr_e( $panel_id ); ?>" class="spark-panel">
								<?php if ( $this->notification && $panel_id == $start_panel ) : ?>
									<div class="spark-infobox"><?php esc_attr_e( $this->notification, 'rms' ); ?></div>					
								<?php endif; ?>	
								<?php if ( $this->warning && $panel_id == $start_panel ) : ?>
									<br/>
									<div class="spark-infobox spark-infobox-warning"><?php esc_attr_e( $this->warning, 'rms' ); ?></div>					
								<?php endif; ?>
								<h3><?php esc_attr_e( $config['name'], 'rms' ); ?></h3>
								<?php
								$reset = false;
								foreach ( $config['ops'] as $id ) {

									$op = $this->ops[ $id ];

									if ( $op['type'] == 'reset' ) {
										$reset = array(
											'id' => $id,
											'op' => $op,
										);
										continue;
									}

									if ( $op['type'] == 'header-2' ) {
										$class = 'wpmega-config-section'; // TODO
									}
									echo $this->show_admin_option( $id, $op, $class );
									if ( $op['type'] == 'header-2' ) {
										$class = 'sub-container sub-container-' . $id;
									}
								}
								?>
								<input type="submit" name="<?php echo esc_attr( $this->id ); ?>-rmsops_submit" value="<?php esc_attr_e( 'Save All Settings', 'rms' ); ?>" class="button save-button"/>

								<?php
								if ( $reset ) {
									echo $this->show_admin_option( $reset['id'], $reset['op'], '' );
								}
								?>
							</div>
							<?php
						}

						wp_nonce_field( $this->options_key, '_rmsoptions-nonce' );
						?>
					</form>
				</div> 
			</div> <!-- end spark-settings-panel -->
		</div> <!-- end spark-controlPanel -->	
		</div> <!-- end wp wrap -->	
		<?php

	}

	public function show_admin_option( $id, $config, $class = '' ) {

		extract(
			wp_parse_args(
				$config,
				array(
					'title'         => '',
					'type'          => 'text',
					'desc'          => '',
					'units'         => '',
					'pattern'       => '',
					'ops'           => null,
					'default'       => '',
					'special_class' => '',
					'gradient'      => false,
					'default_all'   => 'off',
				)
			)
		);

		$settings = $this->get_settings();

		$class .= ' ' . $special_class;

		$html = '<div id="container-' . $id . '" class="spark-admin-op container-type-' . $type . ' ' . $class . '">';
		if ( ! empty( $before ) ) {
			$html .= $before;
		}

		$val = isset( $settings[ $id ] ) ? $settings[ $id ] : '';
		if ( ! is_numeric( $val ) && empty( $val ) ) {
			$val = $default;       // must check numeric otherwise we can't use 0.
		}

		$title   = '<label class="spark-admin-op-title" for="' . $id . '">' . esc_attr__( $title, 'rms' ) . '</label>';
		$desc    = empty( $desc ) ? '' : '<span class="spark-admin-op-desc">' . esc_attr__( $desc, 'rms' ) . '</span>';
		$units   = '<span class="spark-admin-op-units">' . esc_attr__( $units, 'rms' ) . '</span>';
		$pattern = empty( $pattern ) ? '' : ' pattern="' . esc_attr__( $pattern ) . '"';

		switch ( $type ) {

			case 'text':
				$html .= $title;
				$html .= '<input type="text" id="' . $id . '" name="' . $id . '" value="' . stripslashes( $val ) . '"' . $pattern . '/>';
				$html .= $units;
				$html .= $desc;

				break;

			case 'checkbox':
				if ( empty( $val ) ) {
					$ischecked = $default == 'on' ? true : false;
				} else {
					$ischecked = $val == 'on' ? true : false;
				}

				$html .= $title;
				$html .= '<input type="checkbox" id="' . $id . '" name="' . $id . '" ' . checked( $ischecked, true, false ) . '/>';
				$html .= $desc;
				$html .= '<div class="clear"></div>';

				break;

			case 'checklist':
				$html .= '<label class="spark-admin-op-title">' . esc_attr__( $config['title'], 'rms' ) . '</label>'; // $title;
				$html .= $desc;
				$html .= '<div class="spark-admin-checklist">';

				if ( ! is_array( $ops ) ) {
					$ops = $ops();  // if it's not an array it's a function that produces an array.
				}

				if ( is_array( $ops ) ) {

					$val   = '';
					$multi = false;

					if ( isset( $settings[ $id ] ) ) {
						$val = $settings[ $id ];
						if ( is_array( $val ) ) {
							$multi = true;
						}
					}

					$k = 0;
					foreach ( $ops as $op_val => $op ) {

						$checked = '';
						if ( $multi ) {
							$checked = in_array( $op_val, $val ) ? 'checked="checked"' : '';
						} else {
							$checked = $op_val == $val ? 'checked="checked"' : '';
						}
						$input_id = $id . '-' . $k;
						$html    .= '<label class="spark-admin-op-title" for="' . $input_id . '">' . esc_attr__( $op ) . '</label> <input type="checkbox" value="' . $op_val . '" ' . $checked . ' name="' . $id . '[]" id="' . $input_id . '" />';
						$k++;
					}
				}

				$html .= '</div>';

				break;

			case 'header':
				$html .= '<h3>' . $title . '</h3>';
				break;

			case 'header-2':
				$html .= '<h4>' . $title . '</h4>';
				break;

			case 'infobox':
				$html .= '<div class="spark-infobox ' . $special_class . '">';
				if ( ! empty( $config['title'] ) ) {
					$html .= '<h4>' . $title . '</h4>';
				}
				$html .= $desc . '</div>';

				break;

			case 'hidden':
				$html .= '<input type="hidden" id="' . $id . '" name="' . $id . '" value="' . stripslashes( $val ) . '"/>';
				break;

		}
		$html .= '</div>';

		return $html;
	}



	/* INPUT TYPES */

	/**
	 * Add a text input
	 *
	 * @param string $panel_id the ID of the panel to add the option to.
	 * @param int    $id String the ID of this option.
	 * @param string $title String the label for the text input.
	 * @param string $desc String the description of the option.
	 * @param string $special_class the class to add to the dialog, like 'spark-infobox-warning'.
	 */
	function add_text_input( $panel_id, $id, $title, $desc = '', $default = '', $special_class = '', $units = '', $pattern = '' ) {

		$this->ops[ $id ] = array(
			'title'         => $title,
			'desc'          => $desc,
			'default'       => $default,
			'special_class' => $special_class,
			'units'         => $units,
			'pattern'       => $pattern,
			'type'          => 'text',
		);

		$this->add_to_panel( $panel_id, $id );
	}

	function add_checkbox( $panel_id, $id, $title, $desc = '', $default = 'off', $special_class = '' ) {

		$this->ops[ $id ] = array(
			'title'         => $title,
			'desc'          => $desc,
			'default'       => $default,
			'special_class' => $special_class,
			'type'          => 'checkbox',
		);

		$this->add_to_panel( $panel_id, $id );

	}

	function add_checklist( $panel_id, $id, $title, $desc = '', $ops = array(), $default = '', $default_all = 'off', $special_class = '' ) {
		$this->ops[ $id ] = array(
			'title'         => $title,
			'desc'          => $desc,
			'ops'           => $ops,
			'default'       => $default,
			'default_all'   => $default_all,
			'special_class' => $special_class,
			'type'          => 'checklist',
		);

		$this->add_to_panel( $panel_id, $id );
	}

	function add_sub_header( $panel_id, $id, $title, $desc = '', $special_class = '' ) {

		$this->ops[ $id ] = array(
			'title'         => $title,
			'desc'          => $desc,
			'special_class' => $special_class,
			'type'          => 'header-2',
		);

		$this->add_to_panel( $panel_id, $id );
	}

	/**
	 * Add an information box
	 *
	 * @param string $panel_id  - the ID of the panel to add the option to.
	 * @param string $id - the ID of this option.
	 * @param string $title string - the title of the dialog.
	 * @param string $desc string - the text of the dialog.
	 * @param string $special_class - the class to add to the dialog, like 'spark-infobox-warning'.
	 */
	function add_infobox( $panel_id, $id, $title, $desc = '', $special_class = '' ) {

		$this->ops[ $id ] = array(
			'title'         => $title,
			'desc'          => $desc,
			'special_class' => $special_class,
			'type'          => 'infobox',
		);

		$this->add_to_panel( $panel_id, $id );

	}

	function add_hidden( $panel_id, $id, $value ) {

		$this->ops[ $id ] = array(
			'type'    => 'hidden',
			'default' => $value,
		);

		$this->add_to_panel( $panel_id, $id );
	}


	function add_to_panel( $panel_id, $option_id ) {

		if ( ! isset( $this->panels[ $panel_id ] ) ) {
			return;
		}

		$this->panels[ $panel_id ]['ops'][] = $option_id;

	}

	function register_panel( $panel_id, $name ) {

		$this->panels[ $panel_id ]         = array();
		$this->panels[ $panel_id ]['name'] = $name;
		$this->panels[ $panel_id ]['ops']  = array();

	}

	function get_settings() {

		if ( ! $this->settings ) {
			$this->settings = get_option( $this->options_key );
			$this->settings = apply_filters( $this->id . '_settings_filter', $this->settings );
		}

		return $this->settings;
	}

	function op( $id ) {

		$this->get_settings();

		// return the value or the default.
		$val;
		if ( isset( $this->settings[ $id ] ) ) {
			$val = $this->settings[ $id ];
		} elseif ( isset( $this->ops[ $id ]['default'] ) ) {
			$val = $this->ops[ $id ]['default'];
		}
		// this option doesn't exist, or doesn't have a default.
		else {
			return '';
		}

		// translate to true/false for checkboxes.

		switch ( $this->ops[ $id ]['type'] ) {
			case 'checkbox':
				return 'on' === $val ? true : false;
				break;

			case 'hidden':
				if ( 'on' === $val ) {
					return true;
				} elseif ( 'off' === $val ) {
					return false;
				}
				return $val;

			case 'text':
				return esc_textarea( stripslashes( $val ) );
				break;
		}

		return $val;

	}


	public function update_settings() {
		// Only do this on form submission.
		if ( ! isset( $_POST[ $this->id . '-rmsops_submit' ] ) ) {
			return false;
		}

		if ( ! check_admin_referer( $this->options_key, '_rmsoptions-nonce' ) ) {
			// Can't ever actually reach here, as function will die above if nonce is invalid.
			die( esc_attr_e( 'No can dosville, baby', 'rms' ) );
			return false;
		}

		// go through settings, if checkbox and not set, set to 'off'.
		$save_ops = array();

		foreach ( $this->ops as $key => $o ) {
			if ( 'active_theme_locations' === $key && isset( $_POST[ $key ][0] ) ) {
				$val = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) );
			} elseif ( ! isset( $_POST[ $key ] ) && ! isset( $_POST[ $key ][0] ) ) {
				$val = '';
			} else {
				$val = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
			}

			switch ( $o['type'] ) {
				case 'checkbox':
					if ( empty( $val ) ) {
						$val = 'off'; // empty($o['default']) ? 'off' : $o['default']; Don't set to default or we can never have 'off'.
					}
					break;
				case 'text':
					$val = $val;
					break;
				case 'header':
				case 'header-2':
				case 'infobox':
					continue 2;
					break;
			}
			$save_ops[ $key ] = $val;

		}

		$this->settings = $save_ops;     // setup new settings, in case get_settings() has already been run.

		// Give the plugin a go to do something extra special.
		do_action( 'rmsoptions_update_settings_' . $this->id, $save_ops );

		// Here is where we actually update all the Settings.
		update_option( $this->options_key, $this->settings );

		// Notify user of great success!
		$this->notification = __( 'Settings saved!', 'rms' );

		return true;

	}

	public static function generate_options_key( $id ) {
		return 'rmsops_' . $id;
	}

	public function add_tour( $tour ) {
		$this->tour = $tour;
	}

}
