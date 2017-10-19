<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       getbowtied.com
 * @since      1.0.0
 *
 * @package    HMU
 * @subpackage HMU/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    HMU
 * @subpackage HMU/admin
 * @author     GetBowtied <adi@getbowtied.com>
 */
class HMU_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hookmeup-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name,  plugin_dir_url( __FILE__ ) . 'js/hookmeup-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function get_url() {

		$page = $_POST['page'];

		switch($page) {
			case 'shop': 
				echo get_permalink( wc_get_page_id( 'shop' ) ); 
				break;
			case 'cart': 
				echo wc_get_cart_url(); 
				break;
			case 'checkout': 
				echo wc_get_checkout_url(); 
				break;
			case 'account': 
				echo get_permalink( get_option('woocommerce_myaccount_page_id') ); 
				break;
			case 'product': 
				$args = array('orderby' => 'rand'); 
				$products = wc_get_products($args); 
				echo get_permalink( $products[0]->get_id() ); 
				break;
			case 'default':
				echo get_home_url();
				break;
		}

	}

}
