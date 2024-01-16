<?php
/**
 * Singleton trail.
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
 * Singleton trail
 */
trait Singleton {
	/**
	 * The instance of the class
	 *
	 * @since   1.0.0
	 * @var Object $instance
	 */
	private static $instance;

	/**
	 * Create the instance of the class
	 *
	 * @since    1.0.0
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
