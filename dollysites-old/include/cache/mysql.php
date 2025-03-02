<?php
namespace DollySites\Cache;
use DollySites as DS;


class PDO_MySQL extends DS\Cache
{
	public function __construct()
	 {
		MS\Config::RequireFile('sqldb');
		$this->db = new MS\MYSQLDB(['dbname' => 'dollysites'], 'root', 'maxxx');
	 }

	public function Write(MS\URL $url, $url_type, array $content, array $meta)
	 {
		throw new \Exception('not implemented yet...');
	 }

	public function GetMeta(MS\URL $url, $url_type)
	 {
		throw new \Exception('not implemented yet...');
	 }

	public function Delete(MS\URL $url, $url_type)
	 {
		throw new \Exception('not implemented yet...');
	 }

	public function GetContent(MS\URL $url, $url_type, $index)
	 {
		throw new \Exception('not implemented yet...');
	 }

	public function Clear()
	 {
		throw new \Exception('not implemented yet...');
	 }

	private $db;
}