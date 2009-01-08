<?php
class Lib
{
	public static $dirpath = './';
	public static $ext = '.class.php';
	private static $initialized = array();
	
	public static function load($libraryName,$ext = false)
	{
		if(!$ext) $ext = self::$ext;
		
		if(isset(self::$initialized[self::$dirpath.$libraryName.$ext])) return(true);
		
		$exc = new Exception();
		self::$initialized[self::$dirpath.$libraryName.$ext] = array(
			'name' => $libraryName,
			'path' => self::$dirpath . $libraryName . $ext,
			'trace' => $exc->getTrace()
		);
		
		if(include_once(self::$dirpath . $libraryName . $ext))
			return(self::$initialized[self::$dirpath.$libraryName.$ext]['success'] = true);
		else
			return(self::$initialized[self::$dirpath.$libraryName.$ext]['success'] = false);
	}
	
	public static function getList($trace = true)
	{
		$list = array();
		$i = 0;
		foreach(self::$initialized as $libraryName => $params)
		{
			$list[$i] = array(
				'name' => $params['name'],
				'path' => $params['path'],
				'file' => $params['trace'][count($params['trace'])-1]['file'],
				'line' => $params['trace'][count($params['trace'])-1]['line'],
				'function' => $params['trace'][count($params['trace'])-1]['function'],
				'args' => $params['trace'][count($params['trace'])-1]['args']
			);
			if($trace) $list[$i]['trace'] = $params['trace'];
			
			++$i;
		}
		
		return($list);
	}
}
?>