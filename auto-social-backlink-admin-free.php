<?php

add_action('admin_menu', 'auto_social_backlink_free_menu');

function auto_social_backlink_free_menu() {
	add_options_page('Auto Social Backlinks Free', 'Auto Social Backlinks Free', 'manage_options', __FILE__, 'auto_social_backlink_free_setting_page');
	add_action('admin_init', 'auto_social_backlink_free_init');
}

function auto_social_backlink_free_init() {
	register_setting('wpsb_settings_fields', 'wpsb_options');
}

function auto_social_backlink_free_setting_page() {
	global $wpsb_options;
    
?>
<style>
textarea,
input[type="text"],
input[type="password"],
select {
	margin: 1px 0px 15px 0px;
	padding: 3px;
	border: 1px solid #ccc;
	font-family: arial;	
	padding: 5px 3px;
	font-size: 1.2em;
}
label {
	padding-top: 12px;
	line-height: 2;
}
.postbox{
	margin: 15px 0px; 
	font-size: 1.1em;
	width: 80%;
}
.h10 {
	overflow: hidden;
	height: 8px;
}
</style>
<div class="wrap">
    <div class="metabox-holder">

      	<h2>Auto Social Backlink Settings</h2>

         <div class="postbox">
         	<h3>Add you social network accounts</h3>
         	<div class="inside">
	         	<form method="post" action="options.php">
	         		<?php settings_fields('wpsb_settings_fields'); ?>
	         		
					<strong>Delicious:</strong><br/> 
					<label for="delicious_username">Connect to Delicious with your username and password</label><br/>
					<input id="delicious_username" name="wpsb_options[delicious_username]" value="<?php echo $wpsb_options['delicious_username']; ?>" type="text" size="28" maxlength="64" placeholder="Username"/>&nbsp;&nbsp;		
					<input id="delicious_password" name="wpsb_options[delicious_password]" value="<?php echo $wpsb_options['delicious_password']; ?>" type="text" size="28" maxlength="64" placeholder="Password"/>			
					<div class="h10"></div>
					<a href="https://delicious.com/" target="_blank">https://delicious.com/</a>
					<div class="h10"></div>
					<br/>
					
	              	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	              	<br/>
					<br/>
	       		</form>
       		</div>
         </div>
        <div>
        	<h4>Upgrade to Business Edition and get more social networks(Pinterest,StumbleUpon...) backlinks? [<a href="http://wpextends.sinaapp.com" target="_blank">Get It here</a>]</h4>
			<h4>For more infomation</h4>
			Plugin URI: <a href="http://wpextends.sinaapp.com/plugins/auto-social-backlink-builder.html" target="_blank">http://wpextends.sinaapp.com/plugins/auto-social-backlink-builder.html</a><br/>
			Our Website:<a href="http://wpextends.sinaapp.com" target="_blank">http://wpextends.sinaapp.com</a><br/>
	        <div class="h10"></div>
	        Please contact us at <a href="mailto:support@wordpressextends.com">support@wordpressextends.com</a> whenever you have any questions and comments.
        </div>	 
        <div class="h10"></div>         
        
        <div>
          	<h4>Like this plugin? We need your help to make it better:</h4>
          	<ul>
        		<li>Write a positive review.</li>
        		<li>Tell us some improvements.</li>
          		<li>If you’d like to donate...</li>
          	</ul>
          	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_donations">
				<input type="hidden" name="business" value="market@wordpressextends.com">
				<input type="hidden" name="item_name" value="Auto Social Backlink Builder Free plugin for Wordpress">
				<input type="hidden" name="currency_code" value="USD">
				<!-- <input type="hidden" name="notify_url" value="link to IPN script"> -->				
				<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
			</form>
			<p>Your support shows that what we’re doing really matters and help this plugin to move forward! Thank you.</p>
        </div>
        <div class="h10"></div>   
        
    </div>
</div>
<?php } ?>