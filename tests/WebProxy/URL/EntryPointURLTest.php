<?php

declare(strict_types=1);

namespace MaxieSystems\WebProxy\URL;

use MaxieSystems\URLType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EntryPointURL::class)]
##[UsesClass(URL::class)]
##[UsesClass(URL\Path\Segments::class)]
##[UsesClass(URL\Path::class)]
##[UsesClass(Query::class)]
final class EntryPointURLTest extends TestCase
{
    public function testConstruct(): void
    {
        $url = new EntryPointURL('https://example.com/');
        $this->assertSame('https', $url->scheme);
        $this->assertSame('example.com', $url->host);
        $this->assertSame('/', $url->path);
        $this->assertSame('', $url->query);
    }

    #[DataProvider('dataProviderURLsWithTypes')]
    public function testGetType(string $url, URLType $expected): void
    {
        $u = new EntryPointURL($url);
        $this->assertSame($expected, $u->getType());
    }

    public static function dataProviderURLsWithTypes(): array
    {
        return [
            ['https://website.net/index.html', URLType::Absolute],
        ];
    }
}
