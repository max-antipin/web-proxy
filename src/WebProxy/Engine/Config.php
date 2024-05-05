<?php

namespace MaxieSystems\WebProxy\Engine;

use MaxieSystems\URLReadOnly;

class Config
{
    public function __construct(
        public readonly URLReadOnly $scriptURL,
        public readonly bool $useSubdomains,
        public readonly string $delimiter
    ) {
    }
}
