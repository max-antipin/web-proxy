<?php
namespace MaxieSystems;
// Config::RequireFile('types', 'traits\\options', 'http');
// $config = SimpleConfig::Instance('main');
// Config::RegisterClasses('html', 'html.input', 'idna_convert');
return;
// error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED);
Config::RegisterClasses('authenticator', 'document\\resourcemanager', 'traits\\instances', 'dbmeta', 'document.ui', 'debug', 'events', 'filesystem', 'msgqueue', 'registry', 'dropdown', 'select', 'phpinfo');
// 'arrayresult', 'changepassword', 'colconf', 'db', 'dbregval', 'dbtable', 'dropdownlist', 'file', 'filesystemstorage', 'fileuploader', 'filter', 'form', 'format', 'formatdate', 'html', 'http', 'icons', , 'imageprocessor', 'imageuploader', 'imageuploaderurl', 'mk', 'ms', 'ms4xxlog', 'msbreadcrumbs', 'msdataloader', 'msdbtable', 'msemailtpl', 'msfbuttons', 'msfieldset', 'msmail', 'msmessagefieldset', 'msnotifications', 'msnotificationsviewer', 'msoauth2', 'msoptions', 'mssimplelist', 'mstable', 'mstableorder', 'pagenav', 'pagenavl', 'pagetreeaction', 'queue', 'radio', 'searchselect', 'smprofile', 'sqlexpr', 'streamuploader', 'timeleft', 'timemeter', 'pagetree', 'unifiedresult', 'uploader', 'watermark'
// MS\Config::RequireFile('traits', 'containers', 'l10n', 'errorstreams', 'authenticator');
//'auth.utils', 'estreams', 'types', 'containers', 'containers/dataelementproxy', 'containers/stdclassproxy', 'l10n', 
// Config::RequireFile('db', 'sqldb', 'estreams', 'containers', 'containers/dataelementproxy', 'containers/stdclassproxy', 'l10n');
// MS\Config::SetErrorStreams(new MS\ErrorStream());
MS\Authenticator::SetStorageClass('\\DollySites\\AuthStorage');
require_once(MS\INC_DIR.'dollysites.php');
$locale = 'ru_RU';
setlocale(LC_ALL, "$locale.utf8");
setlocale(LC_NUMERIC, 'en_US.utf8');
date_default_timezone_set('Europe/Moscow');// это нужно выбирать в настройках.
require_once(MS\INC_DIR.'fix.php');

interface IConst
{
	const JQUERY = 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js';
	const YMAPS = 'https://api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU';
	const MSAPIS = 'https://msapis.com';
	const AUTH_SESS_LEN = 60;
}

$langs = new L10N\LanguagesArray(['en' => 'English', 'ru' => 'Русский']);
$select = new DollySites\SelectLang($langs);
new L10N\Storage($select, ['root' => MS\INC_DIR, 'dir' => 'lang']);
new L10N\Storage($select, ['root' => MS\LIB_DIR, 'dir' => 'lang', 'index' => 'msse']);
function l10n($dir = '') { return '' === $dir ? L10N\Storage::Instance() : L10N\Storage::Instance()->__invoke($dir); }
MS\Auth\Utils\ConfigPasswordResetHref('https://cp.dollysites.com/', false);