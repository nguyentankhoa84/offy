<?php
/**
 * The main plugin handler.
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

use Arpu\Admin\Admin;

/**
 * Arpu class
 */
class Arpu {
	use Singleton;

	/**
	 * Start
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		self::instance()->load_dependencies()->hooks();
	}

	/**
	 * Hook.
	 *
	 * @since 1.0.0
	 * @return object Itself
	 */
	private function hooks() {
		Admin::init();
		Cron::init();

		return $this;
	}

	/**
	 * Whether the Woocommerce plugin is enabled or not.
	 *
	 * @return bool true
	 */
	public static function is_not_enabled_woocommcere() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		return ! is_plugin_active( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Load the dependencies.
	 *
	 * @since    1.0.0
	 * @return object Itself
	 */
	private function load_dependencies() {
		// Woocommerce reference.
		require_once WP_PLUGIN_DIR . '/woocommerce/includes/admin/wc-admin-functions.php';
		require_once WP_PLUGIN_DIR . '/woocommerce/includes/wc-product-functions.php';

		// Service class.
		require_once ARPU_DIR . '/includes/class-setting.php';
		require_once ARPU_DIR . '/includes/class-rss-price.php';
		require_once ARPU_DIR . '/includes/class-cron.php';

		// Admin side.
		require_once ARPU_DIR . '/admin/class-admin.php';

		return $this;
	}

	/**
	 * Implementation of the hook "admin_notices"
	 */
	public static function noitice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Please activate/install Woocommerce for this plugin can work properly', 'arpu' ); ?></p>
		</div>
		<?php
	}
}
