<?php
if(defined('REQUEST'))
{
//SQL::exec("UPDATE [blog_categories]p SET [category_header] = 'Programování, kódování' WHERE ([category_id] == 1)");
	
//	SQL::exec("INSERT INTO [requests]p ([request_regexp],[request_redirect],[request_url],[request_target],[request_order]) VALUES (1,0,'/clanky/([\w-]+)/','./index.php?c=blog&section=post&slug=$1',0)");
	if($result = SQL::query("SELECT [request_id],[request_regexp],[request_redirect],[request_url],[request_target] FROM [requests]p ORDER BY [request_order] ASC"))
	{
		foreach($result->fetch() as $request)
		{//echo($request->request_url . '=>'.$request->request_target . '; ');
			if((boolean) $request->request_regexp === false)
			{
				if(REQUEST == $request->request_url)
				{
					if((boolean) $request->request_redirect === true) exit(header('Location: ' . $request->request_target));
					else { define('LOC',$request->request_target); break; }
				}
			}
			else
			{
				if(preg_match('#^'.$request->request_url.'$#s',REQUEST))
				{
					if((boolean) $request->request_redirect === true) exit(header('Location: ' . preg_replace('#^'.$request->request_url.'$#s',$request->request_target,REQUEST)));
					else { define('LOC',preg_replace('#^'.$request->request_url.'$#s',$request->request_target,REQUEST)); break; }
				}
			}
		}
	}
	else
	{
		throw new Exception('The request could not be resolved!');
	}
	unset($sql);
	
	if(!defined('LOC'))
	{
		header("HTTP/1.1 404 Not Found");
		Debug::header(404);
	}
	else
	{
		define('REDIRECTED',true);
		
		$url = parse_url(LOC);
		if(isset($url['query']))
		{
			$e = explode('&',$url['query']);
			foreach($e as $param)
			{
				$param = explode('=',$param,2);
				if(!isset($param[1])) $param[1] = '';
				
				$_GET[$param[0]] = $param[1];
			}
		}
		
		if(!include($url['path']))
		{
			header("HTTP/1.1 404 Not Found");
			die('<h1>HTTP/1.1 404 Not Found</h1>');
		}
	}
}
?>