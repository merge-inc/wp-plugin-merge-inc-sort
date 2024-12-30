<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\WordPress\MetaDataHelper;

/**
 * Class AddTrendingOptionInCategorySortingOptionsController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 20/12/24
 */
final class AddTrendingOptionInCategorySortingOptionsController extends AbstractController {

	/**
	 * @var MetaDataHelper
	 */
	private MetaDataHelper $metaDataHelper;

	/**
	 * @param MetaDataHelper $metaDataHelper
	 */
	public function __construct( MetaDataHelper $metaDataHelper ) {
		$this->metaDataHelper = $metaDataHelper;
	}

	/**
	 * @param array $options
	 * @return array
	 */
	public function __invoke( array $options ): array {
		if ( ! $this->metaDataHelper->isActivated() ) {
			return $options;
		}

		$menuOrder = array();
		if ( isset( $options['menu_order'] ) ) {
			$menuOrder = array( 'menu_order' => $options['menu_order'] );
			unset( $options['menu_order'] );
		}

		$trendingOption = array( 'trending' => $this->metaDataHelper->getTrendingLabel() );

		return $menuOrder + $trendingOption + $options;
	}
}
