<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://ancdretaeixample.cat//sw/info/local-weather/index.php
 * @since      1.0.0
 *
 * @package    Local_Weather
 * @subpackage Local_Weather/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Local_Weather
 * @subpackage Local_Weather/includes
 * @author     Antoni Mas <amrbass@gmail.com>
 */
class Local_Weather_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'local-weather',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
