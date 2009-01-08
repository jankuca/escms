<?php
class Debug
{
	private static $items;
	public static $enabled = false;
	public static $level = E_ALL;
	
	private static $errorTpl = false;
	private static $errorTplItems = array();
	
	public static function add($params)
	{
		self::$items[] = $params;
	}
	
	public static function log($exception = false)
	{
		if(!self::$enabled || count(self::$items) == 0) return;
		
		$o_dirpath = TPL::$dirpath;
		TPL::$dirpath = ESCMS_PATH_TPL . '.debug/';
		if(!$exception && !TPL::addTpl('errors'))
		{
			$items = array();
			foreach(self::$items as $exc)
			{
				$file = end(explode(dirname($_SERVER['SCRIPT_FILENAME']),$exc->getFile()));
				$line = $exc->getLine();
				if(end(explode('/',$file)) == 'debug.layer.php')
				{
					$trace = current($exc->getTrace());
					$file = end(explode(dirname($_SERVER['SCRIPT_FILENAME']),$trace['file']));
					$line = $trace['line'];
				}
				
				$items[] = array(
					'message' => preg_replace('# \[<a href=\'(.*?)\'>(.*?)</a>\]#s','',$exc->getMessage()),
					'file' => $file,
					'line' => $exc->getLine()
				);
			}
			Debug::dump($items,false);
		}
		else
		{
			TPL::add('DEBUG_ERRORS_COUNT',count(self::$items));
			TPL::add('DEBUG_TIMEOUT',$GLOBALS['time'] - microtime(true));
			
			$items = new TPLLoop('DEBUG_ITEMS');
			foreach(self::$items as $exc)
			{
				$file = end(explode(dirname($_SERVER['SCRIPT_FILENAME']),str_replace('\\','/',$exc->getFile())));
				$line = $exc->getLine();
				
				$f = end(explode('/',$file));
				$trace = $exc->getTrace();//print_r($trace);continue;
				for($i = 0; i < count($trace); ++$i)
				{
					if(isset($trace[$i]['file']))
					{
						$e = end(explode(dirname($_SERVER['SCRIPT_FILENAME']),str_replace('\\','/',$trace[$i]['file']),2));
						$e = str_replace('\\','/',$e);
						$ee = explode('/',$e);
						
						if(!($ee[1] == 'app' && count($ee) > 3 && in_array($ee[2],array('layers','modules','lib'))))
						{
							$file = $e;
							$line = $trace[$i]['line'];
							break;
						}
					}
				}
				
				$msg = $exc->getMessage();
				if(preg_match('#(\w:+)\(\): (.*)#m',$msg,$arr)) $msg = '<tt>'.$arr[1].'()</tt>: '.$arr[2];
				
				$i = new TPLLoopItem();
				$i->add('DEBUG_ITEM_FILE',$file);
				$i->add('DEBUG_ITEM_LINE',$line);
				$i->add('DEBUG_ITEM_MESSAGE',$msg);
				
				$lines = array(); $count = 0;
				if($o = @fopen('.'.$file,'r'))
				{
					$content = fread($o,filesize('.'.$file));
					$e = explode("\n",$content);
					$count = count($e);
					array_unshift($e,'');
					//Debug::dump($e);
					
					switch($line):
						case(0): $range = array(); break;
						case(1): $range = range(1,4); break;
						case(2): $range = range(1,5); break;
						case(3): $range = range(1,6); break;
						default: $range = range($line-3,$line+3); break;
					endswitch;
					
					foreach($range as $id => $no)
					{
						$noText = $no;
						while(strlen($noText) != strlen($count)) $noText = '0'.$noText;
						if($no == $line) $lines[] = '</pre><pre class="errorline"> ' . $noText . '| ' . htmlspecialchars($e[$no]) . '</pre><pre>';
						else $lines[] = ' '.$noText . '| ' . htmlspecialchars($e[$no]);
					}
				}
				$i->add('DEBUG_ITEM_CODE',implode("\n",$lines));
				
				$items->append($i);
			}
			$items->pack();
			
			if($exception)
			{
				$constants = get_defined_constants(true);//Debug::dump($constants);
				ksort($constants['user']);
				$items = new TPLLoop('DEBUG_CONSTANTS');
				foreach($constants['user'] as $key => $value)
				{
					$item = new TPLLoopItem();
					$item->add('CONSTANT',$key);
					$item->add('CONSTANT_VALUE',$value);
					$items->append($item);
				}
				$items->pack();
			}
		}
		TPL::$dirpath = $o_dirpath;
	}
	
	public static function catchException($exc)
	{
		if(self::$enabled)
		{
			try
			{
				TPL::trash();
				TPL::$dirpath = ESCMS_PATH_TPL . '.debug/';
				if(!TPL::addTpl('exception'))
					throw $exc;
				else
				{
					TPL::add(array(
						'EXCEPTION_MESSAGE' => $exc->getMessage()
					));
					Debug::log(true);
					TPL::pack();
					die();
				}
			}
			catch(Exception $exc)
			{
				die('Exception caught: <strong>'.$exc->getMessage().'</strong>');
			}
		}
		else
		{
			Debug::header(503);
		}
		
		Debug::add($exc);
	}
	
	public static function header($code,$message = '')
	{
		switch($code)
		{
			case(400): header('HTTP/1.1 400 Bad Request'); break;
			case(404): header('HTTP/1.1 404 Not Found'); break;
			case(503):default: header('HTTP/1.1 503 Service Unavailable'); break;
		}
		
		try
		{
			TPL::trash();
			TPL::$dirpath = ESCMS_PATH_TPL . '.debug/';
			TPL::add(array(
				'EXCEPTION_MESSAGE' => $message
			));
			if(!TPL::addTpl('header' . $code))
			{
				switch($code)
				{
					case(400): die('<h1>HTTP/1.1 400 Bad Request</h1><p>' . $message . '</p>'); break;
					case(404): die('<h1>HTTP/1.1 404 Not Found</h1>'); break;
					case(503):default: throw new Exception($message);
				}
			}
			else
			{
				TPL::pack();
				die();
			}
		}
		catch(Exception $exc)
		{
			die('<h1>503 Service Unavailable</h1><p>'.$exc->getMessage().'</p>');
		}
	}
	public static function error($message)
	{
		Debug::add(new Exception($message));
	}
	
	public static function catchError($errno,$errstr,$errfile,$errline)
	{
		Debug::add(new ErrorException($errstr,0,$errno,$errfile,$errline));
	}
	
	public static function dump($variable,$types = true)
	{
		ob_start();
		if($types) var_dump($variable);
		else print_r($variable);
		$variableString = ob_get_contents();
		ob_end_clean();
		
		ob_start();
		highlight_string('<?php'."\n".preg_replace(
			array('#(\s+)=>(\s+)#m','#\]=>(\s+)#m','~#([0-9]+)~m','#( ){8}#m'),
			array(' => ','] => ','[id=$1]','    '),
			$variableString
		).'?>');
		$h = ob_get_contents();
		ob_end_clean();
		
		print('<div class="escms-debug-dump" style="text-align:left;">' . $h . '</div>'."\n\n");
	}
}
?>