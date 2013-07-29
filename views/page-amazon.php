<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Biblio
 * @author    Miguel Javier <miguel.javier@gmail.com>
 * @license   GPL-3.0
 * @link      http://javijavier.com
 * @copyright 2013 Miguel Javier
  */


	if (isset($_POST['amazon_access_key'])){
		update_option( 'amazon_access_key', $_POST['amazon_access_key'] );
	}


	if (isset($_POST['amazon_secret_key'])){
		update_option( 'amazon_secret_key', $_POST['amazon_secret_key'] );
	}


	if (isset($_POST['amazon_affiliate_tag'])){
		update_option( 'amazon_affiliate_tag', $_POST['amazon_affiliate_tag'] );
	}
	
	$amazon_access_key = get_option('amazon_access_key');
	$amazon_secret_key = get_option('amazon_secret_key');
	$amazon_affiliate_tag = get_option('amazon_affiliate_tag');

?>

	<form name="biblio_key_form" method="post" action=""/>
	<h3>Amazon Configuration</h3>
	<h4>Amazon Advertising API</h4>
	<p>
		<label for="amazon_access_key">Access Key</label>
		<input name="amazon_access_key" type="text" value="<?php echo $amazon_access_key; ?>"/>
	</p>
	<p>
		<label for="amazon_secret_key">Secret Key</label>
		<input type="text" name="amazon_secret_key" value="<?php echo $amazon_secret_key; ?>"/>
	</p>
	<h4>Amazon Affiliate Tag</h4>
	<p>
		<label for="amazon_affiliate_tag">Affiliate Tag</label>
		<input type="text" name="amazon_affiliate_tag" value="<?php echo $amazon_affiliate_tag; ?>"/>
	</p>
	<input type="submit" value="Save"/>
	</form>
