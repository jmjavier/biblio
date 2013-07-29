<?php
/**
 * Biblio
 *
* @package   Biblio
 * @author    Miguel Javier <miguel.javier@gmail.com>
 * @license   GPL-3.0
 * @link      http://javijavier.com
 * @copyright 2013 Miguel Javier
  */

/**
 * Biblio
 *
 *
 * @package Biblio
 * @author  Miguel Javier <miguel.javier@gmail.com>
 */

require 'vendor/autoload.php';

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Lookup;
use ApaiIO\ApaiIO;


class Biblio {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'biblio';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		add_action( 'init', array($this, 'biblio_make_all_custom_posts' ));
  		add_action( 'init', array($this, 'biblio_create_taxonomies' ));
		add_action( 'init', array($this, 'biblio_catch_calls'));
		add_action( 'admin_menu', array($this, 'biblio_admin_menu'), 9 );
		add_filter( 'plugin_action_links', array($this, 'biblio_plugin_action_link'), 10, 2 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if (!get_option('biblio_key')){
			$biblio = new Biblio();
			$randomstring = $biblio->generateRandomString();
			add_option('biblio_key', $randomstring);
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

	}


	public function biblio_create_post_type($singular,$plural,$taxonomies) {
		$singular = ucwords($singular);
		$plural = ucwords($plural);

		$labels = array(
		  'name' => _x($plural, 'post type general name'),
		  'singular_name' => _x($singular, 'post type singular name'),
		  'add_new' => _x('Add New', $singular),
		  'add_new_item' => __('Add New '.$singular),
		  'edit_item' => __('Edit '.$singular),
		  'new_item' => __('New '.$singular),
		  'view_item' => __('View '.$singular),
		  'search_items' => __('Search '.$singular),
		  'not_found' =>  __('No '.$plural.' found'),
		  'not_found_in_trash' => __('No '.$plural.' found in Trash'),
		  'parent_item_colon' => '',
		  'menu_name' => $plural
		);

		$args = array(
		  'labels' => $labels,
		  'public' => true,
		  'publicly_queryable' => true,
		  'show_ui' => true,
		  'show_in_menu' => true,
		  'query_var' => true,
		  'rewrite' => array('slug'=>strtolower(str_replace(' ','-',$singular))),
		  'capability_type' => 'post',
		  'has_archive' => true,
		  'hierarchical' => true,
		  'menu_position' => null,
		  /* 'taxonomies' => $taxonomies, */
		  'supports' => array('title','editor','author','thumbnail','excerpt','custom-fields')
		);

		return $args;
	}

	public function biblio_make_all_custom_posts(){
		$customposts = array();
		$customposts['biblio_book'] = $this->biblio_create_post_type('book','books',array());
		foreach ($customposts as $unique => $custompost){
			register_post_type( $unique, $custompost	);
		}
	}

	public function biblio_admin_menu(){
		add_menu_page( 'Biblio', 'Biblio', 'edit_posts', 'biblio', array($this, 'biblio_admin_page'));
		add_submenu_page( 'biblio', 'Amazon API settings', 'Amazon API settings', 'edit_posts', 'biblio-amazon', array($this, 'biblio_submenu_page'));
	}

	public function biblio_admin_page(){
		require_once('views/page.php');
	}

	public function biblio_submenu_page(){
		require_once('views/page-amazon.php');
	}



	public function biblio_plugin_action_link( $links, $file ) {
		if ( $file === BIBLIO_PLUGIN_BASENAME )
			array_push( $links, '<a href="admin.php?page=biblio">Biblio Settings</a>' );

		return $links;
	}

	public function biblio_amazon_authenticate(){
		$amazon_access_key = get_option('amazon_access_key');
		$amazon_secret_key = get_option('amazon_secret_key');
		$amazon_affiliate_tag = get_option('amazon_affiliate_tag');

		$conf = new GenericConfiguration();
		$conf
		    ->setCountry('com')
		    ->setAccessKey($amazon_access_key)
		    ->setSecretKey($amazon_secret_key)
		    ->setAssociateTag($amazon_affiliate_tag)
		    ->setRequest('\ApaiIO\Request\Soap\Request')
		    ->setResponseTransformer('\ApaiIO\ResponseTransformer\ObjectToArray');
		return $conf;
	}

	public function biblio_amazon_lookup($conf, $asin){

		$lookup = new Lookup();
		$lookup->setItemId($asin);
		$lookup->setResponseGroup(array('Large')); // More detailed information

		$apaiIo = new ApaiIO($conf);
		$response = $apaiIo->runOperation($lookup);

		$book = array();

		if (!isset($response['Items']['Request']['Errors'])) {
			$item = $response['Items']['Item'];


			$book['title'] = $item['ItemAttributes']['Title'];
			$book['author'] = $item['ItemAttributes']['Author'];
			$book['ASIN'] = $item['ASIN'];
			$book['page_length'] = $item['ItemAttributes']['NumberOfPages'];
			$book['publisher'] = $item['ItemAttributes']['Publisher'];
			$book['published'] = $item['ItemAttributes']['PublicationDate'];

			if (isset($item['LargeImage']['URL'])){
				$book['image'] = $item['LargeImage']['URL'];
			}
			if (isset($item['EditorialReviews']['EditorialReview'][0]['Content'])){
				$book['description'] = $item['EditorialReviews']['EditorialReview'][0]['Content'];
			}else {
				$book['description'] = '';
			}

			$nodes = $item['BrowseNodes']['BrowseNode'];
			$categories = array();
			foreach($nodes as $node){
				$current = $node;
				$subnodes = array();
				$subnodes[] = $node['Name'];
				while(isset($current['Ancestors'])) {
					$subnodes[] = $current['Ancestors']['BrowseNode']['Name'];
					$current = $current['Ancestors']['BrowseNode'];
				}
				$subnodes = array_filter($subnodes);
				$categories[] = array_reverse($subnodes);
			}
			$book['categories'] = $categories;

		}else {
			$book['error'] = true;
		}
		return $book;
	}


	public function biblio_catch_calls(){
		if (empty($_GET['bibliokey'])){
			return;
		}else {
			if($_GET['bibliokey'] === get_option('biblio_key')){

				$conf = $this->biblio_amazon_authenticate();
				$book = $this->biblio_amazon_lookup($conf, $_GET['asin']);
				if (!$book['error']){
					$post = array(
						'post_title'=>$book['title'],
						'post_type'=> 'biblio_book',
						'post_status'=> 'publish'
					);

					$pid = wp_insert_post($post);
					add_post_meta($pid, 'description', $book['description']);
					if (isset($book['image'])){
						add_post_meta($pid, 'imageurl', $book['image']);
					}
					add_post_meta($pid, 'asin', $book['ASIN']);

					if (is_array($book['author'])){
						foreach($book['author'] as $author){
							add_post_meta($pid, 'author', $author);
						}
					}else {
						add_post_meta($pid, 'author', $book['author']);
					}
					add_post_meta($pid, 'publisher', $book['publisher']);
					add_post_meta($pid, 'published', $book['published']);
					add_post_meta($pid, 'pages', $book['page_length']);

					$post_terms = array();
					foreach($book['categories'] as $categorychain){
						$parent_term_id = 0;
						foreach($categorychain as $category){
							$parent_term = term_exists($category,'biblio_category');
							if (!$parent_term){
								$parent_term = wp_insert_term($category,'biblio_category',array('parent'=>$parent_term_id));
							}
							$parent_term_id = $parent_term['term_id'];
							$post_terms[] = $parent_term_id;
						}
					}
					$post_terms = array_unique($post_terms);
					wp_set_post_terms( $pid, $post_terms, 'biblio_category' );
	 				delete_option("biblio_category_children"); //temporary bug fix http://core.trac.wordpress.org/ticket/14485
					echo 'success';
				}
			}
			exit;
		}
	}

	public function generateRandomString($length = 12) {
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$randomString = '';
    	for ($i = 0; $i < $length; $i++) {
        	$randomString .= $characters[rand(0, strlen($characters) - 1)];
    	}
    	return $randomString;
	}

	public function biblio_create_taxonomies() {
		$labels = array(
		  'name' => _x( 'Book Categories', 'taxonomy general name' ),
		  'singular_name' => _x( 'Book Category', 'taxonomy singular name' ),
		  'search_items' =>  __( 'Search Book Categories' ),
		  'all_items' => __( 'All Book Categories' ),
		  'edit_item' => __( 'Edit Book Category' ),
		  'update_item' => __( 'Update Book Category' ),
		  'add_new_item' => __( 'Add New Book Category' ),
		  'new_item_name' => __( 'New Book Category' ),
		  'menu_name' => __( 'Book Categories' )
		);

		register_taxonomy('biblio_category',array('biblio_book'), array(
		  'hierarchical' => true,
		  'labels' => $labels,
		  'show_ui' => true,
		  'query_var' => true,
		  'rewrite' => array( 'slug' => 'book-category' )
		));
	}



}