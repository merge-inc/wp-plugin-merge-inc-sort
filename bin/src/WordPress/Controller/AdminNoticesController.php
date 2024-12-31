<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Dependencies\League\Plates\Engine;

/**
 * Class ControllerRegistrar
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class AdminNoticesController extends AbstractController {

	/**
	 * @var Engine
	 */
	private Engine $engine;

	/**
	 * @param Engine $engine
	 */
	public function __construct( Engine $engine ) {
		$this->engine = $engine;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		if ( ( $_GET['page'] ?? null ) === Constants::ADMIN_MENU_PAGE_SLUG ) {
			return;
		}

		echo $this->engine->render( 'generic-message-notice' );

		echo $this->engine->render(
			'subscribe-notice',
			array(
				'message'    => __(
					'Unlock exclusive updates, special offers, and insider tips—subscribe now and never miss out!',
					'ms'
				),
				'adminEmail' => get_option( 'admin_email' ),
				'siteUrl'    => get_site_url(),
			)
		);
	}
}
