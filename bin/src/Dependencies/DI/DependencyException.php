<?php

declare(strict_types=1);

namespace MergeInc\Sort\Dependencies\DI;

use MergeInc\Sort\Dependencies\Psr\Container\ContainerExceptionInterface;

/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
