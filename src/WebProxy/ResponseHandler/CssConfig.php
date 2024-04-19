<?php

namespace MaxieSystems\WebProxy\ResponseHandler;

final class CssConfig extends Config
{
    public function getContentTypes(): array
    {
        return ['text/css'];
    }
}
