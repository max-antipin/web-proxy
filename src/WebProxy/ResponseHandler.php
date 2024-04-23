<?php

namespace MaxieSystems\WebProxy;

abstract class ResponseHandler
{
    abstract public function __invoke(string $content): string;
    abstract protected function configAction(ResponseHandler\Action $action): ResponseHandler\Action;

    final public function addAction(string $fqcn, ...$args): self
    {
        $this->actions[] = $this->configAction(new $fqcn(...$args));
        return $this;
    }

    private array $actions = [];
}
