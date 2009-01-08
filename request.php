<?php
require_once('./app/layers/debug.layer.php');

if(isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) define('REQUEST_RAW',$_SERVER['REQUEST_URI']);
elseif(isset($_SERVER['REDIRECT_URL']) && !empty($_SERVER['REDIRECT_URL'])) define('REQUEST_RAW',$_SERVER['REDIRECT_URL']);
elseif(isset($_SERVER['REDIRECT_SCRIPT_URL']) && !empty($_SERVER['REDIRECT_SCRIPT_URL'])) define('REQUEST_RAW',$_SERVER['REDIRECT_SCRIPT_URL']);
elseif(isset($_SERVER['SCRIPT_URL']) && !empty($_SERVER['SCRIPT_URL'])) define('REQUEST_RAW',$_SERVER['SCRIPT_URL']);
else define('REQUEST_RAW',false);

if(!REQUEST_RAW)
{
	header(404);
}

require('./app/init.php');
?>