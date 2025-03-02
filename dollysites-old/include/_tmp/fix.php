<?php
if(!defined('CURLPROXY_SOCKS4A')) define('CURLPROXY_SOCKS4A', 6);
if(!defined('CURLPROXY_SOCKS5_HOSTNAME')) define('CURLPROXY_SOCKS5_HOSTNAME', 7);
if(!interface_exists('JsonSerializable'))
 {
	interface JsonSerializable
	 {
		public function jsonSerialize();
	 }
 }
?>