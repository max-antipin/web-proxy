<?php

namespace MaxieSystems\WebProxy\WebServer;

use MaxieSystems\URL;
use MaxieSystems\URLReadOnly;

class RequestURL extends URLReadOnly
{
    public function __construct(string $request_uri)
    {
        parent::__construct($request_uri);
    }

    protected function onCreate(URL $url): void
    {
        if ($url->scheme) {
            // $_SERVER['REQUEST_URI'] содержал протокол
        } elseif ('' !== $url->host) {
            # If scheme is empty and host is not empty then path starts with two slashes //.
            $url->path = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        }
        $url->scheme = $this->getScheme();
        if (!empty($_SERVER['HTTP_HOST'])) {
            //HTTP_HOST может содержать порт: dolly.local:8881, 127.0.0.1:8881 or [2001:db8:85a3:8d3:1319:8a2e:370:7348]:443
            $u = parse_url($url->scheme . '://' . $_SERVER['HTTP_HOST']);
            $url->host = $u['host'] ?? '';
            $url->port = $u['port'] ?? '';
        } else {
            $url->host = $_SERVER['SERVER_NAME'];
        }
    }

    final protected function getScheme(): string
    {
        if (isset($_SERVER['REQUEST_SCHEME'])) {
            return $_SERVER['REQUEST_SCHEME'];
        } elseif (isset($_SERVER['SERVER_PROTOCOL']) && 0 === strpos($_SERVER['SERVER_PROTOCOL'], 'HTTP/')) {
            return !(empty($_SERVER['HTTPS']) || 'off' === $_SERVER['HTTPS'])
                || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'])
                || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && 'on' === $_SERVER['HTTP_X_FORWARDED_SSL'])
                ? 'https' : 'http';
        }
    }
}
