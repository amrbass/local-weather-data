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
	<div class="lwd_display">
		<?php echo $this->local_weather . " (v" . $this->version . ") WP plugin <small>by @amrbass</small>"; ?>
		<div class="lwd_time">
			<h2><img src="http://openweathermap.org/img/w/10d.png" class="lwd_weather_icon" /><?php esc_html_e('Local Weather', 'local-weather');?></h2>
		</div>
		<div>
			<p><big><?php echo sprintf(esc_html__('Display weather data from %1$sOpenWeatherMap%2$s.', 'local-weather'), '<b>', '</b>');?></big></p>
			<p><?php echo sprintf(esc_html__('In your selected page/post, insert a %1$sshortcode%2$s. See these shortcode format examples:', 'local-weather'), '<b>', '</b>');?></p>
			<p><code> [lwd-local-weather] </code></p>
			<p><code> [lwd-local-weather country="ad" zipcode="AD500" units="metric"] </code></p>
			<p><code> [lwd-local-weather country="us" zipcode="08514" units="standard"]Your Title[/lwd-local-weather] </code></p>
			<p><?php esc_html_e('Your own OpenWeatherMap API Key required! Get yours at', 'local-weather');?> <a href="https://openweathermap.org/appid" target="_blank">OpenWeatherMap</a></p>
		</div>
		<div style="font-size:120%;background-color:#dddddd;border:1px solid gray;margin:10px;padding:10px;overflow:hidden;">
			<p align="center"><big><b><?php esc_html_e('Generate a shortcode according to your current settings', 'local-weather');?></b></big></p>
			<form method="post">
				<input type="submit" name="lwd_generate_button" class="button" value="Generate" />
			</form>
			<p class="lwd_shortcode"><code>
				<?php
					if(array_key_exists('lwd_generate_button', $_POST)) {
						$this->lwd_generate_button_run();
					}
				?>
			</code></p>
			<p style="text-align:right;"><small><?php esc_html_e('(copy and paste into the required place)', 'local-weather');?></small></p>
		</div>
		<div class="lwd_time">
			<p><small><?php echo sprintf(esc_html__('Based on WordPress %1$sBoilerplate%2$s plugin example.', 'local-weather'), '<b>', '</b>');?></small></p>
			<a href="https://ancdretaeixample.cat/sw/info/local-weather/index.php" target="_blank"><?php esc_html_e('Plugin web link', 'local-weather');?></a>
		</div>
	</div>