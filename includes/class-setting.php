<?php
/**
 * The setting service.
 *
 * @package auto-rss-price-updater
 * @author  Khoa Nguyen
 * @since   1.0.0
 */

?>
<?php
namespace Arpu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setting class
 */
class Setting {
	use Singleton;

	/**
	 * Whether the auto Rss price updater or not
	 *
	 * @since   1.0.0
	 * @var string $enabled yes|no
	 */
	private $enabled = 'yes';

	/**
	 * The gold product.
	 *
	 * @since   1.0.0
	 * @var Object $gold_product
	 */
	private $gold_product = null;

	/**
	 * The silver product.
	 *
	 * @since   1.0.0
	 * @var Object $silver_product
	 */
	private $silver_product = null;

	/**
	 * The platinum product.
	 *
	 * @since   1.0.0
	 * @var Object $platinum_product
	 */
	private $platinum_product = null;

	/**
	 * The palladium product.
	 *
	 * @since   1.0.0
	 * @var Object $palladium_product
	 */
	private $palladium_product = null;

	/**
	 * Contructor
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Init.
	 *
	 * @since    1.0.0
	 */
	private function init() {
		$this->enabled           = woocommerce_settings_get_option( 'wc_arpu_enabled', 'yes' );
		$this->gold_product      = self::get_product_by_key( 'wc_arpu_gold_product' );
		$this->silver_product    = self::get_product_by_key( 'wc_arpu_silver_product' );
		$this->platinum_product  = self::get_product_by_key( 'wc_arpu_platinum_product' );
		$this->palladium_product = self::get_product_by_key( 'wc_arpu_palladium_product' );
	}

	/**
	 * Reload.
	 *
	 * @since    1.0.0
	 */
	public static function reload() {
		self::instance()->init();
	}

	/**
	 * Get the setting product by key
	 *
	 * @param string $product_key The product option name.
	 * @return Object $product
	 */
	public static function get_product_by_key( $product_key ) {
		$product_id = woocommerce_settings_get_option( $product_key, '' );
		if ( empty( $product_id ) ) {
			return null;
		} else {
			return wc_get_product( $product_id );
		}
	}

	/**
	 * Whether the auto uploader is enabled or not.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return 'yes' === self::instance()->enabled;
	}

	/**
	 * Get the gold product.
	 *
	 * @return Object
	 */
	public static function get_gold_product() {
		return self::instance()->gold_product;
	}

	/**
	 * Get the silver product.
	 *
	 * @return Object
	 */
	public static function get_silver_product() {
		return self::instance()->silver_product;
	}

	/**
	 * Get the platinum product.
	 *
	 * @return Object
	 */
	public static function get_platinum_product() {
		return self::instance()->platinum_product;
	}

	/**
	 * Get the palladium product.
	 *
	 * @return Object
	 */
	public static function get_palladium_product() {
		return self::instance()->palladium_product;
	}

	/**
	 * Whether the setting is valid or not.
	 *
	 * @return bool true|false
	 */
	public static function valid() {
		return self::valid_product( 'gold_product' ) || self::valid_product( 'silver_product' )
			|| self::valid_product( 'platinum_product' ) || self::valid_product( 'palladium_product' );
	}

	/**
	 * Whether the product is valid or not.
	 *
	 * @param string $name The name.
	 * @return bool
	 */
	public static function valid_product( $name ) {
		$product = self::get_product( $name );
		return null !== $product;
	}

	/**
	 * Get the product by key.
	 *
	 * @param string $name The name.
	 * @return Object
	 */
	public static function get_product( $name ) {
		switch ( $name ) {
			case 'gold_product':
				return self::instance()->gold_product;
			case 'silver_product':
				return self::instance()->silver_product;
			case 'platinum_product':
				return self::instance()->platinum_product;
			case 'palladium_product':
				return self::instance()->palladium_product;
		}
	}

	/**
	 * Delete settings.
	 */
	public static function delete() {
		delete_option( 'wc_arpu_enabled' );
		delete_option( 'wc_arpu_gold_product' );
		delete_option( 'wc_arpu_silver_product' );
		delete_option( 'wc_arpu_platinum_product' );
		delete_option( 'wc_arpu_palladium_product' );
	}
}
