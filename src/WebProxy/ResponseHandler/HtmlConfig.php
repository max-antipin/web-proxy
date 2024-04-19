<?php

namespace MaxieSystems\WebProxy\ResponseHandler;

final class HtmlConfig extends Config
{
    public function getContentTypes(): array
    {
        return ['text/html'];
    }
}
