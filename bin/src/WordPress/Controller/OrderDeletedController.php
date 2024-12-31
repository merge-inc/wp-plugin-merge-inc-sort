<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use MergeInc\Sort\WordPress\DataHelper;
use MergeInc\Sort\WordPress\OrderRecorder;

/**
 * Class OrderDeletedController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class OrderDeletedController extends AbstractController {

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
			$this->orderRecorder->delete( $order );
		}
	}
}
