<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Globals\EnvironmentDetector;

/**
 * Class PageDetectorAndDataInjectionController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class PageDetectorAndDataInjectionController extends AbstractController {

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
	 * @param array $data
	 * @return array
	 */
	public function __invoke( array $data ): array {
		$screen       = get_current_screen();
		$data['page'] = null;
		if ( $screen && $screen->base === 'edit' && $screen->post_type === 'product' ) {
			$data['page'] = 'product-listing';
		}

		if ( ( $_GET['page'] ?? null ) === Constants::ADMIN_MENU_PAGE_SLUG ) {
			$data['page'] = 'settings-page';
		}

		$data['dev'] = $this->environmentDetector->isDevelopment();

		return $data;
	}
}
