<?php
declare(strict_types=1);

namespace MergeInc\Sort\Globals;

use Exception;

/**
 * Class Mapper
 *
 * @package MergeInc\Sort
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 19/12/24
 */
final class Mapper {

	/**
	 *
	 */
	public const INTERVAL = 'interval';

	/**
	 *
	 */
	public const COLUMN = 'column';

	/**
	 *
	 */
	public const META_KEY = 'metaKey';

	/**
	 *
	 */
	public const LABEL = 'label';

	/**
	 *
	 */
	public const IS_APPLICABLE_INTERVAL = 'isApplicableInterval';

	/**
	 *
	 */
	private const MAPS = array(
		array(
			self::INTERVAL               => 1,
			self::LABEL                  => 'Today',
			self::IS_APPLICABLE_INTERVAL => false,
		),
		array(
			self::INTERVAL               => 7,
			self::COLUMN                 => Constants::WEEKLY_SALES_COLUMN_NAME,
			self::META_KEY               => Constants::PRODUCT_WEEKLY_SALES_META_KEY,
			self::LABEL                  => 'Weekly',
			self::IS_APPLICABLE_INTERVAL => true,
		),
		array(
			self::INTERVAL               => 15,
			self::COLUMN                 => Constants::BIWEEKLY_SALES_COLUMN_NAME,
			self::META_KEY               => Constants::PRODUCT_BIWEEKLY_SALES_META_KEY,
			self::LABEL                  => 'Biweekly',
			self::IS_APPLICABLE_INTERVAL => true,
		),
		array(
			self::INTERVAL               => 30,
			self::COLUMN                 => Constants::MONTHLY_SALES_COLUMN_NAME,
			self::META_KEY               => Constants::PRODUCT_MONTHLY_SALES_META_KEY,
			self::LABEL                  => 'Monthly',
			self::IS_APPLICABLE_INTERVAL => true,
		),
		array(
			self::INTERVAL               => 90,
			self::COLUMN                 => Constants::QUARTERLY_SALES_COLUMN_NAME,
			self::META_KEY               => Constants::PRODUCT_QUARTERLY_SALES_META_KEY,
			self::LABEL                  => 'Quarterly',
			self::IS_APPLICABLE_INTERVAL => true,
		),
		array(
			self::INTERVAL               => 180,
			self::COLUMN                 => Constants::HALF_YEARLY_SALES_COLUMN_NAME,
			self::META_KEY               => Constants::PRODUCT_HALF_YEARLY_SALES_META_KEY,
			self::LABEL                  => 'Half Yearly',
			self::IS_APPLICABLE_INTERVAL => true,
		),
		array(
			self::INTERVAL               => 365,
			self::COLUMN                 => Constants::YEARLY_SALES_COLUMN_NAME,
			self::META_KEY               => Constants::PRODUCT_YEARLY_SALES_META_KEY,
			self::LABEL                  => 'Yearly',
			self::IS_APPLICABLE_INTERVAL => true,
		),
	);

	/**
	 * @return array
	 * @throws Exception
	 */
	public function getColumns(): array {
		$columns   = array();
		$intervals = $this->getIntervals();
		foreach ( $intervals as $interval ) {
			$columns[] = $this->getBy( self::INTERVAL, $interval, self::COLUMN );
		}

		return $columns;
	}

	/**
	 * @return array
	 */
	public function getIntervals(): array {
		$intervals = array();
		foreach ( self::MAPS as $map ) {
			if ( $map[ self::IS_APPLICABLE_INTERVAL ] ) {
				$intervals[] = $map[ self::INTERVAL ];
			}
		}

		return $intervals;
	}

	/**
	 * @param $search
	 * @param $value
	 * @param $return
	 * @return mixed
	 * @throws Exception
	 */
	public function getBy( $search, $value, $return ) {
		$intervals = $this->getIntervals();
		foreach ( self::MAPS as $map ) {
			if ( ( $map[ $search ] ?? null ) === $value ) {
				if ( in_array( $map[ self::INTERVAL ], $intervals ) ) {
					return $map[ $return ];
				}
			}
		}

		throw new Exception( "$search: '$value' was not found" );
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function getMetaKeys(): array {
		$metaKeys  = array();
		$intervals = $this->getIntervals();
		foreach ( $intervals as $interval ) {
			$metaKeys[] = $this->getBy( self::INTERVAL, $interval, self::META_KEY );
		}

		return $metaKeys;
	}
}
