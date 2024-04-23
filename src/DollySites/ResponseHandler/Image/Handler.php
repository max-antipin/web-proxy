<?php

namespace DollySites\ResponseHandler\Image;

use MaxieSystems\WebProxy\ResponseHandler;

class Handler extends ResponseHandler
{
    public function __invoke(string $content): string
    {
        return $content;
    }

    protected function configAction(\MaxieSystems\WebProxy\ResponseHandler\Action $action): Action
    {
        return $action;
    }
}
