<?php

namespace DollySites\Engine;

use MaxieSystems\URLReadOnly;

final class Config extends \MaxieSystems\WebProxy\Engine\Config
{
    public function __construct(
        public readonly URLReadOnly $scriptURL,
        public readonly bool $useSubdomains,
        public readonly string $delimiter
    ) {
        //parent::__construct($scriptURL, $useSubdomains, $delimiter);
    }
}
