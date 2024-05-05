<?php

declare(strict_types=1);

namespace MaxieSystems\Tests\WebProxy;

use MaxieSystems\WebProxy\Engine;
use MaxieSystems\Tests\Stub\WebProxy\Engine\ConfigStorageStub;

trait EngineTestTrait
{
    protected function createEngine(array $config = ['scriptURL' => '']): Engine
    {
        /** @var Engine $engine */
        $engine = $this->getMockBuilder(Engine::class)
                       ->onlyMethods(['createProxyFromSource'])
                       ->setConstructorArgs([new ConfigStorageStub(...$config)])
                       ->getMock();
        $engine->__debugInfo();
        return $engine;
    }
}
