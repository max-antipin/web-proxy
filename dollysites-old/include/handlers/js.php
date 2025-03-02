<?php
namespace DollySites\Handlers;
use \MaxieSystems as MS;
use \DollySites as DS;
use \MaxieSystems\HTTP;
use \MaxieSystems\URL;
use \MaxieSystems\URLs;

class JSDocument extends WebResource
{
	function __construct(MS\AbstractHTTPResponse $response, \DollySites\Engine $engine)
	 {
		parent::__construct($response, $engine);
		$this->code = "$response";
	 }

	final public function __toString()
	 {
		// if(null === $this->doc) return parent::__toString();
		// $doc = $this->GetDoc();
		return $this->code;
	 }

	public function SetCode($c) { $this->code = $c; return $this; }

	private $charset = 'utf-8';
	private $code;
}
?>