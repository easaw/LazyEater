<?php
/**
 * Dashboard - Orders
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0;

if ($order_id && $_GET['print'] == 'order') {

	$order = new WC_Order( $order_id );

	frozr_print_order_template($order);

} else {

if (! isset($_GET['order_status']) && ! isset($_GET['order_id'])) {
	wp_redirect(add_query_arg( array( 'order_status' => 'on-hold' ), home_url( '/dashboard/orders/') ));
}
get_header();
frozr_redirect_login();
frozr_redirect_if_not_seller();
wp_enqueue_script( 'frozr-order' );

?>

<?php frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array( 'active_menu' => 'order', 'title' => __('Orders','frozr'), 'desc' => __('Manage your restaurant orders.','frozr') ) ); ?>

	<div id="orders" class="content-area-orders-list" data-orderid="<?php echo $order_id; ?>">
		<div class="dashboard-widgets">

			<?php do_action('frozr_before_dahsboard_orders'); ?>

			<div class="report-listing-header">
				<?php if ( $order_id ) { ?>
				<a href="<?php echo home_url( '/dashboard/orders/'); ?>" class="ol_order_title"><?php _e( '&larr; Orders', 'frozr' ); ?></a>
				<?php } else {
				frozr_order_listing_status_filter();
				} ?>
			</div>
				
			<?php if ( $order_id ) {
				frozr_get_template( FROZR_WOO_TMP . '/orders/order-details.php');
			} else {
				frozr_orders_table();
			}
			?>

			<?php do_action('frozr_after_dahsboard_orders'); ?>

		</div>
	</div><!-- #orders .content-area_orders_list -->
</div><!-- #dokkan-wrapper -->

<?php

	// action hook for placing content below #container
	frozr_belowcontainer();
	
	//calling sidebar
	frozr_sidebar();
	
	// calling footer.php
	get_footer();
}