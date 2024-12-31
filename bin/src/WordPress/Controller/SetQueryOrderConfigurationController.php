<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use WP_Query;
use Exception;
use MergeInc\Sort\Globals\Mapper;

/**
 * Class SetQueryOrderConfigurationController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 30/12/24
 */
final class SetQueryOrderConfigurationController extends AbstractController {

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @param Mapper $mapper
	 */
	public function __construct( Mapper $mapper ) {
		$this->mapper = $mapper;
	}

	/**
	 * @param WP_Query $query
	 * @return void
	 * @throws Exception
	 */
	public function __invoke( WP_Query $query ): void {
		$metaKeys   = $this->mapper->getMetaKeys();
		$metaKeys[] = 'total_sales';

		$orderBy = $query->get( 'orderby' );

		if ( in_array( $orderBy, $metaKeys ) ) {
			$query->set( 'meta_key', $orderBy );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}
}
