<?php
/**
 * The Template for displaying store header.
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$user = get_query_var( 'restaurant' );
$store_user = get_user_by( 'slug', strtok($user, " ") );
$store_info = frozr_get_store_info( $store_user->ID );
$rest_tbls = get_user_meta($store_user->ID, '_rest_tables', true);
$ord_typ = ($store_info['deliveryby'] == 'order') ? __('order','frozr') : __('product','frozr');
get_header();

do_action('before_rest_page_header');

if (isset( $_GET['make_review'] )) {
	$customer = absint( get_post_meta( intval($_GET['make_review']), '_customer_user', true ) );
	$rest_array = array();
	$rest_rate_orders = array();
	$rest_array = get_user_meta( intval($store_user->ID), 'rest_rating', true );
	if (is_array($rest_array)) {
		foreach($rest_array as $n => $v) {
			$rest_rate_orders[] = $n;
		}
	}
	if ($customer == get_current_user_id() && !in_array(intval($_GET['make_review']),$rest_rate_orders)) {
		frozr_store_rating_form($store_user->ID, intval($_GET['make_review']));
	}
}
if (!empty ($rest_tbls) && $store_info['show_rest_tables'] == 'yes') { ?>
<div id="rest_tables" data-history="false" data-role="popup">
	<div class="table_search_input">
		<i class="fs-icon-users"></i>
		<label class="table_info_span" for="table_seats_list"><?php _e('Find a Table','frozr'); ?></label>
		<input name="table_seats_list" id="table_seats_list" value="" data-rest="<?php echo $store_user->ID; ?>" class="form-control" placeholder="<?php _e('Number of Seats?','frozr'); ?>" type="number">
		<a id="frozr_tables_btn" class="button table_seats_btn" href="#" title="<?php _e('Search Tables','frozr'); ?>"><?php _e('Go','frozr'); ?></a>
	</div>
	<div class="rest_table_info_wrapper"></div>
</div>
<?php } ?>
<div id="rest_contact" class="common_pop" data-history="false" data-role="popup">
	<h3 class="widget-title"><?php 	$restime = _e( 'Contact Restaurant', 'frozr' ); ?></h3>
	<ul>
		<?php if (frozr_get_restaurant_address($store_user->ID)) { ?>
		<li><i class="fs-icon-map-marker"></i> <?php echo frozr_get_restaurant_address($store_user->ID); ?></li>
		<?php } ?>
		<?php if ( isset( $store_info['phone'] ) && !empty( $store_info['phone'] ) ) { ?>
		<li><i class="fs-icon-phone"></i>
		<a href="tel:<?php echo esc_html( $store_info['phone'] ); ?>"><?php echo esc_html( $store_info['phone'] ); ?></a>
		</li>
		<?php } ?>
		<?php if ( isset( $store_info['show_email'] ) && $store_info['show_email'] == 'yes' ) { ?>
		<li><i class="fs-icon-envelope-o"></i>
		<a href="mailto:<?php echo antispambot( $store_user->user_email ); ?>"><?php echo antispambot( $store_user->user_email ); ?></a>
		</li>
		<?php } ?>
	</ul>
	<?php if ( isset( $store_info['allow_email'] ) && $store_info['allow_email'] == 'yes' ) {
	frozr_restaurant_email_form($store_user->ID);
	} ?>
</div>
<div class="profile-frame" style=" background-image: url('<?php if ( isset( $store_info['banner'] ) && !empty( $store_info['banner'] ) ) { echo wp_get_attachment_url( $store_info['banner'] ); } else { echo plugins_url( 'assets/imgs/rest-bg.png', lAZY_EATER_FILE ); } ?>');background-size: cover;background-repeat: no-repeat;" >
	<div class="col-md-4 profile-info-box <?php echo (frozr_is_rest_open($store_user->ID) == false) ? 'Closed' : 'Open'; ?>">
		<div class="profile-info">
			<div class="rest-title">
				<?php echo get_avatar( $store_user->ID, 150 ); ?>
				<?php if ( isset( $store_info['store_name'] ) ) { ?>
				<h1 class="store-name"><?php echo esc_html( $store_info['store_name'] ); ?></h1>
				<?php }
				$restypes = apply_filters('frozr_restaurant_store_cusine_terms',wp_get_object_terms( $store_user->ID, 'cuisine', array("fields" => "names") ));
				if (is_array($restypes)) { ?>
				<ul class="list-inline rest-cusines">
					<li>
						<i class="fs-icon-cutlery"></i>&nbsp;<?php
						foreach ( $restypes as $restype ) {
							$link = get_term_link( $restype, 'cuisine' ); ?>
							<a href="<?php echo esc_url( $link ); ?>" title="<?php echo __('See Restaurants that also serve ','frozr') . $restype; ?>"><?php echo $restype; ?></a>
						<?php } ?>
					</li>
					<?php do_action('frozr_after_restaurant_store_cusines'); ?>
				</ul>
				<?php } ?>
				<ul class="rest-social">

					<?php do_action('frozr_before_restaurant_store_socials'); ?>

					<?php if ( isset( $store_info['socialfb'] ) && !empty( $store_info['socialfb'] ) ) { ?>
					<li>
						<a href="<?php echo esc_url( $store_info['socialfb'] ); ?>" target="_blank"><i class="fs-icon-facebook-square"></i></a>
					</li>
					<?php } ?>

					<?php if ( isset( $store_info['socialgplus'] ) && !empty( $store_info['socialgplus'] ) ) { ?>
					<li>
						<a href="<?php echo esc_url( $store_info['socialgplus'] ); ?>" target="_blank"><i class="fs-icon-google-plus-square"></i></a>
					</li>
					<?php } ?>

					<?php if ( isset( $store_info['socialtwitter'] ) && !empty( $store_info['socialtwitter'] ) ) { ?>
					<li>
						<a href="<?php echo esc_url( $store_info['socialtwitter'] ); ?>" target="_blank"><i class="fs-icon-twitter-square"></i></a>
					</li>
					<?php } ?>

					<?php if ( isset( $store_info['sociallinkedin'] ) && !empty( $store_info['sociallinkedin'] ) ) { ?>
					<li>
						<a href="<?php echo esc_url( $store_info['sociallinkedin'] ); ?>" target="_blank"><i class="fs-icon-linkedin-square"></i></a>
					</li>
					<?php } ?>

					<?php if ( isset( $store_info['socialyoutube'] ) && !empty( $store_info['socialyoutube'] ) ) { ?>
					<li>
						<a href="<?php echo esc_url( $store_info['socialyoutube'] ); ?>" target="_blank"><i class="fs-icon-youtube-square"></i></a>
					</li>
					<?php } ?>

					<?php do_action('frozr_after_restaurant_store_socials'); ?>

				</ul>
				<div class="restu_cpos">
					<?php do_action('frozr_before_restaurant_store_coupons'); ?>

					<?php frozr_show_shop_coupons($store_user->ID); ?>

					<?php do_action('frozr_after_restaurant_store_coupons'); ?>
				</div>
				<div class="rest_delivery_date <?php echo (frozr_is_rest_open($store_user->ID) == false) ? 'Closed' : 'Open'; ?>">

					<?php echo frozr_rest_status($store_user->ID); ?>

				</div>
			</div>
			<ul class="list-inline rest-info">

				<?php do_action('frozr_before_restaurant_store_info'); ?>

				<li><a href="#rest_contact" data-transition="fade" data-rel="popup" data-position-to="window"><i class="fs-icon-book"></i>&nbsp;<?php _e('Restaurant Address','frozr'); ?></a></li>
				<?php if (!empty ($rest_tbls) && $store_info['show_rest_tables'] == 'yes') { ?>
				<li><a href="#rest_tables" data-transition="fade" data-rel="popup" data-position-to="window"><i class="fs-icon-circle"></i>&nbsp;<?php _e('View Tables','frozr'); ?></a></li>
				<?php } ?>
				<li>
					<i class="fs-icon-thumbs-up"></i>
					<?php echo frozr_get_readable_seller_rating( $store_user->ID ); ?>
				</li>
				<?php if ( isset( $store_info['processing_time'] ) && !empty( $store_info['processing_time'] ) ) { ?>
				<li>
					<i class="fs-icon-clock-o"></i>&nbsp;<?php echo __('Delivers in ','frozr') . $store_info['processing_time'] . "min"; ?>
				</li>
				<?php } ?>
				<li>
					<a href="#" title="<?php _e('Delivery Fee.','frozr'); ?>"><i class="fs-icon-money"></i>&nbsp;<?php if($store_info['shipping_fee'] == 0) { _e('Free Delivery.','frozr'); } elseif(!empty($store_info['shipping_fee'])) { echo wc_price( $store_info['shipping_fee']) . ' ' . __('Per','frozr') . ' ' . $ord_typ; } else {echo "N/A";}; ?></a>
				</li>
				<li>
					<a href="#" title="<?php _e('Minimun Order Amount for Delivery.','frozr'); ?>"><i class="fs-icon-shopping-basket"></i>&nbsp;<?php if($store_info['min_order_amt'] == 0) { _e('Any.','frozr'); } elseif(!empty($store_info['min_order_amt'])) { echo $store_info['min_order_amt']; } else {echo "N/A";}; ?></a>
				</li>

				<?php do_action('frozr_after_restaurant_store_info'); ?>

			</ul>
		</div> <!-- .profile-info -->
	</div> <!-- .profile-info-box -->
</div> <!-- .profile-frame -->
<?php do_action('frozr_after_rest_page_header'); ?>