<?php
/**
 * The uninstaller.
 *
 * @package auto-rss-price-updater
 * @author  Khoa Nguyen
 * @since   1.0.0
 */

namespace Arpu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uninstaller class
 */
class Uninstaller {
	/**
	 * Uninstall.
	 *
	 * @since   1.0.0
	 */
	public static function uninstall() {
		require_once ARPU_DIR . '/includes/class-setting.php';
		require_once ARPU_DIR . '/includes/class-cron.php';

		Setting::delete();
		Cron::remove();
	}
}
