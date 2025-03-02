<?php
namespace DollySites\Handlers;
use \DollySites\Engine;
use \MaxieSystems\{HTTP};

abstract class WebResource
{
	function __construct(Engine $engine, HTTP\Response $response)
	 {
		$this->engine = $engine;
		$this->response = $response;
	 }

	public function __toString() { return "$this->response"; }

	final public function __get($name) { return $this->response->__get($name); }
	final public function __isset($name) { return $this->response->__isset($name); }
	final public function GetEngine() : Engine { return $this->engine; }
	final public function __debugInfo() { return $this->response->__debugInfo(); }

	private $engine;
	private $response;
}

abstract class ContentTransform
{
	abstract public function __invoke(WebResource $resource);
	abstract public function GetLabel() : string ;

	// public function GetLastModified() {}

	final public function __debugInfo() { return []; }
}

class Compound extends ContentTransform
{
	final public function __construct(array ...$items)
	 {
		if(!$items) throw new \Exception('Empty transform list');
		foreach($items as $k => $v)
		 {
			$this->items[$k] = $this->InitTransform($v);
			// if($k) $this->label .= $engine::LABEL_SEPARATOR;
			// $this->label .= $this->items[$k]->GetLabel();
		 }
	 }

	final public function __invoke(WebResource $resource)
	 {
		foreach($this->items as $item) $item($resource);
	 }

	final public function GetLabel() : string
	 {
		return $this->label;
	 }

	// public function GetLastModified() {}// !!! метод должен возвращать максимальную дату из всех возвращённых???

	final private function InitTransform(array $args) : ContentTransform
	 {
		$c = __NAMESPACE__.'\\'.array_shift($args);
		return new $c(...$args);
	 }

	private $items = [];
	private $label;
}