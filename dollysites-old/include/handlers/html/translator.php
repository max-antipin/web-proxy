<?php
namespace DollySites\Handlers\HTML;
use \DollySites\{Engine,Handlers};
// use \MaxieSystems as MS;
use \DollySites as DS;
use \MaxieSystems\HTTP;

class Translator extends Handlers\ContentTransform
{
	final public function __construct(string $type, string $source_lang, string $target_lang)
	 {
		// parent::__construct($owner);
		$this->type = $type;
		$this->source_lang = $source_lang;
		$this->target_lang = $target_lang;
	 }

	final public function __invoke(Handlers\WebResource $resource)
	 {return;
		$doc = $resource->GetDoc();
		foreach(['lang' => true, 'xml:lang' => false] as $a => $v)
		 {
			if(false === $v && !$doc->documentElement->hasAttribute($a)) continue;
			$doc->documentElement->setAttribute($a, $this->target_lang);
		 }
		$this->Traverse($doc->documentElement);
		if(!$this->items) return;
		// echo '<br />';
		// echo '<br />';
		// var_dump($this->strings);
		// echo '<br />';
		// echo '<br />';
		// echo 'count items: ', count($this->items), ', count strings: ', count($this->strings);
		// echo '<br />';
		// echo '<br />';
		// die;
		$s0 = '';
/* 		foreach($this->items as $k => $item)
		 {
			// if($k) $s0 .= ' '.self::SEPARATOR.' ';
			if($k) $s0 .= self::SEPARATOR;
			// $s0 .= $item['value'];
			$s0 .= $item->value;
			// var_dump($item);
		 // echo '<br />';
		 } */
		foreach($this->strings as $v => $str)
		 {
			if('' !== $s0) $s0 .= self::SEPARATOR;
			$s0 .= $v;
			// var_dump($item);
		 // echo '<br />';
		 }
		$t = __NAMESPACE__."\\Translator_$this->type";
		$t = new $t();
		$s1 = $t($this->source_lang, $this->target_lang, $s0);
		// var_dump(substr_count($s0, self::SEPARATOR));
		// var_dump($s0);
		// echo '<br />';
		// echo '<br />';
		// var_dump(substr_count($s1, self::SEPARATOR));
		// var_dump($s1);
		// echo '<br />';
		// echo '<br />';
		$s_len = iconv_strlen(self::SEPARATOR, self::CHARSET);
		if(null === $s1 || false === $s1) return;// ??? ошибка? или временная отмена кэширования?
		elseif(is_array($s1))
		 {
			if(!$s1) return;// ??? ошибка? или временная отмена кэширования?
			if(isset($s1['i_source']) && isset($s1['i_target']))
			 {
				$odd = [];
				$j = 0;
				foreach($s1['data'] as $k => $row)
				 {
					if(!isset($row->$s1['i_source']) || !isset($row->$s1['i_target'])) continue;
					$v = $row->$s1['i_source'];
					$str = $row->$s1['i_target'];
					$s = iconv_substr($v, -$s_len, $s_len, self::CHARSET);
					$s_end = false !== $s && self::SEPARATOR === $s;
					if($s_end)
					 {
						$v = iconv_substr($v, 0, -$s_len, self::CHARSET);
						$s = iconv_substr($str, -$s_len, $s_len, self::CHARSET);
						if(false !== $s && self::SEPARATOR === $s) $str = iconv_substr($str, 0, -$s_len, self::CHARSET);
					 }
					if(isset($this->strings[$v]))
					 {
						$this->strings[$v]->tr_len = null;
						$this->strings[$v]->tr = $str;
						++$j;
						// var_dump($v);
					// echo '<br />';
					 }
					else $odd[$k] = ['source' => $v, 'target' => $str, 's_end' => $s_end];
				 }
				$j -= count($this->strings);
				if($j < 0)
				 {
					// echo '<br />';
					foreach($this->strings as $v => $str)
					 if(!isset($str->tr))
					  {
						++$j;
						// var_dump($v);
						// echo '<br />';
						$tr = [];
						foreach($odd as $k => $row)
						 {
							$pos = iconv_strpos($v, $row['source'], 0, self::CHARSET);
							if(false !== $pos)
							 {
								$tr[$pos] = $row['target'];
								// var_dump($pos);
						// echo '<br />';
								unset($odd[$k]);
							 }
						 }
						if($tr)
						 {
							ksort($tr);
							$str->tr = implode('', $tr);
						 }
						// echo '<br />';
					  }
					// echo '<br />';
					// var_dump($j, $odd);
					if($j !== 0 || $odd) throw new \Exception('troubles...');
				 }
				elseif($j > 0) throw new \Exception('not implemented yet');
			 }
			else throw new \Exception('not implemented yet...');
		 }
		elseif('' === "$s1") return;// ??? ошибка? или временная отмена кэширования?
		else
		 {
			$s1_len = iconv_strlen($s1, self::CHARSET);
			$pos_start = $k = 0;
			$get_pos_end = function($pos_start, $k) use($s1, $s1_len){
				$pos_end = iconv_strpos($s1, self::SEPARATOR, $pos_start, self::CHARSET);
				if(false === $pos_end)
				 {
					if($k < count($this->strings) - 1) throw new \Exception('lesser than... not implemented');// это означает, что в ответе пришло меньше элементов, чем отправлялось в запросе.
					$pos_end = $s1_len;
				 }
				return $pos_end;
			};
			foreach($this->strings as $v => $str)
			 {
				if($str->sep > 0)
				 {
					$pos = $pos_start;
					for($j = 0; $j <= $str->sep; ++$j)
					 {
						$pos_end = $get_pos_end($pos, $k);
						$pos = $pos_end + $s_len;
					 }
				 }
				else $pos_end = $get_pos_end($pos_start, $k);
				$str->tr_len = $pos_end - $pos_start;
				$str->tr = iconv_substr($s1, $pos_start, $str->tr_len, self::CHARSET);// trim???
				$pos_start = $pos_end + $s_len;
				++$k;
				var_dump($str->tr_len, $v, $str->tr);
				echo '<br />';
			 }
			if($pos_end < $s1_len) throw new \Exception('greater than... not implemented');// что делать??? это ошибка - означает, что в ответе пришло больше элементов, чем отправлялось в запросе.
		// var_dump($pos_start, $pos_end, $len, $pos_end < $len, iconv_substr($s1, $pos_start, $pos_end - $pos_start, self::CHARSET));
		 }
		// foreach($this->strings as $v => $str)
		 // {
			// var_dump($v, $str);
			// echo '<br />';
		 // }
		// die;
		foreach($this->items as /* $k => */ $item)
		 {
			if(isset($this->strings[$item->value])) $s = $item->lhs.$this->strings[$item->value]->tr.$item->rhs;
			else throw new \Exception('not implemented');
			if(null === $item->attr)
			 {
				if(\XML_ELEMENT_NODE === $item->node->nodeType)
				 {
					while($item->node->firstChild) $item->node->removeChild($item->node->firstChild);
					$item->node->appendChild($doc->createTextNode($s));
				 }
				elseif(\XML_TEXT_NODE === $item->node->nodeType)// временное решение!!!
				 {
					$item->node->parentNode->replaceChild($doc->createTextNode($s), $item->node);// временное решение!!!
				 }
				else throw new \Exception('not implemented');
			 }
			else
			 {
				$item->node->setAttribute($item->attr, $s);
			 }
			// var_dump($item->value, $s);
			// echo '<br />';
		 }
	 }

	final public function GetLabel() : string { return 'a04'; }

	final private function AddItem(\DOMNode $node, $attr)
	 {
		$s = null === $attr ? $node->nodeValue : $node->getAttribute($attr);
		if('' === $s) return false;
		$i = new \stdClass();
		$i->lhs = '';
		$i->value = $s;
		$i->rhs = '';
		// $i->r = $i->l = 0;// !!! убрать ??? 
		$len = iconv_strlen($i->value, self::CHARSET);
		// var_dump($len);
		// echo '<br />';
		// var_dump($i);
		// echo '<br />';
		// echo '<br />';
		for($i->l = 0; $i->l < $len; ++$i->l)
		 {
			$c = iconv_substr($i->value, $i->l, 1, self::CHARSET);
			if(!isset($this->char_mask[$c])) break;
			$i->lhs .= $c;
			// var_dump($c);
			// var_dump(isset($this->char_mask[$c]));
			// echo '<br />';
		 }
		for($i->r = 0, $len0 = $len - $i->l; $i->r < $len0; ++$i->r)
		 {
			$c = iconv_substr($i->value, $len - $i->r - 1, 1, self::CHARSET);
			if(!isset($this->char_mask[$c])) break;
			$i->rhs = $c.$i->rhs;
			// var_dump($c);
			// var_dump(isset($this->char_mask[$c]));
			// echo '<br />';
		 }
		if($i->l > 0 || $i->r > 0)
		 {
			$i->value = $len === $i->l ? '' : iconv_substr($i->value, $i->l, $len - $i->l - $i->r, self::CHARSET);
		 }
		// echo '<br />';
		// var_dump($i);
		// die;
		// $v = rtrim($s);
		if('' === $i->value) return false;
		// if($s !== $v)
		 // {
			// var_dump($v);
			// die;
		 // }
		// $v = ltrim($v);
		// if($s !== $v)
		 // {
			// var_dump($v);
		 // }
		// $i->value = $s;
		// if(preg_match('/^(\s+)(\S+.*)$/', $s, $m)) var_dump($m);
		// if(preg_match('/^(.*\S+)(\s+)$/', $s, $m)) var_dump($m);// вероятно, регулярное выражение будет заменено на последовательную проверку спецсимволов на концах строки... или для всей строки.
		$i->node = $node;
		$i->attr = $attr;
		$this->items[] = $i;
		if(!isset($this->strings[$i->value]))
		 {
			$this->strings[$i->value] = new \stdClass();
			$this->strings[$i->value]->count = 0;
			$this->strings[$i->value]->sep = substr_count($i->value, self::SEPARATOR);
		 }
		++$this->strings[$i->value]->count;
		return true;
	 }

	final private function Traverse(\DOMNode $node)
	 {
		foreach($node->childNodes as $n)
		 {
			if(null === $n->childNodes)
			 {
				if(\XML_TEXT_NODE === $n->nodeType)
				 {
					if($this->AddItem($n, null))// это временное решение!!!
					 {
				// var_dump($n->nodeValue);
				// echo '<br />';
					 }
				 }
				// elseif(\XML_ELEMENT_NODE === $n->nodeType)
				 // {
				// var_dump($n);
				// echo '<br />';
				 // }
				// else
				 // {
				// var_dump($n);
				// echo '<br />';
				 // }
			 }
			elseif(\XML_ELEMENT_NODE === $n->nodeType)
			 {
				$a = 'title';
				if($n->hasAttribute($a) && $this->AddItem($n, $a))
				 {
					// var_dump($n->tagName, $a, $n->getAttribute($a));
					// var_dump($n, $n->childNodes);
					// echo '<br />';
				 }
				if('title' === $n->tagName)
				 {
					// $this->Traverse($n);
					if($this->AddItem($n, null))
					 {
					// var_dump($n->nodeValue);
					// echo '<br />';
					 }
				 }
				elseif(isset($this->tags[$n->tagName]))
				 {
					foreach($this->tags[$n->tagName]['attrs'] as $a => $values)
					 {
						if($n->hasAttribute($a))
						 {
							if(true === $values)
							 {
								if($this->AddItem($n, $a));
							 }
							else
							 {
								foreach($values as $v => $x)
								 {
									if(($v === $n->getAttribute($a)) && $n->hasAttribute($x) && $this->AddItem($n, $x))
									 {
										// var_dump($n->tagName, $a, $v, $x, $n->getAttribute($x));
										// var_dump($n, $n->childNodes);
										// echo '<br />';
									 }
								 }
							 }
						 }
					 }
					// if(!$this->tags[$n->tagName]['empty'])
				 }
				elseif(empty($this->skip_tags[$n->tagName]) && $n->childNodes->length)
				 {
					$this->Traverse($n);
						// var_dump($n->tagName, $n->childNodes->length);
						// echo '<br />';
				 }
			 }
			else
			 {
				// $this->Traverse($n);
				// var_dump($n);
				// echo '<br />';
			 }
		 }
	 }

	private $items = [];
	private $strings = [];
	private $tags = [
		'meta' => [
			'empty' => true,
			'attrs' => [
				'name' => ['description' => 'content', 'keywords' => 'content'],
				'property' => ['og:description' => 'content', 'og:title' => 'content'],
			],
		],
		'img' => [
			'empty' => true,
			'attrs' => [
				'alt' => true,
			],
		],
		'input' => [
			'empty' => true,
			'attrs' => [
				'placeholder' => true,
				'type' => ['submit' => 'value', 'button' => 'value'],
			],
		],
		'textarea' => [
			'empty' => false,
			'attrs' => [
				'placeholder' => true,
			],
		],
	];
	private $skip_tags = ['link' => true, 'script' => true, 'style' => true];
	private $type;
	private $source_lang;
	private $target_lang;
	private $char_mask = [' ' => ' ', "\t" => "\t", "\n" => "\n", "\r" => "\r", "\0" => "\0", "\x0B" => "\x0B"];//, 		'Р' => 'Р', 'а' => 'а', 'б' => 'б', 'о' => 'о', 'т' => 'т', 'к' => 'к', 'л' => 'л', '!' => '!',
		// 'в' => 'в', 'Я' => 'Я', 'н' => 'н', 'д' => 'д', 'е' => 'е', 'О' => 'О', 'с' => 'с', 'р' => 'р', 'ы' => 'ы', 'й' => 'й', 'С' => 'С', ',' => ',', 'г' => 'г', '.' => '.', 'Б' => 'Б', 'Т' => 'Т', 'и' => 'и',
		// 'К' => 'К', 'у' => 'у',
	// ];

	const SEPARATOR = "\n\n";//'.';//'|';//
	const CHARSET = 'utf-8';
}

interface ITranslator
{
	public function __invoke($source_lang, $target_lang, $text);
}

class Translator_yandex implements ITranslator
{
	final public function __invoke($source_lang, $target_lang, $text)
	 {
		$http = new HTTP\Request();
		$r = $http->GET('https://translate.yandex.com');
		// if(200 === $r->code)
		$html = "$r";
		$pos_start = strpos($html, "SID: '");
		// if(false !== $pos_start)
		$pos_start += 6;// strlen("SID: '") === 6
		$pos_end = strpos($html, "',", $pos_start);
		// if(false !== $pos_end)
		$sid = substr($html, $pos_start, $pos_end - $pos_start);
		$sid = strrev(@substr($sid, 0, 8)).'.'.strrev(substr($sid, 9, 8)).'.'.strrev(substr($sid, 18, 8));
		$sid .= '-0-0';
		$r = $http->POST("https://translate.yandex.net/api/v1/tr.json/translate?lang={$source_lang}-{$target_lang}&srv=tr-url&format=plain&id={$sid}", ['text' => $text]);
		// if(200 === $r->code)
		if('application/json' === $r->mime)
		 {
			$json = json_decode("$r");
			if(isset($json->text))
			 {
				return $json->text[0];
			 }
			// elseif(413 === $json->code) {
			// return true;
		// }
			else throw new \Exception('not implemented');
		 }
	 }
}

class Translator_google implements ITranslator
{
	final public function __invoke($source_lang, $target_lang, $text)
	 {
		$response = self::RequestTranslation($source_lang, $target_lang, $text);
		$json = json_decode($response);
		if($json)
		 {
			// $r = [];
			// foreach($json->sentences as $s)
			 // {
				// if(isset($s->trans)) $r[] = $s;
				 // var_dump($s);
			// echo '<br />';
			 // }
			 // var_dump($json->sentences);
			// die;
			// return $r;
			return ['data' => $json->sentences, 'i_source' => 'orig', 'i_target' => 'trans'];
		 }
	 }

	final protected static function RequestTranslation($source, $target, $text)
	 {
		$url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
		$q = http_build_query(['sl' => $source, 'tl' => $target, 'q' => $text]);
		// if(strlen($q) >= 5000) throw new \Exception("Maximum number of characters exceeded: 5000");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	 }
}