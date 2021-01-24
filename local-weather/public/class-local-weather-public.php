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

		//$units = ['standard','metric','imperial'];	//OWM API units allowed
		// get values from settings as default ones
		$country = get_option($this->lwd_keys[LWD_COUNTRY]);
		$zipcode = get_option($this->lwd_keys[LWD_ZIPCODE]);
		$units = get_option($this->lwd_keys[LWD_UNITS]);

		if($content != "")	{	//if it's not a self-closing shortcode
			$title = $content;
			$o = '<h2 class="lwd_title">'.$title.'</h2>';
		}	else{
			$o = '';
		}

		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// override default attributes with user attributes
		$lwd_atts = shortcode_atts(
			array(
				'country' => $country,
				'zipcode' => $zipcode,
				'units' => $units,
			), $atts, $tag
		);
		
		if(array_key_exists(strtolower($lwd_atts['country']), $this->lwd_countries))	{
			$country_name = $this->lwd_countries[strtolower($lwd_atts['country'])];
		} else {
			$country_name = 'bad country code';
		}

		//access OpenWeatherMap data
		$apiKey = get_option($this->lwd_keys[LWD_APIKEY]);
		$lang = get_locale();
		$OWMurl = "http://api.openweathermap.org/data/2.5/weather";
		$googleApiUrl = $OWMurl."?zip=".$lwd_atts['zipcode'].",".$lwd_atts['country']."&lang=".$lang."&units=".$lwd_atts['units']."&APPID=$apiKey";
		$data = $this->lwd_getWeather($googleApiUrl);

		//handle { "cod": "404", "message": "city not found" } or similar
		if ($data->cod > 400) {	//error
			$o .= '<h4 style="color:red;">'.$data->message.' for ZIP code '.$lwd_atts["zipcode"].' in '.$country_name.'</h4>';
		}	else	{
			
			//setlocale(LC_TIME,"ca_ES");
			setlocale(LC_TIME, "ca_ES");
			$currentTime = strftime("%c", $data->dt);
	
			if(property_exists($data->wind, 'gust')) {
			  $wind = $this->lwd_ms_to_Beaufort($data->wind->speed)." ". round($data->wind->speed)."-".round($data->wind->gust)." m/s ";
			}	else {
			  $wind = $this->lwd_ms_to_Beaufort($data->wind->speed)." ".round($data->wind->speed) . " m/s ";
			}
			$windir = "🧭".$this->lwd_degrees_to_windir($data->wind->deg);
			$sunrise = "🌞 ".strftime("%H:%M", $data->sys->sunrise);
			$sunset = "🌜 ".strftime("%H:%M", $data->sys->sunset);

			$o .= '<table class="lwd_table" style="">';
			//special case ||*||
			if (strtolower($lwd_atts['country']) == 'es') {
				$o .= '<caption style="font-size:120%;text-align:center;text-decoration:solid;margin:10px;padding:0px 10px 0px 10px;">El temps ara a '.$data->name.'<br />'.$currentTime.'</caption>';
			}	else	{
				$o .= '<caption style="font-size:120%;text-align:center;text-decoration:solid;margin:10px;padding:0px 10px 0px 10px;">Weather now in '.$data->name.' ('.$country_name.')<br />'.$currentTime.'</caption>';
			}
			$o .= '<tr class="lwd_row2" style="background-color: lightgray;border: 0px solid black;margin:0px;padding:0px;"><td class="lwd_cell2"><img src="http://openweathermap.org/img/w/'.$data->weather[0]->icon.'.png"/></td><td class="lwd_cell2">'.ucwords($data->weather[0]->description).'</td></tr>';
			$o .= '<tr><td class="lwd_cell">Temperatura: '.round($data->main->temp).' ºC</td><td class="lwd_cell">(sensació de '.round($data->main->feels_like).' ªC)</td></tr>';
			$o .= '<tr><td class="lwd_cell">Humitat: '.round($data->main->humidity).' %</td><td class="lwd_cell">Pressió: '.round($data->main->pressure).' hPa</td></tr>';
			$o .= '<tr><td class="lwd_cell">'.$wind.'</td><td class="lwd_cell">'.$windir.'</td></tr>';
			$o .= '<tr><td class="lwd_cell">Nuvolositat: '.$data->clouds->all.' %</td><td class="lwd_cell">Visibilitat: '.$data->visibility.' m</td></tr>';
			$o .= '<tr><td class="lwd_cell">'.$sunrise.'</td><td class="lwd_cell">'.$sunset.'</td></tr>';
			$o .= '</table>';

			//only for debur purpose. Comment this line when done
			//$o .= '<div style="text-align: left;color:white;padding:10px;background-color:black;margin:10px 0px 10px 0px;padding:10px;overflow:hidden;"><pre><code>'.json_encode($data, JSON_PRETTY_PRINT).'</code></pre></div>';

		}

		return $o;
	}

	/**
	 * cURL request to get OWM data.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @param	string	$url	The complete url to access.
	 * @return	string	An object with all data provided by OWM.
	 */
	private function lwd_getWeather($url)	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response);
	}

	/**
	 * Degrees to win directoin converter.
	 *
	 * @since	1.0.0
	 * @access   private
	 * @param	int		$deg	Degrees value.
	 * @return	string	Wind direction standard name.
	 */
  	private function lwd_degrees_to_windir($deg) {
		$directions = array('N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW', 'N');
		return $directions[(round($deg / 22.5) % 16)];
	}

	/**
	 * Wind speed to Beaufort scale conversion.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	float	$gust	Wind speed in m/s.
	 * @return	string	Beaufort wind type name.
	 */
	private function lwd_ms_to_Beaufort($gust) {
		$desc = '';
		if($gust < 0.3)
			$desc = 'Calma';
		elseif($gust < 1.6)
			$desc = 'Ventolina';
		elseif ($gust < 3.4)
			$desc = 'Vent fluixet';
		elseif ($gust < 5.5)
			$desc = 'Vent fluix';
		elseif ($gust < 8.0)
			$desc = 'Vent moderat';
		elseif ($gust < 10.8)
			$desc = 'Vent fresquet';
		elseif ($gust < 13.9)
			$desc = 'Vent fresc';
		elseif ($gust < 17.2)
			$desc = 'Vent fort';
		elseif ($gust < 20.8)
			$desc = 'Temporal';
		elseif ($gust < 24.5)
			$desc = 'Temporal fort';
		elseif ($gust < 28.5)
			$desc = 'Temporal molt fort';
		elseif ($gust < 32.7)
			$desc = 'Temporal violent';
		elseif ($gust >= 32.7)
			$desc = 'Huracà';
		else
			$desc = 'valor desconegut';
		return("$desc");
	}

}
