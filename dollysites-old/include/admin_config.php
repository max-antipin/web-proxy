<?php
namespace MaxieSystems;
function RunDollyAdmin(string $admin_path, string $dir, URL\Path $query_path, DollyConfig $config)
{
	// if('GET' === $_SERVER['REQUEST_METHOD'] && !$query_path->trailing_slash) echo $query_path;// если GET и нету завершающего слэша, то делать 301 редирект.
	require_once(INC_DIR.'dollysites-old.php');// !!!
	$engine = \DollySites\CreateEngine($config);// !!!
	$src_url = URL::Build($engine->GetSource());
	echo 'Копируем сайт: ', $src_url, ' | ', __FUNCTION__, '<br />';
	$u = new URL('/');
	$engine->SetBaseURL($u);
	// var_dump($engine, $engine->SetBaseURL((object)['path' => '/']));
	echo 'Кэш: ';
	if($cache = $engine->GetCache())
	 {
	echo '<pre>';
		var_dump($cache);
		// var_dump($cache);//Здесь выводить настройки кэширования - пока только чекбоксы без фильтров.
	echo '</pre>';
	 }
	else echo 'отключен';
	echo '<hr />';
	$urls = [
		'https://revolted.ru/html/molla/',
		'/html/molla/',
		'/html/molla',
		'html/molla',
		'/html/molla/index.html',
		'https://revolted.ru/html/molla/portfolio.html',
		];
	$urls = [
		'https://www.revolted.ru/index.php/abc',
		'',
		'/',
		'/test/',
		'//www.revolted.ru/index.php/abc',
		'https://revolted.ru//index.php/abc-def#anchor-1',
		'/index.php/abc-def/index.html',
		'/index.php/название-кириллицей-без-кодировки/',
		'index.php/относительный-url-и-тоже-без-кодировки/',
		'/images/FOVB3SCI001.jpg',
		'/script.js',
		'//revolted.ru//index.php/abc-def',
		'https://static.revolted.ru/nonexisting.css',
		'https://images.revolted.ru/images/FOVB3MED001.jpg',
		'//images.revolted.ru/images/FOVB3MED001.jpg',
		'https://dolly-source.msapis.com/images/favicon',
		'//dolly-source.msapis.com/images/favicon',
		'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.min.js',
		'https://code.jquery.com/jquery-3.3.1.min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css',
		'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js',
		'https://fonts.googleapis.com/css?family=PT+Sans&subset=cyrillic,latin-ext',
		'https://use.fontawesome.com/releases/v5.2.0/css/fontawesome.css',
		'https://vk.com/maxthegodslayer',
		'https://sun1-21.userapi.com/s/v1/ig2/pZQ7PvCLv0a_kodpmY6tT7urdfB-3WDi7_3S6D2daPUS509EuujxjvysTeAx8_q6CrA_VBwwu7lJhjJa0VWEiVAd.jpg?size=200x0&quality=96&crop=0,50,960,960&ava=1',
		'https://sun1-21.userapi.com/s/v1/ig2/pZQ7PvCLv0a_kodpmY6tT7urdfB-3WDi7_3S6D2daPUS509EuujxjvysTeAx8_q6CrA_VBwwu7lJhjJa0VWEiVAd.jpg?size=200x0&quality=96&crop=0,50,960,960&ava=1',
		'https://sun1-21.userapi.com/s/v1/ig2/pZQ7PvCLv0a_kodpmY6tT7urdfB-3WDi7_3S6D2daPUS509EuujxjvysTeAx8_q6CrA_VBwwu7lJhjJa0VWEiVAd.jpg?size=200x0&quality=96&crop=0,50,960,960&ava=1',
		'https://st1-18.vk.com/images/login/en/reg_iphone_en.png',
		'mailto:maxxx.test@gmail.com',
		'https://msapis.com/msse/core.100.js',
		// "https://fonts.googleapis.com/css?family=PT+Sans+Caption&subset=cyrillic,latin-ext" "https://use.fontawesome.com/releases/v5.2.0/css/solid.css" "https://static.revolted.ru/style.css',
		// '/" string(6) "/test/" string(31) "//www.revolted.ru/index.php/abc" string(37) "https://www.revolted.ru/index.php/abc" string(32) "//revolted.ru//index.php/abc-def" string(38) "https://revolted.ru//index.php/abc-def" string(29) "/index.php/abc-def/index.html" string(75) "/index.php/название-кириллицей-без-кодировки/
	];
// Какую информацию я хочу выводить для каждой ссылки? Преобразован или нет. Как преобразован. Кэшируется или нет. Если кэшируется, то есть ли эта страница в кэше и сколько места занимает.
	foreach($urls as $u)
	 {
		$url = $engine->GetProxyURL($u, '');
		echo '<pre>';
		if($url)
		 {
			if($u !== $url->u_src->initial) echo GetVarDump($u), PHP_EOL;// var_export($u);
			echo 'Исходное значение, string: ', $url->u_src->initial, PHP_EOL;// Здесь исходное значение отличается от значения атрибута !!! Исправить!!!
			echo 'Исходный URL, абсолютный : ', $url->u_src, PHP_EOL;
			echo 'Proxy URL final, object  : ', $url, PHP_EOL;
			echo 'Новое значение атрибута  : ', $url->ToAttrValue(), PHP_EOL;
			echo '    ', $url->u_abs ? 'Абсолютный' : 'Относительный', '  -  ', $url->u_sub < 0 ? 'Внешний домен' : ($url->u_sub > 0 ? 'Поддомен' : 'Основной домен'), PHP_EOL;//u_modified
			echo '    Source URL из строки : ', PHP_EOL;
		 }
		else echo $u;
		echo '</pre>';
	 }
	echo '<hr />HTTP Referer [Копируем: ', $src_url, ']';
	$refs = [
		'http://dolly.maxtheps.beget.tech/',
		'http://dolly.maxtheps.beget.tech/d/https/dolly-source.msapis.com/1mz9bf0y2c~/',
		'http://static.dolly.maxtheps.beget.tech/styles/homepage.css',
		'http://www.dolly.maxtheps.beget.tech/',
		'http://dolly.maxtheps.beget.tech/?fbclid=IwAR1E84uau7m7SX4K6pPfic9AK3qpWMit8fHWA8CGZSIroMd1xDKKVnrmP_Q',
		'http://msse2.maxtheps.beget.tech/',
		'https://l.facebook.com/',
		'http://revolted.ru/test/',
		'http://www.revolted.ru/index.php/%D0%BD%D0%B0%D0%B7%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5-%D0%BA%D0%B8%D1%80%D0%B8%D0%BB%D0%BB%D0%B8%D1%86%D0%B5%D0%B9-%D0%B1%D0%B5%D0%B7-%D0%BA%D0%BE%D0%B4%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B8/',
		'http://revolted.ru//index.php/abc-def',
		'#',
		'',
		'///',
		'mailto:maxxx.test@gmail.com',
	];
	foreach($refs as $u)
	 {
		echo '<pre>', $u, PHP_EOL, $engine->ConvertReferer($u), '</pre>';
	 }
		// Config::RequireFile('events', 'smauth', 'document');
		// Loader::SetSMURL($admin_path);
		// require_once(INC_DIR.'admin_config.php');
		// require_once(INC_DIR.'document.php');
		// Loader::RunDocument();
	exit;
}
return;
// 'REQUEST_URI' => '/',
// 'REQUEST_URI' => 'http://test-abcd.demo.dollysites.ru/index.php/xyz?a=5',
// 'REQUEST_URI' => 'http://test-abcd.demo.dollysites.ru',
// 'REQUEST_URI' => 'http://test-abcd.demo.dollysites.ru/',
// 'REQUEST_URI' => 'http://test-abcd.demo.dollysites.ru//?a=5',
// 'REQUEST_URI' => 'http://test-abcd.demo.dollysites.ru/?a=5',
// 'REQUEST_URI' => 'http://test-abcd.demo.dollysites.ru?a=5',
// 'REQUEST_URI' => 'http://test-abcd.demo.dollysites.ru?5',
$lang = L10N\Storage::Instance('msse')->GetLang();
use \MaxieSystems\ResourceManager as MSRM;
MSRM::RegisterJSLink('jquery', \IConst::JQUERY);
MSRM::RegisterJSLink('jqueryui', 'https://yandex.st/jquery-ui/1.11.2/jquery-ui.min.js');
MSRM::RegisterCSSLink('jqueryui', 'https://yastatic.net/jquery-ui/1.11.2/themes/cupertino/jquery-ui.min.css');
if('en' !== $lang) MSRM::RegisterJSLink('jqueryui_datepicker_i18n', "https://yandex.st/jquery-ui/1.10.3/i18n/jquery.ui.datepicker-$lang.min.js");
MSRM::RegisterJSLink('editor', '/system/js/ckeditor/ckeditor.js');
MSRM::RegisterJSLink('ymaps', 'https://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU');// !!! locale !!!
MSRM::RegisterScript('htmleditor', function(){
	MSRM::AddJSLink('editor');
	MSRM::AddJS('lib.htmleditor');
});
MSRM::RegisterScript('datepicker', function() use($lang){
	MSRM::AddCSSLink('jqueryui');
	MSRM::AddJSLink('jqueryui');
	if('en' !== $lang) MSRM::AddJSLink('jqueryui_datepicker_i18n');
});
Loader::AddDefaultFactory('\\MaxieSystems\\Factory');
Document::DisableLogging();
Document::SetProductInfo('<a href="https://dollysites.com/">DollySites</a> '.\IConst::PRODUCT_VERSION);
Events::BindTo('msdocument:on_show', function(Containers\Data $d){
	$conf = SimpleConfig::Instance('main');
	$can_check_for_updates = (time() - (int)$conf->updates__last_check >= 60 * 180);
	$l10n = $d->l10n->__invoke('~updates');
	$btn_check = new HTML\Input\Button(['class' => 'msui_small_button updates_available__b_check', 'value' => $l10n->b_check_for_updates]);
	$btn_update = new HTML\Input\Button(['class' => 'msui_small_button updates_available__b_apply', 'value' => $l10n->b_apply_updates, 'disabled' => true]);
	$a = $can_check_for_updates ? 'true' : 'false';
	$d->main_menu_bottom = "<div class='updates_available' data-auto='$a' data-path=''>
	<strong class='updates_available__header'>$l10n->updates</strong>$btn_check
	<div class='updates_available__progbar btn_loader'></div>
	<div class='updates_available__list'>$l10n->no_info...</div>
	<div class='updates_available__footer _hidden'>$btn_update</div>
</div>";
	$l10n = l10n();
	$html = '';
	if($conf->base_url)
	 {
		$domain = (new \idna_convert())->decode(parse_url($conf->base_url, PHP_URL_HOST));
        $html .= "$l10n->source:<br /> <a target='_blank' href='$conf->base_url'>$domain</a>";
	 }
	else
	 {
		$html .= "<a href='{$d->base_url}settings/'>$l10n->copy_new_site</a>";
	 }
	$d->main_menu_bottom = "<div class='main_menu__source_site'>$html</div>";
	MSRM::AddJS('lib.updates');
	MSRM::AddCSS('common');
});
Config::AddAutoload(['dir' => INC_DIR, 'ns' => 'dollysites', 'ns_remove' => 0]);