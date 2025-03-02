<?php
namespace DollySites;
use \MaxieSystems\{URL};
// use \MaxieSystems as MS;

// abstract class CacheStorage
// {
	// protected $cached_items = [];
// }

abstract class Cache// implements \Iterator// extends CacheStorage//
{
	// use MS\TOptions;

	abstract public function GetMeta(URL $url) : array ;
	// abstract public function Write(MS\URL $url, $url_type, array $content, array $meta);//$url_type: 0 - this domain, 1 - subdomain, -1 - external domain.
	// abstract public function GetContent(MS\URL $url, $url_type, $index);// сделать подобную функцию, но вместо индекса указывать название преобразователя, до которого нужен контент. Например, визуальный редактор - один из преобразователей, и я хочу вывести страницу без правок редактора, чтобы отредактировать её.
	// abstract public function Delete(MS\URL $url, $type);
	// abstract public function Clear();


	// public function __construct(array $options)
	 // {
		// $this->AddOptionsMeta([/* 'base_url' => [], */ 'source_host' => ['type' => 'string,len_gt0']]);
		// $this->SetOptionsData($options);
	 // }
	// final public function Exists(MS\URL $url, $type, CachedItem &$cached_item = null)
	 // {
		// $cached_item = null;
		// $u = $url->Crop(0 === $type ? 'host' : false, 'fragment');
		// if(!isset($this->cached_items[$u]))
		 // {
			// $this->cached_items[$u] = $this->Read($url, $type);
			// if(!$this->cached_items[$u]) $this->cached_items[$u] = new CachedItem($this, $url, $type);
		 // }
		// $cached_item = $this->cached_items[$u];
		// return $this->cached_items[$u]->exists;
	 // }

	// private $cached_items = [];

	public function __debugInfo() { return []; }

	final public static function GetTypes() : array
	 {
		if(null === self::$types)
		 {
			self::$types = ['files' => 'Files'];//l10n()->files
			if(defined('PDO::ATTR_DRIVER_NAME'))
			 {
				$d = \PDO::getAvailableDrivers();
				if(in_array('mysql', $d)) self::$types['pdo_mysql'] = 'MySQL';
				// if(in_array('sqlite', $d)) self::$types['sqlite'] = 'Sqlite';
			 }
				// if(empty($cache['Mysql']) && function_exists('mysqli_connect')) $cache['MysqlMysqli'] = 'MySQL';
				// if(empty($cache['Sqlite']) && class_exists('Sqlite3')) $cache['Sqlite3'] = 'Sqlite3';
			self::$types[''] = 'Disable cache';//l10n()->do_not_cache;
		 }
		return self::$types;
	 }

	private static $types = null;
}
return;
class CacheWriter// extends CacheStorage
{
	final public function __construct(Cache $cache, MS\AbstractHTTPResponse $response, $url_type)
	 {
		$this->cache = $cache;
		$this->response = $response;
		$this->url_type = $url_type;
	 }

	final public function SetContent($k, $max_k, $value, $label)
	 {
		$this->content[$k] = ['value' => "$value", 'label' => $label];
		$this->modified = true;
	 }

	final public function __destruct()
	 {
		if($this->modified)
		 {
			$meta = $this->cache->GetMeta($this->response->url, $this->url_type);
			$meta['url'] = $this->response->url->__toString();
			$meta['code'] = $this->response->code;
			$meta['content_type'] = $this->response->headers->content_type ?: $this->response->content_type;
			$meta['headers'] = $this->response->headers->ToArray();
			if(!isset($meta['stages'])) $meta['stages'] = [];
			$t = time();
			$content = [];
			foreach($this->content as $k => $v)
			 {
				$meta['stages'][$k] = ['modified' => $t];
				if(isset($v['label'])) $meta['stages'][$k]['label'] = $v['label'];
				$content[$k] = $v['value'];
			 }
			$this->cache->Write($this->response->url, $this->url_type, $content, $meta);
			$this->modified = false;
		 }
	 }

	final public function __debugInfo() { return ['modified' => $this->modified]; }

	private $cache;
	private $response;
	private $url_type;
	private $content = [];
	private $modified = false;
}

class HTTPResponse// extends MS\HTTPResponse
{
	final public function __construct(Cache $cache, array $meta, $url_type)//$result, $hsize, array $data, array $headers, array $cookies)
	 {
		$this->cache = $cache;
		$this->url_type = $url_type;
		$this->url = $meta['url'];
		foreach(['code' => 'code', 'content_type' => 'content_type'] as $k => $v) $this->InitProperty($k, $meta[$v]);
		$charset = null;
		$v = explode(';', $meta['content_type'], 2);
		$mime = strtolower($v[0]);
		if(!empty($v[1]))
		 {
			$s = 'charset=';
			if(false !== ($pos = strpos($v[1], $s))) $charset = strtolower(trim(substr($v[1], $pos + strlen($s)), ' \'"'));
		 }
		$this->InitProperty('mime', $mime);
		$this->InitProperty('charset', $charset);
		$this->headers = empty($meta['headers']) ? [] : $meta['headers'];
		$this->stages = $meta['stages'];
		// $this->InitProperty('cookies', $cookies);
		// $this->_properties_meta['mime']['callback'] = $this->_properties_meta['charset']['callback'] = [$this, 'InitMIME'];
		$this->_properties_meta['content_length']['callback'] = true;
	 }

	final public function Expired($since)
	 {
		if(null === $this->value) $this->InitValue();
		if(null !== $this->index)
		 {
			if(!$since) throw new \InvalidArgumentException('Invalid time argument');
			$c = $since[strlen($since) - 1];
			switch($c)
			 {
				case 'd':
				case 'h':
				case 'm':
					static $f = ['m' => 60, 'h' => 3600, 'd' => 86400];
					$t = intval($since);
					if(!$t) throw new \InvalidArgumentException('Invalid time argument');
					$t *= $f[$c];
					$t = time() - $t;
					break;
				default: $t = strtotime($since);
			 }
			return $this->stages[$this->index]['modified'] < $t;
		 }
	 }

	final public function GetIndex()
	 {
		if(null === $this->value) $this->InitValue();
		return $this->index;
	 }

	final public function __toString()
	 {
		if(null === $this->value) $this->InitValue();
		return $this->value;
	 }

	final public function Validate(\stdClass $transform)
	 {
		$this->index = $this->GetValidStage($transform);
		if(null === $this->index) $this->value = '';
		else
		 {
			do
			 {
				$this->value = $this->cache->GetContent($this->__get('url'), $this->url_type, $this->index);
				if(null === $this->value)
				 {
					--$this->index;
					if($this->index < 0)
					 {
						$this->value = '';
						return ($this->index = null);
					 }
				 }
				else return $this->index;
			 }
			while(true);
		 }
	 }

	final protected function GetValidStage(\stdClass $transform)
	 {
		$k = null;
		foreach($this->stages as $i => $v)
		 {
			if(isset($transform->labels[$i - 1]))
			 {
				if(!isset($v['label']) || $v['label'] !== $transform->labels[$i - 1]) return $k;
				if($modified = $transform->objects[$i - 1]->GetLastModified())
				 {
					if($modified < 0) $modified = time() + $modified;
					if($v['modified'] - $modified <= 0) return $k;
				 }
			 }
			elseif(0 === $i)
			 {
				if(!empty($v['label'])) return $k;
			 }
			else return $k;
			$k = $i;
		 }
		return $k;
	 }

	final protected function _set_property__content_length() { return strlen("$this"); }

	final private function InitValue()
	 {
		do
		 {
			end($this->stages);
			$this->index = key($this->stages);
			if(null === $this->index) break;
			$this->value = $this->cache->GetContent($this->__get('url'), $this->url_type, $this->index);
			if(null === $this->value) unset($this->stages[$this->index]);
			else return true;
		 }
		while(null === $this->value);
		$this->value = '';
	 }

	private $cache;
	private $url_type;
	private $value = null;
	private $stages;
	private $index = null;
}