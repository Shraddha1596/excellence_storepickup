<?php

function excellence_pickup_method() {
        if ( ! class_exists( 'Excellence_Pickup_Method' ) ) {
            class Excellence_Pickup_Method extends WC_Shipping_Method {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct() {
                    $this->id                 = 'excellence-pickup'; 
                    $this->method_title       = __( 'Excellence Store Pickup', 'excellence-pickup' );  
                    $this->method_description = __( 'Shipping Pickup Plugin by Excellence', 'excellence-pickup' ); 
 
                    // Availability & Countries
                    $this->availability = 'including';
                    $this->countries = array(
                        'US', // Unites States of America
                        'CA', // Canada
                        'DE', // Germany
                        'GB', // United Kingdom
                        'IT',   // Italy
                        'ES', // Spain
                        'HR',  // Croatia
                        'IN'	// India
                        );
 
                    $this->init();
 
                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Excellence Store Pickup', 'excellence-pickup' );

                }
 
                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {

                    // Load the settings API
                    $this->init_form_fields(); 
                    $this->init_settings(); 
 
                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }
 
                /**
                 * Define settings field for this shipping
                 * @return void 
                 */
                function init_form_fields() { 

 
                    $this->form_fields = array(
 
                     	'enabled' => array(
                        	'title' => __( 'Enable', 'excellence-pickup' ),
	                        'type' => 'checkbox',
	                        'description' => __( 'Enable this shipping.', 'excellence-pickup' ),
	                        'default' => 'yes'
	                    ),
 
                     	'title' => array(
                        	'title' => __( 'Title', 'excellence-pickup' ),
                          	'type' => 'text',
                          	'description' => __( 'Title to be display on site', 'excellence-pickup' ),
                          	'default' => __( 'Excellence Store Pickup', 'excellence-pickup' )
                        ),

                       //  'add_store' => array(
                       //  	'title' => __( 'Add Store', 'excellence-pickup' ),
                       //    	'type' => 'button',
                       //    	'description' => __( 'You can add multiple Store Locations', 'excellence' ),
                       //    	'default' => 'Click here to add Store +',

                      	// ),
 						'store_name' => array(
                        	'title' => __( 'Store Name', 'excellence-pickup' ),
                          	'type' => 'text',
                          	'description' => __( 'Add Store Name', 'excellence-pickup' ),
                          	// 'default' => 'Click here to add Store +',

                      	),
                      	'store_address' => array(
                        	'title' => __( 'Store Address', 'excellence-pickup' ),
                          	'type' => 'textarea',
                          	'description' => __( 'Add Store Address', 'excellence-pickup' ),
                          	// 'default' => 'Click here to add Store +',

                      	),
                      	'store_number' => array(
                        	'title' => __( 'Store Contact Number', 'excellence-pickup' ),
                          	'type' => 'number',
                          	'description' => __( 'Add Store Contact Details', 'excellence-pickup' ),
                      	),

                    
                       
                    //  'weight' => array(
                    //     'title' => __( 'Weight (kg)', 'excellence' ),
                    //       'type' => 'number',
                    //       'description' => __( 'Maximum allowed weight', 'excellence' ),
                    //       'default' => 100
                    //       ),
 
                    );
 
                }
 
                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping( $package  = array()) {
                    
                    $weight = 0;
                    $cost = 0;
                    $country = $package["destination"]["country"];
 
                    foreach ( $package['contents'] as $item_id => $values ) 
                    { 
                        $_product = $values['data']; 
                        $weight = $weight + $_product->get_weight() * $values['quantity']; 
                    }
 
                    $weight = wc_get_weight( $weight, 'kg' );
 
                    if( $weight <= 10 ) {
 
                        $cost = 2;
 
                    } elseif( $weight <= 30 ) {
 
                        $cost = 5;
 
                    } elseif( $weight <= 50 ) {
 
                        $cost = 10;
 
                    } else {
 
                        $cost = 20;
 
                    }
 
                    $countryZones = array(
                        'HR' => 1,
                        'US' => 3,
                        'GB' => 2,
                        'CA' => 3,
                        'ES' => 2,
                        'DE' => 1,
                        'IT' => 1,
                        'IN' => 0
                        );
 
                    $zonePrices = array(
                        0 => 10,
                        1 => 30,
                        2 => 50,
                        3 => 70
                        );
 
                    $zoneFromCountry = $countryZones[ $country ];
                    $priceFromZone = $zonePrices[ $zoneFromCountry ];
 
                    $cost += $priceFromZone;
 
                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $cost
                    );
 
                    $this->add_rate( $rate );
                    
                }
            }
        }
}
 
add_action( 'woocommerce_shipping_init', 'excellence_pickup_method' );
 
function add_excellence_pickup_method( $methods ) {

    $methods[] = 'Excellence_Pickup_Method';
     include_once('design/design.php');
    return $methods;

}
 
add_filter( 'woocommerce_shipping_methods', 'add_excellence_pickup_method' );

function excellence_validate_order( $posted )   {

    $packages = WC()->shipping->get_packages();

    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
     
    if( is_array( $chosen_methods ) && in_array( 'excellence-pickup', $chosen_methods ) ) {
         
        foreach ( $packages as $i => $package ) {

            if ( $chosen_methods[ $i ] != "excellence-pickup" ) {
                         
                continue;
                         
            }

            $Excellence_Pickup_Method = new Excellence_Pickup_Method();
            $weightLimit = (int) $Excellence_Pickup_Method->settings['weight'];
            $weight = 0;

            foreach ( $package['contents'] as $item_id => $values ) 
            { 
                $_product = $values['data']; 
                $weight = $weight + $_product->get_weight() * $values['quantity']; 
            }

            $weight = wc_get_weight( $weight, 'kg' );
            
            if( $weight > $weightLimit ) {

                    $message = sprintf( __( 'Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'excellence-pickup' ), $weight, $weightLimit, $Excellence_Pickup_Method->title );
                         
                    $messageType = "error";

                    if( ! wc_has_notice( $message, $messageType ) ) {
                     
                        wc_add_notice( $message, $messageType );
                  
                    }
            }
        }       
    } 
}

add_action( 'woocommerce_review_order_before_cart_contents', 'excellence_validate_order' , 10 );
add_action( 'woocommerce_after_checkout_validation', 'excellence_validate_order' , 10 );





/**
 * Add store location select dropdown in checkout page
 **/
// add_filter( 'woocommerce_checkout_fields' , 'custom_store_pickup_field');
 
// function custom_store_pickup_field( $fields ) {
//       $fields['billing']['store_pickup'] = array(
//      'type'     => 'select',
//             'options'  => array(
//         'option_1' => 'Option 1 text',
//                 'option_2' => 'Option 2 text',
//         'option_3' => 'Option 2 text'
//         ),
//      'label'     => __('Store Pick Up Location', 'woocommerce'),
//     'required'  => false,
//     'class'     => array('store-pickup form-row-wide'),
//     'clear'     => true
//      );
 
//      return $fields;
// }
// /**
//  * Update the order meta with store location pickup value
//  **/
// add_action( 'woocommerce_checkout_update_order_meta', 'store_pickup_field_update_order_meta' );
// function store_pickup_field_update_order_meta( $order_id ) {
//                 if ( $_POST[ 'store_pickup' ] )
//                                 update_post_meta( $order_id, 'Store PickUp Location', esc_attr( $_POST[ 'store_pickup' ] ) );
// }