<?php

declare(strict_types=1);

namespace MaxieSystems\WebProxy;

use MaxieSystems\Tests\Mock\WebProxy\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

#[CoversClass(Engine::class)]
final class EngineTest extends TestCase
{
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
        $config->addAction('ReduceSize');//->addAction('DollySites\\ReduceSize');
        $config = $engine->addResponseHandler('CSS');
        $this->assertInstanceOf(ResponseHandler\CSS\Config::class, $config);
        $config->addAction('ReplaceURLs');
        $ns = 'MaxieSystems\\WebProxy\\';
        $this->assertFalse(class_exists($ns . 'ResponseHandler', false));
        $this->assertFalse(class_exists($ns . 'ResponseHandler\\Action', false));
        return $engine;
    }

    public function testAddResponseHandlerDuplicateType(): void
    {
        $engine = $this->createEngine();
        $engine->addResponseHandler('HTML');
        $this->expectException(Error\ConfigurationError::class);
        $engine->addResponseHandler('HTML');
    }

    #[Depends('testAddResponseHandler')]
    public function testGetResponseHandler(Engine $engine): void
    {
        $rc = new \ReflectionClass($engine);
        $m = $rc->getMethod('getResponseHandler');
        $m->setAccessible(true);
        $this->assertNull($m->invoke($engine, 'application/json'));
        $handler = $m->invoke($engine, 'text/html');
        $handler = $m->invoke($engine, 'image/jpeg');
        $handler = $m->invoke($engine, 'text/css');
    }

    private function createEngine(): Engine
    {
        /** @var Engine $engine */
        $engine = $this->getMockBuilder(Engine::class)
                       ->onlyMethods(['createProxyFromSource'])
                       ->setConstructorArgs([new Config('', false)])
                       ->getMock();
        $engine->__debugInfo();
        return $engine;
    }
}
