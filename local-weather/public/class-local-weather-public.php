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
	 * Data to be used for calls to OWM air quality API.
	 *
	 * @since    1.0.3
	 * @access   private
	 * @var      float    $lon		longitude coordinate of selected place.
	 * @var      float    $lat		latitude coordinate of selected place.
	 * @var      array    $limits	limit values for in-segment CAQI index calculation.
	 */
	private $lon = 2.153833;
	private $lat = 41.385333;
	private $limits = ['Clow' => 0, 'Chigh' => 1, 'Ilow' => 0, 'Ihigh' => 1];

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

		$val_units = [
			'standard' => ['temp' => 'ºK', 'wind' => 'm/s'],
			'metric' => ['temp' => 'ºC', 'wind' => 'm/s'],
			'imperial' => ['temp' => 'ºF', 'wind' => 'mph']
		];

		//$units = ['standard','metric','imperial'];	//OWM API units allowed
		// get values from settings as default ones
		$country = get_option($this->lwd_keys[LWD_COUNTRY]);
		$zipcode = get_option($this->lwd_keys[LWD_ZIPCODE]);
		$units = get_option($this->lwd_keys[LWD_UNITS]);
		$apiKey = get_option($this->lwd_keys[LWD_APIKEY]);	//access OpenWeatherMap data
		

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
		
		//determine units to be used for values get from OWM. If unknown units set, select 'metric' units
		if(!array_key_exists(strtolower($lwd_atts['units']), $val_units))
			$lwd_atts['units'] = 'metric';
		$tunits = $val_units[$lwd_atts['units']]['temp'];
		$wunits = $val_units[$lwd_atts['units']]['wind'];

		if(array_key_exists(strtolower($lwd_atts['country']), $this->lwd_countries))	{
			$country_name = $this->lwd_countries[strtolower($lwd_atts['country'])];
		} else {
			$country_name = esc_html__( 'bad country code', 'local-weather' );
		}

		$lang = get_locale();
		//$currentLocale = setlocale(LC_CTYPE, 0);
		//$o .= "<p>get_locale: ".$lang." setlocale: ".$currentLocale."</p>";
		$OWMurl = "http://api.openweathermap.org/data/2.5/weather";
		$googleApiUrl = $OWMurl."?zip=".$lwd_atts['zipcode'].",".$lwd_atts['country']."&lang=".$lang."&units=".$lwd_atts['units']."&APPID=$apiKey";
		$data = $this->lwd_getWeather($googleApiUrl);

		//handle { "cod": "404", "message": "city not found" } or similar
		if ($data->cod > 400) {	//error
			$o .= sprintf( esc_html__( '%1$s Error: %2$s for postal code "%3$s" and country code "%4$s" (%5$s).%6$s', 'local-weather' ),
				'<p style="color:red;">', $data->message, $lwd_atts["zipcode"], $lwd_atts['country'], $country_name, '</p>');
		}	else	{

			//get air quality data also from OWM

			//get coordinates for air quality access
			if(property_exists($data->coord, 'lon'))
				$this->lon = $data->coord->lon;
			if(property_exists($data->coord, 'lat'))
				$this->lat = $data->coord->lat;

			$googleApiUrl = "http://api.openweathermap.org/data/2.5/air_pollution?lat=".$this->lat."&lon=".$this->lon."&APPID=".$apiKey;
			$airq = $this->lwd_getWeather($googleApiUrl);
			$caqi = ['NO2' => 0, 'PM10' => 0, 'O3' => 0, 'PM2.5' => 0];
			if(property_exists($airq, 'list'))	{
				$caqi['NO2'] = $this->calc_index_NO2($airq->list[0]->components->no2);
				$caqi['PM10'] = $this->calc_index_PM10($airq->list[0]->components->pm10);
				$caqi['O3'] = $this->calc_index_O3($airq->list[0]->components->o3);
				$caqi['PM2.5'] = $this->calc_index_PM25($airq->list[0]->components->pm2_5);
				$aqi_msg = $this->eu_air_quality($caqi);
			}

			$currentTime = date_i18n( __('D, M j, Y H:i', 'local-weather' ), $data->dt + $data->timezone);
			//$currentTime = strftime( __("%a %e %B %G %k:%M", 'local-weather' ), $data->dt);
	
			if(property_exists($data->wind, 'gust')) {
			  $wind = $this->lwd_ms_to_Beaufort($data->wind->speed, $lwd_atts['units'])." ". round($data->wind->speed)."-".round($data->wind->gust);
			}	else {
			  $wind = $this->lwd_ms_to_Beaufort($data->wind->speed, $lwd_atts['units'])." ".round($data->wind->speed);
			}
			$windir = "🧭".$this->lwd_degrees_to_windir($data->wind->deg);
			$sunrise = "🌞 ".strftime("%H:%M", $data->sys->sunrise + $data->timezone);
			$sunset = "🌜 ".strftime("%H:%M", $data->sys->sunset + $data->timezone);

			$o .= '<table class="lwd_table" style="">';
			//special case ||*||
			if (strtolower($lwd_atts['country']) == 'es') {
				$o .= sprintf( esc_html__( '%1$sThe weather now in %2$s%3$s%4$s%5$s', 'local-weather' ),
					'<caption style="font-size:120%;text-align:center;text-decoration:solid;margin:10px;padding:0px 10px 0px 10px;">',
					$data->name, '<br />', $currentTime, '</caption>');
			}	else	{
				$o .= sprintf( esc_html__( '%1$sThe weather now in %2$s (%3$s)%4$s%5$s%6$s', 'local-weather' ),
					'<caption style="font-size:120%;text-align:center;text-decoration:solid;margin:10px;padding:0px 10px 0px 10px;">',
					$data->name, $country_name, '<br />', $currentTime, '</caption>');
			}
			$o .= '<tr class="lwd_row2" style="background-color: lightgray;border: 0px solid black;margin:0px;padding:0px;"><td class="lwd_cell2"><img src="http://openweathermap.org/img/w/'.$data->weather[0]->icon.'.png"/></td><td class="lwd_cell2">'.ucwords($data->weather[0]->description).'</td></tr>';
			$o .= sprintf( esc_html__( '%1$sTemperature: %2$s %3$s%4$s(feels like %5$s %3$s)%6$s', 'local-weather' ),
				'<tr><td class="lwd_cell">', round($data->main->temp), $tunits, '</td><td class="lwd_cell">', round($data->main->feels_like), '</td></tr>');
			$o .= sprintf( esc_html__( '%1$sHumidity: %2$s %3$s%4$sPressure: %5$s %6$s%7$s', 'local-weather' ),
				'<tr><td class="lwd_cell">', round($data->main->humidity), '%', '</td><td class="lwd_cell">', round($data->main->pressure), 'hPa', '</td></tr>');
			$o .= '<tr><td class="lwd_cell">'.$wind.' '.$wunits.'</td><td class="lwd_cell">'.$windir.'</td></tr>';
			$o .= sprintf( esc_html__( '%1$sCloudiness: %2$s %3$s%4$sVisibility: %5$s %6$s%7$s', 'local-weather' ),
				'<tr><td class="lwd_cell">', $data->clouds->all, '%', '</td><td class="lwd_cell">', $data->visibility, 'm', '</td></tr>');
			$o .= '<tr><td class="lwd_cell">'.$sunrise.'</td><td class="lwd_cell">'.$sunset.'</td></tr>';
			if (property_exists($airq, 'list')) {
				//$o .= '<tr><td class="lwd_cell">🌻 Qualitat aire:</td><td class="lwd_cell">'.$aqi_msg.'</td></tr>';
				$o .= sprintf( esc_html__( '%1$s🌻 Air quality:%2$s%3$s%4$s', 'local-weather' ),
					'<tr><td class="lwd_cell">', '</td><td class="lwd_cell">', $aqi_msg, '</td></tr>');
			}
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
	 * @param	float	$gust	Wind speed in m/s or mph.
	 * @param	string	$units	current units family for value conversion from 'mph' to 'm/s'.
	 * @return	string	Beaufort wind type name.
	 */
	private function lwd_ms_to_Beaufort($gust, $units = 'metric') {

		if($units == 'imperial')	{
			//convert $gust to m/s
			$gust = $gust * 0.44704;
		}

		$desc = '';
		if($gust < 0.3)
			$desc = esc_html__( 'Calm', 'local-weather' );
		elseif($gust < 1.5)
			$desc = esc_html__( 'Light air', 'local-weather' );
		elseif ($gust < 3.3)
			$desc = esc_html__( 'Light breeze', 'local-weather' );
		elseif ($gust < 5.5)
			$desc = esc_html__( 'Gentle breeze', 'local-weather' );
		elseif ($gust < 7.9)
			$desc = esc_html__( 'Moderate breeze', 'local-weather' );
		elseif ($gust < 10.7)
			$desc = esc_html__( 'Fresh breeze', 'local-weather' );
		elseif ($gust < 13.8)
			$desc = esc_html__( 'Strong breeze', 'local-weather' );
		elseif ($gust < 17.1)
			$desc = esc_html__( 'Moderate gale', 'local-weather' );
		elseif ($gust < 20.7)
			$desc = esc_html__( 'Gale', 'local-weather' );
		elseif ($gust < 24.4)
			$desc = esc_html__( 'Strong gale', 'local-weather' );
		elseif ($gust < 28.4)
			$desc = esc_html__( 'Storm', 'local-weather' );
		elseif ($gust < 32.6)
			$desc = esc_html__( 'Violent storm', 'local-weather' );
		elseif ($gust >= 32.6)
			$desc = esc_html__( 'Hurricane', 'local-weather' );
		else
			$desc = esc_html__( 'unknown value', 'local-weather' );
		return("$desc");
	}

	/**
	 * Build the proper icq message from collected air quality data.
	 *
	 * @since	1.0.3
	 * @access	private
	 * @param	array	$caqi	array of all calculated quality index.
	 * @return	string	final CAQI text message.
	 */
	private function eu_air_quality($caqi)	{
		$index = max($caqi);
		$key = array_search($index, $caqi);
		$desc = $index.'/100 ';
		if($index < 25)
			$desc .= esc_html__( 'VERY GOOD 🌻', 'local-weather' );
		elseif($index < 50)
			$desc .= esc_html__( 'GOOD 😀', 'local-weather' );
		elseif($index < 75)
			$desc .= esc_html__( 'MEDIUM 😐', 'local-weather' );
		elseif($index <= 100)
			$desc .= esc_html__( 'BAD 🙁', 'local-weather' );
		elseif($index > 100)
			$desc = esc_html__( '>100 VERY BAD 🤢', 'local-weather' );
		else
			$desc = esc_html__( 'unknown value', 'local-weather' );
		
		//$this->dlog($desc.' ('.$key.')');
		return($desc.' ('.$key.')');
	}

	/**
	 * Calculate partial NO2 quality index.
	 *
	 * @since	1.0.3
	 * @access	private
	 * @param	float	$NO2	array of all calculated quality index.
	 * @return	float	calculated partial index.
	 */
	private function calc_index_NO2($NO2)	{
		if($NO2 < 50) {
			$this->limits['Ilow'] = 0;
			$this->limits['Ihigh'] = 25;
			$this->limits['Clow'] = 0;
			$this->limits['Chigh'] = 50;
		}	elseif($NO2 < 100)	{
			$this->limits['Ilow'] = 25;
			$this->limits['Ihigh'] = 50;
			$this->limits['Clow'] = 50;
			$this->limits['Chigh'] = 100;
		}	elseif($NO2 < 200)	{
			$this->limits['Ilow'] = 50;
			$this->limits['Ihigh'] = 75;
			$this->limits['Clow'] = 100;
			$this->limits['Chigh'] = 200;
		}	elseif($NO2 <= 400)	{
			$this->limits['Ilow'] = 75;
			$this->limits['Ihigh'] = 100;
			$this->limits['Clow'] = 200;
			$this->limits['Chigh'] = 400;
		}	else	{	// >400
			return 101;
		}
		return $this->idx_calc($NO2);
	}

	/**
	 * Calculate partial PM10 quality index.
	 *
	 * @since	1.0.3
	 * @access	private
	 * @param	float	$PM10	array of all calculated quality index.
	 * @return	float	calculated partial index.
	 */
	private function calc_index_PM10($PM10)	{
		if($PM10 < 25) {
			$this->limits['Ilow'] = 0;
			$this->limits['Ihigh'] = 25;
			$this->limits['Clow'] = 0;
			$this->limits['Chigh'] = 25;
		}	elseif($PM10 < 50)	{
			$this->limits['Ilow'] = 25;
			$this->limits['Ihigh'] = 50;
			$this->limits['Clow'] = 25;
			$this->limits['Chigh'] = 50;
		}	elseif($PM10 < 90)	{
			$this->limits['Ilow'] = 50;
			$this->limits['Ihigh'] = 75;
			$this->limits['Clow'] = 50;
			$this->limits['Chigh'] = 90;
		}	elseif($PM10 <= 180)	{
			$this->limits['Ilow'] = 75;
			$this->limits['Ihigh'] = 100;
			$this->limits['Clow'] = 90;
			$this->limits['Chigh'] = 180;
		}	else	{	// >180
			return 101;
		}
		return $this->idx_calc($PM10);
	}

	/**
	 * Calculate partial O3 quality index.
	 *
	 * @since	1.0.3
	 * @access	private
	 * @param	float	$O3	array of all calculated quality index.
	 * @return	float	calculated partial index.
	 */
	private function calc_index_O3($O3)	{
		if($O3 < 60) {
			$this->limits['Ilow'] = 0;
			$this->limits['Ihigh'] = 25;
			$this->limits['Clow'] = 0;
			$this->limits['Chigh'] = 60;
		}	elseif($O3 < 120)	{
			$this->limits['Ilow'] = 25;
			$this->limits['Ihigh'] = 50;
			$this->limits['Clow'] = 60;
			$this->limits['Chigh'] = 120;
		}	elseif($O3 < 180)	{
			$this->limits['Ilow'] = 50;
			$this->limits['Ihigh'] = 75;
			$this->limits['Clow'] = 120;
			$this->limits['Chigh'] = 180;
		}	elseif($O3 <= 240)	{
			$this->limits['Ilow'] = 75;
			$this->limits['Ihigh'] = 100;
			$this->limits['Clow'] = 180;
			$this->limits['Chigh'] = 240;
		}	else	{	// >240
			return 101;
		}
		return $this->idx_calc($O3);
	}

	/**
	 * Calculate partial PM25 quality index.
	 *
	 * @since	1.0.3
	 * @access	private
	 * @param	float	$PM25	array of all calculated quality index.
	 * @return	float	calculated partial index.
	 */
	private function calc_index_PM25($PM25)	{
		if($PM25 < 15) {
			$this->limits['Ilow'] = 0;
			$this->limits['Ihigh'] = 25;
			$this->limits['Clow'] = 0;
			$this->limits['Chigh'] = 15;
		}	elseif($PM25 < 30)	{
			$this->limits['Ilow'] = 25;
			$this->limits['Ihigh'] = 50;
			$this->limits['Clow'] = 15;
			$this->limits['Chigh'] = 30;
		}	elseif($PM25 < 55)	{
			$this->limits['Ilow'] = 50;
			$this->limits['Ihigh'] = 75;
			$this->limits['Clow'] = 30;
			$this->limits['Chigh'] = 55;
		}	elseif($PM25 <= 110)	{
			$this->limits['Ilow'] = 75;
			$this->limits['Ihigh'] = 100;
			$this->limits['Clow'] = 55;
			$this->limits['Chigh'] = 110;
		}	else	{	// >110
			return 101;
		}
		return $this->idx_calc($PM25);
	}

	/**
	 * Equation to calculate index value for a given pollutant concentration.
	 *
	 * @since	1.0.3
	 * @access	private
	 * @param	float	$C	pollutant concentration in μg/m3.
	 * @return	float	calculated partial index rounded.
	 * I = the (Air Quality) index,
	 * C = the pollutant concentration,
	 * Clow = the concentration breakpoint that is ≤ C,
	 * Chigh = the concentration breakpoint that is ≥ C,
	 * Ilow = the index breakpoint corresponding to Clow,
	 * Ihigh = the index breakpoint corresponding to Chigh.
	 */
	private function idx_calc($C)	{
		$I = (($this->limits['Ihigh'] - $this->limits['Ilow'])/($this->limits['Chigh'] - $this->limits['Clow']) * ($C - $this->limits['Clow']) + $this->limits['Ilow']);
		return round($I);
	}


}
