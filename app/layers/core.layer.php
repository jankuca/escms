<?php
class ESCMS
{
	private static $cfg;
	
	public static function loadConfig()
	{
		//SQL::exec("INSERT INTO [config]p ([key],[value],[type],[assign]) VALUES ( 'url_blog_category','./%slug/','string',0 )");
		if($result = SQL::query("SELECT [key],[value],[type],[assign] FROM [config]p"))
		{
			foreach($result->fetch() as $item)
			{
				switch($item->type):
					case('string'): ESCMS::$cfg[$item->key] = (string) $item->value; break;
					case('int'):case('integer'): ESCMS::$cfg[$item->key] = (int) $item->value; break;
					case('boolean'): ESCMS::$cfg[$item->key] = (boolean) $item->value; break;
					default: ESCMS::$cfg[$item->key] = $item->value; break;
				endswitch;
				
				if((boolean) $item->assign) TPL::add($item->key,$item->value);
				
				define('CFG_'.strtoupper($item->key),$item->value);
			}
		}
		else throw new Exception('The configuration could not be loaded.');
	}
	
	public static function loadModules()
	{
		//Debug::dump(self::$cfg);
		//SQL::exec("INSERT INTO [modules]p ([codename],[core],[active],[order]) VALUES ('blog',0,1,1)");
		if($result = SQL::query("SELECT [codename],[core] FROM [modules]p WHERE ([active] == 1) ORDER BY [core] DESC, [order] ASC"))
		{
			foreach($result->fetch() as $module)
			{
				if(defined('IN_ACP')) $ext = '.acp';
				elseif(defined('IN_ACTION')) $ext = '.action';
				else $ext = '.sys';
				
				if((boolean) $module->core) $path = './app/modules/' . $module->codename . '.mod.php';
				else $path = './modules/' . $module->codename . '.mod.php';
				if(file_exists($path))
					if(!include_once($path)) Debug::error('The module <strong>' . $module->codename . '</strong> could not be loaded.');
				
				if((boolean) $module->core) $path = './app/modules/' . $module->codename . $ext . '.mod.php';
				else $path = './modules/' . $module->codename . $ext . '.mod.php';
				if(file_exists($path))
					if(!include_once($path)) Debug::error('The module <strong>' . $module->codename . '</strong> could not be loaded.');
			}
		}
		else throw new Exception('The system modules could not be loaded.');
	}
}

interface ESCMSModule
{
	# Returns an stdClass object with module info (codename, version, author, ...)
	# 			
	# @return stdClass
	public function getModuleInfo();
}
?>