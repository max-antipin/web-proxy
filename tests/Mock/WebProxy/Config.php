<?php

namespace MaxieSystems\Tests\Mock\WebProxy;

class Config
{
    final public function __construct(
        public readonly string $script_url,
        public readonly bool $use_subdomains
    ) {
    }
}
