<?php

declare(strict_types=1);

namespace MergeInc\Sort\Dependencies\DI\Definition;

use MergeInc\Sort\Dependencies\DI\Factory\RequestedEntry;

/**
 * Definition.
 *
 * @internal This interface is internal to PHP-MergeInc\Sort\Dependencies\DI and may change between minor versions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Definition extends RequestedEntry
{
    /**
     * Returns the name of the entry in the container.
     */
    public function getName() : string;

    /**
     * Set the name of the entry in the container.
     */
    public function setName(string $name);

    /**
     * Apply a callable that replaces the definitions nested in this definition.
     */
    public function replaceNestedDefinitions(callable $replacer);

    /**
     * Definitions can be cast to string for debugging information.
     */
    public function __toString();
}
