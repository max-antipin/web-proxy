<?php

namespace MaxieSystems\WebProxy;

class Config
{
    public readonly string $script_url;
    public readonly bool $use_subdomains;

    final public function __construct() {
        $this->script_url = getenv('DOLLY_PROXY_SCRIPT_URL');
    }
}
