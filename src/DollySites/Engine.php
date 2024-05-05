<?php

namespace DollySites;

use MaxieSystems\URLInterface;
use MaxieSystems\URLReadOnly;
use MaxieSystems\WebProxy;

class Engine extends WebProxy\Engine
{
    public function __construct(object $config)
    {
        parent::__construct($config);
        $this->initSource(new URLReadOnly($config->sourceURL));
/*        if(Config::GetOption('debug'))
         {
            require_once(MS\INC_DIR.'debugconsole.php');
            $this->debug_console = new DebugConsole();
         }*/
        // $scheme = MS\Config::GetProtocol();// схема этого сайта определяется автоматически, поскольку ранее стоит проверка и редирект на https с http.
        // $o = ['type' => 'string,len_gt0'];
        // if(empty($options['useSubdomains']))
         // {
            // $o['value'] = '';
            // $o['__set'] = function(&$v){ if(!$v) $v = $this->GetThisHost(); };
         // }
        // $this->AddOptionsMeta([
            // 'script_host' => $o,
            // 'source_host' => ['type' => 'string', /* '__set' => function(&$v){$v = MS\URLHost::Encode($v);} */],//,len_gt0
            // 'source_scheme' => ['type' => 'string', 'value' => $scheme],
            // 'uri' => ['type' => 'string', 'value' => $_SERVER['REQUEST_URI']],
            // 'base' => ['type' => 'string', 'value' => '', '__set' => function(&$v){
                // $v = "$v";
                // if('' !== $v)
                 // {
                    // if('/' === $v) $v = '';
                    // else
                     // {
                        // $v = URL::Encode($v);
                        // if('/' !== substr($v, 0, 1)) $v = "/$v";
                        // $i = strlen($v) - 1;
                        // if('/' === substr($v, $i, 1)) $v = substr($v, 0, $i);
                     // }
                 // }
            // }],
            // 'cache' => ['type' => 'closure,null'],
            // 'filter_url' => ['type' => 'closure,null'],
            // 'filter_response' => ['type' => 'closure,null'],
            // 'no_handler' => ['type' => 'closure,null'],
            // 'useSubdomains' => ['type' => 'bool', 'value' => false],
        // ]);
        // $this->SetOptionsData($options);
    }

    public function createProxyFromSource(WebProxy\URL\SourceURL $sourceURL): WebProxy\URL\ProxyURL
    {
        return new WebProxy\URL\ProxyURL($sourceURL, $this->config, function(WebProxy\URL\SourceURL $url, string &$host){
            $host;
        });
    }

    final public function __invoke(URLInterface $url)// что он должен возвращать?
    {

        $this->Run($url);
    }

/*    protected function getSource(): URLReadOnly
    {
        return new URLReadOnly($this->config->sourceURL);
    }*/

    final public function GetCache(): ?Cache
    {
        if (false === $this->cache) {
            if ($c = $this->config->cache) {
                require_once(MS\INC_DIR . 'cache.php');
                require_once(MS\INC_DIR . 'cache' . DIRECTORY_SEPARATOR . $c . '.php');
                $c = __NAMESPACE__ . '\\Cache\\' . $c;
                $this->cache = new $c($this->config);
            } else {
                $this->cache = null;
            }
        }
        return $this->cache;
    }
}
