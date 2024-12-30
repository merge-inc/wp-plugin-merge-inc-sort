<?php

declare(strict_types=1);

namespace MergeInc\Sort\Dependencies\DI\Definition\Source;

use MergeInc\Sort\Dependencies\DI\Definition\Definition;
use MergeInc\Sort\Dependencies\DI\Definition\Exception\InvalidDefinition;

/**
 * Source of definitions for entries of the container.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface DefinitionSource
{
    /**
     * Returns the MergeInc\Sort\Dependencies\DI definition for the entry name.
     *
     * @throws InvalidDefinition An invalid definition was found.
     * @return Definition|null
     */
    public function getDefinition(string $name);

    /**
     * @return Definition[] Definitions indexed by their name.
     */
    public function getDefinitions() : array;
}
