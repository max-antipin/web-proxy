<?php

namespace DollySites\WebProxy\ResponseHandler;

use MaxieSystems\WebProxy\ResponseHandler;

class ImageHandler
{
    public function __invoke($content)
    {
        return $content;
    }
}
