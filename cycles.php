<?php
ini_set("upload_max_filesize", "64M");
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://getCycles.io
 * @since             1.0.2
 * @package           Design_Feedback
 *
 * @wordpress-plugin
 * Plugin Name:       Cycles
 * Plugin URI:        http://getCycles.io/#utm_source=wordpress&utm_medium=plugin&utm_campaign=wpdfplugin&utm_content=v01
 * Description:       An easy way to share any design with clients and colleagues for feedback and review.
 * Version:           1.1
 * Author:            Cycles
 * Author URI:        http://getCycles.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cycles
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-design-feedback-activator.php
 */
function activate_design_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-design-feedback-activator.php';
	Design_Feedback_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-design-feedback-deactivator.php
 */
function deactivate_design_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-design-feedback-deactivator.php';
	Design_Feedback_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_design_feedback' );
register_deactivation_hook( __FILE__, 'deactivate_design_feedback' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-design-feedback.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1
 */
function run_design_feedback() {

	$plugin = new Design_Feedback();
	$plugin->run();

}
run_design_feedback();
