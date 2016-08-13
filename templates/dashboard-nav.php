<?php
/**
 * Dashboard - Navigation
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div id="dokkan-wrapper" data-role="false">

<?php if (!frozr_mobile()) { do_action('frozr_dash_sidebar', $active_menu); } ?>
<div class="dash-content">
	<?php if (frozr_mobile()){ echo '<a href="#dash-side-panel" class="dash_menu_icon"><i class="fs-icon-navicon"></i> '. __('Dashboard Menu','frozr') .'</a>';} ?>
	<div class="dash_title_content">
		<span class="dash_title"><?php echo $title; ?></span>
		<span class="dash_content"><?php echo $desc; ?></span>
	</div>
