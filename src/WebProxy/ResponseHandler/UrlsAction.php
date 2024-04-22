<?php

namespace MaxieSystems\WebProxy\ResponseHandler;

class UrlsAction extends HtmlAction
{
/*	final public function __invoke(Handlers\WebResource $resource)
	 {
		// Config::RequireFile('urls');
		$groups = new \stdClass();
		$groups->update = [];# Проверяемые тэги делятся на 3 категории: удалить, отложить для модификации (собрать URL), игнорировать.
		$resource->EachNode(function(\DOMNode $node, int &$i, Engine $engine, \stdClass $groups, DebugConsole $console = null){
			if(XML_ELEMENT_NODE === $node->nodeType)
			 {
				if(isset($this->tags[$node->nodeName]))
				 {
					if($nnn = $this->FilterNode($node))# Возвращает удалённый узел. Вычитаем 1, потому что удаляется текущий и последующие, а не предыдущие.
					 {
						if($nnn->isSameNode($node)) --$i;
						return false;
					 }
					$c = $this->tags[$node->nodeName];
					// foreach($node->attributes as $a) var_dump($a->name, $a->value, isset($c['attrs'][$a->name]));
					if(isset($c['attrs'])) foreach($c['attrs'] as $attr) if($node->hasAttribute($attr))
					 {
						$u = $node->getAttribute($attr);
						if('' === $u) continue;
						$url = $engine->GetProxyURL($u);
						if(null === $url) continue;
						$groups->update[] = [$node, $attr, $url];
						if(null !== $console) $console->AddLine($node->nodeName."[$attr]".'  |  '.($url->u_sub < 0 ? 'Внешний домен' : ($url->u_sub > 0 ? 'Поддомен' : 'Основной домен')), $u, $url->u_src, $url, $url->ToAttrValue(), $url->u_modified ? 'Изменён' : 'Без изменений');//, \MaxieSystems\GetVarDump($url)
					 }
				 }
				// else ;// проверка data-атрибутов у всех тэгов???
			 }
			elseif(XML_TEXT_NODE === $node->nodeType) return false;
			elseif(XML_COMMENT_NODE === $node->nodeType)
			 {
				if($nnn = $this->FilterCommentNode($node)) if($nnn->isSameNode($node)) --$i;# Возвращает удалённый узел. Вычитаем 1, потому что удаляется текущий и последующие, а не предыдущие.
			 }
		}, $resource->GetEngine(), $groups, $resource->GetEngine()->GetDebugConsole());
		// var_dump(count($groups->update));
		foreach($groups->update as $j => $n)
		 {
			list($node, $attr, $url) = $n;
			// где-то здесь можно заменять URL полностью либо частично. Например, я хочу обновить скрипт jQuery или добавить его, если нет, вместе с моим кодом.
			if($url->u_modified)
			 {
				if(isset($node->ownerDocument)) $node->setAttribute($attr, $url->ToAttrValue());
				// else echo $url;
			 }
			// echo '<pre>', MS\GetVarDump((string)$url), PHP_EOL, 'type: ', $url->u_sub, ' | mod: ', var_export($url->umod, true), '</pre>';
			// 2. Определить, что с ним делать в зависимости от настроек.
					// здесь настраивается кэширование с других доменов
					// если включено кэширование с других доменов? если URL удовлетворяет условиям кэширования (кэшировать можно только картинки, js, css)
					// это должно быть сделано отдельной функцией, чтобы использовать её и в css файлах, и в иных ресурсах, где есть внешние ссылки
					// здесь также можно менять http на https, если скрипт работает на https
		 }
		// if($this->urls) $this->DispatchEvent('urls:after_create', false, ['urls' => $this->urls]);
	 }

	final public function GetLabel() : string { return 'a02'; }

	final protected function FilterCommentNode(\DOMNode $node) : ?\DOMNode
	 {
		foreach($this->comments as $args)
		 {
			$l = array_shift($args);
			if($this->TestNode_pattern($node, ...$args))
			 {
				$n = $node->nextSibling;
				if($n && XML_TEXT_NODE === $n->nodeType && '' === trim($n->nodeValue)) DOM\Remove($n);
				return DOM\Remove($node, $l);
			 }
		 }
		return null;
	 }

	final protected function FilterNode(\DOMNode $node) : ?\DOMNode
	 {
		$c = $this->tags[$node->nodeName];
		if(isset($c['ptrns']))
		 foreach($c['ptrns'] as $args)
		  {
			$l = array_shift($args);
			if($this->TestNode_pattern($node, ...$args))
			 {
				$n = $node->nextSibling;
				if($n && XML_TEXT_NODE === $n->nodeType && '' === trim($n->nodeValue)) DOM\Remove($n);
				return DOM\Remove($node, $l);
			 }
		  }
		if(isset($c['tests']))
		 foreach($c['tests'] as $test => $args)
		  if($this->{"Test_$test"}($node))
		   {
			return DOM\Remove($node);
		   }
		return null;
	 }

	final protected function TestNode_pattern(\DOMNode $node, bool $rx, string $pattern, string $attr = null, callable $c = null) : bool
	 {
		if($attr)
		 {
			if($node->hasAttribute($attr)) $val = $node->getAttribute($attr);
			else return false;
		 }
		else $val = $node->nodeValue;
		if($rx)
		 {
			if(preg_match($pattern, $val)) return true;
		 }
		elseif(false !== stripos($val, $pattern)) return true;
		return false;
	 }

	private function Test_Jivosite(\DOMNode $n) { return false !== strpos($n->nodeValue, '//code.jivosite.com/script/widget') || preg_match('/\bjivo_onLoadCallback\b/', $n->nodeValue); }

	private $tags = [
		'a' => [
			'attrs' => ['href' => 'href'],
		],
		'link' => [
			'attrs' => ['href' => 'href'],
		],
		'script' => [
			'attrs' => ['src' => 'src'],
			'tests' => ['Jivosite' => [], ],
			'ptrns' => [
				[0, false, 'https://connect.facebook.net/en_US/fbevents.js'],# Facebook
				[0, true, '/\byaCounter[0-9]+\b/'],# Yandex.Metrika
				[0, true, '/\bGoogleAnalyticsObject\b/'],# Google Analytics
				[0, false, '.google-analytics.com/ga.js'],# Google Analytics old
				[0, false, 'googletagmanager.com/gtm.js'],# Google Tag Manager
				[0, false, '//vk.com/rtrg?r='],# VK Pixel Code
				[0, false, '//top-fwz1.mail.ru/js/code.js'],# Rating@Mail.ru
				[0, false, 'Tawk_API'],# Tawk
				[0, false, '//k50-a.akamaihd.net/k50/k50tracker2.js'],# K50
				[0, false, "'cloud.roistat.com'"],# ROISTAT
				[0, false, 'mvk:stories_archive:actions_hint'],# ... VK
			],
		],
		'img' => [
			'attrs' => ['src' => 'src'],
			'ptrns' => [
				[1, false, 'https://www.facebook.com/tr?id=', 'src'],# Facebook
				[2, false, 'https://mc.yandex.ru/watch/', 'src'],# Yandex.Metrika №2
				[2, false, '//top-fwz1.mail.ru/counter', 'src'],# Рейтинг@Mail.ru
			],
		],
		'meta' => [
			'ptrns' => [
				[0, false, 'yandex-verification', 'name'],
				[0, false, 'google-site-verification', 'name'],
			],
		],
		'iframe' => [
			'ptrns' => [
				[1, false, 'googletagmanager.com/ns.html', 'src'],# Google Tag Manager
			],
		],
	];
	private $comments = [
		[0, false, 'Google Tag Manager'],
		[0, false, 'Yandex.Metrika'],
		[0, false, 'Facebook Pixel Code'],
		[0, false, 'VK Pixel Code'],
		[0, false, 'Rating@Mail.ru counter'],
		[0, false, 'Tawk.to'],
		[0, false, 'jivosite'],
		[0, false, 'roistat'],
		[0, false, 'k50 tracker'],
	];
*/
}
