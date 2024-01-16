<?php
/**
 * The admin setting controller.
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
use Arpu\Setting;
use Arpu\Rss_Price;
use Arpu\Cron;

/**
 * Setting_Admin class
 */
class Setting_Admin {
	use Singleton;

	/**
	 * Contructor
	 */
	private function __construct() {}

	/**
	 * Init.
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
	 * @return Object Itself
	 */
	private function hooks() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'tab' ), 99 );
		add_action( 'woocommerce_settings_tabs_auto-rss-price-updater', array( $this, 'render' ) );
		add_action( 'woocommerce_update_options_auto-rss-price-updater', array( $this, 'submit' ) );
		add_action( 'woocommerce_admin_field_update_rss_submit', array( $this, 'render_update_rss_submit' ) );

		return $this;
	}

	/**
	 * Submit setting form.
	 */
	public function submit() {
		if ( $this->is_update_rss_manually_btn() ) {
			Rss_Price::update();
		} else {
			woocommerce_update_options( $this->form() );
			Setting::reload();

			$is_auto_disabled = ! Setting::is_enabled();
			if ( $is_auto_disabled ) {
				Cron::remove();
			}
		}
	}

	/**
	 * Whether the submit is from the update rss button or not.
	 *
	 * @return bool true|false
	 */
	private function is_update_rss_manually_btn() {
		$update_rss_button = filter_input( INPUT_POST, 'save' );
		return ! empty( $update_rss_button ) && __( 'Update RSS price manually', 'arpu' ) === $update_rss_button;
	}

	/**
	 * Render the setting form.
	 */
	public function render() {
		woocommerce_admin_fields( $this->form() );
	}

	/**
	 * Prepare the setting form.
	 */
	protected function form() {
		$gold_product = Setting::get_gold_product();

		$products = wc_get_products( array() );
		$options  = array(
			'' => __( '-- Select --', 'arpu' ),
		);

		foreach ( $products as $product ) {
			$name = $product->get_name();
			$id   = $product->get_id();

			$options[ $product->id ] = "$name (ID: {$id})";
		}

		$settings = array(
			'label_title'       => array(
				'name' => __( 'Auto RSS price updater', 'arpu' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wc_arpu_label',
			),
			'enabled'           => array(
				'name'    => __( 'Enable the auto updater', 'arpu' ),
				'type'    => 'checkbox',
				'default' => 'yes',
				'id'      => 'wc_arpu_enabled',
			),
			'gold_product'      => array(
				'name'    => __( 'Gold product', 'arpu' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => __( 'Gold product', 'arpu' ),
				'id'      => 'wc_arpu_gold_product',
			),
			'silver_product'    => array(
				'name'    => __( 'Silver product', 'arpu' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => __( 'Silver product', 'arpu' ),
				'id'      => 'wc_arpu_silver_product',
			),
			'platinum_product'  => array(
				'name'    => __( 'Platinum product', 'arpu' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => __( 'Platinum product', 'arpu' ),
				'id'      => 'wc_arpu_platinum_product',
			),
			'palladium_product' => array(
				'name'    => __( 'Palladium product', 'arpu' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => __( 'Palladium product', 'arpu' ),
				'id'      => 'wc_arpu_palladium_product',
			),
		);

		if ( Setting::valid() ) {
			$settings['update_rss_submit'] = array(
				'type' => 'update_rss_submit',
				'id'   => 'wc_arpu_update_rss',
			);
		}

		$settings['section_end'] = array(
			'type' => 'sectionend',
			'id'   => 'wc_settings_tab_arpu_settings_end',
		);

		return apply_filters( 'wc_settings_tab_arpu_settings', $settings );
	}

	/**
	 * Implementation of hook "woocommerce_settings_tabs_array".
	 * Add Woocommerce setting tab.
	 *
	 * @since 1.0.0
	 * @param array $settings_tabs The settings tabs.
	 * @return array  $settings_tabs
	 */
	public function tab( $settings_tabs ) {
		$settings_tabs['auto-rss-price-updater'] = __( 'Auto Rss price updater', 'arpu' );

		return $settings_tabs;
	}

	/**
	 * Implementation of hook "woocommerce_admin_field_{$type}".
	 * Render custom update rss submit.
	 *
	 * @since 1.0.0
	 * @param array $value The field.
	 */
	public function render_update_rss_submit( $value ) {
		?>
		<tr valign="top">
			<th></th>	
			<td class="forminp">
				<input type="submit" name="save" value="<?php echo esc_attr_e( 'Update RSS price manually', 'arpu' ); ?>" class="button"/>     
			</td>
		</tr>
		<?php
	}
}
