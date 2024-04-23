<?php

namespace DollySites\ResponseHandler\Image;

final class Config extends \MaxieSystems\WebProxy\ResponseHandler\Config
{
    public function getContentTypes(): array
    {
        return ['image/gif', 'image/jpeg'];
    }
}
