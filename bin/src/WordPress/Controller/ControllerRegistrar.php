<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use InvalidArgumentException;
use MergeInc\Sort\Dependencies\Psr\Container\ContainerInterface;
use MergeInc\Sort\Dependencies\Psr\Container\NotFoundExceptionInterface;
use MergeInc\Sort\Dependencies\Psr\Container\ContainerExceptionInterface;

/**
 * Class ControllerRegistrar
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class ControllerRegistrar {

	/**
	 * @var ContainerInterface
	 */
	private ContainerInterface $container;

	/**
	 * @param ContainerInterface $container
	 * @return void
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}

	/**
	 * @param string $hookName
	 * @param string $controller
	 * @param int    $priority
	 * @param int    $acceptedArguments
	 * @return void
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function register( string $hookName, string $controller, int $priority = 10, int $acceptedArguments = 1 ): void {
		if ( ! is_subclass_of( $controller, AbstractController::class ) ) {
			throw new InvalidArgumentException( "The controller must extend AbstractController. Given: $controller" );
		}

		add_filter( $hookName, $this->container->get( $controller ), $priority, $acceptedArguments );
	}
}
