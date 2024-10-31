<?php
/**
 * Plugin Name: Responsive Mobile Select Menu
 * Plugin URI: https://www.saskialund.de/responsive-mobile-select-menu/
 * Description: Turn your menu into a native browser-UI select box at small viewport sizes
 * Version: 1.1.4
 * Author: Saskia Teichmann
 * Author URI: https://www.saskialund.de
 * Text Domain: rms
 * Domain Path: /languages/
 * Contributors: Jyria
 *
 * @package RMS_MENU\Main
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 or higher
 * @copyright 2024 Saskia Teichmann, Twitter @SaskiaLund
 */

define( 'RMS_VERSION', '1.1.4' );
define( 'RMS_SETTINGS', 'responsive-mobile-select-menu' );

if ( ! defined( 'RMS_FILE' ) ) {
	define( 'RMS_FILE', __FILE__ );
}

$rms_dir = plugin_dir_path( RMS_FILE );
$rms_uri = plugin_dir_url( RMS_FILE );
require_once $rms_dir . '/classes/class-rms-options-panel.php';      // Options Panel.
require_once $rms_dir . '/classes/class-rms-main.php';      // Options Panel.
$rms_panel_css = $rms_uri . 'assets/css/rms-options.css';
$rms_panel_js  = $rms_uri . 'assets/js/rms-options.js';
$rms_main      = new RMS_Main();

require_once $rms_dir . '/classes/class-rms-walker.php';

/**
 * Use textdomain for translation
 */
function rms_init() {
	load_plugin_textdomain( 'rms', false, 'responsive-mobile-select-menu/languages' );
}
add_action( 'init', 'rms_init' );
