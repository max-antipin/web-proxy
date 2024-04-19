<?php

namespace MaxieSystems\WebProxy\ResponseHandler;

class HtmlHandler
{
    public function __invoke($content)
    {
        return $content;
    }
}
