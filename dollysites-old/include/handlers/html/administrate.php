<?php
namespace DollySites\Handlers\HTML;
use \DollySites\{Engine,Handlers};
use \MaxieSystems\{Config,DOM};

class Administrate extends Handlers\ContentTransform
{
	final public function __construct()//MS\AuthenticationData $user)
	 {
		$this->user = new \stdClass;//$user;
	 }

	final public function __invoke(Handlers\WebResource $resource)
	 {
		$doc = $resource->GetDoc();
		$b = $doc->documentElement->getElementsByTagName('body');
		if($b->length)
		 {
			$n = DOM\NewEl($b[0], 'div', ['class' => '__dolly_mssm_panel']);
			DOM\NewEl($b[0], 'link', ['rel' => 'stylesheet', 'href' => 'https://msapis.com/dollysites/2/panel.css', 'type' => 'text/css', 'media' => 'all']);
			if(Config::GetOption('debug'))
			 {
				if($console = $resource->GetEngine()->GetDebugConsole())
				 {
					$lines = $console->GetLines();
					if(count($lines))
					 {
						$c = DOM\NewEl($b[0], 'div', ['class' => '__dolly_debug_console']);
						foreach($lines as $line) DOM\NewEl($c, 'div', ['class' => '__dolly_debug_console__line'], $line);
					 }
				 }
			 }
			// $n->appendChild($this->ArrayToDOMElement($doc, DS\GetAdminMenuElements($this->user, '__dolly_mssm_', $resource, $resource->GetEngine())));
			$js = <<<'EOD'
(function(){
var b = document.querySelectorAll('.__dolly_mssm_links_list__toggle'), a = 'data-state', v = 'closed';
for(var i = 0; i < b.length; ++i) b[i].onclick = function(){
	if(v === this.getAttribute(a)) this.removeAttribute(a);
	else this.setAttribute(a, v);
	event.stopPropagation();
	event.preventDefault();
	event.stopImmediatePropagation();
	return false;
};
})();
EOD;
		// $b[0]->appendChild($this->Node($doc, 'script', ['type' => 'text/javascript'], $js));
		 }
		// if(SUNDER_DEBUG) set_error_handler(function($no, $str, $file, $line, $context) use($code, $layout_name, $src, $caller){
			// set_exception_handler('\Sunder\Debug\ExceptionHandler');
			// restore_error_handler();
			// throw new ESunderInvalidXMLFragment(str_replace('DOMDocumentFragment::appendXML(): ', '', $str), $no, libxml_get_last_error(), $code, $layout_name, $src, $caller);
		// });
		// if(!@$fragment->appendXML(false === strpos($code, '&') ? $code : n::ReplaceHTMLEntities($code)))
		 // {
			// if(self::$on_invalid_fragment) call_user_func(self::$on_invalid_fragment, libxml_get_last_error(), $layout_name, $src, $caller);
			// $fragment->appendXML('<div class="sunder_invalid_fragment">An error has occurred during fragment processing.</div>');
		 // }
		// if(SUNDER_DEBUG) restore_error_handler();
	// if(($e instanceof \ESunderInvalidXMLFragment) && ('On' == ini_get('display_errors')))
	 // {
		// $error = $e->GetXMLError();
		// switch($error->level)
		 // {
			// case LIBXML_ERR_WARNING: $level = 'warning'; break;
			// case LIBXML_ERR_ERROR: $level = 'error'; break;
			// case LIBXML_ERR_FATAL: $level = 'fatal error'; break;
			// default: $level = '';
		 // }
	 }

	final protected function ArrayToDOMElement(\DOMDocument $doc, array $el)
	 {
		$n = $this->Node($doc, $el['tagName'], $el['attributes']);
		foreach($el['childNodes'] as $c) $n->appendChild(is_string($c) ? $doc->createTextNode($c) : $this->ArrayToDOMElement($doc, $c));
		return $n;
	 }

	final public function GetLabel() : string {}

	private $user;
}