<?php
/**
 * Restaurants List - by delivery location
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' ); 

	$term_id = get_queried_object_id();
	$term = get_term( $term_id, 'location' );

	$title = __('Filter by delivery location: ','frozr') . $term->name;

	do_action('frozr_list_restaurants',$title,'','location_src',$term->slug,'all');

do_action( 'woocommerce_sidebar' );

get_footer( 'shop' ); ?>