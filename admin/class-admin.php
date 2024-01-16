<?php
/**
 * The main admin handler.
 *
 * @package auto-rss-price-updater
 * @author  Khoa Nguyen
 * @since   1.0.0
 */

?>
<?php
namespace Arpu\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Arpu\Singleton;

/**
 * Admin class
 */
class Admin {
	use Singleton;

	/**
	 * Start.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public static function init() {
		if ( is_admin() ) {
			self::instance()->load_dependencies()->hooks();
		}
	}

	/**
	 * Hook.
	 *
	 * @since 1.0.0
	 * @return object $this
	 */
	private function hooks() {
		Setting_Admin::init();

		return $this;
	}

	/**
	 * Load the dependencies.
	 *
	 * @since    1.0.0
	 * @return Object Itself
	 */
	private function load_dependencies() {
		require_once 'partials/class-setting-admin.php';

		return $this;
	}
}
