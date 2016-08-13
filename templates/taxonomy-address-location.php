<?php
/**
 * Restaurants List - by restaurant Address
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' ); 

	$term_id = get_queried_object_id();
	$term = get_term( $term_id, 'restaurant_addresses' );
	
	$title = __('Filter by restaurants Addresses: ','frozr') . $term->name;
		
	do_action('frozr_list_restaurants',$title,'','address_src',$term->slug,'all');
	
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' ); ?>