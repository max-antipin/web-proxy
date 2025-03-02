<?php

namespace DollySites\Handlers {
use DollySites\Engine;
use MaxieSystems\{HTTP};

class Config implements \Iterator, \Countable
{
	final public function __construct(Engine $engine, string $name)
	 {
		$this->engine = $engine;
		$this->name = $name;
	 }

	final public function AddTransform(int $i, string $name, ...$args) : Config
	 {
		if(isset($this->conf[$i])) throw new \Exception('Duplicate index: '.$i);
		$this->conf[$i] = ['class' => __NAMESPACE__.'\\'.$this->name.'\\'.$name, 'args' => $args];
		return $this;
	 }

	final public function NewResource(HTTP\Response $response) : WebResource
	 {
		if(null === $this->trans) $this->Init();
		$c = __NAMESPACE__.'\\'.$this->name;
		return new $c($this->engine, $response);
	 }

	final public function count()
	 {
		if(null === $this->trans) $this->Init();
		return count($this->trans);
	 }

	final public function rewind()
	 {
		// if(null === $this->trans) $this->Init();
		reset($this->trans);
	 }

	final public function current() { return current($this->trans); }
	final public function next() { return next($this->trans); }
	final public function key() { return key($this->trans); }
	final public function valid() { return null !== key($this->trans); }
	final public function __get($name) { return ; }
	final public function __set($name, $value) {}
	final public function __debugInfo() { return []; }

	final private function Init()
	 {
		$this->trans = [];
		$n = strtolower($this->name);
		// if('\\' !== DIRECTORY_SEPARATOR) $n = str_replace('\\', DIRECTORY_SEPARATOR, $n);
		require_once(\MaxieSystems\INC_DIR.'handlers'.DIRECTORY_SEPARATOR.$n.'.php');
				// $t->labels = [];
				// $t->max = null;
		if($this->conf)
		 {
					// $t->max = 0;
					// $label = '';
			foreach($this->conf as $k => $v)
			 {
				$this->trans[$k] = $this->InitTransform($v['class'], ...$v['args']);
				// var_dump($this->trans[$k]);
				if($k <= $this->engine::H_MAX_INDEX)
				 {
							// if('' !== $label) $label .= self::LABEL_SEPARATOR;
							// $label .= $this->trans[$k]->GetLabel();
							// if(0 < $k) $t->max = $k;
				 }
						// $t->labels[$k] = $label;
// var_dump($k, $v);
			 }
		 }
	 }

	final private function InitTransform(string $c, ...$args) : ContentTransform { return new $c(...$args); }

	private $engine;
	private $name;
	private $conf = [];
	private $trans = null;
}

}