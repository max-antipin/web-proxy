<?php

declare(strict_types=1);

namespace MaxieSystems\WebProxy\Engine;

final class ConfigFilePhp extends ConfigStorage
{
    final public function __construct(string $file_name) {
        $load = fn(string $file_name): array => (require $file_name . '.php');
        $data = $load($file_name);
        $args = [];
        foreach (self::DEFAULTS as $k => $d) {
            if (isset($data[$k])) {
                $v = $data[$k];
                if (is_bool($d) && !is_bool($v)) {
                    $v = boolval($v);
                }
            } else {
                $v = $d;
            }
            $args[$k] = $v;
        }
        parent::__construct(...$args);
    }

    private const DEFAULTS = ['scriptURL' => '', 'useSubdomains' => false, 'delimiter' => ''];
}
