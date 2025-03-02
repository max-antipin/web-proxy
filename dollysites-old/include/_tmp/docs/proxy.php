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

class Proxy extends MS\Document
{
	final public function Show()
	 {
		$proxy = new MS\FileSystemStorage('storage/proxyservers.php', ['readonly' => true, 'root' => MS\INC_DIR]);
?><form method='post' action='core.php' class='form _proxy'>
		<div class='form__row _hdr'><span class='form__hdr _host'>host</span><span class='form__hdr _port'>port</span><span class='form__hdr _user'>user</span><span class='form__hdr _password'>password</span><span class='form__hdr _type'>type</span><span class='form__hdr _tunnel'>tunnel</span></div><?php
		foreach($proxy as $id => $row)
		 {
?>		<div class='form__row'><?=(new HTML\Input\Text(['name' => "host[$id]", 'value' => $row->host])).(new HTML\Input\Number('name', "port[$id]", 'value', $row->port, 'min', 0, 'max', 65535)).(new HTML\Input\Text('name', "user[$id]", 'value', $row->user, 'autocomplete', false)).(new HTML\Input\Text('name', "password[$id]", 'value', $row->password, 'autocomplete', false)).(new Select(HTTP::GetClassMeta('proxy_types'), ['name' => "type[$id]", 'f_value' => '']))->SetSelected($row->type)->Make().(new HTML\CheckBox('name', "tunnel[$id]", 'checked', $row->tunnel)).(new HTML\Hidden('name', "id[$id]", 'value', $id)).(new HTML\Input\Button('class', 'form__delete_row', 'value', '×', 'title', l10n()->delete))?></div><?php
		 }
		$id = 0;
?>		<div class='form__row _new'><?=(new HTML\Input\Text(['name' => "host_new[$id]"])).(new HTML\Input\Number(['name' => "port_new[$id]", 'min' => 0, 'max' => 65535])).(new HTML\Input\Text(['name' => "user_new[$id]", 'autocomplete' => false])).(new HTML\Input\Text(['name' => "password_new[$id]", 'autocomplete' => false])).(new Select(HTTP::GetClassMeta('proxy_types'), ['name' => "type_new[$id]", 'f_value' => '']))->Make().(new HTML\Input\CheckBox(['name' => "tunnel_new[$id]"])).(new HTML\Input\Button(['class' => 'form__delete_row', 'value' => '×', 'title' => l10n()->delete]))?></div>
		<div class='form__bottom'><?=(new UI\FAction('save')).(new HTML\Input\Submit(['class' => 'msui_button', 'value' => l10n()->save])).(new HTML\Input\Button(['class' => 'msui_small_button _icon _add new_proxy', 'value' => l10n()->add]))?></div>
	</form>
<style type='text/css'>
.form__hdr{font-size:13px;letter-spacing:0.1em;text-transform:uppercase;padding:0 0 4px;color:#7f7f7f;display:inline-block;text-indent:8px;margin:0 2px 0 0;}
.form__hdr._host, .form__row input[name^='host']{width:195px;}
.form__hdr._port, .form__row input[name^='port']{width:80px;}
.form__hdr._user, .form__row input[name^='user']{width:140px;}
.form__hdr._password, .form__row input[name^='password']{width:140px;}
.form__hdr._type, .form__row select[name^='type']{width:110px;}
.form__row input[type='checkbox']{padding:0;margin:0 0 0 12px;}
.form__row{margin:0 0 8px;white-space:nowrap;}
.form__row._hdr{white-space:nowrap;}
.form__bottom{padding:11px 0;position:relative;}
.form__row input[type='text'], .form__row input[type='number'], .form__row select{background:#f6f8fa;border:1px solid #dee0e3;border-radius:5px;margin:0 2px 0 0;padding:5px 9px;box-sizing:border-box;}
.form__row select{padding:4px 7px;}
.msui_small_button.new_proxy{position:absolute;top:14px;left:220px;}
.form__delete_row{margin:0 0 0 30px;padding:0;width:24px;height:24px;text-align:center;background:#fee;border:1px solid #faa;border-radius:3px;color:red;}
</style>
<script type='text/javascript'>(function(){
$('.msui_small_button.new_proxy').click(function(){
	var n = $('.form__row._new').first().clone(false).insertBefore(this.parentNode);
	n.find("input[type='text'], input[type='number']").val('');
	n.find("select").find('option:first').prop('selected', true);
	n.find("input[type='checkbox']").prop('checked', false);
	n.find("input[name^='host']").focus();
});
$('.form._proxy').on('click', '.form__delete_row', function(){
	var b = $(this);
	b.prevAll("input[type='text'], input[type='number']").val('');
	b.prevAll("select").find('option:first').prop('selected', true);
	b.prevAll("input[type='checkbox']").prop('checked', false);
});
})();</script>
		<?php
	 }

	final public function Handle()
	 {
		if('save' === $this->ActionPOST())
		 {
			$proxy = new MS\FileSystemStorage('storage/proxyservers.php', ['readonly' => false, 'root' => MS\INC_DIR]);
			if(!empty($_POST['id']))
			 foreach($_POST['id'] as $k => $v)
			  {
				$proxy->$k->host = trim($_POST['host'][$k]);
				if('' === $proxy->$k->host) unset($proxy->$k);
				else
				 {
					$proxy->$k->port = Filter::GetIntOrNull($_POST['port'][$k], 'gt0');
					$proxy->$k->user = trim($_POST['user'][$k]);
					$proxy->$k->password = trim($_POST['password'][$k]);
					$proxy->$k->type = (int)$_POST['type'][$k];
					$proxy->$k->tunnel = !empty($_POST['tunnel'][$k]);
				 }
			  }
			if(!empty($_POST['host_new']))
			 foreach($_POST['host_new'] as $k => $v)
			  {
				$v = trim($v);
				if('' !== $v) $proxy(['host' => $v, 'port' => Filter::GetIntOrNull($_POST['port_new'][$k], 'gt0'), 'user' => trim($_POST['user_new'][$k]), 'password' => trim($_POST['password_new'][$k]), 'type' => (int)$_POST['type_new'][$k], 'tunnel' => !empty($_POST['tunnel_new'][$k])]);
			  }
		 }
	 }
}
?>