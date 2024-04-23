<?php

declare(strict_types=1);

namespace MaxieSystems;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

/*const INC_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
require INC_DIR . 'PSR4Autoloader.php';
new PSR4Autoloader([
    'MaxieSystems\\WebProxy' => 'WebProxy',
    'MaxieSystems' => 'mswlib' . DIRECTORY_SEPARATOR . 'src',
    'DollySites' => 'DollySites',
]);*/
$appRoot = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
require_once($appRoot . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');// алгоритм автозагрузки может меняться в зависимости от сборки
$serv = new WebProxy\WebServer\Request();// вероятно, этот функционал стоит перенести в класс WebProxy\App или WebApp
//var_dump((string)$serv->request_url);

$engine = new \MaxAntipin\WebProxy\Engine(new WebProxy\Config($appRoot . 'config'));
$engine->addResponseHandler('HTML')->addAction('Urls');
$engine->addResponseHandler('Css');
try {
    $engine($serv->request_url);
} catch (WebProxy\Exception\InvalidSourceException $e) {
    // где-то нужно сделать api - адрес для отправки формы с url, после которого происходит редирект.
    if ('POST' === $_SERVER['REQUEST_METHOD']) {
        if (isset($_POST['url'])) {
//https://www.w3.org/TR/WD-html40-970917/htmlweb.html //- здесь на странице есть ftp-ссылки.
//use MaxieSystems\Exception\URL\InvalidHostException;
            $url = new WebProxy\URL\EntryPointURL(trim($_POST['url']));// это - source URL, поскольку он указывает непосредственно на сайт-источник.
  //        if ($engine->getURLConverter($url))
            header('Location: ' . $engine->createProxyFromSource($url), true, 303);
            exit();
        }
        http_response_code(400);
    } else {
        require $appRoot . 'templates' . DIRECTORY_SEPARATOR . 'main.html';
    }
    exit();
}
