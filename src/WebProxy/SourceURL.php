<?php

namespace MaxieSystems\WebProxy;

use MaxieSystems\Exception\URL\InvalidDomainNameException;
use MaxieSystems\Exception\URL\InvalidHostException;
use MaxieSystems\Exception\URL\InvalidSchemeException;
use MaxieSystems\URL;
use MaxieSystems\URLReadOnly;

class SourceURL extends URLReadOnly
{
    protected function onCreate(URL $url): void
    {
        if (!$url->scheme) {
            throw new InvalidSchemeException();
        }
        if (!URL\Host::isIP($url->host)) {
            // idna convert here; $value = idna::encode($value)
            try {
                $url->host = new URL\DomainName($url->host);
            } catch (InvalidDomainNameException $e) {
                throw new InvalidHostException('', 0, $e);
            }
        }
        $url->path = URL::encode($url->path);
    }
}
