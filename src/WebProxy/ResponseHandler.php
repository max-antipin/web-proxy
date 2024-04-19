<?php

namespace MaxieSystems\WebProxy;

abstract class ResponseHandler
{
    abstract public function __invoke(string $content): string;
}
