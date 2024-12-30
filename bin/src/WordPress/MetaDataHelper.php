<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress;

use Exception;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Globals\SalesEncoder;

/**
 * Class MetaDataHelper
 *
 * @package MergeInc\Sort\WordPress
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class MetaDataHelper {

	/**
	 * @var SalesEncoder
	 */
	private SalesEncoder $salesEncoder;

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

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
		$order = wc_get_order( $id );
		if ( ! $order ) {
			return false;
		}

		return $order->get_meta( Constants::ORDER_RECORDED_META_KEY ) === 'yes';
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setOrderRecorded( int $id ): void {
		$order = wc_get_order( $id );
		if ( $order ) {
			$order->update_meta_data( Constants::ORDER_RECORDED_META_KEY, 'yes' );
			$order->save();
		}
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function deleteOrderRecorded( int $id ): void {
		$order = wc_get_order( $id );
		if ( $order ) {
			$order->delete_meta_data( Constants::ORDER_RECORDED_META_KEY );
			$order->save();
		}
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public function getProductSales( int $id ): array {
		$product = wc_get_product( $id );
		if ( $product ) {
			return $this->salesEncoder->decode( (string) $product->get_meta( Constants::PRODUCT_SALES_META_KEY ) );
		}

		return array();
	}

	/**
	 * @param int   $id
	 * @param array $sales
	 * @return void
	 */
	public function setProductSales( int $id, array $sales ): void {
		$product = wc_get_product( $id );
		if ( $product ) {
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
		$product = wc_get_product( $id );
		if ( $product ) {
			$intervalSales = $product->get_meta( $this->mapper->getBy( Mapper::COLUMN, $column, Mapper::META_KEY ) );

			return is_numeric( $intervalSales ) ? (int) $intervalSales : null;
		}
	}

	/**
	 * @param int    $id
	 * @param string $column
	 * @param int    $sales
	 * @return void
	 */
	public function setProductIntervalSalesByColumn( int $id, string $column, int $sales ): void {
		$product = wc_get_product( $id );
		if ( $product ) {
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
		return get_option( Constants::SETTINGS_FIELD_TRENDING_LABEL ) ?: __( 'Trending', 'ms' );
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
		return (int) ( get_option( Constants::SETTINGS_FIELD_TRENDING_INTERVAL ) ?: 30 );
	}
}
