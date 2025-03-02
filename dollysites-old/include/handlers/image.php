<?php
namespace DollySites\Handlers;
use \MaxieSystems as MS;
use \DollySites as DS;
use \DollySites\Engine;
use \MaxieSystems\{HTTP,URL};

class Image extends WebResource
{
	public function __construct(Engine $engine, HTTP\Response $response)
	 {
		parent::__construct($engine, $response);
		// $t = 3 * 3600;
		// $engine->SetHeader('Expires', gmdate('r', time() + $t), true);
		// $engine->AddHeaders('x-my-header', 'x-my-header: true value');
		// $response->headers->x_my_header_3 = 'value 222';
	 }

	// public static function GetLabel() { return '002'; }
}