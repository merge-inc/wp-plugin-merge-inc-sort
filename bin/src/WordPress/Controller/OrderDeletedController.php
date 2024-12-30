<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
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
	 * @param OrderRecorder $orderRecorder
	 */
	public function __construct( OrderRecorder $orderRecorder ) {
		$this->orderRecorder = $orderRecorder;
	}

	/**
	 * @param int $orderId
	 * @return void
	 * @throws Exception
	 */
	public function __invoke( int $orderId ): void {
		if ( $order = wc_get_order( $orderId ) ) {
			$this->orderRecorder->delete( $order );
		}
	}
}
