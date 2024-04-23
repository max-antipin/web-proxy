<?php

namespace MaxieSystems\WebProxy\ResponseHandler\HTML;

class Handler extends \MaxieSystems\WebProxy\ResponseHandler
{
    public function __invoke(string $content): string
    {
        return $content;
    }

    protected function configAction(\MaxieSystems\WebProxy\ResponseHandler\Action $action): Action
    {
        return $action;
    }
/*	function __construct(Engine $engine, HTTP\Response $response)
	 {
		parent::__construct($engine, $response);
		if($this->charset === $response->charset) $this->converted = false;
		else
		 {
			# В случае, когда по ошибке в документе присутствуют более одного тэга meta[charset], браузером используется первый из них.
			$each_meta_charset = function(callable $c, ...$args) : int {
				static $nodes = null;
				if(null === $nodes)
				 {
					$nodes = [];
					$run = function(object $item, string $attr, ...$args) use(&$nodes, $c){
						$c($item, $attr, count($nodes), ...$args);
						// if(!isset($item->nodeName)) return;
						$nodes[] = [$item, $attr];
					};
					foreach($this->GetDoc()->getElementsByTagName('meta') as $item)
					 {
						if($item->hasAttribute('charset')) $run($item, 'charset', ...$args);# meta[charset="utf-8"]
						elseif($item->hasAttribute('http-equiv') && $item->hasAttribute('content'))# meta[http-equiv="content-type"][content="text/html; charset=WINDOWS-1251"]
						 {
							$v = $item->getAttribute('http-equiv');
							if('content-type' === strtolower($v)) $run($item, 'content', ...$args);
						 }
					 }
				 }
				else foreach($nodes as $i => $n) $c($n[0], $n[1], $i, ...$args);
				return count($nodes);
			};
			$charset = $response->charset;
			if('' === $charset)
			 {
				$each_meta_charset(function(object $item, string $attr, int $i, ...$args) use(&$charset){
					if($i) return;
					if('charset' === $attr) $charset = strtolower($item->getAttribute($attr));
					elseif('content' === $attr) list($mime, $charset) = HTTP\Header::ParseContentType($item->getAttribute($attr));
				});
			 }
			if($this->converted = ($this->charset !== $charset))
			 {
				$each_meta_charset(function(object $item, string $attr, int $i){
					if('charset' === $attr) $item->setAttribute($attr, $this->charset);
					elseif('content' === $attr)
					 {
						list($mime, $charset) = HTTP\Header::ParseContentType($item->getAttribute($attr));
						$item->setAttribute($attr, "$mime; charset=$this->charset");
					 }
				});
				$response->headers->content_type = "$response->mime; charset=$this->charset";
			 }
		 }
		// $engine->SetHeader('x-my-header-223', 'HTML document');
	 }

	final public function __toString()
	 {
		if(null === $this->doc) return parent::__toString();
		return $this->doc->saveXML($this->doc->doctype).PHP_EOL.$this->doc->saveHTML($this->doc->documentElement);// а если это фрагмент HTML (например, часть страницы, полученная асинхронным запросом)? // $doc->documentElement->nodeName равен 'html' - можно использовать это для проверки того, фрагмент был загружен или целая страница?
	 }

	final public function EachNode(callable $c, ...$args) : \DOMNode
	 {
		$node = $this->GetDoc()->documentElement;
		$this->Traverse($node, $c, ...$args);
		return $node;
	 }

	final public function Traverse(\DOMNode $node, callable $c, ...$args)
	 {
		if(XML_ELEMENT_NODE === $node->nodeType)
		 {
			for($i = 0; $i < $node->childNodes->length; ++$i)
			 {
				$n = $node->childNodes->item($i);
				if(false === $c($n, $i, ...$args)) continue;
				if(null === $n->parentNode) continue;
				$this->Traverse($node->childNodes->item($i), $c, ...$args);
			 }
		 }
	 }

	final public function GetDoc() : \DOMDocument
	 {
		if(null === $this->doc)
		 {
			$html = parent::__toString();
			if('' === $html)
			 {
				throw new \Exception('Empty response: not implemented yet...');
			 }
			# fix: DOMDocument в некоторых случаях может искажать код встроенного на страницу JavaScript, если в коде содержатся HTML теги (например, выводимые функцией document.write).
			# Вставка CDATA здесь не помогает: https://bugs.php.net/bug.php?id=74858
			$s_pos = 0;
			do
			 {
				$s_pos = strpos($html, '<script', $s_pos);
				if(false === $s_pos) break;
				$s_pos = strpos($html, '>', $s_pos);
				if(false === $s_pos) break;
				++$s_pos;
				$e_pos = strpos($html, '</script>', $s_pos);
				if(false === $e_pos) break;
				if($s_pos !== $e_pos)
				 {
					$len = $e_pos - $s_pos;
					$js = substr($html, $s_pos, $len);
					if(trim($js))
					 {
						$js1 = $js;
						foreach(['</span>' => '<\\/span>'] as $k => $v) $js1 = str_replace($k, $v, $js1, $count);
						$html = substr_replace($html, $js1, $s_pos, $len);
					 }
				 }
				$s_pos = $e_pos + 9;# strlen('</script>') = 9
			 }
			while(true);
			# -- end fix
			if(!$this->converted) $html = '<?xml encoding="utf-8" ?>'.$html;
			$this->doc = new \DOMDocument('1.0', $this->charset);
			libxml_use_internal_errors(true);
			if(!$this->doc->loadHTML($html)) throw new \Exception('not implemented yet...');
			// foreach(libxml_get_errors() as $error)
			 // {
				// var_dump($error);
				// echo '<br />';
			 // }
			libxml_clear_errors();
			libxml_use_internal_errors(false);
			# Path information: the BASE element https://www.w3.org/TR/html4/struct/links.html#h-12.4
			foreach($this->doc->getElementsByTagName('base') as $item)# If multiple <base> elements are specified, only the first href and first target value are used; all others are ignored.
			 if($item->hasAttribute('href'))
			  {
				$href = $item->getAttribute('href');
				if('' === $href) throw new \Exception('not implemented yet...');// !!!
				else
				 {
					$u = $this->GetEngine()->GetProxyURL($href, '*');
					if(!$u->u_abs) throw new \Exception('not implemented yet...');// !!!
					$this->GetEngine()->SetBaseURL($u->u_src);
					$item->setAttribute('href', $u->ToAttrValue());
					break;
				 }
			  }
			Config::RequireFile('dom');
		 }
		return $this->doc;
	 }

	// public static function GetLabel() { return '001'; }

	private $doc = null;
	private $charset = 'utf-8';
	private $converted;*/
}
