<?php
/**
 * The cron job handler.
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

use Arpu\Singleton;

/**
 * Cron class
 */
class Cron {
	use Singleton;

	/**
	 * Start.
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		self::instance()->hooks();
	}

	/**
	 * Hook.
	 *
	 * @since 1.0.0
	 * @return object $this
	 */
	private function hooks() {
		add_action( 'arpu_update_rss_price', array( $this, 'add_job' ) );
		add_filter( 'cron_schedules', array( $this, 'add_schedule' ) );

		if ( ! wp_next_scheduled( 'arpu_update_rss_price' ) && Setting::is_enabled() ) {
			wp_schedule_event( time(), '15min', 'arpu_update_rss_price' );
		}

		return $this;
	}

	/**
	 * Implementation of the hook "cron_schedules"
	 */
	public function add_schedule() {
		return array(
			'15min' => array(
				'interval' => 15 * 60,
				'display'  => 'Arpu - Once every 15 minutes',
			),
		);
	}

	/**
	 * Implementation of the hook "arpu_update_rss_price"
	 */
	public function add_job() {
		if ( Setting::is_enabled() && Setting::valid() ) {
			Rss_Price::update();
		}
	}

	/**
	 * Remove the cron event.
	 */
	public static function remove() {
		$timestamp = wp_next_scheduled( 'arpu_update_rss_price' );
		wp_unschedule_event( $timestamp, 'arpu_update_rss_price' );
	}
}
