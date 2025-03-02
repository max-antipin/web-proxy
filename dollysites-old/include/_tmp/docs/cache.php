<?php
namespace DollySites\Docs;
use \DollySites as DS;
use \MaxieSystems as MS;
use \MaxieSystems\Document\UI;
use \MaxieSystems\HTML;

class Cache extends MS\Document
{
	final public function __construct($engine)
	 {
		$this->engine = $engine;
	 }

	final public function Show()
	 {
		if(!$this->engine->GetCache())
		 {
			echo l10n()->cache_disabled, '. <a href="settings/">', l10n()->cache_settings, '</a>';
			return;
		 }
		$this->AddJS('cache', 'lib.treeview')->AddCSS('cache', 'lib.treeview');
		print(new HTML\Button('class', 'msui_small_button _icon _delete b_dolly_clear_cache', 'value', l10n()->clear_cache));
?><div class='cached_files'></div><?php
	 }

	final public function Handle()
	 {
		if('treeview:get_items' === $this->ActionGET())
		 {
		$root = DOCUMENT_ROOT;
		$max_level = 0;
		$data = [];
		if($max_level) MS\Config::RequireFile('file');
		$read_dir = function($dir, array &$data, $level = 1) use(&$read_dir, $root, $max_level){
			$data['items'] = [];
			$rdir = $root.DIRECTORY_SEPARATOR.$dir;
			if($d = opendir($rdir))
			 {
				while($f = readdir($d))
				 {
					if($f === '.' || $f === '..') continue;
					$name = ('' === $dir ? '' : $dir.DIRECTORY_SEPARATOR).$f;
					$data['items'][$f] = [];
					$rfile = $rdir.DIRECTORY_SEPARATOR.$f;
					if(is_dir($rfile))
					 {
						if(is_link($rfile)) $data['items'][$f]['type'] = 'l';
						else
						 {
							$data['items'][$f]['type'] = 'd';
							if($max_level && $level >= $max_level) $data['items'][$f]['items'] = MS\Files::DirIsEmpty($rfile) ? [] : true;
							else $read_dir($name, $data['items'][$f], $level + 1);
						 }
					 }
					else
					 {
						$data['items'][$f]['type'] = is_link($rfile) ? 'l' : 'f';
						$data['items'][$f]['size'] = filesize($rfile);
					 }
					$data['items'][$f]['ctime'] = filectime($rfile);
				 }
				closedir($d);
			 }
		};
		$read_dir('', $data);
		// echo '<pre>';
		// var_dump($data);
		// echo '</pre>';
			$this->SendJSON($data);
		 }
		switch($this->ActionPOST())
		 {
			case 'clear_cache':
				if($c = $this->engine->GetCache())
				 {
					$c->Clear();
					$this->AddSuccessMsg('Done.');
				 }
				break;
		 }
	 }

	private $engine;
}
?>