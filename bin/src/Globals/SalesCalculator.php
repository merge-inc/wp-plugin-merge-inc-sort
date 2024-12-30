<?php
declare(strict_types=1);

namespace MergeInc\Sort\Globals;

use DateTime;

/**
 * Class SalesCalculator
 *
 * @package MergeInc\Sort
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class SalesCalculator {

	/**
	 * @param array         $sales
	 * @param DateTime|null $dateTime
	 * @return array
	 */
	public function addSale( array $sales, DateTime $dateTime = null ): array {
		if ( $dateTime === null ) {
			$dateTime = new DateTime();
		}

		$dateKey           = $dateTime->format( 'Y-m-d' );
		$sales[ $dateKey ] = ( $sales[ $dateKey ] ?? 0 ) + 1;

		return $this->normalizeSales( $sales );
	}

	/**
	 * @param array $sales
	 * @return array
	 */
	private function normalizeSales( array $sales ): array {
		$normalizedSales = array();
		foreach ( $sales as $date => $currentDaySales ) {
			if ( $this->isValidDate( $date ) && is_int( $currentDaySales ) ) {
				$normalizedSales[ $date ] = $currentDaySales;
			}
		}

		ksort( $normalizedSales );
		$normalizedSales = array_reverse( $normalizedSales );

		$normalizedSales_  = array();
		$maximumDaysToKeep = Constants::PRODUCT_SALES_MAXIMUM_DAYS_TO_KEEP;
		$maximumDaysAgo    = date( 'Y-m-d', strtotime( "-$maximumDaysToKeep days" ) );
		foreach ( $normalizedSales as $date => $currentDaySales ) {
			if ( $date < $maximumDaysAgo ) {
				return $normalizedSales_;
			}

			$normalizedSales_[ $date ] = $currentDaySales;
		}

		return $normalizedSales_;
	}

	/**
	 * @param string $date
	 * @return bool
	 */
	private function isValidDate( string $date ): bool {
		$dateTime = DateTime::createFromFormat( 'Y-m-d', $date );

		return $dateTime && $dateTime->format( 'Y-m-d' ) === $date;
	}

	/**
	 * @param array         $sales
	 * @param DateTime|NULL $dateTime
	 * @return array
	 */
	public function removeSale( array $sales, DateTime $dateTime = null ): array {
		if ( $dateTime === null ) {
			$dateTime = new DateTime();
		}

		$dateKey           = $dateTime->format( 'Y-m-d' );
		$sales[ $dateKey ] = ( $sales[ $dateKey ] ?? 1 ) - 1;
		if ( $sales[ $dateKey ] === 0 ) {
			unset( $sales[ $dateKey ] );
		}

		return $this->normalizeSales( $sales );
	}

	/**
	 * @param array $sales
	 * @param int   $interval
	 * @return int
	 */
	public function getSalesByInterval( array $sales, int $interval ): int {
		$sales     = $this->normalizeSales( $sales );
		$dayToStop = date( 'Y-m-d', strtotime( "-$interval days" ) );

		$totalSales = 0;
		foreach ( $sales as $date => $currentDaySales ) {
			if ( $date <= $dayToStop ) {
				return $totalSales;
			}

			$totalSales += $currentDaySales;
		}

		return $totalSales;
	}
}
