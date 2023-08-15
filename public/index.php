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
if (0 === strpos($_SERVER['REQUEST_URI'], '/site-copy/')
    || 0 === strpos($_SERVER['REQUEST_URI'], '/site/copy/')
    || explode('?', $_SERVER['REQUEST_URI'], 2)[0] === '/site-copy'
    || explode('?', $_SERVER['REQUEST_URI'], 2)[0] === '/site/copy') {// по идее, можно прямо здесь по порту выбирать и подключать скрипт. $serv->request_url->port
    require_once(INC_DIR . 'dollysites.php');
    die;
}

$engine = new WebProxy\EngineImplementation($serv->request_url, new WebProxy\Config());
try {
    $engine();
} catch (WebProxy\Exception\InvalidSourceException $e) {
    // где-то нужно сделать api - адрес для отправки формы с url, после которого происходит редирект.
    if ('POST' === $_SERVER['REQUEST_METHOD']) {
        if (isset($_POST['url'])) {
//https://www.w3.org/TR/WD-html40-970917/htmlweb.html //- здесь на странице есть ftp-ссылки.
//use MaxieSystems\Exception\URL\InvalidHostException;
            $url = new WebProxy\EntryPointURL(trim($_POST['url']));// это - source URL, поскольку он указывает непосредственно на сайт-источник.
  //        if ($engine->getURLConverter($url))
            header('Location: ' . $engine->createProxyFromSource($url), true, 303);
            exit();
        }
        http_response_code(400);
    } else {
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'main.html';
    }
    exit();
}
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
