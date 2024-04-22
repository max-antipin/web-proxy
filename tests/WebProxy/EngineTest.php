<?php

declare(strict_types=1);

namespace MaxieSystems\WebProxy;

use PHPUnit\Framework\TestCase;

final class EngineTest extends TestCase
{
    public function testAutoload(): void
    {
        $this->markTestIncomplete('TODO: check if classes are loaded in a certain sequence');
    }

    public function testAddResponseHandler(): void
    {
        $engine = $this->createEngine();
        $config = $engine->addResponseHandler('HTML');
        $this->assertInstanceOf(ResponseHandler\Config::class, $config);
    }

    private function createEngine(): Engine
    {
        /** @var Engine $engine */
        $engine = $this->getMockBuilder(Engine::class)
                       ->onlyMethods(['createProxyFromSource'])
                       ->setConstructorArgs([new \stdClass])
                       ->getMock();
        $engine->__debugInfo();
        return $engine;
    }
}
