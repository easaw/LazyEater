<?php
/**
 * Frozr rewrite rules class
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Frozr_Rewrites {
    
	public $query_vars = array();

    function __construct() {
 
		add_action( 'init', array( $this, 'register_rule' ) );

        add_filter( 'template_include', array($this, 'store_template') );
        add_filter( 'template_include', array($this, 'product_edit_template'), 11 );
        add_filter( 'template_include', array($this, 'cuisine_template'), 11 );
        add_filter( 'template_include', array($this, 'addresses_template'), 11 );
        add_filter( 'template_include', array($this, 'location_template'), 11 );
        add_filter( 'template_include', array($this, 'restaurants_template'), 11 );
        add_filter( 'template_include', array($this, 'dashboard_template'), 11 );
        add_filter( 'query_vars', array($this, 'register_query_var') );
    }

    /**
     * Register the rewrite rule
     *
     * @return void
     */
    function register_rule() {
		
		$permalinks = get_option( 'woocommerce_permalinks', array() );
        if( isset( $permalinks['product_base'] ) ) {
            $base = substr( $permalinks['product_base'], 1 );
        }
        
        if ( !empty( $base ) ) {
            
            // special treatment for product cat
            if ( stripos( $base, 'product_cat' ) ) {
                
                // get the category base. usually: shop
                $base_array = explode( '/', ltrim( $base, '/' ) ); // remove first '/' and explode
                $cat_base = isset( $base_array[0] ) ? $base_array[0] : 'shop';
                
                add_rewrite_rule( $cat_base . '/(.+?)/([^/]+)(/[0-9]+)?/edit?$', 'index.php?product_cat=$matches[1]&product=$matches[2]&page=$matches[3]&edit=true', 'top' );
                
            } else {
                add_rewrite_rule( $base . '/([^/]+)(/[0-9]+)?/edit/?$', 'index.php?product=$matches[1]&page=$matches[2]&edit=true', 'top' );
            }
        }
        add_rewrite_rule('^restaurants/?', 'index.php?restaurants=restaurants', 'top' );
		add_rewrite_rule('^restaurants/page/?([0-9])?','index.php?restaurants=restaurants&paged=$matches[1]','top'); 

        add_rewrite_rule( '^restaurant/([^/]+)/?$', 'index.php?restaurant=$matches[1]', 'top' );
        add_rewrite_rule( '^restaurant/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?restaurant=$matches[1]&paged=$matches[2]', 'top' );

        add_rewrite_rule( '^dashboard/([^/]+)/?$', 'index.php?dashboard=$matches[1]', 'top' );
        add_rewrite_rule( '^dashboard/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?dashboard=$matches[1]&paged=$matches[2]', 'top' );
		
	}
    /**
     * Register the query var
     *
     * @param array $vars
     * @return array
     */	
    function register_query_var( $vars ) {
		$vars[] = 'restaurants';
		$vars[] = 'restaurant';
		$vars[] = 'edit';
		$vars[] = 'dashboard';

		foreach ( $this->query_vars as $var ) {
			$vars[] = $var;
		}

		return $vars;
	}

    /**
     * Include restaurant template
     *
     * @param type $template
     * @return string
     */
    function store_template( $template ) {

        $store_name = get_query_var( 'restaurant' );

        if ( !empty( $store_name ) ) {
            $store_user = get_user_by( 'slug', $store_name );

            // no user found
            if ( !$store_user ) {
                return get_404_template();
            }

            // check if the user is seller
            if ( !user_can( $store_user->ID, 'frozer' ) ) {
                return get_404_template();
            }

            return FROZR_WOO_TMP . '/restaurant/restaurant.php';
        }

        return $template;
    }

    function restaurants_template( $template ) {
		if( get_query_var( 'restaurants' ) ){
            return FROZR_WOO_TMP . '/restaurants.php';
		}
        return $template;
    }

    function addresses_template( $template ) {
		if( is_tax('restaurant_addresses') ){
            return FROZR_WOO_TMP . '/taxonomy-address-location.php';
		}
        return $template;
    }
	
    function location_template( $template ) {
		if( is_tax('location') ){
            return FROZR_WOO_TMP . '/taxonomy-location.php';
		}
        return $template;
    }

    function cuisine_template( $template ) {
		if( is_tax('cuisine')){
            return FROZR_WOO_TMP . '/taxonomy-cuisine.php';
		}
        return $template;
    }
	
    function dashboard_template( $template ) {

		$redirect = get_query_var( 'dashboard' );
		if ( $redirect == 'home') {
            return FROZR_WOO_TMP . '/dashboard.php';
		} elseif ( $redirect == 'dishes' ) {
            return FROZR_WOO_TMP . '/dishes.php';
        } elseif ( $redirect == 'orders' ) {
            return FROZR_WOO_TMP . '/orders.php';
		} elseif ( $redirect == 'coupons' ) {
            return FROZR_WOO_TMP . '/coupons.php';
		} elseif ( $redirect == 'withdraw' ) {
            return FROZR_WOO_TMP . '/withdraw.php';
		} elseif ( $redirect == 'settings' ) {
            return FROZR_WOO_TMP . '/settings.php';
		} elseif ( $redirect == 'new_dish' ) {
            return FROZR_WOO_TMP . '/new_dish.php';
		} elseif ( $redirect == 'sellers' ) {
            return FROZR_WOO_TMP . '/sellers.php';
		}
		
		return $template;
    }
	
    function product_edit_template( $template ) {
        if ( get_query_var( 'edit' ) && is_singular( 'product' ) ) {

            return FROZR_WOO_TMP . '/dish-edit.php';
        }

        return $template;
    }
}