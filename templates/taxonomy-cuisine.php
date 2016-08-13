<?php
/**
 * Restaurants List - by term
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' ); 

	$term_id = get_queried_object_id();
	$term = get_term( $term_id, 'cuisine' );
	$user_loc = (isset($_COOKIE['frozr_user_location'])) ? $_COOKIE['frozr_user_location'] : 'all';

	$title = __('Filter by Cuisine: ','frozr'). $term->name;

	do_action('frozr_list_restaurants',$title,'','cusine_src',$user_loc,$term->slug);

do_action( 'woocommerce_sidebar' );

get_footer( 'shop' ); ?>