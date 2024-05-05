<?php

namespace MaxieSystems\Tests\Stub\WebProxy\Engine;

use MaxieSystems\WebProxy\Engine\ConfigStorage;

class ConfigStorageStub extends ConfigStorage
{
    final public function __construct(
        public readonly string $scriptURL,
        public readonly bool $useSubdomains = false,
        public readonly string $delimiter = ''
    ) {
    }
}
