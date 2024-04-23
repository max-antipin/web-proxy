<?php

namespace MaxieSystems\WebProxy\ResponseHandler\CSS;

final class Config extends \MaxieSystems\WebProxy\ResponseHandler\Config
{
    public function getContentTypes(): array
    {
        return ['text/css'];
    }
}
