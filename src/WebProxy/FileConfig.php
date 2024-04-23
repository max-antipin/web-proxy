<?php

namespace MaxieSystems\WebProxy;

class FileConfig extends Config
{
    final public function __construct(string $file_name) {
        $this->data = (require $file_name . '.php');
    }

    final public function __get($name)
    {
        return $this->data[$name] ?? '';
    }

    protected readonly array $data;
}
