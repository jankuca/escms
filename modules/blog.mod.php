<?php
class ESCMSModule_blog implements ESCMSModule
{
	public function getModuleInfo()
	{
		$info = new stdClass();
		$info->codename = 'blog';
		$info->version = '0.1';
		$info->author = 'Look Smog';
		
		return($info);
	}
	
	public function getCategories()
	{
		if($result = SQL::query("
SELECT
	[blog_categories]p.[category_id] AS [category_id],
	[blog_categories]p.[category_header] AS [category_header],
	[blog_categories]p.[category_slug] AS [category_slug],
	count([blog_posts]p.[post_id]) AS [posts_count]
FROM [blog_categories]p
LEFT JOIN [blog_posts]p
	ON ([blog_categories]p.[category_id] == [blog_posts]p.[category_id] AND [blog_posts]p.[post_published] == 1)
GROUP BY [blog_posts]p.[category_id]
ORDER BY [blog_categories]p.[category_header] ASC"))
		{
			$loop = new TPLLoop(TPL_LOOP_BLOG_CATEGORIES);
			foreach($result->fetch() as $category)
			{
				$item = new TPLLoopItem();
				$item->add('CATEGORY_LINK',str_replace(
					array('%slug','%id'),
					array($category->category_slug,$category->category_id),
					CFG_URL_BLOG_CATEGORY
				));
				
				$item->add('CATEGORY_ID',$category->category_id);
				$item->add('CATEGORY_SLUG',$category->category_slug);
				$item->add('CATEGORY_HEADER',$category->category_header);
				$item->add('POSTS_COUNT',$category->posts_count);
				$loop->append($item);
			}
			$loop->pack();
		}
		else Debug::error('The blog categories could not be loaded.');
	}
	
	public function getCategoryInfo($slug)
	{
		if($result = SQL::query("
SELECT
	[blog_categories]p.[category_id] AS [category_id],
	[blog_categories]p.[category_header] AS [category_header],
	[blog_categories]p.[category_slug] AS [category_slug],
	count([blog_posts]p.[post_id]) AS [posts_count]
FROM [blog_categories]p
LEFT JOIN [blog_posts]p
	ON ([blog_categories]p.[category_id] == [blog_posts]p.[category_id] AND [blog_posts]p.[post_published] == 1)
WHERE ([blog_categories]p.[category_slug] == '" . SQL::escape($slug) . "')
GROUP BY [blog_posts]p.[category_id]
ORDER BY [blog_categories]p.[category_header] ASC")) { if($result->numRows() > 0) return($result->fetchOne()); }
		
		Debug::header(404);
	}
	
	public function getCategoryPosts($slug)
	{
	//	SQL::exec("DROP TABLE [blog_posts_contents]p");
	//	SQL::exec("CREATE TABLE [blog_comments]p ( [comment_id] INTEGER NOT NULL, [post_id] INTEGER NOT NULL, [comment_number] INTEGER(8) NOT NULL, [comment_date] INTEGER(16) NOT NULL, [comment_content] TEXT NOT NULL, [comment_author_name] VARCHAR(32) NOT NULL, [comment_author_email] VARCHAR(48) NOT NULL, [comment_author_website] NULL, [comment_author_user_agend] NULL, [comment_author_os] NULL )");
	//	SQL::exec("INSERT INTO [blog_posts_contents]p VALUES ( 2, '<p>Aliquam viverra, nisl et cursus tincidunt, leo ligula euismod magna, aliquam molestie leo sem sagittis leo. In sem. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aenean auctor, pede sed luctus ultricies, leo felis convallis pede, vel viverra odio odio at neque. Nunc tempus. Mauris ut eros sit amet neque dignissim auctor. Cras sodales eros vitae nunc. Vestibulum lacus. Fusce scelerisque leo nec tellus. Quisque neque libero, tincidunt vitae, tristique sed, commodo vitae, purus. Vestibulum eu odio sit amet tellus aliquam ultricies. Aenean et lorem in magna pharetra faucibus. Praesent vel tellus a leo imperdiet elementum. Nulla eu mi ac nibh ullamcorper adipiscing. Pellentesque egestas.</p>','')");
		
		Lib::load('pagination');
		
		$p = new Pagination(array(
			'query' => "
SELECT
	[blog_posts]p.[post_id] AS [post_id],
	[blog_posts]p.[post_slug] AS [post_slug],
	[blog_posts]p.[post_date] AS [post_date],
	[blog_posts]p.[post_header] AS [post_header],
	[blog_posts]p.[category_id] AS [category_id],
	[blog_categories]p.[category_slug] AS [category_slug],
	[blog_categories]p.[category_header] AS [category_header],
	[blog_posts_contents]p.[post_prologue] AS [post_prologue],
	count([blog_comments]p.[comment_id]) AS [comments_count]
FROM [blog_posts]p
LEFT JOIN [blog_categories]p
	ON [blog_posts]p.[category_id] == [blog_categories]p.[category_id]
LEFT JOIN [blog_posts_contents]p
	ON [blog_posts]p.[post_id] == [blog_posts_contents]p.[post_id]
LEFT JOIN [blog_comments]p
	ON [blog_posts]p.[post_id] == [blog_comments]p.[post_id]
WHERE ([blog_categories]p.[category_slug] == '" . SQL::escape($slug) . "')
GROUP BY [blog_posts]p.[post_id]
ORDER BY [blog_posts]p.[post_date]",
			'url' => './' . REQUEST . '%page/'
		));
		
		if(!$p->make()) throw new Exception('The category posts could not be selected.');
		else
		{
			$this->fetchPosts($p);
		}
	}
	
	public function getLatestPosts()
	{
		Lib::load('pagination');
		
		$query = "
SELECT
	[blog_posts]p.[post_id] AS [post_id],
	[blog_posts]p.[post_slug] AS [post_slug],
	[blog_posts]p.[post_date] AS [post_date],
	[blog_posts]p.[post_header] AS [post_header],
	[blog_posts]p.[category_id] AS [category_id],
	[blog_categories]p.[category_slug] AS [category_slug],
	[blog_categories]p.[category_header] AS [category_header],
	[blog_posts_contents]p.[post_prologue] AS [post_prologue],
	count([blog_comments]p.[comment_id]) AS [comments_count]
FROM [blog_posts]p
LEFT JOIN [blog_categories]p
	ON [blog_posts]p.[category_id] == [blog_categories]p.[category_id]
LEFT JOIN [blog_posts_contents]p
	ON [blog_posts]p.[post_id] == [blog_posts_contents]p.[post_id]
LEFT JOIN [blog_comments]p
	ON [blog_posts]p.[post_id] == [blog_comments]p.[post_id]
GROUP BY [blog_posts]p.[post_id]
ORDER BY [blog_posts]p.[post_date]";
		
		
		$p = new Pagination(array(
			'query' => $query,
			'url' => './archive/%page/',
			'perPage' => 1,
			'urlFirst' => './'
		));
		
		if(!$p->make()) throw new Exception('The latest posts could not be selected.');
		else
		{
			$this->fetchPosts($p,false);
		}
	}
	
	private function fetchPosts($paginationObject,$category_slug = false,$browser = true)
	{
		$posts = new TPLLoop(TPL_LOOP_BLOG_POSTS);
		foreach($paginationObject->fetch() as $post)
		{
			$i = new TPLLoopItem();
			
			$i->add('POST_LINK',str_replace(
				array('%slug','%id'),
				array($post->post_slug,$post->post_id),
				CFG_URL_BLOG_POST
			));
			$i->add('CATEGORY_LINK',str_replace(
				array('%slug','%id'),
				array($post->category_slug,$post->category_id),
				CFG_URL_BLOG_CATEGORY
			));
			
			$i->add('POST_ID',$post->post_id);
			$i->add('POST_SLUG',$post->post_slug);
			$i->add('POST_HEADER',$post->post_header);
			$i->add('POST_DATE',date(ESCMS_DATE_FORMAT,(int) $post->post_date));
			$i->add('POST_PROLOGUE',$post->post_prologue);
			$i->add('CATEGORY_ID',$post->category_id);
			$i->add('CATEGORY_SLUG',$post->category_slug);
			$i->add('CATEGORY_HEADER',$post->category_header);
			$i->add('COMMENTS_COUNT',$post->comments_count);
			$posts->append($i);
		}
		$posts->pack();
		
		if($browser) TPL::assignAsLoop(TPL_LOOP_BLOG_BROWSER,$paginationObject->getBrowser());
	}
	
	
	public function getPost($slug)
	{
		if(!$result = SQL::query("
SELECT
	[blog_posts]p.[post_id] AS [post_id],
	[blog_posts]p.[post_date] AS [post_date],
	[blog_posts]p.[post_header] AS [post_header],
	[blog_posts]p.[category_id] AS [category_id],
	[blog_categories]p.[category_slug] AS [category_slug],
	[blog_categories]p.[category_header] AS [category_header],
	[blog_posts_contents]p.[post_prologue] AS [post_prologue],
	[blog_posts_contents]p.[post_content] AS [post_content],
	count([blog_comments]p.[comment_id]) AS [comments_count]
FROM [blog_posts]p
LEFT JOIN [blog_categories]p
	ON [blog_posts]p.[category_id] == [blog_categories]p.[category_id]
LEFT JOIN [blog_posts_contents]p
	ON [blog_posts]p.[post_id] == [blog_posts_contents]p.[post_id]
LEFT JOIN [blog_comments]p
	ON [blog_posts]p.[post_id] == [blog_comments]p.[post_id]
WHERE ([blog_posts]p.[post_slug] == '" . SQL::escape($slug) . "')
GROUP BY [blog_posts]p.[post_id]
ORDER BY [blog_posts]p.[post_date]"))
			throw new Exception('The post could not be selected.');
		else
		{
			if($result->numRows() == 0) Debug::header(404);
			
			$post = $result->fetchOne();
			
			TPL::add('BLOG_CATEGORY_LINK',str_replace(
				array('%slug','%id'),
				array($post->category_slug,$post->category_id),
				CFG_URL_BLOG_CATEGORY
			));
			
			TPL::add('BLOG_POST_ID',$post->post_id);
			TPL::add('BLOG_POST_HEADER',$post->post_header);
			TPL::add('BLOG_POST_SLUG',$slug);
			TPL::add('BLOG_POST_DATE',date(ESCMS_DATE_FORMAT,(int) $post->post_date));
			TPL::add('BLOG_POST_PROLOGUE',$post->post_prologue);
			TPL::add('BLOG_POST_CONTENT',$post->post_content);
			TPL::add('BLOG_CATEGORY_HEADER',$post->category_header);
			TPL::add('BLOG_CATEGORY_SLUG',$post->category_slug);
			TPL::add('BLOG_POST_COMMENTS_COUNT',$post->comments_count);
		}
	}
}

define('TPL_LOOP_BLOG_CATEGORIES','BLOG_CATEGORIES');
define('TPL_LOOP_BLOG_POSTS','BLOG_POSTS');
define('TPL_LOOP_BLOG_BROWSER','BLOG_BROWSER');
?>