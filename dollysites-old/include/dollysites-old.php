<?php
namespace DollySites;
use \MaxieSystems as MS;

function CreateEngine(object $config) : Engine
{
	require_once(MS\INC_DIR.'engine.php');
	$engine = new Engine($config);
	$h = $engine->AddHandler('HTML', 'text/html');
	// $h->AddTransform(-2, 'RemoveScripts');
	$h->AddTransform(-1, 'ReplaceURLs');
	// $h->AddTransform(-1, 'Compound', ['RemoveScripts'], ['ReplaceURLs']);
	$i = 0;
	// $h->AddTransform($i++, 'Compound', ['Synonymize'], ['Translate', 'ru', 'en'], ['Replacements']);
	// $h->AddTransform($i++, 'Synonymize');
	if($config->translator && $config->translator_source && $config->translator_target && $config->translator_source !== $config->translator_target)
	 {
		$h->AddTransform($i++, 'Translator', $config->translator, $config->translator_source, $config->translator_target);
	 }
	$h->AddTransform($i++, 'Replacements');// что, если проверять, заданы ли вообще замены???// if($->replacements_enabled) - а там нужна общая галочка для полного отключения замен???
	// $smauth = MS\SMAuth();
	$h->AddTransform(100, 'Administrate');// if($smauth->GetSUID()) $h->AddTransform(100, 'Administrate', $smauth->user);// !!! если это обращение выдаст ошибку, то юзер увидит пустую страницу (или страницу ошибки), что недопустимо
	$engine->AddHandler('Image', 'image/gif', 'image/jpeg', 'image/png');
	return $engine;
		// [['text/css'], 'CSS', [['CSSReplacements']]],
		// [['text/javascript', 'application/javascript'], 'JS', [['CSSReplacements']]],
	$o = [
		// 'base' => '/site-dir/',
		// 'salt' => '1a9bf02e',
		// 'use_subdomains' => true,
		'filter_url' => function(MS\URL $source_url, $url_type, $method, Engine $engine, &$content, &$content_type){
			// $content = 'Access denied!';
			// return 403;
			if('POST' === $_SERVER['REQUEST_METHOD']) return;
			if(0 === $url_type && count($source_url->query))
			 {
				foreach([
					'fbclid',
					'gclid',
					'yclid',
					'utm_medium',
					'utm_campaing',
					'utm_term',
					'utm_content',
					'utm_campaign',
					'dollyeditor',
					'utm_source',
					'__dolly_action',
				] as $k) unset($source_url->query->$k);
			 }
		},
		'filter_response' => function(MS\AbstractHTTPResponse $response, $opts, $url_type, $method, Engine $engine, &$content, &$content_type) use($smauth){
			// if('text/html' === $response->mime) $opts->cached = null;
			if('POST' === $method) return;
			if($smauth->GetSUID())
			 {
				$url = MS\Config::ParseURL($_SERVER['REQUEST_URI']);
				if($url->query)
				 {
					$q = new MS\URLQueryRaw($url->query);
					if(isset($q->{$engine::PRM_ACT}))
					 {
						$act = $q->{$engine::PRM_ACT};
						$acts = GetIFrameActions();
						if(isset($acts[$act]))
						 {
							switch($act)
							 {
								case 'forms':
									
									break;
							 }
							unset($q->{$engine::PRM_ACT});
							$url->query = "$q";
							header('Content-Type: text/html; charset=UTF-8', true, 200);
							die(MakeIFrameUI($url, $act, $acts[$act], [], [], [], $smauth->user, $response, $engine));
						 }
					 }
				 }
			 }
			// $content_type = 'application/json';
			// $content = json_encode(['test' => 1]);
			// return 200;
			// if('POST' === $_SERVER['REQUEST_METHOD']) return;
			if(0 === $url_type && 'text/html' === $response->mime)
			 {
				// if(false === $cached) return false;
				// if('GET' === $request_method && 200 === $http_code && isset($_GET['__dolly_action']))
				 // {
					// var_dump($request_method, $http_code, $mime, $response->url, $type, $cached, $engine);die;
				 // }
			 }
		},
		'no_handler' => function(MS\AbstractHTTPResponse $response, MS\Containers\Data $opts, Engine $engine){
			// $engine->SetHeader('x-test', gmdate('r', time() + $t));
			$t = 13 * 3600;
			$engine->SetHeaders('x-test: '.gmdate('r', time() + $t), 'x-my-header: true value');
			// $engine->SetHeaders('x-test1234abcd');
			// $engine->SetHeader('x-test: 1234abcd');
			// var_dump($opts);
			// if('text/css' === $response->mime)
			 // {
				// if(null !== $opts->cached)
				 // {
					// if(true === $opts->cached)
					 // {
						// $opts->cached = null;
						// $cached = !$response->Expired('1m');
					 // }
				 // }
			 // }
			// var_dump($response, $opts->cached, $opts->url_type);
			// return;
			$types = ['image/png' => 'image/png', 'image/jpeg' => 'image/jpeg', 'image/gif' => 'image/gif'];
			if(isset($types[$response->mime]))
			 {
				$t = 13 * 3600;
				// $engine->SetHeader('Last-modified', gmdate('r', $last_modified));
				// $engine->SetHeader('Last-modified', gmdate('r', time()), true);
				// $engine->SetHeader('Expires', gmdate('r', time() + $t), true);
				// return false;
				$response->headers->{'Last-modified'} = gmdate('r', time());
				$response->headers->{'x-my-header'} = 'true value';
				// $engine->SetCharset('utf-8');
				// $last_modified = filemtime($img);
		// $etag = sha1_file($img);
		// if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified || trim(@$_SERVER['HTTP_IF_NONE_MATCH']) == $etag)
		 // {
			// header('Last-modified: '.gmdate('r', $last_modified), true, 304);
			// return false;
		 // }
		// $days = 31536000;
		// header('Last-modified: '.gmdate('r', $last_modified), true, 200);
		// header('Expires: '.gmdate('r', time() + $days));
		// header('Cache-Control: private, max-age='.$days);
		// header('Etag: '.$etag);
			 }
		},
	];
}
return;
class SelectLang extends MS\L10N\SelectLanguage
{
	final public function GetLang()
	 {
		if(null === $this->lang)
		 {
			$conf = MS\SimpleConfig::Instance('main');
			$auth = MS\SMAuth();
			if($auth->user)
			 {
				if($auth->user->language && isset($this->data->{$auth->user->language}))
				 {
					if(!$conf->language || $conf->language !== $auth->user->language) $conf->language = $auth->user->language;
					return ($this->lang = $auth->user->language);
				 }
			 }
			$lang = $conf->language;
			if($lang && isset($this->data->$lang)) return $lang;
			foreach($this->ParseAcceptLanguage() as $lang) if(isset($this->data->$lang)) return $lang;
		 }
		return $this->lang;
	 }

	private $lang = null;
}

trait RemoteAPI
{
	final public function API_Auth()
	 {
		$r = $this->REST_Key()->POST('auth', ['uid' => $_POST['uid'], 'password' => $_POST['password']]);
		if(200 === $r->code)
		 {
			if($r->value) return $r->value->user;
		 }
		elseif(403 === $r->code);
		else throw new \Exception("HTTP error: $r->code");
	 }

	final public function API_AuthUser($suid, $uid)
	 {
		$r = $this->REST_Key()->GET('auth/user', ['suid' => $suid, 'uid' => $uid]);
		if(200 === $r->code)
		 {
			if($r->value) return $r->value->user;
		 }
		elseif(403 === $r->code);
		else throw new \Exception("HTTP error: $r->code");
	 }

	// final public function API_()
	 // {
		
	 // }

	// final public function API_()
	 // {
		
	 // }

	final private function REST_Key()
	 {
		if(null === $this->rest__key)
		 {
			MS\Config::RequireFile('rest');
			$this->rest__key = new MS\REST(\IConst::API_URL, ['headers' => ['X-Product-Key: '.\IConst::PRODUCT_KEY, 'X-Product-Version: '.\IConst::PRODUCT_VERSION]]);
		 }
		return $this->rest__key;
	 }

	private $rest__key = null;
}

class AuthStorage extends MS\AuthenticatorStorage
{
	use RemoteAPI;

	final public function __construct(MS\Authenticator $owner)
	 {
		parent::__construct($owner);
		MS\Config::RequireFile('db', 'filesystemstorage');
		$prefix = $this->GetOwner()->GetPrefix();
		$dir = 'storage'.DIRECTORY_SEPARATOR;
		$o = ['root' => MS\INC_DIR, 'readonly' => false];
		// $this->user_data = new FileSystemStorage($dir.$prefix.'_data.php', $o);
		// $this->user_denial = new FileSystemStorage($dir.$prefix.'_denial.php', $o);
		$this->user_session = new MS\FileSystemStorage($dir.$prefix.'_session.php', $o);
	 }

	final public function GetInstanceByUID($uid, $password)
	 {
		if($data = $this->API_Auth())
		 {
			$_SESSION['uid'] = $data->uid;
			return new MS\AuthenticationData($data, $this);
		 }
	 }

	final public function GetInstanceByKey($value, $field)
	 {throw new \Exception('not implemented yet: '.__METHOD__);
		// $res = DB::Select($this->owner->GetDataTName(), '*', "`$field` = ?", [$value]);
		// $n = $res);
		// if(1 === $n) return $this->Init($res->Fetch());
		// elseif(1 < $n) throw new EAuthenticationDuplicateEntries();
	 }

	final public function GetInstanceBySID($sid)
	 {
		if(isset($this->user_session->$sid) && isset($_SESSION['uid']))
		 {
			$session = $this->user_session->$sid;
			if($row = $this->API_AuthUser($session->suid, $_SESSION['uid']))
			 {
				$row->session__id = $session->id;
				$row->session__length = $session->length;
				$row->session__last_visit = $session->last_visit;
				return new MS\AuthenticationData($row, $this);
			 }
		 }
	 }

	final public function AddDenial($ip)
	 {
		 // throw new \Exception('not implemented yet: '.__METHOD__);
		// $ex = isset($this->user_denial->$ip);
		// $row = $this->user_denial->$ip;
		// $row->date_time = time();
		// if($ex) ++$row->count;
		// return $row->count;
	 }

	final public function GetDenial($ip) {return;throw new \Exception('not implemented yet: '.__METHOD__); if(isset($this->user_denial->$ip)) return $this->user_denial->$ip; }
	final public function RemoveDenial($ip) {return;throw new \Exception('not implemented yet: '.__METHOD__); if(isset($this->user_denial->$ip)) return $this->user_denial->$ip; }
	final public function GetSalt($i) { return $i ? '1173e9ca6c8e185d59f33b3ceace65cd83b79' : '5b4a20cb20ffe17faed23fd3dec'; }

	final public function GetUsersByCol($col, $value) {throw new \Exception('not implemented yet: '.__METHOD__);}
	final public function ValueExists($key, $value) {throw new \Exception('not implemented yet: '.__METHOD__);}
	final public function SetUniqID(\stdClass $user) {throw new \Exception('not implemented yet: '.__METHOD__);}
	final public function SetValueUnique(\stdClass $user, $field, $hash = 'sha256') {throw new \Exception('not implemented yet: '.__METHOD__);}

	final public function Update(array $values, \stdClass $user)
	 {
		// $row = $this->user_data->{$user->suid};
		// foreach($values as $k => $v) $row->$k = $v;
		return 1;
	 }

	final public function UpdateByUniqID($uniqid, array $values, array $o = null) {throw new \Exception('not implemented yet: '.__METHOD__);}
	final public function DeleteSession($qty, MS\AuthenticationSession $session)
	 {
		if('all' === $qty)
		 {
			foreach($this->user_session as $k => $sess) if($session->suid == $sess->suid) unset($this->user_session->$k);
		 }
		elseif(null !== $session->id)
		 {
			if('this' === $qty)
			 {
				if(isset($this->user_session->{$session->id}) && $session->suid == $this->user_session->{$session->id}->suid) unset($this->user_session->{$session->id});
			 }
			elseif('other' === $qty)
			 {
				foreach($this->user_session as $k => $sess) if($k != $session->id && $session->suid == $sess->suid) unset($this->user_session->$k);
			 }
		 }
	 }

	final public function ReplaceSession(MS\AuthenticationSession $session)
	 {
		if($session->id)
		 {
			$row = $this->user_session->{$session->id};
			$row->suid = $session->suid;
			$row->length = $session->length;
			// '=last_visit' => 'NOW()';
			$this->user_session->Save();
		 }
	 }

	private $user_data;
	// private $user_denial;
	private $user_session;
}

function GetAdminMenu()
{
	return [
		'dashboard' => ['', l10n()->dashboard],
		// 'editor' => l10n()->file_editor,
		// 'scripts' => l10n()->scripts,
		'replacements' => ['replacements', l10n()->text_replacements],
		'content' => ['content', l10n()->content],
		'images' => ['images', l10n()->images],
		'proxy' => ['proxy', l10n()->proxy],
		'forms_handlers' => ['forms-handlers', l10n()->forms_handler],
		'get_archive' => ['get-archive', l10n()->get_archive],
		'cache' => ['cache', l10n()->cache],
		'cache_settings' => ['cache/settings', l10n()->cache_settings, 'cache', ['type' => 'small settings']],
		'settings' => ['settings', l10n()->settings, false, ['type' => 'icon settings']],
		'cp' => ['cp', l10n()->control_panel, 'https://cp.dollysites.com/', false, ['type' => 'icon users', 'target' => false], 'external' => true],
		'change_password' => ['change-password', l10n()->auth_prefs, 'https://cp.dollysites.com/change-password/', 'cp', ['type' => 'small keys', 'target' => false], 'external' => true],
		'php_info' => ['php-info', l10n()->php_info],
	];
}

function WalkAdminMenuItems(\closure $callback, $css_class)
{
	foreach(GetAdminMenu() as $k => $v)
	 {
		if($external = isset($v['external']))
		 {
			$href = $v[2];
			unset($v[2]);
			unset($v['external']);
		 }
		else $href = MS\Config::GetMSSMURL().($v[0] ? "$v[0]/" : '');
		$callback($k, $external, ['href' => $href, 'class' => $css_class.'__item', /* 'type' => 'button' */], ...$v);
	 }
}

function GetIFrameActions()
{
	static $acts = null;
	if(null === $acts) $acts = ['editor' => l10n()->wysiwyg_editor, 'forms' => l10n()->forms_constructor];
	return $acts;
}

function GetAdminMenuElements(MS\AuthenticationData $user, $prefix, $response, Engine $engine)
{
	$l = l10n();
	$c = $prefix.'links_list';
	$hc = $c.'__item';
	$menu = [// DOMElement
		'tagName' => 'div',
		'attributes' => ['class' => $c.'__inner'],
		'childNodes' => [],
	];
	if(200 == $response->code)
	 {
		$items = GetIFrameActions();
		$url = MS\Config::ParseURL($_SERVER['REQUEST_URI']);
		$selected = null;
		if($url->query)
		 {
			$q = new MS\URLQueryRaw($url->query);
			if(isset($q->{Engine::PRM_ACT})) $selected = $q->{Engine::PRM_ACT};
			foreach($items as $k => $v)
			 {
				$q->{Engine::PRM_ACT} = $k;
				$url->query = "$q";
				$a = ['href' => MS\Config::BuildURL($url), 'class' => $hc];
				if($selected === $k) $a['data-state'] = 'selected';
				$menu['childNodes'][] = ['tagName' => 'a', 'attributes' => $a, 'childNodes' => [$v]];
			 }
		 }
		else
		 {
			foreach($items as $k => $v)
			 {
				$url->query = Engine::PRM_ACT.'='.$k;
				$menu['childNodes'][] = ['tagName' => 'a', 'attributes' => ['href' => MS\Config::BuildURL($url), 'class' => $hc], 'childNodes' => [$v]];
			 }
		 }
	 }
	$blocks = [];
	WalkAdminMenuItems(function($k, $external, array $attrs, $id, $title, $pid = false, array $o = null) use(&$menu, $c, &$blocks){
		if($o)
		 {
			if(!empty($o['type'])) $attrs['data-type'] = $o['type'];
		 }
		$menu['childNodes'][] = ['tagName' => 'div', 'attributes' => ['class' => $c.'__block'], 'childNodes' => [
			['tagName' => 'a', 'attributes' => $attrs, 'childNodes' => [$title]],
		]];
		$blocks[$id] = count($menu['childNodes']) - 1;
	}, $c);
	$menu['childNodes'][] = [
		'tagName' => 'div',
		'attributes' => ['class' => $c.'__user'],
		'childNodes' => [
			['tagName' => 'a', 'attributes' => ['href' => MS\Config::GetMSSMURL().'?__mssm_auth_action=logout', 'class' => $c.'__logout', 'title' => $l->logout], 'childNodes' => [$l->logout]],
			$user->uid,
		],
	];
	$menu['childNodes'][] = [
		'tagName' => 'div',
		'attributes' => ['class' => $c.'__bottom'],
		'childNodes' => [
			200 == $response->code ? ['tagName' => 'button', 'attributes' => ['type' => 'button', 'class' => $c.'__clear_cache'], 'childNodes' => [$l->clear_cache]] : 'HTTP status: '.$response->code,
		],
	];
	return [
		'tagName' => 'div',
		'attributes' => ['class' => $c],
		'childNodes' => [
			['tagName' => 'button', 'attributes' => ['type' => 'button', 'class' => $c.'__toggle', 'data-state' => 'closed'], 'childNodes' => []],
			$menu,
		],
	];
}

function GetAdminMenuAsString(MS\AuthenticationData $user, $prefix, $response, Engine $engine)
{
	$f = function(array $el) use(&$f){
		$a = $h = '';
		foreach($el['attributes'] as $k => $v) $a .= " $k='$v'";
		foreach($el['childNodes'] as $v) $h .= is_string($v) ? $v : $f($v);
		return "<$el[tagName]$a>$h</$el[tagName]>";
	};
	return $f(GetAdminMenuElements($user, $prefix, $response, $engine));
}

function MakeIFrameUI(\stdClass $url, $action, $document_title, array $css, array $js, array $data, MS\AuthenticationData $user, $response, Engine $engine)
{
	$l = l10n();
	$o = new MS\Containers\Options($data, [
		'body_content' => ['type' => 'string', 'value' => ''],
		'js_inline' => ['type' => 'string', 'value' => ''],
		'status_type' => ['type' => 'string', 'value' => ''],
		'status_msg' => ['type' => 'string', 'value' => ''],
		'toolbar_content' => ['type' => 'string', 'value' => ''],
	]);
	array_unshift($js, \IConst::JQUERY, 'iframeui');
	array_unshift($css, 'iframeui');
	$st = $o->status_type ? " data-status='$o->status_type'" : '';
	$html_head = '';
	$f = [];
	foreach($js as $src)
	 {
		if(false === strpos($src, '//')) $src = "https://api.dollysites.com/content/$src.js";
		if(empty($f[$src]))
		 {
			$html_head .= "<script type='text/javascript' src='$src'></script>";
			$f[$src] = true;
		 }
	 }
	$f = [];
	foreach($css as $src)
	 {
		if(false === strpos($src, '//')) $src = "https://api.dollysites.com/content/$src.css";
		if(empty($f[$src]))
		 {
			$html_head .= "<link rel='stylesheet' href='$src' type='text/css' media='all' />";
			$f[$src] = true;
		 }
	 }
	if($src = trim($o->js_inline)) $html_head .= "<script type='text/javascript'>/* <![CDATA[ */ $src /* ]]> */</script>";
	$links = GetAdminMenuAsString($user, '', $response, $engine);
	$src = MS\Config::BuildURL($url);
	return "<!DOCTYPE html>
<html lang='{$l->GetLang()}' prefix='og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#'>
<head>
<meta charset='utf-8' />
<title>$document_title</title>
<meta name='robots' content='noindex, nofollow' />
$html_head
</head>
<body data-action='$action'>$links
<div class='toolbar _top'>
	<div class='toolbar__row'>$o->toolbar_content</div>
	<div class='status_msg'$st>$o->status_msg</div>
</div>
<a href='$src' class='toolbar__action_2 _close _loading' title='$l->close'>$l->close</a>
<div class='page_container'><iframe name='page_container' src='$src'></iframe></div>
$o->body_content
</body>
</html>";
}

trait TReplacements
{
	final public function Save(array $data)
	 {
		if($data == $this->Load()) return false;
		return file_put_contents($this->file_name, '<?php'.PHP_EOL.'return '.var_export($data, true).PHP_EOL.'?>');
	 }

	final public function Load()
	 {
		if(file_exists($this->file_name))
		 {
			$r = (require $this->file_name);
			if($r && is_array($r)) return $r;
		 }
	 }

	private $file_name = MS\INC_DIR.'storage'.DIRECTORY_SEPARATOR.'replacements.php';
}
?>