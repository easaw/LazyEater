<?php
/**
 * Frozr installer class
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Frozr_Installer {


    public function do_install() {

        // installs
		Frozr_Installer::woocommerce_settings();
        Frozr_Installer::user_roles();
		
		do_action('frozr_lazyeater_installed');
		
        flush_rewrite_rules();

    }

    public function woocommerce_settings() {
		$selling_contry = get_option('woocommerce_default_country');
		
        update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
        update_option( 'woocommerce_ship_to_countries', 'disabled' );
        update_option( 'woocommerce_ship_to_destination', 'billing_only' );
        update_option( 'woocommerce_allowed_countries', 'specific' );
        update_option( 'woocommerce_specific_allowed_countries', array($selling_contry) );
   }

    /**
     * Init frozr user roles
     *
     * @global WP_Roles $wp_roles
     */
    public function user_roles() {
        global $wp_roles;

        if ( class_exists( 'WP_Roles' ) && !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        add_role( 'seller', __( 'Seller', 'frozr' ), apply_filters('frozr_add_seller_role', array(
			'read' => true,
			'publish_posts' => true,
			'edit_posts' => true,
			'delete_published_posts' => true,
			'edit_published_posts' => true,
			'delete_posts' => true,
			'manage_categories' => true,
			'moderate_comments' => true,
			'upload_files' => true,
			'frozer' => true
		)));

        $wp_roles->add_cap( 'shop_manager', 'frozer' );
        $wp_roles->add_cap( 'administrator', 'frozer' );
    }

}