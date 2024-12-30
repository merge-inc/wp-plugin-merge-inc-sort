<?php

namespace MergeInc\Sort\Dependencies\League\Plates\Extension;

use MergeInc\Sort\Dependencies\League\Plates\Engine;

/**
 * A common interface for extensions.
 */
interface ExtensionInterface
{
    public function register(Engine $engine);
}
