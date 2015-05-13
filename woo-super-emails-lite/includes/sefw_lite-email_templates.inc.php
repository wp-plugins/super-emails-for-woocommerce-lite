<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Returns emails template for this plugin
 * 
 * @return array
 */

function sfew_lite_image( $id, $size, $image_id, $image_unique_id ) {
	if ( get_the_post_thumbnail( $id, array( $size, $size ) ) ) {
		return get_the_post_thumbnail( $id, array( $size, $size ) );
	} else {
		return "<div style='width:${size}px;height:${size}px;display:inline-block'></div>";
	}
}


function sefw_lite_email_templates( $layout_arg, $products_data ) {

	$layout_arg['sefw_lite_t1_color'] = wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' );
	$layout_arg['sefw_lite_t1_bgcolor'] = get_option( 'woocommerce_email_base_color', '#557da1' );
	$layout_arg['sefw_lite_t2_color'] = wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' );
	$layout_arg['sefw_lite_t2_bgcolor'] = get_option( 'woocommerce_email_base_color', '#557da1' );
	$layout_arg['sefw_lite_main_background_color'] = get_option( 'woocommerce_email_background_color', '#f5f5f5' );
	$layout_arg['sefw_lite_product_name_color'] = wc_hex_lighter( get_option( 'woocommerce_email_text_color', '#505050' ), 20 );
	$layout_arg['sefw_lite_price_color'] = get_option( 'woocommerce_email_base_color', '#557da1' );
	$layout_arg['sefw_lite_product_description_color'] = wc_hex_lighter( get_option( 'woocommerce_email_text_color', '#505050' ), 20 );
	$layout_arg['sefw_lite_product_button_color'] = wc_light_or_dark( get_option( 'woocommerce_email_base_color', '#557da1' ), '#202020', '#ffffff' );
	$layout_arg['sefw_lite_product_button_background_color'] = get_option( 'woocommerce_email_base_color', '#557da1' );
	$structure_top = array();
	$structure_top[] = " <!----> ";
	$structure_top[] = sprintf('<p style="font-size: %1$s;font-weight: 300;text-align: center;color: %2$s;background-color: %3$s;padding: 15px;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;margin-bottom:0; margin-top: 0px; line-height:100%%;-webkit-font-smoothing: antialiased;">%4$s</p>', $layout_arg['sefw_lite_t1_size'], $layout_arg['sefw_lite_t1_color'], $layout_arg['sefw_lite_t1_bgcolor'], $layout_arg['sefw_lite_t1_text']);

	$structure_top[] = sprintf('<p style="font-size: %1$s;font-weight: normal;text-align: center;color: %2$s;background-color: %3$s;padding: 20px;padding-top:0;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;margin:0;line-height: 20px;-webkit-font-smoothing: antialiased;">%4$s</p>', $layout_arg['sefw_lite_t2_size'], $layout_arg['sefw_lite_t2_color'], $layout_arg['sefw_lite_t2_bgcolor'], nl2br($layout_arg['sefw_lite_t2_text']));

	$structure_top[] = sprintf('<table style="width: 100%%;border-spacing: 0;background: %1$s;padding-top:6px"><tr style="">', $layout_arg['sefw_lite_main_background_color']);

	$structure_bottom = array();
	$structure_bottom[] = '</tr></table><br> <!--Super Emails for WooCommerce--> ';

	$product_title_str = '<p style="font-size:%1$s;color:%2$s;margin:0;margin-bottom:4px;text-align:center;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;">%3$s</p>';

	$product_price_str = '<p style="font-size:%1$s;color:%2$s;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;text-align:center;margin:0;margin-bottom:4px">%3$s</p>';

	$product_description_str = '<p style="font-size: %1$s;text-align: justify;padding: 0 12px;color:%2$s;margin-top: 0px;margin-bottom: 7px">%3$s</p>';

	$product_button_str = '<p style="text-align:center"><a style="font-size:%1$s;color: %2$s;background:%3$s;padding: 10px 20px;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;display:block;width:60%%;margin:auto;text-decoration:none;margin-bottom: 12px;font-weight:bold" href="%5$s" target="_blank" >%4$s</a></p></td>';

		
	$paddings = array(
			0 => array('left'=>'6px', 'right'=>'4px'),
			1 => array('left'=>'5px', 'right'=>'5px'),
			2 => array('left'=>'4px', 'right'=>'6px')
		);

	$structure_data = array();
	
	for ( $i=0; $i < 3; $i++ ) {
		$structure_data[] = sprintf('<td style="padding: 0;padding-left:%1$s;padding-right:%2$s;vertical-align:top;width:33%%">', $paddings[$i]['left'], $paddings[$i]['right'] );
		$structure_data[] = sprintf('<a href="%1$s" target="_blank" style="font-size:0; text-decoration: none;width:158px;text-align:center">', get_permalink( $products_data[$i]['id'] ));
		
		$structure_data[] = sfew_lite_image( $products_data[$i]['id'], 158, $products_data[$i]['image_id'], $products_data[$i]['image_unique_id'] );
		$structure_data[] = sprintf( $product_title_str, $layout_arg['sefw_lite_product_name_size'], $layout_arg['sefw_lite_product_name_color'], $products_data[$i]['title']);

		$structure_data[] = sprintf( $product_price_str, $layout_arg['sefw_lite_price_size'], $layout_arg['sefw_lite_price_color'], $products_data[$i]['price']);

		$structure_data[] = sprintf( $product_description_str, $layout_arg['sefw_lite_product_description_size'], $layout_arg['sefw_lite_product_description_color'], $products_data[$i]['description']);

		$structure_data[] = '</a>';
		
		$structure_data[] = sprintf( $product_button_str, $layout_arg['sefw_lite_product_button_size'], $layout_arg['sefw_lite_product_button_color'], $layout_arg['sefw_lite_product_button_background_color'], $layout_arg['sefw_lite_product_button_text'], get_permalink( $products_data[$i]['id'] ) );
	}

	$structure_data = array_merge($structure_top, $structure_data, $structure_bottom);

	return $structure_data;
}