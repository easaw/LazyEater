<?php
/**
 * Dashboard home
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ($_GET['print'] == 'summary') {

frozr_sales_summary_report(sanitize_key($_GET['rtype']), $_GET['startd'], $_GET['endd'], intval($_GET['auser']));

} else {
frozr_redirect_login();
frozr_redirect_if_not_seller();

get_header();

frozr_frontend_dashboard_scripts();

?>

<?php frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array( 'active_menu' => 'dashboard', 'title' => __('Dashboard Home','frozr'), 'desc' => __('Quick Reports and statistics.','frozr') ) ); ?>

	<div id="dashboard" class="content-area-dashboard">
		<?php
		
		frozr_output_totals('f-green'); ?>
		
		<div class="js-masonry" data-masonry-options='{ "isAnimated": true, "isFitWidth": true, "itemSelector": ".content-area-dashboard .js-masonry > *", "isOriginLeft": <?php echo frozr_theme_layout(); ?> }' >
		<?php
		do_action('frozr_before_user_dashboard');

		if (!is_super_admin()) {
			frozr_dash_rest_balance();
		}
		frozr_dash_orders();
		frozr_dash_top_dishes();
		frozr_dash_top_customers();
		
		do_action('frozr_after_user_dashboard');
		?>
		</div>
	</div><!-- #dashboard .content-area-dashboard -->
</div><!-- #dokkan-wrapper -->

<?php

	// action hook for placing content below #container
	frozr_belowcontainer();
	
	//calling sidebar
	frozr_sidebar();
	
	// calling footer.php
	get_footer();
}