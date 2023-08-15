<?php

namespace MaxieSystems\WebProxy\WebServer;

class Request
{
    //var_dump($_SERVER);
    // Как быть, когда нужны разные классы request_uri - если делаем routing, то нужны path segments.
    //https://github.com/symfony/symfony/blob/6.3/src/Symfony/Component/HttpFoundation/Request.php
    public function __construct()
    {
        // $this->_server = $_SERVER;
        $this->request_url = new RequestURL($_SERVER['REQUEST_URI']);
        $this->async = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 0 === strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest');
        //REDIRECT_STATUS
    }

/*function GetIP() : ?string
{
    if(!empty($_SERVER['REMOTE_ADDR'])) return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
}*/
    public readonly bool $async;
    public readonly RequestURL $request_url;
}
