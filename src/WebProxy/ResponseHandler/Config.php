<?php

namespace MaxieSystems\WebProxy\ResponseHandler;

abstract class Config
{
    public function __construct(private readonly string $fqcn, ...$args)
    {
        $this->args = $args;
    }

    abstract public function getContentTypes(): array;

    final public function newHandler(): object
    {
        return new $this->fqcn(...$this->args);
    }

    private readonly array $args;
}
