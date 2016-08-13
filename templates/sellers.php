<?php
/**
 * Dashboard - Sellers
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

frozr_redirect_login();
frozr_redirect_if_not_admin();

get_header();

?>

<?php frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array('active_menu' => 'sellers', 'title' => __('Sellers','frozr'), 'desc' => __('Sellers Registered in Your Website.','frozr')) ); ?>

	<div id="sellers_dash_page" class="content-area-sellers-page">
		<div class="dashboard-widgets">
		
			<?php do_action('frozr_before_dashboard_sellers'); ?>

			<?php frozr_sellers_page_body(); ?>

			<?php do_action('frozr_after_dashboard_sellers'); ?>
		</div>
	</div><!-- #sellers_dash_page .content-area-sellers-page -->
</div><!-- #dokkan-wrapper -->

<?php

// action hook for placing content below #container
frozr_belowcontainer();

//calling sidebar
frozr_sidebar();

// calling footer.php
get_footer();