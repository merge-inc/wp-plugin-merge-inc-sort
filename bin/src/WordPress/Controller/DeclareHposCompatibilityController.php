<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

/**
 * Class ControllerRegistrar
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class DeclareHposCompatibilityController extends AbstractController {

	/**
	 * @return void
	 */
	public function __invoke(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', __DIR__ . '/../../../merge-inc-sort.php' );
		}
	}
}
