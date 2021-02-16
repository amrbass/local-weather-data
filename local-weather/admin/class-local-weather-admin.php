<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ancdretaeixample.cat//sw/info/local-weather/index.php
 * @since      1.0.0
 *
 * @package    Local_Weather
 * @subpackage Local_Weather/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Local_Weather
 * @subpackage Local_Weather/admin
 * @author     Antoni Mas <amrbass@gmail.com>
 */
class Local_Weather_Admin {

	/**
	 * Global data arrays to be used for admin settings validation.
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
	 * @param      string    $local_weather       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $local_weather, $version ) {

		$this->local_weather = $local_weather;
		$this->version = $version;
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);
		add_action('admin_init', array( $this, 'registerAndBuildFields' ));

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
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->local_weather, plugin_dir_url( __FILE__ ) . 'css/local-weather-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->local_weather, plugin_dir_url( __FILE__ ) . 'js/local-weather-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Add menu pages and submenu pages.
	 *
	 * @since    1.0.0
	 */
	public function addPluginAdminMenu() {

		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page(  $this->local_weather, esc_html__('Local Weather', 'local-weather'), 'administrator',
			$this->local_weather, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-cloud', 26 );
		
		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page( $this->local_weather, esc_html__('Local Weather Settings', 'local-weather'), esc_html__('Settings', 'local-weather'),'administrator',
			$this->local_weather.'-settings', array( $this, 'displayPluginAdminSettings' ));

	}
	
	/**
	 * Access the file with functions that shows up when a user clicks on your plugin name in the left
	 * sidebar in the admin menu.
	 *
	 * @since    1.0.0
	 */
	public function displayPluginAdminDashboard() {
		require_once 'partials/'.$this->local_weather.'-admin-display.php';
	}

	/**
	 * Determine what tab you’re on if you have multiple settings forms.
	 *
	 * @since    1.0.0
	 */
	public function displayPluginAdminSettings() {
		// set this var to be used in the settings-display view
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'localWeatherSettingsMessages'));
			do_action( 'admin_notices', $_GET['error_message'] );
		}
		require_once 'partials/'.$this->local_weather.'-admin-settings-display.php';
	}
	
	/**
	 * This function is helpful when debugging.
	 *
	 * @since	1.0.0
	 * @param	string	$error_message	The error message or code.
	 */
	public function localWeatherSettingsMessages($error_message){
		switch ($error_message) {
			case '1':
				$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );
				$err_code = esc_attr( 'local_weather_example_setting' );
				$setting_field = 'local_weather_example_setting';                 
				break;
		}
		$type = 'error';
		add_settings_error(
				$setting_field,
				$err_code,
				$message,
				$type
			);
	}

	/**
	 * This file is where you define what fields you want to include in your settings form
	 * and it hooks up to another function that handles saving and pre-population of your form
	 * if users have already filled it out.
	 *
	 * @since	1.0.0
	 */
	public function registerAndBuildFields() {
		/**
		 * For each filed, do as follows.
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */
		add_settings_section(
			// ID used to identify this section and with which to register options
			'local_weather_general_section',
			// Title to be displayed on the administration page
			esc_html__('Basic options:', 'local-weather'),
			// Callback used to render the description of the section
			array( $this, 'local_weather_display_general_account' ),
			// Page on which to add this section of options
			'local_weather_general_settings'                   
		);

		unset($args);
		$args = array (
				'type'				=> 'input',
				'subtype'			=> 'text',
				'id'				=> $this->lwd_keys[LWD_COUNTRY],
				'name'				=> $this->lwd_keys[LWD_COUNTRY],
				'required'			=> 'true',
				'get_options_list'	=> '',
				'value_type'		=> 'normal',
				'wp_data'			=> 'option',
				'append_text'		=> esc_html__(' (2 letters country code)', 'local-weather'),
				'text_size'			=> '2'
		);
		add_settings_field(
				$this->lwd_keys[LWD_COUNTRY],
				esc_html__('1) Country code:', 'local-weather'),
				array( $this, 'local_weather_render_settings_field' ),
				'local_weather_general_settings',
				'local_weather_general_section',
				$args
		);
		register_setting(
				'local_weather_general_settings',
				$this->lwd_keys[LWD_COUNTRY],
				array('sanitize_callback' => array( $this, 'sanitize_local_weather_country' )),
		);

		unset($args);
		$args = array (
				'type'				=> 'input',
				'subtype'			=> 'text',
				'id'				=> $this->lwd_keys[LWD_ZIPCODE],
				'name'				=> $this->lwd_keys[LWD_ZIPCODE],
				'required'			=> 'true',
				'get_options_list'	=> '',
				'value_type'		=> 'normal',
				'wp_data'			=> 'option',
				'append_text'		=> esc_html__(' (a valid postal code)', 'local-weather'),
				'text_size'			=> '10'
		);
		add_settings_field(
				$this->lwd_keys[LWD_ZIPCODE],
				esc_html__('2) Postal code:', 'local-weather'),
				array( $this, 'local_weather_render_settings_field' ),
				'local_weather_general_settings',
				'local_weather_general_section',
				$args
		);
		register_setting(
				'local_weather_general_settings',
				$this->lwd_keys[LWD_ZIPCODE],
				array('sanitize_callback' => array( $this, 'sanitize_local_weather_zipcode' )),
		);

		unset($args);
		$args = array (
				'type'				=> 'input',
				'subtype'			=> 'text',
				'id'				=> $this->lwd_keys[LWD_UNITS],
				'name'				=> $this->lwd_keys[LWD_UNITS],
				'required'			=> 'true',
				'get_options_list'	=> '',
				'value_type'		=> 'normal',
				'wp_data'			=> 'option',
				'append_text'		=> esc_html__(' (standard, metric or imperial)', 'local-weather'),
				'text_size'			=> '8'
		);
		add_settings_field(
				$this->lwd_keys[LWD_UNITS],
				esc_html__('3) Units:', 'local-weather'),
				array( $this, 'local_weather_render_settings_field' ),
				'local_weather_general_settings',
				'local_weather_general_section',
				$args
		);
		register_setting(
				'local_weather_general_settings',
				$this->lwd_keys[LWD_UNITS],
				array('sanitize_callback' => array( $this, 'sanitize_local_weather_units' )),
		);

		unset($args);
		$args = array (
				'type'				=> 'input',
				'subtype'			=> 'text',
				'id'				=> $this->lwd_keys[LWD_APIKEY],
				'name'				=> $this->lwd_keys[LWD_APIKEY],
				'required'			=> 'true',
				'get_options_list'	=> '',
				'value_type'		=> 'normal',
				'wp_data'			=> 'option',
				'append_text'		=> esc_html__(' (32 chars alphanumeric key)', 'local-weather'),
		);
		add_settings_field(
				$this->lwd_keys[LWD_APIKEY],
				esc_html__('4) OpenWeatherMap API Key:', 'local-weather'),
				array( $this, 'local_weather_render_settings_field' ),
				'local_weather_general_settings',
				'local_weather_general_section',
				$args
		);
		register_setting(
				'local_weather_general_settings',
				$this->lwd_keys[LWD_APIKEY],
				array('sanitize_callback' => array( $this, 'sanitize_local_weather_apikey' )),
		);

	}

	/**
	 * This is the description on the settings page that explains why you need the information.
	 *
	 * @since	1.0.0
	 */
	public function local_weather_display_general_account( $arg ) {
		$o = sprintf( esc_html__( '%1$sEnter the default setting values to be used by %2$sLocal Weather%3$s. See parameters description below.%4$s', 'local-weather' ),
			'<div><h4>', '<b>', '</b>', '</h4>');
		//$o .= '<p>'.$arg['title'].'</p>';
		$o .= sprintf( esc_html__( '%1$sCountry code:%2$s a 2 letters contry code as defined in ISO 3166-1 alpha-2.%3$s', 'local-weather' ),
			'<ol><li><b>', '</b>', '</li>');
		$o .= sprintf( esc_html__( '%1$sPostal code:%2$s your target area postal code.%3$s', 'local-weather' ),
			'<li><b>', '</b>', '</li>');
		$o .= sprintf( esc_html__( '%1$sUnits:%2$s required measurement units system (standard, metric, imperial)%3$s', 'local-weather' ),
			'<li><b>', '</b>', '</li>');
		$o .= sprintf( esc_html__( '%1$sYour own OpenWeatherMap API Key%2$s (do not share nor publish)%3$s', 'local-weather' ),
			'<li><b>', '</b>', '</li></ol></div>');

		echo $o;
	}

	/**
	 * This function is used to automatically save and populate inputs based
	 * on the specified option name (relevant to the settings page)
	 * or postmeta key (more relevant for custom post types) associated with that setting.
	 * It will also handle saving serialized or normal values. 
	 *
	 * @since	1.0.0
	 * @param	array	$args	Options?.
	 */
	public function local_weather_render_settings_field($args) {
		/* EXAMPLE INPUT
				'type'		=> 'input',
				'subtype'	=> '',
				'id'		=> $this->plugin_name.'_example_setting',
				'name'		=> $this->plugin_name.'_example_setting',
				'required'	=> 'required="required"',
				'get_option_list'	=> "",
				'value_type'		=> serialized OR normal,
				'wp_data'			=> (option or post_meta),
				'post_id'			=>
		*/     
		if($args['wp_data'] == 'option')	{
			$wp_data_value = get_option($args['name']);
		}	elseif($args['wp_data'] == 'post_meta')	{
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}

		switch ($args['type'])	{

			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if($args['subtype'] != 'checkbox')	{
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$appendText = (isset($args['append_text'])) ? $args['append_text'] : '';
					$size = (isset($args['text_size'])) ? $args['text_size'] : '40';
					$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
					$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
					$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
					if(isset($args['disabled'])){
						// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="'.$size.'" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="'.$size.'" value="' . esc_attr($value) . '" />'.$appendText.$prependEnd;
					} else {
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="'.$size.'" value="' . esc_attr($value) . '" />'.$appendText.$prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/

				}	else	{
					$checked = ($value) ? 'checked' : '';
					echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="'.$size.'" value="1" '.$checked.' />'.$appendText;
				}
				break;
			default:
				# code...
				break;
		}
	}

	/**
	 * Sanitize Country code input text.
	 *
	 * @since	1.0.0
	 * @param	string	$input	The value entered by the user.
	 * @return	string	The corrected value.
	 */
	public function sanitize_local_weather_country( $input )	{

		//sanitize
		$output = strtolower(sanitize_text_field($input));

		//detect a 2 letters country code
		if(!preg_match('/^[a-zA-Z]{2}$/', $output))	{
			if (strlen($output) > 2) {
				$output = substr($output, 0, 2);
			}
		}
		//if not a valid country, provide a default one
		if(!array_key_exists($output, $this->lwd_countries))	{
			//$output = 'ad';
			$output = $this->lwd_settings[$this->lwd_keys[LWD_COUNTRY]];
		}

		return $output;
	}

	/**
	 * Sanitize Postal code input text.
	 *
	 * @since	1.0.0
	 * @param	string	$input	The value entered by the user.
	 * @return	string	The corrected value.
	 */
	public function sanitize_local_weather_zipcode( $input )	{

		//sanitize
		$output = strtoupper(sanitize_text_field($input));

		//detect a 5 alpha/digits postal code
		if(!preg_match('/^[a-zA-Z0-9]+[a-zA-Z0-9\-]{4}$/', $output))	{
			//it's unclear the ZIP code format OWM expects. Most probably, only a total of 5 alpha/digits
			//but real Postal codes may be as long as 10 digits in some countries
			if (strlen($output) > 5) {
				$output = substr($output, 0, 5);
			}	else	{
				$output = $this->lwd_settings[$this->lwd_keys[LWD_ZIPCODE]];
			}
		}

		return $output;
	}

	/**
	 * Sanitize Units input text.
	 *
	 * @since	1.0.0
	 * @param	string	$input	The value entered by the user.
	 * @return	string	The corrected value.
	 */
	public function sanitize_local_weather_units( $input )	{

		//$units = ['standard','metric','imperial'];	//OWM API units allowed

		//sanitize
		$output = strtolower(sanitize_text_field($input));

		//chack if entered text is in OWM units list
		if (!in_array($output, $this->lwd_units)) {
			//$output = $this->lwd_units[1];
			$output = $this->lwd_settings[$this->lwd_keys[LWD_UNITS]];
		}

		return $output;
	}

	/**
	 * Sanitize API key input text.
	 *
	 * @since	1.0.0
	 * @param	string	$input	The value entered by the user.
	 * @return	string	The corrected value.
	 */
	public function sanitize_local_weather_apikey( $input )	{

		//sanitize
		//error_log("inside 'sanitize_local_weather_apikey'");
		$output = sanitize_text_field($input);

		//detect a 5 alpha/digits postal code
		if(!preg_match('/^[a-zA-Z0-9]{32}$/', $output))	{
			$output = $this->lwd_settings[$this->lwd_keys[LWD_APIKEY]];
		}

		return $output;
	}

	private function lwd_generate_button_run()	{
		$country = get_option($this->lwd_keys[LWD_COUNTRY]);
		$zipcode = get_option($this->lwd_keys[LWD_ZIPCODE]);
		$units = get_option($this->lwd_keys[LWD_UNITS]);
		echo '[lwd-local-weather country="'.$country.'" zipcode="'.$zipcode.'" units="'.$units.'"]Your Title[/lwd-local-weather]';
	}

}
