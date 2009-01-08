<?php
require_once('./app/config.php');

header('Content-Type: text/html; charset='.ESCMS_OUTPUT_ENCODING);

require_once('./app/layers/debug.layer.php');
set_exception_handler(array('Debug','catchException'));
set_error_handler(array('Debug','catchError'));
date_default_timezone_set(ESCMS_TIMEZONE);

Debug::$enabled = ESCMS_DEBUG_ENABLE;

require_once('./app/layers/lib.layer.php');
require_once('./app/layers/dal.layer.php');
require_once('./app/layers/tpl.layer.php');

require_once('./app/layers/core.layer.php');

Lib::$dirpath = ESCMS_PATH_LIB;
SQL::$dirpath = ESCMS_PATH_DAL_DRIVERS;
TPL::$dirpath = ESCMS_PATH_TPL . 'looksmog/'; # replace! -> db

TPL::add('SITE_ROOT_PATH',SITE_ROOT_PATH);

switch(ESCMS_DB_TYPE):
	case('sqlite2'): $params = array('file' => ESCMS_DB_FILE); break;
endswitch;
SQL::openConnection(array(
	'type' => ESCMS_DB_TYPE,
	'prefix' => ESCMS_DB_PREFIX,
	'params' => $params
));

if(defined('REQUEST_RAW') && !defined('REDIRECTED'))
{
	$e = explode(SITE_ROOT_PATH,'http://'.$_SERVER['HTTP_HOST'].REQUEST_RAW,2);
	TPL::add('PAGE_URI',SITE_ROOT_PATH.$e[1]);
	define('REQUEST','/'.end($e));
	
	require_once('./app/resolve.php');
	die();
}
else
{
	$e = explode(SITE_ROOT_PATH,'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	define('PAGE_URI',SITE_ROOT_PATH.$e[1]);
}

//SQL::exec("CREATE TABLE [config]p ( [key] VARCHAR(32) NOT NULL UNIQUE, [value] VARCHAR(256) NULL, [type] VARCHAR(8) NOT NULL, [assign] BOOLEAN )");
//Debug::dump(SQL::exec("INSERT INTO [config]p ([key],[value],[type],[assign]) VALUES ('URL_BLOG_POST','./clanky/%slug/','string',0)"));
ESCMS::loadConfig();
ESCMS::loadModules();

Debug::log();

TPL::pack();

print((round(microtime(true)-$time,3)*1000).' ms');
?>