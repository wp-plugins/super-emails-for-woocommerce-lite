<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Returns configuration for this plugin
 * 
 * @return array
 */

if ( ! function_exists( 'wc_light_or_dark' ) ) {
    function wc_light_or_dark( $arg ) {
        return $arg;
    }
}

if ( ! function_exists( 'wc_hex_lighter' ) ) {
    function wc_hex_lighter( $arg ) {
        return $arg;
    }
}

function sfew_lite_settings()
{
    return array(
        'general_settings' => array(
            'title' => __('General settings', 'sefw-lite'),
            'icon' => '',
            'children' => array(
                'main_settings' => array(
                    'title' => __('Integration of your products', 'sefw-lite'),
                    'children' => array(
                        'enabled' => array(
                            'title' => __('Activate', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Activate WC Super Emails plugin</span>', 'sefw-lite'),
                        ),
                        'na_products_quantity' => array(
                            'title' => __('Number of products', 'sefw-lite'),
                            'type' => 'dropdown',
                            'default' => 3,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                3 => '3',
                            ),
                            'hint' => __('<span>Number of products to be displayed per email</span>', 'sefw-lite'),
                        ),
                        'na_number_rows' => array(
                            'title' => __('Number of rows', 'sefw-lite'),
                            'type' => 'dropdown',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                1 => '1',
                            ),
                            'hint' => __('<span>Display 1 or 2 rows of products per email.<br>For 2 rows, number of products must be at least 4.</span>', 'sefw-lite'),
                        ),
                        'na_enable_on_new_order' => array(
                            'title' => __('Enable with Processing order', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Display suggested products on Processing order emails</span>', 'sefw-lite'),
                        ),
                        'enable_on_order_complete' => array(
                            'title' => __('Enable with Completed order', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Display suggested products on Completed order emails</span>', 'sefw-lite'),
                        ),
                        'na_enable_on_customer_note' => array(
                            'title' => __('Enable with Customer note', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Display suggested products on Customer note emails</span>', 'sefw-lite'),
                        ),
                        'na_embedded_images' => array(
                            'title' => __('Embed images', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Integrate images in emails. May not work on your server</span>', 'sefw-lite'),
                        )
                    ),
                ),
            ),
        ),
        'products_selection' => array(
            'title' => __('Products selection', 'sefw-lite'),
            'icon' => '',
            'children' => array(
            ),
        ),
        'layout' => array(
            'title' => __('Text and Layout', 'sefw-lite'),
            'icon' => '',
            'children' => array(
                'main_background' => array(
                    'title' => __('Main background', 'sefw-lite'),
                    'children' => array(
                        'na_main_background_color' => array(
                            'title' => __('Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => get_option( 'woocommerce_email_background_color', '#f5f5f5' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        )
                    ),
                ),
                'title' => array(
                    'title' => __('Title', 'sefw-lite'),
                    'children' => array(
                        't1_text' => array(
                            'title' => __('Text', 'sefw-lite'),
                            'type' => 'text',
                            'default' => __('You may like...', 'sefw-lite'),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't1_size' => array(
                            'title' => __('Font Size', 'sefw-lite'),
                            'type' => 'text',
                            'default' => '25px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_t1_color' => array(
                            'title' => __('Text Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_t1_bgcolor' => array(
                            'title' => __('Background Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => get_option( 'woocommerce_email_base_color', '#557da1' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        )
                    ),
                ),
                'subtitle' => array(
                    'title' => __('Introduction Text', 'sefw-lite'),
                    'children' => array(
                        'na_t2_enable' => array(
                            'title' => __('Show', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't2_text' => array(
                            'title' => __('Text', 'sefw-lite'),
                            'type' => 'textarea',
                            'default' => __('We have selected those products just for you according to your tastes. Have a look!', 'sefw-lite'),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't2_size' => array(
                            'title' => __('Font Size', 'sefw-lite'),
                            'type' => 'text',
                            'default' => '14px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_t2_color' => array(
                            'title' => __('Text Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_t2_bgcolor' => array(
                            'title' => __('Background Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => get_option( 'woocommerce_email_base_color', '#557da1' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        )
                    ),
                ),
                'product_name' => array(
                    'title' => __('Product Name', 'sefw-lite'),
                    'children' => array(
                        'product_name_size' => array(
                            'title' => __('Font size', 'sefw-lite'),
                            'type' => 'text',
                            'default' => '16px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_product_name_color' => array(
                            'title' => __('Text Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => wc_hex_lighter( get_option( 'woocommerce_email_text_color', '#505050' ), 20 ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        )
                    ),
                ),
                'price' => array(
                    'title' => __('Price', 'sefw-lite'),
                    'children' => array(
                        'na_price_enable' => array(
                            'title' => __('Show', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'price_size' => array(
                            'title' => __('Font Size', 'sefw-lite'),
                            'type' => 'text',
                            'default' => '14px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_price_color' => array(
                            'title' => __('Text Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => get_option( 'woocommerce_email_base_color', '#557da1' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        )
                    ),
                ),
                'product_description' => array(
                    'title' => __('Product Description', 'sefw-lite'),
                    'children' => array(
                        'na_product_description_enable' => array(
                            'title' => __('Show', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_description_short' => array(
                            'title' => __('Use short description', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_description_maxsize' => array(
                            'title' => __('Description Maximum Words', 'sefw-lite'),
                            'type' => 'text',
                            'default' => '15',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_description_size' => array(
                            'title' => __('Font Size', 'sefw-lite'),
                            'type' => 'text',
                            'default' => '12px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_product_description_color' => array(
                            'title' => __('Text Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => wc_hex_lighter( get_option( 'woocommerce_email_text_color', '#505050' ), 20 ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        )
                    ),
                ),
                'product_button' => array(
                    'title' => __('Product Link Button', 'sefw-lite'),
                    'children' => array(
                        'na_product_button_enable' => array(
                            'title' => __('Show', 'sefw-lite'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_button_text' => array(
                            'title' => __('Text', 'sefw-lite'),
                            'type' => 'text',
                            'default' => __('View product', 'sefw-lite'),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_button_size' => array(
                            'title' => __('Font Size', 'sefw-lite'),
                            'type' => 'text',
                            'default' => '12px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_product_button_color' => array(
                            'title' => __('Text Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'na_product_button_background_color' => array(
                            'title' => __('Background Color', 'sefw-lite'),
                            'type' => 'colorpicker',
                            'default' => get_option( 'woocommerce_email_base_color', '#557da1' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        )
                    ),
                ),
            ),
        ),
        'preview' => array(
            'title' => __('Preview', 'sefw-lite'),
            'icon' => '',
            'children' => array(
                'selection_settings' => array(
                    'title' => __('Emails Previewing', 'sefw-lite'),
                    'children' => array(
                        'test_order_id' => array(
                            'title' => __('Test Order ID', 'sefw-lite'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => __('To preview the email relating to a specific order, please fill in its ID. If not, the last order will be automatically used. Please make sure that you have at least one valid order in WooCommerce.', 'sefw-lite'),
                        )
                    ),
                )
            ),
        ),
    );
}

?>
