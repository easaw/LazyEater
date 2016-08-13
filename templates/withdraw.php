<?php
/**
 * Dashboard - Withdraw
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

frozr_redirect_login();
frozr_redirect_if_not_seller();
frozr_wid_redirect_if_admin();

get_header();

wp_enqueue_media();
wp_enqueue_script( 'serializejson' );
wp_enqueue_script( 'withdraw' );
?>

<?php frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array('active_menu' => 'withdraw', 'title' => __('Withdraw','frozr'), 'desc' => __('Manage your withdraw requests.','frozr')) ); ?>

	<div id="withdraw" class="content-area-withdraw-page">
		<div class="dashboard-widgets">
			<?php do_action('frozr_before_dashboard_withdraw'); ?>
			
			<?php frozr_withdraws_page_body(); ?>
			
			<?php do_action('frozr_after_dashboard_withdraw'); ?>
		</div>
	</div><!-- #withdraw .content-area-withdraw-page -->
</div><!-- #dokkan-wrapper -->

<?php

	// action hook for placing content below #container
	frozr_belowcontainer();

	//calling sidebar
	frozr_sidebar();

	// calling footer.php
	get_footer();