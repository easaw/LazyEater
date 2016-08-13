<?php
/**
* Functions for the print page templates
*
* @package FrozrCoreLibrary
* @subpackage Frozrmarketlibrary
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function frozr_summary_template($type = 'today', $start = '', $end = '', $user = '') {

	$site_logo_img = get_theme_mod('site_logo');

	if (is_super_admin()) {
		if (esc_attr($type) != 'custom') {
			$report_values = frozr_dash_total_sales($type, '', '', $user);
		} else {
			$report_values = frozr_dash_total_sales($type, $start, $end, $user);
		}
		if ($type == 'begging') {
			$actual_balance = $report_values[0] - $report_values[4] - frozr_print_total_withdraws($user);
		}
	} else {
		if (esc_attr($type) != 'custom') {
			$report_values = frozr_dash_total_sales($type);
		} else {
			$report_values = frozr_dash_total_sales($type, $start, $end);
		}
		if ($type == 'begging') {
			$actual_balance = $report_values[17] - frozr_print_total_withdraws();
		}		
	}
	$seller = is_super_admin() ? $user : get_current_user_id();
	if ($seller) {
		$store_info = frozr_get_store_info( $seller );
		$get_user = get_userdata( $seller );
		$registered = $get_user->user_registered;
	}
	$total_note = is_super_admin() ? __('Net profit from completed orders after all sellers commissions are paid.','frozr') : __('Net of any withdraws made during this period.','frozr');
	//Readable summary period
	switch ($type) {
        case 'begging':
            $period =  __( 'All Time:', 'frozr' ) . ' ' . date( "d, M, Y", strtotime($registered) ) . ' - ' . date('d, M, Y', strtotime("today"));
            break;
        case 'today':
            $period =  __( 'Today:', 'frozr' ) . ' ' . date('d, M, Y', strtotime("today"));
            break;
        case 'week':
            $period =  __( 'This Week:', 'frozr' ) . ' ' . date('d, M, Y', strtotime("-6 days")) . ' - ' . date('d, M, Y', strtotime("today"));
            break;
        case 'month':
            $period =  __( 'This Month:', 'frozr' ) . ' ' . date('M, Y', strtotime("this month"));
            break;
        case 'lastmonth':
            $period =  date('d, M, Y', strtotime("first day of last month")) . ' - ' . date('d, M, Y', strtotime("last day of last month"));
            break;
        case 'year':
            $period =  __('This Year:','frozr') . ' ' . date('Y', strtotime("this year"));
            break;
        case 'custom':
            $period =  date('d, M, Y', strtotime($start)) . ' - ' . date('d, M, Y', strtotime($end));
            break;
	}
	?>
	<div class="frozr_dash_report">

		<table class="summary_report_header">
			<tbody>
			<tr class="summary_logo">
				<td>
					<a href="<?php echo home_url(); ?>/" title="<?php bloginfo( 'name' ); ?>" rel="home"><img width="auto" height="auto" alt="<?php bloginfo( 'name' ); ?>" style="max-width:79%;" src="<?php echo $site_logo_img;?>"/></a>
				</td>
				<td><?php echo apply_filters('frozr_general_summary_report_title',__('General Summary Report','frozr')) . ' - ' . date('d, M, Y', strtotime("today")); ?>&nbsp;-&nbsp;<span><?php _e('Beta','frozr'); ?></span></td>
			</tr>
			<tr class="summary_report_details">
				<td>
				<?php if ($seller) { ?>

					<span><?php echo __('Restaurant:','frozr') . ' ' .$store_info['store_name']; ?></span><br/>
					<span><?php echo __('Address:','frozr'); ?></span>
					<address>
					<?php
						$address = apply_filters( 'frozr_summary_report_address', array(
							'first_name'  => get_user_meta( $seller, 'billing' . '_first_name', true ),
							'last_name'   => get_user_meta( $seller, 'billing' . '_last_name', true ),
							'company'     => get_user_meta( $seller, 'billing' . '_company', true ),
							'address_1'   => get_user_meta( $seller, 'billing' . '_address_1', true ),
							'address_2'   => get_user_meta( $seller, 'billing' . '_address_2', true ),
							'city'        => get_user_meta( $seller, 'billing' . '_city', true ),
							'state'       => get_user_meta( $seller, 'billing' . '_state', true ),
							'postcode'    => get_user_meta( $seller, 'billing' . '_postcode', true ),
							'country'     => get_user_meta( $seller, 'billing' . '_country', true )
						), $seller);

						$formatted_address = WC()->countries->get_formatted_address( $address );

						if ( ! $formatted_address )
							if (is_super_admin()) {
							_e( 'This seller has not set up this type of address yet.', 'frozr' );
							} else {
							_e( 'You have not set up this type of address yet.', 'frozr' );
							}
						else
							echo $formatted_address;
					?>
					</address>
				<?php } elseif (is_super_admin()) { ?>
					<span><?php echo __('Report based on all sellers actvity.','frozr'); ?></span>
				<?php } ?>
				</td>
				<td><?php echo __('Period:','frozr') . ' ' . $period; ?></td>
			</tr>
			</tbody>
		</table>
		<div class="summary_report_body">
			<div class="summary_report_container">
				<table>
					<thead>
						<tr>
							<th><?php echo apply_filters('frozr_income_summary_to_earnings_account',__('Income Summary to Earnings Account','frozr')); ?></th>
							<th><?php _e('Amount','frozr'); ?></th>
						</tr>
					</thead>

					<tbody>
						<tr>
							<td><?php echo __('Total Orders:','frozr') . $report_values[1]; ?></td>
							<td><?php echo wc_price($report_values[0]+$report_values[2]+$report_values[18]+$report_values[19]); ?></td>
						</tr>
						<?php if ($type == 'begging') { ?>
						<tr>
							<td><?php echo __('Total Pending Orders:','frozr') . $report_values[9]; ?></td>
							<td>-&nbsp;<?php echo wc_price($report_values[8]); ?></td>
						</tr>
						<tr>
							<td><?php echo __('Total Processing Orders:','frozr') . $report_values[11]; ?></td>
							<td>-&nbsp;<?php echo wc_price($report_values[10]); ?></td>
						</tr>
						<tr>
							<td><?php echo __('Total On-hold Orders:','frozr') . $report_values[15]; ?></td>
							<td>-&nbsp;<?php echo wc_price($report_values[14]); ?></td>
						</tr>
						<tr>
							<td><?php echo __('Total Cancelled Orders:','frozr') . $report_values[13]; ?></td>
							<td>-&nbsp;<?php echo wc_price($report_values[12]); ?></td>
						</tr>
						<tr>
							<td><?php echo __('Total refunded Orders:','frozr') . $report_values[5]; ?></td>
							<td>-&nbsp;<?php echo wc_price($report_values[4]); ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td><?php echo __('Total Coupon Usage:','frozr'); ?></td>
							<td>-&nbsp;<?php echo wc_price($report_values[2]); ?></td>
						</tr>
						<?php if (wc_tax_enabled() && is_super_admin() || wc_tax_enabled() && get_option('woocommerce_prices_include_tax') == 'yes') { ?>
						<?php foreach ( $report_values[20] as $code => $tax ) { ?>
						<tr>
							<td><?php echo $code; ?>:</td>
							<td>-&nbsp;<?php echo $tax; ?></td>
						</tr>
						<?php } ?>
						<?php } ?>
						<tr>
							<td><?php if (!is_super_admin()) echo __('Total','frozr') . ' ' . get_bloginfo( 'name' ) . ' ' .__('Fees','frozr'); else echo __('Seller Fees','frozr'); ?></td>
							<td>-&nbsp;<?php echo wc_price($report_values[16]); ?></td>
						</tr>
						<?php do_action('frozr_after_summary_report_body',$type, $start, $end, $user); ?>
					</tbody>
				</table>

				<div class="summary_total">
					<span class="summary_total_amount"><?php echo __('Total:','frozr') . ' ' . wc_price($report_values[17]); ?></span>
					<span class="summary_total_notice"><?php echo apply_filters('frozr_summary_report_total_notice',$total_note); ?></span>
					<?php if ($type == 'begging') { ?>
					<span class="summary_total_notice"><strong><?php echo __('Total Withdraws:','frozr') . ' -' . wc_price(frozr_print_total_withdraws($user)); ?></strong></span>
					<span class="summary_total_notice"><?php echo __('Actual Balance in Account:','frozr') . ' <strong>' . wc_price($actual_balance) . '</strong> '; if (wc_tax_enabled() && is_super_admin()) { echo __('- Exclusive of Tax','frozr'); } ?></span>
					<?php } ?>
					<?php do_action('frozr_after_summary_report_total',$type, $start, $end, $user); ?>
				</div>
			</div>
		</div>

		<div class="summary_footnotes">

			<div class="summary_notice">
			<?php echo apply_filters('frozr_sales_summary_report_one',__('Profits are only gained from completed orders.','frozr')); ?>
			</div>

		</div>
	</div>
<?php
}
//Set head title for the summary print template
function frozr_summary_report_page_title($title) {
	$title .= '<title>'. get_bloginfo( 'name' ) . ' - ' . __('Summary Sales Report.','frozr') .'</title>';
	return $title;
}

// summary report template
function frozr_sales_summary_report($type, $start, $end, $user) {

	frozr_print_template_header('frozr_summary_report_page_title');

	if (is_super_admin()) {
		if (esc_attr($type) != 'custom') {
			frozr_summary_template($type, '', '', $user);
		} else {
			frozr_summary_template($type, $start, $end, $user);
		}
	} elseif (esc_attr($type) != 'custom') {
		frozr_summary_template($type);
	} else {
		frozr_summary_template($type, $start, $end);
	}

	frozr_print_template_footer();

}

// print pages header
function frozr_print_template_header($page_title) {

//add cache control - its off to use add "true" to the below function
frozr_cache_support();

frozr_create_doctype();
echo " ";
language_attributes();
echo ">\n";

add_filter('frozr_head_profile',$page_title);

// Opens the head tag
frozr_head_profile();

// Create the meta content type
frozr_create_contenttype();

// mobile support
frozr_viewport();

// Create the tag <meta name="robots"
frozr_show_robots();

//Show Theme Favicon
frozr_show_favicon();

?>
<link rel="stylesheet" href="<?php echo plugins_url( 'assets/css/print.css', lAZY_EATER_FILE ); ?>" type="text/css" media="all">
</head>
<?php
	frozr_body();
}

// print pages footer
function frozr_print_template_footer() {

?>
</body>
</html>
<?php
}

// print order template
function frozr_print_order_template($order) {
	frozr_print_template_header('frozr_order_page_title');
	frozr_print_order($order);
	frozr_print_template_footer();
}
// print order
function frozr_print_order($order) {

	$site_logo_img = get_theme_mod('site_logo');

?>
<div class="frozr_dash_print_order">
	<div class="order_print_header">
	<a href="<?php echo home_url(); ?>/" title="<?php bloginfo( 'name' ); ?>" class="order_print_logo" rel="home"><img width="auto" height="auto" alt="<?php bloginfo( 'name' ); ?>" style="max-width:79%;" src="<?php echo $site_logo_img;?>"/></a>
	<?php echo '<a data-ajax="false" href="' . wp_nonce_url( add_query_arg( array( 'order_id' => $order->id ), home_url( '/dashboard/orders/') ), 'frozr_view_order' ) . '"><strong>' . sprintf( __( 'Order %s', 'frozr' ), esc_attr( $order->get_order_number() ) ) . '</strong></a>'; ?>
	</div>
	<?php frozr_order_items_table($order); ?>
	<?php frozr_order_general_details($order); ?>
	<?php frozr_order_customer_details($order); ?>
</div>
<?php
}

//Set head title for the summary print template
function frozr_order_page_title($title) {
	$title .= '<title>'. get_bloginfo( 'name' ) . ' - ' . __('Print Order.','frozr') .'</title>';
	return $title;
}