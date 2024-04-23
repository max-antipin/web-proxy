<?php

namespace MaxieSystems\WebProxy;

class Config
{
    final public function __construct(string $file_name) {
        $this->data = (require $file_name . '.php');
    }

    protected readonly array $data;
}
