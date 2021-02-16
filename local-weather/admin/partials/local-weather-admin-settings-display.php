<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ancdretaeixample.cat//sw/info/local-weather/index.php
 * @since      1.0.0
 *
 * @package    Local_Weather
 * @subpackage Local_Weather/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<div class="lwd_display">
		<div id="icon-themes" class="icon32"><span class="dashicons dashicons-cloud"></span></div>
		<div class="lwd_time">
			<h2><img src="http://openweathermap.org/img/w/10d.png" class="lwd_weather_icon" /><?php esc_html_e('Local Weather Settings', 'local-weather');?></h2>
		</div>
		<!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
		<?php settings_errors(); ?>  
		<form method="POST" action="options.php">
			<?php
				settings_fields( 'local_weather_general_settings' );
				do_settings_sections( 'local_weather_general_settings' );
			?>
			<?php submit_button(); ?>
		</form>
	</div>
</div>