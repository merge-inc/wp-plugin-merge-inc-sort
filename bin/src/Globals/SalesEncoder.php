<?php
declare(strict_types=1);

namespace MergeInc\Sort\Globals;

/**
 * Class SalesEncoder
 *
 * @package MergeInc\Sort
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class SalesEncoder {

	/**
	 * @param array $sales
	 * @return string
	 */
	public function encode( array $sales ): string {
		return $this->urlSafeEncode( json_encode( $sales ) );
	}

	/**
	 * @param string $data
	 * @return string
	 */
	private function urlSafeEncode( string $data ): string {
		return urlencode( base64_encode( $data ) );
	}

	/**
	 * @param string $sales
	 * @return array
	 */
	public function decode( string $sales ): array {
		$decoded = $this->urlSafeDecode( $sales );
		$sales   = json_decode( $decoded, true );

		return $sales ?: array();
	}

	/**
	 * @param string $data
	 * @return string
	 */
	private function urlSafeDecode( string $data ): string {
		return base64_decode( urldecode( $data ) );
	}
}
