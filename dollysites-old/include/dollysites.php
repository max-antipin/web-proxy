<?php
namespace MaxieSystems;
$config = new DollyConfig('main', INC_DIR.'storage'.DIRECTORY_SEPARATOR.'config.php', ['admin_path' => 'd-admin', ]);
$u = new URL($_SERVER['REQUEST_URI']);
$redirect = false;
if('' === $u->scheme)//Откуда (в каких ситуациях) может быть получен source URL? Не нужно (сразу же ???) добавлять хост к REQUEST URI.
 {
	$u->scheme = Config::GetProtocol();
	if('' !== $u->host)
	 {
		$x = strpos($_SERVER['REQUEST_URI'], '?');
		$u->path = URL\Path::Normalize(false === $x ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 0, $x));
	 }
	$u->host = Config::GetHost();// текущий хост должен быть отдельной переменной !!!
 }
if($config->https_only)
 {
	if('http' === $u->scheme)
	 {
		$u->scheme = 'https';
		$redirect = true;
	 }
 }
$run_admin = null;
if($config->this_host)
 {
	$x = URL\Host::IsSubdomain($u->host, $config->this_host, $label);// админка может быть на поддомене - проверить здесь!!!
	if(false === $x)// проверка хоста: при заходе на "левый" домен выдавать 404 или редирект на основной домен.
	 {
		if($config->GetAdminHost() === $u->host) $run_admin = ['', $u->path];
		// if() RunHandler();
		// elseif() return 404;
		// else $u->host = $config->this_host;$redirect = true;
	 }
	elseif(1 === $x || -1 === $x)
	 {
		if('www' === $label)
		 {
			$u->host = $config->this_host;
			$redirect = true;
		 }
		else ;// проверка хоста: при заходе на "левый" поддомен выдавать 404 или редирект на основной домен.
	 }
 }
if($redirect) $u->Redirect(301);// текущий хост должен быть отдельной переменной !!! И поэтому его нужно явно указывать здесь!
Config::AddAutoload(['dir' => INC_DIR, 'ns' => 'dollysites\\handlers', 'ns_remove' => 0]);// А это точно должно быть здесь, а не там, где загружается класс Engine ???
$p = new URL\Path($u->path);
if(null === $run_admin)
 {
	if($admin_host = $config->GetAdminHost())
	 {
		if($u->host === $admin_host) $run_admin = ['', $u->path];
	 }
	elseif('/' !== $u->path)
	 {
		if($p->StartsWith($config->admin_path, $sub)) $run_admin = [$config->admin_path, $sub];
	 }
 }
if($run_admin)
 {
	require_once(INC_DIR.'admin_config.php');
	$run_admin[] = $p;
	$run_admin[] = $config;
	return RunDollyAdmin(...$run_admin);
 }
// Config::RequireFile('filesystemstorage', 'smauth');
// $smauth = SMAuth();
// try
 // {
	// $smauth->Run();// !!! если это обращение выдаст ошибку, то юзер увидит пустую страницу (или страницу ошибки), что недопустимо
 // }
// catch(EAuthentication $e) {}
if($config->source_scheme && $config->source_host)
 {
// if(!$config->this_host) ;// перейти в настройки сайта. // это не обязательная настройка!!!
	// if(function_exists('set_time_limit')) set_time_limit(15);// 150!!!
// echo '<pre>', GetVarDump($_SERVER['REQUEST_URI']), '</pre>';
	require_once(INC_DIR.'dollysites-old.php');
	$engine = \DollySites\CreateEngine($config);
	$u->path = $p;
	$engine($u);
 }
// elseif($smauth->GetSUID()) HTTP::Redirect(Config::GetMSSMURL('/settings/all/'), 307);// а куда должен быть редирект с учётом изменяющегося адреса админки и потенциально отдельного домена для неё? GetAdminURL ??? Причём, это редирект на один из адресов админки, а не на её главную страницу. Задавать для некоторых пунктов\документов псевдонимы (типа site-url - для раздела с настройкой адреса сайта-источника; вообще, URL Routers и так требуют задания псевдонима для каждого элемента)?
else http_response_code(404);