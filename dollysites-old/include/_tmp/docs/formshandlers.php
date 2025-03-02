<?php
namespace DollySites\Docs;
use \DollySites as DS;
use \MaxieSystems as MS;
use \MaxieSystems\DB;
use \MaxieSystems\Config;
use \MaxieSystems\Document\UI;
use \MaxieSystems\HTML;
use \MaxieSystems\HTTP;
use \MaxieSystems\L10N;
use Select;
use Filter;

class FormsHandlers extends MS\Document
{
	final public function Show()
	 {
		Config::RequireFile('filesystemstorage');
		$fs_conf = new \MaxieSystems\FileSystemStorage('storage/fs_config.php', ['readonly' => true, 'root' => MS\INC_DIR]);
		if(count($fs_conf))
		 {
?><form method='post' action='core.php' class='form _form_handlers'><?php
	foreach($fs_conf as $id => $row) : ?><div class='form__row'>
	<div class=''><?=$row->title?></div>
	<div class=''><?=$row->subject?></div>
	<div class=''><?=$row->email?></div>
	<input type='checkbox' name='ids[]' value='<?=$id?>' />
</div><?php endforeach;
			echo new UI\FAction('delete_form');
?><input type='submit' value='<?=l10n()->delete?>' disabled='disabled' />
</form>
<script type='text/javascript'>$(function(){
$('.form._form_handlers').submit(function(){return confirm('Удалить отмеченные элементы?');});
var $_IDS = '.form._form_handlers input[name="ids[]"]', i_submit = $('.form._form_handlers [type="submit"]');
$($_IDS).change(function(){i_submit.prop('disabled', !$($_IDS + ':checked').length);});
});</script>
<style type='text/css'>
.form._form_handlers{}
.form._form_handlers .form__row{border:1px solid #ccd8e4;padding:8px 10px 5px 32px;margin:0;position:relative;}
.form._form_handlers .form__row + .form__row{border-top:none;}
.form._form_handlers input[name="ids[]"]{margin:0;padding:0;position:absolute;top:10px;left:10px;}
.form._form_handlers input[type='submit']{margin:10px 0;}
</style><?php
		 }
		else echo new UI\WarningMsg('Не создано ни одного обработчика, они создаются в &laquo;Конструкторе форм&raquo;, в который можно перейти из административного меню, расположенного на каждой странице скопированного сайта.');
	 }

	final public function Handle()
	 {
		if('delete_form' === $this->ActionPOST())
		 {
			if($ids = Filter::NumArrFromPOST('ids'))
			 {
				Config::RequireFile('filesystemstorage');
				$fs_conf = new MS\FileSystemStorage('storage/fs_config.php', ['readonly' => false, 'root' => MS\INC_DIR]);
				foreach($ids as $id) unset($fs_conf->$id);
			 }
		 }
	 }
}
?>