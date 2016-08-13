<?php
/**
 * All Related Item Management Functions
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Includes frontend-dashboard scripts for seller
 *
 * @return void
 */
function frozr_frontend_dashboard_scripts() {

	wp_enqueue_script( 'upload' );
	wp_enqueue_script( 'post' );
	wp_enqueue_script( 'chosen' );
	wp_enqueue_script( 'accounting' );
	wp_enqueue_script( 'frozr-product-editor' );
	wp_enqueue_script( 'reviews' );
	
	do_action('frozr_after_frontend_dashboard_scripts');
}
//frozr dashboard menu
function frozr_get_dashboard_nav() {
	$urls = apply_filters('frozr_dashboard_nav_menu',array(
		'dashboard' => array(
			'title' => __( 'Dashboard', 'frozr'),
			'icon' => '<i class="fs-icon-dashboard"></i>',
			'url' => home_url( '/dashboard/home/' )
		),
		'dishes' => array(
			'title' => __( 'Products', 'frozr'),
			'icon' => '<i class="fs-icon-briefcase"></i>',
			'url' => home_url( '/dashboard/dishes/' )
		),
		'order' => array(
			'title' => __( 'Orders', 'frozr'),
			'icon' => '<i class="fs-icon-truck"></i>',
			'url' => home_url( '/dashboard/orders/' )
		),
		'coupon' => array(
			'title' => __( 'Coupons', 'frozr'),
			'icon' => '<i class="fs-icon-gift"></i>',
			'url' => home_url( '/dashboard/coupons/' )
		),
		'withdraw' => array(
			'title' => __( 'Withdraw', 'frozr'),
			'icon' => '<i class="fs-icon-money"></i>',
			'url' => home_url( '/dashboard/withdraw/' )
		),
		'settings' => array(
			'title' => __( 'Settings', 'frozr'),
			'icon' => '<i class="fs-icon-cog"></i>',
			'url' => home_url( '/dashboard/settings/' )
		),
		'sellers' => array(
			'title' => __( 'Sellers', 'frozr'),
			'icon' => '<i class="fs-icon-users"></i>',
			'url' => home_url( '/dashboard/sellers/' )
		),
	));
	if (is_super_admin()){
		array_splice($urls, 1, 1);
		array_splice($urls, 4, 1);
	} else {
		array_splice($urls, 6, 1);
	}
    return apply_filters( 'frozr_get_dashboard_nav', $urls );
}
//Dashboard main menu
function frozr_dashboard_nav( $active_menu ) {
    $urls = frozr_get_dashboard_nav();
    $menu = '<ul class="frozr-dashboard-menu">';

    foreach ($urls as $key => $item) {
        $class = ( $active_menu == $key ) ? ' class="active"' : '';
        $menu .= sprintf( '<li%s><a href="%s">%s %s</a></li>', $class, $item['url'], $item['icon'], $item['title'] );
    }
    $menu .= '</ul>';

    return $menu;
}
//Dashboard sidebar
function frozr_dash_sidebar($active_menu) {

	$current_user = wp_get_current_user();
	$store_info = frozr_get_store_info( $current_user->ID );
	$userphoto = get_avatar( $current_user->ID, 50);
	$usercity = get_user_meta( $current_user->ID, 'billing_city', true );
	$usercountry = get_user_meta( $current_user->ID, 'billing_country', true );
	?>
	<div <?php if (frozr_mobile()) { echo 'data-role="panel" data-position="left" data-position-fixed="true" data-display="overlay" data-theme="a" id="dash-side-panel"'; } ?> class="frozr-dash-sidebar">
		<div class="user_details_dash">
			<?php echo $userphoto; ?>
			<span class="store_name_dash"><?php  if ( !empty( $store_info['store_name'] ) ) echo esc_html( $store_info['store_name'] ); else echo $current_user->display_name; ?></span>
			<?php if (!empty ($usercity) && !empty ($usercountry)) { ?>
			<span class="user_location_dash"><i class="fs-icon-map-marker"></i>&nbsp;<?php echo "$usercity $usercountry" ?></span>
			<?php } ?>
			<?php do_action('frozr_after_dashboard_sidebar_user_details'); ?>
		</div>
		<?php echo frozr_dashboard_nav( $active_menu ); ?>
	</div>
<?php
}
add_action('frozr_dash_sidebar', 'frozr_dash_sidebar', 1);

//Dashboard Total Sales
function frozr_dash_total_sales( $type = 'today', $start = '', $end = '', $user = '' ) {
	$website_fee = array();
	$total_coupon_usage = array();
	$total = array();
	$refunded_tax = array();	
	$taxes_total = array();
	$taxes = array();
	$refunded = array();
	$completed = array();
	$pending = array();
	$processing = array();
	$cancelled = array();
	$on_hold = array();
	
	$psts = ($type == 'begging') ? array('wc-refunded', 'wc-completed', 'wc-pending', 'wc-processing', 'wc-cancelled', 'wc-on-hold') :  array ('wc-completed');
	if (is_super_admin()) {
		$suser = ($user == 'all') ? '' : $user;
	} else {
		$suser = get_current_user_id();
	}
	$order_ids = get_posts( apply_filters('frozr_dashboard_total_sales_orders_args',array(
		'posts_per_page'	=> -1,
		'post_type'			=> 'shop_order',
		'orderby'			=> 'date',
		'author'			=> $suser,
		'order'				=> 'desc',
		'post_status'		=> $psts,
	) ));

	foreach ($order_ids as $order_id) {
		$order = new WC_Order($order_id);
		$complete_day = strtotime(get_post_meta($order_id->ID, '_completed_date', true));

		$website_profit = get_post_meta($order_id->ID, 'frozr_order_website_fee', true);

		if ($type == 'today') {
			if ( date('ymd', $complete_day) == current_time('ymd') ) {
				if (is_super_admin()) {
					$website_fee[] = $website_profit;			
				}
				$total_coupon_usage[] = frozr_sub_order_get_total_coupon( $order_id->ID );
				if ( wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes' || wc_tax_enabled() && is_super_admin()) {
					foreach ( $order->get_tax_totals() as $code => $tax ) {
						$taxes_total[] = $tax->amount;
						$taxes[$tax->label] = $tax->formatted_amount;
					}
				}
				$total[] = floatval(frozr_get_seller_total_order($order));
			}
		} elseif ($type == 'week') {
			if (date('ymd', $complete_day) > date('ymd', strtotime("-6 days")) ) {
				if (is_super_admin()) {
					$website_fee[] = $website_profit;			
				}
				$total_coupon_usage[] = frozr_sub_order_get_total_coupon( $order_id->ID );
				if ( wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes' || wc_tax_enabled() && is_super_admin()) {
					foreach ( $order->get_tax_totals() as $code => $tax ) {
						$taxes_total[] = $tax->amount;
						$taxes[$tax->label] = $tax->formatted_amount;
					}
				}
				$total[] = floatval(frozr_get_seller_total_order($order));
			}
		} elseif ($type == 'month') {
			if (date('ym', $complete_day) == current_time('ym') ) {
				if (is_super_admin()) {
					$website_fee[] = $website_profit;			
				}
				$total_coupon_usage[] = frozr_sub_order_get_total_coupon( $order_id->ID );
				if ( wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes' || wc_tax_enabled() && is_super_admin()) {
					foreach ( $order->get_tax_totals() as $code => $tax ) {
						$taxes_total[] = $tax->amount;
						$taxes[$tax->label] = $tax->formatted_amount;
					}
				}
				$total[] = floatval(frozr_get_seller_total_order($order));
			}
		} elseif ($type == 'lastmonth') {
			if (date('ymd', $complete_day) > date('ymd', strtotime("first day of last month")) && date('ymd', $complete_day) < date('ymd', strtotime("last day of last month")) ) {
				if (is_super_admin()) {
					$website_fee[] = $website_profit;			
				}
				$total_coupon_usage[] = frozr_sub_order_get_total_coupon( $order_id->ID );
				if ( wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes' || wc_tax_enabled() && is_super_admin()) {
					foreach ( $order->get_tax_totals() as $code => $tax ) {
						$taxes_total[] = $tax->amount;
						$taxes[$tax->label] = $tax->formatted_amount;
					}
				}
				$total[] = floatval(frozr_get_seller_total_order($order));
			}
		} elseif ($type == 'year') {
			if (date('y', $complete_day) == current_time('y') ) {
				if (is_super_admin()) {
					$website_fee[] = $website_profit;			
				}
				$total_coupon_usage[] = frozr_sub_order_get_total_coupon( $order_id->ID );
				if ( wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes' || wc_tax_enabled() && is_super_admin()) {
					foreach ( $order->get_tax_totals() as $code => $tax ) {
						$taxes_total[] = $tax->amount;
						$taxes[$tax->label] = $tax->formatted_amount;
					}
				}
				$total[] = floatval(frozr_get_seller_total_order($order));
			}
		} elseif ($type == 'custom' && $start != '' && $end != '') {
			
			if (date('ymd', $complete_day) > date('ymd', strtotime($start)) && date('ymd', $complete_day) < date('ymd', strtotime($end)) ) {
				if (is_super_admin()) {
					$website_fee[] = $website_profit;			
				}
				$total_coupon_usage[] = frozr_sub_order_get_total_coupon( $order_id->ID );
				if ( wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes' || wc_tax_enabled() && is_super_admin()) {
					foreach ( $order->get_tax_totals() as $code => $tax ) {
						$taxes_total[] = $tax->amount;
						$taxes[$tax->label] = $tax->formatted_amount;
					}
				}
				$total[] = floatval(frozr_get_seller_total_order($order));
			}
		} elseif ($type == 'begging') {
			$total_coupon_usage[] = frozr_sub_order_get_total_coupon( $order_id->ID );
			if ( wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes' || wc_tax_enabled() && is_super_admin()) {
				foreach ( $order->get_tax_totals() as $code => $tax ) {
					if ( ( $refunded_tax[] = $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) > 0 ) {
						$taxes_total[] = $tax->amount - $order->get_total_tax_refunded_by_rate_id( $tax->rate_id );
						$taxes[$tax->label] = $tax->formatted_amount. ' - ' . ($tax->amount - $order->get_total_tax_refunded_by_rate_id( $tax->rate_id )) . ' ' . __('(Refunded)','frozr');
					} else {
						$taxes_total[] = $tax->amount;
						$taxes[$tax->label] = $tax->formatted_amount;
					}
				}
			}
			$total[] = floatval(frozr_get_seller_total_order($order));
			if ($order_id->post_status == 'wc-refunded'){
				$refunded[] = floatval(frozr_get_seller_total_order($order));
			} elseif ($order_id->post_status == 'wc-completed'){
				$completed[] = floatval(frozr_get_seller_total_order($order));
				if (is_super_admin()) {
					$website_fee[] = $website_profit;			
				}
			} elseif ($order_id->post_status == 'wc-pending'){
				$pending[] = floatval(frozr_get_seller_total_order($order));
			} elseif ($order_id->post_status == 'wc-processing'){
				$processing[] = floatval(frozr_get_seller_total_order($order));
			} elseif ($order_id->post_status == 'wc-cancelled'){
				$cancelled[] = floatval(frozr_get_seller_total_order($order));
			} elseif ($order_id->post_status == 'wc-on-hold'){
				$on_hold[] = floatval(frozr_get_seller_total_order($order));
			}
		}
	}
	$total_sales = !empty ($completed) ? $completed : $total;
	$percent = (!is_super_admin()) ? floatval(array_sum($website_fee)) : floatval(array_sum($total_sales)) - floatval(array_sum($website_fee));
	$netsales = (!is_super_admin()) ? floatval(array_sum($total_sales)) - $percent : floatval(array_sum($website_fee));

	return apply_filters ('frozr_output_dash_totals', array (
	floatval(array_sum($total)),
	count($total),
	floatval(array_sum($total_coupon_usage)),
	count($total_coupon_usage),
	floatval(array_sum($refunded)),
	count($refunded),
	floatval(array_sum($completed)),
	count($completed),
	floatval(array_sum($pending)),
	count($pending),
	floatval(array_sum($processing)),
	count($processing),
	floatval(array_sum($cancelled)),
	count($cancelled),
	floatval(array_sum($on_hold)),
	count($on_hold),
	$percent,
	$netsales,
	floatval(array_sum($taxes_total)),
	floatval(array_sum($refunded_tax)),
	$taxes,
	), $type, $start, $end, $user);
}
//output dashboard totals body
function frozr_dashboard_totals( $type = 'today', $start = '', $end = '', $user = '' ) {
	$totals = frozr_dash_total_sales( $type, $start, $end, $user );
	?>
	<table data-role="table" data-mode="reflow" class="ui-responsive">
		<thead>
			<tr>
				<th data-priority="1"><?php _e('Gross','frozr'); ?></th>
				<th data-priority="3"><?php _e('Total Coupon Usage','frozr'); ?></th>
				<?php if (wc_tax_enabled() && is_super_admin() || wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes') { ?>
				<th data-priority="5"><?php _e('Taxes','frozr'); ?></th>			
				<th data-priority="5"><?php _e('Taxes Refunded','frozr'); ?></th>			
				<?php } ?>
				<?php if ($type == 'begging') { ?>
				<th data-priority="6"><?php _e('Uncompleted Orders','frozr'); ?></th>
				<th data-priority="7"><?php _e('Refunded Orders','frozr'); ?></th>
				<?php } ?>
				<th data-priority="4"><?php if (!is_super_admin()) echo get_bloginfo( 'name' ) . ' ' .__('Fees','frozr'); else echo __('Seller Fees','frozr'); ?></th>
				<?php do_action('frozr_before_dashboard_total_sales_table_header_net'); ?>
				<th data-priority="2"><?php _e('Net','frozr'); ?></th>
				<?php do_action('frozr_after_dashboard_total_sales_table_header_net'); ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo wc_price($totals[0]+$totals[2]+$totals[18]+$totals[19]); ?></td>
				<td><?php echo wc_price($totals[2]); ?></td>
				<?php if (wc_tax_enabled() && is_super_admin() || wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes') { ?>
				<td><?php echo wc_price($totals[18]); ?></td>
				<td><?php echo wc_price($totals[19]); ?></td>
				<?php } ?>
				<?php if ($type == 'begging') { ?>
				<td><?php echo wc_price($totals[8]+$totals[10]+$totals[12]+$totals[14]); ?></td>
				<td><?php echo wc_price($totals[4]); ?></td>
				<?php } ?>
				<td><?php echo wc_price($totals[16]); ?></td>
				
				<?php do_action('frozr_before_dashboard_total_sales_table_body_net'); ?>
				
				<td><?php echo wc_price($totals[17]); ?></td>
				
				<?php do_action('frozr_after_dashboard_total_sales_table_body_net'); ?>
				
			</tr>
		</tbody>
	</table>
	<?php
}
//output dash totals
function frozr_output_totals($color = '') {
	if (is_super_admin()) {
		$args = array(
			'role'			=> 'seller',
			'orderby'		=> 'registered',
			'order'			=> 'DESC',
			'fields'		=> 'ID'
		 );
		$sellers_query = new WP_User_Query( apply_filters( 'frozr_output_totals_sellers_listing_query', $args ) );
		$sellers_results = $sellers_query->get_results();
	}
?>
<div class="dash_totals sales_summary_wid <?php echo $color; ?>">
	<span class="dash_totals_title"><span class="print_summary_report"><?php _e('Print This Report','frozr'); ?></span><i class="fs-icon-money"></i>&nbsp;<?php echo __('Sales','frozr'); ?><?php if (is_super_admin()) { ?> <select id="seller_summary_select" data-rtype="today" name="seller_summary_select"><option value="all"><?php _e('From all sellers','frozr'); ?></option><?php foreach ($sellers_results as $seller_result) { ?> <option value="<?php echo $seller_result; ?>"><?php $user_store = frozr_get_store_info($seller_result); echo $user_store['store_name']; ?></option> <?php } ?></select> <?php } ?></span>
	<div class="dash_totals_opt"><span data-rtype="begging" class="show_resutl"><?php _e('All Time','frozr'); ?></span><span data-rtype="year" class="show_resutl"><?php _e('Year','frozr'); ?></span><span data-rtype="lastmonth" class="show_resutl"><?php _e('Last Month','frozr'); ?></span><span data-rtype="month" class="show_resutl"><?php _e('Month','frozr'); ?></span><span data-rtype="week" class="show_resutl"><?php _e('Week','frozr'); ?></span><span data-rtype="today" class="show_resutl active"><?php _e('Today','frozr'); ?></span><span class="show_custom"><i class="fs-icon-calendar"></i></span>
	<form class="custom_start_end" data-rtype="custom" method="post" style="display:none;">
		<label for="dast_sales_start"><?php echo __('Start Date, English Expressions or mm/dd/yyyy','frozr'); ?>
		<input class="dast_totals_start" value="<?php echo wc_clean($_POST['dast_sales_start']); ?>" name="dast_sales_start" required type="date">
		</label>
		<label for="dast_sales_end"><?php echo __('End Date, English Expressions or mm/dd/yyyy','frozr'); ?>
		<input class="dast_totals_end" value="<?php echo wc_clean($_POST['dast_sales_end']); ?>" name="dast_sales_end" required type="date">
		</label>
		<input class="rest_rating_submit" type="submit" value="<?php _e( 'Go', 'frozr' ); ?>" >
	</form>
	</div>
	<div class="dash_totals_results"><?php frozr_dashboard_totals(); ?></div>
</div>
<?php }

//dash top selling dishes
function frozr_dash_top_dishes() {
	global $post, $products;

	$meta_query = WC()->query->get_meta_query();
	$get_curnt_user = (is_super_admin()) ? '' : get_current_user_id();

	$atts = array(
		'orderby' => 'title',
		'order'   => 'asc');
		
	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => 10,
		'author'			=> $get_curnt_user,
		'meta_key'            => 'total_sales',
		'orderby'             => 'meta_value_num',
		'meta_query'          => $meta_query
	);		
	$products = new WP_Query(apply_filters('frozr_dashboard_top_dishes_products_query', $args, $atts));

?>

<div class="dash_totals f-black">
	<span class="dash_totals_title"><i class="fs-icon-star"></i>&nbsp;<?php _e('Top Selling Items','frozr'); ?></span>
	<?php if ( $products->have_posts() ) { ?>
	<table class="dash_top_selling_dishes">
		<thead>
            <tr class="table_collumns">
				<th style="text-align:left; text-indent:1em;" data-priority="1"><?php _e( 'Item', 'frozr' ); ?></th>
				<th data-priority="2"><?php _e( 'Sales', 'frozr' ); ?></th>
				<?php do_action('frozr_after_dash_top_dishes_table_header', $products); ?>
			</tr>
		</thead>
		<tbody>
		<?php while ( $products->have_posts() ) { $products->the_post(); ?>
			<tr>
				<td class="dast_dtit">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'frozr' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</td>
				<td class="dast_psales">
					<?php echo get_post_meta( $post->ID, 'total_sales', true ); ?>
				</td>
				<?php do_action('frozr_after_dash_top_dishes_table_body', $products); ?>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php } ?>
</div>

<?php }
//dash restaurant balance 
function frozr_dash_rest_balance() { ?>

<div class="dash_totals f-light-blue">
	<span class="dash_totals_title"><i class="fs-icon-money"></i>&nbsp;<?php _e('Current Balance','frozr'); ?></span>
	<div class="dash_current_balance">
		<?php echo wc_price(get_user_meta(get_current_user_id(), '_restaurant_balance', true)); ?>
		<?php if (!is_super_admin()) { ?>
		<span class="dash_with_link"><a href="<?php echo home_url( '/dashboard/withdraw/'); ?>" title="<?php _e('Withdraw','frozr'); ?>"><?php _e('Withdraw Money','frozr'); ?></a></span>
		<?php } ?>
		<?php do_action('frozr_after_restaurant_balance'); ?>
	</div>
</div>

<?php }
//dash top customers
function frozr_dash_top_customers() {
	$nx = array();
	$user = (is_super_admin()) ? '' : get_current_user_id();
	$order_ids = get_posts( apply_filters('frozr_top_customers_order_ids_args',array(
		'posts_per_page'	=> 2,
		'post_type'			=> 'shop_order',
		'orderby'			=> 'date',
		'author'			=> $user,
		'order'				=> 'desc',
		'post_status'		=> array( 'wc-completed' ),
		'fields'			=> 'ID',
	) ));
	
	foreach ($order_ids as $order_id) {
		$totalc = new WC_Order($order_id->ID);
		$usert	= get_user_by( 'id', get_post_meta($order_id->ID, '_customer_user', true) );
		$nx[$usert->display_name] += $totalc->get_total();	
	}
	
	arsort($nx);
	?>
	<div class="dash_totals f-black">
		<span class="dash_totals_title"><i class="fs-icon-users"></i>&nbsp;<?php _e('Top Customers','frozr'); ?></span>
		<table class="dash_top_selling_dishes">
		<thead>
            <tr class="table_collumns">
				<th style="text-align:left; text-indent:1em;" data-priority="1"><?php _e( 'Customer', 'frozr' ); ?></th>
				<th data-priority="2"><?php _e( 'Money Spent', 'frozr' ); ?></th>
				<?php do_action('frozr_after_dash_top_customers_table_header'); ?>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($nx as $key => $val) { ?>
		<tr>
			<td class="dast_dtit">
				<?php echo $key; ?>
			</td>
			<td class="dast_psales">
				<?php echo wc_price($val); ?>
			</td>
			<?php do_action('frozr_after_dash_top_customers_table_body', $key, $val); ?>
		</tr>
		<?php } ?>
		</tbody>
		</table>
	</div>
<?php
}
//dash orders count
function frozr_dash_orders() {
    $orders_url = home_url( '/dashboard/orders/');
?>
	<div class="dash_totals f-black">
		<span class="dash_totals_title"><i class="fs-icon-truck"></i>&nbsp;<?php _e( 'Orders', 'frozr' ); ?></span>
		<table class="dash_top_selling_dishes">
		<thead>
            <tr class="table_collumns">
				<th data-priority="3"></th>
				<th style="text-align:left; text-indent:1em;" data-priority="1"><?php _e( 'Status', 'frozr' ); ?></th>
				<th data-priority="2"><?php _e( 'Count', 'frozr' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<td class="dast_dico">
				<i class="fs-icon-check-square-o"></i>
			</td>
			<td class="dast_dtit">
				<a href="<?php echo add_query_arg( array( 'order_status' => 'completed' ), $orders_url ); ?>" ><?php _e( 'Completed', 'frozr' ); ?></a>
			</td>
			<td class="dast_pcount">
				<?php echo frozr_count_user_object('wc-completed', 'shop_order'); ?>
			</td>
		</tr>
		<tr>
			<td class="dast_dico">
				<i class="fs-icon-edit"></i>
			</td>
			<td class="dast_dtit">
				<a href="<?php echo add_query_arg( array( 'order_status' => 'pending' ), $orders_url ); ?>" ><?php _e( 'Pending', 'frozr' ); ?></a>
			</td>
			<td class="dast_pcount">
				<?php echo frozr_count_user_object('wc-pending', 'shop_order'); ?>
			</td>
		</tr>
		<tr>
			<td class="dast_dico">
				<i class="fs-icon-spinner"></i>
			</td>
			<td class="dast_dtit">
				<a href="<?php echo add_query_arg( array( 'order_status' => 'processing' ), $orders_url ); ?>" ><?php _e( 'Processing', 'frozr' ); ?></a>
			</td>
			<td class="dast_pcount">
				<?php echo frozr_count_user_object('wc-processing', 'shop_order'); ?>
			</td>
		</tr>
		<tr>
			<td class="dast_dico">
				<i class="fs-icon-remove"></i>
			</td>
			<td class="dast_dtit">
				<a href="<?php echo add_query_arg( array( 'order_status' => 'cancelled' ), $orders_url ); ?>" ><?php _e( 'Cancelled', 'frozr' ); ?></a>
			</td>
			<td class="dast_pcount">
				<?php echo frozr_count_user_object('wc-cancelled', 'shop_order'); ?>
			</td>
		</tr>
		<tr>
			<td class="dast_dico">
				<i class="fs-icon-refresh"></i>
			</td>
			<td class="dast_dtit">
				<a href="<?php echo add_query_arg( array( 'order_status' => 'refunded' ), $orders_url ); ?>" ><?php _e( 'Refunded', 'frozr' ); ?></a>
			</td>
			<td class="dast_pcount">
				<?php echo frozr_count_user_object('wc-refunded', 'shop_order'); ?>
			</td>
		</tr>
		<tr>
			<td class="dast_dico">
				<i class="fs-icon-warning"></i>
			</td>
			<td class="dast_dtit">
				<a href="<?php echo add_query_arg( array( 'order_status' => 'on-hold' ), $orders_url ); ?>" ><?php _e( 'On-hold', 'frozr' ); ?></a>
			</td>
			<td class="dast_pcount">
				<?php echo frozr_count_user_object('wc-on-hold', 'shop_order'); ?>
			</td>
		</tr>
		</tbody>
		</table>
	</div>
<?php
}