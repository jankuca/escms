<?php
class Pagination
{
	private $page;
	private $query;
	
	public function __construct($params)
	{
		if(!isset($params['url'],$params['query']))
		{
			trigger_error('Pagination::__construct(): The SQL query is required.');
			return(false);
		}
		if(!preg_match('#^(\s+)SELECT(.*?)$#is',$params['query']))
		{
			trigger_error('Pagination::__construct(): The SQL query is not a valid SELECT query.');
			return(false);
		}
		
		if(!isset($params['perPage'])) $params['perPage'] = 10;
		if(!isset($params['GET'])) $params['GET'] = 'page';
		
		if(!isset($_GET[$params['GET']])) $this->page['active'] = 1;
		else $this->page['active'] = (int) $_GET[$params['GET']];
		
		$this->query = $params['query'];
		$this->url = $params['url'];
		$this->perPage = (int) $params['perPage'];
		
		if(isset($params['urlFirst'])) $this->urlFirst = $params['urlFirst'];
		else $this->urlFirst = $params['url'];
	}
	
	public function make()
	{
		$this->countItems();
		$this->setLimit();
		
		if(!$this->resource = SQL::query($this->query . " LIMIT " . $this->start . "," . $this->perPage)) return(false);
		return(true);
	}
	
	public function fetch()
	{
		if(!isset($this->returnType)) return($this->resource->fetch());
		else
		{
			switch($this->returnType)
			{
				case('array'): return($this->resource->fetchAllAsArray());
				case('object'):default: return($this->resource->fetchAllAsObject());
			}
		}
	}
	
	
	public function getBrowser($show_all = false)
	{
		$this->pages_count = ceil($this->count / $this->perPage);
		
		$pages = array();
		for($i = 1; $i <= $this->pages_count; ++$i)
		{
			if($show_all || $this->pages_count <= 10)
				$pages[] = $this->addPageToBrowser($i);
			else
			{
				if($i == 1)
				{
					if($this->page['active'] > 4) $pages[] = $this->addPageToBrowser($i,true);
					else $pages[] = $this->addPageToBrowser($i);
				}
				elseif($i == $this->pages_count) $pages[] = $this->addPageToBrowser($i);
				elseif($i >= $this->page['active'] - 2 && $i < $this->page['active'] + 2) $pages[] = $this->addPageToBrowser($i);
				elseif($i == $this->page['active'] + 2)
				{
					if($this->page['active'] < $this->pages_count - 3) $pages[] = $this->addPageToBrowser($i,true);
					else $pages[] = $this->addPageToBrowser($i);
				}
			}
		}
		
		return($pages);
	}
	
	public function addPageToBrowser($i,$hellip = false)
	{
		return(array(
			'PAGE_NUMBER' => $i,
			'PAGE_RANGE' => $this->range($i),
			'PAGE_LINK' => str_replace('%page',$i,($i == 1 ? $this->urlFirst : $this->url)),
			'PAGE_LINK_PREV' => str_replace('%page',$this->page['active'] - 1,($this->page['active'] - 1 == 1 ? $this->urlFirst : $this->url)),
			'PAGE_LINK_NEXT' => str_replace('%page',$this->page['active'] + 1,$this->url),
			
			'conds' => array(
				'ACTIVE' => ($i == $this->page['active'] ? true : false),
				'HELLIP' => (boolean) $hellip,
				'FIRST' => ($i == 1 ? true : false),
				'LAST' => ($i == $this->pages_count ? true : false)
			)
		));
	}
	
	private function countItems()
	{
		if($result = SQL::query($this->query)) $this->count = $result->numRows();
		else $this->count = 0;
	}
	
	private function setLimit()
	{
		$max = ($this->page['active'] * $this->perPage);
		$this->start = $max - $this->perPage;
		$this->end = ($max > $this->count ? $this->count : $max);
		
		$this->page['count'] = ceil($this->count / $this->perPage);
	}
	
	
	public function range($i)
	{
		$max = $i * $this->perPage;
		return(($max - $this->perPage + 1) . '-' . ($this->count < $max ? $this->count : $max));
	}
}
?>