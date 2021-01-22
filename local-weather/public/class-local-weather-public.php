<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ancdretaeixample.cat//sw/info/local-weather/index.php
 * @since      1.0.0
 *
 * @package    Local_Weather
 * @subpackage Local_Weather/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Local_Weather
 * @subpackage Local_Weather/public
 * @author     Antoni Mas <amrbass@gmail.com>
 */
class Local_Weather_Public {

	/**
	 * Global data arrays to be used for calls to OWM.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $lwd_countries	Array of countries and codes.
	 * @var      array    $lwd_units		Array of valid units names.
	 * @var      array    $lwd_settings		Array of sample settings, with 'keys' used in admin page.
	 * @var      array    $lwd_keys			Array of key names used in $lwd_settings, the setting names used in admin page.
	 */
	private $lwd_countries;
	private $lwd_units;
	private $lwd_settings;
	private $lwd_keys;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $local_weather    The ID of this plugin.
	 */
	private $local_weather;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $local_weather       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $local_weather, $version ) {

		$this->local_weather = $local_weather;
		$this->version = $version;

		//insert global data variables for further access
		require plugin_dir_path( dirname( __FILE__ ) ) . 'data/local-weather-countries.php';
		$this->lwd_countries = $lwd_countries;
		require plugin_dir_path( dirname( __FILE__ ) ) . 'data/local-weather-units.php';
		$this->lwd_units = $lwd_units;
		require plugin_dir_path( dirname( __FILE__ ) ) . 'data/local-weather-settings.php';
		$this->lwd_settings = $lwd_settings;
		$this->lwd_keys = array_keys($lwd_settings);

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Local_Weather_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Local_Weather_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->local_weather, plugin_dir_url( __FILE__ ) . 'css/local-weather-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Local_Weather_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Local_Weather_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->local_weather, plugin_dir_url( __FILE__ ) . 'js/local-weather-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Handler for [lwd-local-weather] shortcode.
	 * This function renders the provided shortcode options into HTML.
	 *
	 * @since    1.0.0
	 * @param    array	$atts		[$tag] attributes
	 * @param    string	$content	post content
	 * @param    string	$tag		the name of the [$tag] (i.e. the name of the shortcode)
	 */
	public function lwd_local_weather_handler( $atts = array(), $content = null, $tag = '' ) {

		$units = ['standard','metric','imperial'];	//OWM API units allowed

		$o = "Local Weather plugin";
		if($content == "")	{	//only for self-closing shortcode
			$title = '<h2>Local Weather data</h2>';
		}	else	{
			$title = $content;
		}
		$o = '<h2 class="lwd_title">'.$title.'</h2>';

		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// override default attributes with user attributes
		$lwd_atts = shortcode_atts(
			array(
				'country' => 'es',
				'zipcode' => '08009',
				'units' => $units[1],
			), $atts, $tag
		);

		$o .= '<table class="lwd_table" style="width:60%;">';
		$o .= '<caption>API Key used: '.get_option($this->lwd_keys[LWD_APIKEY]).'</caption>';
		$o .= '<tr><td class="lwd_cell">Country:</td><td class="lwd_cell">'.$lwd_atts['country'].' ('.$this->lwd_countries[strtolower($lwd_atts['country'])].')</td></tr>';
		$o .= '<tr><td class="lwd_cell">ZIP Code:</td><td class="lwd_cell">'.$lwd_atts['zipcode'].'</td></tr>';
		$o .= '<tr><td class="lwd_cell">Units:</td><td class="lwd_cell">'.$lwd_atts['units'].'</td></tr>';
		$o .= '<tr><td class="lwd_cell">Language:</td><td class="lwd_cell">'.get_locale().'</td></tr>';
		$o .= '</table>';

		return $o;
	}

}
