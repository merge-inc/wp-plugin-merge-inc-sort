<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\WordPress\DataHelper;

/**
 * Class AddTrendingOptionInCategorySortingOptionsController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 20/12/24
 */
final class AddTrendingOptionInCategorySortingOptionsController extends AbstractController {

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param DataHelper $dataHelper
	 */
	public function __construct( DataHelper $dataHelper ) {
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @param array $options
	 * @return array
	 */
	public function __invoke( array $options ): array {
		if ( ! $this->dataHelper->isActivated() ) {
			return $options;
		}

		$menuOrder = array();
		if ( isset( $options['menu_order'] ) ) {
			$menuOrder = array( 'menu_order' => $options['menu_order'] );
			unset( $options['menu_order'] );
		}

		$trendingOption = array( $this->dataHelper->getTrendingOptionNameUrl() => $this->dataHelper->getTrendingLabel() );

		return $menuOrder + $trendingOption + $options;
	}
}
