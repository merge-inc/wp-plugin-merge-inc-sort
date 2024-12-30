<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress;

use Exception;
use WC_Product;
use WC_Product_Query;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Globals\SalesCalculator;

/**
 * Class ProductsHelper
 *
 * @package MergeInc\Sort\WordPress
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class ProductsHelper {

	/**
	 * @var bool|null
	 */
	private ?bool $haveAllProductsSortMetaKeys = null;

	/**
	 * @var MetaDataHelper
	 */
	private MetaDataHelper $metaDataHelper;

	/**
	 * @var SalesCalculator
	 */
	private SalesCalculator $salesCalculator;

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @param MetaDataHelper  $metaDataHelper
	 * @param SalesCalculator $salesCalculator
	 * @param Mapper          $mapper
	 */
	public function __construct( MetaDataHelper $metaDataHelper, SalesCalculator $salesCalculator, Mapper $mapper ) {
		$this->metaDataHelper  = $metaDataHelper;
		$this->salesCalculator = $salesCalculator;
		$this->mapper          = $mapper;
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function haveAllProductsSortMetaKeys(): bool {
		if ( $this->haveAllProductsSortMetaKeys !== null ) {
			return $this->haveAllProductsSortMetaKeys;
		}

		$query = new WC_Product_Query(
			array(
				'limit'   => 1,
				'orderby' => 'rand',
				'status'  => 'publish',
			)
		);

		$products = $query->get_products();

		/**
		 * @var WC_Product $product
		 */
		$product = ! empty( $products ) ? $products[0] : null;
		if ( ! $product ) {
			return false;
		}

		foreach ( $this->mapper->getIntervals() as $interval ) {
			$productSales = $this->metaDataHelper->getProductIntervalSalesByColumn(
				$product->get_id(),
				$this->mapper->getBy( Mapper::INTERVAL, $interval, Mapper::COLUMN )
			);

			if ( $productSales === null ) {
				return $this->haveAllProductsSortMetaKeys = false;
			}
		}

		return $this->haveAllProductsSortMetaKeys = true;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	function createMetaKeys( bool $forceRestart = false ): array {
		$batchSize         = $this->calculateMemoryBasedBatchSize();
		$lastProcessedPage = get_option( Constants::BATCH_UPDATE_LAST_PROCESSED_PAGE_OPTION_NAME, 1 );
		if ( $forceRestart ) {
			$lastProcessedPage = 1;
		}

		$args = array(
			'limit'   => $batchSize,
			'orderby' => 'ID',
			'order'   => 'ASC',
			'status'  => 'publish',
			'return'  => 'ids',
			'page'    => $lastProcessedPage,
		);

		$query      = new WC_Product_Query( $args );
		$productIds = $query->get_products();

		if ( is_array( $productIds ) && ! empty( $productIds ) ) {
			foreach ( $productIds as $productId ) {
				$productId    = (int) $productId;
				$productSales = $this->metaDataHelper->getProductSales( $productId );

				foreach ( $this->mapper->getIntervals() as $interval ) {
					$this->metaDataHelper->setProductIntervalSalesByColumn(
						$productId,
						$this->mapper->getBy( Mapper::INTERVAL, $interval, Mapper::META_KEY ),
						$this->salesCalculator->getSalesByInterval( $productSales, $interval )
					);
				}
			}

			update_option( Constants::BATCH_UPDATE_LAST_PROCESSED_PAGE_OPTION_NAME, $lastProcessedPage + 1 );
		} else {
			delete_option( Constants::BATCH_UPDATE_LAST_PROCESSED_PAGE_OPTION_NAME );
		}

		$sample = (int) ceil( $batchSize * 0.10 );

		return array(
			'page'             => $lastProcessedPage,
			'batchSize'        => $batchSize,
			'sampleProductIds' => array_slice( $productIds, rand( 0, count( $productIds ) - ( $sample ) + 1 ), $sample ),
		);
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	private function calculateMemoryBasedBatchSize(): int {
		$memoryLimit          = ini_get( 'memory_limit' );
		$baseMemoryLimitBytes = 256 * 1024 * 1024;
		$currentUsage         = memory_get_usage();

		$memoryLimitBytes = (int) filter_var( $memoryLimit, FILTER_SANITIZE_NUMBER_INT ) * 1024 * 1024;
		if ( $memoryLimitBytes < $baseMemoryLimitBytes ) {
			$memoryLimitBytes = $baseMemoryLimitBytes;
		}
		$availableMemory = $memoryLimitBytes - $currentUsage;

		$memoryPerProduct = 100000 * count( $this->mapper->getMetaKeys() );

		$batchSize = (int) floor( $availableMemory / $memoryPerProduct );

		return max( 10, min( 500, $batchSize ) );
	}
}
