<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       getbowtied.com
 * @since      1.2
 * @package    HMU
 * @subpackage HMU/includes/customizer
 */

class HookMeUp_Customizer {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.2
	 */
	public function __construct() {

		$this->add_hooks();
	}

	/**
	 * Add hooks.
	 *
	 * @since 1.2
	 *
	 * @return void
	 */
	protected function add_hooks() {

		add_action( 'customize_register', array( $this, 'define_hmu_sections' ) );
		add_action( 'customize_register', array( $this, 'define_hmu_controls' ) );
		add_action( 'wp_ajax_get_hmu_customize_section_url', array( $this, 'get_hmu_customize_section_url' ) );
	}

	/**
	 * Define customizer sections
	 *
	 * @since 1.2
	 *
	 * @return void
	 */
	public function define_hmu_sections( $wp_customize ) {

		// Woocommerce Hooks
		$wp_customize->add_panel( 'hookmeup_section' , array(
		    'title'      => esc_attr__('WooCommerce Hooks', 'hookmeup'),
		    'priority'   => 10,
		) );

		// Shop Archives
	    $wp_customize->add_section( 'hookmeup_shop_section', array(
	 		'title'       => esc_attr__('Shop Archives', 'hookmeup'),
	 		'priority'    => 10,
	 		'panel'       => 'hookmeup_section',
	 	) );

	 	// Single Product
	    $wp_customize->add_section( 'hookmeup_product_section', array(
	 		'title'       => esc_attr__('Product Page', 'hookmeup'),
	 		'priority'    => 10,
	 		'panel'       => 'hookmeup_section',
	 	) );

	    // Cart
	    $wp_customize->add_section( 'hookmeup_cart_section', array(
	 		'title'       => esc_attr__('Cart', 'hookmeup'),
	 		'priority'    => 10,
	 		'panel'       => 'hookmeup_section',
	 	) );

	 	// Cart Widget
	    $wp_customize->add_section( 'hookmeup_cart_widget_section', array(
	 		'title'       => esc_attr__('Cart Widget', 'hookmeup'),
	 		'priority'    => 10,
	 		'panel'       => 'hookmeup_section',
	 	) );

	 	// Thank You Page
	    $wp_customize->add_section( 'hookmeup_thankyou_section', array(
	 		'title'       => esc_attr__('Thank You Page', 'hookmeup'),
	 		'priority'    => 10,
	 		'panel'       => 'hookmeup_section',
	 	) );

	    // Checkout
	    $wp_customize->add_section( 'hookmeup_checkout_section', array(
	 		'title'       => esc_attr__('Checkout', 'hookmeup'),
	 		'priority'    => 10,
	 		'panel'       => 'hookmeup_section',
	 	) );

	    // My Account / Login
	    $wp_customize->add_section( 'hookmeup_account_section', array(
	 		'title'       => esc_attr__('My Account', 'hookmeup'),
	 		'priority'    => 10,
	 		'panel'       => 'hookmeup_section',
	 	) );
	}

	/**
	 * Define customizer controls
	 *
	 * @since 1.2
	 *
	 * @return void
	 */
	public function define_hmu_controls( $wp_customize ) {

		$hooks  = new HookMeUp_Hooks();
		$hook_sections = $hooks->get_hook_sections();

		foreach( $hook_sections as $section ) {
			$section_hooks = $hooks->get_hooks( $section );

			$wp_customize->add_setting( $section . '_preview', array(
				'default'    => '1',
				'capability' => 'manage_options',
				'transport' => 'refresh',
			) );

			$description = '<span>They will only be visible while logged in as admin.</span>';

			if( $section == 'hookmeup_thankyou_section' ) {
				$description .= '<span class="section_warning">There is no preview available for this page.</span>';
			}

			if( $section == 'hookmeup_cart_widget_section' ) {
				$description .= '<span class="section_warning">In order to see the changes in your cart widget, you may need to update your cart items.</span>';
			}

			$wp_customize->add_control( new WP_Customize_Toggle_Control( $wp_customize, $section . '_preview', array(
				'label'       	=> esc_attr__( 'Preview Available Hooks', 'hookmeup' ),
				'section'     	=> $section,
				'description'	=> __( $description, 'hookmeup' ),
				'priority'    	=> 10,
			) ) );

		    foreach( $section_hooks as $hook ) {

		    	$wp_customize->add_setting( $hook['slug'] . '_collapsible', array(
					'transport'  => 'postMessage',
					'capability' => 'edit_theme_options'
				) );

				$wp_customize->add_control( new WP_Customize_Collapsible_Control( $wp_customize, $hook['slug'] . '_collapsible', array(
					'section' 			=> $hook['section'],
					'label'				=> $hook['label'],
					'slug'				=> $hook['slug'],
					'priority'			=> 10,
				) ) );

				$wp_customize->add_setting( $hook['slug'] . '_editor', array(
					'transport' => 'refresh',
					'default' => '',
				) );

		     	$wp_customize->add_control( new WP_Customize_Editor_Control( $wp_customize, $hook['slug'] . '_editor', array(
					'section' 			=> $hook['section'],
					'priority'			=> 10,
					'editor_settings' 	=> array(
						'quicktags' 	=> true,
						'tinymce'   	=> true,
						'mediaButtons' 	=> true
					),
				) ) );
			}
		}
	}

	/**
	 * Retrieve preview url for the current section
	 *
	 * @since 1.2
	 *
	 * @return void
	 */
	public function get_hmu_customize_section_url() {

		switch( $_POST['page'] ) {
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
				$args = array('orderby' => 'rand', 'limit' => 1); 
				$products = wc_get_products($args); 
				echo get_permalink( $products[0]->get_id() ); 
				break;
			default:
				echo get_home_url();
				break;
		}
		exit();
	}
}