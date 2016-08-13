<?php
/**
 * Orders - Order Details
 *
 * @package FrozrCoreLibrary
 * @subpackage FrozrmarketTemplates
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $wpdb;

$order_id = apply_filters('frozr_detailed_order_id',isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0, $_GET['order_id']);

$orders = get_post($order_id);

if ( get_current_user_id() != $orders->post_author && !is_super_admin()) {
    echo '<div class="alert alert-danger">' . __( 'This is not yours, I swear!', 'frozr' ) . '</div>';
    return;
}
$order = new WC_Order( $order_id );

?>
	<div class="order_datails_container">
		<div class="order_table">
			<div class="or or-default">
				<div class="or-heading"><strong><?php printf( 'Order#%d', $order->id ); ?></strong> &rarr; <?php _e( 'Order Items', 'frozr' ); ?></div>
				<div class="or-body order_items_tbl">
				<?php frozr_order_items_table($order); ?>
				</div>
			</div>
			<div class="or or-default order_details">
				<div class="or-heading"><strong><?php _e( 'Cutomer Details', 'frozr' ); ?></strong></div>
				<?php frozr_order_customer_details($order); ?>
			</div>
			<div class="or or-default order_details">
				<div class="or-heading"><strong><?php _e( 'General Details', 'frozr' ); ?></strong></div>
				<?php frozr_order_general_details($order); ?>
			</div>
		</div>
		<div class="or_notes">
			<div class="or or-default">
				<div class="or-heading"><strong><?php _e( 'Order Notes', 'frozr' ); ?></strong></div>
				<div class="or-body" id="frozr-order-notes">
					<?php
					$args = array(
					'post_id'   => $orders->ID,
					'orderby'   => 'comment_ID',
					'order'     => 'DESC',
					'approve'   => 'approve',
					'type'      => 'order_note'
					);

					remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

					$notes = get_comments( $args );

					add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

					echo '<ul class="order_notes">';

					if ( $notes ) {

						foreach( $notes as $note ) {

						$note_classes = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? array( 'customer-note', 'note' ) : array( 'note' );

						?>
						<li rel="<?php echo absint( $note->comment_ID ) ; ?>" class="<?php echo implode( ' ', $note_classes ); ?>">
							<div class="note_content">
								<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
							</div>
							<p class="order_meta">
								<abbr class="exact-date" title="<?php echo $note->comment_date; ?>"><?php printf( __( 'added on %1$s at %2$s', 'frozr' ), date_i18n( wc_date_format(), strtotime( $note->comment_date ) ), date_i18n( wc_time_format(), strtotime( $note->comment_date ) ) ); ?></abbr>
								<?php if ( $note->comment_author !== __( 'WooCommerce', 'frozr' ) ) printf( ' ' . __( 'by %s', 'frozr' ), $note->comment_author ); ?>
								<a href="#" class="delete_note"><?php _e( 'Delete note', 'frozr' ); ?></a>
							</p>
						</li>
						<?php }

					} else {
						echo '<li>' . __( 'There are no notes yet.', 'frozr' ) . '</li>';
					}

					echo '</ul>';
					?>
					<div class="add_note">
						<h4><?php _e( 'Add note', 'frozr' ); ?> <img class="help_tip" data-tip='<?php esc_attr_e( 'Add a note for your reference, or add a customer note (the user will be notified).', 'frozr' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></h4>
						<p>
							<textarea type="text" name="order_note" id="add_order_note" class="input-text" cols="20" rows="5"></textarea>
						</p>
						<p>
							<select name="order_note_type" id="order_note_type">
								<option value=""><?php _e( 'Private note', 'frozr' ); ?></option>
								<option value="customer"><?php _e( 'Note to customer', 'frozr' ); ?></option>
							</select>
							<a href="#" class="add_note button"><?php _e( 'Add', 'frozr' ); ?></a>
						</p>
					</div>
				</div> <!-- .or-body -->
			</div> <!-- .or -->
		</div>
	</div>
