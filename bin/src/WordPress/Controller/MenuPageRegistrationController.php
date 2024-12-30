<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Dependencies\League\Plates\Engine;

/**
 * Class MenuPageRegistrationController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class MenuPageRegistrationController extends AbstractController {

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
		add_menu_page(
			'📊 | ' . __( 'Sort Settings', 'ms' ),
			__( 'Sort Settings', 'ms' ),
			'manage_options',
			Constants::ADMIN_MENU_PAGE_SLUG,
			function () {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				ob_start();
				settings_fields( Constants::ADMIN_MENU_OPTION_GROUP );
				do_settings_sections( Constants::ADMIN_MENU_PAGE_SLUG );
				submit_button( __( 'Save Settings', 'ms' ) );
				$pageContent = ob_get_clean();

				echo $this->engine->render(
					'settings-page',
					array(
						'title'       => esc_html( get_admin_page_title() ),
						'pageContent' => $pageContent,
					)
				);
			},
			'dashicons-sort',
			20,
		);
	}
}
