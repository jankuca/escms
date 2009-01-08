<?php
class ESCMSModule_Action_blog extends ESCMSModule_blog
{
	public function handleCommentAdd()
	{
		if(!isset($_POST['comment'])) Debug::header(400,'Some of the required data has not been sent.');
		
		if(isset($_POST['comment']['author']['openid']) && !empty($_POST['comment']['author']['openid']))
		{
			setcookie('escms_comment_handle',serialize($_POST['comment']));
			
			Lib::load('openid');
			
			$openid = new OpenID_Client;
			$openid->returnTo = SITE_ROOT_PATH . 'action.php?c=blog&section=comment&mode=add&openid';
			$openid->trustRoot = SITE_ROOT_PATH;
			$openid->setIdentity(trim($_POST['comment']['author']['openid']));
			$openid->addFields(array('nickname','email'));
			$openid->make();
		}
		else $this->addComment($_POST['comment']);
	}
	
	public function validateCommentAdd()
	{
		Lib::load('openid');
			
		$openid = new OpenID_Client;
		$openid->returnTo = SITE_ROOT_PATH . 'action.php?c=blog&section=comment&mode=add&openid';
		$openid->trustRoot = SITE_ROOT_PATH;
		$openid->addFields(array('nickname','email'));
		
		if(!$fields = $openid->validate()) Debug::header(403,'Invalid OpenID authentication.');
		
		if(!isset($_COOKIE['escms_comment_handle'])) Debug::header(400,'The comment cookie is not available.');
		$this->addComment(unserialize(stripslashes($_COOKIE['escms_comment_handle'])),$fields);
	}
	
	private function addComment($comment,$OpenIDFields = false)
	{
		if(!isset($comment['author']['openid']) || $comment['author']['openid'] == '')
		{
			$author = $comment['author'];
			$author['openid'] = '';
		}
		else
		{
			$author = array(
				'name' => $OpenIDFields['nickname'],
				'email' => $OpenIDFields['email'],
				'website' => $comment['author']['website']
			);
			
			$e = explode('http://',$comment['author']['openid']);
			$author['openid'] = end($e);
			if(preg_match('#(.*?)\/$#s',$author['openid'])) $author['openid'] = substr($author['openid'],0,-1);
		}
		
		if($author['website'] == 'http://' || $author['website'] == '')
		{
			if(trim($comment['author']['openid']) == '') $author['website'] = '';
			else $author['website'] = trim($comment['author']['openid']);
		}
		
		if($result = !SQL::query("SELECT MAX([comment_number]) AS [max_number] FROM [blog_comments]p WHERE ([post_id] == " . ((int) $comment['post_id']) . ")"))
		{
			$number = $result->fetchOne();
			$number = (int) $number->max_number;
		}
		else $number = 0;
		
		if(!SQL::exec("
INSERT INTO [blog_comments]p
(
[post_id],
[comment_number],
[comment_date],
[comment_content],
[comment_author_openid],
[comment_author_name],
[comment_author_email],
[comment_author_website],
[comment_author_user_agent],
[comment_author_os]
) VALUES (
" . ((int) $comment['post_id']) . ",
" . $number . ",
" . time() . ",
'" . SQL::escape(trim($comment['content'])) . "',
'" . SQL::escape(trim($author['openid'])) . "',
'" . SQL::escape(trim($author['name'])) . "',
'" . SQL::escape(strtolower(trim($author['email']))) . "',
'" . SQL::escape(trim($author['website'])) . "',
'" . SQL::escape($_SERVER['HTTP_USER_AGENT']) . "',
'" . /*$author['os']*/'' . "'
)"))
			throw new Exception('The comment could not be added.');
		
		$uri = SITE_ROOT_PATH . str_replace(array('%id','%slug'),array($comment['post_id'],$comment['post_slug']),CFG_URL_BLOG_POST) . '#comments';
		header('Location: ' . $uri);
		echo('<a href="' . $uri . '">' . $uri . '</a>');
	}
}

if(isset($_GET['c']) && $_GET['c'] == 'blog')
{
	$blog = new ESCMSModule_Action_blog;
	
	if(isset($_GET['section']))
	{
		switch($_GET['section'])
		{
			case('comment'):
				if(isset($_GET['mode']))
				{
					switch($_GET['mode'])
					{
						case('add'):
							if(!isset($_GET['openid'])) $blog->handleCommentAdd();
							else $blog->validateCommentAdd();
							break;
					}
				}
				break;
		}
	}
}
?>