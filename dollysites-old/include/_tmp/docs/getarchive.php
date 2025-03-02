<?php
namespace DollySites\Docs;
use \DollySites as DS;
use \MaxieSystems as MS;

class GetArchive extends MS\Document
{
	final public function Show()
	 {
		
	 }

	final public function Handle()
	 {
		// if(file_exists($this->fname))
		 // {
			// $content = file_get_contents($this->fname);
			// $content = str_replace(['xxxx-xxx---dollysites-api-key---xxx-xx', 'xxxx-xxx---dollysites-api-language---xxx-xx', 'xxxx-xxx---dollysites-api-version---xxx-xx'], [MS\SMAuth()->user->product_key, $this->GetLang(), $this->GetVersion()], $content, $count);
			// header('Content-Type: text/x-php');
			// header('Content-Disposition: attachment; filename="index.php"');
			// header('Content-Length: '.strlen($content));
			// die($content);
		 // }
		// else $this->AddErrorMsg($this->L10N()->{'e_http/file_not_found'});
	 }
}
?>