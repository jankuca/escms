<?php
SQL::$types['sqlite2'] = 'SQLType_sqlite2';

class SQLType_sqlite2 implements SQLType
{
	public static function openConnection($prefix,$params)
	{
		if(!isset($params['file'])) return(false);
		if(!isset($params['mode'])) $params['mode'] = 0666;
		
		$connection = new SQLConnection_sqlite2();
		if($connection->open($prefix,$params)) return($connection);
		else return(false);
		return(true);
	}
}

class SQLConnection_sqlite2 implements SQLConnection
{
	private $handle;
	
	public function __destruct()
	{
		if(!is_null($this->handle)) $this->close();
	}
	
	public function open($prefix,$params)
	{
		$this->prefix = $prefix;
		return($this->handle = sqlite_open($params['file'],$params['mode']));
	}
	public function close()
	{
		sqlite_close($this->handle);
		$this->handle = NULL;
	}
	
	public function exec($query)
	{
		$this->enhanceQuery($query);
		
		return(sqlite_exec($this->handle,$query));
	}
	public function query($query)
	{
		$this->enhanceQuery($query);
		 
		if($resource = sqlite_query($this->handle,$query)) return(new SQLResult_sqlite2($resource));
		return(false);
	}
	
	public function enhanceQuery(&$query)
	{
		preg_match_all('#\[(\w+)\]p#s',&$query,$arr);
		foreach($arr[1] as $i => $table)
		{
			$query = str_replace($arr[0][$i],'['.$this->prefix.$table.']',&$query);
		}
		/*
		if(preg_match('#^SELECT(.*?)FROM#is',trim(&$query)))
		{
			$columns = explode(',',trim(end(explode('SELECT',reset(explode('FROM',trim(&$query))),2))));
			foreach($columns as $column)
			{
				if(!preg_match('#^(.*?)(\s+)AS(\s+)(.*?)$#is',trim($column)))
					$query = str_replace($column,$column.' AS "'.str_replace(array('[',']'),'',trim(substr($column,1,-1))).'"',&$query);
			}
		}print($query);*/
		
		return($query);
	}
	
	public function escape($string)
	{
		return(sqlite_escape_string($string));
	}
	
	public function getLastInsertId()
	{
		return(sqlite_last_insert_rowid($this->handle));
	}
	
	
	public function createAggregate($queryFuction,$stepFunction,$finalizeFunction,$args)
	{
		if(!$args) sqlite_create_aggregate($this->handle,$queryFuction,$stepFunction,$finalizeFunction);
		else sqlite_create_aggregate($this->handle,$queryFuction,$stepFunction,$finalizeFunction,$args);
		return(true);
	}
}

class SQLResult_sqlite2
{
	private $resource;
	
	public function __construct($resource)
	{
		$this->resource = $resource;
	}
	
	public function fetch($count = false)
	{
		return($this->fetchAllAsObject($count));
	}
	public function fetchOne($count = false)
	{
		return($this->fetchOneAsObject($count));
	}
	
	public function fetchAllAsObject($count = false)
	{
		$result = array();
		$i = 0;
		while($item = sqlite_fetch_object($this->resource))
		{
			if($count && $i == $count) return(false);
			$result[] = $item;
		}
		
		$this->enhanceResult($result);
		
		return($result);
	}
	public function fetchOneAsObject($count = false)
	{
		if($item = sqlite_fetch_object($this->resource)) return($item);
		else return(new stdClass());
		
		$this->enhanceResult($result);
		
		return($result);
	}
	
	public function fetchAllAsArray($count = false)
	{
		$result = array();
		$i = 0;
		while($item = sqlite_fetch_assoc($this->resource))
		{
			if($count && $i == $count) return(false);
			$result[] = $item;
		}
		
		$this->enhanceResult($result);
		
		return($result);
	}
	public function fetchOneAsArray($count = false)
	{
		if($item = sqlite_fetch_assoc($this->resource)) return($item);
		else return(new stdClass());
		
		$this->enhanceResult($result);
		
		return($result);
	}
	
	
	private function enhanceResult(&$result,$type = 1)
	{
		# $type: 1 = object, 2 = assoc. array
		
		foreach($result as $i => $item)
		{
			foreach($item as $key => $value)
			{
				if($type == 2)
				{
					$result[$i][str_replace(array('[',']'),'',$key)] = $value;
				}
				elseif($type == 1)
				{
					$k = str_replace(array('[',']'),'',$key);
					$result[$i]->{$k} = $value;
				}
			}
		}
	}
	
	public function numRows()
	{
		return(sqlite_num_rows($this->resource));
	}
}
?>