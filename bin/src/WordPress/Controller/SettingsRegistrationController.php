<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\DataHelper;
use MergeInc\Sort\Dependencies\League\Plates\Engine;

/**
 * Class SettingsRegistrationController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class SettingsRegistrationController extends AbstractController {

	/**
	 * @var Engine
	 */
	private Engine $engine;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @param Engine     $engine
	 * @param DataHelper $dataHelper
	 * @param Mapper     $mapper
	 */
	public function __construct( Engine $engine, DataHelper $dataHelper, Mapper $mapper ) {
		$this->engine     = $engine;
		$this->dataHelper = $dataHelper;
		$this->mapper     = $mapper;
	}

	/**
	 * TODO MAYBE FIELDS VALUE SHOULD COME FROM A SERVICE IN ORDER TO NORMALIZE & VALIDATE THEM
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __invoke(): void {
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELDS_ACTIVATED );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELDS_DEFAULT );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELD_TRENDING_LABEL );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELD_TRENDING_INTERVAL );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL );

		add_settings_section(
			Constants::SETTINGS_SECTION_ACTIVATION,
			'🔌 ' . __( 'Activation Settings', 'ms' ),
			function () {
				echo '<hr>';
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_ACTIVATED,
			__( 'Activated', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-checkbox',
					array(
						'id'      => Constants::SETTINGS_FIELDS_ACTIVATED,
						'checked' => checked( true, $this->dataHelper->isActivated(), false ),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_ACTIVATION,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED,
			__( 'Freemium Activation', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-checkbox',
					array(
						'id'      => Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED,
						'checked' => checked( true, $this->dataHelper->isFreemiumActivated(), false ),
					)
				);

				echo $this->engine->render( 'freemium-notice' );
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_ACTIVATION,
		);

		add_settings_section(
			Constants::SETTINGS_SECTION_BASIC,
			'⚙️ ' . __( 'Basic Settings', 'ms' ),
			function () {
				echo '<hr>';
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_DEFAULT,
			__( 'Set Trending as Default', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-checkbox',
					array(
						'id'      => Constants::SETTINGS_FIELDS_DEFAULT,
						'checked' => checked( true, $this->dataHelper->isDefault(), false ),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_BASIC,
		);

		add_settings_section(
			Constants::SETTINGS_SECTION_FREEMIUM,
			'🌟 ' . __( 'Freemium Settings', 'ms' ),
			function () {
				echo '<hr>';
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_LABEL,
			__( 'Trending Label', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-trending-label',
					array(
						'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
						'id'                => Constants::SETTINGS_FIELD_TRENDING_LABEL,
						'value'             => $this->dataHelper->getTrendingLabel(),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_INTERVAL,
			__( 'Trending Interval', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-trending-interval',
					array(
						'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
						'id'                => Constants::SETTINGS_FIELD_TRENDING_INTERVAL,
						'intervals'         => $this->mapper->getIntervals(),
						'daysLabel'         => __( 'Days', 'ms' ),
						'value'             => $this->dataHelper->getTrendingInterval(),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL,
			__( 'Trending Option Name in URL (for SEO)', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-trending-option-name-url',
					array(
						'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
						'id'                => Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL,
						'value'             => $this->dataHelper->getTrendingOptionNameUrl(),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);
	}
}
