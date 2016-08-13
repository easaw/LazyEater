<?php
/**
 * WC Dependency Checker
 *
 * Checks if WooCommerce is enabled
 */
class Frozr_WC_Dependencies {

	private static $active_plugins;

	public static function init() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	public static function frozr_woocommerce_active_check() {

		if ( ! self::$active_plugins ) self::init();

		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
	}

}

/**
 * WC Detection
 */
if ( ! function_exists( 'frozr_is_woocommerce_active' ) ) {
	function frozr_is_woocommerce_active() {
		return Frozr_WC_Dependencies::frozr_woocommerce_active_check();
	}
}
