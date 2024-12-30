<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Globals\EnvironmentDetector;

/**
 * Class InjectAdminJavascriptController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class InjectAdminJavascriptController extends AbstractController {

	/**
	 * @var EnvironmentDetector
	 */
	private EnvironmentDetector $environmentDetector;

	/**
	 * @param EnvironmentDetector $environmentDetector
	 */
	public function __construct( EnvironmentDetector $environmentDetector ) {
		$this->environmentDetector = $environmentDetector;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		echo "<div id='frontend-admin'></div>";

		$version = $this->environmentDetector->isDevelopment() ? hash( 'crc32', (string) microtime( true ) ) : false;

		wp_enqueue_script(
			Constants::ADMIN_FRONTEND_HANDLE,
			rtrim( plugin_dir_url( __DIR__ . '/../../../merge-inc-sort.php' ), '/' ) . '/frontend/admin/dist/js/admin.js',
			false,
			$version
		);

		wp_enqueue_style(
			Constants::ADMIN_FRONTEND_HANDLE,
			rtrim( plugin_dir_url( __DIR__ . '/../../../merge-inc-sort.php' ), '/' ) . '/frontend/admin/dist/css/admin.css',
			false,
			$version
		);

		$data = apply_filters( Constants::ADMIN_DATA_FILTER, array( 'sort' => true ) );
		wp_localize_script( Constants::ADMIN_FRONTEND_HANDLE, Constants::ADMIN_FRONTEND_DATA_HANDLE, $data );
	}
}
