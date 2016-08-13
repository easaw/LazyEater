<?php
/**
 * Dashboard - Settings
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

frozr_redirect_login();
frozr_redirect_if_not_seller();

get_header();

frozr_frontend_dashboard_scripts();

?>

<?php frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array( 'active_menu' => 'settings', 'title' => __('Restaurant Settings','frozr'), 'desc' => __('Manage your Account.','frozr') ) ); ?>

	<div id="seller-settings" class="content-area-user-settings">
		<div class="dashboard-widgets">

			<?php do_action('frozr_before_dashboard_settings'); ?>

			<?php frozr_output_restaurant_settings(); ?>

			<?php do_action('frozr_after_dashboard_settings'); ?>

		</div>
	</div><!-- #seller-settings .content-area-user-settings -->
</div><!-- #dokkan-wrapper -->

<?php

	// action hook for placing content below #container
	frozr_belowcontainer();
	
	//calling sidebar
	frozr_sidebar();
	
	// calling footer.php
	get_footer();