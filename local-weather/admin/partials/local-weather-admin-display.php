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
		<?php echo $this->local_weather . " (v" . $this->version . ")"; ?>
		<div class="lwd_time">
			<h2><img src="http://openweathermap.org/img/w/10d.png" class="lwd_weather_icon" />local-weather WP plugin <small>by @amrbass</small></h2>
		</div>
		<div>
			<p><big>Display weather data from <b>OpenWeatherMap</b>.</big></p>
			<p>In your selected page/post, insert a <b>shortcode</b> similar to this:</p>
			<p><code>[lwd-local-weather country="us" zipcode="08514" units="metric"]Your Title[/lwd-local-weather]</code></p>
			<p>Your own OpenWeatherMap API Key required!</p>
		</div>
		<div style="font-size:120%;background-color:#dddddd;border:1px solid gray;margin:10px;padding:10px;overflow:hidden;">
			<p align="center"><big><b>Generate a shortcode according to your current settings</b></big></p>
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
			<p style="text-align:right;"><small>(copy and paste into the required place)</small></p>
		</div>
		<div class="lwd_time">
			<p><small>Based on WordPress Plugin <b>Boilerplate</b></small></p>
			<a href="https://ancdretaeixample.cat//sw/info/local-weather/index.php" target="_blank">Plugin web link</a>
		</div>
	</div>