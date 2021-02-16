<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ancdretaeixample.cat//sw/info/local-weather/index.php
 * @since             1.0.0
 * @package           Local_Weather
 *
 * @wordpress-plugin
 * Plugin Name:       Local Weather
 * Plugin URI:        https://ancdretaeixample.cat//sw/info/local-weather/index.php
 * Description:       Display local weather info using a shortcode. API key for OpenWeatherMap and local data should be provided.
 * Version:           1.0.1
 * Author:            Antoni Mas
 * Author URI:        https://ancdretaeixample.cat/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       local-weather
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LOCAL_WEATHER_VERSION', '1.0.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-local-weather-activator.php
 */
function activate_local_weather() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-local-weather-activator.php';
	Local_Weather_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-local-weather-deactivator.php
 */
function deactivate_local_weather() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-local-weather-deactivator.php';
	Local_Weather_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_local_weather' );
register_deactivation_hook( __FILE__, 'deactivate_local_weather' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-local-weather.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_local_weather() {

	$plugin = new Local_Weather();
	$plugin->run();

}
run_local_weather();
