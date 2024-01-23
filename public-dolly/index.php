<?php

declare(strict_types=1);

namespace MaxieSystems;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

const INC_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
require INC_DIR . 'PSR4Autoloader.php';
new PSR4Autoloader([
    'MaxieSystems\\WebProxy' => 'WebProxy',
    'MaxieSystems' => 'mswlib' . DIRECTORY_SEPARATOR . 'src',
    'DollySites' => 'DollySites',
]);

$serv = new WebProxy\WebServer\Request();
//var_dump((string)$serv->request_url);
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

/* require_once('.'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'sys_config.php');
set_error_handler(['Config', 'HandleError'], E_STRICT & E_DEPRECATED & E_USER_DEPRECATED);// !!!
Config::DisplayErrors(true);// !!!
Config::SetOption('debug', true);// !!!
Config::SetOption('e_root_relative_paths', true);
Config::RequireFile('estreams', 'simpleconfig', 'url');
class DollyConfig extends SimpleConfig
{
    const PRODUCT_KEY = '4e3c92b876dc070c2a7dccd592f51b5900';
    const PRODUCT_VERSION = '2.0.0 b1';
    const API_HOST = 'api-dollysites.msapis.com';
    const CP_HOST = 'cp-dollysites.msapis.com';

    final public function GetAdminHost() : string
     {
        if($this->admin_host)
         {
            if('~' === $this->admin_host && $this->this_host)
             {
                return $this->admin_path.'.'.$this->this_host;
             }
            return $this->admin_host;
         }
        return '';
     }
}
Config::SetErrorStreams(new EStream(DollyConfig::API_HOST, function(array &$h){ $h[] = 'X-Product-Key: '.DollyConfig::PRODUCT_KEY; }, true), new DebugEStream());// !!! На API test сделать вывод запроса, как на msapis.com - взять код оттуда.
Config::AddSoftware('DollySites', DollyConfig::PRODUCT_VERSION);
require_once(INC_DIR.'dollysites.php'); */
