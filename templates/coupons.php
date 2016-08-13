<?php
/**
 * Dashboard - Coupon
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

frozr_redirect_login();
frozr_redirect_if_not_seller();

get_header();

wp_enqueue_script( 'serializejson' );
wp_enqueue_script( 'coupons' );

frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array( 'active_menu' => 'coupon', 'title' => __('Coupons','frozr'), 'desc' => __('Add/Manage Discount Coupons.','frozr') ) ); ?>

	<div id="coupons" class="content-area-coupons-list">
		<div class="dashboard-widgets">
			
			<?php do_action('frozr_before_dashboard_coupons');
			
			frozr_coupons_header_nav();

			if ( isset( $_GET['post'] ) && $_GET['action'] == 'edit' || isset( $_GET['view'] ) && $_GET['view'] == 'add_coupons') {
				frozr_add_coupons_form();
			} else {
				frozr_list_user_coupons();
			}
			
			do_action('frozr_after_dashboard_coupons'); ?>

		</div>
	</div><!-- #coupons .content-area-coupons-list -->
</div><!-- #dokkan-wrapper -->

<?php

	// action hook for placing content below #container
	frozr_belowcontainer();
	
	//calling sidebar
	frozr_sidebar();
	
	// calling footer.php
	get_footer();