<?php
/**
 * Get orders methods
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// Orders page nav
function frozr_order_listing_status_filter() {
    $orders_url = home_url( '/dashboard/orders/');
    $status_class = isset( $_GET['order_status'] ) ? $_GET['order_status'] : 'processing';
    ?>

    <ul class="order-statuses-filter">
        <li <?php echo $status_class == 'processing' ? ' class="active fs-icon-caret-up"' : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'order_status' => 'processing' ), $orders_url ); ?>">
                <?php printf( __( 'Processing (%d)', 'frozr' ), frozr_count_user_object('wc-processing', 'shop_order') ); ?></span>
            </a>
        </li>
        <li <?php echo $status_class == 'completed' ? ' class="active fs-icon-caret-up"' : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'order_status' => 'completed' ), $orders_url ); ?>">
                <?php printf( __( 'Completed (%d)', 'frozr' ), frozr_count_user_object('wc-completed', 'shop_order') ); ?></span>
            </a>
		</li>
		<li <?php echo $status_class == 'on-hold' ? ' class="active fs-icon-caret-up"' : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'order_status' => 'on-hold' ), $orders_url ); ?>">
                <?php printf( __( 'On-hold (%d)', 'frozr' ), frozr_count_user_object('wc-on-hold', 'shop_order') ); ?></span>
            </a>
        </li>
        <li <?php echo $status_class == 'pending' ? ' class="active fs-icon-caret-up"' : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'order_status' => 'pending' ), $orders_url ); ?>">
                <?php printf( __( 'Pending (%d)', 'frozr' ), frozr_count_user_object('wc-pending', 'shop_order')); ?></span>
            </a>
        </li>
        <li <?php echo $status_class == 'cancelled' ? ' class="active fs-icon-caret-up"' : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'order_status' => 'cancelled' ), $orders_url ); ?>">
                <?php printf( __( 'Cancelled (%d)', 'frozr' ), frozr_count_user_object('wc-cancelled', 'shop_order') ); ?></span>
            </a>
        </li>
        <li <?php echo $status_class == 'refunded' ? ' class="active fs-icon-caret-up"' : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'order_status' => 'refunded' ), $orders_url ); ?>">
                <?php printf( __( 'Refunded (%d)', 'frozr' ), frozr_count_user_object('wc-refunded', 'shop_order') ); ?></span>
            </a>
        </li>
        <li <?php echo $status_class == 'failed' ? ' class="active fs-icon-caret-up"' : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'order_status' => 'failed' ), $orders_url ); ?>">
                <?php printf( __( 'failed (%d)', 'frozr' ), frozr_count_user_object('wc-failed', 'shop_order') ); ?></span>
            </a>
        </li>
		<?php do_action('frozr_after_dashoboard_order_listing_status_filter'); ?>
    </ul>
    <?php
}
//Order table
function frozr_orders_table () { 

global $post;
$order_status = isset( $_GET['order_status'] ) ? sanitize_key( $_GET['order_status'] ) : 'all';
?>
	<table class="orders_list_table ui-responsive table-stroke" data-role="table">
		<thead>
			<tr class="table_collumns">
				<th data-priority="1"><?php _e( 'Order', 'frozr' ); ?></th>
				<th data-priority="4"><?php _e( 'Order Total', 'frozr' ); ?></th>
				<th data-priority="2"><?php _e( 'Status', 'frozr' ); ?></th>
				<th data-priority="7"><?php _e( 'Customer', 'frozr' ); ?></th>
				<th data-priority="6"><?php _e( 'Date', 'frozr' ); ?></th>
				<th data-priority="3"><?php _e( 'Action', 'frozr' ); ?></th>
				<?php if (is_super_admin()) { ?>
				<th data-priority="8"><?php _e( 'Seller', 'frozr' ); ?></th>
				<?php } ?>
				<?php do_action('frozr_after_dashboard_orders_table_header'); ?>
			</tr>
		</thead>
		<tbody class="orders_lists" data-ods="<?php echo $order_status; ?>">
			<?php frozr_orders_lists($order_status); ?>
		</tbody>
	</table>
	<?php if ( $orders_query->max_num_pages > 1 ) {
				echo '<div class="pagination-wrap">';
				$page_links = paginate_links( array(
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total' => $orders_query->max_num_pages,
				'base' => str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) ),
				'type' => 'array',
				'prev_text' => __( '&laquo; Previous', 'frozr' ),
				'next_text' => __( 'Next &raquo;', 'frozr' )
				) );

			echo '<ul class="pagination"><li>';
			echo join("</li>\n\t<li>", $page_links);
			echo "</li>\n</ul>\n";
			echo '</div>';
	}
	wp_reset_postdata();
}
//Orders lists
function frozr_orders_lists($ods) {
global $post;

	$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
	$get_curnt_user = (is_super_admin()) ? '' : get_current_user_id();
	$order_ids = apply_filters('frozr_dashboard_orders_lists_args',array(
		'posts_per_page'	=> 10,
		'post_type'			=> 'shop_order',
		'orderby'			=> 'date',
		'author'			=> $get_curnt_user,
		'order'				=> 'desc',
		'post_status'		=> array('wc-'. $ods),
		'paged'				=> $paged,
	));
	$orders_query = new WP_Query( apply_filters( 'frozr_withdraw_listing_query', $order_ids ) );

	if ( $orders_query->have_posts() ) {
		while ($orders_query->have_posts()) { $orders_query->the_post();
			frozr_order_listing_body($post->ID, $ods);
		}
	} else { ?>
		<tr>
			<td colspan="7">
				<div class="frozr-error">
					<?php _e( 'No orders found', 'frozr' ); ?>
				</div>
			</td>
		</tr>
	<?php }
}
// Get total order for sellers
function frozr_get_seller_total_order( $order, $inc_fee = true, $inc_sub = true) {
	$order_sub_total = 0;
	$delivery_fee_total = 0;
	
	if ( $order->get_fees() && $inc_fee == true) {
		$fees = $order->get_fees();
		foreach ( $fees as $id => $fee ) {
			if ($fee['name'] == "Total Delivery") {
				$delivery_fee_total = $fee['line_total'];
			}
		}
	}
	if ($inc_sub) { $order_sub_total = $order->get_subtotal(); }
	
	return apply_filters('frozr_get_seller_total_order', $order_sub_total + $delivery_fee_total);
}

// Calculate the net amount a seller get after an order
function frozr_calculate_order_fees($order) {
	$order_total = frozr_get_seller_total_order($order);
	$order_post = get_post( $order->id );
	$order_seller = $order_post->post_author;
	$order_customer = $order->get_user_id();
	$frozr_option = get_option( 'fro_settings' );
	$fees_options = (! empty( $frozr_option['fro_lazy_fees']) ) ? $frozr_option['fro_lazy_fees'] : '';
	
	if ($fees_options) {
		
		// Get the order sub total
		$order_sub_total = frozr_get_seller_total_order($order, false);

		// Get the order delivery fee
		$order_delivery = frozr_get_seller_total_order($order, true, false);

		// Applicable fees
		$applicable_fees_data = array();
		$applicable_fees = array();

		// Total fees
		$total_fee = array();

		// Total amount effected
		$amount_effected = array();

		// Default result for testing
		$matching_rule = false;
				
		// Start testing for matching fees rules
		foreach ($fees_options as $fees_rules) {
			if ($fees_rules['customers_effected'] == 'all' && $fees_rules['sellers_effected'] == 'all' ) {
				$matching_rule = true;
			} elseif ($fees_rules['customers_effected'] == 'all' && $fees_rules['sellers_effected'] == 'all_but') {
				if (!in_array($order_seller, $fees_rules['sellers'])) {				
					$matching_rule = true;
				}
			} elseif ($fees_rules['customers_effected'] == 'all' && $fees_rules['sellers_effected'] == 'specific') {
				if (in_array($order_seller, $fees_rules['sellers'])) {				
					$matching_rule = true;
				}
			} elseif ($fees_rules['customers_effected'] == 'all_but' && $fees_rules['sellers_effected'] == 'all') {
				if (!in_array($order_customer, $fees_rules['customers'])) {				
					$matching_rule = true;
				}
			} elseif ($fees_rules['customers_effected'] == 'all_but' && $fees_rules['sellers_effected'] == 'all_but') {
				if (!in_array($order_customer, $fees_rules['customers']) && !in_array($order_seller, $fees_rules['sellers'])) {				
					$matching_rule = true;
				}
			} elseif ($fees_rules['customers_effected'] == 'all_but' && $fees_rules['sellers_effected'] == 'specific') {
				if (!in_array($order_customer, $fees_rules['customers']) && in_array($order_seller, $fees_rules['sellers'])) {				
					$matching_rule = true;
				}
			} elseif ($fees_rules['customers_effected'] == 'specific' && $fees_rules['sellers_effected'] == 'all') {
				if (in_array($order_customer, $fees_rules['customers'])) {				
					$matching_rule = true;
				}
			} elseif ($fees_rules['customers_effected'] == 'specific' && $fees_rules['sellers_effected'] == 'all_but') {
				if (in_array($order_customer, $fees_rules['customers']) && !in_array($order_seller, $fees_rules['sellers'])) {				
					$matching_rule = true;
				}
			} elseif ($fees_rules['customers_effected'] == 'specific' && $fees_rules['sellers_effected'] == 'specific') {
				if (in_array($order_customer, $fees_rules['customers']) && in_array($order_seller, $fees_rules['sellers'])) {				
					$matching_rule = true;
				}
			}
			if ($fees_rules['order_amount'] > $order_sub_total) {
					$matching_rule = false;
			}

			if ($matching_rule) {
				$applicable_fees_data[$fees_rules['fee_title']] = array('rate' => $fees_rules['rate'],'description' => $fees_rules['description']);
				$applicable_fees[] = array($fees_rules['rate'], $fees_rules['amount_effect']);
			}
		}
	}

		if (!empty ($applicable_fees)) {
			
			foreach ($applicable_fees as $fee) {
			
				$order_sub_net = ($order_sub_total * $fee[0])/100;
				$delivery_net = ($order_delivery * $fee[0])/100;

				if ($fee[1] == 'full') {
	
				$order_sub_total = $order_sub_total - $order_sub_net;
				$order_delivery = $order_delivery - $delivery_net;
				
				} elseif ($fee[1] == 'order_total') {
				
				$order_sub_total = $order_sub_total - $order_sub_net;
	
				} elseif ($fee[1] == 'delivery') {

				$order_delivery = $order_delivery - $delivery_net;
				
				}
			}
			$order_net = $order_delivery + $order_sub_total;
		} else {
			$order_net = $order_total;
		}

	return apply_filters('frozr_order_seller_net_profit',array('total_profit' => $order_net, 'fee_details' => $applicable_fees_data), $order );
} 
//Order listing body
function frozr_order_listing_body($oid, $sts) {
	global $post;
	
	$order_post = get_post( $oid );
	$the_order = new WC_Order( $oid );
	$order_author = frozr_get_store_info($post->post_author);
	?>
	<tr>
		<td>
			<?php echo '<a data-ajax="false" href="' . wp_nonce_url( add_query_arg( array( 'order_id' => $the_order->id ), home_url( '/dashboard/orders/') ), 'frozr_view_order' ) . '"><strong>' . sprintf( __( 'Order %s', 'frozr' ), esc_attr( $the_order->get_order_number() ) ) . '</strong></a>'; ?>
		</td>
		<td>
			<?php echo wc_price (frozr_get_seller_total_order($the_order), array('currency' => $the_order->get_order_currency())); ?></br>
			<?php if ( $the_order->payment_method_title ) {
			echo '<span class="meta">' . __('Via','frozr') . ' ' . esc_html( $the_order->payment_method_title ) . '</span>';
			} ?>
		</td>
		<td>
			<?php printf( '<mark class="%s tips" data-tip="%s">%s</mark>', sanitize_title( $the_order->get_status() ), wc_get_order_status_name( $the_order->get_status() ), wc_get_order_status_name( $the_order->get_status() ) ); ?>
		</td>
		<td>
			<?php
			$customer_tip = '';

			if ( $address = $the_order->get_formatted_billing_address() ) {
				$customer_tip .= __( 'Billing:', 'frozr' ) . ' ' . $address;
			}

			if ( $the_order->billing_phone ) {
				$customer_tip .= __( 'Tel:', 'frozr' ) . ' ' . $the_order->billing_phone;
			}

			echo '<div class="order_poster">';

			if ( $the_order->user_id ) {
				$user_info = get_userdata( $the_order->user_id );
			}

			if ( ! empty( $user_info ) ) {

				$username = '<a title="' . str_replace('<br/>', ', ', $customer_tip) . '">';

			if ( $user_info->first_name || $user_info->last_name ) {
				$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
			} else {
				$username .= esc_html( ucfirst( $user_info->display_name ) );
			}

			$username .= '</a>';

			} else {
				$username = '<a title="' . str_replace('<br/>', ', ', $customer_tip) . '">';

			if ( $the_order->billing_first_name || $the_order->billing_last_name ) {
				$username = trim( $the_order->billing_first_name . ' ' . $the_order->billing_last_name );
			} else {
				$username = __( 'Guest', 'frozr' );
			}
				$username .= '</a>';
			}

			printf( __( 'by %s', 'frozr' ), $username );

			?>
			</div>
		</td>
		<td>
			<?php
			if ( '0000-00-00 00:00:00' == $post->post_date ) {
				$t_time = $h_time = __( 'Unpublished', 'frozr' );
			} else {
				$t_time    = get_the_time( __( 'Y/m/d g:i:s A', 'frozr' ), $post );
				$gmt_time  = strtotime( $post->post_date_gmt . ' UTC' );
				$time_diff = current_time( 'timestamp', 1 ) - $gmt_time;

				if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 )
					$h_time = sprintf( __( '%s ago', 'frozr' ), human_time_diff( $gmt_time, current_time( 'timestamp', 1 ) ) );
				else
					$h_time = get_the_time( __( 'Y/m/d', 'frozr' ), $post );
			}
			echo '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( apply_filters( 'post_date_column_time', $h_time, $post ) ) . '</abbr>';
			?>
		</td>
		<td>
			<?php if ($the_order->status == 'on-hold' || $the_order->status == 'pending'|| $the_order->status == 'failed') { ?>
			<a class="order_status_butn processing" data-status="processing" data-orderid="<?php echo $oid; ?>" href="#" title="<?php _e( 'Processing', 'frozr' ); ?>"><i class="fs-icon-motorcycle">&nbsp;</i><?php _e( 'Processing', 'frozr' ); ?></a>
			<?php } if ($the_order->status == 'on-hold' || $the_order->status == 'pending' || $the_order->status == 'processing' || $the_order->status == 'failed') { ?>
			<a class="order_status_butn complete" data-status="completed" data-orderid="<?php echo $oid; ?>" href="#" title="<?php _e( 'Complete', 'frozr' ); ?>"><i class="fs-icon-check">&nbsp;</i><?php _e( 'Complete', 'frozr' ); ?></a>
			<a class="order_status_butn cancelled" data-status="cancelled" data-orderid="<?php echo $oid; ?>" href="#" title="<?php _e( 'Cancelled', 'frozr' ); ?>"><i class="fs-icon-times-circle">&nbsp;</i><?php _e( 'Cancelled', 'frozr' ); ?></a>
			<?php } if ($the_order->status == 'on-hold' || $the_order->status == 'pending' || $the_order->status == 'processing' || $the_order->status == 'cancelled' || $the_order->status == 'failed') { ?>
			<a class="order_status_butn refunded" data-status="refunded" data-orderid="<?php echo $oid; ?>" href="#" title="<?php _e( 'Refunded', 'frozr' ); ?>"><i class="fs-icon-mail-reply-all">&nbsp;</i><?php _e( 'Refunded', 'frozr' ); ?></a>
			<?php } if ($the_order->status == 'on-hold' || $the_order->status == 'processing' || $the_order->status == 'failed') { ?>
			<a class="order_status_butn pending" data-status="pending" data-orderid="<?php echo $oid; ?>" href="#" title="<?php _e( 'Pending', 'frozr' ); ?>"><i class="fs-icon-cutlery">&nbsp;</i><?php _e( 'Pending', 'frozr' ); ?></a>
			<?php } if ($the_order->status != 'completed' && $the_order->status != 'refunded') { ?>
			<a class="order_status_butn failed" data-status="failed" data-orderid="<?php echo $oid; ?>" href="#" title="<?php _e( 'Failed', 'frozr' ); ?>"><i class="fs-icon-minus-circle">&nbsp;</i><?php _e( 'Failed', 'frozr' ); ?></a>
			<?php } ?>
			<a class="order_print_butn" data-orderid="<?php echo $oid; ?>" href="#" title="<?php _e( 'Print', 'frozr' ); ?>"><i class="fs-icon-print">&nbsp;</i><?php _e( 'Print', 'frozr' ); ?></a>
			
		</td>
		<?php if (is_super_admin()) { ?>
		<td>
		<?php echo get_the_author_meta('login', $post->post_author) . ' (' . $order_author['store_name'] . ')'; ?>
		</td>
		<?php } ?>
		<?php do_action('frozr_after_order_listing_table_body', $oid, $sts); ?>
	</tr>
<?php
}

/**
 * Orders items table
 */
function frozr_order_items_table($order) {

// Get line items
$line_items          = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$line_items_fee      = $order->get_items( 'fee' );

if ( wc_tax_enabled() ) {
	$order_taxes         = $order->get_taxes();
	$tax_classes         = WC_Tax::get_tax_classes();
	$classes_options     = array();
	$classes_options[''] = __( 'Standard', 'frozr' );

	if ( ! empty( $tax_classes ) ) {
		foreach ( $tax_classes as $class ) {
			$classes_options[ sanitize_title( $class ) ] = $class;
		}
	}

	// Older orders won't have line taxes so we need to handle them differently :(
	$tax_data = '';
	if ( $line_items ) {
		$check_item = current( $line_items );
		$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
	} elseif ( $line_items_fee ) {
		$check_item = current( $line_items_fee );
		$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
	}

	$legacy_order     = ! empty( $order_taxes ) && empty( $tax_data ) && ! is_array( $tax_data );
	$show_tax_columns = ! $legacy_order || sizeof( $order_taxes ) === 1;
}

?>
<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
	<thead>
		<tr>
			<th class="order_item" <?php if (!isset( $_GET['print'] )) { echo 'colspan="2"'; } ?>><?php _e( 'Item', 'frozr' ); ?></th>

			<?php do_action( 'woocommerce_admin_order_item_headers', $order ); ?>

			<th class="item_cost"><?php _e( 'Cost', 'frozr' ); ?></th>
			<th class="order_quantity"><?php _e( 'Qty', 'frozr' ); ?></th>
			<th class="line_cost"><?php _e( 'Total', 'frozr' ); ?></th>

			<?php
			if ( empty( $legacy_order ) && ! empty( $order_taxes ) ) :
				foreach ( $order_taxes as $tax_id => $tax_item ) :
					$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
					$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'frozr' );
					$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'frozr' );
					?>
					<th class="line_tax tips" data-tip="<?php
					echo esc_attr( $tax_item['name'] . ' (' . $tax_class_name . ')' );
					?>">
					<?php echo esc_attr( $column_label ); ?>
					</th>
					<?php
				endforeach;
			endif;
			?>
		</tr>
	</thead>
	<tbody id="order_line_items">
		<?php
		foreach ( $line_items as $item_id => $item ) {
			$_product  = $order->get_product_from_item( $item );
			$item_meta = $order->get_item_meta( $item_id );

			include( FROZR_WOO_TMP .'/orders/html-order-item.php' );

			do_action( 'woocommerce_order_item_' . $item['type'] . '_html', $item_id, $item, $order );
		}
		do_action( 'woocommerce_admin_order_items_after_line_items', $order->id );
		?>
	</tbody>
	<tbody id="order_fee_line_items">
		<?php
		foreach ( $line_items_fee as $item_id => $item ) {
			include( FROZR_WOO_TMP .'/orders/html-order-fee.php' );
		}
		do_action( 'woocommerce_admin_order_items_after_fees', $order->id );
		?>
	</tbody>
	<tbody id="order_refunds">
		<?php
		if ( $refunds = $order->get_refunds() ) {
			foreach ( $refunds as $refund ) {
				include( FROZR_WOO_TMP .'/orders/html-order-refund.php' );
			}
			do_action( 'woocommerce_admin_order_items_after_refunds', $order->id );
		}
		?>
	</tbody>
</table>
<?php
$coupons = $order->get_items( array( 'coupon' ) );
if ( $coupons ) { ?>
	<div class="wc-used-coupons">
		<ul class="wc_coupon_list"><?php
		echo '<li><strong>' . __( 'Coupon(s) Used', 'frozr' ) . '</strong></li>';
		foreach ( $coupons as $item_id => $item ) {
		$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item['name'] ) );

		$link = $post_id ? add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), admin_url( 'post.php' ) ) : add_query_arg( array( 's' => $item['name'], 'post_status' => 'all', 'post_type' => 'shop_coupon' ), admin_url( 'edit.php' ) );

		echo '<li class="order_code"><a href="' . esc_url( $link ) . '" class="order_tips" data-tip="' . esc_attr( wc_price( $item['discount_amount'], array( 'currency' => $order->get_order_currency() ) ) ) . '"><span>' . esc_html( $item['name'] ). '</span></a></li>';
		}
		?></ul>
	</div>
<?php } ?>
<table class="wc-order-totals">
	<tr>
		<td class="order_label"><?php _e( 'Discount', 'frozr' ); if (!isset( $_GET['print'] )) { echo '<span class="order_tips" data-tip="'. esc_attr( "This is the total discount. Discounts are defined per line item.", "frozr" ). '">[?]</span>'; } ?>:</td>
		<td class="order_total">
		<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_order_currency() ) ); ?>
		</td>
		<td width="1%"></td>
	</tr>

	<?php do_action( 'woocommerce_admin_order_totals_after_discount', $order->id ); ?>

	<?php if ( wc_tax_enabled() ) : ?>
		<?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
			<tr>
				<td class="order_label"><?php echo $tax->label; ?>:</td>
				<td class="order_total"><?php
				if ( ( $refunded = $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) > 0 ) {
					echo '<del>' . strip_tags( $tax->formatted_amount ) . '</del> <ins>' . wc_price( $tax->amount - $refunded, array( 'currency' => $order->get_order_currency() ) ) . '</ins>';
				} else {
					echo $tax->formatted_amount;
				}
				?></td>
				<td width="1%"></td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_admin_order_totals_after_tax', $order->id ); ?>

	<tr>
		<td class="order_label"><?php _e( 'Order Total', 'frozr' ); ?>:</td>
		<td class="order_total">
		<div class="order_view"><?php echo $order->get_formatted_order_total(); ?></div>
		</td>
		<td width="1%"></td>
	</tr>

	<?php do_action( 'woocommerce_admin_order_totals_after_total', $order->id ); ?>

	<tr>
		<td class="order_label refunded-total"><?php _e( 'Refunded', 'frozr' ); ?>:</td>
		<td class="order_total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_order_currency() ) ); ?></td>
		<td width="1%"></td>
	</tr>

	<?php do_action( 'woocommerce_admin_order_totals_after_refunded', $order->id ); ?>

</table>

<?php
}

/**
 * Order general details
 */
function frozr_order_general_details($order) {
	$cod_notice = (get_post_meta($order->id, '_payment_method', true) == 'cod') ? __('Note: The website fees of this order will be detected from the current restaurant website balance. If the balance is insufficient, it will go in minus.','frozr') : '';
?>
<div class="or-body general-details">
	<table class="order_details_table">
		<tr>
			<td><?php _e( 'Order Status:', 'frozr' ); ?></td>
			<td class="status-label"><?php echo $order->status; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Payment Method:', 'frozr' ); ?></td>
			<td><?php echo esc_html( $order->payment_method_title ) . ' <span class="frozr_cod_notice">'. $cod_notice . '</span>'; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Order Date:', 'frozr' ); ?></td>
			<td><?php echo $order->order_date; ?></td>
		</tr>
	</table>
</div>
<?php
}

/**
 * Order customer details
 */
function frozr_order_customer_details($order) { ?>

<div class="or-body general-details">
	<table class="order_details_table">
		<tr>
			<td><?php _e( 'Customer:', 'frozr' ); ?></td>
			<td>
			<?php
			$customer_user = absint( get_post_meta( $order->id, '_customer_user', true ) );
			$customer_userdata = get_userdata( $customer_user );
			echo $customer_userdata->display_name;
			?>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Email:', 'frozr' ); ?></td>
			<td><?php echo esc_html( get_post_meta( $order->id, '_billing_email', true ) ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Phone:', 'frozr' ); ?></td>
			<td><?php echo esc_html( get_post_meta( $order->id, '_billing_phone', true ) ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Address:', 'frozr' ); ?></td>
			<td><?php echo $order->get_formatted_billing_address(); ?></td>
		</tr>
	</table>

	<?php
	if ( get_option( 'woocommerce_enable_order_comments' ) != 'no' ) {
		$customer_note = get_post_field( 'post_excerpt', $order->id );

		if ( !empty( $customer_note ) ) { ?>
			<div class="alert alert-success customer-note">
				<strong><?php _e( 'Customer Note:', 'frozr' ) ?></strong><br>
				<?php echo wp_kses_post( $customer_note ); ?>
			</div>
		<?php } ?>
	<?php } ?>
</div>
<?php
}

/**
 * Add order note via ajax
 */
add_action( 'wp_ajax_frozr_add_order_note', 'frozr_add_order_note' );
function frozr_add_order_note() {
	ob_start();

	check_ajax_referer( 'add-order-note', 'security' );

	$order_post = get_post( intval($_POST['post_id']) );
	$author = $order_post->post_author;

	if ( ! current_user_can( 'edit_shop_orders' ) || $author != get_current_user_id() && !is_super_admin() ) {
		die(-1);
	}

	$post_id   = absint( $_POST['post_id'] );
	$note      = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
	$note_type = $_POST['note_type'];

	$is_customer_note = $note_type == 'customer' ? 1 : 0;

	if ( $post_id > 0 ) {
		$order      = wc_get_order( $post_id );
		$comment_id = $order->add_order_note( $note, $is_customer_note, true );

		echo '<li rel="' . esc_attr( $comment_id ) . '" class="note ';
		if ( $is_customer_note ) {
			echo 'customer-note';
		}
		echo '"><div class="note_content">';
		echo wpautop( wptexturize( $note ) );
		echo '</div><p class="meta"><a href="#" class="delete_note">'.__( 'Delete note', 'frozr' ).'</a></p>';
		echo '</li>';
	}

	// Quit out
	die();
}

/**
 * Delete order note via ajax
 */
add_action( 'wp_ajax_frozr_delete_order_note', 'frozr_delete_order_note' );
function frozr_delete_order_note() {
	ob_start();

	check_ajax_referer( 'delete-order-note', 'security' );

	if ( ! current_user_can( 'edit_shop_orders' ) ) {
		die(-1);
	}

	$note_id = (int) $_POST['note_id'];

	if ( $note_id > 0 ) {
	wp_delete_comment( $note_id );
	}

	// Quit out
	die();
}
//some ajax function to process the order actions
add_action( 'wp_ajax_frozr_set_order_status', 'set_order_status' );
function frozr_set_order_status() {
	ob_start();

	check_ajax_referer( 'set_order_status', 'security' );
	
	$order_post = get_post( intval($_POST['order_id']) );
	$author = $order_post->post_author;

	// Check permissions again and make sure we have what we need
	if ( !current_user_can( 'frozer' ) && !frozr_is_seller_enabled(get_current_user_id()) || empty( $order_post->ID ) || $author != get_current_user_id() && !is_super_admin()) {
		die( -1 );
	}
	$order = new WC_Order( intval($_POST['order_id']) );
	
	if ($_POST['order_sts'] == 'refunded' && $order_post->post_status != 'wc-completed' && $order_post->post_status != 'wc-refunded') {
		$order->update_status( 'refunded', __( 'Order should be refunded. Customer please send a message to  ', 'frozr' ) . get_option( 'admin_email' ) . __(' in reference to this order ID and your payment account details and method to complete refund.','frozr') );
	} elseif ($_POST['order_sts'] == 'cancelled' && $order_post->post_status != 'wc-completed' && $order_post->post_status != 'wc-refunded' && $order_post->post_status != 'wc-cancelled') {
		$order->update_status( 'cancelled', __( 'Order has been cancelled.', 'frozr' ) );
	} elseif ($_POST['order_sts'] == 'pending' && $order_post->post_status != 'wc-completed' && $order_post->post_status != 'wc-refunded' && $order_post->post_status != 'wc-cancelled' && $order_post->post_status != 'wc-pending') {
		$order->update_status( 'pending', __( 'Order is being prepared.', 'frozr' ) );
	} elseif ($_POST['order_sts'] == 'processing' && $order_post->post_status != 'wc-completed' && $order_post->post_status != 'wc-refunded' && $order_post->post_status != 'wc-cancelled' && $order_post->post_status != 'wc-processing') {
		$order->update_status( 'processing', __( 'Order on its way to customer.', 'frozr' ) );
	} elseif ($_POST['order_sts'] == 'completed' && $order_post->post_status != 'wc-completed' && $order_post->post_status != 'wc-refunded' && $order_post->post_status != 'wc-cancelled' ) {
		$order->update_status( 'completed', __( 'Customer has received the order.', 'frozr' ) );
	} elseif ($_POST['order_sts'] == 'failed' && $order_post->post_status != 'wc-completed' && $order_post->post_status != 'wc-refunded' ) {
		$order->update_status( 'failed', __( 'Order failed to process.', 'frozr' ) );
	}

	die();
}
/**
 * Update the child order status when a parent order status is changed
 *
 * @param int $order_id
 * @param string $old_status
 * @param string $new_status
 */
function frozr_on_order_status_change( $order_id, $old_status, $new_status ) {

    // if any child orders found, change the orders as well
    $sub_orders = get_children( array( 'post_parent' => $order_id, 'post_type' => 'shop_order' ) );
    if ( $sub_orders ) {
        foreach ($sub_orders as $order_post) {
            $order = new WC_Order( $order_post->ID );
            $order->update_status( $new_status );
        }
    }	
}

add_action( 'woocommerce_order_status_changed', 'frozr_on_order_status_change', 10, 3 );

/**
 * Add user commission on order complete
 *
 * @param int $order_id
 */
function frozr_add_user_balance( $order_id ) {
	$order_post = get_post( $order_id );
	$order = new WC_Order( $order_id );
	$order_customer = $order->get_user_id();
	$order_seller = $order_post->post_author;
	
	if ( $order_customer ) {
		$user_info = get_userdata( $order_customer );
		$seller_info = get_user_meta( $order_seller, 'frozr_profile_settings', true);
		$msg_args = apply_filters('frozr_add_user_balance_message_args',array (
			'to' => sanitize_email($user_info->user_email),
			'cusname' => sanitize_text_field($user_info->display_name),
			'orid' => $order_id,
			'ordate' => $order->order_date,
			'restname' => sanitize_text_field( $seller_info['store_name'] ),
			'revlink' => frozr_get_store_url($order_seller) . '?make_review=' . $order_id,
		));
	}
	if ( $order->status == 'completed' && get_post_meta($order->id, 'frozr_order_website_fee_balance_detected', true) == null) {
		$seller_profit = frozr_calculate_order_fees($order);

		$seller_profit_val = floatval($seller_profit['total_profit']);
		$website_profit = floatval(frozr_get_seller_total_order($order)) - $seller_profit_val;
		$user_current_balance = floatval(get_user_meta($order_seller,"_restaurant_balance", true));

		if (get_post_meta($order->id, '_payment_method', true) == 'cod') {
			$seller_new_balance = $user_current_balance - $website_profit;
			
			update_user_meta($order_seller, "_restaurant_balance",$seller_new_balance);
			update_post_meta($order_id, 'frozr_order_website_fee_balance_detected', array($website_profit, $user_current_balance, $seller_new_balance));
		} else {
			update_user_meta($order_seller, "_restaurant_balance",($seller_profit_val + $user_current_balance));
		}
		update_post_meta($order_id, 'frozr_fees_on_order', $seller_profit['fee_details']);
		update_post_meta($order_id, 'frozr_order_seller_profit', $seller_profit_val);
		update_post_meta($order_id, 'frozr_order_website_fee', $website_profit);
		
		frozr_send_msgs($msg_args, 'seller_rating');
	}
}
add_action( 'woocommerce_order_status_changed', 'frozr_add_user_balance', 99, 1 );

/**
 * Delete sub orders when parent order is trashed
 *
 * @param int $post_id
 */
function frozr_admin_on_trash_order( $post_id ) {
    $post = get_post( $post_id );

    if ( $post->post_type == 'shop_order' && $post->post_parent == 0 ) {
        $sub_orders = get_children( array( 'post_parent' => $post_id, 'post_type' => 'shop_order' ) );

        if ( $sub_orders ) {
            foreach ($sub_orders as $order_post) {
                wp_trash_post( $order_post->ID );
            }
        }
    }
}

add_action( 'wp_trash_post', 'frozr_admin_on_trash_order' );

/**
 * Untrash sub orders when parent orders are untrashed
 *
 * @param int $post_id
 */
function frozr_admin_on_untrash_order( $post_id ) {
    $post = get_post( $post_id );

    if ( $post->post_type == 'shop_order' && $post->post_parent == 0 ) {
        $sub_orders = get_children( array( 'post_parent' => $post_id, 'post_type' => 'shop_order' ) );

        if ( $sub_orders ) {
            foreach ($sub_orders as $order_post) {
                wp_untrash_post( $order_post->ID );
            }
        }
    }
}

add_action( 'wp_untrash_post', 'frozr_admin_on_untrash_order' );
/**
 * Delete sub orders and from frozr sync table when a order is deleted
 *
 * @param int $post_id
 */
function frozr_admin_on_delete_order( $post_id ) {
    $post = get_post( $post_id );

    if ( $post->post_type == 'shop_order' ) {
        $sub_orders = get_children( array( 'post_parent' => $post_id, 'post_type' => 'shop_order' ) );

        if ( $sub_orders ) {
            foreach ($sub_orders as $order_post) {
                wp_delete_post( $order_post->ID );
            }
        }
    }
}
add_action( 'delete_post', 'frozr_admin_on_delete_order' );

/**
 * update the refunded time
 *
 * @param int $order_id
 */
function frozr_update_refund_date( $order_id ) {
	$order_post = get_post( $order_id );
	$order = new WC_Order( $order_id );
		
	if ( $order->status == 'refunded' ) {

	update_post_meta($order_post->ID, '_refunded_date', current_time('ymd'));
		
	}
}
add_action( 'woocommerce_order_status_changed', 'frozr_update_refund_date', 99, 1 );

/**
 * Mark the parent order as complete when all the child order are completed
 *
 * @param int $order_id
 * @param string $old_status
 * @param string $new_status
 * @return void
 */
function frozr_on_child_order_status_change( $order_id, $old_status, $new_status ) {
    $order_post = get_post( $order_id );

    // we are monitoring only child orders
    if ( $order_post->post_parent === 0 ) {
        return;
    }

    // get all the child orders and monitor the status
    $parent_order_id = $order_post->post_parent;
    $sub_orders = get_children( array( 'post_parent' => $parent_order_id, 'post_type' => 'shop_order' ) );


    // return if any child order is not completed
    $all_complete = true;

    if ( $sub_orders ) {
        foreach ($sub_orders as $sub) {
            $order = new WC_Order( $sub->ID );

            if ( $order->status != 'completed' ) {
                $all_complete = false;
            }
        }
    }

    // seems like all the child orders are completed
    // mark the parent order as complete
    if ( $all_complete ) {
        $parent_order = new WC_Order( $parent_order_id );
        $parent_order->update_status( 'completed', __( 'Mark parent order completed as all child orders are completed.', 'frozr' ) );
    }
}

add_action( 'woocommerce_order_status_changed', 'frozr_on_child_order_status_change', 99, 3 );
/**
 * Monitors a new order and attempts to create sub-orders
 *
 * If an order contains products from multiple vendor, we can't show the order
 * to each seller dashboard. That's why we need to divide the main order to
 * some sub-orders based on the number of sellers.
 *
 * @param int $parent_order_id
 * @return void
 */
function frozr_create_sub_order( $parent_order_id ) {

    $parent_order = new WC_Order( $parent_order_id );
    $order_items = $parent_order->get_items();
	
    $sellers = array();
    foreach ($order_items as $item) {
        $seller_id = get_post_field( 'post_author', $item['product_id'] );
        $sellers[$seller_id][] = $item;
    }

    // Return if we have only ONE seller
    if ( count( $sellers ) == 1 ) {
        $temp = array_keys( $sellers );
        $seller_id = reset( $temp );
        wp_update_post( array( 'ID' => $parent_order_id, 'post_author' => $seller_id ) );
        return;
    }

    // flag it as it has a suborder
    update_post_meta( $parent_order_id, 'has_sub_order', true );

    // seems like we've got multiple sellers
    foreach ($sellers as $seller_id => $seller_products ) {
        frozr_create_seller_order( $parent_order, $seller_id, $seller_products );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'frozr_create_sub_order' );

/**
 * Creates a sub order
 *
 * @param int $parent_order
 * @param int $seller_id
 * @param array $seller_products
 */
function frozr_create_seller_order( $parent_order, $seller_id, $seller_products ) {
    $order_data = apply_filters( 'frozr_new_sub_order_data', array(
        'post_type'     => 'shop_order',
        'post_title'    => sprintf( __( 'Order &ndash; %s', 'frozr' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'frozr' ) ) ),
        'post_status'   => 'on-hold',
        'ping_status'   => 'closed',
        'post_excerpt'  => isset( $posted['order_comments'] ) ? $posted['order_comments'] : '',
        'post_author'   => $seller_id,
        'post_parent'   => $parent_order->id,
        'post_password' => uniqid( 'order_' )   // Protects the post just in case
    ) );

    $order_id = wp_insert_post( $order_data );

    if ( $order_id && !is_wp_error( $order_id ) ) {

        $order_total = $order_tax = 0;
        $product_ids = array();

        // now insert line items
        foreach ($seller_products as $item) {
            $order_total += (float) $item['line_total'];
            $order_tax += (float) $item['line_tax'];
            $product_ids[] = $item['product_id'];

            $item_id = wc_add_order_item( $order_id, array(
                'order_item_name' => $item['name'],
                'order_item_type' => 'line_item'
            ) );

            if ( $item_id ) {
                wc_add_order_item_meta( $item_id, '_qty', $item['qty'] );
                wc_add_order_item_meta( $item_id, '_tax_class', $item['tax_class'] );
                wc_add_order_item_meta( $item_id, '_product_id', $item['product_id'] );
                wc_add_order_item_meta( $item_id, '_variation_id', $item['variation_id'] );
                wc_add_order_item_meta( $item_id, '_line_subtotal', $item['line_subtotal'] );
                wc_add_order_item_meta( $item_id, '_line_total', $item['line_total'] );
                wc_add_order_item_meta( $item_id, '_line_tax', $item['line_tax'] );
                wc_add_order_item_meta( $item_id, '_line_subtotal_tax', $item['line_subtotal_tax'] );
            }
        } // foreach

        $bill_ship = array(
            '_billing_country', '_billing_first_name', '_billing_last_name', '_billing_company',
            '_billing_address_1', '_billing_address_2', '_billing_city', '_billing_state', '_billing_postcode',
            '_billing_email', '_billing_phone'
        );

        // save billing and shipping address
        foreach ($bill_ship as $val) {
            $order_key = ltrim( $val, '_' );
            update_post_meta( $order_id, $val, $parent_order->$order_key );
        }

        // add coupons if any
        frozr_create_sub_order_coupon( $parent_order, $order_id, $product_ids );
        $discount = frozr_sub_order_get_total_coupon( $order_id );

        // calculate the total
        $order_in_total = $order_total + $order_tax - $discount;

        // set order meta
        update_post_meta( $order_id, '_payment_method',         $parent_order->payment_method );
        update_post_meta( $order_id, '_payment_method_title',   $parent_order->payment_method_title );

        update_post_meta( $order_id, '_order_discount',         woocommerce_format_decimal( $discount ) );
        update_post_meta( $order_id, '_cart_discount',          '0' );
        update_post_meta( $order_id, '_order_tax',              woocommerce_format_decimal( $order_tax ) );
        update_post_meta( $order_id, '_order_shipping_tax',     '0' );
        update_post_meta( $order_id, '_order_total',            woocommerce_format_decimal( $order_in_total ) );
        update_post_meta( $order_id, '_order_key',              apply_filters('woocommerce_generate_order_key', uniqid('order_') ) );
        update_post_meta( $order_id, '_customer_user',          $parent_order->customer_user );
        update_post_meta( $order_id, '_order_currency',         get_post_meta( $parent_order->id, '_order_currency', true ) );
        update_post_meta( $order_id, '_prices_include_tax',     $parent_order->prices_include_tax );
        update_post_meta( $order_id, '_customer_ip_address',    get_post_meta( $parent_order->id, '_customer_ip_address', true ) );
        update_post_meta( $order_id, '_customer_user_agent',    get_post_meta( $parent_order->id, '_customer_user_agent', true ) );

        do_action( 'frozr_checkout_update_order_meta', $order_id );

    } // if order
}
/**
 * Get discount coupon total from a order
 *
 * @global WPDB $wpdb
 * @param int $order_id
 * @return int
 */
function frozr_sub_order_get_total_coupon( $order_id ) {
    global $wpdb;

    $sql = $wpdb->prepare( "SELECT SUM(oim.meta_value) FROM {$wpdb->prefix}woocommerce_order_itemmeta oim
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi ON oim.order_item_id = oi.order_item_id
            WHERE oi.order_id = %d AND oi.order_item_type = 'coupon'", $order_id );

    $result = $wpdb->get_var( $sql );
    if ( $result ) {
        return $result;
    }

    return 0;
}

/**
 * Create coupons for a sub-order if neccessary
 *
 * @param WC_Order $parent_order
 * @param int $order_id
 * @param array $product_ids
 * @return type
 */
function frozr_create_sub_order_coupon( $parent_order, $order_id, $product_ids ) {
    $used_coupons = $parent_order->get_used_coupons();

    if ( ! count( $used_coupons ) ) {
        return;
    }

    if ( $used_coupons ) {
        foreach ($used_coupons as $coupon_code) {
            $coupon = new WC_Coupon( $coupon_code );

            if ( $coupon && !is_wp_error( $coupon ) && array_intersect( $product_ids, $coupon->product_ids ) ) {

                // we found some match
                $item_id = wc_add_order_item( $order_id, array(
                    'order_item_name' => $coupon_code,
                    'order_item_type' => 'coupon'
                ) );

                // Add line item meta
                if ( $item_id ) {
                    wc_add_order_item_meta( $item_id, 'discount_amount', isset( WC()->cart->coupon_discount_amounts[ $coupon_code ] ) ? WC()->cart->coupon_discount_amounts[ $coupon_code ] : 0 );
                }
            }
        }
    }
}
/**
 * Show sub-orders on a parent order if available
 *
 * @param WC_Order $parent_order
 * @return void
 */
function frozr_order_show_suborders( $parent_order ) {

	$sub_orders = get_children( array( 'post_parent' => $parent_order->id, 'post_type' => 'shop_order' ) );

	if ( !$sub_orders ) {
		return;
	}
	?>
	<header>
		<h2><?php _e( 'Sub Orders', 'frozr' ); ?></h2>
	</header>

	<div class="frozr-info">
		<strong><?php _e( 'Note:', 'frozr' ); ?></strong>
		<?php _e( 'This order has items from multiple vendors/restaurants. So we divided this order into multiple orders, each will be handled by their respective vendor independently.', 'frozr' ); ?>
	</div>

	<table class="shop_table my_account_orders table table-striped">

		<thead>
			<tr>
				<th class="order-number"><span class="nobr"><?php _e( 'Order', 'frozr' ); ?></span></th>
				<th class="order-date"><span class="nobr"><?php _e( 'Date', 'frozr' ); ?></span></th>
				<th class="order-status"><span class="nobr"><?php _e( 'Status', 'frozr' ); ?></span></th>
				<th class="order-total"><span class="nobr"><?php _e( 'Total', 'frozr' ); ?></span></th>
				
				<?php do_action('frozr_after_order_suborders_table_header', $parent_order); ?>
				
				<th class="order-actions">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($sub_orders as $order_post) {
			$order = new WC_Order( $order_post->ID );
			$status = 'wc-' . $order->get_status();
			$item_count = $order->get_item_count();
		?>
			<tr class="order">
				<td class="order-number">
					<a href="<?php echo $order->get_view_order_url(); ?>">
						<?php echo $order->get_order_number(); ?>
					</a>
				</td>
				<td class="order-date">
					<time datetime="<?php echo date('Y-m-d', strtotime( $order->order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order->order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></time>
				</td>
				<td class="order-status" style="text-align:left; white-space:nowrap;">
					<?php echo ucfirst( __( $status->name, 'frozr' ) ); ?>
				</td>
				<td class="order-total">
					<?php echo sprintf( _n( '%s for %s item', '%s for %s items', $item_count, 'frozr' ), $order->get_formatted_order_total(), $item_count ); ?>
				</td>
				
				<?php do_action('frozr_after_order_suborders_table_body', $order_post); ?>
				
				<td class="order-actions">
					<?php
					$actions = array();

					$actions['view'] = array(
					'url'  => $order->get_view_order_url(),
					'name' => __( 'View', 'frozr' )
					);

					$actions = apply_filters( 'frozr_order_sub_orders_actions', $actions, $order );

					foreach( $actions as $key => $action ) {
						echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
					}
					?>
				</td>
			</tr>
	<?php } ?>
	</tbody>
	</table>
	<?php
}
add_action( 'woocommerce_order_details_after_order_table', 'frozr_order_show_suborders' );

//send message to restaurant owner on new order
add_filter( 'woocommerce_email_recipient_new_order', 'frozr_new_order_restaurant_email', 10, 2 );
function frozr_new_order_restaurant_email( $recipient, $order ) {
	$seller_id = get_post_field( 'post_author', $order->id );
	
	if ( $seller_id ) {
		$seller = get_user_by( 'id', $seller_id );
	
		$recipient .= ', '. $seller->user_email;
	}
	return $recipient;
}