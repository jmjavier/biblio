<?php
/**
 *
 * @package   Biblio
 * @author    Miguel Javier <miguel.javier@gmail.com>
 * @license   GPL-3.0
 * @link      http://javijavier.com
 * @copyright 2013 Miguel Javier
 *
 * @wordpress-plugin
 * Plugin Name: Biblio
 * Plugin URI:  http://javijavier.com
 * Description: Stores books
 * Version:     1.0.0
 * Author:      Miguel Javier
 * Author URI:  http://javijavier.com
 * License:     GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-biblio.php' );

if ( ! defined( 'BIBLIO_PLUGIN_BASENAME' ) )
	define( 'BIBLIO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );


// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'Biblio', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Biblio', 'deactivate' ) );


Biblio::get_instance();