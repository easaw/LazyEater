<?php
/**
* New Product
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

<?php frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array( 'active_menu' => 'dishes', 'title' => __('New Dish','frozr'), 'desc' => __('Add a new dish to your menu.','frozr') ) ); ?>

		<div id="product-edit" class="content-area-product-edit">

			<div class="dashboard-widgets">

			<?php do_action('frozr_before_new_product'); ?>

			<?php frozr_edit_add_dish_body(true); ?>

			<?php do_action('frozr_after_new_product'); ?>

			</div> <!-- .row -->
		</div><!-- #product-edit .content-area-product-edit -->
	</div><!-- .dash-content -->
</div><!-- #dokkan-wrapper -->

<?php

	// action hook for placing content below #container
	frozr_belowcontainer();

	//calling sidebar
	frozr_sidebar();

	// calling footer.php
	get_footer();