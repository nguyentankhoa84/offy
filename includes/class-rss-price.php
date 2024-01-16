<?php
/**
 * The Rss price service.
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
 * Rss_Price class
 */
class Rss_Price {
	use Singleton;

	/**
	 * The Rss url.
	 *
	 * @since   1.0.0
	 * @var string $rss_url yes|no
	 */
	private $rss_url = 'https://www.cookson-clal.com/mp/rss_mpfr_cdl.jsp';

	/**
	 * The regular expression to retrieve the first price.
	 *
	 * @since   1.0.0
	 * @var string FIRST_PRICE_EXPR
	 */
	const FIRST_PRICE_EXPR = '/(1er fixing : ([0-9]*,[0-9]*) €\/ kg)/';

	/**
	 * The regular expression to retrieve the second price.
	 *
	 * @since   1.0.0
	 * @var string SECOND_PRICE_EXPR
	 */
	const SECOND_PRICE_EXPR = '/(2è fixing : ([0-9]*,[0-9]*) €\/ kg)/';

	/**
	 * The Rss content.
	 *
	 * @since   1.0.0
	 * @var string $result
	 */
	private $rss_result = '';

	/**
	 * Whether it downloads the RSS successfull or not.
	 *
	 * @since   1.0.0
	 * @var bool $download
	 */
	private $downloaded = false;

	/**
	 * The Rss gold product price.
	 *
	 * @since   1.0.0
	 * @var float $gold_product_price
	 */
	private $gold_product_price = null;

	/**
	 * The Rss silver product price.
	 *
	 * @since   1.0.0
	 * @var float $silver_product_price
	 */
	private $silver_product_price = null;

	/**
	 * The Rss platinum product price.
	 *
	 * @since   1.0.0
	 * @var float $palladium_product_price
	 */
	private $platinum_product_price = null;

	/**
	 * The Rss palladium product price.
	 *
	 * @since   1.0.0
	 * @var float $palladium_product_price
	 */
	private $palladium_product_price = null;

	/**
	 * Contructor
	 */
	private function __construct() {}

	/**
	 * Update Rss price.
	 */
	public static function update() {
		self::instance()->download()->parse()->save();
	}

	/**
	 * Download Rss content.
	 */
	public function download() {
		$this->rss_result = wp_remote_get( $this->rss_url );
		$this->downloaded = 200 === wp_remote_retrieve_response_code( $this->rss_result );

		return $this;
	}

	/**
	 * Parse the Rss content.
	 *
	 * @return Object Itself
	 */
	public function parse() {
		if ( $this->downloaded ) {
			$body = wp_remote_retrieve_body( $this->rss_result );
			$rss  = simplexml_load_string( $body );

			$has_item = ! empty( $rss->channel->item );
			if ( $has_item ) {
				foreach ( $rss->channel->item as $item ) {
					$this->retrieve_price( $item );
				}
			}
		}

		return $this;
	}

	/**
	 * Save Rss price into database.
	 *
	 * @return Object Itself
	 */
	public function save() {
		if ( $this->downloaded ) {
			$updatable = null !== $this->gold_product_price && Setting::valid_product( 'gold_product' );
			if ( $updatable ) {
				$gold_product = Setting::get_gold_product();
				$gold_product->set_sale_price( $this->gold_product_price );
				$gold_product->save();
			}

			$updatable = null !== $this->silver_product_price && Setting::valid_product( 'silver_product' );
			if ( $updatable ) {
				$silver_product = Setting::get_silver_product();
				$silver_product->set_sale_price( $this->silver_product_price );
				$silver_product->save();
			}

			$updatable = null !== $this->platinum_product_price && Setting::valid_product( 'platinum_product' );
			if ( $updatable ) {
				$platinum_product = Setting::get_platinum_product();
				$platinum_product->set_sale_price( $this->platinum_product_price );
				$platinum_product->save();
			}

			$updatable = null !== $this->palladium_product_price && Setting::valid_product( 'palladium_product' );
			if ( $updatable ) {
				$palladium_product = Setting::get_palladium_product();
				$palladium_product->set_sale_price( $this->palladium_product_price );
				$palladium_product->save();
			}
		}

		return $this;
	}

	/**
	 * Retrieve the metal param from the xml rss item.
	 *
	 * @param Object $item The item.
	 */
	public function retrieve_price( $item ) {
		$link  = isset( $item->link ) ? $item->link : '';
		$title = isset( $item->title ) ? $item->title : '';

		$metal = $this->retrieve_metal( $link );
		switch ( $metal ) {
			case 'OR':
				$this->gold_product_price = $this->retrieve_last_price( $title );
				break;
			case 'ARGENT':
				$this->silver_product_price = $this->retrieve_last_price( $title );
				break;
			case 'PLATINE':
				$this->platinum_product_price = $this->retrieve_last_price( $title );
				break;
			case 'PALLADIUM':
				$this->palladium_product_price = $this->retrieve_last_price( $title );
				break;
			default:
		}
	}

	/**
	 * Retrieve the last price from Rss.
	 *
	 * @param string $title The title of the Rss item.
	 * @return float $price
	 */
	public function retrieve_last_price( $title ) {
		$output = array();

		preg_match( self::SECOND_PRICE_EXPR, $title, $output );
		$price = empty( $output[2] ) ? null : $output[2];

		if ( empty( $price ) ) {
			preg_match( self::FIRST_PRICE_EXPR, $title, $output );
			$price = empty( $output[2] ) ? null : $output[2];
		}

		$price = str_replace( ',', '.', $price );
		$price = is_numeric( $price ) ? floatval( $price ) : null;
		return $price;
	}

	/**
	 * Retrieve the metal param from the link.
	 *
	 * @param string $link The link.
	 * @return array $metal The metal param.
	 */
	public function retrieve_metal( $link ) {
		$query        = array();
		$string_query = wp_parse_url( $link, PHP_URL_QUERY );
		$string_query = explode( '&', $string_query );
		foreach ( $string_query as $string_query_param ) {
			$pair = explode( '=', $string_query_param );
			if ( isset( $pair[0] ) && isset( $pair[1] ) ) {
				$query[ $pair[0] ] = $pair[1];
			}
		}

		$metal = isset( $query['metal'] ) ? strtoupper( $query['metal'] ) : '';

		return $metal;
	}
}
