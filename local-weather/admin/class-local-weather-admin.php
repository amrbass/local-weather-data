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
		add_menu_page(  $this->local_weather, 'Local Weather', 'administrator', $this->local_weather, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-cloud', 26 );
		
		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page( $this->local_weather, 'Local Weather Settings', 'Settings', 'administrator', $this->local_weather.'-settings', array( $this, 'displayPluginAdminSettings' ));

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
		require_once 'partials/'.$this->plugin_name.'-admin-settings-display.php';
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
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */
		add_settings_section(
			// ID used to identify this section and with which to register options
			'local_weather_general_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			array( $this, 'local_weather_display_general_account' ),
			// Page on which to add this section of options
			'local_weather_general_settings'                   
		);
		unset($args);
		$args = array (
				'type'				=> 'input',
				'subtype'			=> 'text',
				'id'				=> 'local_weather_example_setting',
				'name'				=> 'local_weather_example_setting',
				'required'			=> 'true',
				'get_options_list'	=> '',
				'value_type'		=> 'normal',
				'wp_data'			=> 'option'
		);
		add_settings_field(
				'local_weather_example_setting',
				'Example Setting',
				array( $this, 'local_weather_render_settings_field' ),
				'local_weather_general_settings',
				'local_weather_general_section',
				$args
		);


		register_setting(
				'local_weather_general_settings',
				'local_weather_example_setting'
		);

	}

	/**
	 * This is the description on the settings page that explains why you need the information.
	 *
	 * @since	1.0.0
	 */
	public function local_weather_display_general_account() {
		echo '<p>These settings apply to all Local Weather functionality.</p>';
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
					$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
					$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
					$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
					if(isset($args['disabled'])){
						// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					} else {
						echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/

				}	else	{
					$checked = ($value) ? 'checked' : '';
					echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
				}
				break;
			default:
				# code...
				break;
		}
	}

}
