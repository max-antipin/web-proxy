<?php

declare(strict_types=1);

namespace MaxieSystems\WebProxy;

use MaxieSystems\Tests\WebProxy\EngineTestTrait;
use MaxieSystems\WebProxy\WebServer\RequestURL;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Engine::class)]
#[CoversClass(Engine\Config::class)]
#[UsesClass(\DollySites\ResponseHandler\Image\Config::class)]
#[UsesClass(\DollySites\ResponseHandler\Image\Handler::class)]
#[UsesClass(ResponseHandler::class)]
#[UsesClass(ResponseHandler\Config::class)]
#[UsesClass(ResponseHandler\CSS\Handler::class)]
#[UsesClass(ResponseHandler\HTML\Handler::class)]
#[UsesClass(ResponseHandler\CSS\Config::class)]
#[UsesClass(ResponseHandler\HTML\Config::class)]
final class EngineTest extends TestCase
{
    use EngineTestTrait;

    public function testAddResponseHandler(): Engine
    {
        $engine = $this->createEngine();
        $config = $engine->addResponseHandler('HTML');
        $this->assertInstanceOf(ResponseHandler\HTML\Config::class, $config);
        $config->addAction('DeleteScripts')
               ->addAction('ReplaceURLs')
               ->addAction('DollySites\\Replace');
        $config = $engine->addResponseHandler('DollySites\\Image');
        $this->assertInstanceOf(\DollySites\ResponseHandler\Image\Config::class, $config);
        $config->addAction('ReduceSize');
        $config = $engine->addResponseHandler('CSS');
        $this->assertInstanceOf(ResponseHandler\CSS\Config::class, $config);
        $config->addAction('ReplaceURLs');
        $ns = 'MaxieSystems\\WebProxy\\';
        $this->assertFalse(class_exists($ns . 'ResponseHandler', false));
        $this->assertFalse(class_exists($ns . 'ResponseHandler\\Action', false));
        return $engine;
    }

    #[Depends('testAddResponseHandler')]
    public function testGetResponseHandler(Engine $engine): void
    {
        $rc = new \ReflectionClass($engine);
        $m = $rc->getMethod('getResponseHandler');
        $this->assertNull($m->invoke($engine, 'application/json'));
        $handler = $m->invoke($engine, 'text/html');
        $this->assertInstanceOf(ResponseHandler\HTML\Handler::class, $handler);
        $handler = $m->invoke($engine, 'image/jpeg');
        $this->assertInstanceOf(\DollySites\ResponseHandler\Image\Handler::class, $handler);
        $handler = $m->invoke($engine, 'text/css');
        $this->assertInstanceOf(ResponseHandler\CSS\Handler::class, $handler);
    }

    #[DataProvider('dataProviderGetSourceURL')]
    public function testGetSourceURL(string $request_url): void
    {
        $url = new RequestURL($request_url);
        $engine = $this->createEngine();
        $source_url = $engine->getSourceURL($url);
    }

    public static function dataProviderGetSourceURL(): array
    {
        return [
            ['http://web-proxy-dev.local:8881/~/www.w3.org~/1mz9bf0y2c~/TR/WD-html40-970917/htmlweb.html'],
            ['https://web-proxy-dev.local:8881/index.html'],
            ['http://web-proxy-dev.local:8881/'],
        ];
    }
}
