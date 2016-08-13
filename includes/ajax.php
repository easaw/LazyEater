<?php
/**
 * Ajax handler for Frozr
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Frozr_Ajax {
	
    /**
     * Singleton object
     *
     * @staticvar boolean $instance
     * @return \self
     */
    public static function init() {

        static $instance = false;

        if ( !$instance ) {
            $instance = new self;
        }

        return $instance;
    }

    /**
     * Init ajax handlers
     *
     * @return void
     */
    function init_ajax() {
		
		do_action('before_frozr_inint_ajax');

		add_action( 'wp_ajax_frozr_ajax_add_to_cart', array( $this, 'frozr_ajax_add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_frozr_ajax_add_to_cart', array( $this, 'frozr_ajax_add_to_cart' ) );
		add_action( 'wp_ajax_frozr_seller_settings', array( $this, 'frozr_seller_settings' ) );
		add_action( 'wp_ajax_nopriv_frozr_get_tables_settings', array( $this, 'frozr_get_tables_settings' ) );
		add_action( 'wp_ajax_frozr_get_tables_settings', array( $this, 'frozr_get_tables_settings' ) );
		add_action( 'wp_ajax_frozr_save_restaurant_settings', array( $this, 'save_restaurant_settings' ) );
		add_action( 'wp_ajax_frozr_grant_access_to_download', array( $this, 'grant_access_to_download' ) );
		add_action( 'wp_ajax_frozr_contact_seller', array( $this, 'contact_seller' ) );
		add_action( 'wp_ajax_frozr_delete_dish', array( $this, 'delete_dish' ) );
		add_action( 'wp_ajax_nopriv_frozr_rating_login', array( $this, 'rating_login' ) );
		add_action( 'wp_ajax_frozr_get_totals_data', array( $this, 'get_totals_data' ) );
		add_action( 'wp_ajax_frozr_print_summary_report', array( $this, 'dash_print_summary_report' ) );
		add_action( 'wp_ajax_frozr_print_order', array( $this, 'dash_print_order' ) );
		add_action( 'wp_ajax_frozr_save_rest_rating', array( $this, 'save_rest_rating') );
		add_action( 'wp_ajax_frozr_update_product', array( $this, 'update_product' ) );
		add_action( 'wp_ajax_frozr_add_new_attribute', array( $this, 'add_new_attribute' ) );
		add_action( 'wp_ajax_frozr_add_attribute', array( $this, 'add_attribute' ) );
		add_action( 'wp_ajax_frozr_add_variation', array( $this, 'add_variation' ) );
		add_action( 'wp_ajax_frozr_remove_variations', array( $this, 'remove_variations' ) );
		add_action( 'wp_ajax_frozr_link_all_variations', array( $this, 'link_all_variations' ) );
		add_action( 'wp_ajax_frozr_save_attributes', array( $this, 'save_attributes' ) );
		add_action( 'wp_ajax_frozr_revoke_access_to_download', array( $this, 'revoke_access_to_download' ) );
		add_action( 'wp_ajax_frozr_load_variations', array( $this, 'load_variations' ) );
		add_action( 'wp_ajax_frozr_save_variations', array( $this, 'save_variations_ajax' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_toggle_enabled', array( $this, 'variation_bulk_action_toggle_enabled' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_toggle_downloadable', array( $this, 'variation_bulk_action_toggle_downloadable' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_toggle_virtual', array( $this, 'variation_bulk_action_toggle_virtual' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_toggle_manage_stock', array( $this, 'variation_bulk_action_toggle_manage_stock' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_regular_price', array( $this, 'variation_bulk_action_variable_regular_price' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_sale_price', array( $this, 'variation_bulk_action_variable_sale_price' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_stock', array( $this, 'variation_bulk_action_variable_stock' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_weight', array( $this, 'variation_bulk_action_variable_weight' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_length', array( $this, 'variation_bulk_action_variable_length' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_width', array( $this, 'variation_bulk_action_variable_width' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_height', array( $this, 'variation_bulk_action_variable_height' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_download_limit', array( $this, 'variation_bulk_action_variable_download_limit' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_download_expiry', array( $this, 'variation_bulk_action_variable_download_expiry' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_delete_all', array( $this, 'variation_bulk_action_delete_all' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_sale_schedule', array( $this, 'variation_bulk_action_variable_sale_schedule' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_regular_price_increase', array( $this, 'variation_bulk_action_variable_regular_price_increase' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_regular_price_decrease', array( $this, 'variation_bulk_action_variable_regular_price_decrease' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_sale_price_increase', array( $this, 'variation_bulk_action_variable_sale_price_increase' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_action_variable_sale_price_decrease', array( $this, 'variation_bulk_action_variable_sale_price_decrease' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_adjust_price', array( $this, 'variation_bulk_adjust_price' ) );
		add_action( 'wp_ajax_frozr_variation_bulk_set_meta', array( $this, 'variation_bulk_set_meta' ) );
		add_action( 'wp_ajax_frozr_bulk_edit_variations', array( $this, 'bulk_edit_variations' ) );
		add_action( 'wp_ajax_nopriv_frozr_adv_loc_filter', array( $this, 'frozr_adv_loc_filter' ) );
		add_action( 'wp_ajax_frozr_adv_loc_filter', array( $this, 'frozr_adv_loc_filter' ) );
		add_action( 'wp_ajax_frozr_save_front_mods', array( $this, 'frozr_save_front_mods' ) );
		add_action( 'wp_ajax_frozr_save_filter_text', array( $this, 'frozr_save_filter_text' ) );
		add_action( 'wp_ajax_frozr_refresh_orders_list', array( $this, 'frozr_orders_lists_refresh' ) );
		add_action( 'wp_ajax_frozr_send_rest_invitation', array( $this, 'frozr_send_rest_invitation' ) );
		add_action( 'wp_ajax_frozr_add_fee_setting_row', array( $this, 'frozr_add_fee_setting_row' ) );

		add_action( 'wp_ajax_nopriv_shop_url', array($this, 'shop_url_check') );
		add_action( 'wp_ajax_nopriv_frozr_user_loc_cookie', array($this, 'user_loc_cookie') );
		add_action( 'wp_ajax_frozr_user_loc_cookie', array($this, 'user_loc_cookie') );

		do_action('after_frozr_inint_ajax');
	}

	/**
     * Add a new row to the admin fees/commission table
     *
     */
    function frozr_add_fee_setting_row() {
		ob_start();

        if ( !is_super_admin() ) {
            echo $message = __( 'Something went wrong!', 'frozr' );
			die(-1);
        }
		
		frozr_get_fees_rules_body();

        die();
    }
	/**
     * Send Invitation Letter to Restaurant
     *
     */
    function frozr_send_rest_invitation() {
		ob_start();
		
        check_ajax_referer( 'frozr_rest_invitation_nonce', 'security' );

        if ( !is_super_admin() ) {
            echo $message = __( 'Something went wrong!', 'frozr' );
			die(-1);
        }
		$msg_args = apply_filters('frozr_send_restaurant_email_args',array (
			'to' => sanitize_email($_POST['rest_invit_email']),
			'subject' => sanitize_text_field($_POST['rest_invit_subject']),
			'msg' => wc_clean($_POST['rest_invit_text']),
		));
		frozr_send_msgs($msg_args, 'rest_invit_msg');

        $message = __( 'Email sent successfully!', 'frozr' );
		
		echo $message;
        die();
    }

	/**
	 * Ajax refresh orders list every 5 minutes.
	 */
	public static function frozr_orders_lists_refresh() {

		ob_start();
		
		check_ajax_referer( 'frozr_refresh_orders_list', 'security' );

		frozr_orders_lists($_POST['ods']);

		die();
	}

	/**
	 * Ajax Add to cart
	 *
	 */
	function frozr_ajax_add_to_cart() {
		ob_start();

        check_ajax_referer( 'frozr_ajax_add_to_cart', 'security' );
		
		$product_id			= apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['pid'] ) );
		$quantity			= empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
		$variation_id		= empty( $_POST['variation_id'] ) ? 0 : absint( $_POST['variation_id'] );
		$product_status		= get_post_status( $product_id );
		$adding_to_cart		= wc_get_product( $variation_id ? $variation_id : $product_id );
		$missing_attributes	= array();
		$variations			= array();
		$attributes			= $adding_to_cart->get_attributes();
		$variation			= empty( $_POST['variation_id'] ) ? wc_get_product( $variation_id ) : '';
		$cart_item_data		= (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );
		$cart_id			= WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );
		$cart_item_key		= WC()->cart->find_product_in_cart( $cart_id );
		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', wc_clean($_POST['product_type']), $adding_to_cart );

		// Security check
		if ( $quantity <= 0 || ! $adding_to_cart || 'trash' === $adding_to_cart->post->post_status ) {
			wp_send_json_error( __('Something Went Wrong!','frozr') );
			die(-1);
		}
		if ( $in_cart_quantity > 0 ) {
			$message = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View Cart', 'frozr' ), sprintf( __( 'You cannot add another &quot;%s&quot; to your cart.', 'frozr' ), $adding_to_cart->get_title() ) );
			wp_send_json_error( $message );
			die(-1);
		}
		if ( $adding_to_cart->is_sold_individually() ) {
			$quantity         = apply_filters( 'woocommerce_add_to_cart_sold_individually_quantity', 1, $quantity, $product_id, $variation_id, $cart_item_data );
			$in_cart_quantity = $cart_item_key ? WC()->cart->cart_contents[ $cart_item_key ]['quantity'] : 0;

			if ( $in_cart_quantity > 0 ) {
				$message = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View Cart', 'frozr' ), sprintf( __( 'You cannot add another &quot;%s&quot; to your cart.', 'frozr' ), $adding_to_cart->get_title() ) ) ;
				wp_send_json_error( $message );
				die(-1);
			}
		}
		// Check product is_purchasable
		if ( ! $adding_to_cart->is_purchasable() ) {
			$message = __( 'Sorry, this product cannot be purchased.', 'frozr' );
			wp_send_json_error( $message );
			die(-1);
		}

		// Stock check - only check if we're managing stock and backorders are not allowed
		if ( ! $adding_to_cart->is_in_stock() ) {
			$message = sprintf( __( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'frozr' ), $adding_to_cart->get_title() );
			wp_send_json_error( $message );
			die(-1);
		}
		if ( ! $adding_to_cart->has_enough_stock( $quantity ) ) {
			$message = sprintf(__( 'You cannot add that amount of &quot;%s&quot; to the cart because there is not enough stock (%s remaining).', 'frozr' ), $adding_to_cart->get_title(), $adding_to_cart->get_stock_quantity() );
			wp_send_json_error( $message );
			die(-1);
		}

		// Stock check - this time accounting for what's already in-cart
		if ( $managing_stock = $adding_to_cart->managing_stock() ) {
			$products_qty_in_cart = WC()->cart->get_cart_item_quantities();

			if ( $adding_to_cart->is_type( 'variation' ) && true === $managing_stock ) {
				$check_qty = isset( $products_qty_in_cart[ $variation_id ] ) ? $products_qty_in_cart[ $variation_id ] : 0;
			} else {
				$check_qty = isset( $products_qty_in_cart[ $product_id ] ) ? $products_qty_in_cart[ $product_id ] : 0;
			}

			/**
			 * Check stock based on all items in the cart.
			 */
			if ( ! $adding_to_cart->has_enough_stock( $check_qty + $quantity ) ) {
				$message = sprintf(
					'<a href="%s" class="button wc-forward">%s</a> %s',
					wc_get_cart_url(),
					__( 'View Cart', 'frozr' ),
					sprintf( __( 'You cannot add that amount to the cart &mdash; we have %s in stock and you already have %s in your cart.', 'frozr' ), $adding_to_cart->get_stock_quantity(), $check_qty )
				);
				wp_send_json_error( $message );
				die(-1);
			}
		}
		
		// Start add to cart process
		// Variable product handling
		if ( 'variable' === $add_to_cart_handler ) {
			// Verify all attributes
			foreach ( $attributes as $attribute ) {
				
				if ( ! $attribute['is_variation'] ) {
					continue;
				}

				$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

				if ( isset( $_POST[ $taxonomy ] ) ) {

					// Get value from post data
					if ( $attribute['is_taxonomy'] ) {
						// Don't use wc_clean as it destroys sanitized characters
						$value = sanitize_title( stripslashes( $_POST[ $taxonomy ] ) );
					} else {
						$value = wc_clean( stripslashes( $_POST[ $taxonomy ] ) );
					}

					// Get valid value from variation
					$valid_value = isset( $variation->variation_data[ $taxonomy ] ) ? $variation->variation_data[ $taxonomy ] : '';

					// Allow if valid
					if ( '' === $valid_value || $valid_value === $value ) {
						$variations[ $taxonomy ] = $value;
						continue;
					}

				} else {
					$missing_attributes[] = wc_attribute_label( $attribute['name'] );
				}
			}
			if ( $missing_attributes ) {
				$message = sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'frozr' ), wc_format_list_of_items( $missing_attributes ) );
				wp_send_json_error( $message );
				die(-1);
			} elseif ( empty( $variation_id ) ) {
				$message = __( 'Please choose product options&hellip;', 'frozr' );
				wp_send_json_error( $message );
				die(-1);
			} else {
				// Add to cart validation
				$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

				if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) !== false && 'publish' === $product_status ) {

					do_action( 'woocommerce_ajax_added_to_cart', $product_id );

					$this->get_refreshed_fragments();
				}
			}

		// Grouped Products
		} elseif ( 'grouped' === $add_to_cart_handler ) {

			$was_added_to_cart = false;
			$added_to_cart     = array();

			if ( ! empty( $_POST['quantity'] ) && is_array( $_POST['quantity'] ) ) {
				$quantity_set = false;

				foreach ( $_POST['quantity'] as $item => $quantity ) {
					if ( $quantity <= 0 ) {
						continue;
					}
					$quantity_set = true;

					// Add to cart validation
					$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );

					if ( $passed_validation && WC()->cart->add_to_cart( $item, $quantity ) !== false && 'publish' === $product_status) {
						$was_added_to_cart = true;
						$added_to_cart[]   = $item;
					}
				}

				if ( ! $was_added_to_cart && ! $quantity_set ) {
					$message = __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'frozr' );
					wp_send_json_error( $message );
					die(-1);
				} elseif ( $was_added_to_cart ) {

					do_action( 'woocommerce_ajax_added_to_cart', $product_id );

					$this->get_refreshed_fragments();
				}

			} elseif ( $product_id ) {
				/* Link on product archives */
				$message = __( 'Please choose a product to add to your cart&hellip;', 'frozr' );
				wp_send_json_error( $message );
				die(-1);
			}

		// Custom Handler
		} elseif ( has_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler ) ){
			do_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler );

		} else {

			$passed_validation	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

			if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

				do_action( 'woocommerce_ajax_added_to_cart', $product_id );

				$this->get_refreshed_fragments();

			} else {
				wp_send_json_error( __('Something Went Wrong!','frozr') );
				die(-1);
			}

		}
		// If we added the product to the cart we can now optionally do a redirect.
		if ( wc_notice_count( 'error' ) === 0 ) {
			// If has custom URL redirect there
			if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', $url ) ) {
				wp_safe_redirect( $url );
				exit;
			} elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}
		die();
	}

	/**
	 * Get a refreshed cart fragment.
	 */
	public static function get_refreshed_fragments() {

		// Get mini cart
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
					'#topcart .mini_cart' => $mini_cart
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() ),
			'count_items' => count(WC()->cart->get_cart())
		);

		wp_send_json( $data );

		die();
	}

	/**
	 * Save front filters text
	 *
	 */
	function frozr_save_filter_text() {
		ob_start();
		
		check_ajax_referer( 'frozr_save_front_mods_nonce', 'security' );
		
		if (!is_super_admin()) {
			echo __('Something Went Wrong!','frozr');
			die(-1);
		}
		
		$valu = wc_clean($_POST['valu']);
		$txt = wc_clean($_POST['txt']);
		$mod = 'front_'. $valu;
		
		set_theme_mod( $mod, $txt );
		
		wp_send_json( array(
			'txt'	=> $txt,
			'msg'	=> __('Title Saved!','frozr'),
		) );
		die();
	}

	/**
	 * Save front layout settings
	 *
	 */
	function frozr_save_front_mods() {
		ob_start();
		
		check_ajax_referer( 'frozr_save_front_mods_nonce', 'security' );
		if (!is_super_admin()) {
			echo __('Something Went Wrong!','frozr');
			die(-1);
		}
		
		if (!empty ($_POST['type'])) {
			if ($_POST['type'] == 'cusearchimg') {
				set_theme_mod( 'cusine_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'catsearchimg') {
				set_theme_mod( 'category_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'ingsearchimg') {
				set_theme_mod( 'ingredient_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'restsearchimg') {
				set_theme_mod( 'rests_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'spysearchimg') {
				set_theme_mod( 'search_type_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'adlocsearchimg') {
				set_theme_mod( 'addressloc_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'typeimg') {
				set_theme_mod( 'typeimg_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'recoimg') {
				set_theme_mod( 'recoimg_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'popuimg') {
				set_theme_mod( 'popuimg_filter_image', intval($_POST['valu']) );
			} elseif($_POST['type'] == 'restdimg') {
				set_theme_mod( 'restdimg_filter_image', intval($_POST['valu']) );
			}
			echo __('Layout Saved!','frozr');
			die();
		} elseif ($_POST['tp']) {
			if ($_POST['tp'] == 'fst') {
				if (! empty($_POST['sort'])) {
					$first_ary = frozr_sortable_value_filter($_POST['sort']);
				} else {
					$first_ary = '';
				}
				if (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "restaurants" ) {
				set_theme_mod('front_sort_objects', $first_ary );
				} elseif (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "delivery" ) {
				set_theme_mod('front_delv_sort_objects', $first_ary );
				}
			} elseif ($_POST['tp'] == 'snd') {
				if (! empty($_POST['sort'])) {
					$snd_ary = frozr_sortable_value_filter($_POST['sort']);
				} else {
					$snd_ary = '';
				}			
				if (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "restaurants" ) {
				set_theme_mod('front_sort_objects_two', $snd_ary );
				} elseif (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "delivery" ) {
				set_theme_mod('front_delv_sort_objects_two', $snd_ary );
				}
			} elseif ($_POST['tp'] == 'trd') {
				if (! empty($_POST['sort'])) {
					$thrd_ary = frozr_sortable_value_filter($_POST['sort']);
				} else {
					$thrd_ary = '';
				}
				if (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "restaurants" ) {
				set_theme_mod('front_trash_sort_objects', $thrd_ary );
				} elseif (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "delivery" ) {
				set_theme_mod('front_trash_delv_sort_objects', $thrd_ary );
				}
			}
			echo __('Layout Saved!','frozr');
			die();
		} elseif ($_POST['mod']) {
			
			$mods = array('frozr_'. wc_clean($_POST['mod']) .'_text', 'frozr_'. wc_clean($_POST['mod']) .'_color', 'frozr_'. wc_clean($_POST['mod']) .'_icon','frozr_'. wc_clean($_POST['mod']) .'_txt_color' );
			$outputs = array();
			foreach($mods as $mod) {
				if (!empty($_POST[$mod]) && get_theme_mod($mod, $_POST[$mod]) != '') {
					set_theme_mod( $mod, $_POST[$mod] );
					$outputs[] = $_POST[$mod];
				}
			}
			wp_send_json( array(
				'btn'	=> wc_clean($_POST['mod']),
				'output'	=> $outputs,
				'msg'	=> __('Layout Saved!','frozr'),
			) );

			die();
		}
		echo __('Something Went Wrong!','frozr');
		die();
	}

	/**
	 * load home search location filter lists
	 *
	 */
	function frozr_adv_loc_filter() {
		ob_start();

		check_ajax_referer( 'frozr_adv_loc_filter_nonce', 'security' );
		$svals = wc_clean($_POST['svals']);
		
		//get all locations
		$getallocs = get_terms( 'location', 'hide_empty=0' );
		//get all addresses
		$getallads = get_terms( 'restaurant_addresses', 'hide_empty=0' );
		
		if ($svals == 'delivery') {
			if ( ! empty( $getallocs ) && ! is_wp_error( $getallocs ) ){
			foreach ( $getallocs as $term ) {
				echo "<li class=\"ui-screen-hidden\"><a href=\"#\" data-aft=\"refresh\" data-loc=\"". $term->slug ."\" data-src=\"delivery\">" . $term->name . "</a></li>";
			} }
		} elseif ($svals == 'restaurants') {
			if ( ! empty( $getallads ) && ! is_wp_error( $getallads ) ){
			foreach ( $getallads as $term ) {
				echo "<li class=\"ui-screen-hidden\"><a href=\"#\" data-aft=\"refresh\" data-loc=\"". $term->slug ."\" data-src=\"restaurants\">" . $term->name . "</a></li>";
			} }
		}
		
		die();
	}

	/**
	 * save sellers settings
	 *
	 */
	function frozr_seller_settings() {
		ob_start();

		check_ajax_referer( 'frozr_seller_settings_nonce', 'security' );
		
		if (!is_super_admin()) {
			echo $message = sprintf( '<div class="style_box fs-icon-warning alert-success">%s</div>', __( 'Cheating!', 'frozr' ) );
			die(-1);
		}
		$user_id = intval($_POST['seller_edit_id']);
		$user_sel = esc_attr($_POST['seller_edit_selling']);
		
		update_user_meta( $user_id, 'frozr_enable_selling', $user_sel );
		
		$message = sprintf( '<div class="style_box fs-icon-check alert-success">%s</div>', __( 'Settings Saved!', 'frozr' ) );
		echo $message;
		
		die();
	}

	/**
	 * get restaurant tables
	 *
	 */
	function frozr_get_tables_settings() {
		ob_start();

		check_ajax_referer( 'restaurant_tables_nonce', 'security' );
	
		$profile_info = frozr_get_store_info( $_POST['usr'] );

		if ($profile_info['show_rest_tables'] == 'no') {
			die(-1);
		}

		if (!empty ($_POST['usr']) && !empty ($_POST['seats'])) {
			frozr_rest_search_tables(intval($_POST['usr']), intval($_POST['seats']));
		}

		die();
	}

	/**
	 * setting the user location cookie
	 *
	 */
	function user_loc_cookie() {
		ob_start();

		check_ajax_referer( 'frozr_set_user_loc', 'security' );
		
		$usrsrc = isset($_POST['srctyp']) ? esc_attr($_POST['srctyp']) : 'delivery';
		
		setcookie('frozr_user_src_type', $usrsrc, time() + (86400 * 30), "/");
		setcookie('frozr_user_location', esc_attr($_POST['userloc']), time() + (86400 * 30), "/");
		if ($_POST['useraft'] == 'check' && ! count($_COOKIE) > 0 ) {
			echo __('Please Enable cookies in your browser.','frozr');
		}

		die();
	}

	/**
	 * Item delete action
	 *
	 */
	function delete_dish() {
		ob_start();

		check_ajax_referer( 'frozr_delete_dish_nonce', 'security' );

		$dish_id = isset( $_POST['dishid'] ) ? intval( $_POST['dishid'] ) : 0;
		$seller_id = get_current_user_id();
		
		if ( ! frozr_is_author( $dish_id ) && ! is_super_admin() || ! current_user_can( 'frozer') && ! is_super_admin() || ! frozr_is_seller_enabled($seller_id) && ! is_super_admin() || $dish_id == 0 ) {
			die(-1);
		}
		$result = wp_delete_post( $dish_id );
		 
		 if (is_wp_error($result)) {
			wp_send_json_error( $result->get_error_message() );
		 } else {
			wp_send_json_success( __('Item Deleted!','frozr') );
		 }
		 
		die();
	}

	/**
	 * Save User Settings via AJAX 
	 */
	public static function save_restaurant_settings() {
		ob_start();

		check_ajax_referer( 'frozr_settings_nonce', 'security' );

        $openclosetime = array(
            'Sat' => array('restop'=> wc_clean($_POST['rest_sat_open']), 'restshifts' => wc_clean($_POST['rest_sat_shifts']), 'open_one' => wc_clean($_POST['rest_sat_opening_one']), 'close_one' => wc_clean($_POST['rest_sat_closing_one']), 'open_two' => wc_clean($_POST['rest_sat_opening_two']), 'close_two' => wc_clean($_POST['rest_sat_closing_two'])),
            'Sun' => array('restop'=> wc_clean($_POST['rest_sun_open']), 'restshifts' => wc_clean($_POST['rest_sun_shifts']), 'open_one' => wc_clean($_POST['rest_sun_opening_one']), 'close_one' => wc_clean($_POST['rest_sun_closing_one']), 'open_two' => wc_clean($_POST['rest_sun_opening_two']), 'close_two' => wc_clean($_POST['rest_sun_closing_two'])),
            'Mon' => array('restop'=> wc_clean($_POST['rest_mon_open']), 'restshifts' => wc_clean($_POST['rest_mon_shifts']), 'open_one' => wc_clean($_POST['rest_mon_opening_one']), 'close_one' => wc_clean($_POST['rest_mon_closing_one']), 'open_two' => wc_clean($_POST['rest_mon_opening_two']), 'close_two' => wc_clean($_POST['rest_mon_closing_two'])),
            'Tue' => array('restop'=> wc_clean($_POST['rest_tue_open']), 'restshifts' => wc_clean($_POST['rest_tue_shifts']), 'open_one' => wc_clean($_POST['rest_tue_opening_one']), 'close_one' => wc_clean($_POST['rest_tue_closing_one']), 'open_two' => wc_clean($_POST['rest_tue_opening_two']), 'close_two' => wc_clean($_POST['rest_tue_closing_two'])),
            'Wed' => array('restop'=> wc_clean($_POST['rest_wed_open']), 'restshifts' => wc_clean($_POST['rest_wed_shifts']), 'open_one' => wc_clean($_POST['rest_wed_opening_one']), 'close_one' => wc_clean($_POST['rest_wed_closing_one']), 'open_two' => wc_clean($_POST['rest_wed_opening_two']), 'close_two' => wc_clean($_POST['rest_wed_closing_two'])),
            'Thu' => array('restop'=> wc_clean($_POST['rest_thu_open']), 'restshifts' => wc_clean($_POST['rest_thu_shifts']), 'open_one' => wc_clean($_POST['rest_thu_opening_one']), 'close_one' => wc_clean($_POST['rest_thu_closing_one']), 'open_two' => wc_clean($_POST['rest_thu_opening_two']), 'close_two' => wc_clean($_POST['rest_thu_closing_two'])),
            'Fri' => array('restop'=> wc_clean($_POST['rest_fri_open']), 'restshifts' => wc_clean($_POST['rest_fri_shifts']), 'open_one' => wc_clean($_POST['rest_fri_opening_one']), 'close_one' => wc_clean($_POST['rest_fri_closing_one']), 'open_two' => wc_clean($_POST['rest_fri_opening_two']), 'close_two' => wc_clean($_POST['rest_fri_closing_two'])),
        );
        $frozr_settings = array(
            'store_name'      => sanitize_text_field($_POST['frozr_store_name']),
            'socialfb' => esc_url($_POST['socialfb']),
            'socialgplus' => esc_url($_POST['socialgplus']),
            'socialtwitter' => esc_url($_POST['socialtwitter']),
            'sociallinkedin' => esc_url($_POST['sociallinkedin']),
            'socialyoutube' => esc_url($_POST['socialyoutube']),
            'payment' => array(),
            'phone' => intval($_POST['setting_phone']),
            'show_email' => esc_attr( $_POST['setting_show_email']),
            'allow_email' => esc_attr( $_POST['setting_allow_email']),
            'accpet_order_type' => ($_POST['accept_order_types'][0] != '') ? array_map( 'wc_clean', $_POST['accept_order_types']) : array('delivery'),
            'accpet_order_type_cl' => in_array('none', $_POST['accept_order_types_cl']) ? array('none') : array_map( 'wc_clean', $_POST['accept_order_types_cl']),
            'allow_ofline_orders' => esc_attr( $_POST['setting_allow_ofline_orders']),
            'show_rest_tables' => esc_attr( $_POST['show_rest_tables']),
            'shipping_fee' => floatval($_POST['shipping_fee']),
            'deliveryby' => esc_attr($_POST['deliveryby']),
            'shipping_pro_adtl_cost' => floatval($_POST['shipping_pro_adtl_cost']),
            'processing_time' => intval($_POST['processing_time']),
            'min_order_amt' => floatval($_POST['min_order_amt']),
			'banner' => intval($_POST['frozr_banner']),
            'gravatar' => intval($_POST['frozr_gravatar'])
        );

        if ( isset( $_POST['settings']['bank'] ) ) {
            $bank = $_POST['settings']['bank'];

            $frozr_settings['payment']['bank'] = array(
                'ac_name' => sanitize_text_field( $bank['ac_name'] ),
                'ac_number' => sanitize_text_field( $bank['ac_number'] ),
                'bank_name' => sanitize_text_field( $bank['bank_name'] ),
                'bank_addr' => sanitize_text_field( $bank['bank_addr'] ),
                'swift' => sanitize_text_field( $bank['swift'] ),
            );
        }

        if ( isset( $_POST['settings']['paypal'] ) ) {
            $frozr_settings['payment']['paypal'] = array(
                'email' => sanitize_email( $_POST['settings']['paypal']['email'] )
            );
        }

        if ( isset( $_POST['settings']['skrill'] ) ) {
            $frozr_settings['payment']['skrill'] = array(
                'email' => sanitize_email( $_POST['settings']['skrill']['email'] )
            );
        }

        $store_id = get_current_user_id();
        update_user_meta( $store_id, 'frozr_profile_settings', $frozr_settings );
        update_user_meta( $store_id, 'rest_open_close_time', $openclosetime );
        update_user_meta( $store_id, '_rest_unavds', array_map( 'strval', $_POST['rest_unads']) );
        update_user_meta( $store_id, '_rest_tables', array_map( 'wc_clean', $_POST['rest_tables']) );
        update_user_meta( $store_id, 'frozr_food_type', array_map( 'wc_clean', $_POST['rest_food_type']) );

		//Save restaurant addresses
		$ravals = explode(',', $_POST['setting_address']);
		foreach($ravals as $key => $val) {
			$ravals[$key] = trim($val);
		}
		$ra_vals = array_diff($ravals, array(""));
		$resad = array_map( 'strval', $ra_vals );

		wp_set_object_terms( $store_id, $resad, 'restaurant_addresses' );

		//Save restaurant type
		$rtvals = explode(',', $_POST['rest_type']);
		foreach($rtvals as $key => $val) {
			$rtvals[$key] = trim($val);
		}
		$rt_vals = array_diff($rtvals, array(""));
		$restype = array_map( 'strval', $rt_vals );

		wp_set_object_terms( $store_id, $restype, 'cuisine' );

		//Save delivery locations
		$vals = explode(',', $_POST['delivery_locations']);
		foreach($vals as $key => $val) {
			$vals[$key] = trim($val);
		}
		$loc_vals = array_diff($vals, array(""));
		$locs = array_map( 'strval', $loc_vals );

		wp_set_object_terms( $store_id, $locs, 'location' );

        do_action( 'frozr_store_profile_saved', $store_id, $frozr_settings );
		
		wp_send_json_success( __('Settings Saved!','frozr') );
			
		die();
	}
	
	/**
	 * update product via AJAX
	 */
	function update_product() {
		ob_start();

		check_ajax_referer( 'update_wc_product', 'security' );

		$seller_id = get_current_user_id();
		
		if ($_POST['product_id']) {

			// Check permissions again and make sure we have what we need
			if ( empty( $_POST ) || empty( $_POST['product_id'] ) ) {
				die( -1 );
			}

			// Check permissions again and make sure we have what we need
			if ( !current_user_can( 'frozer' ) && !is_super_admin() || ! frozr_is_author( absint($_POST['product_id']) ) && !is_super_admin() || !frozr_is_seller_enabled($seller_id) && !is_super_admin()) {
				die( -1 );
			}

			$product_id = absint( $_POST['product_id'] );

			$messgae = __('Item Updated Successfully!','frozr');

		} else {

			// Check permissions again and make sure we have what we need
			if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled($seller_id) && !is_super_admin()) {
				die( -1 );
			}
			if ($_POST['newpid'] == "new_attr") {
				$product_info = apply_filters('frozr_product_new_info_args',array(
					'post_type' => 'product',
					'post_title' => !empty($_POST['post_title']) ? wc_clean($_POST['post_title']) : __('No Title','frozr'),
					'post_content' =>  !empty($_POST['post_content']) ? wp_kses_post($_POST['post_content']) : __('No Content','frozr'),
					'post_excerpt' => !empty($_POST['post_excerpt']) ? wp_kses_post($_POST['post_excerpt']) : __('No Excerpt','frozr'),
					'post_status' => 'draft',
					'comment_status' => isset( $_POST['_enable_reviews'] ) ? 'open' : 'closed'
				));
			} else {
				$product_info = apply_filters('frozr_product_update_info_args',array(
					'post_type' => 'product',
					'post_title' => wc_clean($_POST['post_title']),
					'post_content' =>  wp_kses_post($_POST['post_content']),
					'post_excerpt' => wp_kses_post($_POST['post_excerpt']),
					'post_status' => isset( $_POST['post_status'] ) ? esc_attr($_POST['post_status']) : 'pending',
					'comment_status' => isset( $_POST['_enable_reviews'] ) ? 'open' : 'closed'
				));
				$messgae = __('Item Created Successfully!','frozr');
			}
			
			$product_id = wp_insert_post( $product_info );

		}
		if ($_POST['newpid'] == 'new_attr') {
			$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

			$product_type_terms = wp_get_object_terms( $product_id, 'product_type' );

			// If the product type hasn't been set or it has changed, update it before saving variations
			if ( empty( $product_type_terms ) || $product_type !== sanitize_title( current( $product_type_terms )->name ) ) {
				wp_set_object_terms( $product_id, $product_type, 'product_type' );
			}

			wp_send_json( array(
				'pid'    => $product_id,
			) );
		} else {
			frozr_process_dish_meta( $product_id, get_post( $product_id ) );

			// Clear cache/transients
			wc_delete_product_transients( $product_id );

			add_meta( $product_id );

			add_post_meta( $product_id, '_edit_last', $seller_id );

			// Now that we have an ID we can fix any attachment anchor hrefs
			_fix_attachment_links( $product_id );
			
			wp_send_json( array(
				'msg' => $messgae,
				'newp'    => home_url( '/dashboard/new_dish/'),
				'viewp'    => get_permalink( $product_id ),
			) );
		}
		die();
	}

	/**
	 * Load variations via AJAX
	 */
	public static function load_variations() {
		ob_start();

		check_ajax_referer( 'load-variations', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || empty( $_POST['product_id'] ) || empty( $_POST['attributes'] ) ) {
			echo 'test one';
			die( -1 );
		}

		// Check permissions again and make sure we have what we need
		if ( !frozr_is_author( absint($_POST['product_id']) ) && !is_super_admin() ) {
			die( -1 );
			echo 'test two';
		}

		global $post;

		$product_id = absint( $_POST['product_id'] );
		$post       = get_post( $product_id ); // Set $post global so its available like within the admin screens
		$per_page   = ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
		$page       = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

		// Get attributes
		$attributes        = array();
		$posted_attributes = wp_unslash( $_POST['attributes'] );

		foreach ( $posted_attributes as $key => $value ) {
			$attributes[ $key ] = array_map( 'wc_clean', $value );
		}

		// Get tax classes
		$tax_classes           = WC_Tax::get_tax_classes();
		$tax_class_options     = array();
		$tax_class_options[''] = __( 'Standard', 'frozr' );

		if ( ! empty( $tax_classes ) ) {
			foreach ( $tax_classes as $class ) {
				$tax_class_options[ sanitize_title( $class ) ] = esc_attr( $class );
			}
		}

		// Set backorder options
		$backorder_options = array(
			'no'     => __( 'Do not allow', 'frozr' ),
			'notify' => __( 'Allow, but notify customer', 'frozr' ),
			'yes'    => __( 'Allow', 'frozr' )
		);

		// set stock status options
		$stock_status_options = array(
			'instock'    => __( 'In stock', 'frozr' ),
			'outofstock' => __( 'Out of stock', 'frozr' )
		);

		$parent_data = array(
			'id'                   => $product_id,
			'attributes'           => $attributes,
			'tax_class_options'    => $tax_class_options,
			'sku'                  => get_post_meta( $product_id, '_sku', true ),
			'weight'               => wc_format_localized_decimal( get_post_meta( $product_id, '_weight', true ) ),
			'length'               => wc_format_localized_decimal( get_post_meta( $product_id, '_length', true ) ),
			'width'                => wc_format_localized_decimal( get_post_meta( $product_id, '_width', true ) ),
			'height'               => wc_format_localized_decimal( get_post_meta( $product_id, '_height', true ) ),
			'tax_class'            => get_post_meta( $product_id, '_tax_class', true ),
			'backorder_options'    => $backorder_options,
			'stock_status_options' => $stock_status_options
		);

		if ( ! $parent_data['weight'] ) {
			$parent_data['weight'] = wc_format_localized_decimal( 0 );
		}

		if ( ! $parent_data['length'] ) {
			$parent_data['length'] = wc_format_localized_decimal( 0 );
		}

		if ( ! $parent_data['width'] ) {
			$parent_data['width'] = wc_format_localized_decimal( 0 );
		}

		if ( ! $parent_data['height'] ) {
			$parent_data['height'] = wc_format_localized_decimal( 0 );
		}

		// Get variations
		$args = apply_filters( 'woocommerce_ajax_admin_get_variations_args', array(
			'post_type'      => 'product_variation',
			'post_status'    => array( 'private', 'publish' ),
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'DESC' ),
			'post_parent'    => $product_id
		), $product_id );

		$variations = get_posts( $args );
		$loop = 0;

		if ( $variations ) {

			foreach ( $variations as $variation ) {
				$variation_id     = absint( $variation->ID );
				$variation_meta   = get_post_meta( $variation_id );
				$variation_data   = array();
				$shipping_classes = get_the_terms( $variation_id, 'product_shipping_class' );
				$variation_fields = array(
					'_sku'                   => '',
					'_stock'                 => '',
					'_regular_price'         => '',
					'_sale_price'            => '',
					'_weight'                => '',
					'_length'                => '',
					'_width'                 => '',
					'_height'                => '',
					'_download_limit'        => '',
					'_download_expiry'       => '',
					'_downloadable_files'    => '',
					'_downloadable'          => '',
					'_virtual'               => '',
					'_thumbnail_id'          => '',
					'_sale_price_dates_from' => '',
					'_sale_price_dates_to'   => '',
					'_manage_stock'          => '',
					'_stock_status'          => '',
					'_backorders'            => null,
					'_tax_class'             => null,
					'_variation_description' => ''
				);

				foreach ( $variation_fields as $field => $value ) {
					$variation_data[ $field ] = isset( $variation_meta[ $field ][0] ) ? maybe_unserialize( $variation_meta[ $field ][0] ) : $value;
				}

				// Add the variation attributes
				$variation_data = array_merge( $variation_data, wc_get_product_variation_attributes( $variation_id ) );

				// Formatting
				$variation_data['_regular_price'] = wc_format_localized_price( $variation_data['_regular_price'] );
				$variation_data['_sale_price']    = wc_format_localized_price( $variation_data['_sale_price'] );
				$variation_data['_weight']        = wc_format_localized_decimal( $variation_data['_weight'] );
				$variation_data['_length']        = wc_format_localized_decimal( $variation_data['_length'] );
				$variation_data['_width']         = wc_format_localized_decimal( $variation_data['_width'] );
				$variation_data['_height']        = wc_format_localized_decimal( $variation_data['_height'] );
				$variation_data['_thumbnail_id']  = absint( $variation_data['_thumbnail_id'] );
				$variation_data['image']          = $variation_data['_thumbnail_id'] ? wp_get_attachment_thumb_url( $variation_data['_thumbnail_id'] ) : '';
				$variation_data['shipping_class'] = $shipping_classes && ! is_wp_error( $shipping_classes ) ? current( $shipping_classes )->term_id : '';
				$variation_data['menu_order']     = $variation->menu_order;

				// Stock BW compat
				if ( '' !== $variation_data['_stock'] ) {
					$variation_data['_manage_stock'] = 'yes';
				}

				include( FROZR_WOO_INC . '/woo-views/html-variation-admin.php');

				$loop++;
			}
		}

		die();
	}

	/**
	 * Save variations via AJAX
	 */
	public static function save_variations_ajax() {
		ob_start();

		check_ajax_referer( 'save-variations', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() || empty( $_POST ) || empty( $_POST['product_id'] ) ) {
			die( -1 );
		}
		
		// Check permissions again and make sure we have what we need
		if ( !frozr_is_author( absint($_POST['product_id']) ) && !is_super_admin() ) {
			die( -1 );
		}

		$product_id   = absint( $_POST['product_id'] );
		$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

		$product_type_terms = wp_get_object_terms( $product_id, 'product_type' );

		// If the product type hasn't been set or it has changed, update it before saving variations
		if ( empty( $product_type_terms ) || $product_type !== sanitize_title( current( $product_type_terms )->name ) ) {
			wp_set_object_terms( $product_id, $product_type, 'product_type' );
		}

		save_variations( $product_id, get_post( $product_id ) );

		do_action( 'woocommerce_ajax_save_product_variations', $product_id );

		// Clear cache/transients
		wc_delete_product_transients( $product_id );

		die();
	}

	/**
	 * Bulk action - Toggle Enabled
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_toggle_enabled( $variations, $data ) {
		global $wpdb;

		foreach ( $variations as $variation_id ) {
			$post_status = get_post_status( $variation_id );
			$new_status  = 'private' === $post_status ? 'publish' : 'private';
			$wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $variation_id ) );
		}
	}

	/**
	 * Bulk action - Toggle Downloadable Checkbox
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_toggle_downloadable( $variations, $data ) {
		foreach ( $variations as $variation_id ) {
			$_downloadable   = get_post_meta( $variation_id, '_downloadable', true );
			$is_downloadable = 'no' === $_downloadable ? 'yes' : 'no';
			update_post_meta( $variation_id, '_downloadable', wc_clean( $is_downloadable ) );
		}
	}

	/**
	 * Bulk action - Toggle Virtual Checkbox
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_toggle_virtual( $variations, $data ) {
		foreach ( $variations as $variation_id ) {
			$_virtual   = get_post_meta( $variation_id, '_virtual', true );
			$is_virtual = 'no' === $_virtual ? 'yes' : 'no';
			update_post_meta( $variation_id, '_virtual', wc_clean( $is_virtual ) );
		}
	}

	/**
	 * Bulk action - Toggle Manage Stock Checkbox
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_toggle_manage_stock( $variations, $data ) {
		foreach ( $variations as $variation_id ) {
			$_manage_stock   = get_post_meta( $variation_id, '_manage_stock', true );
			$is_manage_stock = 'no' === $_manage_stock || '' === $_manage_stock ? 'yes' : 'no';
			update_post_meta( $variation_id, '_manage_stock', $is_manage_stock );
		}
	}

	/**
	 * Bulk action - Set Regular Prices
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_regular_price( $variations, $data ) {
		if ( ! isset( $data['value'] ) ) {
			return;
		}

		foreach ( $variations as $variation_id ) {
			// Price fields
			$regular_price = wc_clean( $data['value'] );
			$sale_price    = get_post_meta( $variation_id, '_sale_price', true );

			// Date fields
			$date_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
			$date_to   = get_post_meta( $variation_id, '_sale_price_dates_to', true );
			$date_from = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
			$date_to   = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';

			_wc_save_product_price( $variation_id, $regular_price, $sale_price, $date_from, $date_to );
		}
	}

	/**
	 * Bulk action - Set Sale Prices
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_sale_price( $variations, $data ) {
		if ( ! isset( $data['value'] ) ) {
			return;
		}

		foreach ( $variations as $variation_id ) {
			// Price fields
			$regular_price = get_post_meta( $variation_id, '_regular_price', true );
			$sale_price    = wc_clean( $data['value'] );

			// Date fields
			$date_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
			$date_to   = get_post_meta( $variation_id, '_sale_price_dates_to', true );
			$date_from = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
			$date_to   = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';

			_wc_save_product_price( $variation_id, $regular_price, $sale_price, $date_from, $date_to );
		}
	}

	/**
	 * Bulk action - Set Stock
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_stock( $variations, $data ) {
		if ( ! isset( $data['value'] ) ) {
			return;
		}

		$value = wc_clean( $data['value'] );

		foreach ( $variations as $variation_id ) {
			if ( 'yes' === get_post_meta( $variation_id, '_manage_stock', true ) ) {
				wc_update_product_stock( $variation_id, wc_stock_amount( $value ) );
			} else {
				delete_post_meta( $variation_id, '_stock' );
			}
		}
	}

	/**
	 * Bulk action - Set Weight
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_weight( $variations, $data ) {
		self::variation_bulk_set_meta( $variations, '_weight', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Set Length
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_length( $variations, $data ) {
		self::variation_bulk_set_meta( $variations, '_length', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Set Width
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_width( $variations, $data ) {
		self::variation_bulk_set_meta( $variations, '_width', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Set Height
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_height( $variations, $data ) {
		self::variation_bulk_set_meta( $variations, '_height', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Set Download Limit
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_download_limit( $variations, $data ) {
		self::variation_bulk_set_meta( $variations, '_download_limit', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Set Download Expiry
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_download_expiry( $variations, $data ) {
		self::variation_bulk_set_meta( $variations, '_download_expiry', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Delete all
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_delete_all( $variations, $data ) {
		if ( isset( $data['allowed'] ) && 'true' === $data['allowed'] ) {
			foreach ( $variations as $variation_id ) {
				wp_delete_post( $variation_id );
			}
		}
	}

	/**
	 * Bulk action - Sale Schedule
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_sale_schedule( $variations, $data ) {
		if ( ! isset( $data['date_from'] ) && ! isset( $data['date_to'] ) ) {
			return;
		}

		foreach ( $variations as $variation_id ) {
			// Price fields
			$regular_price = get_post_meta( $variation_id, '_regular_price', true );
			$sale_price    = get_post_meta( $variation_id, '_sale_price', true );

			// Date fields
			$date_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
			$date_to   = get_post_meta( $variation_id, '_sale_price_dates_to', true );

			if ( 'false' === $data['date_from'] ) {
				$date_from = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
			} else {
				$date_from = $data['date_from'];
			}

			if ( 'false' === $data['date_to'] ) {
				$date_to = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';
			} else {
				$date_to = $data['date_to'];
			}

			_wc_save_product_price( $variation_id, $regular_price, $sale_price, $date_from, $date_to );
		}
	}

	/**
	 * Bulk action - Increase Regular Prices
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_regular_price_increase( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, '_regular_price', '+', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Decrease Regular Prices
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_regular_price_decrease( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, '_regular_price', '-', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Increase Sale Prices
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_sale_price_increase( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, '_sale_price', '+', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Decrease Sale Prices
	 * @access private
	 * @param  array $variations
	 * @param  array $data
	 */
	private static function variation_bulk_action_variable_sale_price_decrease( $variations, $data ) {
		self::variation_bulk_adjust_price( $variations, '_sale_price', '-', wc_clean( $data['value'] ) );
	}

	/**
	 * Bulk action - Set Price
	 * @access private
	 * @param  array $variations
	 * @param string $operator + or -
	 * @param string $field price being adjusted
	 * @param string $value Price or Percent
	 */
	private static function variation_bulk_adjust_price( $variations, $field, $operator, $value ) {
		foreach ( $variations as $variation_id ) {
			// Get existing data
			$_regular_price = get_post_meta( $variation_id, '_regular_price', true );
			$_sale_price    = get_post_meta( $variation_id, '_sale_price', true );
			$date_from      = get_post_meta( $variation_id, '_sale_price_dates_from', true );
			$date_to        = get_post_meta( $variation_id, '_sale_price_dates_to', true );
			$date_from      = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
			$date_to        = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';

			if ( '%' === substr( $value, -1 ) ) {
				$percent = wc_format_decimal( substr( $value, 0, -1 ) );
				$$field  += ( ( $$field / 100 ) * $percent ) * "{$operator}1";
			} else {
				$$field  += $value * "{$operator}1";
			}
			_wc_save_product_price( $variation_id, $_regular_price, $_sale_price, $date_from, $date_to );
		}
	}

	/**
	 * Bulk action - Set Meta
	 * @access private
	 * @param array $variations
	 * @param string $field
	 * @param string $value
	 */
	private static function variation_bulk_set_meta( $variations, $field, $value ) {
		foreach ( $variations as $variation_id ) {
			update_post_meta( $variation_id, $field, $value );
		}
	}

	/**
	 * Bulk edit variations via AJAX
	 */
	public static function bulk_edit_variations() {
		ob_start();

		check_ajax_referer( 'bulk-edit-variations', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() || empty( $_POST['product_id'] ) || empty( $_POST['bulk_action'] ) ) {
			die( -1 );
		}
		
		// Check permissions again and make sure we have what we need
		if ( !frozr_is_author( absint($_POST['product_id']) ) && !is_super_admin() ) {
			die( -1 );
		}

		$product_id  = absint( $_POST['product_id'] );
		$bulk_action = wc_clean( $_POST['bulk_action'] );
		$data        = ! empty( $_POST['data'] ) ? array_map( 'wc_clean', $_POST['data'] ) : array();
		$variations  = array();

		if ( apply_filters( 'woocommerce_bulk_edit_variations_need_children', true ) ) {
			$variations = get_posts( array(
				'post_parent'    => $product_id,
				'posts_per_page' => -1,
				'post_type'      => 'product_variation',
				'fields'         => 'ids',
				'post_status'    => array( 'publish', 'private' )
			) );
		}

		if ( method_exists( __CLASS__, "variation_bulk_action_$bulk_action" ) ) {
			call_user_func( array( __CLASS__, "variation_bulk_action_$bulk_action" ), $variations, $data );
		} else {
			do_action( 'woocommerce_bulk_edit_variations_default', $bulk_action, $data, $product_id, $variations );
		}

		do_action( 'woocommerce_bulk_edit_variations', $bulk_action, $data, $product_id, $variations );

		// Sync and update transients
		WC_Product_Variable::sync( $product_id );
		wc_delete_product_transients( $product_id );
		die();
	}

	/**
	 * Add an attribute row
	 */
	public static function add_attribute() {
		ob_start();

		check_ajax_referer( 'add-attribute', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() ) {
			die( -1 );
		}

		global $wc_product_attributes;

		$thepostid     = 0;
		$taxonomy      = sanitize_text_field( $_POST['taxonomy'] );
		$i             = absint( $_POST['i'] );
		$position      = 0;
		$metabox_class = array();
		$attribute     = array(
			'name'         => $taxonomy,
			'value'        => '',
			'is_visible'   => apply_filters( 'woocommerce_attribute_default_visibility', 1 ),
			'is_variation' => 0,
			'is_taxonomy'  => $taxonomy ? 1 : 0
		);

		if ( $taxonomy ) {
			$attribute_taxonomy = $wc_product_attributes[ $taxonomy ];
			$metabox_class[]    = 'taxonomy';
			$metabox_class[]    = $taxonomy;
			$attribute_label    = wc_attribute_label( $taxonomy );
		} else {
			$attribute_label = '';
		}

        include( FROZR_WOO_INC . '/woo-views/html-product-attribute.php');
		die();
	}

	/**
	 * Add a new attribute via ajax function
	 */
	public static function add_new_attribute() {
		ob_start();

		check_ajax_referer( 'add-attribute', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() ) {
			die( -1 );
		}

		$taxonomy = esc_attr( $_POST['taxonomy'] );
		$term     = wc_clean( $_POST['term'] );

		if ( taxonomy_exists( $taxonomy ) ) {

			$result = wp_insert_term( $term, $taxonomy );

			if ( is_wp_error( $result ) ) {
				wp_send_json( array(
					'error' => $result->get_error_message()
				) );
			} else {
				$term = get_term_by( 'id', $result['term_id'], $taxonomy );
				wp_send_json( array(
					'term_id' => $term->term_id,
					'name'    => $term->name,
					'slug'    => $term->slug
				) );
			}
		}

		die();
	}

	/**
	 * Delete variations via ajax function
	 */
	public static function remove_variations() {
		check_ajax_referer( 'delete-variations', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() ) {
			die( -1 );
		}

		$variation_ids = (array) $_POST['variation_ids'];

		foreach ( $variation_ids as $variation_id ) {
			$variation = get_post( $variation_id );

			if ( $variation && 'product_variation' == $variation->post_type ) {
				wp_delete_post( $variation_id );
			}
		}

		die();
	}

	/**
	 * Save attributes via ajax
	 */
	public static function save_attributes() {

		check_ajax_referer( 'save-attributes', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() || empty( $_POST['post_id'] ) ) {
			die( -1 );
		}
		
		// Check permissions again and make sure we have what we need
		if ( !frozr_is_author( absint($_POST['post_id']) ) && !is_super_admin() ) {
			die( -1 );
		}

		// Get post data
		parse_str( $_POST['data'], $data );
		$post_id = absint( $_POST['post_id'] );

		// Save Attributes
		$attributes = array();

		if ( isset( $data['attribute_names'] ) ) {

			$attribute_names  = array_map( 'stripslashes', $data['attribute_names'] );
			$attribute_values = isset( $data['attribute_values'] ) ? $data['attribute_values'] : array();

			if ( isset( $data['attribute_visibility'] ) ) {
				$attribute_visibility = $data['attribute_visibility'];
			}

			if ( isset( $data['attribute_variation'] ) ) {
				$attribute_variation = $data['attribute_variation'];
			}

			$attribute_is_taxonomy   = $data['attribute_is_taxonomy'];
			$attribute_position      = $data['attribute_position'];
			$attribute_names_max_key = max( array_keys( $attribute_names ) );

			for ( $i = 0; $i <= $attribute_names_max_key; $i++ ) {
				if ( empty( $attribute_names[ $i ] ) ) {
					continue;
				}

				$is_visible   = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
				$is_variation = isset( $attribute_variation[ $i ] ) ? 1 : 0;
				$is_taxonomy  = $attribute_is_taxonomy[ $i ] ? 1 : 0;

				if ( $is_taxonomy ) {

					if ( isset( $attribute_values[ $i ] ) ) {

						// Select based attributes - Format values (posted values are slugs)
						if ( is_array( $attribute_values[ $i ] ) ) {
							$values = array_map( 'sanitize_title', $attribute_values[ $i ] );

						// Text based attributes - Posted values are term names, wp_set_object_terms wants ids or slugs.
						} else {
							$values     = array();
							$raw_values = array_map( 'wc_sanitize_term_text_based', explode( WC_DELIMITER, $attribute_values[ $i ] ) );

							foreach ( $raw_values as $value ) {
								$term = get_term_by( 'name', $value, $attribute_names[ $i ] );
								if ( ! $term ) {
									$term = wp_insert_term( $value, $attribute_names[ $i ] );

									if ( $term && ! is_wp_error( $term ) ) {
										$values[] = $term['term_id'];
									}
								} else {
									$values[] = $term->term_id;
								}
							}
						}

						// Remove empty items in the array
						$values = array_filter( $values, 'strlen' );

					} else {
						$values = array();
					}

					// Update post terms
					if ( taxonomy_exists( $attribute_names[ $i ] ) ) {
						wp_set_object_terms( $post_id, $values, $attribute_names[ $i ] );
					}

					if ( $values ) {
						// Add attribute to array, but don't set values
						$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
							'name' 			=> wc_clean( $attribute_names[ $i ] ),
							'value' 		=> '',
							'position' 		=> $attribute_position[ $i ],
							'is_visible' 	=> $is_visible,
							'is_variation' 	=> $is_variation,
							'is_taxonomy' 	=> $is_taxonomy
						);
					}

				} elseif ( isset( $attribute_values[ $i ] ) ) {

					// Text based, possibly separated by pipes (WC_DELIMITER). Preserve line breaks in non-variation attributes.
					$values = $is_variation ? wc_clean( $attribute_values[ $i ] ) : implode( "\n", array_map( 'wc_clean', explode( "\n", $attribute_values[ $i ] ) ) );
					$values = implode( ' ' . WC_DELIMITER . ' ', wc_get_text_attributes( $values ) );

					// Custom attribute - Add attribute to array and set the values
					$attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
						'name' 			=> wc_clean( $attribute_names[ $i ] ),
						'value' 		=> $values,
						'position' 		=> $attribute_position[ $i ],
						'is_visible' 	=> $is_visible,
						'is_variation' 	=> $is_variation,
						'is_taxonomy' 	=> $is_taxonomy
					);
				}

			 }
		}

		if ( ! function_exists( 'attributes_cmp' ) ) {
			function attributes_cmp( $a, $b ) {
				if ( $a['position'] == $b['position'] ) {
					return 0;
				}

				return ( $a['position'] < $b['position'] ) ? -1 : 1;
			}
		}
		uasort( $attributes, 'attributes_cmp' );

		update_post_meta( $post_id, '_product_attributes', $attributes );

		die();
	}

	/**
	 * Add variation via ajax function
	 */
	public static function add_variation() {

		check_ajax_referer( 'add-variation', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() || empty( $_POST['post_id'] ) ) {
			die( -1 );
		}
		
		// Check permissions again and make sure we have what we need
		if ( !frozr_is_author( absint($_POST['post_id']) ) && !is_super_admin() ) {
			die( -1 );
		}

		global $post;

		$post_id = intval( $_POST['post_id'] );
		$post    = get_post( $post_id ); // Set $post global so its available like within the admin screens
		$loop    = intval( $_POST['loop'] );

		$variation = array(
			'post_title'   => 'Product #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $post_id,
			'post_type'    => 'product_variation',
			'menu_order'   => -1
		);

		$variation_id = wp_insert_post( $variation );

		do_action( 'woocommerce_create_product_variation', $variation_id );

		if ( $variation_id ) {
			$variation        = get_post( $variation_id );
			$variation_meta   = get_post_meta( $variation_id );
			$variation_data   = array();
			$shipping_classes = get_the_terms( $variation_id, 'product_shipping_class' );
			$variation_fields = array(
				'_sku'                   => '',
				'_stock'                 => '',
				'_regular_price'         => '',
				'_sale_price'            => '',
				'_weight'                => '',
				'_length'                => '',
				'_width'                 => '',
				'_height'                => '',
				'_download_limit'        => '',
				'_download_expiry'       => '',
				'_downloadable_files'    => '',
				'_downloadable'          => '',
				'_virtual'               => '',
				'_thumbnail_id'          => '',
				'_sale_price_dates_from' => '',
				'_sale_price_dates_to'   => '',
				'_manage_stock'          => '',
				'_stock_status'          => '',
				'_backorders'            => null,
				'_tax_class'             => null,
				'_variation_description' => ''
			);

			foreach ( $variation_fields as $field => $value ) {
				$variation_data[ $field ] = isset( $variation_meta[ $field ][0] ) ? maybe_unserialize( $variation_meta[ $field ][0] ) : $value;
			}

			// Add the variation attributes
			$variation_data = array_merge( $variation_data, wc_get_product_variation_attributes( $variation_id ) );

			// Formatting
			$variation_data['_regular_price'] = wc_format_localized_price( $variation_data['_regular_price'] );
			$variation_data['_sale_price']    = wc_format_localized_price( $variation_data['_sale_price'] );
			$variation_data['_weight']        = wc_format_localized_decimal( $variation_data['_weight'] );
			$variation_data['_length']        = wc_format_localized_decimal( $variation_data['_length'] );
			$variation_data['_width']         = wc_format_localized_decimal( $variation_data['_width'] );
			$variation_data['_height']        = wc_format_localized_decimal( $variation_data['_height'] );
			$variation_data['_thumbnail_id']  = absint( $variation_data['_thumbnail_id'] );
			$variation_data['image']          = $variation_data['_thumbnail_id'] ? wp_get_attachment_thumb_url( $variation_data['_thumbnail_id'] ) : '';
			$variation_data['shipping_class'] = $shipping_classes && ! is_wp_error( $shipping_classes ) ? current( $shipping_classes )->term_id : '';
			$variation_data['menu_order']     = $variation->menu_order;
			$variation_data['_stock']         = wc_stock_amount( $variation_data['_stock'] );

			// Get tax classes
			$tax_classes           = WC_Tax::get_tax_classes();
			$tax_class_options     = array();
			$tax_class_options[''] = __( 'Standard', 'frozr' );

			if ( ! empty( $tax_classes ) ) {
				foreach ( $tax_classes as $class ) {
					$tax_class_options[ sanitize_title( $class ) ] = esc_attr( $class );
				}
			}

			// Set backorder options
			$backorder_options = array(
				'no'     => __( 'Do not allow', 'frozr' ),
				'notify' => __( 'Allow, but notify customer', 'frozr' ),
				'yes'    => __( 'Allow', 'frozr' )
			);

			// set stock status options
			$stock_status_options = array(
				'instock'    => __( 'In stock', 'frozr' ),
				'outofstock' => __( 'Out of stock', 'frozr' )
			);

			// Get attributes
			$attributes = (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

			$parent_data = array(
				'id'                   => $post_id,
				'attributes'           => $attributes,
				'tax_class_options'    => $tax_class_options,
				'sku'                  => get_post_meta( $post_id, '_sku', true ),
				'weight'               => wc_format_localized_decimal( get_post_meta( $post_id, '_weight', true ) ),
				'length'               => wc_format_localized_decimal( get_post_meta( $post_id, '_length', true ) ),
				'width'                => wc_format_localized_decimal( get_post_meta( $post_id, '_width', true ) ),
				'height'               => wc_format_localized_decimal( get_post_meta( $post_id, '_height', true ) ),
				'tax_class'            => get_post_meta( $post_id, '_tax_class', true ),
				'backorder_options'    => $backorder_options,
				'stock_status_options' => $stock_status_options
			);

			if ( ! $parent_data['weight'] ) {
				$parent_data['weight'] = wc_format_localized_decimal( 0 );
			}

			if ( ! $parent_data['length'] ) {
				$parent_data['length'] = wc_format_localized_decimal( 0 );
			}

			if ( ! $parent_data['width'] ) {
				$parent_data['width'] = wc_format_localized_decimal( 0 );
			}

			if ( ! $parent_data['height'] ) {
				$parent_data['height'] = wc_format_localized_decimal( 0 );
			}
			
			include( FROZR_WOO_INC . '/woo-views/html-variation-admin.php');
		}

		die();
	}

	/**
	 * Link all variations via ajax function
	 */
	public static function link_all_variations() {

		if ( ! defined( 'WC_MAX_LINKED_VARIATIONS' ) ) {
			define( 'WC_MAX_LINKED_VARIATIONS', 49 );
		}

		check_ajax_referer( 'link-variations', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() || empty( $_POST['post_id'] ) ) {
			die( -1 );
		}
		
		// Check permissions again and make sure we have what we need
		if ( !frozr_is_author( absint($_POST['post_id']) ) && !is_super_admin() ) {
			die( -1 );
		}

		if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		$post_id = intval( $_POST['post_id'] );

		if ( ! $post_id ) {
			die();
		}

		$variations = array();
		$_product   = wc_get_product( $post_id, array( 'product_type' => 'variable' ) );

		// Put variation attributes into an array
		foreach ( $_product->get_attributes() as $attribute ) {

			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$attribute_field_name = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( $attribute['is_taxonomy'] ) {
				$options = wc_get_product_terms( $post_id, $attribute['name'], array( 'fields' => 'slugs' ) );
			} else {
				$options = explode( WC_DELIMITER, $attribute['value'] );
			}

			$options = array_map( 'trim', $options );

			$variations[ $attribute_field_name ] = $options;
		}

		// Quit out if none were found
		if ( sizeof( $variations ) == 0 ) {
			die();
		}

		// Get existing variations so we don't create duplicates
		$available_variations = array();

		foreach( $_product->get_children() as $child_id ) {
			$child = $_product->get_child( $child_id );

			if ( ! empty( $child->variation_id ) ) {
				$available_variations[] = $child->get_variation_attributes();
			}
		}

		// Created posts will all have the following data
		$variation_post_data = array(
			'post_title'   => 'Product #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $post_id,
			'post_type'    => 'product_variation'
		);

		// Now find all combinations and create posts
		if ( ! function_exists( 'array_cartesian' ) ) {

			/**
			 * @param array $input
			 * @return array
			 */
			function array_cartesian( $input ) {
				$result = array();

				while ( list( $key, $values ) = each( $input ) ) {
					// If a sub-array is empty, it doesn't affect the cartesian product
					if ( empty( $values ) ) {
						continue;
					}

					// Special case: seeding the product array with the values from the first sub-array
					if ( empty( $result ) ) {
						foreach ( $values as $value ) {
							$result[] = array( $key => $value );
						}
					}
					else {
						// Second and subsequent input sub-arrays work like this:
						//   1. In each existing array inside $product, add an item with
						//      key == $key and value == first item in input sub-array
						//   2. Then, for each remaining item in current input sub-array,
						//      add a copy of each existing array inside $product with
						//      key == $key and value == first item in current input sub-array

						// Store all items to be added to $product here; adding them on the spot
						// inside the foreach will result in an infinite loop
						$append = array();
						foreach ( $result as &$product ) {
							// Do step 1 above. array_shift is not the most efficient, but it
							// allows us to iterate over the rest of the items with a simple
							// foreach, making the code short and familiar.
							$product[ $key ] = array_shift( $values );

							// $product is by reference (that's why the key we added above
							// will appear in the end result), so make a copy of it here
							$copy = $product;

							// Do step 2 above.
							foreach ( $values as $item ) {
								$copy[ $key ] = $item;
								$append[] = $copy;
							}

							// Undo the side effecst of array_shift
							array_unshift( $values, $product[ $key ] );
						}

						// Out of the foreach, we can add to $results now
						$result = array_merge( $result, $append );
					}
				}

				return $result;
			}
		}

		$variation_ids       = array();
		$added               = 0;
		$possible_variations = array_cartesian( $variations );

		foreach ( $possible_variations as $variation ) {

			// Check if variation already exists
			if ( in_array( $variation, $available_variations ) ) {
				continue;
			}

			$variation_id = wp_insert_post( $variation_post_data );

			$variation_ids[] = $variation_id;

			foreach ( $variation as $key => $value ) {
				update_post_meta( $variation_id, $key, $value );
			}

			// Save stock status
			update_post_meta( $variation_id, '_stock_status', 'instock' );

			$added++;

			do_action( 'product_variation_linked', $variation_id );

			if ( $added > WC_MAX_LINKED_VARIATIONS ) {
				break;
			}
		}

		delete_transient( 'wc_product_children_' . $post_id );

		echo $added;

		die();
	}

	/**
	 * Delete download permissions via ajax function
	 */
	public static function revoke_access_to_download() {

		check_ajax_referer( 'revoke-access', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() || empty( $_POST['download_id'] ) ) {
			die( -1 );
		}
		
		// Check permissions again and make sure we have what we need
		if ( !frozr_is_author( absint($_POST['download_id']) ) && !is_super_admin() ) {
			die( -1 );
		}

		global $wpdb;

		$download_id = $_POST['download_id'];
		$product_id  = intval( $_POST['product_id'] );
		$order_id    = intval( $_POST['order_id'] );

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s;", $order_id, $product_id, $download_id ) );

		do_action( 'woocommerce_ajax_revoke_access_to_product_download', $download_id, $product_id, $order_id );

		die();
	}

	/**
	 * Grant download permissions via ajax function
	 */
	public static function grant_access_to_download() {

		check_ajax_referer( 'grant-access', 'security' );

		// Check permissions again and make sure we have what we need
		if ( !current_user_can( 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() || empty( $_POST['order_id'] ) ) {
			die( -1 );
		}
		
		// Check permissions again and make sure we have what we need
		if ( !frozr_is_author( absint($_POST['order_id']) ) && !is_super_admin() ) {
			die( -1 );
		}

		global $wpdb;

		$wpdb->hide_errors();

		$order_id     = intval( $_POST['order_id'] );
		$product_ids  = $_POST['product_ids'];
		$loop         = intval( $_POST['loop'] );
		$file_counter = 0;
		$order        = wc_get_order( $order_id );

		if ( ! is_array( $product_ids ) ) {
			$product_ids = array( $product_ids );
		}

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			$files   = $product->get_files();

			if ( ! $order->billing_email ) {
				die();
			}

			if ( $files ) {
				foreach ( $files as $download_id => $file ) {
					if ( $inserted_id = wc_downloadable_file_permission( $download_id, $product_id, $order ) ) {

						// insert complete - get inserted data
						$download = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE permission_id = %d", $inserted_id ) );

						$loop ++;
						$file_counter ++;

						if ( isset( $file['name'] ) ) {
							$file_count = $file['name'];
						} else {
							$file_count = sprintf( __( 'File %d', 'frozr' ), $file_counter );
						}
						include( FROZR_WOO_INC . '/woo-views/html-order-download-permission.php' );
					}
				}
			}
		}

		die();
	}

    /**
     * shop url check
     */
    function shop_url_check() {
        
		check_ajax_referer( 'new_restaurant_nonce', 'security' );

        $url_slug = esc_attr($_POST['url_slug']);

        $check = true;

        $user = get_user_by( 'slug', $url_slug );

        if ( $user != '' ) {
            $check = false;
        }

        echo $check;

		die();
    }

    /**
     * Seller restaurant page email contact form handler
     *
     * Catches the form submission from restaurant page
     */
    function contact_seller() {
		ob_start();
		
        check_ajax_referer( 'frozr_contact_seller' );

        $restaurant = get_user_by( 'id', (int) $_POST['seller_id'] );
		$store_info = frozr_get_store_info( $_POST['seller_id'] );

        if ( !$restaurant || $store_info['allow_email'] == 'no') {
            echo $message = sprintf( '<div class="style_box fs-icon-warning alert-success">%s</div>', __( 'Something went wrong!', 'frozr' ) );
			die(-1);
        }
		$msg_args = array (
		'to' => sanitize_email($restaurant->user_email),
		'restaurant_name' => sanitize_text_field($restaurant->first_name),
		'msg' => sanitize_text_field( $_POST['message'] ),
		);
		if (is_super_admin()) {
			if (!empty ($_POST['subject'])) {
				$msg_args['subject'] = sanitize_text_field($_POST['subject']);
			}
		} else {
				$msg_args['name'] = sanitize_text_field($_POST['name']);
				$msg_args['email'] = sanitize_text_field($_POST['email']);
		}
		frozr_send_msgs($msg_args, 'rest_contact_msg');

        $message = sprintf( '<div class="style_box fs-icon-check alert-success">%s</div>', __( 'Email sent successfully!', 'frozr' ) );
		
		echo $message;
        die();
    }

	//user login via ajax on rating
	function rating_login( $credentials = array(), $secure_cookie = '' ) {

		ob_start();

		check_ajax_referer( 'rating_user_login', 'security' );

		if ( empty($credentials) ) {
			if ( ! empty($_POST['rat_username']) )
				$credentials['user_login'] = $_POST['rat_username'];
			if ( ! empty($_POST['rat_password']) )
				$credentials['user_password'] = $_POST['rat_password'];
		}
		$credentials['remember'] = true;
		
		do_action_ref_array( 'wp_authenticate', array( &$credentials['user_login'], &$credentials['user_password'] ) );

		if ( '' === $secure_cookie )
			$secure_cookie = is_ssl();

		$secure_cookie = apply_filters( 'secure_signon_cookie', $secure_cookie, $credentials );

		global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie
		$auth_secure_cookie = $secure_cookie;

		add_filter('authenticate', 'wp_authenticate_cookie', 30, 3);

		$user = wp_authenticate($credentials['user_login'], $credentials['user_password']);

		if ( is_wp_error($user) ) {
			if ( $user->get_error_codes() == array('empty_username', 'empty_password') ) {
				$user = new WP_Error('', '');
				echo __('Empty username or password.');
			} else {
				echo __('Wrong username or password.');
			}
			die(-1);
		}

		wp_set_auth_cookie($user->ID, $credentials['remember'], $secure_cookie);

		do_action( 'wp_login', $user->user_login, $user );

		die();
	}

	// Save user rating
	function save_rest_rating() {
		ob_start();

		check_ajax_referer( 'add_rest_review', 'security' );
		
		$customer = absint( get_post_meta( intval($_POST['order_id']), '_customer_user', true ) );
		
		if ( $customer != get_current_user_id() ) {
			echo sprintf( '<div class="style_box fs-icon-warning alert-success">%s</div>', __("You cant review this restaurant.","frozr") );
			die(-1);
		}
		if ( intval($_POST['seller_id']) == get_current_user_id() ) {
			echo sprintf( '<div class="style_box fs-icon-warning alert-success">%s</div>', __("You cant review your self.","frozr") );
			die(-1);
		}
		$rest_array = array();
		$rest_rate_orders = array();
		$rest_array = get_user_meta( intval($_POST['seller_id']), 'rest_rating', true );
		if (is_array($rest_array)) {
			foreach($rest_array as $n => $v) {
				$rest_rate_orders[] = $n;
			}
		}
		if (in_array(intval($_POST['order_id']),$rest_rate_orders)) {
			
			echo sprintf( '<div class="style_box fs-icon-warning alert-success"><p>%s</p></div>', __("You've already made a review.","frozr") );
			die(-1);
			
		}
		$rest_array[intval($_POST['order_id'])] = sanitize_text_field($_POST['restrating']);
		update_user_meta( intval($_POST['seller_id']), 'rest_rating', $rest_array );
		
		echo sprintf( '<div class="style_box fs-icon-check alert-success">%s</div>', __("Thank you!","frozr") );
		die();
	}

	//print order
	function dash_print_order() {
		ob_start();

		check_ajax_referer( 'frozr_dash_print', 'security' );
		$order_post = get_post( intval($_POST['order_id']) );
		$author = $order_post->post_author;

		if ( ! current_user_can( 'edit_shop_orders' ) || $author != get_current_user_id() && !is_super_admin() ) {
			die(-1);
		}
		wp_send_json( array(
			'url' => home_url('/dashboard/orders/') . '?print=order&order_id='. intval($_POST['order_id']),
		));
	}
	
	//print summary report
	function dash_print_summary_report() {
		ob_start();

		check_ajax_referer( 'frozr_dash_print', 'security' );

		if ( ! current_user_can( 'frozer' ) ) {
			die(-1);
		}
		if (is_super_admin()) {
			if (esc_attr($_POST['rtype']) != 'custom') {
				wp_send_json( array(
					'url' => home_url('/dashboard/home/') . '?print=summary&rtype='. wc_clean($_POST['rtype']) . '&auser='. wc_clean($_POST['auser']),
				));
			} else {
				wp_send_json( array(
					'url' => home_url('/dashboard/home/') . '?print=summary&rtype='. wc_clean($_POST['rtype']) . '&startd='. wc_clean($_POST['startd']) . '&endd='. wc_clean($_POST['endd']) . '&auser='. wc_clean($_POST['auser']),
				));
			}
		} elseif (esc_attr($_POST['rtype']) != 'custom') {
			wp_send_json( array(
				'url' => home_url('/dashboard/home/') . '?print=summary&rtype='. wc_clean($_POST['rtype']),
			));
		} else {
			wp_send_json( array(
				'url' => home_url('/dashboard/home/') . '?print=summary&rtype='. wc_clean($_POST['rtype']) . '&startd='. wc_clean($_POST['startd']) . '&endd='. wc_clean($_POST['endd']),
			));
		}
		// Quit out
		die();
	}
	//get dashboard reports data via ajax
	function get_totals_data() {
		ob_start();

		check_ajax_referer( 'get_dash_totals', 'security' );

		if ( ! current_user_can( 'frozer' ) ) {
			die(-1);
		}
		if (is_super_admin()) {
			if (esc_attr($_POST['rtype']) != 'custom') {
				frozr_dashboard_totals(wc_clean($_POST['rtype']), '','',wc_clean($_POST['auser']));
			} else {
				frozr_dashboard_totals(wc_clean($_POST['rtype']), wc_clean($_POST['startd']), wc_clean($_POST['endd']), wc_clean($_POST['auser']));
			}
		} elseif (esc_attr($_POST['rtype']) != 'custom') {
			frozr_dashboard_totals(wc_clean($_POST['rtype']));
		} else {
			frozr_dashboard_totals(wc_clean($_POST['rtype']), wc_clean($_POST['startd']), wc_clean($_POST['endd']));
		}
		// Quit out
		die();
	}

	/**
     * Make all the products to pending once a seller is deactivated for selling
     *
     * @param int $seller_id
     */
    function make_products_pending( $seller_id ) {
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $seller_id,
            'orderby' => 'post_date',
            'order' => 'DESC'
        );

        $product_query = new WP_Query( $args );
        $products = $product_query->get_posts();

        if ( $products ) {
            foreach ($products as $pro) {
                wp_update_post( array( 'ID' => $pro->ID, 'post_status' => 'pending' ) );
            }
        }
    }
}