<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress;

use WC_Order;
use Exception;
use WC_Product;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Globals\SalesEncoder;

/**
 * Class DataHelper
 *
 * @package MergeInc\Sort\WordPress
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class DataHelper {

	/**
	 * @var SalesEncoder
	 */
	private SalesEncoder $salesEncoder;

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @var array
	 */
	private array $cache = array(
		'orders'   => array(),
		'products' => array(),
	);

	/**
	 * @param SalesEncoder $salesEncoder
	 * @param Mapper       $mapper
	 */
	public function __construct( SalesEncoder $salesEncoder, Mapper $mapper ) {
		$this->salesEncoder = $salesEncoder;
		$this->mapper       = $mapper;
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function isOrderRecorded( int $id ): bool {
		$order = $this->getOrderById( $id );
		if ( ! $order ) {
			return false;
		}

		return $order->get_meta( Constants::ORDER_RECORDED_META_KEY ) === 'yes';
	}

	/**
	 * @param int $id
	 * @return WC_Order|null
	 */
	public function getOrderById( int $id ): ?WC_Order {
		if ( ! $order = ( $this->cache['orders'][ $id ] ?? false ) ) {
			$order = wc_get_order( $id );
			if ( ! $order ) {
				return null;
			}
			$this->cache['orders']['id'] = $order;
		}

		return $order;
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setOrderRecorded( int $id ): void {
		if ( $order = $this->getOrderById( $id ) ) {
			$order->update_meta_data( Constants::ORDER_RECORDED_META_KEY, 'yes' );
			$order->save();
		}
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function deleteOrderRecorded( int $id ): void {
		if ( $order = $this->getOrderById( $id ) ) {
			$order->delete_meta_data( Constants::ORDER_RECORDED_META_KEY );
			$order->save();
		}
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public function getProductSales( int $id ): array {
		if ( $product = $this->getProductById( $id ) ) {
			return $this->salesEncoder->decode( (string) $product->get_meta( Constants::PRODUCT_SALES_META_KEY ) );
		}

		return array();
	}

	/**
	 * @param int $id
	 * @return WC_Order|null
	 */
	public function getProductById( int $id ): ?WC_Product {
		if ( ! $product = ( $this->cache['products'][ $id ] ?? false ) ) {
			$product = wc_get_product( $id );
			if ( ! $product ) {
				return null;
			}

			$this->cache['products']['id'] = $product;
		}

		return $product;
	}

	/**
	 * @param int   $id
	 * @param array $sales
	 * @return void
	 */
	public function setProductSales( int $id, array $sales ): void {
		if ( $product = $this->getProductById( $id ) ) {
			$product->update_meta_data( Constants::PRODUCT_SALES_META_KEY, $this->salesEncoder->encode( $sales ) );
			$product->save();
		}
	}

	/**
	 * @param int    $id
	 * @param string $column
	 * @return int|null
	 * @throws Exception
	 */
	public function getProductIntervalSalesByColumn( int $id, string $column ): ?int {
		if ( $product = $this->getProductById( $id ) ) {
			$intervalSales = $product->get_meta( $this->mapper->getBy( Mapper::COLUMN, $column, Mapper::META_KEY ) );

			return is_numeric( $intervalSales ) ? (int) $intervalSales : null;
		}

		return null;
	}

	/**
	 * @param int    $id
	 * @param string $column
	 * @param int    $sales
	 * @return void
	 */
	public function setProductIntervalSalesByColumn( int $id, string $column, int $sales ): void {
		if ( $product = $this->getProductById( $id ) ) {
			$product->update_meta_data( $column, $sales );
			$product->save();
		}
	}

	/**
	 * @return bool
	 */
	public function isActivated(): bool {
		return get_option( Constants::SETTINGS_FIELDS_ACTIVATED, 'no' ) === 'yes';
	}

	/**
	 * @return bool
	 */
	public function isDefault(): bool {
		return get_option( Constants::SETTINGS_FIELDS_DEFAULT, 'no' ) === 'yes';
	}

	/**
	 * @return string
	 */
	public function getTrendingLabel(): string {
		return $this->isFreemiumActivated() ?
			( get_option( Constants::SETTINGS_FIELD_TRENDING_LABEL ) ?: Constants::TRENDING_DEFAULT_LABEL_NAME ) :
			Constants::TRENDING_DEFAULT_LABEL_NAME;
	}

	/**
	 * @return bool
	 */
	public function isFreemiumActivated(): bool {
		return get_option( Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED, 'no' ) === 'yes';
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function getTrendingMetaKey(): string {
		return $this->mapper->getBy( Mapper::INTERVAL, $this->getTrendingInterval(), Mapper::META_KEY );
	}

	/**
	 * @return int
	 */
	public function getTrendingInterval(): int {
		return $this->isFreemiumActivated() ? (int) ( get_option( Constants::SETTINGS_FIELD_TRENDING_INTERVAL ) ?: 30 ) : 7;
	}

	/**
	 * @return string
	 */
	public function getTrendingOptionNameUrl(): string {
		return $this->isFreemiumActivated() ?
			( get_option( Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL ) ?: Constants::TRENDING_DEFAULT_OPTION_NAME ) :
			Constants::TRENDING_DEFAULT_OPTION_NAME;
	}
}
