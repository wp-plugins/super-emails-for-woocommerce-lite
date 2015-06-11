<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Returns emails template for this plugin
 * 
 * @return array
 */

function sfew_image( $id, $size, $image_id, $image_unique_id, $image_embedded ) {
	if ( get_the_post_thumbnail( $id, array( $size, $size ) ) ) {
		if ( $image_embedded ) {
			return '<img src="cid:'.$image_unique_id.'" '.image_hwstring($size,$size).'/>';
		} else {
			return get_the_post_thumbnail( $id, array( $size, $size ) );	
		}
	} else {
		return "<div style='width:${size}px;height:${size}px;display:inline-block'></div>";
	}
}


function sefw_email_templates( $format, $layout_arg, $products_data, $image_embedded ) {

	$structure_top = array();
	$structure_top[] = " <!----> ";
	$structure_top[] = sprintf('<p style="font-size: %1$s;font-weight: 300;text-align: center;color: %2$s;background-color: %3$s;padding: 15px;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;margin-bottom:0; margin-top: 0px; line-height:100%%;-webkit-font-smoothing: antialiased;">%4$s</p>', $layout_arg['wc_se_t1_size'], $layout_arg['wc_se_t1_color'], $layout_arg['wc_se_t1_bgcolor'], $layout_arg['wc_se_t1_text']);

	$structure_top[]['t2_enable'] = sprintf('<p style="font-size: %1$s;font-weight: normal;text-align: center;color: %2$s;background-color: %3$s;padding: 20px;padding-top:0;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;margin:0;line-height: 20px;-webkit-font-smoothing: antialiased;">%4$s</p>', $layout_arg['wc_se_t2_size'], $layout_arg['wc_se_t2_color'], $layout_arg['wc_se_t2_bgcolor'], nl2br($layout_arg['wc_se_t2_text']));

	$structure_top[] = sprintf('<table style="width: 100%%;border-spacing: 0;background: %1$s;padding-top:6px"><tr style="">', $layout_arg['wc_se_main_background_color']);

	$structure_bottom = array();
	$structure_bottom[] = '</tr></table><br> <!--Super Emails for WooCommerce--> ';

	$product_title_str = '<p style="font-size:%1$s;color:%2$s;margin:0;margin-bottom:4px;text-align:center;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;">%3$s</p>';

	$product_price_str = '<p style="font-size:%1$s;color:%2$s;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;text-align:center;margin:0;margin-bottom:4px">%3$s</p>';

	$product_description_str = '<p style="font-size: %1$s;text-align: justify;padding: 0 12px;color:%2$s;margin-top: 0px;margin-bottom: 7px">%3$s</p>';

	$product_button_str = '<p style="text-align:center"><a style="font-size:%1$s;color: %2$s;background:%3$s;padding: 10px 20px;font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;display:block;width:60%%;margin:auto;text-decoration:none;margin-bottom: 12px;font-weight:bold" href="%5$s" target="_blank" >%4$s</a></p></td>';

	if ( $format == "2_1" or $format == "2_2" ) {
		
		$paddings = array(
				0 => array('left'=>'6px', 'right'=>'4px'),
				1 => array('left'=>'4px', 'right'=>'6px')
			);

		$structure_2_1 = array();
		for( $i=0; $i < count( $products_data ); $i++ ) {
			// col
			$structure_2_1[] = sprintf('<td style="padding: 0;padding-left:%1$s;padding-right:%2$s;vertical-align:top;width:50%%">', $paddings[$i]['left'], $paddings[$i]['right'] );
			$structure_2_1[] = sprintf('<a href="%1$s" target="_blank" style="font-size:0; text-decoration: none;width:242px;text-align:center">', get_permalink( $products_data[$i]['id'] ));
			$structure_2_1[] = sfew_image( $products_data[$i]['id'], 242, $products_data[$i]['image_id'], $products_data[$i]['image_unique_id'], $image_embedded );
			// title
			$structure_2_1[] = sprintf( $product_title_str, $layout_arg['wc_se_product_name_size'], $layout_arg['wc_se_product_name_color'], $products_data[$i]['title']);
			// price
			$structure_2_1[]['price_enable'] = sprintf( $product_price_str, $layout_arg['wc_se_price_size'], $layout_arg['wc_se_price_color'], $products_data[$i]['price']);

			// description
			$structure_2_1[]['product_description_enable'] = sprintf( $product_description_str, $layout_arg['wc_se_product_description_size'], $layout_arg['wc_se_product_description_color'], $products_data[$i]['description']);
			$structure_2_1[] = '</a>';
			// link
			$structure_2_1[]['product_button_enable'] = sprintf( $product_button_str, $layout_arg['wc_se_product_button_size'], $layout_arg['wc_se_product_button_color'], $layout_arg['wc_se_product_button_background_color'], $layout_arg['wc_se_product_button_text'], get_permalink( $products_data[$i]['id'] ) );
		}

		$structure_2_1 = array_merge($structure_top, $structure_2_1, $structure_bottom);
		$structure_2_2 = $structure_2_1;
	}


	if ( $format == "4_2" ) {
		$paddings = array(
				0 => array('left'=>'6px', 'right'=>'4px'),
				1 => array('left'=>'4px', 'right'=>'6px'),
				2 => array('left'=>'6px', 'right'=>'4px'),
				3 => array('left'=>'4px', 'right'=>'6px')
			);

		$structure_4_2 = array();

		for ( $i=0; $i < count( $products_data ); $i++ ) {
			$structure_4_2[] = sprintf('<td style="padding: 0;padding-left:%1$s;padding-right:%2$s;vertical-align:top;width:50%%">', $paddings[$i]['left'], $paddings[$i]['right'] );
			$structure_4_2[] = sprintf('<a href="%1$s" target="_blank" style="font-size:0; text-decoration: none;width:242px;text-align:center">', get_permalink( $products_data[$i]['id'] ));
			
			$structure_4_2[] = sfew_image( $products_data[$i]['id'], 242, $products_data[$i]['image_id'], $products_data[$i]['image_unique_id'], $image_embedded );
			$structure_4_2[] = sprintf( $product_title_str, $layout_arg['wc_se_product_name_size'], $layout_arg['wc_se_product_name_color'], $products_data[$i]['title']);

			$structure_4_2[]['price_enable'] = sprintf( $product_price_str, $layout_arg['wc_se_price_size'], $layout_arg['wc_se_price_color'], $products_data[$i]['price']);

			$structure_4_2[]['product_description_enable'] = sprintf( $product_description_str, $layout_arg['wc_se_product_description_size'], $layout_arg['wc_se_product_description_color'], $products_data[$i]['description']);

			$structure_4_2[] = '</a>';
			
			$structure_4_2[]['product_button_enable'] = sprintf( $product_button_str, $layout_arg['wc_se_product_button_size'], $layout_arg['wc_se_product_button_color'], $layout_arg['wc_se_product_button_background_color'], $layout_arg['wc_se_product_button_text'], get_permalink( $products_data[$i]['id'] ) );
			if ( $i == 1 ) {
				$structure_4_2[] = '</tr><tr>';
			}
		}

		$structure_4_2 = array_merge($structure_top, $structure_4_2, $structure_bottom);
	}
	if ( $format == "3_1" or $format == "3_2") {
		
		$paddings = array(
				0 => array('left'=>'6px', 'right'=>'4px'),
				1 => array('left'=>'5px', 'right'=>'5px'),
				2 => array('left'=>'4px', 'right'=>'6px')
			);

		$structure_3_1 = array();
		
		for ( $i=0; $i < count( $products_data ); $i++ ) {
			$structure_3_1[] = sprintf('<td style="padding: 0;padding-left:%1$s;padding-right:%2$s;vertical-align:top;width:33%%">', $paddings[$i]['left'], $paddings[$i]['right'] );
			$structure_3_1[] = sprintf('<a href="%1$s" target="_blank" style="font-size:0; text-decoration: none;width:158px;text-align:center">', get_permalink( $products_data[$i]['id'] ));
			
			$structure_3_1[] = sfew_image( $products_data[$i]['id'], 158, $products_data[$i]['image_id'], $products_data[$i]['image_unique_id'], $image_embedded );
			$structure_3_1[] = sprintf( $product_title_str, $layout_arg['wc_se_product_name_size'], $layout_arg['wc_se_product_name_color'], $products_data[$i]['title']);

			$structure_3_1[]['price_enable'] = sprintf( $product_price_str, $layout_arg['wc_se_price_size'], $layout_arg['wc_se_price_color'], $products_data[$i]['price']);

			$structure_3_1[]['product_description_enable'] = sprintf( $product_description_str, $layout_arg['wc_se_product_description_size'], $layout_arg['wc_se_product_description_color'], $products_data[$i]['description']);

			$structure_3_1[] = '</a>';
			
			$structure_3_1[]['product_button_enable'] = sprintf( $product_button_str, $layout_arg['wc_se_product_button_size'], $layout_arg['wc_se_product_button_color'], $layout_arg['wc_se_product_button_background_color'], $layout_arg['wc_se_product_button_text'], get_permalink( $products_data[$i]['id'] ) );
		}

		$structure_3_1 = array_merge($structure_top, $structure_3_1, $structure_bottom);
		$structure_3_2 = $structure_3_1;
	}
	if ( $format == "6_2" or $format == "6_1") {
		
		$paddings = array(
				0 => array('left'=>'6px', 'right'=>'4px'),
				1 => array('left'=>'5px', 'right'=>'5px'),
				2 => array('left'=>'4px', 'right'=>'6px'),
				3 => array('left'=>'6px', 'right'=>'4px'),
				4 => array('left'=>'5px', 'right'=>'5px'),
				5 => array('left'=>'4px', 'right'=>'6px')
			);

		$structure_6_2 = array();

		for ( $i=0; $i < count( $products_data ); $i++ ) {
			$structure_6_2[] = sprintf('<td style="padding: 0;padding-left:%1$s;padding-right:%2$s;vertical-align:top;width:33%%">', $paddings[$i]['left'], $paddings[$i]['right'] );
			$structure_6_2[] = sprintf('<a href="%1$s" target="_blank" style="font-size:0; text-decoration: none;width:158px;text-align:center">', get_permalink( $products_data[$i]['id'] ));
			
			$structure_6_2[] = sfew_image( $products_data[$i]['id'], 158, $products_data[$i]['image_id'], $products_data[$i]['image_unique_id'], $image_embedded );
			$structure_6_2[] = sprintf( $product_title_str, $layout_arg['wc_se_product_name_size'], $layout_arg['wc_se_product_name_color'], $products_data[$i]['title']);

			$structure_6_2[]['price_enable'] = sprintf( $product_price_str, $layout_arg['wc_se_price_size'], $layout_arg['wc_se_price_color'], $products_data[$i]['price']);

			$structure_6_2[]['product_description_enable'] = sprintf( $product_description_str, $layout_arg['wc_se_product_description_size'], $layout_arg['wc_se_product_description_color'], $products_data[$i]['description']);

			$structure_6_2[] = '</a>';
			
			$structure_6_2[]['product_button_enable'] = sprintf( $product_button_str, $layout_arg['wc_se_product_button_size'], $layout_arg['wc_se_product_button_color'], $layout_arg['wc_se_product_button_background_color'], $layout_arg['wc_se_product_button_text'], get_permalink( $products_data[$i]['id'] ) );
			if ( $i == 2 ) {
				$structure_6_2[] = '</tr><tr>';
			}
		}
		
		$structure_6_2 = array_merge($structure_top, $structure_6_2, $structure_bottom);
		$structure_6_1 = $structure_6_2;
	}

	if ( $format == "4_1") {
		
		$paddings = array(
				0 => array('left'=>'6px', 'right'=>'4px'),
				1 => array('left'=>'5px', 'right'=>'5px'),
				2 => array('left'=>'5px', 'right'=>'5px'),
				3 => array('left'=>'4px', 'right'=>'6px')
			);

		$structure_4_1 = array();
		
		for ( $i=0; $i < count( $products_data ); $i++ ) {
			$structure_4_1[] = sprintf('<td style="padding: 0;padding-left:%1$s;padding-right:%2$s;vertical-align:top;width:25%%">', $paddings[$i]['left'], $paddings[$i]['right'] );
			$structure_4_1[] = sprintf('<a href="%1$s" target="_blank" style="font-size:0; text-decoration: none;width:116px;text-align:center">', get_permalink( $products_data[$i]['id'] ));
			
			$structure_4_1[] = sfew_image( $products_data[$i]['id'], 116, $products_data[$i]['image_id'], $products_data[$i]['image_unique_id'], $image_embedded );
			$structure_4_1[] = sprintf( $product_title_str, $layout_arg['wc_se_product_name_size'], $layout_arg['wc_se_product_name_color'], $products_data[$i]['title']);

			$structure_4_1[]['price_enable'] = sprintf( $product_price_str, $layout_arg['wc_se_price_size'], $layout_arg['wc_se_price_color'], $products_data[$i]['price']);

			$structure_4_1[]['product_description_enable'] = sprintf( $product_description_str, $layout_arg['wc_se_product_description_size'], $layout_arg['wc_se_product_description_color'], $products_data[$i]['description']);

			$structure_4_1[] = '</a>';
			
			$structure_4_1[]['product_button_enable'] = sprintf( $product_button_str, $layout_arg['wc_se_product_button_size'], $layout_arg['wc_se_product_button_color'], $layout_arg['wc_se_product_button_background_color'], $layout_arg['wc_se_product_button_text'], get_permalink( $products_data[$i]['id'] ) );
		}

		$structure_4_1 = array_merge($structure_top, $structure_4_1, $structure_bottom);
	}
	if ( $format == "8_2" or $format == "8_1") {

		$paddings = array(
				0 => array('left'=>'6px', 'right'=>'4px'),
				1 => array('left'=>'5px', 'right'=>'5px'),
				2 => array('left'=>'5px', 'right'=>'5px'),
				3 => array('left'=>'4px', 'right'=>'6px'),
				4 => array('left'=>'6px', 'right'=>'4px'),
				5 => array('left'=>'5px', 'right'=>'5px'),
				6 => array('left'=>'5px', 'right'=>'5px'),
				7 => array('left'=>'4px', 'right'=>'6px')
			);

		$structure_8_2 = array();
		
		for ( $i=0; $i < count( $products_data ); $i++ ) {
			$structure_8_2[] = sprintf('<td style="padding: 0;padding-left:%1$s;padding-right:%2$s;vertical-align:top;width:25%%">', $paddings[$i]['left'], $paddings[$i]['right'] );
			$structure_8_2[] = sprintf('<a href="%1$s" target="_blank" style="font-size:0; text-decoration: none;width:116px;text-align:center">', get_permalink( $products_data[$i]['id'] ));
			
			$structure_8_2[] = sfew_image( $products_data[$i]['id'], 116, $products_data[$i]['image_id'], $products_data[$i]['image_unique_id'], $image_embedded );
			$structure_8_2[] = sprintf( $product_title_str, $layout_arg['wc_se_product_name_size'], $layout_arg['wc_se_product_name_color'], $products_data[$i]['title']);

			$structure_8_2[]['price_enable'] = sprintf( $product_price_str, $layout_arg['wc_se_price_size'], $layout_arg['wc_se_price_color'], $products_data[$i]['price']);

			$structure_8_2[]['product_description_enable'] = sprintf( $product_description_str, $layout_arg['wc_se_product_description_size'], $layout_arg['wc_se_product_description_color'], $products_data[$i]['description']);

			$structure_8_2[] = '</a>';
			
			$structure_8_2[]['product_button_enable'] = sprintf( $product_button_str, $layout_arg['wc_se_product_button_size'], $layout_arg['wc_se_product_button_color'], $layout_arg['wc_se_product_button_background_color'], $layout_arg['wc_se_product_button_text'], get_permalink( $products_data[$i]['id'] ) );
			if ( $i == 3 ) {
				$structure_8_2[] = '</tr><tr>';
			}
		}	

		$structure_8_2 = array_merge($structure_top, $structure_8_2, $structure_bottom);
		$structure_8_1 = $structure_8_2;
	}

	return ${"structure_".$format};
}