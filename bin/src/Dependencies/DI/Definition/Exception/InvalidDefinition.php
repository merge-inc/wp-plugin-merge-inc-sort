<?php

declare(strict_types=1);

namespace MergeInc\Sort\Dependencies\DI\Definition\Exception;

use MergeInc\Sort\Dependencies\DI\Definition\Definition;
use MergeInc\Sort\Dependencies\Psr\Container\ContainerExceptionInterface;

/**
 * Invalid MergeInc\Sort\Dependencies\DI definitions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class InvalidDefinition extends \Exception implements ContainerExceptionInterface
{
    public static function create(Definition $definition, string $message, \Exception $previous = null) : self
    {
        return new self(sprintf(
            '%s' . \PHP_EOL . 'Full definition:' . \PHP_EOL . '%s',
            $message,
            (string) $definition
        ), 0, $previous);
    }
}
