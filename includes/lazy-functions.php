<?php
/**
 * Lazy Eater Main Functions
 *
 * @package FrozrCoreLibrary
 * @subpackage Frozrmarketlibrary
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * add the restaurant ingredients taxonomy
 */
add_action( 'init', 'frozr_create_ingredients', 0 );
if (!function_exists ('frozr_create_ingredients') ) {
	function frozr_create_ingredients() {

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = apply_filters( 'frozr_ingredents_taxonomy_labels', array(
			'name'                       => _x( 'Ingredients', 'frozr' ),
			'singular_name'              => _x( 'Ingredient', 'frozr' ),
			'search_items'               => __( 'Search Ingredients', 'frozr' ),
			'popular_items'              => __( 'Popular Ingredients', 'frozr' ),
			'all_items'                  => __( 'All Ingredients', 'frozr' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Ingredient', 'frozr' ),
			'update_item'                => __( 'Update Ingredient', 'frozr' ),
			'add_new_item'               => __( 'Add New Ingredient', 'frozr' ),
			'new_item_name'              => __( 'New Ingredient Name', 'frozr' ),
			'separate_items_with_commas' => __( 'Separate Ingredients with commas', 'frozr' ),
			'add_or_remove_items'        => __( 'Add or remove Ingredients', 'frozr' ),
			'choose_from_most_used'      => __( 'Choose from the most used Ingredients', 'frozr' ),
			'not_found'                  => __( 'No Ingredients found.', 'frozr' ),
			'menu_name'                  => __( 'Ingredients', 'frozr' ),
		));

		$args = apply_filters( 'frozr_ingredents_taxonomy_args', array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'ingredient' ),
		));

		register_taxonomy( 'ingredient', 'product', $args );
	}
}
/**
 * add the restaurant delivery taxonomy
 */
add_action( 'init', 'frozr_create_delivery_location', 0 );
if (!function_exists ('frozr_create_delivery_location') ) {
	function frozr_create_delivery_location() {

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = apply_filters( 'frozr_delivery_locations_taxonomy_labels', array(
			'name'                       => _x( 'Delivery Locations', 'taxonomy general name' ),
			'singular_name'              => _x( 'Delivery Location', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Locations' ),
			'popular_items'              => __( 'Popular Locations' ),
			'all_items'                  => __( 'All Locations' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Location' ),
			'update_item'                => __( 'Update Location' ),
			'add_new_item'               => __( 'Add New Location' ),
			'new_item_name'              => __( 'New Location Name' ),
			'separate_items_with_commas' => __( 'Separate Locations with commas' ),
			'add_or_remove_items'        => __( 'Add or remove Locations' ),
			'choose_from_most_used'      => __( 'Choose from the most used Locations' ),
			'not_found'                  => __( 'No Locations found.' ),
			'menu_name'                  => __( 'Delivery Locations' ),
		));

		$args = apply_filters( 'frozr_delivery_locations_taxonomy_args', array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_in_quick_edit'	=> false,
			'show_admin_column'     => false,
			'meta_box_cb'           => false,
			'update_count_callback' => '_update_generic_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'location' ),
		));

		register_taxonomy( 'location', 'user', $args );
	}
}
/**
 * add the restaurant addresses taxonomy
 */
add_action( 'init', 'frozr_create_addresses_taxonomy', 0 );
if (!function_exists ('frozr_create_addresses_taxonomy') ) {
	function frozr_create_addresses_taxonomy() {

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = apply_filters( 'frozr_restaurant_addresses_taxonomy_labels', array(
			'name'                       => _x( 'Restaurant Addresses', 'taxonomy general name' ),
			'singular_name'              => _x( 'Restaurant Address', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Restaurant Addresses' ),
			'popular_items'              => __( 'Popular Addresses' ),
			'all_items'                  => __( 'All Addresses of Restaurants' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Address' ),
			'update_item'                => __( 'Update Address' ),
			'add_new_item'               => __( 'Add New Address' ),
			'new_item_name'              => __( 'New Address' ),
			'separate_items_with_commas' => __( 'Separate Addresses with commas' ),
			'add_or_remove_items'        => __( 'Add or remove Addresses' ),
			'choose_from_most_used'      => __( 'Choose from the most used Addresses' ),
			'not_found'                  => __( 'No Addresses found.' ),
			'menu_name'                  => __( 'Restaurant Addresses' ),
		));

		$args = apply_filters( 'frozr_restaurant_addresses_taxonomy_args', array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_in_quick_edit'	=> false,
			'show_admin_column'     => false,
			'meta_box_cb'           => false,
			'update_count_callback' => '_update_generic_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'restaurant_addresses' ),
		));

		register_taxonomy( 'restaurant_addresses', 'user', $args );
	}
}
/**
 * add the restaurant type taxonomy
 */
add_action( 'init', 'frozr_create_rest_type', 0 );
if (!function_exists ('frozr_create_rest_type') ) {
	function frozr_create_rest_type() {

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = apply_filters( 'frozr_restaurant_types_taxonomy_labels', array(
			'name'                       => _x( 'Restaurant Cuisine', 'taxonomy general name' ),
			'singular_name'              => _x( 'Restaurant Cuisine', 'taxonomy singular name' ),
			'search_items'               => __( 'Cuisines' ),
			'popular_items'              => __( 'Popular Restaurant Cuisines' ),
			'all_items'                  => __( 'All Types' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Cuisine' ),
			'update_item'                => __( 'Update Cuisine' ),
			'add_new_item'               => __( 'Add New Cuisine' ),
			'new_item_name'              => __( 'New Cuisine Name' ),
			'separate_items_with_commas' => __( 'Separate cuisines with commas' ),
			'add_or_remove_items'        => __( 'Add or remove cuisines' ),
			'choose_from_most_used'      => __( 'Choose from the most used cuisines' ),
			'not_found'                  => __( 'No cuisine found.' ),
			'menu_name'                  => __( 'Restaurant Cuisines' ),
		));

		$args = apply_filters( 'frozr_restaurant_types_taxonomy_args', array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_in_quick_edit'	=> false,
			'show_admin_column'     => false,
			'meta_box_cb'           => false,
			'update_count_callback' => '_update_generic_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'cuisine' ),
		));

		register_taxonomy( 'cuisine', 'user', $args );
	}
}
/**
 * Check if current user is the author
 *
 * @global WP_Post $post
 * @param int $product_id
 * @return boolean
 */
if (!function_exists ('frozr_is_author') ) {
	function frozr_is_author( $product_id = 0 ) {
		global $post;

		if ( $product_id == 0 ) {
			$author = $post->post_author;
		} else {
			$author = get_post_field( 'post_author', $product_id );
		}

		if ( $author == get_current_user_id() ) {
			return true;
		}

		return false;
	}
}
/**
 * Redirect to login page if not already logged in
 *
 * @return void
 */
if (!function_exists ('frozr_redirect_login') ) {
	function frozr_redirect_login() {
		if ( ! is_user_logged_in() ) {
			wp_redirect( get_permalink(wc_get_page_id( 'myaccount' )) );
			exit;
		}
	}
}
/**
 * If the current user is not seller, redirect to homepage
 *
 * @param string $redirect
 */
if (!function_exists ('frozr_redirect_if_not_seller') ) {
	function frozr_redirect_if_not_seller( $redirect = '' ) {
		if ( !user_can( get_current_user_id(), 'frozer' ) && !is_super_admin() || !frozr_is_seller_enabled(get_current_user_id()) && !is_super_admin() ) {
			$redirect = empty( $redirect ) ? home_url( '/' ) : $redirect;

			wp_redirect( $redirect );
			exit;
		}
	}
}
/**
 * Count post type from a user
 *
 * @global WPDB $wpdb
 * @param string $post_type
 * @param int $user_id
 * @return array
 */ 
if (!function_exists ('frozr_count_posts') ) {
	function frozr_count_posts( $post_type, $user_id ) {

		global $wpdb;

		$cache_key = 'frozr-count-' . $post_type . '-' . $user_id;
		$counts = wp_cache_get( $cache_key, 'frozr' );

		if ( false === $counts ) {
			$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d GROUP BY post_status";
			$results = $wpdb->get_results( $wpdb->prepare( $query, $post_type, $user_id ), ARRAY_A );
			$counts = array_fill_keys( get_post_stati(), 0 );

			$total = 0;
			foreach ( $results as $row ) {
				$counts[ $row['post_status'] ] = (int) $row['num_posts'];
				$total += (int) $row['num_posts'];
			}

			$counts['total'] = $total;
			$counts = (object) $counts;
			wp_cache_set( $cache_key, $counts, 'frozr' );
		}
		return $counts;
	}
}
/**
 * Function to get the client ip address
 *
 * @return string
 */
if (!function_exists ('frozr_get_client_ip') ) {
	function frozr_get_client_ip() {
		$ipaddress = '';

		if ( getenv( 'HTTP_CLIENT_IP' ) )
			$ipaddress = getenv( 'HTTP_CLIENT_IP' );
		else if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )
			$ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' & quot );
		else if ( getenv( 'HTTP_X_FORWARDED' ) )
			$ipaddress = getenv( 'HTTP_X_FORWARDED' );
		else if ( getenv( 'HTTP_FORWARDED_FOR' ) )
			$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
		else if ( getenv( 'HTTP_X_CLUSTER_CLIENT_IP' ) )
			$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
		else if ( getenv( 'HTTP_FORWARDED' ) )
			$ipaddress = getenv( 'HTTP_FORWARDED' );
		else if ( getenv( 'REMOTE_ADDR' ) )
			$ipaddress = getenv( 'REMOTE_ADDR' );
		else
			$ipaddress = 'UNKNOWN';

		return $ipaddress;
	}
}
/**
 * generate a input box based on arguments
 *
 * @param int $post_id
 * @param string $meta_key
 * @param array $attr
 * @param string $type
 */
if (!function_exists ('frozr_post_input_box') ) {
	function frozr_post_input_box( $post_id, $meta_key, $attr = array(), $type = 'text'  ) {
		$placeholder = isset( $attr['placeholder'] ) ? esc_attr( $attr['placeholder'] ) : '';
		$class = isset( $attr['class'] ) ? esc_attr( $attr['class'] ) : 'form-control';
		$name = isset( $attr['name'] ) ? esc_attr( $attr['name'] ) : $meta_key;
		if ($post_id) {
			$value = isset( $attr['value'] ) ? $attr['value'] : get_post_meta( $post_id, $meta_key, true );
		} else {
			$value = "";
		}
		$size = isset( $attr['size'] ) ? $attr['size'] : 30;

		switch ($type) {
			case 'text':
				?>
				<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>" class="<?php echo $class; ?>" placeholder="<?php echo $placeholder; ?>">
				<?php
				break;

			case 'textarea':
				$rows = isset( $attr['rows'] ) ? absint( $attr['rows'] ) : 4;
				?>
				<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" rows="<?php echo $rows; ?>" class="<?php echo $class; ?>" placeholder="<?php echo $placeholder; ?>"><?php echo esc_textarea( $value ); ?></textarea>
				<?php
				break;

			case 'checkbox':
				$label = isset( $attr['label'] ) ? $attr['label'] : '';
				?>

				<label class="checkbox-inline" for="<?php echo $name; ?>">
					<input name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $value; ?>" type="checkbox"<?php checked( $value, 'yes' ); ?>>
					<?php echo $label; ?>
				</label>

				<?php
				break;

			case 'select':
				$options = is_array( $attr['options'] ) ? $attr['options'] : array();
				?>
				<select name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="<?php echo $class; ?>">
					<?php foreach ($options as $key => $label) { ?>
						<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $value, $key ); ?>><?php echo $label; ?></option>
					<?php } ?>
				</select>

				<?php
				break;

			case 'number':
				$min = isset( $attr['min'] ) ? $attr['min'] : 0;
				$step = isset( $attr['step'] ) ? $attr['step'] : 'any';
				?>
				<input type="number" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>" class="<?php echo $class; ?>" placeholder="<?php echo $placeholder; ?>" min="<?php echo esc_attr( $min ); ?>" step="<?php echo esc_attr( $step ); ?>" size="<?php echo esc_attr( $size ); ?>">
				<?php
				break;
				
				do_action('frozr_post_input_box_type', $name, $class, $placeholder, $attr, $value);
		}
	}
}
/**
 * Hidden field
 */
if (!function_exists ('frozr_wp_hidden_input') ) {
	function frozr_wp_hidden_input( $field ) {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
		$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
		echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) .  '" /> ';
	}
}
/**
 * Textarea field
 */
if (!function_exists ('frozr_wp_textarea_input') ) {
	function frozr_wp_textarea_input( $field ) {
		global $thepostid, $post;
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
		// Custom attribute handling
		$custom_attributes = array();
		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
			foreach ( $field['custom_attributes'] as $attribute => $value ){
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}
		echo '<p class="fl-form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="2" cols="20" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';
		if ( ! empty( $field['description'] ) ) {
			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				echo frozr_wc_help_tip( $field['description'] );
			} else {
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}
		}
		echo '</p>';
	}
}
/**
 * Select field
 */
if (!function_exists ('frozr_wp_select') ) {
	function frozr_wp_select( $field ) {
		global $thepostid, $post;
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$m = (! empty( $field['custom_attributes']) && $field['custom_attributes']['multiple'] == 'multiple') ? '[]' : '';
		
		// Custom attribute handling
		$custom_attributes = array();
		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
			foreach ( $field['custom_attributes'] as $attribute => $value ){
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}
		echo '<p class="fl-form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . $m .'" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . '>';
		foreach ( $field['options'] as $key => $value ) {
			if ($field['custom_attributes']['multiple'] == 'multiple') {
				$selected = ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' );
			} else {
				$selected = ( esc_attr( $field['value'] ) == esc_attr( $key ) ) ? 'selected="selected"' : '';
			}

			echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $value ) . '</option>';
		}
		echo '</select> ';
		if ( ! empty( $field['description'] ) ) {
			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				echo frozr_wc_help_tip( $field['description'] );
			} else {
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}
		}
		echo '</p>';
	}
}
/**
 * Output a radio input box.
 *
 * @param array $field
 */
function frozr_wp_radio( $field ) {
	global $thepostid, $post;
	$product_author = get_post_field( 'post_author', $post->id );
	$store_info = frozr_get_store_info( $product_author );

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];

	echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['label'] ) . '</legend><ul class="wc-radios">';

	
	foreach ( $field['options'] as $key => $value ) {
	if (in_array($key, $store_info['accpet_order_type'])) {
		if ('' != $value[1]) { $ricon = '<i class="fado_radio_icon '. $value[1]  .'"></i>'; } else { $ricon = ''; }
		
			echo '<li>'. $ricon .'<label><input
					name="' . esc_attr( $field['name'] ) . '"
					id="' . esc_attr( $key ) . '_'. esc_attr( $field['id'] ) . '"
					value="' . esc_attr( $key ) . '"
					type="radio"
					class="' . esc_attr( $field['class'] ) .' '. esc_attr( $value[2] ) . '"
					style="' . esc_attr( $field['style'] ) . '"
					' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
					/> ' . esc_html( $value[0] ) . '</label>
			</li>';
		}
	}
	echo '</ul>';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			echo wc_help_tip( $field['description'] );
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}
	}

	echo '</fieldset>';
}
/**
 * checkbox field
 */
if (!function_exists ('frozr_wp_checkbox') ) {
	function frozr_wp_checkbox( $field ) {
		global $thepostid, $post;
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
		$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		// Custom attribute handling
		$custom_attributes = array();
		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
			foreach ( $field['custom_attributes'] as $attribute => $value ){
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}
		echo '<p class="fl-form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';
		if ( ! empty( $field['description'] ) ) {
			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				echo frozr_wc_help_tip( $field['description'] );
			} else {
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}
		}
		echo '</p>';
	}
}
/**
 * Help tip
 */
if (!function_exists ('frozr_wc_help_tip') ) {
	function frozr_wc_help_tip( $tip, $allow_html = false ) {
		if ( $allow_html ) {
			$tip = wc_sanitize_tooltip( $tip );
		} else {
			$tip = esc_attr( $tip );
		}
		return '<span class="woocommerce-help-tip" data-tip="' . $tip . '"></span>';
	}
}
/**
 * Text Input field
 */
if (!function_exists ('frozr_wp_text_input') ) {
	function frozr_wp_text_input( $field ) {
		global $thepostid, $post;
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
		$data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];
		switch ( $data_type ) {
			case 'price' :
				$field['class'] .= ' wc_input_price';
				$field['value']  = wc_format_localized_price( $field['value'] );
				break;
			case 'decimal' :
				$field['class'] .= ' wc_input_decimal';
				$field['value']  = wc_format_localized_decimal( $field['value'] );
				break;
			case 'stock' :
				$field['class'] .= ' wc_input_stock';
				$field['value']  = wc_stock_amount( $field['value'] );
				break;
			case 'url' :
				$field['class'] .= ' wc_input_url';
				$field['value']  = esc_url( $field['value'] );
				break;
			default :
				break;
		}
		// Custom attribute handling
		$custom_attributes = array();
		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
			foreach ( $field['custom_attributes'] as $attribute => $value ){
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}
		echo '<p class="fl-form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';
		if ( ! empty( $field['description'] ) ) {
			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				echo frozr_wc_help_tip( $field['description'] );
			} else {
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}
		}
		echo '</p>';
	}
}

/**
 * Helper function to include a file
 *
 * @param type $template_name
 * @param type $args
 */
if (!function_exists ('frozr_get_template') ) {
	function frozr_get_template( $template_name, $args = array() ) {

		if ( file_exists( $template_name ) ) {
			extract( apply_filters('frozr_get_template_args', $args) );

			include_once $template_name;
		}
	}
}
/**
 * Check if the seller is enabled
 *
 * @param int $user_id
 * @return boolean
 */
if (!function_exists ('frozr_is_seller_enabled') ) {
	function frozr_is_seller_enabled( $user_id ) {
		$selling = get_user_meta( $user_id, 'frozr_enable_selling', true );

		if ( $selling == 'yes' ) {
			return true;
		}

		return false;
	}
}
/**
 * Add cart total amount on add_to_cart_fragments
 *
 * @param array $fragment
 * @return array
 */
if (!function_exists ('frozr_add_to_cart_fragments') ) {
	function frozr_add_to_cart_fragments( $fragment ) {
		$fragment['amount'] = WC()->cart->get_cart_total();

		return $fragment;
	}
}
add_filter( 'add_to_cart_fragments', 'frozr_add_to_cart_fragments' );
/**
 * Prevent sellers and customers from seeing the admin bar
 *
 * @param bool $show_admin_bar
 * @return bool
 */
if (!function_exists ('frozr_disable_admin_bar') ) {
	function frozr_disable_admin_bar( $show_admin_bar ) {
		global $current_user;

		if ( $current_user->ID !== 0 ) {
			$role = reset( $current_user->roles );

			if ( in_array( $role, apply_filters('frozr_disable_admin_access_roles', array( 'seller', 'customer' )) ) ) {
				return false;
			}
		}

		return $show_admin_bar;
	}
}
add_filter( 'show_admin_bar', 'frozr_disable_admin_bar' );

/**
 * Getting the product subtotal
 */
if (!function_exists ('frozr_get_product_subtotal') ) {
	function frozr_get_product_subtotal( $_product, $quantity ) {

		  $price       = $_product->get_price();
		  $taxable     = $_product->is_taxable();

		  // Taxable
		  if ( $taxable ) {

			if ( WC()->cart->tax_display_cart == 'excl' ) {

			  $row_price        = $_product->get_price_excluding_tax( $quantity );
			  $product_subtotal = $row_price;

			  if ( WC()->cart->prices_include_tax && WC()->cart->tax_total > 0 ) {
				$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
			  }

			} else {

			  $row_price        = $_product->get_price_including_tax( $quantity );
			  $product_subtotal = $row_price;

			  if ( ! WC()->cart->prices_include_tax && WC()->cart->tax_total > 0 ) {
				$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
			  }

			}

		  // Non-taxable
		  } else {

			$row_price        = $price * $quantity;
			$product_subtotal = $row_price;

		  }

		  return apply_filters( 'woocommerce_cart_product_subtotal', $product_subtotal, $_product, $quantity );
	}
}
/**
 * Default accepted orders type
 */
function frozr_default_accepted_orders_types() {
	$accepted_orders = array("delivery", "pickup", "dine-in", "curbside");
	
	return apply_filters('frozr_default_accepted_orders_types', $accepted_orders);
}
/**
 * Adds the delivery fee to the cart total
 */
if (!function_exists ('frozr_add_delivery_fee') ) {
	function frozr_add_delivery_fee() {
		global $woocommerce;

		$cop_vals = array();
		$cop_auths = array();
		$por_authos = array();
		$delivey_total = array();

		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id	= apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
		
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$auth = get_post_field('post_author', $product_id);
				$por_authos[$auth][] = $product_id;
			}
		}

		foreach ( $woocommerce->cart->get_applied_coupons() as $code ) {
			$coupon = new WC_Coupon( $code );
			$cop_vals[$coupon->id] = get_post_field('post_author', $coupon->id);
			$cop_auths[get_post_field('post_author', $coupon->id)] = $coupon->id;
		}

		foreach ($por_authos as $por_autho => $pid) {
			$seller_info = frozr_get_store_info($por_autho);
			$div_total = array();
			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
				$product_id		= apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				$pauth			= get_post_field('post_author', $product_id);
				if ($pauth == $por_autho) {
					$cop_val = get_post_field('post_author', $product_id);
					$auth_cop_cal = (! empty ($cop_auths[$cop_val]) ) ? $cop_auths[$cop_val]: '';
					$delv_val = get_post_meta($auth_cop_cal, 'free_shipping', true);
					
					do_action('frozr_before_order_items_delivery_check', $cart_item_key, $cart_item);
					
					if ( empty ($cop_vals[$auth_cop_cal]) || $cop_vals[$auth_cop_cal] != $cop_val || $delv_val != 'yes' || !empty ($seller_info['shipping_fee'])) {
						if ($seller_info['deliveryby'] == 'item' && $cart_item['order_l_type'] == 'delivery') {

							do_action('frozr_order_item_delivery_fee_added', $cart_item_key, $cart_item);

							$div_total[] = $cart_item['quantity'];

						} elseif ($seller_info['deliveryby'] != 'item' && $cart_item['order_l_type'] == 'delivery') {

							do_action('frozr_order_cart_delivery_fee_added', $cart_item_key, $cart_item);

							$div_total[0] = 'bycart';
						}
					} else {
						
						do_action('frozr_order_free_delivery_added', $cart_item_key, $cart_item);
						
						$div_total[0] = 'free';
					}
					
					do_action('frozr_after_order_items_delivery_check', $cart_item_key, $cart_item);
				}
			}
			//Lets check if we actually have a fee, then add it
			$total_div = array_sum($div_total);
			$default_shipping = (!empty ($seller_info['shipping_fee'])) ? $seller_info['shipping_fee'] : 0;
			$default_adl_shipping = (!empty ($seller_info['shipping_pro_adtl_cost'])) ? $seller_info['shipping_pro_adtl_cost'] : 0;
			if ($div_total[0] != 'free') {
				
				do_action('frozr_before_order_items_fee_check', $por_autho, $pid);
				
				if ( $div_total[0] == 'bycart' || $total_div == 1) {
					$delivey_total[] = $default_shipping;
				} elseif ($total_div > 1) {
					$_adl_fees_add = ($total_div - 1) * $default_adl_shipping;
					$delivey_total[] = $default_shipping + $_adl_fees_add;
				}
				
				do_action('frozr_after_order_items_fee_check', $por_autho, $pid);
			}
		}
		$the_totla = array_sum($delivey_total);
		if ($the_totla > 0) {
			$woocommerce->cart->add_fee( __('Total Delivery', 'frozr'), apply_filters('frozr_total_delivery_fee', $the_totla));
		}
	}
}
add_action( 'woocommerce_cart_calculate_fees','frozr_add_delivery_fee' );
/**
 * User avatar
 */
if (!function_exists ('frozr_get_avatar') ) {
	function frozr_get_avatar( $avatar, $id_or_email, $size, $default, $alt ) {

		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', $id_or_email );
		} elseif ( is_object( $id_or_email ) ) {
			if ( $id_or_email->user_id != '0' ) {
				$user = get_user_by( 'id', $id_or_email->user_id );
			} else {
				return $avatar;
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
		}

		if ( !$user ) {
			return $avatar;
		}

		// see if there is a user_avatar meta field
		$user_avatar = get_user_meta( $user->ID, 'frozr_profile_settings', true );
		$gravatar_id = isset( $user_avatar['gravatar'] ) ? $user_avatar['gravatar'] : 0;
		if ( empty( $gravatar_id ) ) {
			return $avatar;
		}

		$avater_url = wp_get_attachment_thumb_url( $gravatar_id );

		return apply_filters('frozr_user_avatar', sprintf( '<div style="background-image:url(%1$s); background-color:transparent; background-size:%3$spx %3$spx; border: medium none; height:%3$spx; margin:0 auto; width:%3$spx;" alt="%2$s" class="avatar photo"></div>', esc_url( $avater_url ), $alt, $size ));
	}
}
add_filter( 'get_avatar', 'frozr_get_avatar', 99, 5 );
/*
 * Get the Avatar Url
*/
if (!function_exists ('frozr_avatar_url') ) {
	function frozr_avatar_url($userid){
		// see if there is a user_avatar meta field
		$user_avatar = get_user_meta( $userid, 'frozr_profile_settings', true );
		$gravatar_id = isset( $user_avatar['gravatar'] ) ? $user_avatar['gravatar'] : 0;
		if ( empty( $gravatar_id ) ) {
			return $avatar;
		}

		$avater_url = wp_get_attachment_thumb_url( $gravatar_id );
		
		return $avater_url;

	}
}
//add the dashboard menu to frozr user menu
if (!function_exists ('frozr_lazyeater_dash_menu') ) {
	function frozr_lazyeater_dash_menu(){
		$title = (is_super_admin()) ? __('Admin','frozr') : __('Seller','frozr');
	?>
		<?php if (frozr_is_seller_enabled(get_current_user_id()) || is_super_admin()) {
		$urls = frozr_get_dashboard_nav();
		?>
		<div data-role="collapsible" data-iconpos="right" data-collapsed-icon="gear" data-expanded-icon="minus" class="usr_dash_menu_content">
			<h2><?php echo apply_filters('frozr_your_dashboard_text', sprintf(__('%s Dashboard','frozr'), $title)); ?></h2>

			<?php do_action('frozr_before_lazyeater_dash_menu'); ?>

			<ul data-role="listview">
			<?php
			foreach ($urls as $key => $item) {
				if($key == 'dashboard') {$xn = 'home';} elseif ($key == 'dishes') {$xn = 'star';} elseif ($key == 'order') {$xn = 'comment';} elseif ($key == 'coupon') {$xn = 'tag';} elseif ($key == 'withdraw') {$xn = 'minus';} elseif ($key == 'settings') {$xn = 'edit';} elseif ($key == 'sellers') {$xn = 'user';}
				printf( '<li%s><a class="ui-btn ui-btn-icon-right ui-icon-%s"href="%s">%s</a></li>', $class, $xn, $item['url'], $item['title'] );
			} ?>
			</ul>

			<?php do_action('frozr_before_lazyeater_dash_menu'); ?>

		</div>
	<?php }
	}
}
add_action('frozr_add_user_top_menu_item', 'frozr_lazyeater_dash_menu');

//user location form
if (!function_exists ('frozr_user_location_form') ) {
	function frozr_user_location_form($nx='') {
		//get all locations
		$getallocs = apply_filters('frozr_user_location_form_terms', get_terms( 'location', 'hide_empty=0' )); ?>
		
		<div class="loc_form_wrapper">
		
			<?php do_action('frozr_before_user_location_form', $nx); ?>
		
			<ul class="user_location_ul" data-role="listview" data-filter="true" data-filter-reveal="true" data-filter-placeholder="<?php echo apply_filters('frozr_user_location_input_placeholder_text', __('Type the first three letters and choose from the list.','frozr')); ?>" data-inset="true">
				<?php
				if ( ! empty( $getallocs ) && ! is_wp_error( $getallocs ) ){
				foreach ( $getallocs as $term ) {
					echo "<li><a href=\"#\" data-aft=\"".$nx."\" data-loc=\"". $term->slug ."\">" . $term->name . "</a></li>";
				} } ?>
			</ul>
			
			<?php do_action('frozr_after_user_location_form', $nx); ?>
			
		</div>
	<?php
	}
}

//location not set warning
if (!function_exists ('frozr_location_not_set') ) {
	function frozr_location_not_set($in='rest') {
		if ($in == 'dish') {
			$ind = __('products.','frozr');
		} else {
			$ind = __('restaurants.','frozr');
		}
	?>
			<div class="style_box fs-icon-warning">
			
				<?php do_action('frozr_before_user_location_notice', $in); ?>
				
				<p><?php echo apply_filters('frozr_no_location_notice_text', __('You have not set your location yet, if you want to make some food delivery order, you might be out of the delivery zone of these ','frozr')) . $ind; ?>&nbsp;<a title="" data-transition="fade" data-rel="popup" href="#loc_pop"><?php echo apply_filters('frozr_location_set_request_text', __('Set your location.','frozr')); ?></a></p>
				
				<?php do_action('frozr_after_user_location_notice', $in); ?>
			
			</div>
	<?php
	}
}
//location pop-up
if (!function_exists ('frozr_location_popup') ) {
	function frozr_location_popup() {
		$loc = (isset($_COOKIE['frozr_user_location'])) ? apply_filters('frozr_user_location_cookie', $_COOKIE['frozr_user_location']) : __('Unset','frozr');
		$dis = (isset($_COOKIE['frozr_user_location'])) ? __('Change','frozr') : __('Set','frozr'); ?>
		<div class="user_loc_top_menu">
			<span><?php echo apply_filters('frozr_your_location_popup_title_text', __('Your location is: ','frozr')) . '<strong>' . $loc . '</strong>'; ?>&nbsp;<a href="#loc_pop" data-transition="fade" data-rel="popup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-inline"><?php echo $dis . __(' it.','frozr'); ?></a>
			<div data-history="false" data-role="popup" id="loc_pop">
			
				<?php do_action('frozr_before_user_location_popup'); ?>
				
				<?php frozr_user_type_option(); ?>		
				<?php frozr_user_location_form('check'); ?>
				
				<?php do_action('frozr_after_user_location_popup'); ?>
							
			</div>
		</div>
	<?php 
	}
}
add_action('after_top_menu','frozr_location_popup');

//item add to cart function
if (!function_exists ('frozr_add_tocart') ) {
	function frozr_add_tocart($post) {
		global $post; ?>
		<?php if (!isset($_COOKIE['frozr_user_location'])) { ?>
			<h2><?php echo apply_filters('frozr_your_location_text', __("Your Location?","frozr")); ?></h2>
			<?php frozr_user_location_form('check'); ?>
		<?php } elseif (isset($_COOKIE['frozr_user_location'])) {
			$store_info = frozr_get_store_info($post->post_author);
			if (frozr_is_rest_open($post->post_author) == false && $store_info['allow_ofline_orders'] != 'yes') { ?>
				<div class="style_box fs-icon-warning"><?php echo apply_filters('frozr_no_closed_orders_text', __('This restaurant does not accept orders while closed. Please come back ','frozr')) . frozr_rest_status($post->post_author, false); ?></div>	
			<?php } else {
			$nc = '';
			$usersads = get_term_by( 'slug', $_COOKIE['frozr_user_location'], 'restaurant_addresses');
			$usersadsids = get_objects_in_term( (int) $usersads->term_id, 'restaurant_addresses');
			$userslocs = get_term_by( 'slug', $_COOKIE['frozr_user_location'], 'location');
			$userslocids = get_objects_in_term( (int) $userslocs->term_id, 'location');
			if (! in_array($post->post_author, $userslocids, true) && ! in_array($post->post_author, $usersadsids, true)) {
				$nc = 'no_delivery_location';
			} ?>
			<div class="fle_addtocart <?php echo $nc; ?>">
			
				<?php do_action('frozr_before_product_addtocart', $post); ?>
				
				<?php frozr_single_add_to_cart($post); ?>
				
				<?php do_action('frozr_after_product_addtocart', $post); ?>
				
			</div>
			<?php
			}
		}
	}
}
// Send emails messages
//item add to cart function
if (!function_exists ('frozr_send_msgs') ) {
	function frozr_send_msgs( $args = array(), $type ) {
		
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) === 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$headers = array();
		$headers[] = 'From: ' . $site_name . ' <noreply@'. $sitename .'>';
		$site_url = home_url();
		$admin_users_url = admin_url( 'users.php');
		$user_agent = substr( $_SERVER['HTTP_USER_AGENT'], 0, 150 );
		$to = ('' != $args['to'])? $args['to'] : get_option( 'admin_email' );
		
		do_action('before_frozr_messages',$args,$type);
		
		if ($type == 'rest_contact_msg') {
			$subject = (empty ($args['subject'])) ? sprintf( __( '"%s"! "%s" sent you a message from your "%s" restaurant page.', 'frozr' ), $args['restaurant_name'], $args['name'], $site_name ) : $args['subject'];
			$message = (empty ($args['subject'])) ? sprintf( frozr_email_msgs('cont-seller'), $args['name'], frozr_get_client_ip(), $user_agent, $args['msg'], $site_name, $site_url) : sprintf( frozr_email_msgs('cont-seller-admin'), $args['msg'], $site_name, $site_url);
			$headers[] = 'Cc: ' . get_option( 'admin_email' );
			$headers[] = (empty ($args['subject'])) ? 'Reply-To: '. $args['name'] . ' <' . $args['email'] . '>' : 'Reply-To: '. $site_name . ' <' . get_option( 'admin_email' ) . '>';
		} elseif ($type == 'rest_invit_msg') {
			$subject = (empty ($args['subject'])) ? sprintf( __( 'Invitation Letter from %s.', 'frozr' ), $site_name ) : $args['subject'];
			$message = sprintf( frozr_email_msgs('cont-seller-admin'), $args['msg'], $site_name, $site_url);
			$headers[] = 'Reply-To: '. $site_name . ' <' . get_option( 'admin_email' ) . '>';
		} elseif ($type == 'withdraw') {
			$subject = __( 'Withdraw Request # ', 'frozr' ) . $args['num'];
			$headers[] = 'Reply-To: ' . get_option( 'admin_email' );
			switch ($args['sts']) {
				case 'pending':
					$message = sprintf( frozr_email_msgs('with-pen'),$args['restaurant_name'], $args['num'], $args['amt'], $args['via'], $site_name, $site_url );
					break;
				case 'completed':
					$message = sprintf( frozr_email_msgs('with-com'),$args['restaurant_name'], $args['num'], $args['amt'], $args['via'], $site_name, $site_url );
					break;
				case 'trash':
					$message = sprintf( frozr_email_msgs('with-trash'),$args['restaurant_name'], $args['num'], $args['amt'], $args['via'], $args['note'], $site_name, $site_url );		
					break;
			}
		} elseif ($type == 'registration') {
			$user_id = $args['id'];
			$user_edit_url = get_edit_user_link($user_id);
			$user_dashboard = home_url('/dashboard/home/');
			$user_privileges = (get_user_meta( $user_id, 'frozr_enable_selling', true ) == 'yes') ? 'Enabled' : 'Disabled' ;
			switch ($args['type']) {
				case 'new_customer':
					$subject = __( 'New Customer Registration at ', 'frozr' ) . $site_name;
					$message = sprintf( frozr_email_msgs('new-customer'),$site_name, $args['uemail'], $admin_users_url, $site_name, $site_url );
					break;
				case 'new_seller':
					$subject = __( 'New Restaurant Registration at ', 'frozr' ) . $site_name;
					$message = sprintf( frozr_email_msgs('new-seller'),$site_name, $args['uemail'], $args['fname'],$args['lname'],$args['shopname'],$args['shopurl'],$args['shopaddress'],$args['shopphone'],$user_edit_url,$admin_users_url, $site_name, $site_url );
					break;
				case 'new_seller_auto':
					$subject = __( 'New Restaurant Registration at ', 'frozr' ) . $site_name;
					$message = sprintf( frozr_email_msgs('new-seller-auto'),$site_name, $args['uemail'], $args['fname'],$args['lname'],$args['shopname'],$args['shopurl'],$args['shopaddress'],$args['shopphone'],$admin_users_url, $site_name, $site_url );
					break;
				case 'to_new_seller':
					$subject = __( 'Thank you for registering at ', 'frozr' ) . $site_name;
					$message = sprintf( frozr_email_msgs('to-new-seller'),$args['shopname'],$site_name, $site_name, $site_url );
					break;
				case 'to_new_seller_auto':
					$subject = __( 'Thank you for registering at ', 'frozr' ) . $site_name;
					$message = sprintf( frozr_email_msgs('to-new-seller-auto'),$args['shopname'], $site_name, $user_dashboard, $site_name, $site_url );
					break;
				case 'privileges':
					$subject = __( 'Your Selling Privileges has Updated', 'frozr' );
					$message = sprintf( frozr_email_msgs('selling-privileges'),$args['shopname'], $user_privileges, $site_name, $site_url );
					break;
			}
		} elseif ($type == 'seller_rating') {
			$subject = __( 'Make your review on ', 'frozr' ) . $args['restname'];
			$message = sprintf( frozr_email_msgs('seller-rating'),$args['cusname'],$args['orid'], $args['ordate'], $args['restname'], $args['revlink'], $site_name, $site_url );
		} elseif ($type == 'minus_seller_balance') {
			$subject = __( 'Your', 'frozr' ) . ' ' . $site_name . ' ' . __('Balance Updated.','frozr');
			$message = sprintf( frozr_email_msgs('balance-decreased'),$args['restaurant_name'],$args['order_id'], $args['new_sts'], $args['order_amount'], $args['amount'], $args['current_balance'], $site_name, $site_url );
		}
		
		do_action('after_frozr_messages',$args,$type);
		
		wp_mail( $to, $subject, $message, $headers );
	}
}
// email messages
if (!function_exists ('frozr_email_msgs') ) {
function frozr_email_msgs( $msg ) {

do_action('before_frozr_messages_text',$msg);

switch ($msg) {
case 'with-pen':
return apply_filters('frozr_withdraw_pending_message',__( 'Hello %s,

We received your withdraw request #%s, with total amount of %s, Via %s. We will review your request as soon as possible and get back to you.

Regards!
%s
%s','frozr'));
break;
case 'with-com':
return apply_filters('frozr_withdraw_completed_message',__( 'Hello %s,

Your withdraw request #%s, with total amount of %s, Via %s, has been approved.
We will transfer this amount to your preferred destination shortly.

Regards!
%s
%s','frozr'));
break;
case 'with-trash':
return apply_filters('frozr_withdraw_rejected_message',__( 'Hello %s,

Your withdraw request #%s, with total amount of %s, Via %s, has been rejected with the following reason: %s.

Regards!
%s
%s','frozr'));
break;
case 'cont-seller-admin':
return __('
%s

------------------------------
Sent from %s
%s', 'frozr');
break;
case 'cont-seller':
return apply_filters('frozr_contact_seller_message',__('From: %s
IP: %s
User Agent: %s
------------------------------

%s

------------------------------
Sent from %s
%s', 'frozr'));
break;
case 'new-customer':
return apply_filters('frozr_new_customer_message',__('Hello %s Admin,

A New Customer has registered in your site.
User details:
Email : %s

View all users list at %s

Regards!
%s
%s', 'frozr'));
break;
case 'new-seller':
return apply_filters('frozr_admin_new_seller_message',__('Hello %s Admin,

A New Restaurant has registered in your site.
Restaurant details:
Email : %s
First Name : %s
Last Name : %s
Shop Name : %s
Shop URL : %s
Shop Address : %s
Shop Phone : %s

Activate restaurant selling privileges %s
View all users list at %s

Regards!
%s
%s', 'frozr'));
break;
case 'new-seller-auto':
return apply_filters('frozr_admin_new_auto_seller_message',__('Hello %s Admin,

A New Restaurant has registered in your site.
Restaurant details:
Email : %s
First Name : %s
Last Name : %s
Shop Name : %s
Shop URL : %s
Shop Address : %s
Shop Phone : %s

View all users list at %s

Regards!
%s
%s', 'frozr'));
break;
case 'to-new-seller':
return apply_filters('frozr_new_seller_message',__('Hello %s,

Thank you for registering at %s.
Your selling privileges will be activated shortly by one of the site admins.
We will let you know if any updates.

Regards!
%s
%s', 'frozr'));
break;
case 'to-new-seller-auto':
return apply_filters('frozr_auto_new_seller_message',__('Hello %s,

Thank you for registering at %s.
Your selling privileges has been activated. You can staring posting your items from your dashboard %s.

Regards!
%s
%s', 'frozr'));
break;
case 'selling-privileges':
return apply_filters('frozr_selliing_privileges_message',__('Hello %s,

Your selling privileges has updated to: %s

Regards!
%s
%s', 'frozr'));
break;
case 'seller-rating':
return apply_filters('frozr_seller_rating_message',__('Hello %s,

Your order number %s dated %s has been marked as completed.
Please take few moments and make a review on %s by clicking on the following link
%s

Regards!
%s
%s', 'frozr'));
break;
case 'balance-decreased':
return apply_filters('frozr_seller_balance_decreased_message',__('Hello %s,

The order id %s status has been changed from completed to %s. The order total amount is %s an amount of %s has been dropped from your balance.
Your new balance is %s. Please contact the website Admin if you might need any clarification.

Regards!
%s
%s', 'frozr'));
break;
}
do_action('after_frozr_messages_text',$msg);
}
}
//items navigation bar
if (!function_exists ('frozr_dash_nav_control') ) {
	function frozr_dash_nav_control() {
		$theme_layout = get_theme_mod('theme_layout','left');
		
		echo "<div class=\"f_control_nav\">";
		do_action('frozr_before_items_go_back_button');
		echo "<span class=\"f_go_back\"><i class=\"fs-icon-arrow-$theme_layout\"></i></span>";
		do_action('frozr_after_items_go_back_button');
		echo "</div>";
	}
}
// add item special comments to the add to cart form
if (!function_exists ('frozr_add_special_comments') ) {
	function frozr_add_special_comments() {
		global $product, $post;
		
		$product_author = apply_filters('frozr_add_special_comments_author',get_post_field( 'post_author', $product->id ),$product );
		$store_info = frozr_get_store_info( $product_author );
		$usersads = get_term_by( 'slug', $_COOKIE['frozr_user_location'], 'restaurant_addresses');
		$usersadsids = get_objects_in_term( (int) $usersads->term_id, 'restaurant_addresses');
		$userslocs = get_term_by( 'slug', $_COOKIE['frozr_user_location'], 'location');
		$userslocids = get_objects_in_term( (int) $userslocs->term_id, 'location');
		
		if (! in_array($product_author, $userslocids, true)) {
			$dev_notice = apply_filters('frozr_no_delivery_notice_text',__('The Restaurant Will not deliver to your location, please choose another option.','frozr'));
		} elseif (in_array($product_author, $usersadsids, true)) {
			$dev_notice = apply_filters('frozr_restaurant_in_neighbourhood_text',__('This Restaurant is in your neighbourhood.','frozr'));
		} else {
			$dev_notice = apply_filters('frozr_delivery_not_to_billing_address_text',__('Notice: The delivery will be sent to the Order Billing Address','frozr'));
		}
		frozr_wp_radio (array ( 'id' => 'order_l_type', 'value' => 'delivery', 'options' => array('delivery' => array(__('Delivery','frozr'), 'fs-icon-motorcycle', frozr_orders_type_close_open($post, 'delivery')), 'pickup' => array(__('Pickup','frozr'), 'fs-icon-shopping-bag', frozr_orders_type_close_open($post, 'pickup')), 'dine-in' => array(__('Dine-in','frozr'), 'fs-icon-cutlery', frozr_orders_type_close_open($post, 'dine-in')), 'curbside' => array(__('Curbside','frozr'), 'fs-icon-car', frozr_orders_type_close_open($post, 'curbside')))));
		if (in_array('dine-in', $store_info['accpet_order_type'])) {
		frozr_wp_text_input (array( 'label' => __('Number of People in the Party?','frozr'), 'placeholder' => 5, 'id' => 'order_ppl_num', 'type' => 'number'));
		} if (in_array('curbside', $store_info['accpet_order_type'])) {
		frozr_wp_textarea_input (array('label' => __('Car Information','frozr'), 'id' => 'order_car_info', 'placeholder' => __('Car Make, Model & color','frozr')));
		}
		?>
		<?php if (frozr_is_rest_open($product_author) == false) { ?>
		<div class="closed_order_notice">
		<?php do_action('frozr_before_closed_order_notice'); ?>
		<?php echo apply_filters('frozr_closed_order_notice_text',__('The Restaurant will not accept this type of orders at this time please choose another option or come back ','frozr')) . frozr_rest_status($product_author, false); ?>
		<?php do_action('frozr_after_closed_order_notice'); ?>
		</div>
		<div class="open_closed_order_notice">
		<?php do_action('frozr_before_open_closed_order_notice'); ?>
		<?php echo apply_filters('frozr_open_closed_order_notice_text',__('The Restaurant is currently closed, but will process your order when open ','frozr')) . frozr_rest_status($product_author, false); ?>
		<?php do_action('frozr_after_open_closed_order_notice'); ?>
		</div>
		<?php } ?>
		<div class="delivery_notice">
		<?php do_action('frozr_before_delivery_notice'); ?>
		<p><?php echo $dev_notice; ?></p>
		<?php do_action('frozr_after_delivery_notice'); ?>
		</div>
		<?php if (in_array($product_author, $usersadsids, true) || frozr_get_restaurant_address($product_author) ) { ?>
		<div class="lepop_rest_address">
		<?php do_action('frozr_before_restaurant_address_notice'); ?>
		<p><?php if (in_array($product_author, $usersadsids, true)) { echo apply_filters('frozr_restaurant_address_notice_text',__('This Restaurant is in your neighbourhood.','frozr')); } else {echo apply_filters('frozr_addresses_text',__('Addresses: ','frozr')) . frozr_get_restaurant_address($product_author);} ?></p>
		<?php do_action('frozr_after_restaurant_address_notice'); ?>
		</div>
		<?php }

		do_action('frozr_before_special_comments_input');

		frozr_wp_textarea_input(  array( 'id' => 'dish_special_comments', 'label' => '<a href="#" title="' . __( 'Add special comments or a person name.', 'frozr' ) . '">' . __( 'Add special comments or a person name.', 'frozr' ) . '</a>', 'placeholder' => __( 'Don\'t add items names, that have separate prices, Just add instructions for your current item like "add extra sauces or toppings" ..etc.', 'frozr' ) ) );

		do_action('frozr_after_special_comments_input');

		frozr_wp_hidden_input(array('id' => 'product_type', 'value' => $product->product_type));

	}
}
add_action('woocommerce_before_add_to_cart_button', 'frozr_add_special_comments');

//support special item comments
add_filter( 'woocommerce_add_cart_item_data', 'frozr_dish_comments', 10, 3 );
if (!function_exists ('frozr_dish_comments') ) {
	function frozr_dish_comments( $cartItemData, $productId, $variationId ) {

		if ('' != $_POST['dish_special_comments']) {
			$cartItemData['dish_comments'] = $_POST['dish_special_comments'];
		}
		if ('' != $_POST['order_l_type']) {
			$cartItemData['order_l_type'] = $_POST['order_l_type'];
		}
		if ($_POST['order_l_type'] == 'dine-in' && '' != $_POST['order_ppl_num']) {
			$cartItemData['order_ppl_num'] = $_POST['order_ppl_num'];
		}
		if ($_POST['order_l_type'] == 'curbside' && '' != $_POST['order_car_info']) {
			$cartItemData['order_car_info'] = $_POST['order_car_info'];
		}
		return $cartItemData;
	}
}
//support comments in sessions
add_filter( 'woocommerce_get_cart_item_from_session', 'frozr_dish_comments_session', 10, 3 );
if (!function_exists ('frozr_dish_comments_session') ) {
	function frozr_dish_comments_session( $cartItemData, $cartItemSessionData, $cartItemKey ) {
		
		if ( isset( $cartItemSessionData['dish_comments'] ) ) {
			$cartItemData['dish_comments'] = $cartItemSessionData['dish_comments'];
		}
		if ( isset( $cartItemSessionData['order_l_type'] ) ) {
			$cartItemData['order_l_type'] = $cartItemSessionData['order_l_type'];
		}
		if ( isset( $cartItemSessionData['order_ppl_num'] ) ) {
			$cartItemData['order_ppl_num'] = $cartItemSessionData['order_ppl_num'];
		}
		if ( isset( $cartItemSessionData['order_car_info'] ) ) {
			$cartItemData['order_car_info'] = $cartItemSessionData['order_car_info'];
		}

		return $cartItemData;
	}
}

// show item comments on checkout
add_filter( 'woocommerce_get_item_data', 'frozr_dish_comments_checkout', 10, 2 );
if (!function_exists ('frozr_dish_comments_checkout') ) {
	function frozr_dish_comments_checkout( $data, $cartItem ) {

		if ( isset( $cartItem['dish_comments'] ) ) {
			$data[] = apply_filters('frozr_dish_comments_args',array(
				'name' => 'Comments',
				'value' => $cartItem['dish_comments']
			));
		}
		if ( isset( $cartItem['order_l_type'] ) ) {
			$data[] = apply_filters('frozr_order_type_args',array(
				'name' => 'Order Type',
				'value' => $cartItem['order_l_type']
			));
		}
		if ( isset( $cartItem['order_ppl_num'] ) ) {
			$data[] = apply_filters('frozr_order_people_number_args',array(
				'name' => 'People In',
				'value' => $cartItem['order_ppl_num']
			));
		}
		if ( isset( $cartItem['order_car_info'] ) ) {
			$data[] = apply_filters('frozr_order_car_info_args',array(
				'name' => 'Car Info',
				'value' => $cartItem['order_car_info']
			));
		}

		return $data;
	}
}

//Save special comments as order meta
add_action( 'woocommerce_add_order_item_meta', 'frozr_save_dish_special_comments', 10, 3 );
function frozr_save_dish_special_comments( $itemId, $values, $key ) {
	
	do_action('before_fave_frozr_dish_comments');
   
   if ( isset( $values['dish_comments'] ) ) {
        wc_add_order_item_meta( $itemId, 'Item Comments', $values['dish_comments'] );
    }
	if ( isset( $values['order_l_type'] ) ) {
		wc_add_order_item_meta( $itemId, 'Order Type', $values['order_l_type'] );
	}
	if ( isset( $values['order_ppl_num'] ) ) {
		wc_add_order_item_meta( $itemId, 'People In', $values['order_ppl_num'] );
	}
	if ( isset( $values['order_car_info'] ) ) {
		wc_add_order_item_meta( $itemId, 'Car Info', $values['order_car_info'] );
	}
	
	do_action('after_save_frozr_dish_comments');
	
}
// classes for closed/open order type
function frozr_orders_type_close_open($post, $nx) {
	global $post;
	$product_author = apply_filters('frozr_orders_type_close_open_author',get_post_field( 'post_author', $post->id ), $post, $nx);
	$store_info = frozr_get_store_info( $product_author );
	$rest = '';
	
	if (frozr_is_rest_open($product_author) == false && in_array($nx, $store_info['accpet_order_type_cl'])) {
		$rest = 'show_open_closed_order_notice';
	} elseif(frozr_is_rest_open($product_author) == false) {
		$rest = 'show_closed_order_notice';
	}
	
	return $rest;
}
//count user object
function frozr_count_user_object($sts, $type ="", $nx =""){
	$user_id = (!empty($nx)) ? $nx : get_current_user_id();
	$get_curnt_user = (is_super_admin() && empty($nx)) ? '' : $user_id;
	$args = apply_filters('frozr_count_user_object_args',array(
		'posts_per_page'	=> -1,
		'post_type'			=> $type,
		'orderby'			=> 'date',
		'author'			=> $get_curnt_user,
		'order'				=> 'desc',
		'post_status'		=> array($sts),
		'fields'			=> 'ID',
	));

	$coupon_query = new WP_Query( $args );
	return $coupon_query->found_posts;
}

//redirect if not admin
function frozr_redirect_if_not_admin() {

	if (!is_super_admin()) {
		wp_redirect( home_url() );
	}
}
//user search type options
function frozr_user_type_option() {
	$food_dev_text = apply_filters('frozr_food_delivery_text',__('Food Delivery','frozr'));
	$rest_text = apply_filters('frozr_restaurants_text',__('Restaurants','frozr'));
	?>
	<h2><i class="fs-icon-map-marker"></i>&nbsp;<span><?php echo apply_filters('frozr_set_your_location_text',__('Set Your Location.','frozr')); ?></span></h2>
	<div class="adv_src_checkbox">
		<?php do_action('frozr_before_user_seach_type_option'); ?>
		<a class="adv_loc_src_checkbox<?php if (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "delivery") { echo ' active'; } ?>" data-src="delivery" title="<?php echo $food_dev_text; ?>" href="#" ><span><i class="fs-icon-motorcycle"></i>&nbsp;<?php echo $food_dev_text; ?></span></a>
		<a class="adv_loc_src_checkbox<?php if (isset($_COOKIE['frozr_user_src_type']) && $_COOKIE['frozr_user_src_type'] == "restaurants") { echo ' active'; } ?>" data-src="restaurants" title="<?php echo $rest_text; ?>" href="#" ><span><i class="fs-icon-cutlery"></i>&nbsp;<?php echo $rest_text; ?></span></a>
		<?php do_action('frozr_after_user_seach_type_option'); ?>
	</div>
	<?php
}
//cuisine search body
function frozr_cuisine_search_body() {
	do_action('frozr_before_cusine_search_body'); ?>
	<ul class="cussearch_ul" data-role="listview" data-filter="true" data-filter-reveal="true" data-filter-placeholder="<?php echo apply_filters('frozr_cusine_filter_input_placeholder',__('e.g. Indian, Italian, Chinese ...','frozr')); ?>" data-inset="true">
	<?php
	//get all cuisines
	$getalltyps= apply_filters('frozr_cusine_search_terms',get_terms( 'cuisine', 'hide_empty=0' ));
	$usrce = (isset($_COOKIE['frozr_user_location']) && $_COOKIE['frozr_user_location'] == 'delivery') ? 'delivery' : 'restaurant';
	if ( ! empty( $getalltyps ) && ! is_wp_error( $getalltyps ) ){
		foreach ( $getalltyps as $term ) {
		if (isset($_COOKIE['frozr_user_location']) && $usrce == 'delivery') {
			$utresulttwo = get_objects_in_term( (int) $term->term_id, 'cuisine');
			$utresultone = 0;
			foreach ($utresulttwo as $usr) {
				$locs = wp_get_object_terms( $usr, 'location', array('fields' => 'slugs'));
				if (in_array($_COOKIE['frozr_user_location'], $locs, true)) {
					$utresultone++;
				}
			}
			if ($utresultone != 0) {
				echo "<li><a href=\"". get_term_link( $term ) ."\">" . $term->name . "</a></li>";
			}
		} else {
			echo "<li><a href=\"". get_term_link( $term ) ."\">" . $term->name . "</a></li>";
		}
	} } ?>
	</ul>
	<?php do_action('frozr_after_cusine_search_body');
}
//ingredient search body
function frozr_ingredient_search_body() { ?>
	<?php do_action('frozr_before_ingredent_search_body'); ?>
	<ul class="ingsearch_ul" data-role="listview" data-filter="true" data-filter-reveal="true" data-filter-placeholder="<?php echo apply_filters('frozr_ingredient_search_input_placeholder',__('e.g. Lamp, Chicken, Onion ...','frozr')); ?>" data-inset="true">
	<?php
	//get_all_ingredient
	$ingres = apply_filters('frozr_ingredient_search_terms',get_terms( 'ingredient', 'hide_empty=0' )); 
	if ( ! empty( $ingres ) && ! is_wp_error( $ingres ) ){
	foreach ( $ingres as $term ) {
		if (isset($_COOKIE['frozr_user_location'])) {
		$utermtwo = get_term_by( 'slug', $_COOKIE['frozr_user_location'], 'location');
		$utresulttwo = get_objects_in_term( (int) $utermtwo->term_id, 'location');
		
		$args = array('author__in' => array_unique($utresulttwo), 'tax_query' => array(array('taxonomy' => 'ingredient','field' => 'slug','terms' => $term,)));
		$ing_query = new WP_Query( $args );
		if ( $ing_query->have_posts() ) {
			echo "<li><a href=\"". get_term_link( $term ) ."\">" . $term->name . "</a></li>";
		}
		} else {
		echo "<li><a href=\"". get_term_link( $term ) ."\">" . $term->name . "</a></li>";
		}
	}
	} ?>
	</ul>
	<?php do_action('frozr_after_ingredent_search_body'); ?>
<?php
}
//Address location search body
function frozr_address_location_search_body() { ?>
	<?php do_action('frozr_before_location_search_body'); ?>
	<ul class="cussearch_ul" data-role="listview" data-filter="true" data-filter-reveal="true" data-filter-placeholder="<?php echo apply_filters('frozr_address_location_search_input_placeholder',__('e.g. Road One, Road Two, Road Three ...','frozr')); ?>" data-inset="true">
	<?php
	$getallocs = apply_filters('frozr_ingredient_search_terms',get_terms( 'restaurant_addresses', 'hide_empty=0' ));
	//get all locations
	if ( ! empty( $getallocs ) && ! is_wp_error( $getallocs ) ){
		 foreach ( $getallocs as $term ) {
		   echo "<li><a href=\"". get_term_link( $term ) ."\">" . $term->name . "</a></li>";
		}
	} ?>
	</ul>
	<?php do_action('frozr_after_location_search_body'); ?>
<?php
}
//Restaurants search body
function frozr_resturant_search_body() {
	$args = apply_filters('frozr_restaurant_search_body_args',array(
		'role'			=> 'seller',
		'meta_key'		=> 'frozr_enable_selling',
		'meta_value'	=> 'yes',
		'order'			=> 'DESC',
		'fields'		=> 'ID',
	));
	$sellers_query = new WP_User_Query( apply_filters( 'frozr_home_restaurant_search_query', $args ) );		
	$sellers_results = $sellers_query->get_results();
	
	do_action('frozr_before_restaurant_search_body'); ?>
	
	<ul class="adlocsearch_ul" data-role="listview" data-filter="true" data-filter-reveal="true" data-filter-placeholder="<?php echo apply_filters('frozr_restaurant_name_text', __('Restaurant Name?','frozr')); ?>" data-inset="true">
	<?php
	foreach ($sellers_results as $seller_result) {
		$user_store = frozr_get_store_info($seller_result);
		$gravatar_url = !empty($user_store['gravatar']) ? wp_get_attachment_url( $user_store['gravatar'] ) : '';
		echo '<li><a title="'. $user_store['store_name'] .'" href="'. frozr_get_store_url($seller_result) .'"><img src="'. $gravatar_url .'" alt="'. $user_store['store_name'] .'" height="30" width="30"></img><h3>'. $user_store['store_name'] .'</h3></a></li>';
	} ?>
	</ul>
	
	<?php do_action('frozr_after_restaurant_search_body');
}
//prase the sortable value
function frozr_sortable_value_filter($xn) {
	parse_str($xn, $tstary);
	array_filter($tstary['srt']);
	array_unique($tstary['srt']);
	array_map('wc_clean', $tstary['srt']);
	
	return implode(',',$tstary['srt']);
}
//Helper function to give the closest value in array
function frozr_get_closest_arry_val($array, $value) {
	$size = count($array);
	if ($size > 0) {
		$diff = abs($array[0] - $value);
		$ret = $array[0];
		for ($i = 1; $i < $size; $i++) {
			$temp = abs($array[$i] - $value);
			if ($temp < $diff) {
				$diff = $temp;
				$ret = $array[$i];
			}
		}
		return $ret;
	} else {
		return false;
	}
}
//fix for the Indian currency symbol
add_filter( 'woocommerce_currencies', 'frozr_add_inr_currency' );
add_filter( 'woocommerce_currency_symbol', 'frozr_add_inr_currency_symbol' );

function frozr_add_inr_currency( $currencies ) {
	$currencies['INR'] = 'INR';
	return $currencies;
}

function frozr_add_inr_currency_symbol( $symbol ) {
	$currency = get_option( 'woocommerce_currency' );
	switch( $currency ) {
		case 'INR': $symbol = 'Rs.'; break;
	}
	return $symbol;
}

add_action( 'admin_notices', 'frozr_fee_admin_error_notice');

// Fee/Commision unset admin notice
function frozr_fee_admin_error_notice() {
	$frozr_option = get_option( 'fro_settings' );
	$fees_options = (! empty( $frozr_option['fro_lazy_fees']) ) ? $frozr_option['fro_lazy_fees'] : '';

	if ( $_GET['tab'] != 'fees' && '' == $fees_options) {
		$class = "error notice is-dismissible";
		$message = __('You have not yet set a fee/commission on your sellers sales. Your sellers will get %100 of their sales. Set your fee/commission now from the','frozr'). ' <a href="'.admin_url( 'admin.php?page=lazyeater&tab=fees' ).'" title="'.__('Fees/Commission Settings','frozr').'">'.__('Fees/Commission Settings','frozr').'</a>';
		echo sprintf('<div class="%s"> <p>%s</p></div>',$class, $message);
	}
}