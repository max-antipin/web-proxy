<?php

namespace DollySites;

use MaxieSystems\URL;
//use MaxieSystems\URLReadOnly;

class SourceURL extends \MaxieSystems\WebProxy\SourceURL
{
/*	protected function onCreate(URL $url): void
	{
		parent::onCreate($url);
	}*/
	/*final public function __construct(string $value, bool $readonly = false, array $properties = [])
	 {
		$properties['u_type'] = 'proxy';
		parent::__construct($value, $readonly, $properties);
	 }

	final public function ToAttrValue() : string
	 {
		return $this->u_modified ? $this->Trim('path') : $this->__toString();
	 }*/
}
