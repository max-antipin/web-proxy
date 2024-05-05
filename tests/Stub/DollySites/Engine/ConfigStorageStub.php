<?php

namespace MaxieSystems\Tests\Stub\DollySites\Engine;

use DollySites\Engine\ConfigStorage;

class ConfigStorageStub extends ConfigStorage
{
    final public function __construct(
        public readonly string $sourceURL,
        public readonly string $scriptURL,
        public readonly bool $useSubdomains = false,
        public readonly string $delimiter = ''
    ) {
    }
}
