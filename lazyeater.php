<?php
/*
 * Plugin Name: Frozr - Lazy Eater Marketplace Extension
 * Plugin URI: http://www.mahmudhamid.com/
 * Description: An Online Food Ordering System, This plugin will only work with LzyEater compatible themes like Frozr - WP Theme.
 * Version: 1.1.0
 * Author: Mahmud Hamid
 * Author URI: http://mahmudhamid.com
 * Text Domain: frozr
 * Domain Path: /languages/
 * Copyright: © 2009-2015 Mahmud Hamid.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Backwards compatibility for older than PHP 5.3.0
if ( !defined( '__DIR__' ) ) {
    define( '__DIR__', dirname( __FILE__ ) );
}
//Define some main file paths
	define( 'lAZY_EATER_VERSION', '0.1' );
	define( 'lAZY_EATER_PATH', plugin_dir_path( __FILE__ ) );
	define( 'lAZY_EATER_FILE', __FILE__ );
	define( 'FROZR_WOO_INC',  lAZY_EATER_PATH .  '/includes' );
	define( 'FROZR_WOO_CLS',  lAZY_EATER_PATH .  '/classes' );
	define( 'FROZR_WOO_EXT',  lAZY_EATER_PATH .  '/extensions' );
	define( 'FROZR_WOO_TMP',  lAZY_EATER_PATH .  '/templates' );

/**
 * Required functions
 */
require_once( 'woo-includes/class-wc-dependencies.php' );


if ( frozr_is_woocommerce_active() ) {

	final class Lazy_Eater {

		/**
		 * Constructor
		 */
		public function __construct() {

			$fle_option = get_option( 'fro_settings' );
			$auto_update_option = (! empty($fle_option['fro_lazy_auto_updates'])) ? '__return_true' : '__return_false';

			//includes file
			$this->includes();
			// initialize classes
			$this->init_classes();
			$this->init_ajax();
			
			//filters
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			add_filter( 'posts_where', array($this, 'hide_others_uploads') );
			add_filter( 'auto_update_frozr-lazyeater', $auto_update_option );
			add_filter( 'auto_update_frozr', $auto_update_option );
			
			//actions
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array($this, 'woo_enqueue_scripts') );
			add_action( 'admin_enqueue_scripts', array( $this,'woo_admin_enqueue_scripts') );
			add_action( 'admin_init', array($this, 'block_admin_access') );
			
			//add post type support
			add_post_type_support( 'product', 'author' );
			
			do_action('frozr_lazyeater_loaded');
		}
		
		public static function init() {
			
			static $instance = false;

			do_action('frozr_lazyeater_before_init');
			
			if ( ! $instance ) {
				$instance = new Lazy_Eater();
				
				do_action('frozr_lazyeater_init');
			}

			return $instance;

		}
		public static function activate() {

			require_once __DIR__ . '/installer.php';

			$installer = new Frozr_Installer();
			$installer->do_install();
		}
		
		public static function deactivate() {
			//nothing here yet!
		
		}

		/**
		 * Translation
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'frozr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Show row meta on the plugin screen.
		 * @param	array $links Plugin Row Meta
		 * @param	string $file  Plugin Base file
		 * @return	array
		 */
		public function plugin_row_meta( $links, $file ) {
			if ( $file == plugin_basename( __FILE__ ) ) {
				$row_meta = array(
					'support'	=>	'<a href="' . esc_url( apply_filters( 'lazyeater_support_url', 'http://mahmudhamid.com/forums/' ) ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support Forum', 'frozr' ) ) . '">' . __( 'Premium Support', 'frozr' ) . '</a>',
				);
				return array_merge( $links, $row_meta );
			}
			return (array) $links;
		}
		
		function includes() {
			require_once ( 'includes/lazy-functions.php' );
			require_once ( 'includes/ajax.php' );
			require_once ( 'includes/rewrites-functions.php' );
			require_once ( 'includes/coupons-functions.php' );
			require_once ( 'eater-options.php' );
			require_once ( 'includes/restaurant-functions.php' );
			require_once ( 'includes/registration-functions.php' );
			require_once ( 'includes/withdraw-functions.php' );
			require_once ( 'includes/order-functions.php' );
			require_once ( 'includes/sellers-functions.php' );
			require_once ( 'includes/dishes-functions.php' );
			require_once ( 'includes/dashboard-functions.php' );
			require_once ( 'includes/print-functions.php' );
			require_once ( 'extensions/home_extensions.php' );
			require_once ( 'metaboxes/dish_details.php' );
			if ( is_admin() ) {
				require_once ( 'includes/admin-functions.php' );
			}
		}
		/**
		 * Init all the classes
		 *
		 * @return void
		 */
		function init_classes() {	
			new Frozr_Rewrites();
		}
		/**
		 * Init ajax classes
		 *
		 * @return void
		 */
		function init_ajax() {
			$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

			if ( $doing_ajax ) {
				Frozr_Ajax::init()->init_ajax();
			}
		}
		/**
		 * Enqueue scripts and styles
		 *
		 */
		function woo_enqueue_scripts() {

			$theme_layout = get_theme_mod('theme_layout','left');
			$theme_layout_val = ($theme_layout == 'left') ? true : false;

			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'frozr-scripts', plugins_url( 'assets/js/script.js', lAZY_EATER_FILE ), false, null, true );
			wp_enqueue_script( 'lazyeater',  plugins_url( 'assets/js/lazyeater.js', lAZY_EATER_FILE ),false, '1.5.4');
			wp_enqueue_media();

			wp_register_script( 'frozr-product-editor', plugins_url( 'assets/js/product-editor.js', lAZY_EATER_FILE ), false, null, true );
			wp_register_script( 'frozr-order',plugins_url( 'assets/js/orders.js', lAZY_EATER_FILE ), false, null, true );
			wp_register_script( 'reviews', plugins_url( 'assets/js/reviews.js', lAZY_EATER_FILE ), array('jquery'), null, true );
			wp_register_script( 'accounting', plugins_url( 'assets/js/accounting.min.js', lAZY_EATER_FILE ), array('jquery'), null, true );
			wp_enqueue_script( 'serializejson', plugins_url( 'assets/js/jquery.serializejson.min.js', lAZY_EATER_FILE ), array('jquery'), null, true );
			wp_enqueue_script( 'tagator', plugins_url( 'assets/js/tagator.js', lAZY_EATER_FILE ), array('jquery'), null, true );
			//withdraw
			wp_register_script( 'withdraw', plugins_url( 'assets/js/withdraw.js', lAZY_EATER_FILE ), array('jquery'), null, true );
			//coupons
			wp_register_script( 'coupons', plugins_url( 'assets/js/coupons.js', lAZY_EATER_FILE ), array('jquery'), null, true );

			$params = array(
				'i18n_enter_menu_order'					=> esc_js( __( 'Variation menu order (determines position in the list of variations)', 'frozr' ) ),
				'remove_attribute'						=> esc_js( __( 'Remove this attribute?', 'frozr' )),
				'new_attribute_prompt'					=> esc_js( __( 'Enter a name for the new attribute term:', 'frozr' )),
				'featured_label'						=> esc_js( __( 'Featured', 'frozr' )),
				'ajax_url'								=> admin_url( 'admin-ajax.php' ),
				'ajax_loader'							=> plugins_url( 'assets/imgs/ajax-loader.gif', lAZY_EATER_FILE ),
				'new_restaurant_nonce'					=> wp_create_nonce( 'new_restaurant_nonce' ),
				'add_attribute_nonce'					=> wp_create_nonce( 'add-attribute' ),
				'frozr_contact_seller'					=> wp_create_nonce( 'frozr_contact_seller' ),
				'save_attributes_nonce'					=> wp_create_nonce( 'save-attributes' ),
				'post_id'								=> isset( $post->ID ) ? $post->ID : '',
				'seller'								=> array('available' => __( 'Available', 'frozr' ),'notAvailable' => __( 'Not Available', 'frozr' )),
				'product_types'							=> array_map( 'sanitize_title', get_terms( 'product_type', array( 'hide_empty' => false, 'fields' => 'names' ) ) ),
				'i18n_product_type_alert'				=> esc_js( __( 'Your product has variations! Before changing the product type, it is a good idea to delete the variations to avoid errors in the stock reports.', 'frozr' )),
				'variations_per_page'					=> 15,
				'woocommerce_placeholder_img_src'		=> wc_placeholder_img_src(),
				'i18n_choose_image'						=> esc_js( __( 'Choose an image', 'frozr' ) ),
				'i18n_set_image'						=> esc_js( __( 'Set variation image', 'frozr' ) ),
				'i18n_edited_variations'				=> esc_js( __( 'Save changes before changing page?', 'frozr' ) ),
				'load_variations_nonce'					=> wp_create_nonce( 'load-variations' ),
				'save_variations_nonce'					=> wp_create_nonce( 'save-variations' ),
				'add_variation_nonce'					=> wp_create_nonce( 'add-variation' ),
				'i18n_remove_variation'					=> esc_js( __( 'Are you sure you want to remove this variation?', 'frozr' ) ),
				'delete_variations_nonce'				=> wp_create_nonce( 'delete-variations' ),
				'i18n_link_all_variations'				=> esc_js( __( 'Are you sure you want to link all variations? This will create a new variation for each and every possible combination of variation attributes (max 50 per run).', 'frozr' ) ),
				'link_variation_nonce'					=> wp_create_nonce( 'link-variations' ),
				'i18n_variation_added'					=> esc_js( __( "variation added", 'frozr' ) ),
				'i18n_variations_added'					=> esc_js( __( "variations added", 'frozr' ) ),
				'i18n_no_variations_added'				=> esc_js( __( "No variations added", 'frozr' ) ),
				'i18n_delete_all_variations'			=> esc_js( __( 'Are you sure you want to delete all variations? This cannot be undone.', 'frozr' ) ),
				'i18n_last_warning'						=> esc_js( __( 'Last warning, are you sure?', 'frozr' ) ),
				'i18n_enter_a_value_fixed_or_percent'	=> esc_js( __( 'Enter a value (fixed or %)', 'frozr' ) ),
				'mon_decimal_point'						=> wc_get_price_decimal_separator(),
				'i18n_enter_a_value'					=> esc_js( __( 'Enter a value', 'frozr' ) ),
				'i18n_scheduled_sale_start'				=> esc_js( __( 'Sale start date (YYYY-MM-DD format or leave blank)', 'frozr' ) ),
				'i18n_scheduled_sale_end'				=> esc_js( __( 'Sale end date (YYYY-MM-DD format or leave blank)', 'frozr' ) ),
				'bulk_edit_variations_nonce'			=> wp_create_nonce( 'bulk-edit-variations' ),
				'i18n_variation_count_single'			=> esc_js( __( '%qty% variation', 'frozr' ) ),
				'i18n_variation_count_plural'			=> esc_js( __( '%qty% variations', 'frozr' ) ),
				'new_wc_product_nonce'					=> wp_create_nonce( 'new_wc_product' ),
				'update_wc_product_nonce'				=> wp_create_nonce( 'update_wc_product' ),
				'restaurant_settings_nonce'				=> wp_create_nonce( 'frozr_settings_nonce' ),
				'home_url'								=> get_home_url(),
				'frozr_save_withdraw'					=> wp_create_nonce( 'save_fro_withdraw' ),
				'delete_fro_withdraw'					=> wp_create_nonce( 'delete_fro_withdraw' ),
				'frozr_set_order_status'				=> wp_create_nonce( 'set_order_status' ),
				'add_order_note'						=> wp_create_nonce( 'add-order-note' ),
				'delete_order_note_nonce'				=> wp_create_nonce( 'delete-order-note' ),
				'add_rest_review'						=> wp_create_nonce( 'add_rest_review' ),
				'rating_user_login'						=> wp_create_nonce( 'rating_user_login' ),
				'get_total_dash_rep'					=> wp_create_nonce( 'get_dash_totals' ),
				'frozr_delete_dish_nonce'				=> wp_create_nonce( 'frozr_delete_dish_nonce' ),
				'delete_dish'							=> esc_js( __( 'Are you sure you want to delete this item permanently?', 'frozr' ) ),
				'set_user_loc'							=> wp_create_nonce( 'frozr_set_user_loc' ),
				'add_new_product_text'					=> esc_js( __( 'Add new product', 'frozr' )),
				'view_product_text'						=> esc_js( __( 'View product', 'frozr' )),
				'make_order_btn_txt'					=> esc_js( __('Make Order!','frozr')),
				'make_order_b_btn_txt'					=> esc_js( __('Back!','frozr')),
				'restaurant_tables_nonce'				=> wp_create_nonce( 'restaurant_tables_nonce' ),
				'withdraw_delete'						=> esc_js( __('Are you sure you want to delete this withdraw request!','frozr')),
				'frozr_seller_settings_nonce'			=> wp_create_nonce( 'frozr_seller_settings_nonce' ),
				'coupon_nonce_field'					=> wp_create_nonce( 'coupon_nonce_field' ),
				'coupon_del_nonce'						=> wp_create_nonce( 'coupon_del_nonce' ),
				'coupon_delete'							=> esc_js( __('Are you sure you want to delete this coupon!','frozr')),
				'frozr_adv_loc_filter_nonce'			=> wp_create_nonce( 'frozr_adv_loc_filter_nonce' ),
				'frozr_save_front_mods_nonce'			=> wp_create_nonce( 'frozr_save_front_mods_nonce' ),
				'masonry_rtl'							=> $theme_layout_val,
				'frozr_atc_nonce'						=> wp_create_nonce( 'frozr_atc_nonce' ),
				'frozr_refresh_orders_list'				=> wp_create_nonce( 'frozr_refresh_orders_list' ),
				'frozr_no_connection'					=> esc_js( __('Fail to connect, please check your internet connection.','frozr')),
				'frozr_rest_invitation_nonce'			=> wp_create_nonce( 'frozr_rest_invitation_nonce' ),
				'frozr_ajax_add_to_cart'				=> wp_create_nonce( 'frozr_ajax_add_to_cart' ),
				'frozr_dash_print'						=> wp_create_nonce( 'frozr_dash_print' ),
				);
			wp_localize_script( 'jquery', 'frozr', $params );

			wp_enqueue_style( 'eater', plugins_url( 'assets/css/eater.css', lAZY_EATER_FILE ), array(), '1.0', 'all' );
			wp_enqueue_style( 'user-settings', plugins_url( 'assets/css/users_settings.css', lAZY_EATER_FILE ), array(), '1.0', 'all' );
			wp_enqueue_style( 'tagator', plugins_url( 'assets/css/tagator.css', lAZY_EATER_FILE ), array(), '1.0', 'all' );

			wp_localize_script( 'frozr-lazyeater', 'frozr_lazyeater_params', array(
				'i18n_no_row_selected' => __( 'No row selected', 'lazyeater' ),
				'i18n_product_id'      => __( 'Product ID', 'lazyeater' ),
				'i18n_country_code'    => __( 'Country Code', 'lazyeater' ),
				'i18n_state'           => __( 'State/County Code', 'lazyeater' ),
				'i18n_postcode'        => __( 'Zip/Postal Code', 'lazyeater' ),
				'i18n_cost'            => __( 'Cost', 'lazyeater' ),
				'i18n_item_cost'       => __( 'Item Cost', 'lazyeater' )
			) );
		}
		/**
		 * Enqueue admin scripts and styles
		 *
		 */
		function woo_admin_enqueue_scripts() {
			
			wp_enqueue_style( 'le-admin-css', plugins_url( 'assets/css/admin.css', lAZY_EATER_FILE ), array(), '1.0', 'all' );
			wp_enqueue_style( 'select2', plugins_url( 'assets/css/select2.min.css', lAZY_EATER_FILE ), array(), '1.0', 'all' );
			wp_enqueue_script( 'select2', plugins_url( 'assets/js/select2.min.js', lAZY_EATER_FILE ), array('jquery'), null, true );
			wp_enqueue_script( 'le-admin-js', plugins_url( 'assets/js/admin.js', lAZY_EATER_FILE ), array('jquery'), null, true );

			wp_localize_script( 'jquery', 'frozr_admin', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			) );
		}
		/**
		 * Hide other users uploads for `seller` users
		 *
		 * Hide media uploads in page "upload.php" and "media-upload.php" for
		 * sellers. They can see only their uploads.
		 *
		 * @param string $where
		 * @return string
		 */
		function hide_others_uploads( $where ) {
			global $pagenow, $wpdb;

			if ( ( $pagenow == 'upload.php' || $pagenow == 'media-upload.php') && current_user_can( 'frozer' ) ) {
				$user_id = get_current_user_id();

				$where .= " AND $wpdb->posts.post_author = $user_id";
			}

			return $where;
		}
		/**
		 * Block user access to admin panel for specific roles
		 *
		 * @global string $pagenow
		 */
		function block_admin_access() {
			global $pagenow, $current_user;
			
			// bail out if we are from WP Cli
			if ( defined( 'WP_CLI' ) ) {
				return;
			}
			$fle_option = get_option( 'fro_settings' );
			$allow_access = (! empty( $fle_option['fro_allow_user_admin_access']) ) ? $fle_option['fro_allow_user_admin_access'] : false;
			$valid_pages = array('admin-ajax.php', 'admin-post.php', 'async-upload.php', 'media-upload.php');
			$user_role = reset( $current_user->roles );

			if ( ( $allow_access == false ) && (!in_array( $pagenow, $valid_pages ) ) && in_array( $user_role, array( 'seller', 'customer' ) ) ) {
				wp_redirect( home_url() );
				exit;
			}
		}
	}

	/**
	 * Load Lazy Eater
	 *
	 * @return void
	 */
	function frozr_load_lazy_eater() {

		return Lazy_Eater::init();

	}
	add_action( 'plugins_loaded', 'frozr_load_lazy_eater', 5 );

	register_activation_hook( __FILE__, array( 'Lazy_Eater', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Lazy_Eater', 'deactivate' ) );
}