<?php

namespace MaxieSystems\WebProxy\URL;

use MaxieSystems\URL;
use MaxieSystems\URLReadOnly;
use MaxieSystems\WebProxy\EngineConfig;

/**
 * Proxy URL всегда указывает на хост скрипта
 */
class ProxyURL extends URLReadOnly
{
    final public function __construct(
        private readonly SourceURL $source,
        private readonly EngineConfig $config,
        callable $on_create = null
    ) {
        if (null === $on_create) {
            $this->on_create = null;
        } else {
            $this->on_create = ($on_create instanceof \Closure) ? $on_create : $on_create(...);
        }
        parent::__construct($source);
    }

    final protected function onCreate(URL $url): void
    {
        $host = $url->host;
        $transform = true;
        if (null !== $this->on_create) {
            if (false === $this->on_create->__invoke($this->source, $host)) {
                $transform = false;
            }
        }
        if ($transform) {
            if ($this->config->use_subdomains) {
                $new_host = $this->addSourceOriginToSubdomain($url->scheme, $host, $url->port);
            } else {
                $url->path = $this->addSourceOriginToPath($url->scheme, $host, $url->port, $url->path);
            }
        }
        if ($this->config->script_url->path) {
            $url->path = '/' . $this->config->script_url->path . '/' . $url->path;
        }
        $url->copy($this->config->script_url, 'origin');
        if ($transform && $this->config->use_subdomains) {
            $url->host = $new_host;
        }
    }

    final protected function addSourceOriginToPath(string $scheme, string $host, int|string $port, string $path): string
    {
        $p = [
            '',
            ('https' === $scheme ? '' : $scheme) . '~',
            $host . ($port ? ':' . $port : '') . '~',
            $this->config->delimiter . '~',
        ];
        $p = implode('/', $p);
        if (URL::isPathRootless($path)) {
            $p .= '/';
        }
        return URL::encode($p . $path);
    }

    final protected function addSourceOriginToSubdomain(string $scheme, string $host, int|string $port): string
    {
        if (str_ends_with($host, '.')) {
            $sub = $host;
        } else {}
        // base64 (origin) and apply as subdomain
        /*function base64_encode_url($string) {
            return str_replace(['+','/','='], ['-','_',''], base64_encode($string));
        }
        // добавить это в класс URL???
        function base64_decode_url($string) {
            return base64_decode(str_replace(['-','_'], ['+','/'], $string));
        }*/
        return $sub . $this->config->script_url->host;
    }

    private readonly ?\Closure $on_create;
}
