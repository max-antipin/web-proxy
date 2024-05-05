<?php

namespace DollySites\Engine;

/**
 * @property-read string $sourceURL
 * @property-read string $scriptURL
 * @property-read bool $useSubdomains
 * @property-read string $delimiter
 */
abstract class ConfigStorage extends \MaxieSystems\WebProxy\Engine\ConfigStorage
{
    protected function __construct(
        public readonly string $sourceURL,
        string $scriptURL,
        bool $useSubdomains,
        string $delimiter,
    ) {
        parent::__construct($scriptURL, $useSubdomains, $delimiter);
    }
}
