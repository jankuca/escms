		<div id="content" class="intro">
			<h2>Poslední články</h2>
			
<loop(BLOG_POSTS)>
			<h3><a href="<var(POST_LINK)>"><strong><var(POST_HEADER)></strong></a></h3>
			<small><var(POST_DATE)>; kategorie: <a href="<var(CATEGORY_LINK)>"><var(CATEGORY_HEADER)></a></small>
			<var(POST_PROLOGUE)>
			<span class="share"><if(BLOG_POSTS.POST_SHARE_ALLOW)>Sdílet: <a href="#"><img src="./images/icons/facebook.png" alt="Facebook.com" /></a></if(BLOG_POSTS.POST_SHARE_ALLOW)></span>
			<span class="options"><if(BLOG_POSTS.POST_COMMENTS_ALLOW)><a class="comments" href="<var(POST_LINK)>#comments"><var(COMMENTS_COUNT)> komentářů</a><if(BLOG_POSTS.POST_CONTENT)>, <a href="<var(POST_LINK)>">Celý článek &rarr;</a></if(BLOG_POSTS.POST_CONTENT)></span>
</loop(BLOG_POSTS)>
			
			<div class="pagination"><loop(BLOG_BROWSER)><if(BLOG_BROWSER.FIRST)><if(!BLOG_BROWSER.ACTIVE)><a href="<var(PAGE_LINK_PREV)>">&larr; Předchozí</a> </if(!BLOG_BROWSER.ACTIVE)></if(BLOG_BROWSER.FIRST)><if(BLOG_BROWSER.ACTIVE)><strong><var(PAGE_RANGE)></strong><else(BLOG_BROWSER.ACTIVE)><a href="<var(PAGE_LINK)>"><var(PAGE_RANGE)></a></if(BLOG_BROWSER.ACTIVE)><if(BLOG_BROWSER.LAST)><if(!BLOG_BROWSER.ACTIVE)><a href="<var(PAGE_LINK_NEXT)>">Next &rarr;</a> </if(!BLOG_BROWSER.ACTIVE)></if(BLOG_BROWSER.LAST)><if(BLOG_BROWSER.HELLIP)>&hellip; </if(BLOG_BROWSER.HELLIP)></loop(BLOG_BROWSER)></div>
		</div>
