<?php
declare(strict_types=1);

namespace WeProvide\AsyncDeferJs\Config;

use WeProvide\AsyncDeferJs\Exception\Config\ValueReaderException;

/**
 * Responsible for reading the value of a configuration setting
 */
interface ValueReaderInterface
{
    /**
     * Get the value of the configuration setting
     *
     * @return mixed|null the value of the configuration setting, null if none was configured
     * @throws ValueReaderException thrown if something unexpected happened whilst trying to get the value
     */
    public function read();
}
