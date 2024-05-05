<?php

declare(strict_types=1);

namespace MaxieSystems\WebProxy;

use MaxieSystems\Tests\WebProxy\EngineTestTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Engine::class)]
#[UsesClass(Engine\Config::class)]
#[UsesClass(ResponseHandler\Config::class)]
#[UsesClass(ResponseHandler\HTML\Config::class)]
final class EngineErrorsTest extends TestCase
{
    use EngineTestTrait;
    /*public function testAddResponseHandler(): Engine
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
    }*/

    #[TestDox('Forbidden to add more than one handler for each content type.')]
    public function testAddResponseHandlerDuplicateType(): void
    {
        $engine = $this->createEngine();
        $engine->addResponseHandler('HTML');
        $this->expectException(Error\ConfigurationError::class);
        $engine->addResponseHandler('HTML');
    }

    /*#[Depends('testAddResponseHandler')]
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
    }*/
}
