<?php

namespace MaxieSystems\WebProxy;

use MaxieSystems\WebProxy\URL\ProxyURL;
use MaxieSystems\WebProxy\URL\SourceURL;
use MaxieSystems\URLReadOnly;

class EngineImplementation extends \MaxieSystems\WebProxy\Engine
{
/*    public function __construct(URLReadOnly $request_url, object $config)
    {
        parent::__construct($request_url, $config);
    }*/

    protected function getSource(): URLReadOnly
    {
        throw new Exception\InvalidSourceException();
    }

    public function createProxyFromSource(SourceURL $source_url): ProxyURL
    {
        return new ProxyURL($source_url, $this->config);
    }

    final public function __invoke(WebServer\RequestURL $request_url)// что он должен возвращать?
    {
/*        foreach ([
            '',
            '/',
            '/#primary-nav',
            '/my-path-to-something/?test=true',
            'https://google.com',
            'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js',
            'http://158.160.118.134:8081',
            'http://158.160.118.134:8081?query=true',
            'http://wiki.advantshop.net/wiki/208',
            '//dollysites.com/',
            'https://работа.рф/вакансии',
        ] as $url) {
            $ex_url = new URL($url);
            if ($ex_url->isAbsolute()) {
                $src_url = new SourceURL($ex_url);
                echo $this->createProxyFromSource($src_url);
                echo '<br />';
            }
        }*/
        $this->Run($request_url);
    }
}
