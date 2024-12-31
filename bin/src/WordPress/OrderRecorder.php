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
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param SalesCalculator $salesCalculator
	 * @param DataHelper      $dataHelper
	 */
	public function __construct( SalesCalculator $salesCalculator, DataHelper $dataHelper ) {
		$this->salesCalculator = $salesCalculator;
		$this->dataHelper      = $dataHelper;
	}

	/**
	 * @param WC_Order $wcOrder
	 * @return void
	 * @throws Exception
	 */
	public function record( WC_Order $wcOrder ): void {
		if ( $this->dataHelper->isOrderRecorded( $wcOrder->get_id() ) ) {
			return;
		}

		foreach ( $wcOrder->get_items() as $item ) {
			$itemData  = $item->get_data();
			$productId = $itemData['product_id'] ?? null;
			if ( $productId ) {
				$this->dataHelper->setProductSales(
					$productId,
					$this->salesCalculator->addSale(
						$this->dataHelper->getProductSales( $productId ),
						new DateTime( $wcOrder->get_date_created()->date( 'Y-m-d' ) )
					)
				);
			}
		}

		$this->dataHelper->setOrderRecorded( $wcOrder->get_id() );
	}

	/**
	 * @param WC_Order $wcOrder
	 * @return void
	 * @throws Exception
	 */
	public function delete( WC_Order $wcOrder ): void {
		if ( ! $this->dataHelper->isOrderRecorded( $wcOrder->get_id() ) ) {
			return;
		}

		foreach ( $wcOrder->get_items() as $item ) {
			$itemData  = $item->get_data();
			$productId = $itemData['product_id'] ?? null;
			if ( $productId ) {
				$this->dataHelper->setProductSales(
					$productId,
					$this->salesCalculator->removeSale(
						$this->dataHelper->getProductSales( $productId ),
						new DateTime( $wcOrder->get_date_created()->date( 'Y-m-d' ) )
					)
				);
			}
		}

		$this->dataHelper->deleteOrderRecorded( $wcOrder->get_id() );
	}
}
