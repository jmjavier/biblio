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

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// TODO: Define uninstall functionality here
delete_option('biblio_key');
delete_option('amazon_access_key');
delete_option('amazon_secret_key');
delete_option('amazon_affiliate_tag');
