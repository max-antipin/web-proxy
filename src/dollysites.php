<?php

declare(strict_types=1);

namespace MaxieSystems;

try {
    $engine = new \DollySites\Engine($serv->request_url,
        (object)[
            'script_url' => ['path' => '/site-copy/'],
            'source_url' => 'https://rncb.ru',
        ]);
} catch (WebProxy\Exception\InvalidSourceException $e) {
    echo 'отправить в админку для ввода адреса сайта';
    exit();
}
$engine($serv->request_url);
