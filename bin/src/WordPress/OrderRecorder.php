<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress;

use WC_Order;
use DateTime;
use Exception;
use MergeInc\Sort\Globals\SalesCalculator;

/**
 * Class OrderRecorder
 *
 * @package MergeInc\Sort\WordPress
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 27/11/24
 */
final class OrderRecorder {

	/**
	 * @var SalesCalculator
	 */
	private SalesCalculator $salesCalculator;

	/**
	 * @var MetaDataHelper
	 */
	private MetaDataHelper $metaDataHelper;

	/**
	 * @param SalesCalculator $salesCalculator
	 * @param MetaDataHelper  $metaDataHelper
	 */
	public function __construct( SalesCalculator $salesCalculator, MetaDataHelper $metaDataHelper ) {
		$this->salesCalculator = $salesCalculator;
		$this->metaDataHelper  = $metaDataHelper;
	}

	/**
	 * @param WC_Order $wcOrder
	 * @return void
	 * @throws Exception
	 */
	public function record( WC_Order $wcOrder ): void {
		if ( $this->metaDataHelper->isOrderRecorded( $wcOrder->get_id() ) ) {
			return;
		}

		foreach ( $wcOrder->get_items() as $item ) {
			$itemData  = $item->get_data();
			$productId = $itemData['product_id'] ?? null;
			if ( $productId ) {
				$this->metaDataHelper->setProductSales(
					$productId,
					$this->salesCalculator->addSale(
						$this->metaDataHelper->getProductSales( $productId ),
						new DateTime( $wcOrder->get_date_created()->date( 'Y-m-d' ) )
					)
				);
			}
		}

		$this->metaDataHelper->setOrderRecorded( $wcOrder->get_id() );
	}

	/**
	 * @param WC_Order $wcOrder
	 * @return void
	 * @throws Exception
	 */
	public function delete( WC_Order $wcOrder ): void {
		if ( ! $this->metaDataHelper->isOrderRecorded( $wcOrder->get_id() ) ) {
			return;
		}

		foreach ( $wcOrder->get_items() as $item ) {
			$itemData  = $item->get_data();
			$productId = $itemData['product_id'] ?? null;
			if ( $productId ) {
				$this->metaDataHelper->setProductSales(
					$productId,
					$this->salesCalculator->removeSale(
						$this->metaDataHelper->getProductSales( $productId ),
						new DateTime( $wcOrder->get_date_created()->date( 'Y-m-d' ) )
					)
				);
			}
		}

		$this->metaDataHelper->deleteOrderRecorded( $wcOrder->get_id() );
	}
}
