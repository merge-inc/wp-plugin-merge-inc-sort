<?php
declare(strict_types=1);

namespace MergeInc\Sort\Globals;

/**
 * Class EnvironmentDetector
 *
 * @package MergeInc\Sort
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 5/12/24
 */
final class EnvironmentDetector {

	/**
	 *
	 */
	public const PRODUCTION = 'prod';

	/**
	 *
	 */
	public const STAGING = 'staging';

	/**
	 *
	 */
	public const DEVELOPMENT = 'dev';

	/**
	 * @var string|null
	 */
	private ?string $env = null;

	/**
	 * @return bool
	 */
	public function isProduction(): bool {
		return $this->detect() === self::PRODUCTION;
	}

	/**
	 * @return string
	 */
	public function detect(): string {
		if ( $this->env !== null ) {
			return $this->env;
		}

		return $this->env = $this->_detect();
	}

	/**
	 * @return string
	 */
	private function _detect(): string {
		if ( str_contains( ( $_SERVER['HTTP_HOST'] ?? null ), 'localhost' ) ) {
			return self::DEVELOPMENT;
		}

		if ( str_contains( ( $_SERVER['HTTP_HOST'] ?? null ), '127.0.0.1' ) ) {
			return self::DEVELOPMENT;
		}

		if ( str_contains( ( $_SERVER['HTTP_HOST'] ?? null ), 'host.docker.internal' ) ) {
			return self::DEVELOPMENT;
		}

		if ( str_contains( ( $_SERVER['HTTP_HOST'] ?? null ), 'staging' ) ) {
			return self::STAGING;
		}

		$appEnv = getenv( 'APP_ENV' );

		if ( in_array(
			$appEnv,
			array(
				'dev',
				'development',
				'local',
			),
			true
		) ) {
			return self::DEVELOPMENT;
		}

		if ( in_array(
			$appEnv,
			array(
				'staging',
				'test',
			),
			true
		) ) {
			return self::STAGING;
		}

		return self::PRODUCTION;
	}

	/**
	 * @return void
	 */
	public function clearCache(): void {
		$this->env = null;
	}

	/**
	 * @return bool
	 */
	public function isStaging(): bool {
		return $this->detect() === self::STAGING;
	}

	/**
	 * @return bool
	 */
	public function isDevelopment(): bool {
		return $this->detect() === self::DEVELOPMENT;
	}
}
