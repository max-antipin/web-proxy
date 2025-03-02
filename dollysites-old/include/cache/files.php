<?php
namespace DollySites\Cache;
use DollySites as DS;
use \MaxieSystems\{URL};

class Files extends DS\Cache
{
	// public function __construct(array $options)
	 // {
		// $this->AddOptionsMeta(['dir' => ['type' => 'string']]);
		// $this->SetOptionsData($options);
		// parent::__construct($options);
	 // }

	// public function Write(MS\URL $url, $url_type, array $content, array $meta)
	 // {
		// $m_file_name = $this->URL2FileName($url, $url_type, null);
		// $dir_name = dirname($m_file_name);
		// if(!file_exists($dir_name)) mkdir($dir_name, 0777, true);
		// file_put_contents($m_file_name, $this->SerializeMeta($meta));
		// foreach($content as $k => $v)
		 // {
			// $file_name = $this->URL2FileName($url, $url_type, $k);
			// $dir_name = dirname($file_name);
			// if(!file_exists($dir_name)) mkdir($dir_name, 0777, true);
			// file_put_contents($file_name, $v);
		 // }
	 // }

	public function GetMeta(URL $url) : array//, &$file_name = null
	 {
		// $file_name = $this->URL2FileName($url, $url_type, null);
		// if(file_exists($file_name))
		 // {
			// $m = (require $file_name);
			// if(!empty($m) && is_array($m)) return $m;
		 // }
		return [];
	 }

	// public function Delete(MS\URL $url, $url_type)
	 // {
		// if($meta = $this->GetMeta($url, $url_type, $file_name))
		 // {
			// unlink($file_name);
			// foreach($meta['stages'] as $k => $v)
			 // {
				// $file_name = $this->URL2FileName($url, $url_type, $k);
				// if(file_exists($file_name)) unlink($file_name);
			 // }
		 // }
	 // }

	// public function GetContent(MS\URL $url, $url_type, $index)
	 // {
		// $file_name = $this->URL2FileName($url, $url_type, $index);
		// if(file_exists($file_name)) return file_get_contents($file_name);
	 // }

	// public function Clear()
	 // {
		// MS\File::rmdir($this->GetDir(), false);
	 // }

	final private function URL2FileName(MS\URL $url, $type, $stage)
	 {
		$url = clone $url;
		if($url->path->is_dir)
		 {
			if(count($url->query))
			 {
				$url->path .= sha1($url->query);
				$url->query = '';
			 }
			else $url->path .= 'index.php';
		 }
		elseif(count($url->query))
		 {
			$ext = $url->path->extension;
			$url->path = $url->path->dirname.'/'.sha1($url->path->basename.'?'.$url->query);// !!! а если файл именно так называется???
			if($ext) $url->path .= ".$ext";
			$url->query = '';
		 }
		if(0 === $type)
		 {
			$u = $url->Crop('host', 'fragment');
			$p = 'this_';
		 }
		elseif(-1 === $type)
		 {
			$u = substr($url->Crop('scheme', 'fragment'), 1);
			$p = 'domains_';
		 }
		else throw new \Exception('Invalid type: '.MS\Config::GetVarType($type));
		// elseif($url->host->IsIP())
		 // {
			// $file_name = $this->MkFNameDomain($url, false);
			// $m_file_name = $this->MkFNameDomain($url, true);
		 // }
		// else
		 // {
			// if(0 === strpos($source_host, 'www.')) $source_host = substr($source_host, 4);
			// if($url->host->IsSubdomain($source_host, $label))
			 // {
				// $file_name = $this->MkFNameSubdomain($url, false, $label);
				// $m_file_name = $this->MkFNameSubdomain($url, true, $label);
			 // }
			// else
			 // {
				// $file_name = $this->MkFNameDomain($url, false);
				// $m_file_name = $this->MkFNameDomain($url, true);
			 // }
		 // }
		$u = str_replace('/', DIRECTORY_SEPARATOR, $u);
		return $this->GetDir().DIRECTORY_SEPARATOR.$p.(null === $stage ? "meta{$u}.meta" : 'data_'.$stage.$u);
	 }

	final private function SerializeMeta(array $meta)
	 {
		return '<?php'.PHP_EOL.'return '.var_export($meta, true).';'.PHP_EOL.'?>';
	 }

	// final private function GetDir() { return $this->GetOption('dir').DIRECTORY_SEPARATOR.'cache'; }

	// private $urls = [];
}