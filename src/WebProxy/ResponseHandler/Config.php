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

    final public function addAction(string $name): self
    {
        $this->actions[$name] = $name;
        return $this;
    }

    private readonly array $args;
    private array $actions = [];
}
