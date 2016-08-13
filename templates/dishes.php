<?php
/**
 * Dashboard - Products
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

frozr_redirect_login();
frozr_redirect_if_not_seller();

$action = isset( $_GET['action'] ) ? wc_clean($_GET['action']) : 'listing';

if ( $action == 'edit' ) {

    frozr_get_template( FROZR_WOO_TMP . '/dish-edit.php');

} else {
    frozr_get_template( FROZR_WOO_TMP . '/dishes-listing.php');
}