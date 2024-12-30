<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\MetaDataHelper;
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
	 * @var MetaDataHelper
	 */
	private MetaDataHelper $metaDataHelper;

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @param Engine         $engine
	 * @param MetaDataHelper $metaDataHelper
	 * @param Mapper         $mapper
	 */
	public function __construct( Engine $engine, MetaDataHelper $metaDataHelper, Mapper $mapper ) {
		$this->engine         = $engine;
		$this->metaDataHelper = $metaDataHelper;
		$this->mapper         = $mapper;
	}

	/**
	 * TODO MAYBE FIELDS VALUE SHOULD COME FROM A SERVICE IN ORDER TO NORMALIZE & VALIDATE THEM
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __invoke(): void {
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELDS_ACTIVATED );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELDS_DEFAULT );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELD_TRENDING_LABEL );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELD_TRENDING_INTERVAL );

		add_settings_section(
			Constants::SETTINGS_SECTION_ACTIVATION,
			'🔌 | ' . __( 'Activation Settings', 'ms' ),
			function () {},
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
						'checked' => checked( true, $this->metaDataHelper->isActivated(), false ),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_ACTIVATION,
		);

		add_settings_section(
			Constants::SETTINGS_SECTION_BASIC,
			'⚙️ | ' . __( 'Basic Settings', 'ms' ),
			function () {},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_DEFAULT,
			__( 'Default Sorting', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-checkbox',
					array(
						'id'      => Constants::SETTINGS_FIELDS_DEFAULT,
						'checked' => checked( true, $this->metaDataHelper->isDefault(), false ),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_BASIC,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_LABEL,
			__( 'Trending Label', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-trending-label',
					array(
						'id'    => Constants::SETTINGS_FIELD_TRENDING_LABEL,
						'value' => $this->metaDataHelper->getTrendingLabel(),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_BASIC,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_INTERVAL,
			__( 'Trending Key', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-trending-interval',
					array(
						'id'        => Constants::SETTINGS_FIELD_TRENDING_INTERVAL,
						'intervals' => $this->mapper->getIntervals(),
						'daysLabel' => __( 'Days', 'ms' ),
						'value'     => $this->metaDataHelper->getTrendingInterval(),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_BASIC,
		);
	}
}
