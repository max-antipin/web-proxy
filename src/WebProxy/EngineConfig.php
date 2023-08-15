<?php

namespace MaxieSystems\WebProxy;

use MaxieSystems\URLReadOnly;

final readonly class EngineConfig
{
    public function __construct(
        public readonly URLReadOnly $script_url,
        public readonly bool $use_subdomains,
        public readonly WebServer\RequestURL $request_url,
        public readonly string $delimiter
    ) {
    }
}
