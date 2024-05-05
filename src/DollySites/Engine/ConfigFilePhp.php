<?php

namespace DollySites\Engine;

final class ConfigFilePhp extends ConfigStorage
{
    final public function __construct(string $file_name) {
        $this->data = (require $file_name . '.php');
    }

    protected readonly array $data;
}
