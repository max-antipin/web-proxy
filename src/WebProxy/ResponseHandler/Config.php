<?php

namespace MaxieSystems\WebProxy\ResponseHandler;

use MaxieSystems\WebProxy\Error\ConfigurationError;

abstract class Config
{
    public function __construct(...$args)
    {
        $class = get_class($this);
        $this->ns = substr($class, 0, strrpos($class, '\\'));
        $this->fqcn = $this->ns . '\\Handler';
        $this->args = $args;
    }

    abstract public function getContentTypes(): array;

    final public function newHandler(): \MaxieSystems\WebProxy\ResponseHandler
    {
        /** @var \MaxieSystems\WebProxy\ResponseHandler $h */
        $h = new $this->fqcn(...$this->args);
        foreach ($this->actions as $a) {
            $h->addAction($a['fqcn']);
        }
        return $h;
    }

    /**
     * @throws \MaxAntipin\WebProxy\Error\ConfigurationError
     */
    final public function addAction(string $name): self
    {
        $pos = strrpos($name, '\\');
        if (false === $pos) {
            $ns = $this->ns;
        } else {
            $ns = substr($name, 0, $pos);
            $name = substr($name, $pos + 1);
            $pos = strrpos($this->ns, '\\ResponseHandler\\');
            if (false !== $pos) {
                $ns .= substr($this->ns, $pos);
            }
        }
        $nameLC = strtolower($name);
        if (isset($this->actions[$nameLC])) {
            throw new ConfigurationError('Duplicate action name: ' . $name);
        }
        $this->actions[$nameLC] = ['fqcn' => $ns . '\\' . $name . 'Action'];
        return $this;
    }

    final public function getNamespace(): string
    {
        return $this->ns;
    }

    private readonly string $ns;
    private readonly string $fqcn;
    private readonly array $args;
    private array $actions = [];
}
