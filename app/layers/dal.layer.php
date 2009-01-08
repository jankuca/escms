<?php
class SQL
{
	public static $dirpath;
	public static $connections = array();
	public static $types = array();
	
	private static function loadType($type)
	{
		if(include_once(self::$dirpath . $type . '.type.php')) return(true);
		else return(false);
	}
	
	public static function openConnection($attr)
	{
		if(empty(self::$dirpath)) return(false);
		if(!isset($attr['type'])) return(false);
		if(!self::loadType($attr['type'])) return(false);

		if(!isset($attr['name']) || !$attr['name']) $name = 0;
		else $name = $attr['name'];
		 
		return(self::$connections[$name] = @call_user_func_array(
			array(self::$types[$attr['type']],'openConnection'),
			array($attr['prefix'],$attr['params'])
		));
	}
	public static function closeConnections()
	{
		foreach(self::$connections as $connection)
			$connection->close();
	}
	
	public static function exec($query,$connection = false)
	{
		if(!$connection) $connection = 0;
		return(self::$connections[$connection]->exec($query));
	}
	public static function query($query,$connection = false)
	{
		if(!$connection) $connection = 0;
		return(self::$connections[$connection]->query($query));
	}
	
	public static function enhanceQuery($query,$connection = false)
	{
		if(!$connection) $connection = 0;
		return(self::$connections[$connection]->enhanceQuery($query));
	}
	
	public static function escape($string,$connection = false)
	{
		if(!$connection) $connection = 0;
		return(self::$connections[$connection]->escape($string));
	}
	
	public static function getLastInsertId($connection = false)
	{
		if(!$connection) $connection = 0;
		return(self::$connections[$connection]->getLastInsertId());
	}
	
	public static function createAggregate($queryFunction,$stepFunction,$finalizeFunction,$args = false,$connection = false)
	{
		if(!$connection) $connection = 0;
		return(self::$connections[$connection]->createAggregate($queryFunction,$stepFunction,$finalizeFunction,$args));
	}
}

interface SQLType
{
	public static function openConnection($prefix,$params);
}
interface SQLConnection
{
	public function __destruct();
	public function exec($query);
	public function query($query);
	public function enhanceQuery(&$query);
	public function escape($string);
	public function getLastInsertId();
	
	public function createAggregate($queryFuction,$stepFunction,$finalizeFunction,$args);
}
?>