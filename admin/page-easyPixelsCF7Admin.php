<div class="wrap">
	<h1><?php echo __('Easy Pixels by <b>JEVNET</b>','easy-pixels-contact-form-extension-by-jevnet'); ?></h1>
     <p><?php echo __('Track Contact Form 7! Works with Google Ads, Facebook and Google Analytics.','easy-pixels-contact-form-extension-by-jevnet'); ?><br/><br/></p>

	<?php
	echo '<h2 class="nav-tab-wrapper">';
	do_action('easypixels_admintabs');
	echo '</h2>';
	?>


	<form method="post" action="options.php">
	<?php
		settings_fields('jnAnalyticsCF7Settings-group');
		do_settings_sections('jnAnalyticsCF7Settings-group');

		do_action('easyPixelsContactForm');
		submit_button(); 
		?>
	</form>
</div>