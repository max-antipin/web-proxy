<?php

namespace MaxieSystems\WebProxy;

abstract class ResponseHandler
{
    abstract public function __invoke(string $content): string;
    abstract protected function configAction(ResponseHandler\Action $action): ResponseHandler\Action;

    final public function addAction(string $action, ...$args): self
    {
        $this->actions[] = $this->configAction(new $action(...$args));
        return $this;
    }

    private array $actions = [];
}
