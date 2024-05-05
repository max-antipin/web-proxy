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
    public readonly string $sourceURL;
    public readonly string $scriptURL;
    public readonly bool $useSubdomains;
    public readonly string $delimiter;
}
