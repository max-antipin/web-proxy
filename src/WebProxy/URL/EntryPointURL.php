<?php

namespace MaxieSystems\WebProxy\URL;

use MaxieSystems\Exception\URL\InvalidSchemeException;
use MaxieSystems\URL;

class EntryPointURL extends SourceURL
{
    protected function onCreate(URL $url): void
    {# или это делать в конструкторе?
        static $schemas = ['https' => 1, 'http' => 1];
        if (empty($schemas[$url->scheme])) {
            throw new InvalidSchemeException();
        }
        # здесь нужно сделать HTTP-запрос с помощью curl, чтобы проверить доступность адреса, а также правильность схемы и хоста (перехватить редирект)
    }
}
