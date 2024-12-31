<?php
declare(strict_types=1);

/**
 * Plugin Name: Sort
 * Plugin URI: https://wordpress.org/plugins/sort/
 * Description: Sort Plugin
 * Version: 0.0.10
 * Author: Merge Inc
 * Author URI: https://joinmerge.gr
 * GitHub Plugin URI: https://github.com/merge-inc/sort
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 6.2.1
 * Tested up to: 6.7.1
 * WC requires at least: 7.3.0
 * WC tested up to: 9.5.1
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 *
 * @package MergeInc\Sort
 */

namespace MergeInc\Sort;

require_once __DIR__ . '/bin/vendor/autoload.php';

use Error;
use Exception;
use WC_Product_Query;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\MetaDataHelper;
use MergeInc\Sort\WordPress\ProductsHelper;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use MergeInc\Sort\Dependencies\DI\ContainerBuilder;
use MergeInc\Sort\Dependencies\DI\NotFoundException;
use MergeInc\Sort\Dependencies\League\Plates\Engine;
use MergeInc\Sort\Dependencies\DI\DependencyException;
use MergeInc\Sort\WordPress\Controller\ThankYouController;
use MergeInc\Sort\WordPress\Controller\ControllerRegistrar;
use MergeInc\Sort\WordPress\Controller\OrderUpdatedController;
use MergeInc\Sort\WordPress\Controller\OrderDeletedController;
use MergeInc\Sort\Dependencies\Psr\Container\ContainerInterface;
use MergeInc\Sort\WordPress\Controller\MenuPageRegistrationController;
use MergeInc\Sort\WordPress\Controller\SettingsRegistrationController;
use MergeInc\Sort\WordPress\Controller\InjectAdminJavascriptController;
use MergeInc\Sort\Dependencies\Psr\Container\NotFoundExceptionInterface;
use MergeInc\Sort\Dependencies\Psr\Container\ContainerExceptionInterface;
use MergeInc\Sort\WordPress\Controller\SetQueryOrderConfigurationController;
use MergeInc\Sort\WordPress\Controller\PageDetectorAndDataInjectionController;
use MergeInc\Sort\WordPress\Controller\RunProductsMetaKeysCreationActionController;
use MergeInc\Sort\WordPress\Controller\AddTrendingOptionInCategorySortingOptionsController;

/**
 * Class Sort
 *
 * @package MergeInc\Sort
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 26/11/24
 */
class Sort {

	/**
	 * @var Sort|null
	 */
	private static ?Sort $self = null;

	/**
	 * @var ContainerInterface
	 */
	private ContainerInterface $container;

	/**
	 * @return Sort
	 */
	final public static function construct(): Sort {
		/**
		 * The singleton pattern
		 */
		if ( static::$self === null ) {
			static::$self = new Sort();
		}

		return static::$self;
	}

	/**
	 * @return void
	 * @throws ContainerExceptionInterface
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws NotFoundExceptionInterface
	 */
	final public function init() {
		if ( ! wp_next_scheduled( Constants::CREATE_PRODUCTS_META_KEYS_ACTION_NAME ) ) {
			wp_schedule_event( time(), 'hourly', Constants::CREATE_PRODUCTS_META_KEYS_ACTION_NAME );
		}

		if ( ! wp_next_scheduled( Constants::INFORM_WEBSITE_DATA_ACTION_NAME ) ) {
			wp_schedule_event( time(), 'daily', Constants::INFORM_WEBSITE_DATA_ACTION_NAME );
		}

		add_action(
			Constants::INFORM_WEBSITE_DATA_ACTION_NAME,
			function () {
				/**
				 * @var MetaDataHelper $metaDataHelper
				 */
				$metaDataHelper = $this->getFromContainer( MetaDataHelper::class );

				if ( $metaDataHelper->isFreemiumActivated() ) {
					echo get_site_url();
					echo PHP_EOL;
					echo get_option( 'admin_email' );
				} else {
					echo 'FREEMIUM NOT ACTIVE';
				}
				echo PHP_EOL;
			}
		);

		/**
		 * @var ControllerRegistrar $controllerRegistrar
		 */
		$controllerRegistrar = $this->getFromContainer( ControllerRegistrar::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register(
			Constants::CREATE_PRODUCTS_META_KEYS_ACTION_NAME,
			RunProductsMetaKeysCreationActionController::class,
		);

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register( 'woocommerce_thankyou', ThankYouController::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register( 'woocommerce_order_status_changed', OrderUpdatedController::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register( 'woocommerce_delete_order', OrderDeletedController::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register( Constants::ADMIN_DATA_FILTER, PageDetectorAndDataInjectionController::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register( 'admin_menu', MenuPageRegistrationController::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register( 'admin_init', SettingsRegistrationController::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register( 'pre_get_posts', SetQueryOrderConfigurationController::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register( 'admin_footer', InjectAdminJavascriptController::class );

		/**
		 * TODO ADD COMMENT WHAT THIS IS
		 */
		$controllerRegistrar->register(
			'woocommerce_catalog_orderby',
			AddTrendingOptionInCategorySortingOptionsController::class,
		);

		/**
		 * TODO THIS IS FOR DEV -- NOT FOR PRODUCTION
		 * REMOVE IT FROM HERE
		 */
		add_action(
			'admin_notices',
			function () {
				$args = array(
					'limit'   => 1,
					'orderby' => 'rand',
					'status'  => 'publish',
				);

				$query    = new WC_Product_Query( $args );
				$products = $query->get_products();

				if ( empty( $products ) ) {
					return;
				}

				if ( $this->getFromContainer( ProductsHelper::class )->haveAllProductsSortMetaKeys() ) {
					return;
				}

				// TODO USE TEMPLATE
				// TODO MAKE AJAX
				?>
				<div class="notice notice-warning is-dismissible">
					<p><strong>Sort</strong> | It seems that <code>Sort</code> has never created product keys. <a
								href="/sort/api/v1/keys" target="_blank">here</a> to create them. <strong
								style="color:red">Warning:</strong> It might
						take some moments to be completed</p>
				</div>
				<?php
			},
			-1,
		);

		add_action(
			'admin_notices',
			function () {
				if ( ( $_GET['page'] ?? null ) === Constants::ADMIN_MENU_PAGE_SLUG ) {
					return;
				}

				/**
				 * @var Engine $engine
				 */
				$engine = $this->getFromContainer( Engine::class );

				echo $engine->render( 'generic-message-notice' );

				$adminEmail = get_option( 'admin_email' );
				echo $engine->render(
					'subscribe-notice',
					array(
						'message'    => __(
							'Unlock exclusive updates, special offers, and insider tips—subscribe now and never miss out!',
							'ms'
						),
						'adminEmail' => $adminEmail,
						'siteUrl'    => get_site_url(),
					)
				);
			},
			-99,
		);

		add_filter(
			'woocommerce_get_catalog_ordering_args',
			/**
			 * @param array $args
			 * @return array
			 * @throws ContainerExceptionInterface
			 * @throws DependencyException
			 * @throws NotFoundException
			 * @throws NotFoundExceptionInterface
			 * @throws Exception
			 */
			function ( array $args ): array {
				/**
				 * @var MetaDataHelper $metaDataHelper ;
				 */
				$metaDataHelper = $this->getFromContainer( MetaDataHelper::class );

				if ( in_array(
					( $_GET['orderby'] ?? null ),
					array(
						'trending',
						null,
					),
				) ) {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = $metaDataHelper->getTrendingMetaKey();
					$args['order']    = 'DESC';
				}

				return $args;
			},
			11,
		);

		add_filter(
			'pre_option_woocommerce_default_catalog_orderby',
			/**
			 * @return false|string
			 * @throws ContainerExceptionInterface
			 * @throws DependencyException
			 * @throws NotFoundException
			 * @throws NotFoundExceptionInterface
			 */
			function () {
				/**
				 * @var MetaDataHelper $metaDataHelper
				 */
				$metaDataHelper = $this->getFromContainer( MetaDataHelper::class );

				return $metaDataHelper->isDefault() ? 'trending' : false;
			},
			99,
		);

		add_action(
			'before_woocommerce_init',
			function () {
				if ( class_exists( FeaturesUtil::class ) ) {
					FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
				}
			},
		);
	}

	/**
	 * @param string $key
	 * @return mixed
	 * @throws ContainerExceptionInterface
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws NotFoundExceptionInterface
	 * @throws Exception
	 */
	final public function getFromContainer( string $key ) {
		if ( ! ( $this->container ?? false ) ) {
			$containerBuilder = new ContainerBuilder();
			$containerBuilder->addDefinitions(
				array(
					Engine::class => function (): Engine {
						return new Engine( __DIR__ . '/templates' );
					},
				),
			);
			$this->container = $containerBuilder->build();
		}

		return $this->container->get( $key );
	}
}

try {
	Sort::construct()->init();
} catch ( Error | Exception | DependencyException | NotFoundException | ContainerExceptionInterface $e ) {
	add_action(
		'admin_notices',
		function () use ( $e ) {
			$engine = new Engine( __DIR__ . '/templates' );
			echo $engine->render( 'error-notice', array( 'e' => $e ) );
		},
		-1,
	);
}