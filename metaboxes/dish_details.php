<?php
/**
 * Eater Dish Details
 *
 * @author      Frozr
 * @category    Metaboxes
 * @package     Frozr/Meta Boxes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Calls the class on the post edit screen.
 */
function call_EaterDishDetails() {
    new call_EaterDishDetails();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'call_EaterDishDetails' );
    add_action( 'load-post-new.php', 'call_EaterDishDetails' );
}

/** 
 * The Class.
 */
class call_EaterDishDetails {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
	 	// Styles, and JavaScript
	 	add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
	 
	 	// Setup the meta box hooks
	 	add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	 	add_action( 'save_post', array( $this, 'save' ) );
	}
	/*--------------------------------------------*
	 * Styles, and JavaScript
	 *--------------------------------------------*/
	
	/**
	 * Defines the admin styles.
	 */
	public function register_admin_styles() {
		wp_enqueue_style('dishdetails', plugins_url( '/metaboxes/css/dishdetails.css', lAZY_EATER_FILE ));
	} // end register_admin_styles
	
	/*--------------------------------------------*
	 * Hooks
	 *--------------------------------------------*/
	
	/**
	 * Introduces the Quick links meta box.
	 */ 
	public function add_meta_box( $post_type ) {
    $post_types = array('product');     //limit meta box to certain post types
    if ( in_array( $post_type, $post_types )) {
		add_meta_box(
			'eaterdishdetails',
			__( 'Dish Details', 'frozr' ),
			array( $this, 'dishdetails' ),
			$post_type,
			'side'
		);
    }
	} // quick_links_metabox

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['eaterdishdetails_nonce'] ) )
			return $post_id;

		$nonce = esc_attr($_POST['eaterdishdetails_nonce']);

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'edd_nonce' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
        // so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'product' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Update the meta field.
		if( isset( $_POST[ 'dish_veg' ] ) ) {
			update_post_meta( $post_id, '_dish_veg', esc_attr($_POST[ 'dish_veg' ]) );
		} else {
			update_post_meta( $post_id, '_dish_veg', 'veg' );
		}
		if( isset( $_POST[ 'dish_spicy' ] ) ) {
			update_post_meta( $post_id, '_dish_spicy', 'yes' );
		} else {
			update_post_meta( $post_id, '_dish_spicy', '' );
		}
		if( isset( $_POST[ 'dish_fat' ] ) ) {
			update_post_meta( $post_id, '_dish_fat', 'yes' );
		} else {
			update_post_meta( $post_id, '_dish_fat', '' );
		}
		if( isset( $_POST[ 'dish_fat_rate' ] ) ) {
			update_post_meta( $post_id, '_dish_fat_rate', esc_attr($_POST[ 'dish_fat_rate' ]) );
		} else {
			update_post_meta( $post_id, '_dish_fat_rate', '' );
		}

		do_action('frozr_after_save_backend_dish_details');

	}

	 /* Adds the file input box for the post meta data.
	 *
	 * @param object $post The post to which this information is going to be saved.
	 */
	public function dishdetails( $post ) {
			
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'edd_nonce', 'eaterdishdetails_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$frozr_stored_meta = get_post_meta( $post->ID );
			
		?>
		<div class="quick_links_content">
			<label for="dish_veg">
				<input type="radio" name="dish_veg" id="dish_veg" value="veg" <?php if ( isset ( $frozr_stored_meta['_dish_veg'] ) ) checked($frozr_stored_meta['_dish_veg'][0], 'veg' ); ?>>
				<?php _e( 'A Veg Dish.', 'frozt' )?>
			</label>
			<br>
			<label for="dish_nonveg">
				<input type="radio" name="dish_veg" id="dish_nonveg" value="nonveg" <?php if ( isset ( $frozr_stored_meta['_dish_veg'] ) ) checked($frozr_stored_meta['_dish_veg'][0], 'nonveg' ); ?>>
				<?php _e( 'A Non-Veg Dish.', 'frozt' )?>
			</label>
			<br>
			<label for="dish_spicy">
				<input type="checkbox" name="dish_spicy" id="dish_spicy" value="yes" <?php if ( $frozr_stored_meta['_dish_spicy'][0] == "yes" ) {echo 'checked="checked"';} ?> />
				<?php _e( 'Dish is Spicy', 'frozt' )?>
			</label>
			<br>
			<label for="dish_fat">
				<input type="checkbox" name="dish_fat" id="dish_fat" value="yes" <?php if ( $frozr_stored_meta['_dish_fat'][0] == "yes" ) {echo 'checked="checked"';} ?> />
				<?php _e( 'Show Fat Amount?', 'frozt' )?>
			</label>
			<br>
			<div class="hide_show_opt">
				<label for="dish_fat_rate">
				<input type="number" name="dish_fat_rate" id="dish_fat_rate" min="0" max="100" value="<?php echo $frozr_stored_meta['_dish_fat_rate'][0]; ?>" data-popup-enabled="true">
				<?php _e( 'Amount of Fat in Grams.', 'frozt' )?>
				</label>
			</div>

			<?php do_action('frozr_after_backend_dish_details'); ?>

		</div>
		<script type="text/javascript">// <![CDATA[
		jQuery(document).ready(function () {
			jQuery('.hide_show_opt').hide();
			jQuery( "#dish_fat" ).change(function() {
			  var input = jQuery( this );
			  if (input.is( ":checked" ) == true) {
			  jQuery('.hide_show_opt').show();
			  } else {
			  jQuery('.hide_show_opt').hide();
			  }
			});
		});// ]]></script>
	<?php
	} // end post_link
}