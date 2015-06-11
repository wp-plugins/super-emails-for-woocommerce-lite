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

function sfew_settings()
{
    return array(
        'general_settings' => array(
            'title' => __('General settings', 'sefw'),
            'icon' => '',
            'children' => array(
                'main_settings' => array(
                    'title' => __('Integration of your products', 'sefw'),
                    'children' => array(
                        'enabled' => array(
                            'title' => __('Activate', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Activate WC Super Emails plugin</span>', 'sefw'),
                        ),
                        'products_quantity' => array(
                            'title' => __('Number of products', 'sefw'),
                            'type' => 'dropdown',
                            'default' => 3,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                2 => '2',
                                3 => '3',
                                4 => '4',
                                6 => '6',
                                6 => '6',
                                8 => '8',
                            ),
                            'hint' => __('<span>Number of products to be displayed per email</span>', 'sefw'),
                        ),
                        'number_rows' => array(
                            'title' => __('Number of rows', 'sefw'),
                            'type' => 'dropdown',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                1 => '1',
                                2 => '2',
                            ),
                            'hint' => __('<span>Display 1 or 2 rows of products per email.<br>For 2 rows, number of products must be at least 4.</span>', 'sefw'),
                        ),
                        'enable_on_new_order' => array(
                            'title' => __('Enable with Processing order', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Display suggested products on Processing order emails</span>', 'sefw'),
                        ),
                        'enable_on_order_complete' => array(
                            'title' => __('Enable with Completed order', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Display suggested products on Completed order emails</span>', 'sefw'),
                        ),
                        'enable_on_customer_note' => array(
                            'title' => __('Enable with Customer note', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Display suggested products on Customer note emails</span>', 'sefw'),
                        ),
                        'embedded_images' => array(
                            'title' => __('Embed images', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<span>Integrate images in emails. May not work on your server</span>', 'sefw'),
                        )
                    ),
                ),
            ),
        ),
        'products_selection' => array(
            'title' => __('Products selection', 'sefw'),
            'icon' => '',
            'children' => array(
                'selection_settings' => array(
                    'title' => __('Specific Products', 'sefw'),
                    'children' => array(
                        'specific_ids' => array(
                            'title' => __('Select your products', 'sefw'),
                            'type' => 'product_select',
                            'default' => array(),
                            'validation' => array(
                                'rule' => 'product',
                                'empty' => true
                            )
                        ),
                        'selection_order' => array(
                            'title' => '',
                            'type' => 'hidden',
                            'default' => 'up_sells,cross_sells,related_products,specific_products,random_shop',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            )
                        ),
                        'upsells_max' => array(
                            'title' => '',
                            'type' => 'hidden',
                            'default' => '2',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            )
                        ),
                        'crosssells_max' => array(
                            'title' => '',
                            'type' => 'hidden',
                            'default' => '2',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            )
                        ),
                        'related_max' => array(
                            'title' => '',
                            'type' => 'hidden',
                            'default' => '2',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            )
                        ),
                        'randomshop_max' => array(
                            'title' => '',
                            'type' => 'hidden',
                            'default' => '8',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            )
                        )
                    ),
                )
            ),
        ),
        'layout' => array(
            'title' => __('Text and Layout', 'sefw'),
            'icon' => '',
            'children' => array(
                'main_background' => array(
                    'title' => __('Main background', 'sefw'),
                    'children' => array(
                        'main_background_color' => array(
                            'title' => __('Color', 'sefw'),
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
                    'title' => __('Title', 'sefw'),
                    'children' => array(
                        't1_text' => array(
                            'title' => __('Text', 'sefw'),
                            'type' => 'text',
                            'default' => __('You may like...', 'sefw'),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't1_size' => array(
                            'title' => __('Font Size', 'sefw'),
                            'type' => 'text',
                            'default' => '25px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't1_color' => array(
                            'title' => __('Text Color', 'sefw'),
                            'type' => 'colorpicker',
                            'default' => wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't1_bgcolor' => array(
                            'title' => __('Background Color', 'sefw'),
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
                    'title' => __('Introduction Text', 'sefw'),
                    'children' => array(
                        't2_enable' => array(
                            'title' => __('Show', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't2_text' => array(
                            'title' => __('Text', 'sefw'),
                            'type' => 'textarea',
                            'default' => __('We have selected those products just for you according to your tastes. Have a look!', 'sefw'),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't2_size' => array(
                            'title' => __('Font Size', 'sefw'),
                            'type' => 'text',
                            'default' => '14px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't2_color' => array(
                            'title' => __('Text Color', 'sefw'),
                            'type' => 'colorpicker',
                            'default' => wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        't2_bgcolor' => array(
                            'title' => __('Background Color', 'sefw'),
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
                    'title' => __('Product Name', 'sefw'),
                    'children' => array(
                        'product_name_size' => array(
                            'title' => __('Font size', 'sefw'),
                            'type' => 'text',
                            'default' => '16px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_name_color' => array(
                            'title' => __('Text Color', 'sefw'),
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
                    'title' => __('Price', 'sefw'),
                    'children' => array(
                        'price_enable' => array(
                            'title' => __('Show', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'price_size' => array(
                            'title' => __('Font Size', 'sefw'),
                            'type' => 'text',
                            'default' => '14px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'price_color' => array(
                            'title' => __('Text Color', 'sefw'),
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
                    'title' => __('Product Description', 'sefw'),
                    'children' => array(
                        'product_description_enable' => array(
                            'title' => __('Show', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_description_short' => array(
                            'title' => __('Use short description', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_description_maxsize' => array(
                            'title' => __('Description Maximum Words', 'sefw'),
                            'type' => 'text',
                            'default' => '15',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_description_size' => array(
                            'title' => __('Font Size', 'sefw'),
                            'type' => 'text',
                            'default' => '12px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_description_color' => array(
                            'title' => __('Text Color', 'sefw'),
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
                    'title' => __('Product Link Button', 'sefw'),
                    'children' => array(
                        'product_button_enable' => array(
                            'title' => __('Show', 'sefw'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_button_text' => array(
                            'title' => __('Text', 'sefw'),
                            'type' => 'text',
                            'default' => __('View product', 'sefw'),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_button_size' => array(
                            'title' => __('Font Size', 'sefw'),
                            'type' => 'text',
                            'default' => '12px',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_button_color' => array(
                            'title' => __('Text Color', 'sefw'),
                            'type' => 'colorpicker',
                            'default' => wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' ),
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => '',
                        ),
                        'product_button_background_color' => array(
                            'title' => __('Background Color', 'sefw'),
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
            'title' => __('Preview', 'sefw'),
            'icon' => '',
            'children' => array(
                'selection_settings' => array(
                    'title' => __('Emails Previewing', 'sefw'),
                    'children' => array(
                        'test_order_id' => array(
                            'title' => __('Test Order ID', 'sefw'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'text',
                                'empty' => false
                            ),
                            'hint' => __('To preview the email relating to a specific order, please fill in its ID. If not, the last order will be automatically used. Please make sure that you have at least one valid order in WooCommerce.', 'sefw'),
                        )
                    ),
                )
            ),
        ),
    );
}

?>
