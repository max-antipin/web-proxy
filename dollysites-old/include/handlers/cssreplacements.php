<?php
namespace DollySites\Handlers;
use \MaxieSystems as MS;
use \DollySites as DS;
use \MaxieSystems\HTTP;
use \MaxieSystems\URL;

class CSSReplacements extends ContentTransform
{
	use DS\TReplacements;

	final public function __invoke(WebResource $resource)
	 {
		if($r = $this->Load())
		 {
			$code = "$resource";
			$settings = MS\SimpleConfig::Instance('main');
			foreach($r as $rule) $code = $this->replaceElementOfReplacesTasks($code, $rule, $settings);
			$resource->SetCode($code);
		 }
	 }

	final public function GetLabel() { return 'a06'; }

	final public function GetLastModified()
	 {
		// return 1548239000;// !!!
	 }

	private function replaceElementOfReplacesTasks($page, $rule, $settings)
	 {
		if (!isset($rule['change_type'])) {
			return $page;
		}

		switch ($rule['change_type']) {
			case 'string' :
				$page = $this->_replace_str($page, $settings, $rule);
				break;
			case 'preg' :
				$page = preg_replace($rule['l_textarea'], isset($rule['r_textarea']) ? $rule['r_textarea'] : '', $page);
				break;
			case 'script':
				$rule['l_textarea'] = isset($rule['l_textarea']) ? trim(htmlspecialchars_decode($rule['l_textarea'])) : '';
				$rule['r_textarea'] = isset($rule['r_textarea']) ? trim(htmlspecialchars_decode($rule['r_textarea'])) : '';
				$page = $this->_replace_str($page, $settings, $rule);
				break;

		}
		return $page;
	 }

	private function _replace_str($page, $settings, $rule)
	 {
		$iconv = function ($settings, $text) {
			return $text;
		};
		$page = str_replace(
			$iconv($settings, str_replace("\r\n", PHP_EOL, $rule['l_textarea'])),
			$iconv($settings, isset($rule['r_textarea']) ? $rule['r_textarea'] : ''),
			$page);
		return $page;
	 }
}
?>