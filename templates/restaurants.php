<?php
/**
 * Restaurants List - All
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' );
	
	$termone = (isset($_GET['by'])) ? sanitize_key($_GET['by']) : 'all';
	if ($termone == "top") {
		$title = __('Most Popular Restaurants','frozr');
	} elseif ($termone == "recommended") {
		$title = __('Restaurants We Recommend','frozr');
	} elseif ($termone == 'veg' || $termone == 'nonveg' || $termone == 'sea-food') {
		$title = __('Restaurants by Food Type','frozr');		
	} else {
		$title = __('All Restaurants','frozr');		
	}
		
	do_action('frozr_list_restaurants',$title,'','restaurant_src',$termone,'all');

do_action( 'woocommerce_sidebar' );

// calling footer.php
get_footer( 'shop' );