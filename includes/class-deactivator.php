<?php
/**
 * The deactivator handler.
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
 * Deactivator class
 */
class Deactivator {
	/**
	 * Deactivate.
	 *
	 * @since   1.0.0
	 */
	public static function deactivate() {
		if ( class_exists( 'Arpu\Cron' ) ) {
			Cron::remove();
		}
	}
}
