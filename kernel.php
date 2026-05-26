<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_PARSE); //error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (preg_match ("/(192.168|10.4.100|10.4.101|10.211.55|127.0.0)/i", $_SERVER["SERVER_ADDR"])) {
	define('S_SRV', 'local');
	define('PRO_HOST', 'http://tilkg');
} else {
	define('S_SRV', 'remote');
	define('PRO_HOST', 'https://uchim.kg');
}

define('TL_SECRET', 'ce25d8f23ba');
define('EXPIRY', 604800);

//#...................... Pathes
define('S_ROOT', dirname(__FILE__));
define('S_LIB', S_ROOT . '/_lib');

define('S_AVA', 'Files/ava');
define('S_PIC', 'Files/pic');
define('S_STORAGE', 'Files/storage');
define('S_UPLOADS', 'Files/uploads');

//#......................Global Constants

//#...................... General options
// Include in All script
if (!defined("PATH_SEPARATOR"))
	define("PATH_SEPARATOR", getenv("COMSPEC")? ";" : ":");
ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.dirname(__FILE__));

//#...................... DSN connect to DB
if (S_SRV == 'local') {
	define('S_DSN_DEFAULT', 'mypdo://tilkg_adm:tl76dc8fJrjdK0cAGZ@localhost/tilkg_srv?charset=utf8');
} else {
	//define('S_DSN_DEFAULT', 'mypdo://cc90219_isra:7dZSvT6n@localhost/cc90219_isra?charset=utf8');
	define('S_DSN_DEFAULT', 'mypdo://user154975_admin:tl76dc8fJrjdK0cAGZ@176.126.165.65/user154975_db?charset=utf8');
}

define('S_TABLE_PREFIX', 'tl_');

date_default_timezone_set("Asia/Bishkek");

//#...................... Include files
require_once S_LIB . '/debug.php';
require_once S_LIB . '/db.php';
require_once S_LIB . '/functions.php';
require_once S_LIB . '/tlclass.php';

$pathInfo = getPath();
$sPath    = $pathInfo[0];
$last     = $pathInfo[1];
$tlreq  = $pathInfo[2];

$TL = new TL_cli;

//#......................Global ARRAYS
$group_statuses = [
	'forming'   => 'Набор',
	'active'    => 'Активная',
	'finished'  => 'Завершена',
	'cancelled' => 'Отменена'
];

$where_translate = [
	'hand'		=> 'Самовывоз',
	'capital'	=> 'Бишкек',
	'region'	=> 'Регион',
];

$defect_statuses = [
	'defective'   => 'Забраковано',
	'returned'    => 'Возвратили'
];

$who_will = ["me"=>"Только я","child"=>"Ребенок","together"=>"Вместе"];
$my_level = ["zero"=>["С нуля","(алфавит)"],"basic"=>["Базовый","(понимаю, но не говорю)"],"intermediate"=>["Средний","(могу объясниться)"]];
$studtype = ["new" =>"Новый", "returned"=>"Вернувшийся"];
$appltype = ["pending" =>"Ожидающая заявка", "assigned"=>"Заявка на группу", "recorded"=>"Записан", "paused"=>"Приостановлен"];
$group_user_status = ["listed"=>"Записан", "enrolled"=>"Зачислен", "paused"=>"Приостановлен"];