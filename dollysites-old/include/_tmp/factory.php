<?php
namespace MaxieSystems;
use \DollySites as DS;
use \MaxieSystems\Docs;

class Factory implements IFactory
{
	final public function CreateDocument($id)
	 {
		if('' === $id) return new DS\Docs\Dashboard();
		switch($id)
		 {
			case 'cache': return new DS\Docs\Cache(DS\CreateEngine(DS\Conf()->source_url));
			case 'cache/settings': Config::RequireFile('docs.simpleconfig');
				$conf = SimpleConfig::Instance('main');
				$doc = new Docs\SimpleConfig([
					'root' => $conf->GetOption('root'),
					'file' => $conf->GetOption('file'),
				]);
				$this->ConfigCacheSettings($doc);
				return $doc;
			case 'content':
				Config::RequireFile('docs.simpleconfig');
				$upFirstLetter = function($str, $encoding = 'UTF-8'){return mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).mb_substr($str, 1, null, $encoding);};
				$translators = array(
					'yandex' => 'Yandex '.l10n()->translator,
					'google' => 'Google '.l10n()->translator,
					// 'BaiduTranslate' => 'Baidu ' . l10n()->translator,
				);
				$target = array('en' => 'Английский', 'ru' => 'Русский');// locale!!!
				$source = array('ru' => 'Русский', 'en' => 'English');// locale!!!
				$langs = [];
				foreach(l10n('lang_list')->__toArray() as $k => $v) $langs[$k] = $upFirstLetter($v);
				$conf = SimpleConfig::Instance('main');
				$doc = new Docs\SimpleConfig([
					'root' => $conf->GetOption('root'),
					'file' => $conf->GetOption('file'),
					'field_omission_allowed' => true,
				]);
				$doc->BindToEvent('on_show', function($d){
					$d->doc->AddJS('content');
				});
				$disabled_1 = function() use($doc){ return !$doc->GetFieldSet()->GetField('translate')->GetValue(); };
				$doc->OpenGroup('gr_translator', l10n()->translator);
				$doc->Add('translate', l10n()->turn_on.' '.l10n()->translator, ['type' => 'Checkbox']);
				$doc->Add('translator', l10n()->select_translator, ['type' => 'Select', 'data' => $translators, 'f_value' => '', 'disabled' => $disabled_1]);
				$doc->Add('translate_source', l10n()->site_language, ['type' => 'Select', 'data' => array_merge($source, $langs), 'f_value' => '', 'disabled' => $disabled_1]);
				$doc->Add('translate_target', l10n()->translate_into, ['type' => 'Select', 'data' => array_merge($target, $langs), 'f_value' => '', 'disabled' => $disabled_1]);
				// $doc->OpenGroup('gr_synonymizer', l10n()->synonymizer);
				// $doc->Add('', '', []);
				// $doc->OpenGroup('gr_settings', l10n()->settings);
				// $doc->Add('syn_pos', l10n()->trigger_first, ['type' => 'Select', 'data' => ['_1' => l10n()->synonymizer, '_2' => l10n()->translator], 'f_value' => '']);
				return $doc;
			case 'forms-handlers': return new DS\Docs\FormsHandlers();
			case 'get-archive': return new DS\Docs\GetArchive();
			case 'images': return new DS\Docs\Images();
			case 'php-info': return new PHPInfo();
			case 'replacements': return new DS\Docs\Replacements();
			case 'proxy': return new DS\Docs\Proxy();
			case 'settings':
			case 'settings/all': Config::RequireFile('docs.simpleconfig');
				$conf = SimpleConfig::Instance('main');
				$doc = new Docs\SimpleConfig([
					'root' => $conf->GetOption('root'),
					'file' => $conf->GetOption('file'),
				]);
				$doc->Add('source_url', 'URL', ['type' => 'Text', 'required' => true]);
				if('settings/all' === $id)
				 {
					$doc->OpenGroup('gr_cache', l10n()->cache);
					$this->ConfigCacheSettings($doc);
				 }
				return $doc;
				// case 'settings/watermark/images': return new MSWatermark(IATL::DIR_IMAGE, MS\Page::GetStaticRoot(), MS\Page::GetStaticHost());
		 }
	 }

	final public function CreateMenuGroup($id)
	 {
		foreach(DS\GetAdminMenu() as $v)
		 {
			if(isset($v['external']))
			 {
				unset($v['external']);
				MainMenu::AddExternal(...$v);
			 }
			else MainMenu::AddItem(...$v);
		 }
		MainMenu::AddItem('settings/all', l10n()->settings, false, ['hide' => true]);
	 }

	final private function ConfigCacheSettings($doc)
	 {
		$doc->BindToEvent('on_show', function($d){
			$d->doc->AddJS('cache_settings');
		});
		$doc->Add('cache_type', 'Тип', ['type' => 'Select', 'data' => DS\CacheTypes::Get(), 'f_value' => ''])
			->OpenGroup('gr_cache_db', null, null, true)
			->Add('db_name', l10n()->db_name, [])
			->Add('db_username', l10n()->username, [])
			->Add('db_password', l10n()->password, [])
			->Add('db_host', l10n()->host, ['default' => 'localhost'])
			->OpenGroup('gr_cache_other_domains', l10n()->cache_other_domains, null, true)
			->Add('cache_external_css', l10n()->external_scripts, ['type' => 'Checkbox'])
			->Add('cache_external_img', l10n()->images, ['type' => 'Checkbox'])
			->CloseGroup();
	 }
}
?>