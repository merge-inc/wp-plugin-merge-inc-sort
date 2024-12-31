<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use MergeInc\Sort\WordPress\DataHelper;
use MergeInc\Sort\WordPress\OrderRecorder;

/**
 * Class OrderUpdatedController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class OrderUpdatedController extends AbstractController {

	/**
	 *
	 */
	public const INVALID_ORDER_STATUSES = array(
		'pending',
		'trash',
		'cancelled',
		'failed',
		'refunded',
		'on-hold',
	);

	/**
	 * @var OrderRecorder
	 */
	private OrderRecorder $orderRecorder;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param OrderRecorder $orderRecorder
	 * @param DataHelper    $dataHelper
	 */
	public function __construct( OrderRecorder $orderRecorder, DataHelper $dataHelper ) {
		$this->orderRecorder = $orderRecorder;
		$this->dataHelper    = $dataHelper;
	}

	/**
	 * @param int $orderId
	 * @return void
	 * @throws Exception
	 */
	public function __invoke( int $orderId ): void {
		if ( $order = $this->dataHelper->getOrderById( $orderId ) ) {
			if ( $order->is_paid() ) {
				if ( in_array( $order->get_status(), self::INVALID_ORDER_STATUSES ) ) {
					$this->orderRecorder->delete( $order );
					return;
				}

				$this->orderRecorder->record( $order );
				return;
			}

			$this->orderRecorder->delete( $order );
		}
	}
}
