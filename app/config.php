<?php
$time = microtime(true);

ini_set('error_reporting',E_ALL);
ini_set('display_errors','on');
#ini_set('docref_root','http://cz2.php.net/');
ini_set('html_errors',false);
ini_set('precision','14');

# ↑↑ It will be overriden in init.php with the configuration from database. ↑↑
# ↓↓ You have to set up the folowing constants. ↓↓

# -- location --
define('SITE_ROOT_PATH','http://blog.blackpig.cz/cms/');
#define('SITE_ROOT_PATH','http://localhost/cms/');
#define('SITE_ROOT_PATH','http://192.168.123.12/cms/');

define('ESCMS_DATE_FORMAT','d. F Y');
define('ESCMS_TIMEZONE','Europe/Prague');

# -- database --
define('ESCMS_DB_PREFIX','escms_');

define('ESCMS_DB_TYPE','sqlite2');
define('ESCMS_DB_FILE','./app/.db/.database.db');

# -- debug --
define('ESCMS_DEBUG_ENABLE',true);


# ↓↓ You should not change any value of the following constants! ↓↓

# -- output --
define('ESCMS_OUTPUT_ENCODING','utf-8');

# -- internal --
define('ESCMS_PATH_LIB','./app/lib/');
define('ESCMS_PATH_DAL_DRIVERS','./app/lib/sql/');
define('ESCMS_PATH_TPL','./styles/'); # replace! -> db
?>