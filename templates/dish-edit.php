<?php
/**
 * Products - Edit
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
<?php frozr_get_template( FROZR_WOO_TMP . '/dashboard-nav.php', array( 'active_menu' => 'product', 'title' => __('Edit Dish','frozr'), 'desc' => __('Modify/Update your dish.','frozr') ) ); ?>

	<div id="product-edit" class="content-area-product-edit">

        <div class="dashboard-widgets">

		<?php frozr_edit_add_dish_body(); ?>
		
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