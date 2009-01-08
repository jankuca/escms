<?php
class ESCMSModule_Sys_blog extends ESCMSModule_blog
{
	
}

$blog = new ESCMSModule_Sys_blog();
$blog->getCategories();

if(!isset($_GET['c']) || ($_GET['c'] == 'blog' && !isset($_GET['slug'],$_GET['section']) && isset($_GET['archive'])))
{
	if(!isset($_GET['archive']) || !isset($_GET['selectmode'])) TPL::addTpl('index');
	else TPL::addTpl('archive');
	
	$blog->getLatestPosts();
}
if(isset($_GET['c']) && $_GET['c'] == 'blog')
{
	if(isset($_GET['section']))
	{
		switch($_GET['section'])
		{
			case('category'):
				if(isset($_GET['slug']))
				{
					TPL::add('BLOG_CATEGORY_HEADER',$blog->getCategoryInfo($_GET['slug'])->category_header);
					TPL::addTpl('blog_category');
					$blog->getCategoryPosts($_GET['slug']);
				}
				break;
			
			case('post'):
				if(isset($_GET['slug']))
				{
					TPL::addTpl('blog_post');
					$blog->getPost($_GET['slug']);
				}
				break;	
		}
	}
}
?>