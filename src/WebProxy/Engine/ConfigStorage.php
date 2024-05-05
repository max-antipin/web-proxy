<?php

namespace MaxieSystems\WebProxy\Engine;

/**
 * @property-read string $scriptURL
 * @property-read bool $useSubdomains
 * @property-read string $delimiter
 */
abstract class ConfigStorage
{
    protected function __construct(
        public readonly string $scriptURL,
        public readonly bool $useSubdomains,
        public readonly string $delimiter,
    ) {
    }
}
