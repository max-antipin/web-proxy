<?php

namespace MaxieSystems\WebProxy\ResponseHandler\HTML;

final class Config extends \MaxieSystems\WebProxy\ResponseHandler\Config
{
    public function getContentTypes(): array
    {
        return ['text/html'];
    }
}
