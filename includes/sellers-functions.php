<?php
/**
 * Sellers page in the front-end for the admin
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//sellers nav
function sellers_page_nav() {
    $permalink = home_url( '/dashboard/sellers/');
    $status_class = isset( $_GET['sellers'] ) ? sanitize_key($_GET['sellers']) : 'all';
	$nav_array = array('all', 'yes', 'no');
	$nav_array_out = array();
	foreach ($nav_array as $nx) {
		if ($nx == 'all') {
		$args = array(
			'role'			=> 'seller',
			'orderby'		=> 'registered',
			'order'			=> 'DESC',
			'cunt_total'	=> true,
			'fields'		=> 'ID'
		 );
		} else {
		$args = array(
			'role'			=> 'seller',
			'meta_key'		=> 'frozr_enable_selling',
			'meta_value'	=> $nx,
			'orderby'		=> 'registered',
			'order'			=> 'DESC',
			'cunt_total'	=> true,
			'fields'		=> 'ID'
		 );
		 }
	$user_query = new WP_User_Query( apply_filters('frozr_sellers_page_nav_args',$args) );
	$nav_array_out[] = $user_query->get_total();
	}
	if (frozr_mobile()) { $active_icon='fs-icon-caret-right'; } else {  $active_icon='fs-icon-caret-up'; }
    ?>
	<div class="sellers-listing-header">
	
	<?php frozr_restaurant_invitation(); ?>

	<ul class="sellers-filter">
	
		<?php do_action('frozr_before_sellers_filter_list'); ?>
		
        <li <?php echo $status_class == 'all' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo $permalink; ?>"><?php printf( __( 'All (%d)', 'frozr' ), $nav_array_out[0] ); ?></a>
        </li>
        <li <?php echo $status_class == 'yes' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'sellers' => 'yes' ), $permalink ); ?>"><?php printf( __( 'Active Sellers (%d)', 'frozr' ), $nav_array_out[1] ); ?></a>
        </li>
        <li <?php echo $status_class == 'no' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'sellers' => 'no' ), $permalink ); ?>"><?php printf( __( 'Inactive Sellers (%d)', 'frozr' ), $nav_array_out[2] ); ?></a>
        </li>
        <li <?php echo $status_class == 'top-sellers' ? "class=\"active $active_icon\"" : ''; ?> >
            <a href="<?php echo add_query_arg( array( 'sellers' => 'top-sellers' ), $permalink ); ?>"><?php _e( 'Top Sellers', 'frozr' ); ?></a>
        </li>
		<li class="send_rest_invit_link"><a href="#rest_invit_form_wid" data-transition="fade" data-rel="popup" data-position-to="window"><i class="fs-icon-envelope"></i>&nbsp;<?php _e('Send Invitation!','frozr'); ?></a></li>
		
		<?php do_action('frozr_after_sellers_filter_list'); ?>
		
    </ul> <!-- .seller-filter -->
	
	<?php do_action('frozr_after_sellers_page_nav'); ?>
	
	</div>
    <?php
}
// Restaurant invitation widget 
function frozr_restaurant_invitation() {
?>
	<div id="rest_invit_form_wid" class="dash_totals f-orange" data-history="false" data-role="popup">
		<span class="dash_totals_title"><i class="fs-icon-envelope"></i>&nbsp;<?php echo __('Send Invitation','frozr'); ?></span>
		<span class="dash_totals_title_desc"><?php _e('Send an invitation to a vendor to join your restaurant network.','frozr'); ?></span>
		<form id="rest_invit_form" method="post">
		
			<?php do_action('frozr_before_restaurant_invitation_form'); ?>
			
			<label for="rest_invit_email">
			<input class="rest_invit_email" value="<?php echo sanitize_email($_POST['rest_invit_email']); ?>" placeholder="<?php _e('Email of recipient','frozr'); ?>" name="rest_invit_email" required type="email">
			</label>
			<label for="rest_invit_subject">
			<input class="rest_invit_subject" value="<?php echo sanitize_text_field($_POST['rest_invit_subject']); ?>" placeholder="<?php _e('Invitation Subject','frozr'); ?>" name="rest_invit_subject" required type="text">
			</label>
			<label for="rest_invit_text">
			<textarea class="rest_invit_text" name="rest_invit_text" required placeholder="<?php _e('Invitation Message','frozr'); ?>"><?php echo wc_clean($_POST['rest_invit_text']); ?></textarea>
			</label>
			
			<?php do_action('frozr_after_restaurant_invitation_form'); ?>
			
			<input class="rest_invit_wid_btn" type="submit" value="<?php _e( 'Send', 'frozr' ); ?>" >
		</form>
	</div>
<?php
}

//sellers lists body
function frozr_sellers_page_body() {
	
	global $post;
	
	sellers_page_nav(); ?>
	
	<div id="seller_mgs" class="common_pop" data-history="false" data-role="popup"><?php frozr_restaurant_email_form(0, true); ?></div>
	<table data-role="table" id="sellers-table" data-mode="reflow" class="ui-responsive">
		<thead>
			<tr>

				<?php do_action('frozr_before_sellers_table_header'); ?>

				<th data-priority="1"><?php _e('Name','frozr'); ?></th>
				<th data-priority="2"><?php _e('Email','frozr'); ?></th>
				<th data-priority="3"><?php _e('Active Seller','frozr'); ?></th>
				<th data-priority="5"><?php _e('Balance','frozr'); ?></th>
				<th data-priority="6"><?php _e('Phone & Address','frozr'); ?></th>
				<th data-priority="7"><?php _e('Products','frozr'); ?></th>
				<th data-priority="8"><?php _e('Orders','frozr'); ?></th>
				<th data-priority="9"><?php _e('Coupons','frozr'); ?></th>

				<?php do_action('frozr_after_sellers_table_header'); ?>

			</tr>
		</thead>
		<tbody>
		<?php
		$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
		if (!isset($_GET['sellers']) || $_GET['sellers'] == 'top-sellers') {
		$args = array(
			'role'			=> 'seller',
			'orderby'		=> 'registered',
			'order'			=> 'DESC',
			'fields'		=> 'ID'
		 );
		} elseif ($_GET['sellers'] != 'top-sellers') {
		$args = array(
			'role'			=> 'seller',
			'meta_key'		=> 'frozr_enable_selling',
			'meta_value'	=> sanitize_key($_GET['sellers']),
			'orderby'		=> 'registered',
			'order'			=> 'DESC',
			'fields'		=> 'ID',
			'paged'			=> $paged
		 );
		}
		$sellers_query = new WP_User_Query( apply_filters( 'frozr_sellers_listing_query', $args ) );
		
		$sellers_results = $sellers_query->get_results();
		if (!empty($sellers_results)) {
			if ($_GET['sellers'] == 'top-sellers') {
				$topsellers = array(); 
				foreach ($sellers_results as $seller_result) {
					$com_ords = frozr_count_user_object('wc-completed','shop_order',$seller_result );
					if ($com_ords != 0) {
						$topsellers[$seller_result] = $com_ords;
					}
				}
				arsort($topsellers); ?>
					<div class="top_sellers_notice style_box fs-icon-trophy">
						<p><?php _e('Sellers ordered by highest completed orders.','frozr'); ?></p>
					</div>
				<?php
				foreach ($topsellers as $topseller => $sellerv) {
					frozr_top_sellers_list_body($topseller);
					}
			} else {
			foreach ($sellers_results as $seller_result) {
					frozr_top_sellers_list_body($seller_result);
				}
			}
        } else { ?>
			<tr>
            <div class="style_box alert alert-warning fs-icon-warning-sign">
                <p><?php _e( 'Sorry, no sellers found!', 'frozr' ); ?></p>
            </div>
			</tr>
            <?php
        } ?>
		</tbody>
	</table>
	<?php
}
// Top Sellers list body
function frozr_top_sellers_list_body($xxn) {
	
	$user_store = frozr_get_store_info($xxn);
	$user_is_seller = ('' != (get_user_meta($xxn, 'frozr_enable_selling', true))) ? get_user_meta($xxn, 'frozr_enable_selling', true) : 'no';
	$user_balance = ('' != (get_user_meta($xxn, '_restaurant_balance', true))) ? get_user_meta($xxn, '_restaurant_balance', true) : 0;
	$seller_store = (!empty ($user_store['store_name'])) ? ' (' . $user_store['store_name'] . ')' : '';
	$user_product_counts = frozr_count_posts( 'product', $xxn );
	$user_info = get_userdata($xxn);
	$user_pro_orders = frozr_count_user_object('wc-processing','shop_order',$xxn );
	$user_com_orders = frozr_count_user_object('wc-completed','shop_order',$xxn );
	$user_coupons = frozr_count_user_object('publish','shop_coupon',$xxn );
	?>
	<tr>

		<?php do_action('frozr_before_seller_table_loop', $xxn); ?>
		
		<td><div id="seller_edit_pop_<?php echo $xxn; ?>" class="common_pop" data-history="false" data-role="popup"><?php frozr_seller_edit_form($xxn); ?></div> <a href="#seller_edit_pop_<?php echo $xxn; ?>" data-rel="popup" data-position-to="window"><strong><?php echo $user_info->user_login . $seller_store; ?></strong></a></td>
		<td><a href="#seller_mgs" class="send_seller_msg_pop" data-transition="fade" data-rel="popup" data-position-to="window" data-userid="<?php echo $xxn; ?>"><strong><?php echo $user_info->user_email; ?></strong></a></td>
		<td><?php echo $user_is_seller; ?></td>
		<td><?php echo $user_balance; ?></td>
		<td><?php echo $user_store['phone']; ?></td>
		<td><?php echo $user_product_counts->publish; ?></td>
		<td><?php echo __('Processing:','frozr') . ' ' . $user_pro_orders . '</br>' . __('Completed:','frozr') . ' ' . $user_com_orders; ?></td>
		<td><?php echo $user_coupons; ?></td>

		<?php do_action('frozr_after_seller_table_loop', $xxn); ?>

	</tr>
	<?php
}
// Sellers edit form
function frozr_seller_edit_form($nx) { ?>

	<form id="seller_<?php echo $nx; ?>_edit" action="" method="post" class="seller_edit_form clearfix">
		<div class="ajax-response"></div>
		<div class="form-group">
			<span class="control-label"><?php echo __( 'Activate Selling', 'frozr' ); ?>&nbsp;<a class="tips" title="<?php _e( 'Activate/Deactivated selling privileges for this seller.', 'frozr' ) ?>" href="#">[?]</a></span>
			<div>
				<label><?php _e( 'Activated.', 'frozr' ); ?>
					<input type="radio" name="seller_edit_selling" value="yes" <?php checked( frozr_is_seller_enabled($nx), true ); ?>>
				</label>
				<label><?php _e( 'Deactivated.', 'frozr' ); ?>
					<input type="radio" name="seller_edit_selling" value="no" <?php checked( frozr_is_seller_enabled($nx), false ); ?>>
				</label>
			</div>
		</div>

		<?php do_action('frozr_after_seller_edit_form', $nx); ?>

		<input type="hidden" class="seller_edit_id" name="seller_edit_id" value="<?php echo $nx; ?>">
		<input type="submit" name="seller_edit_form_submit" value="<?php esc_attr_e( 'Save Settings', 'frozr' ); ?>" class="pull-right btn btn-theme">
	</form>

<?php }