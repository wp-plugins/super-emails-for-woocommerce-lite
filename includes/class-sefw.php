<?php
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 

if (!class_exists('SEFW')) {
	class SEFW {
		public function __construct() {
			
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2.0', '<' ) ) {
				add_action( 'admin_notices', array($this, 'sefw_fallback_notice' ) );
		        return;
			}
			// configuration
			$this->id = 'sefw';
			// this is the title in WooCommerce Email settings
			$this->title = 'Super Emails';

			require sefw_PLUGIN_PATH . '/includes/wc_se-structure.inc.php';
			require sefw_PLUGIN_PATH . '/includes/wc_se-email_templates.inc.php';
			
			// Add settings page
			if (is_admin()) {
			    add_action('admin_menu', array($this, 'add_admin_menu'));
			    add_action('admin_init', array($this, 'admin_construct'));
			    if (preg_match('/page=sefw/i', $_SERVER['QUERY_STRING'])) {
					add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
			    }
			}
			
			// Load the settings.
			$this->get_settings();

			// Load options
			$this->opt = $this->get_options();

			if ( $this->opt['wc_se_enabled'] ) {
				add_action( 'woocommerce_email', array( $this, 'get_emails_list' ) );
				add_action( 'woocommerce_email_header',  array( $this, 'get_notification_email_type' ) );
				add_action( 'woocommerce_email_order_meta', array( $this, 'get_order_id' ), 10);
			}
			add_action( 'admin_init', array( $this, 'preview_emails' ) );
		}

		function sefw_fallback_notice() {
			echo '<div class="error"><p>WooCommerce 2.2 is required for Super Emails for WooCommerce to run, so please update to the latest stable version of WooCommerce.</p></div>';
		}

		/**
	     * Add content to the WC emails.
	     *
	     * @access public
	     * @param WC_Order $order
	     * @param bool $sent_to_admin
	     * @param bool $plain_text
	     */


	 	function get_emails_list( $email ) {
	 		$this->emails_list[$email->emails['WC_Email_Customer_Processing_Order']->heading] = 'WC_Email_Customer_Processing_Order';
	 		$this->emails_list[$email->emails['WC_Email_Customer_Completed_Order']->heading] = 'WC_Email_Customer_Completed_Order';
	 		$this->emails_list[$email->emails['WC_Email_Customer_Note']->heading] = 'WC_Email_Customer_Note';
	 	}

	 	public function get_notification_email_type($heading) {
	 		if ( ! array_key_exists( $heading, $this->emails_list ) ) {
	 			return;
	 		}
	 		$email_type = $this->emails_list[$heading];

	 		if (
	 			( $this->emails_list[$heading] == 'WC_Email_Customer_Processing_Order' and  $this->opt['wc_se_enable_on_new_order'] ) 
	 			or
	 			( $this->emails_list[$heading] == 'WC_Email_Customer_Completed_Order' and  $this->opt['wc_se_enable_on_order_complete'] )
	 			or
	 			( $this->emails_list[$heading] == 'WC_Email_Customer_Note' and  $this->opt['wc_se_enable_on_customer_note'] ) 
	 		) {
	 			add_filter( 'woocommerce_email_footer_text', array( $this, 'select_products_to_add' ), 9);
	 		}
	 	}
	 	public function get_order_id($order) {
	 		$this->order_id = $order->id;
	 	}



	 	function embed_images( $mail ) {
	 		foreach ($this->promoted_products_info as $key => $value) {
	 			if ( $value['image_id'] ) {
	 				switch($this->opt['wc_se_products_quantity'])
	 				{
	 				    case '2';
	 				    	$size = 242;
	 				    	break;
	 				    case '3';
	 				    case '6';
	 				    	$size = 158;
	 				    	break;
	 				    case '4';
	 				        if ( $this->opt['wc_se_number_rows'] == 1) {
	 				        	$size = 116;
	 				        }else{
	 				        	$size = 242;
	 				        }
	 				    	break;
	 				    case '8';
	 				    	$size = 116;
	 				    	break;
	 				    default;
	 				        $size = 242;
	 				    	break;
	 				}
	 				$image_full = wp_get_attachment_image_src($value['image_id'], 'full');
	 				$image_resized = image_get_intermediate_size($value['image_id'], array( $size, $size ));
	 				$full_name = wp_basename($image_full[0]);
	 				$resized_name = $image_resized['file'];
	 				$resized_path = str_replace( $full_name, $resized_name, get_attached_file( $value['image_id']) );
	 					
	 				$mail->AddEmbeddedImage($resized_path, $value['image_unique_id'], get_the_title($value['image_id']));	
	 			}
	 		}
	 	}

	 	public function display_promotion_products( Array $promoted_products ){

	 		// get promoted products data
	 		$promoted_products_info = array();
	 		$index = 0;
	 		if ( $this->opt['wc_se_embedded_images'] and ! isset( $this->email_preview ) ) {
	 			$embedded_images = true;
	 		} else {
	 			$embedded_images = false;
	 		}
	 		foreach ( $promoted_products as $product_id ) {
	 			$promoted_products_info[$index]['id'] = $product_id;
	 			if ( get_post_thumbnail_id( $product_id ) ) {
	 				$promoted_products_info[$index]['image_id'] = get_post_thumbnail_id( $product_id );
	 				$promoted_products_info[$index]['image_path'] = get_attached_file( $promoted_products_info[$index]['image_id'] );
	 				$promoted_products_info[$index]['image_unique_id'] = uniqid();
	 				$promoted_products_info[$index]['image_type'] = get_post_mime_type( $promoted_products_info[$index]['image_id'] );
	 			} else {
	 				$promoted_products_info[$index]['image_id'] = 0;
	 				$promoted_products_info[$index]['image_path'] = 0;
	 				$promoted_products_info[$index]['image_unique_id'] = 0;
	 				$promoted_products_info[$index]['image_type'] = 0;
	 			}
	 			
	 			
	 			$product = new WC_Product( $product_id );
	 			$promoted_products_info[$index]['title'] = $product->get_title();
	 			$promoted_products_info[$index]['description'] = $product->post->post_content;
	 			$promoted_products_info[$index]['short_description'] = $product->post->post_excerpt;

	 			if ( $this->opt['wc_se_product_description_short']  and  $product->post->post_excerpt) {
	 				$promoted_products_info[$index]['description'] = wp_trim_words( $product->post->post_excerpt, $this->opt['wc_se_product_description_maxsize']);
	 			}else{
	 				$promoted_products_info[$index]['description'] = wp_trim_words( $product->post->post_content, $this->opt['wc_se_product_description_maxsize']);
	 			}
	 			if ( $embedded_images ) {
	 				add_filter( 'phpmailer_init', array( $this, 'embed_images' ) );	
	 			}
	 			
	 			$promoted_products_info[$index]['price'] = $product->get_price_html();
	 			$index++;
	 		}

	 		$products_structure = sefw_email_templates($this->opt['wc_se_products_quantity'].'_'.$this->opt['wc_se_number_rows'], $this->opt, $promoted_products_info, $embedded_images	);
	 		$this->promoted_products_info = $promoted_products_info;
	 		$products_structure_out = array();
	 		foreach ($products_structure as $key => $value) {
	 			if ( is_array( $value ) ) {
	 				$first_key = array_keys( $value )[0];
	 				if ( $this->opt['wc_se_'.$first_key] ) {
	 					$products_structure_out[] = $value[$first_key];	
	 				}
	 			}else{
	 				$products_structure_out[] = $value;
	 			}
	 		}

	 		echo implode( '', $products_structure_out);
	 	}
	 	
	 	public function get_products_from_order( $order_id ) {
	 		$order = new WC_Order( $order_id );
	 		$items = $order->get_items();
	 		
	 		foreach ( $items as $item ) {
	 			$this->cart_products_data[] = $item['product_id'];
	 		    $product = new WC_Product( $item['product_id'] );
	 		    $this->get_promoted_products( $product );
	 		}

	 		$this->display_promotion_products( $this->promoted_products_data );
	 	}

	 	function get_promoted_products( $product ) {
	 		if ( ! isset( $this->promoted_products_data ) ) {
	 			$this->promoted_products_data = Array();
	 		}
	 		$this->new_data = Array();
	 		$this->current_product = $product;
	 		$wc_se_selection_orders = explode( ',', $this->opt['wc_se_selection_order'] );

	 		foreach ( $wc_se_selection_orders as $wc_se_selection_orders ){
	 			call_user_func( array($this, $wc_se_selection_orders.'_getter') );
	 			call_user_func( array($this, 'clean_data') );
	 		}

	 		$this->promoted_products_data = array_chunk( $this->promoted_products_data , $this->opt['wc_se_products_quantity'], true );
	 		$this->promoted_products_data = $this->promoted_products_data[0];
	 	}
	 	function specific_products_getter() {
	 		$this->max_data = 100;
	 		$specific_products = $this->opt['wc_se_specific_ids'];
	 		if ( count( $specific_products ) ) {
	 			$this->new_data = explode( ',', $specific_products );	
	 		}
	 	}
	 	function up_sells_getter() {
	 		$this->max_data = $this->opt['wc_se_upsells_max'];
	 		$this->new_data = $this->current_product->get_upsells();
	 	}
	 	function cross_sells_getter() {
	 		$this->max_data = $this->opt['wc_se_crosssells_max'];
	 		$this->new_data = $this->current_product->get_cross_sells();
	 		
	 	}
	 	function related_products_getter() {
	 		$this->max_data = $this->opt['wc_se_related_max'];
	 		$this->new_data = $this->current_product->get_related( $this->max_data );
	 	}
	 	function random_shop_getter() {
	 		$this->max_data = $this->opt['wc_se_randomshop_max'];
	 		$args = array(
	 		    'posts_per_page'   => 10,
	 		    'orderby'          => 'rand',
	 		    'post_type'        => 'product',
	 		    'fields'           => 'ids',
	 		    'id__not_in'   =>  $this->promoted_products_data
	 		); 

	 		$random_products = get_posts( $args );
	 		$this->new_data = $random_products;
	 	}

	 	function clean_data()  {
			if ( isset($this->new_data[0]) and $this->new_data[0]) {
				// get only new products
				$this->new_data = array_diff( $this->new_data , $this->promoted_products_data, $this->cart_products_data );
				if( count( $this->new_data ) ) {
					// limit new products size
					$this->new_data = array_chunk( $this->new_data , $this->max_data, true );
					$this->new_data = $this->new_data[0];

					// concate products
					$this->promoted_products_data = array_merge( $this->promoted_products_data, $this->new_data);
				}
			}
	 	}


	 	public function select_products_to_add( $email_footer ){

	 		$this->get_products_from_order($this->order_id);

	 		// display default footer
	 		echo $email_footer;

	 	}
	 	/******************************/
	 	public function options($name, $split_by_page = false)
	 	{
	 	    $results = array();

	 	    // Iterate over settings array and extract values
	 	    foreach ($this->settings as $page => $page_value) {
	 	        $page_options = array();

	 	        foreach ($page_value['children'] as $section => $section_value) {
	 	            foreach ($section_value['children'] as $field => $field_value) {
	 	                if (isset($field_value[$name])) {
	 	                    $page_options['wc_se_' . $field] = $field_value[$name];
	 	                }
	 	            }
	 	        }

	 	        $results[preg_replace('/_/', '-', $page)] = $page_options;
	 	    }

	 	    $final_results = array();

	 	    if (!$split_by_page) {
	 	        foreach ($results as $value) {
	 	            $final_results = array_merge($final_results, $value);
	 	        }
	 	    }
	 	    else {
	 	        $final_results = $results;
	 	    }
	 	    
	 	    return $final_results;
	 	}

	 	/**
	 	 * Get array of section info strings
	 	 * 
	 	 * @access public
	 	 * @return array
	 	 */
	 	public function get_section_info()
	 	{
	 	    $results = array();

	 	    // Iterate over settings array and extract values
	 	    foreach ($this->settings as $page_value) {
	 	        foreach ($page_value['children'] as $section => $section_value) {
	 	            if (isset($section_value['info'])) {
	 	                $results[$section] = $section_value['info'];
	 	            }
	 	        }
	 	    }

	 	    return $results;
	 	}

	 	/*
	 	 * Get plugin options set by user
	 	 * 
	 	 * @access public
	 	 * @return array
	 	 */
	 	public function get_options()
	 	{
	 	    $saved_options = get_option('sefw_lite_options', $this->options('default'));

	 	    if (is_array($saved_options)) {
	 	        return array_merge($this->options('default'), $saved_options);
	 	    }
	 	    else {
	 	        return $this->options('default');
	 	    }
	 	}

	 	/*
	 	 * Update options
	 	 * 
	 	 * @access public
	 	 * @return bool
	 	 */
	 	public function update_options($args = array())
	 	{
	 	    return update_option('sefw_lite_options', array_merge($this->get_options(), $args));
	 	}

	 	/**
	 	 * Add link to admin page under Woocommerce menu
	 	 * 
	 	 * @access public
	 	 * @return void
	 	 */
	 	public function add_admin_menu()
	 	{            
	 	    global $current_user;
	 	    get_currentuserinfo();
	 	    $user_roles = $current_user->roles;
	 	    $user_role = array_shift($user_roles);

	 	    if (!in_array($user_role, array('administrator', 'shop_manager'))) {
	 	        return;
	 	    }

	 	    global $submenu;

	 	    if (isset($submenu['woocommerce'])) {
	 	        add_submenu_page(
	 	            'woocommerce',
	 	            __('Super Emails for WooCommerce', 'sefw'),
	 	            __('Super Emails', 'sefw'),
	 	            'edit_posts',
	 	            'sefw',
	 	            array($this, 'set_up_admin_page')
	 	        );
	 	    }
	 	}

	 	function set_up_admin_page(){
	 		// Print notices
	 		settings_errors('wc_se');

	 		$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general_settings';
	 		$current_tab = isset($this->settings[$current_tab]) ? $current_tab : 'general_settings';

	 		// Print page tabs
	 		$this->render_tabs($current_tab);

	 		// Print page content
	 		$this->render_page($current_tab);
	 	}

	 	public function admin_construct()
	 	{
	 		global $current_user;

	 		get_currentuserinfo();
	 		$user_roles = $current_user->roles;
	 		$user_role = array_shift($user_roles);

	 		if (!in_array($user_role, array('administrator', 'shop_manager'))) {
	 		    return;
	 		}

	 		// Iterate pages
	 		foreach ($this->settings as $page => $page_value) {

	 		    register_setting(
	 		        'wc_se_opt_group_' . $page,               // Option group
	 		        'sefw_lite_options',                          // Option name
	 		        array($this, 'options_validate')            // Sanitize
	 		    );

	 		    // Iterate sections
	 		    foreach ($page_value['children'] as $section => $section_value) {
	 		    	//echo 'wc_se-admin-' . str_replace('_', '-', $page).PHP_EOL;
	 		        add_settings_section(
	 		            $section,
	 		            $section_value['title'],
	 		            array($this, 'render_section_info'),
	 		            'sefw-admin-' . str_replace('_', '-', $page)
	 		        );

	 		        foreach ($section_value['children'] as $field => $field_value) {
	 		            add_settings_field(
	 		                'wc_se_' . $field,		// ID
	 		                $field_value['title'],	// Title 
	 		                array($this, 'render_options_' . $field_value['type']),	// Callback
	 		                'sefw-admin-' . str_replace('_', '-', $page),	// Page
	 		                $section,	// Section
	 		                array(	// Arguments
	 		                    'name' => 'wc_se_' . $field,
	 		                    'options' => $this->opt,
	 		                )
	 		            );
	 		        }
	 		    }
	 		}
	 	}

	 	/**
	     * Render admin page navigation tabs
	     * 
	     * @access public
	     * @param string $current_tab
	     * @return void
	     */
	    public function render_tabs($current_tab = 'general-settings')
	    {
	        $current_tab = preg_replace('/-/', '_', $current_tab);
	        echo '<div class="wc_se_tabs_container">';
	        echo '<h2 class="nav-tab-wrapper">';
	        foreach ($this->settings as $page => $page_value) {
	            $class = ($page == $current_tab) ? ' nav-tab-active' : '';
	            echo '<a class="sefw-tab nav-tab'.$class.'" href="?page=sefw&tab='.$page.'">'.((isset($page_value['icon']) && !empty($page_value['icon'])) ? $page_value['icon'] . '&nbsp;' : '').$page_value['title'].'</a>';
	        }
	        echo '</h2>';
	        echo '</div>';
	    }

	 	/**
	 	 * Render settings page
	 	 * 
	 	 * @access public
	 	 * @param string $page
	 	 * @return void
	 	 */
	 	public function render_page($page){
	 	    $page_name = preg_replace('/_/', '-', $page);

	 	    $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	 	    // Register scripts
	 	    wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION );

	 	    wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.66', true );

	 	    wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );

	 	    wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/admin/accounting' . $suffix . '.js', array( 'jquery' ), '0.3.2' );

	 	    wp_register_script( 'round', WC()->plugin_url() . '/assets/js/admin/round' . $suffix . '.js', array( 'jquery' ), WC_VERSION );

	 	    wp_register_script( 'wc-admin-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round', 'ajax-chosen', 'chosen', 'plupload-all' ), WC_VERSION );

	 	    wp_register_script( 'ajax-chosen', WC()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'chosen'), WC_VERSION );

	 	    wp_register_script( 'chosen', WC()->plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array('jquery'), WC_VERSION );

	 	    wp_enqueue_script( 'wc-admin-product-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product' . $suffix . '.js', array( 'wc-admin-meta-boxes' ), WC_VERSION );

			//wp_enqueue_script( 'wc-admin-variation-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product-variation' . $suffix . '.js', array( 'wc-admin-meta-boxes' ), WC_VERSION );
	 	    wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'chosen' );
			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'chosen' );
			$params = array(
				
				'plugin_url'                    => WC()->plugin_url(),
				'ajax_url'                      => admin_url('admin-ajax.php'),
				'order_item_nonce'              => wp_create_nonce("order-item"),
				'add_attribute_nonce'           => wp_create_nonce("add-attribute"),
				'save_attributes_nonce'         => wp_create_nonce("save-attributes"),
				'calc_totals_nonce'             => wp_create_nonce("calc-totals"),
				'get_customer_details_nonce'    => wp_create_nonce("get-customer-details"),
				'search_products_nonce'         => wp_create_nonce("search-products"),
				'grant_access_nonce'            => wp_create_nonce("grant-access"),
				'revoke_access_nonce'           => wp_create_nonce("revoke-access"),
				'add_order_note_nonce'          => wp_create_nonce("add-order-note"),
				'delete_order_note_nonce'       => wp_create_nonce("delete-order-note"),
				'post_id'                       => isset( $post->ID ) ? $post->ID : '',
				'currency_format_num_decimals'  => absint( get_option( 'woocommerce_price_num_decimals' ) ),
				'default_attribute_visibility'  => apply_filters( 'default_attribute_visibility', true ),
				'default_attribute_variation'   => apply_filters( 'default_attribute_variation', true )
			);

			wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $params );
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
 	        ?>

 	            <div class="wrap woocommerce sefw">
 	            <style>

 	              .sefw .product_selection_order, #sortable {
 	              	list-style-type: none;
 	              	margin: 0;
 	              	padding: 0;
 	              	display: inline-block;
 	              	vertical-align: top;
 	              	width: 60%;
 	              	margin-top: 10px;
 	              	margin-bottom: 25px;
 	              }
 	              .sefw .product_selection_order{
 	              	margin-left: 25px;
 	              }
 	              .sefw .product_selection_order li, #sortable li {
 	              	padding: 4px;
 	              	padding-top: 11px;
 	              	height: 45px;
 	              	cursor: move;
 	              	background: #fff;
 	              	box-sizing: border-box;
 	              	border: 1px solid #0074A2;
 	              	position: relative;
 	              }
 	              .sefw .product_selection_order li {
 	              	cursor: default;
 	              	background: #fff;
 	              	border-color: #ddd;
 	              }
 	              .sefw #sortable li p{
 	              	position: absolute;
 	              	right: 10px;
 	              	top: -6px;
 	              }
 	              .sefw li#specific_products p{
 	              	top: -1px;
 	              }
 	              
 	              .sefw #sortable li span {
 	              	position: absolute;
 	              	margin-left: -1.3em;
 	              }
 	              .sefw #sortable li.ui-state-highlight {
 	              	background: #f4f4f4;
 	              	border-style: dotted;
 	              }
 	              .sefw .product_selection_order{
 	              	width: 50px;
 	              }
 	              .sefw .product_selection_order li{
 	              	font-size: 25px;
 	              	padding-top: 11px;
 	              	padding-left: 17px;
 	              }
 	              .sefw .wc_se_container .chosen-container-multi{
 	              	width: calc(50% + 53px) !important;
 	              }
 	              .sefw .wc_se_container h3 {
 	              padding: 10px 0 10px 15px;
 	              border-top: 1px solid #dfdfdf;
 	              border-bottom: 1px solid #dfdfdf;
 	              background-color: #f9f9f9;
 	              margin: 0;
 	              }
 	              .sefw .wc_se_container {
 	              width: 820px;
 	              min-width: 500px;
 	              float: left;
 	              border: 1px solid #dfdfdf;
 	              border-top: none;
 	              background-color: #fcfcfc;
 	              }
 	              .woocommerce.sefw .wc_se_container table.form-table {
 	              margin: 10px 0 10px 25px;
 	              width: auto;
 	              }
 	              .sefw .wc_se_container .submit {
 	              border-top: 1px solid #dfdfdf;
 	              padding: 15px 0 10px 15px;
 	              }
 	              .sefw .select2-container-multi .select2-choices .select2-search-field input{
 	              	min-width: 337px;
 	              }
 	              .sefw .wc_se_container input[id$='size'],.sefw .wc_se_container input[id$='id']{
 	              	width: 6em;
 	              }
 	              .sefw .wc_se_container #wc_se_t2_text{
 	              	width: 20em;
 	              	height: 7em;
 	              }
 	              .sefw .wc_se_container table.form-table span.help_tip, .sefw .wc_se_container #sortable li span.help_tip{
 	              	/*display: inline-block;
 	              	width: 16px;
 	              	height: 16px;
 	              	border: solid 1px #888;
 	              	border-radius: 10px;
 	              	text-indent: 5px;
 	              	font-weight: bold;
 	              	margin-left: 4px;
 	              	*/
 	              	position: relative;
 	              	color: #888;
 	              	font-weight: bold;
 	              	font-size: 16px;
 	              }
 	              /*.sefw .wc_se_container table.form-table span.help_tip:before, .sefw .wc_se_container #sortable li span.help_tip:before{
 	              	content: "\f339";
 	              	color: #888;
 	              	font-family: dashicons;
 	              }*/
 	              .sefw .wc_se_container #sortable li span.help_tip{
 	              	margin-left: -25px;
 	              	margin-top: 5px;
 	              	font-weight: bold;
 	              	font-size: 16px;
 	              	cursor: help;
 	              }
 	              .sefw .wc_se_container .instruction{
 	              	margin-left: 25px;
 	              }
 	              </style>
 	            <div class="wc_se_container">
 	                <form method="post" action="options.php" enctype="multipart/form-data">
 	                    <input type="hidden" name="current_tab" value="<?php echo $page_name; ?>">
						<?php
							settings_fields('wc_se_opt_group_' . $page);
							/*register_setting(
							    'wc_se_opt_group_' . $page,               // Option group
							    'sefw_lite_options',                          // Option name
							    array($this, 'options_validate')            // Sanitize
							);*/
	 	                    if ($page == 'general_settings') {
	 	                    	do_settings_sections('sefw-admin-' . $page_name);

	 	                    }else if ($page == 'products_selection') {
	 	                ?>
 	                    	<h3>
 	                    	<?php
 	                    	echo __('Selection sequence', 'sefw');
 	                    	?>
 	                    	</h3>
 	                    	
 	                    	<p class="instruction">
 	                    	<?php
 	                    	echo __('Drag and drop the blocks to define your sequence for the products selection', 'sefw');
 	                    	?>
 	                    	</p>

 	                    	<ul class="product_selection_order">
 	                    		<li>1</li>
 	                    		<li>2</li>
 	                    		<li>3</li>
 	                    		<li>4</li>
 	                    		<li>5</li>
 	                    	</ul>
 	                    	<ul id="sortable" style="visibility:hidden">
 	                    	  <li class="" id="up_sells">
 	                    	  	<b>
 	                    	  	<?php
 	                    	  	echo __('Up-sells', 'sefw');
 	                    	  	?>
 	                    	  	</b>
 	                    	  	<p>
 	                    	  	<?php $this->render_hint( __('Maximum products quantity', 'sefw') ); ?>
 	                    	  	<select id="upsells_max" class="max">
 	                    	  		<option>1</option>
 	                    	  		<option>2</option>
 	                    	  		<option>3</option>
 	                    	  		<option>4</option>
 	                    	  		<option>5</option>
 	                    	  		<option>6</option>
 	                    	  		<option>7</option>
 	                    	  		<option>8</option>
 	                    	  	</select>
 	                    	  	</p>
 	                    	  </li>
 	                    	  <li class="" id="cross_sells">
 	                    	  	<b>
 	                    	  	<?php
 	                    	  	echo __('Cross-sells', 'sefw');
 	                    	  	?>
 	                    	  	</b>
 	                    	  	<p>
 	                    	  	<?php $this->render_hint( __('Maximum products quantity', 'sefw') ); ?>
 	                    	  	<select id="crosssells_max" class="max">
 	                    	  		<option>1</option>
 	                    	  		<option>2</option>
 	                    	  		<option>3</option>
 	                    	  		<option>4</option>
 	                    	  		<option>5</option>
 	                    	  		<option>6</option>
 	                    	  		<option>7</option>
 	                    	  		<option>8</option>
 	                    	  	</select>
 	                    	  	</p>
 	                    	  </li>
 	                    	  <li class="" id="related_products">
 	                    	  	<b>
 	                    	  	<?php
 	                    	  	echo __('Related products', 'sefw');
 	                    	  	?>
 	                    	  	</b>
 	                    	  	<span class="help_tip" data-tip="<?php echo __('Products sharing the same tags or categories', 'sefw')?>" style="margin-left: 5px;margin-top: 0;">?</span>
 	                    	  	<p>
 	                    	  	<?php $this->render_hint( __('Maximum products quantity', 'sefw') ); ?>
 	                    	  	<select id="related_max" class="max">
 	                    	  		<option>1</option>
 	                    	  		<option>2</option>
 	                    	  		<option>3</option>
 	                    	  		<option>4</option>
 	                    	  		<option>5</option>
 	                    	  		<option>6</option>
 	                    	  		<option>7</option>
 	                    	  		<option>8</option>
 	                    	  	</select>
 	                    	  	</p>
 	                    	  </li>
 	                    	  <li class="" id="specific_products">
 	                    	  	<b>
 	                    	  	<?php
 	                    	  	echo __('Specific products from the shop', 'sefw');
 	                    	  	?>
 	                    	  	</b>
 	                    	  	<p><span class="help_tip" data-tip="<?php echo __('Use the field below to select products', 'sefw')?>" style="margin-top:0">?</span></p>
 	                    	  </li>
 	                    	  <li class="" id="random_shop">
 	                    	  	<b>
 	                    	  	<?php
 	                    	  	echo __('Random products from the shop', 'sefw');
 	                    	  	?>
 	                    	  	</b>
 	                    	  	<p>
 	                    	  	<?php $this->render_hint( __('Maximum products quantity', 'sefw') ); ?>
 	                    	  	<select  id="randomshop_max" class="max">
 	                    	  		<option>1</option>
 	                    	  		<option>2</option>
 	                    	  		<option>3</option>
 	                    	  		<option>4</option>
 	                    	  		<option>5</option>
 	                    	  		<option>6</option>
 	                    	  		<option>7</option>
 	                    	  		<option>8</option>
 	                    	  	</select>
 	                    	  	</p>
 	                    	  </li>
 	                    	</ul>
 	                    <?php
 	                    	do_settings_sections('sefw-admin-' . $page_name);
 	                    	}else if ($page == 'layout') {
	 	                    	do_settings_sections('sefw-admin-' . $page_name);
 	                		}else if ($page == 'preview') {
	 	                    	do_settings_sections('sefw-admin-' . $page_name);

	 	                 		echo '<table class="form-table">
	 	                 				<tbody>
	 	                 				<tr><th scope="row">';
	 	                 		echo __('New Order email', 'sefw');
	 	                 		echo '</th><td>';
		                    	printf('<a href="%s" target="_blank" class="button preview_email">',wp_nonce_url(admin_url('?wc_se_preview=processing_order'), 'wc_se-preview-mail')
	       				 		);
	       				 		echo __('Preview', 'sefw');
	       				 		echo '</a>';
	       				 		echo '</td></tr><tr><th scope="row">';
	       				 		echo __('Order Complete email', 'sefw');
	       				 		echo '</th><td>';
								printf('<a href="%s" target="_blank" class="button preview_email">',wp_nonce_url(admin_url('?wc_se_preview=completed_order'), 'wc_se-preview-mail')
	       				 		);
	       				 		echo __('Preview', 'sefw');
	       				 		echo '</a>';
	       				 		echo '</td></tr><tr><th scope="row">';
	       				 		echo __('Customer Note email', 'sefw');
	       				 		echo '</th><td>';
								printf('<a href="%s" target="_blank" class="button preview_email">',wp_nonce_url(admin_url('?wc_se_preview=customer_note'), 'wc_se-preview-mail')
	       				 		);
	       				 		echo __('Preview', 'sefw');
	       				 		echo '</a>';
	       				 		echo '</td></tr></tbody></table>';
 	                		}
 	                        echo '<div></div>';
 	                        if ($page != 'preview') {
 	                        	submit_button();
 	                        }
 	                    ?>

 	                </form>
 	            </div>
 	            </div>
 	        <?php
	 	    
	 	}

	 	/**
	 	 * Get current tab (fallback to default)
	 	 * 
	 	 * @access public
	 	 * @param bool $is_dash
	 	 * @return string
	 	 */
	 	public function get_current_tab($is_dash = false)
	 	{
	 	    $tab = (isset($_GET['tab']) && $this->page_has_tab($_GET['tab'])) ? preg_replace('/-/', '_', $_GET['tab']) : $this->get_default_tab();

	 	    return (!$is_dash) ? $tab : preg_replace('/_/', '-', $tab);
	 	}

	 	/**
	 	 * Get default tab
	 	 * 
	 	 * @access public
	 	 * @return string
	 	 */
	 	public function get_default_tab()
	 	{
	 	    $current_page_slug = $this->get_current_page_slug();
	 	    return $this->default_tabs[$current_page_slug];
	 	}
	 	/**
	 	 * Get current page slug
	 	 * 
	 	 * @access public
	 	 * @return string
	 	 */
	 	public function get_current_page_slug()
	 	{
	 	    $current_screen = get_current_screen();
	 	    //print_r(get_current_screen());
	 	    $current_page = $current_screen->base;
	 	    $current_page_slug = preg_replace('/woocommerce_page_/', '', $current_page);
	 	    return preg_replace('/-/', '_', $current_page_slug);
	 	}

	 	public function get_settings() {
	 		
	        $this->settings = sfew_settings();
	        
	 		//$this->options = get_option('sefw_lite_options');

	 		//$this->validation = $this->options('validation', true);

	 		// Load some data from config
	 		$this->hints = $this->options('hint');
	 		$this->validation = $this->options('validation', true);
	 		$this->titles = $this->options('title');
	 		$this->options = $this->options('values');
	 		$this->section_info = $this->get_section_info();
	 	}

		public function render_hint($text) {
			if ( $text ) {
				echo '<span class="help_tip" data-tip="'.$text.'">?</span>';
			}
		}

	 	public function render_section_info($section)
	 	{	
	 	    if (isset($this->section_info[$section['id']])) {
	 	        echo $this->section_info[$section['id']];
	 	    }
	 	}

	 	/*
	 	 * Render a text field
	 	 * 
	 	 * @access public
	 	 * @param array $args
	 	 * @return void
	 	 */
	 	public function render_options_text($args = array())
	 	{
	 	    printf(
	 	        '<input type="text" id="%s" name="sefw_lite_options[%s]" value="%s" class="sefw-field-width" /> ',
	 	        $args['name'],
	 	        $args['name'],
	 	        $args['options'][$args['name']]
	 	    );
	 	    $this->render_hint( $this->hints[ $args[ 'name' ] ] );
	 	}

	 	/*
	 	 * Render a text field
	 	 * 
	 	 * @access public
	 	 * @param array $args
	 	 * @return void
	 	 */
	 	public function render_options_hidden($args = array())
	 	{
	 	    printf(
	 	        '<input type="hidden" id="%s" name="sefw_lite_options[%s]" value="%s" class="sefw-field-width" /> ',
	 	        $args['name'],
	 	        $args['name'],
	 	        $args['options'][$args['name']]
	 	    );
	 	    printf(
	 	    	'<script>var %s = "%s"</script>',
	 	    	$args['name'],
	 	        $args['options'][$args['name']]
	 	    );
	 	}

	 	/*
	 	 * Render a product selector
	 	 * 
	 	 * @access public
	 	 * @param array $args
	 	 * @return void
	 	 */
	 	public function render_options_product_select($args = array())
	 	{
	 		if (version_compare(WOOCOMMERCE_VERSION, '2.3.0', '>=')){
                printf('<input type="hidden" class="wc-product-search" style="" id="sefw_lite_options[%s]" name="sefw_lite_options[%s]" data-placeholder="',
                	$args['name'],
                	$args['name']
                );
                _e( 'Search for a product&hellip;', 'woocommerce' );
                echo '" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="';
                $product_ids = array_filter( array_map( 'absint', (array) explode(',', $args['options'][$args['name']]) ) );
                $json_ids    = array();
                foreach ( $product_ids as $product_id ) {
                	$product = wc_get_product( $product_id );
                	$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
                }
				echo esc_attr( json_encode( $json_ids ) );
                echo '" value="';
                echo implode( ',', array_keys( $json_ids ) );
                echo '" />';
            }else{
                printf('<select id="%s" name="sefw_lite_options[%s][]" class="ajax_chosen_select_products" multiple="multiple" data-placeholder="">',
                	$args['name'],
                	$args['name']
                );
                foreach ($args['options'][$args['name']] as $value) {
                	$product = new WC_Product( $value );
                	printf(
                		'<option value="%s" selected="selected">#%s – %s</option>',
                		$value,
                		$value,
                		$product->get_title()
                	);
                };
                echo '</select>';
            }
	 	}

	 	/*
	 	 * Render a text area
	 	 * 
	 	 * @access public
	 	 * @param array $args
	 	 * @return void
	 	 */
	 	public function render_options_textarea($args = array())
	 	{
	 	    printf(
	 	        '<textarea id="%s" name="sefw_lite_options[%s]" class="wc_se_textarea">%s</textarea>',
	 	        $args['name'],
	 	        $args['name'],
	 	        $args['options'][$args['name']]
	 	    );
	 	}

	 	/*
	 	 * Render a checkbox
	 	 * 
	 	 * @access public
	 	 * @param array $args
	 	 * @return void
	 	 */
	 	public function render_options_checkbox($args = array())
	 	{
	 	    printf(
	 	        '<label><input type="checkbox" id="%s" name="sefw_lite_options[%s]" value="1" %s />',
	 	        $args['name'],
	 	        $args['name'],
	 	        checked($args['options'][$args['name']], true, false)
	 	    );
	 	    echo '</label>';
	 	    $this->render_hint( $this->hints[ $args[ 'name' ] ] );
	 	}

	 	/*
	 	 * Render a dropdown
	 	 * 
	 	 * @access public
	 	 * @param array $args
	 	 * @return void
	 	 */
	 	public function render_options_dropdown($args = array())
	 	{
	 	    printf(
	 	        '<select id="%s" name="sefw_lite_options[%s]" class="sefw-field-width">',
	 	        $args['name'],
	 	        $args['name']
	 	    );
	 	    foreach ($this->options[$args['name']] as $key => $name) {
	 	        printf(
	 	            '<option value="%s" %s>%s</option>',
	 	            $key,
	 	            selected($key, $args['options'][$args['name']], false),
	 	            $name
	 	        );
	 	    }
	 	    echo '</select> ';
	 	    $this->render_hint( $this->hints[ $args[ 'name' ] ] );

	 	}

	 	/*
	 	 * Render a colorpicker
	 	 * 
	 	 * @access public
	 	 * @param array $args
	 	 * @return void
	 	 */
	 	public function render_options_colorpicker($args = array())
	 	{
	 	    printf(
	 	        '<input
				name="sefw_lite_options[%s]"
				id="%s"
				type="text"
				style="width:6em;"
				value="%s"
				class="colorpick"
				placeholder=""/>',
	 	        $args['name'],
	 	        $args['name'],
	 	        $args['options'][$args['name']]
	 	    );
	 	}

	 	


	 	/**
	 	 * Validate admin form input
	 	 * 
	 	 * @access public
	 	 * @param array $input
	 	 * @return array
	 	 */
	 	public function options_validate($input)
	 	{
	 	    $current_tab = isset($_POST['current_tab']) ? $_POST['current_tab'] : 'general-settings';
	 	    $output = $this->get_options();

	 	    $errors = array();

	 	    // Iterate over fields and validate/sanitize input
	 	    foreach ($this->validation[$current_tab] as $field => $rule) {
	 	        
	 	        // Different routines for different field types
	 	        switch($rule['rule']) {

	 	            // Validate numbers
	 	            case 'number':
 	                    if (is_numeric($input[$field]) || ($input[$field] == '' && $rule['empty'] == true)) {
 	                        $output[$field] = $input[$field];
 	                    }
 	                    else {
 	                        array_push($errors, array('setting' => $field, 'code' => 'number'));
 	                    }
	 	                break;

	 	            // Validate boolean values (actually 1 and 0)
	 	            case 'bool':
	 	                $input[$field] = (!isset($input[$field]) || $input[$field] == '') ? '0' : $input[$field];
	 	                if (in_array($input[$field], array('0', '1')) || ($input[$field] == '' && $rule['empty'] == true)) {
	 	                    $output[$field] = $input[$field];
	 	                }
	 	                else {
	 	                    array_push($errors, array('setting' => $field, 'code' => 'bool'));
	 	                }
	 	                break;

	 	            // Validate predefined options
	 	            case 'option':
	 	                if (isset($input[$field]) && (isset($this->options[$field][$input[$field]]) || ($input[$field] == '' && $rule['empty'] == true))) {
	 	                    $output[$field] = $input[$field];
	 	                }
	 	                else if (!isset($input[$field])) {
	 	                    $output[$field] = '';
	 	                }
	 	                else {
	 	                    array_push($errors, array('setting' => $field, 'code' => 'option'));
	 	                }
	 	                break;

	 	            // Validate emails
	 	            case 'email':
	 	                if (isset($input[$field]) && (filter_var(trim($field), FILTER_VALIDATE_EMAIL) || ($input[$field] == '' && $rule['empty'] == true))) {
	 	                    $output[$field] = esc_attr(trim($input[$field]));
	 	                }
	 	                else if (!isset($input[$field])) {
	 	                    $output[$field] = '';
	 	                }
	 	                else {
	 	                    array_push($errors, array('setting' => $field, 'code' => 'email'));
	 	                }
	 	                break;

	 	            // Validate URLs
	 	            case 'url':
	 	                // FILTER_VALIDATE_URL for filter_var() does not work as expected
	 	                if (isset($input[$field]) && ($input[$field] == '' && $rule['empty'] != true)) {
	 	                    array_push($errors, array('setting' => $field, 'code' => 'url'));
	 	                }
	 	                else if (!isset($input[$field])) {
	 	                    $output[$field] = '';
	 	                }
	 	                else {
	 	                    $output[$field] = esc_attr(trim($input[$field]));
	 	                }
	 	                break;

	 	            // Validate product options
	 	            case 'product':
	 	                //if (isset( $input[$field] ) && is_array($input[$field])) {
	 	                if (isset( $input[$field] )) {
	 	                    $output[$field] = $input[$field];
	 	                }
	 	                else if (!isset($input[$field])) {
	 	                    $output[$field] = array();
	 	                }
	 	                else {
	 	                    array_push($errors, array('setting' => $field, 'code' => 'product'));
	 	                }
	 	                break;

	 	            // Default validation rule (text fields etc)
	 	            default:
	 	                if (isset($input[$field]) && ($input[$field] == '' && $rule['empty'] != true)) {
	 	                    array_push($errors, array('setting' => $field, 'code' => 'string'));
	 	                }
	 	                else if (!isset($input[$field])) {
	 	                    $output[$field] = '';
	 	                }
	 	                else {
	 	                    $output[$field] = esc_attr(trim($input[$field]));
	 	                }
	 	                break;
	 	        }
	 	    }

	 	    // Display settings updated message
	 	    add_settings_error(
	 	        'wc_se',
	 	        'wc_se_' . 'settings_updated',
	 	        __('Your settings have been saved.', 'sefw'),
	 	        'updated'
	 	    );

	 	    // Display errors
	 	    foreach ($errors as $error) {
	 	        $reverted = __('Reverted to a previous value.', 'sefw');

	 	        $messages = array(
	 	            'number' => __('must be numeric', 'sefw') . '. ' . $reverted,
	 	            'bool' => __('must be either 0 or 1', 'sefw') . '. ' . $reverted,
	 	            'option' => __('is not allowed', 'sefw') . '. ' . $reverted,
	 	            'email' => __('is not a valid email address', 'sefw') . '. ' . $reverted,
	 	            'url' => __('is not a valid URL', 'sefw') . '. ' . $reverted,
	 	            'string' => __('is not a valid text string', 'sefw') . '. ' . $reverted,
	 	            'product' => __('is not a valid product', 'sefw') . '. ' . $reverted,
	 	        );

	 	        add_settings_error(
	 	            'wc_se',
	 	            $error['code'],
	 	            __('Value of', 'sefw') . ' "' . $this->titles[$error['setting']] . '" ' . $messages[$error['code']]
	 	        );
	 	    }

	 	    return $output;
	 	}

	 	/**
	     * Sanitize each setting field as needed
	     *
	     * @param array $input Contains all settings fields as array keys
	     */
	    public function sanitize( $input )
	    {
	        $new_input = array();
	        if( isset( $input['id_number'] ) )
	            $new_input['id_number'] = absint( $input['id_number'] );

	        if( isset( $input['title'] ) )
	            $new_input['title'] = sanitize_text_field( $input['title'] );

	        if( isset( $input['enabled'] ) ) {
	        	$new_input['enabled'] = $input['enabled'];
	        }

	        return $new_input;
	    }

	    public function enqueue_scripts()
	    {
	    	wp_enqueue_script('iris');
	    	wp_register_script('wc_se', sefw_PLUGIN_URL . '/assets/js/wc_se-admin.js', array('iris'), sefw_VERSION);
	    	wp_enqueue_script('wc_se');
	    }

	    public function get_last_valid_order(){
	    	$args = array(
	    		'posts_per_page'   => 1,
	    		'offset'           => 0,
	    		'orderby'          => 'post_date',
	    		'order'            => 'DESC',
	    		'post_type'        => 'shop_order',
	    		'post_status'      => 'wc-completed',
	    		'suppress_filters' => true );

	    	$orders = get_posts( $args );
	    	if( !empty($orders) )
	    		return  $orders[0]->ID;
	    	return false;
	    }

	    public function preview_emails() {
	    	if ( isset( $_GET['wc_se_preview'] ) ) {
	    		
	    		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wc_se-preview-mail') ) {
	    			die( 'Security check' );
	    		}
	    		if ( isset( $_GET['order_id'] ) ) {
	    			$order_id = $_GET['order_id'];
	    		}else{
	    			$order_id = $this->get_last_valid_order();
	    		}
	    		
	    		if ( get_post_type( $order_id ) != 'shop_order' ) {
	    			$order_id = $this->get_last_valid_order();
	    		}
	    		if( $order_id ) {
	    			$this->email_preview = true;
		    		switch ($_GET['wc_se_preview']) {
		    			
		    			case 'processing_order':

		    				$email = WC()->mailer();
		    				$email->emails['WC_Email_Customer_Processing_Order']->object = wc_get_order( $order_id );
		    				echo $email->emails['WC_Email_Customer_Processing_Order']->style_inline($email->emails['WC_Email_Customer_Processing_Order']->get_content_html());
		    				break;

		    			case 'completed_order':

		    				$email = WC()->mailer();
		    				$email->emails['WC_Email_Customer_Completed_Order']->object = wc_get_order( $order_id );
		    				echo $email->emails['WC_Email_Customer_Completed_Order']->style_inline($email->emails['WC_Email_Customer_Completed_Order']->get_content_html());
		    				break;

		    			case 'customer_note':

		    				$email = WC()->mailer();
		    				$email->emails['WC_Email_Customer_Note']->object = wc_get_order( $order_id );
		    				echo $email->emails['WC_Email_Customer_Note']->style_inline($email->emails['WC_Email_Customer_Note']->get_content_html());
		    				break;
		    				
		    		}

	    		}else{
	    			echo __('You must have at least one valid order in WooCommerce to preview emails.', 'sefw');
	    		}
	    		exit;
	    	}
	    }

	} // end \SEFWlite
}

