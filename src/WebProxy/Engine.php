<?php

declare(strict_types=1);

namespace MaxieSystems\WebProxy;

use MaxieSystems\WebProxy\URL\ProxyURL;
use MaxieSystems\WebProxy\URL\SourceURL;
use MaxieSystems\URL;
//use MaxieSystems\URLInterface;
use MaxieSystems\URLReadOnly;
use MaxieSystems\WebProxy\Error\ConfigurationError;

abstract class Engine// implements MS\IEvents
{
    public function __construct(Config $config)
    {
        $has_script_url = false;
        $script_url = new URLReadOnly(
            $config->script_url,
            function (URL &$url) use (&$has_script_url): void {
                $u = new URL('');
                if ($has_script_url = ($url->scheme && $url->host)) {
                    $u->copy($url, 'origin');
                /*
                    if (!URL\Host::isIP($u->host)) {
                        $u->host = URL\DomainName($u->host);
                    }
                */
                }
                $u->path = trim($url->path, '/');
                $url = $u;
            }
        );
        $this->config = new EngineConfig(
            $script_url,
            $has_script_url && $config->use_subdomains && ($script_url->host instanceof URL\DomainName),
            '1mz9bf0y2c'
        );
        // Определяем $this_origin, а также источник происхождения: из конфига, или определена косвенно; или иначе: постоянная или переменная.
        // Если переменная, то нельзя использовать поддомены и любое сравнение хостов.
        // Если this host - ip-address, то нельзя использовать поддомены и любое сравнение хостов.
        // Для this origin достаточно задать хост: порт по умолчанию, а протокол по факту.
        $this->schemeHandlers['https'] = $this;
        $this->schemeHandlers['http'] = $this;
    }

    // interface URLConverterInterface??? SchemeHandler & ContentTypeHandler??? HTTPHandler, HTMLHandler
    //public function getProxyURL(ExtractedURL $url): ProxyURL
    //public function URLConverter::sourceToProxy($source_url): ProxyURL
    //public function URLConverter::getProxyFromSource($source_url): ProxyURL
    //public function getSourceURL(URLInterface $request_url): SourceURL
    //public function SourceURL::createFromRequest($request_url)
    // if ($this->isConvertable())
    abstract public function createProxyFromSource(SourceURL $source_url): ProxyURL;

    final public function getSourceURL(WebServer\RequestURL $request_url): SourceURL
    {
//        echo $_SERVER['REQUEST_URI'], '<br />', $request_url, '<br />';
  //      if ($engine->getURLConverter($url))
        if (!isset($this->schemeHandlers[$request_url->scheme])) {// схемы должны быть привязаны к обработчику?
            throw new Exception\UnsupportedSchemeException();
        }
        $url = new URL($request_url);
        if ($this->config->use_subdomains) {
            throw new \Exception('not implemented yet...');
        }// && (string)$this->this_host !== (string)$this->base_url->host
         // {
        // if($url->host) throw new \Exception('not implemented yet...');
            // if($this->this_host->IsIP() || $this->base_url->host->IsIP()) throw new \Exception('not implemented yet...');
            // else
             // {
                // $base_host = (string)$this->base_url->host;
                // if(0 === strpos($base_host, 'www.')) $base_host = substr($base_host, 4);
                // $is_sub = $this->this_host->IsSubdomain($base_host, $label);
                // if(1 === $is_sub)// это другой поддомен. base не удалять, path не дешифровать.
                 // {
                    // $url->host->AddLabel('www', $label);
                    // $u_sub = 1;
                    // return;
                 // }
                // elseif(0 === $is_sub) $u_sub = 0;
                // else return false;
             // }
         // }
        // else
        // URL::IsAbsolute($url, $ut);//'absolute' !== $ut ||
        if ($this->config->script_url->path) {
            // нужно удалять директорию, если задана директория скрипта
            $ends_w_slash = false;
            $url->path = new URL\Path\Segments($url->path, function (string $segment, int $i, int $last_i) use (&$ends_w_slash): ?string {
                if ('' === $segment) {
                    if ($i === $last_i) {
                        $ends_w_slash = true;
                    }
                    return null;
                }
                return $segment;
            });
            $path = null;
            if ($url->path->startsWith($this->config->script_url->path, $path)) {
                $url->path = '/' . $path;// trailing slash!!!
                if ($url->path !== '/' && $ends_w_slash) {
                    $url->path .= '/';
                }
                var_dump($url);
            } else {
                throw new \Exception('not implemented yet...');// здесь должна быть 404-я ошибка.
                // вообще, фактически это недопустимый\некорректный request url.
            }
        }
        if ($this->unpackSourceOriginFromPath($url)) {

        } else {
            $url->copy($this->getSource(), 'origin');
        }
        //var_dump($url);
        return new SourceURL($url);
        $u_sub = 0;
        $u_lbl = null;
        //$url->scheme = $this->source_scheme;
        // для proxy не имеет смысла s (потому что все домены, не от чего отсчитывать поддомен),
        // и не имеет смысла _ (потому что статичного source)
        // в качестве хоста может быть ip
        // Упаковка хоста в path у прокси проще - нет проверки на поддомен.
        // Вместо s & d использовать точку: /~/example.com/ vs /~http/static./
        if (preg_match('#^/(s|d)/(_|http|https)/([a-z0-9_.-]+)/' . $this->config->delimiter . '~(/.*)$#', $url->path, $m)) {
            if ('_' === $m[2]) {
            } else {
                $url->scheme = $m[2];
            }
            $is_subdomain = 's' === $m[1];
            $url->host = $is_subdomain ? URL\Host::AddDomainLabel($this->source_host, 'www', $m[3]) : $m[3];
            $url->path = $m[4];
            if ($is_subdomain) {
                $u_lbl = $m[3];
            }
            $u_sub = $is_subdomain ? 1 : -1;
            // if($u_sub < 0) throw new \Exception('not implemented yet...');// А что здесь было не так? Почему была заглушка?
        } else {
            $url->host = $this->source_host;
        }
        if ('' !== ($dir = $this->GetBaseDir())) { // теперь, если указан каталог, то его нужно "прибавлять" к началу URL path. это только для dolly sites.
            var_dump($dir);
            // if($opt = $url->path->StartsWith($opt, false)) $url->path = $opt;
            // else return false;
        }
        return SourceURL::FromObject($url, true, ['u_sub' => $u_sub, 'u_lbl' => $u_lbl]);
    }

    final public function __debugInfo()
    {
        return [];
    }

    protected function getSource(): URLReadOnly
    {
        return $this->source;
    }

    /**
     * @throws \MaxieSystems\WebProxy\Exception\InvalidSourceException
     */
    final protected function initSource(URLReadOnly $url): void
    {
        if (!$url->scheme || !$url->host) {
            throw new Exception\InvalidSourceException();
        }
        $this->source = $url;
    }

    final protected function unpackSourceOriginFromPath(URL $url): bool
    {
        $ends_w_slash = false;
        $path = new URL\Path\Segments($url->path, function (string $segment, int $i, int $last_i) use (&$ends_w_slash): ?string {
            if ('' === $segment) {
                if ($i === $last_i) {
                    $ends_w_slash = true;
                }
                return null;
            }
            return $segment;
        });
        static $schemas = ['~' => 'https', 'http~' => 'http'];// этот список должен быть динамическим и глобальным. формируется при регистрации обработчика схем.
        if (count($path) >= 3) {
            if (!isset($schemas[$path[0]])) {
                return false;
            }
            if ($path[2] !== $this->config->delimiter . '~') {
                return false;
            }
            if (!str_ends_with($path[1], '~')) {
                return false;
            } else {
                $host = substr($path[1], 0, -1);
                $pos = strrpos($host, ':');
                if (false === $pos) {
                    $port = '';
                } else {
                    $port = substr($host, $pos + 1);
                    $host = substr($host, 0, $pos);
                }
            }
            if (!URL\Host::isDomainName($host) && !URL\Host::isIP($host)) {
                return false;
            }
            $url->scheme = $schemas[$path[0]];
            $url->host = $host;
            $url->port = $port;
            $url->path = (string)$path->slice(3);
            if ($ends_w_slash) {
                $url->path .= '/';
            }
            return true;
        }
        return false;
    }

    final public function addResponseHandler(string $name, ...$args): ResponseHandler\Config
    {
        $pos = strrpos($name, '\\');
        if (false === $pos) {
            $ns = __NAMESPACE__;
        } else {
            $ns = substr($name, 0, $pos);
            $name = substr($name, $pos + 1);
        }
        $fqcn_config = "$ns\\ResponseHandler\\{$name}\\Config";
        /** @var ResponseHandler\Config $config */
        $config = new $fqcn_config($args);
        foreach ($config->getContentTypes() as $type) {
            if (isset($this->responseHandlersByType[$type])) {
                throw new ConfigurationError("Duplicate type: $type [$fqcn_config]");
            }
            $this->responseHandlersByType[$type] = $config;
        }
        $this->responseHandlers[$name] = $config;
        return $config;
    }

    protected readonly EngineConfig $config;
    private readonly URLReadOnly $source;
    private array $schemeHandlers = [];
    private array $responseHandlers = [];
    private array $responseHandlersByType = [];

/*    final public static function CheckScheme(object $url): bool
    {
        return '' === $url->scheme || isset(self::$schemes[$url->scheme]);
    }*/

    # Относительно $this->base_url преобразуются все ссылки в документе, по умолчанию равен request URL, но может быть изменён при разборе документа.
    # Базовый URL определяется сразу после получения контента. Где он используется? Зачем нужен? Например, для преобразования source to proxy.
    final public function GetBaseURL(): URL
    {
        return $this->base_url;
    }

    final public function SetBaseURL(object $url): URL
    {
        if ($url instanceof URL) {
            $uclass = get_class($url);
            $p = $url->GetProperties();
        } else {
            $uclass = 'MaxieSystems\\URL';
            $p = [];
        }
        $k = 'u_sub';
        if (!isset($p[$k])) {
            $p[$k] = isset($url->$k) ? $url->$k : $this->GetUSub($url, $a, $t, $p['u_lbl']);
        }
        // if(!isset($p[$k]))
        // {
            // if(isset($url->$k)) $p[$k] = $url->$k;
            // else
            // {
                // $this->GetUSub($url, $a, $t, $label);
            // }
        // }
        $this->base_url = $uclass::FromObject($url, true, $p, function (string $v, string $k, object $url): string {
            static $idx = ['scheme' => 'source_scheme', 'host' => 'source_host'];
            return isset($idx[$k]) && '' === $v ? $this->{$idx[$k]} : $v;
        });
        // var_dump($this->base_url, $url);
        // $this_host = $this->GetThisHost();
        // if($this_host === $this->base_url->host) $this->base_url->scheme = Config::GetProtocol();// !!! проверить с www. !!!
        // elseif('' === $this->base_url->host || $this->source_host === $this->base_url->host)// !!! проверить с www. !!!
         // {
            // $this->base_url->host = $this_host;
            // $this->base_url->scheme = Config::GetProtocol();
         // }
        // else throw new \Exception('Not implemented yet...');
        return $this->base_url;
    }

    protected function getSourceContent(SourceURL $source_url)
    {
        return file_get_contents((string)$source_url);
    }

    final protected function Run(URLReadOnly $url)// что он должен возвращать?
    {
        static $methods = ['GET' => 'GET', 'POST' => 'POST'];
        if (!isset($methods[$_SERVER['REQUEST_METHOD']])) {
            // здесь можно вписать в лог уведомление об этой ошибке. А вообще, здесь должен выдаваться HTTP 405, только если нужно вывести контент в браузер; а что, если нужно пересохранить страницу в кэш, например?
            http_response_code(405);//throw new \Exception("Unsupported request method: $_SERVER[REQUEST_METHOD]");
            return;
        }
        if ($url->query) {
            $q = $url->query;
            if (
                URL::deleteQueryParameters($q, ['utm_source',// должно браться из настроек!!!
                'utm_key',
                'utm_medium',
                'fbclid',
                'gclid',
                'utm_campaing',
                'yclid',
                '_m_15',
                'mode',
                'action',
                'rand',
                'utm_term',
                'utm_content',
                'utm_campaign',
                'dollyeditor',
                '__dolly_action',
                '_openstat'])
            ) {
                //$url->query = $q;# Указанных параметров не должно быть ни в URL кэша, ни в URL запроса к источнику.
            }
        }
    //    try {
        $src_url = $this->getSourceURL($url);
  //      } catch (Exception $e) {
//        }
        // if(!isset($src_url->u_sub)) throw new \Exception('not implemented yet...');//var_dump($src_url->u_sub);
        /*if (null !== $this->debug_console) {
            $this->debug_console->AddLines(__METHOD__, $url, $src_url, MS\GetVarDump($src_url), $_SERVER['REQUEST_METHOD'], 'HTTP Referer: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '—'), 'HTTP User Agent: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '—'));
        }*/
        //$this->SetBaseURL($src_url);// base URL must be set after content is received.
        // echo '<pre>', var_dump((string)$url), '</pre>', 'URL type: ';
        // здесь проверять наличие папки внутри path// теперь, если указан каталог, то его нужно "прибавлять" к началу URL path - это означает, что копируется некий сайт из папки и переносится в корень этого сайта.
        // if(0 === $type) // распространяется только на этот домен, но не поддомены\внешние.
        // if(false === $this->Uncover($this->source_url, $this->url_type)) HTTP::Status(404);
        // if(null === $this->url_type) throw new \Exception('Invalid URL type: '.MS\Config::GetVarType($this->url_type));
        // if($c = $this->GetOption('filter_url')) $this->ApplyFilter($c, $this->source_url);// фильтр не должен быть здесь, он должен быть при сохранении URL в документе. Но!!! В некоторых случаях здесь может быть фильтрация: например, склейка /index.php, /index.html & /. Но параметры типа fbclid не нужно фильтровать здесь, поскольку они могут быть подставлены вместо оригинальных; а вот те, что открывают редактор или конструктор форм можно фильтровать всегда. А зачем отправлять новый fbclid на сайт-источник? Где удаление "плохих" параметров происходит в dolly 1???
        // $opts = new MS\Containers\Data(['cached' => ['type' => 'bool,null', 'set' => true], 'url_type' => ['type' => 'int', 'value' => $this->url_type]]);
        //echo $src_url;
        $content = $this->getSourceContent($src_url);
        $mime = 'text/html';
        if ($handler = $this->getResponseHandler($mime)) {
            $content = $handler($content);
        }
        echo $content;
        die;
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $response = $this->GetResponse($src_url, $_POST, 'POST');
            // if($c = $this->GetOption('filter_response'))
             // {
                // $r = $c($_SERVER['REQUEST_METHOD'], $response->code, $response->mime, clone $source_url, $url_type, null, $this);
                // if(false === $r) HTTP::Status(404);
                // elseif($r) HTTP::Status($r);
             // }
        } elseif ($cache = $this->GetCache()) {
                // $this->SetBaseURL($url);// когда происходит редирект, кто устанавливает base URL?
            if ($meta = $cache->GetMeta($src_url)) {
                // $response = new HTTPResponse($cache, $meta, $this->url_type);
                // $opts->cached = true;
            } else {
                $response = $this->GetResponse($src_url);//$opts->cached = false;
            }
            // if($c = $this->GetOption('filter_response'))
             // {
                // $use_cache = null !== $opts->cached;
                // $this->ApplyFilter($c, $response, $opts);// кэшированный тоже нужно фильтровать??? это должно быть в другом месте, потому что response получается не только здесь !!!
                // if($use_cache && null === $opts->cached)
                 // {
                    // $cache->Delete($response->url, $opts->url_type);
                    // $response = $this->GetResponse($this->source_url);
                 // }
             // }
                 // var_dump($this->IsCacheable($res));
            // if($this->IsCacheableURL($src_url))
            if ($this->IsCacheable($src_url)) {
                // ;
                $response = true || ($response->http_code >= 300) ? $this->HandleResponse_NoCache($response) : $this->HandleResponse_GET($response);
            } else {
                $response = $this->HandleResponse_NoCache($response);
            }
        } else {
            $response = $this->GetResponse_NoCache($src_url);
        }
        $content = (string)$response;
        foreach ($this->headers as $h) {
            $response->headers->SetHeader($h);
        }
        $response->headers['Content-Length'] = strlen($content);
        http_response_code($response->http_code);# Эту функцию нужно вызывать ДО отправки контента в браузер, как и header().
        // var_dump($response->headers);//->Send()die;// протестировать !!!
        $response->headers->Send();
        die($content);
    }

    final protected function getResponseHandler(string $mime): ?ResponseHandler
    {
        if (isset($this->responseHandlersByType[$mime])) {
            /** @var ResponseHanler\Config $config */
            $config = $this->responseHandlersByType[$mime];
            return $config->newHandler();
        }
        return null;
    }

    final public function getProxyURL(SourceURL $url): ProxyURL
    {
        # Establishing a Base URI - https://datatracker.ietf.org/doc/html/rfc3986#section-5.1
/*        if (!self::CheckScheme($url)) {
            return null;
        }
        //, string $mime
/*      if(URL::IsAbsolute($url, $ut))# absolute or protocol-relative
         {
            # Удаление www. здесь нужно, чтобы правильно обрабатывалась ситуация, когда есть поддомены при source_host, начинающемся с www; например: www.revolted.ru и images.revolted.ru.
            # Строго говоря, images.revolted.ru не является поддоменом www.revolted.ru, но на практике мы считаем иначе.
            $is_sub = URL\Host::IsSubdomain($url->host, 0 === strncasecmp($this->source_host, 'www.', 4) ? substr($this->source_host, 4) : $this->source_host, $label);
            if(0 === $is_sub || ((1 === $is_sub || -1 === $is_sub) && 'www' === $label))# (1\-1 === $is_sub && 'www' === $label) то же самое, что и (0 === $is_sub)
             {
                $u_sub = 0;
                $u_mod = 1;
             }
            elseif(false === $is_sub)# "Внешний" домен: нужна проверка настроек кэширования.
             {
                $u_sub = -1;
                $u_mod = $this->IsCacheableURL_external($url) ? 2 : 0;
             }
            elseif(1 === $is_sub)# Поддомен: URL преобразуется в любом случае, даже если он не кэшируется (например, если указано правило "кэшировать, кроме").
             {
                // if($this->GetOption('use_subdomains')) $new_host = URL::AddDomainLabel($this->base_url->host, 'www', $label);
                $u_sub = 1;
                $u_mod = 2;
             }
            elseif(-1 === $is_sub)
             {
                throw new \Exception('not implemented yet...');
             }
            $u_src = URL::FromObject($url, true);
         }
        else
         {
            $b = $this->GetBaseURL();
            $u_src = URL::ToAbsolute($url, $b, true);
            // var_dump($b, $u_src);echo PHP_EOL;
            $url = clone $u_src;
            $u_sub = 0;
            $u_mod = 1;
         } */
        $u_sub = $this->GetUSub($url, $abs, $ut, $label);
        if ($abs) {# absolute or protocol-relative
            if (0 === $u_sub) {
                $u_mod = 1;
            } elseif (-1 === $u_sub) {# "Внешний" домен: нужна проверка настроек кэширования.
                $u_mod = ('*' === $mime) || $this->IsCacheableURL_external($url, $mime) ? 2 : 0;// Здесь должен подставляться MIME !!!
            } elseif (1 === $u_sub) {# Поддомен: URL преобразуется в любом случае, даже если он не кэшируется (например, если указано правило "кэшировать, кроме").
                // if($this->GetOption('use_subdomains')) $new_host = URL::AddDomainLabel($this->base_url->host, 'www', $label);
                $u_mod = 2;
            }
            $u_src = URL::FromObject($url, true);
        } else {
            $b = $this->GetBaseURL();
            $u_src = URL::ToAbsolute($url, $b, true);//var_dump($url, $b, $u_src);
            $url = clone $u_src;
            if (0 === $b->u_sub) {
                $u_mod = 1;
            } else {
                $u_sub = $b->u_sub;
                $u_mod = 2;
                if (1 === $b->u_sub) {
                    $label = $b->u_lbl;
                }
            }
        }
        // Проверить: Если указана папка, из которой выкачивать сайт, то выполняется проверка URL path для URL с типом 0. В случае находки удаляем соответствующий фрагмент.
        // Если заданы фильтры URL, то применить их. Они применяются после удаления папки из начала URL path, потому что при фильтрации по маске нет смысла указывать удаляемый фрагмент.
        // if(0 === $u_sub)
         // {
            // if('' !== $this->source_dir)
             // {

             // }
         // }
        if ($u_mod > 0) {
            // !!! Исправить! (string)new URL\Path($url->path, Normalize = true)// if(0 <= $u_sub) $url->path = URL\Path\Normalize($url->path);# "Внешние" не трогаем.
            if ($u_mod & 2) {
                if (-1 === $u_sub) {
                    $t = 'd';
                    $h = $url->host;
                } elseif (1 === $u_sub) {
                    $t = 's';
                    $h = $label;
                } else {
                    throw new \Exception('not implemented yet...');
                }
                $url->path = "/$t/" . ('protocol-relative' === $ut ? (-1 === $u_sub ? $this->source_scheme : '_') : $url->scheme) . "/$h/$this->delimiter~$url->path";
            }
            $url->host = $this->GetThisHost();
            $url->scheme = Config::GetProtocol();
        }
        return ProxyURL::FromObject($url, true, ['u_sub' => $u_sub, 'u_src' => $u_src, 'u_modified' => $u_mod > 0, 'u_abs' => $abs]);
    }

    // final public function GetReferer(object $src_url) : string
     // {
        // if(null === $u)
         // {
            // if(isset($_SERVER['HTTP_REFERER'])) $u = $_SERVER['HTTP_REFERER'];
            // else return '';
         // }
     // }

    final public function ConvertReferer(string $u): string
    {
        if ('' === $u) {
            return '';
        }
        $url = URL::Parse($u, $invalid);
        if ($invalid) {
            return '';
        }
        URL::IsAbsolute($url, $type);
        if ('absolute' !== $type || !self::CheckScheme($url)) {
            return '';
        }
        // var_dump($type);
         // {

         // }
        // else
        return URL::Build($url);
    }

    final public function GetUSub(object $url, bool &$abs = null, string &$type = null, string &$label = null): int
    {
        $label = null;// if(!self::CheckScheme($url)) throw new \Exception('Invalid scheme: '.$url->scheme);
        if ($abs = URL::IsAbsolute($url, $type)) {# absolute or protocol-relative
         # Удаление www. здесь нужно, чтобы правильно обрабатывалась ситуация, когда есть поддомены при source_host, начинающемся с www; например: www.revolted.ru и images.revolted.ru.
            # Строго говоря, images.revolted.ru не является поддоменом www.revolted.ru, но на практике мы считаем иначе.
            $is_sub = URL\Host::IsSubdomain($url->host, 0 === strncasecmp($this->source_host, 'www.', 4) ? substr($this->source_host, 4) : $this->source_host, $label);
            if (0 === $is_sub || ((1 === $is_sub || -1 === $is_sub) && 'www' === $label)) {
                return 0;# (1\-1 === $is_sub && 'www' === $label) то же самое, что и (0 === $is_sub)
            } elseif (false === $is_sub) {
                return -1;# "Внешний" домен: нужна проверка настроек кэширования.
            } elseif (1 === $is_sub) {
                return 1;# Поддомен: URL преобразуется в любом случае, даже если он не кэшируется (например, если указано правило "кэшировать, кроме").
            } elseif (-1 === $is_sub) {
                return -1;# Source host является поддоменом хоста проверяемого URL. Например, копируется dolly-source.msapis.com, и на этой странице найдена ссылка https://msapis.com/msse/core.100.js.
            }
        } else {
            return 0;
        }
    }

    final public function GetBaseDir(): string
    {
        return (string)$this->config->base_dir;
    }

    final public function SetHeader(string $name, string $value): Engine
    {
        $this->headers[] = new HTTP\Header($name, $value);
        return $this;
    }

    final public function AddHeaders(string ...$hdrs): Engine
    {
        foreach ($hdrs as $hdr) {
            $h = HTTP\Header::FromString($hdr);
            if (null === $h) {
                continue;
            }
            $this->headers[] = $h;
        }
        return $this;
    }

    final public function GetProxy(): ?HTTP\IProxies
    {
        if ($this->config->proxy_enabled) {
            $fst = new HTTP\Proxies\FileStorage(MS\INC_DIR . 'storage' . DIRECTORY_SEPARATOR . 'proxyservers.php');
            return new HTTP\Proxies($fst->Load());
        }
        return null;
    }

    // final public function IsCacheableURL(HTTP\Result $result) : bool
     // {

     // }

    final public function IsCacheable(URL $url): ?bool
    {
        if ($url->u_sub >= 0) {
            ;
            // var_dump($url->u_sub);
            return true;
        } else {
            return $this->IsCacheableURL_external($url, '');// Здесь должен подставляться MIME !!!
        }
    }

    final public function GetDebugConsole(): ?DebugConsole
    {
        return $this->debug_console;
    }
    final protected function IsCacheableURL_external(\stdClass $url, string $mime): ?bool
    {
        if (($v = (int)$this->config->cache_externals) > 0) {
            return true;
            if ('' === $mime) {# Если MIME не определён "извне", то определяю его по расширению файла.
                if ($url->path && '' !== ($ext = pathinfo($url->path, PATHINFO_EXTENSION))) {// Если используется кэш, то данная ссылка уже может быть в кэше (для внешних - маловероятно, но все же).
                    $mime = MS\GetMIMEType($ext);# https://www.php.net/manual/en/function.mime-content-type
                    if ('' === $mime) {
                        return null;
                    }
                } else {
                    return null;# Если расширения нет, то нельзя определить MIME без доступа к контенту, и неизвестно, что делать с этим URL.
                }
            }
            // if(null === $this->GetCache()) return false;// Эта проверка нужна здесь, если до этого момента нигде не вызывается GetCache(), а он вызывается в __invoke() для выбора алгоритма получения и обработки контента.
            // Но тогда получается, что если убрать эту проверку отсюда, то некоторые "внешние" URL будут преобразовываться даже без кэша???
                    // var_dump(URL::Build($url), $mime);//, $o['mime']);
            foreach ($this->caching_opts as $k => $o) {
                if ($v & $k) {
                    if ($this->ApplyFilter_MIME($mime, $o['mime'])) {
                        return true;
                    }
                    // echo '<br />';
                }
            }
        }
        return false;# По умолчанию не кэшируется контент с "внешних" доменов.
        // если включено кэширование с других доменов? если URL удовлетворяет условиям кэширования (кэшировать можно только картинки, js, css)
        // foreach(['facebook.com', 'vk.com', 'youtube.com', 'yandex.ru'] as $d)
         // {
            // if(0 === $is_sub || 1 === $is_sub) return false;
         // }
    }

    final protected function ApplyFilter_MIME(string $mime, array $types): bool
    {
        if (isset($types[$mime])) {
            return true;// здесь можно сразу же выполнять проверку по маске.
        }
        $m = explode('/', $mime, 2);
        $m[1] = '*';
        $m = implode('/', $m);
        if (isset($types[$m])) {
            return true;// здесь тоже можно сразу же выполнять проверку по маске.
        }
        return false;
    }

    final protected function GetResponse(URL $source_url, array $data = [], $m = 'GET'): HTTP\Response
    {
        if (null === $this->http) {
            $o = ['follow_location' => 10, 'user_agent' => true, ];
            if (isset($_SERVER['HTTP_REFERER'])) {
                $u = $this->ConvertReferer($_SERVER['HTTP_REFERER']);
        // echo GetVarDump($u);
        // echo GetVarDump($this->GetReferer($src_url));
                // var_dump($u);
                //$o['referer'] = ;
            }
            if ($p = $this->GetProxy()) {
                $o['proxy'] = $p;
            }
            $this->http = new HTTP\Request($o);
        }
        $r = $this->http->$m($source_url, $data/* , ['on_redirect' => function($r, $new_url) use($source_url){// #7 (работа с поддоменами) должен выполняться (быть реализован) здесь!!!
                // $source_url = $r->url;// ???
                if($is_sub = $source_url->host->IsSubdomain($new_url->host, $label))
                 {
                    if('www' === $label)
                     {
                        // $new_url->scheme = $this->base_url->scheme;
                        // $new_url->host = $this->base_url->host;
                     }
                    else ;// ???
                 }
                elseif(0 === $is_sub) ;// хосты равны.
                else ;// !!! если хосты совсем не равны.
                if("$source_url->path" !== "$new_url->path")
                 {
                    if(0 === $is_sub || ($is_sub && 'www' === $label))
                     {
                        $new_url->scheme = $this->base_url->scheme;
                        $new_url->host = $this->base_url->host;
                     }
                 }
                if($new_url->modified) HTTP::Redirect("$new_url", $r->code, false);// !!!
            }] */);
        // foreach($r->headers as $k => $v) var_dump($k, $v);
        foreach (['Content-Encoding', 'Content-Length', 'Transfer-Encoding', 'Date', 'X-Powered-By', 'Server', 'Set-Cookie', 'Connection', 'Keep-Alive', 'Cache-Control', 'Pragma', 'Vary', 'Content-Security-Policy'] as $k) {
            unset($r->headers->$k);// !!!
        }
        // var_dump($r->headers['']);
        unset($r->headers['']);
        return $r;
    }

    final protected function GetResponse_NoCache(URL $src_url): object # DollySites\Handlers\WebResource or MaxieSystems\HTTP\Response
    {
        return $this->HandleResponse_NoCache($this->GetResponse($src_url));
    }

    final protected function HandleResponse_NoCache(HTTP\Response $response): object # DollySites\Handlers\WebResource or MaxieSystems\HTTP\Response
    {
        if ($h = $this->GetHandler($response)) {
            $response = $h->NewResource($response);
            foreach ($h as $transform) {
                $transform($response);
            }
        }
        return $response;
    }

    private function HandleResponse_GET(MS\HTTP\Response $response, MS\Containers\Data $opts = null)
    {
        $transform = null;
        if ($h = $this->GetHandler($response, $transform)) {
            if ($opts->cached) {
                if ($transform->objects) {
                     // var_dump($transform);die;
                     // echo 'labels:<br />';
                     // var_dump($transform->labels);
                     // echo '<br />';
                     // echo '<br />';
                    $index = $response->Validate($transform);
                    // $index = $response->GetIndex();
                    // echo '<br />stage index:<br />';
                    // var_dump($index);
                    // die;
                    if (null === $index) {
                        $response = $this->GetResponse($this->source_url);
                        $opts->cached = false;
                        return $this->HandleResponse_GET($response, $opts);
                    } else {
                        if ($index <= $transform->max + 1) {
                            $cache_writer = new CacheWriter($this->GetCache(), $response, $opts->url_type);
                        }
                        $response = new $hc($response, $this);
                        foreach ($transform->objects as $k => $v) {
                            if ($k < $index) {
                                continue;
                            }
                            $v($response);
                            if (0 <= $k && $k <= self::H_MAX_INDEX) {
                                $cache_writer->SetContent($k + 1, $transform->max, $response, $transform->labels[$k]);
                            }
                        }
                    }
                } elseif (null === $response->GetIndex()) {
                    $response = $this->GetResponse($this->source_url);
                    $opts->cached = false;
                    return $this->HandleResponse_GET($response, $opts);
                }
            } else {
                $cache_writer = new CacheWriter($this->GetCache(), $response, $opts->url_type);
                $response = new $hc($response, $this);
                if ($transform->objects) {
                    $prev_k = null;
                    $first = false;
                    foreach ($transform->objects as $k => $v) {
                        if (null === $prev_k) {
                            if ($k >= 0) {
                                $first = true;
                            }
                        } elseif ($prev_k < 0 && $k >= 0) {
                            $first = true;
                        }
                        if (true === $first) {
                            $cache_writer->SetContent(0, $transform->max, $response, null === $prev_k ? '' : $transform->labels[$prev_k]);
                            $first = null;
                        }
                        $v($response);
                        if (0 <= $k && $k <= self::H_MAX_INDEX) {
                            $cache_writer->SetContent($k + 1, $transform->max, $response, $transform->labels[$k]);
                        }
                        $prev_k = $k;
                    }
                    if (false === $first) {
                        $cache_writer->SetContent(0, $transform->max, $response, $transform->labels[$k]);
                    }
                } else {
                    $cache_writer->SetContent(0, 0, $response, $h);
                }
            }
        } elseif (null === $opts->cached) {
            if ($c = $this->GetOption('no_handler')) {
                $this->ApplyNoHandler($c, $response, $opts);
            }
        } elseif ($opts->cached) {
            if (null === $response->GetIndex()) {
                $opts->cached = false;
                return $this->HandleResponse_GET($this->GetResponse($this->source_url), $opts);
            } elseif ($c = $this->GetOption('no_handler')) {
                $this->ApplyNoHandler($c, $response, $opts);
                if (!$opts->cached) {
                    if (null === $opts->cached) {
                        $this->GetCache()->Delete($response->url, $opts->url_type);
                    }
                    return $this->HandleResponse_GET($this->GetResponse($this->source_url), $opts);
                }
            }
        } elseif ($c = $this->GetOption('no_handler')) {
            $this->ApplyNoHandler($c, $response, $opts);
            if (null !== $opts->cached) {
                $cache_writer = new CacheWriter($this->GetCache(), $response, $opts->url_type);
                $cache_writer->SetContent(0, null, $response, null);
            }
        }
        return $response;
    }

    // final private function ApplyFilter($c, ...$args)
     // {
        // $content = $content_type = false;
        // $args[] = $this->url_type;// чтобы ни один идиот не додумался менять $url_type, передаётся его копия
        // $args[] = $_SERVER['REQUEST_METHOD'];
        // $args[] = $this;
        // $args[] = &$content;
        // $args[] = &$content_type;
        // $r = $c(...$args);
        // if(false === $r) HTTP::Status(404);
        // elseif($r)
         // {
            // if($content_type) header("Content-Type: $content_type");
            // HTTP::Status($r, ['exit' => false]);
            // if($content) print($content);
            // exit();
         // }
     // }

    // final private function ApplyNoHandler($c, MS\AbstractHTTPResponse $response, MS\Containers\Data $opts)
     // {
        // return $c($response, $opts, $this);
     // }
    private $http = null;
    private $headers = [];
    private $base_url;
    private $cache = false;
    private $caching_opts = [
        1 => ['mime' => ['image/*' => true], 'title' => 'Images'],
        2 => ['mime' => ['' => true], 'title' => 'Fonts'],
        4 => ['mime' => ['text/css' => true], 'title' => 'CSS'],
        8 => ['mime' => ['application/javascript' => true], 'title' => 'JavaScript'],
    ];
    private $debug_console = null;

    const H_MAX_INDEX = 99;
    // const CACHE_IMAGES = 1;
    // const CACHE_FONTS = 2;
    // const CACHE_CSS = 4;
    // const CACHE_JS = 8;
    // const LABEL_SEPARATOR = ':';
    // const PRM_ACT = '__dolly_action';
}
