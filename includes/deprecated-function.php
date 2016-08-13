<?php
/**
 * Lazy Eater Deprecated Functions
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function best_selling_dishes() {
	return frozr_best_selling_dishes();
}
function latest_resturants() {
	return frozr_latest_resturants();
}
function home_advance_search() {
	return frozr_home_advance_search();
}