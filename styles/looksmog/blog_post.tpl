		<div id="content" class="post">
			<h2>{BLOG_POST_HEADER}</h2>
			<small>{BLOG_POST_DATE}; kategorie: <a href="{BLOG_CATEGORY_LINK}">{BLOG_CATEGORY_HEADER}</a></small>
			{BLOG_POST_PROLOGUE}
			<div style="border: 1px solid #DDD; width: 468px; margin: 16px auto; height: 60px; line-height: 60px; text-align: center; font-size: 0.95em;">468&times;60</div>
			{BLOG_POST_CONTENT}
			
			<span class="share"><if(BLOG_POST_SHARE_ALLOW)>Sdílet: <a href="#"><img src="./images/icons/facebook.png" alt="Facebook.com" /></a></if(BLOG_POST_SHARE_ALLOW)></span>
			
			<!--if(BLOG_POST_COMMENTS_ALLOW)-->
			<h2>Komentáře <small>({BLOG_POST_COMMENTS_COUNT})</small></h2>
			
			<loop(BLOG_POST_COMMENTS)>
			</loop(BLOG_POST_COMMENTS)>
			
			<h3>Přidat komentář</h3>
			<form method="post" action="./action.php?c=blog&amp;section=comment&amp;mode=add">
			<div id="add-comment">
				<div id="comment-author">
					<ul class="tabs">
						<li><a href="#tab-openid"><img src="./styles/looksmog/media/images/i-openid.png" alt="OpenID" style="position: relative; top: 2px;" /></a></li>
						<li><a href="#tab-normal"><img src="./styles/looksmog/media/images/i-no-openid.png" alt="Bez OpenID" style="position: relative; top: 2px;" /></a></li>
					</ul>
					<div id="tab-openid">
						<label for="comment-author-openid">OpenID:</label>
						<input type="text" name="comment[author][openid]" id="comment-author-openid" /> <small>(povinné; <a href="http://www.openvatar.com/">Openvatar</a>)</small><br />
						<small class="sub"><a href="http://openid.net/get/">Získat OpenID</a>; <a href="http://mozek.cz/info/openid">Informace o OpenID</a></small>
					</div>
					<div id="tab-normal">
						<label for="comment-author-name">Jméno:</label>
						<input type="text" name="comment[author][name]" id="comment-author-name" /> <small>(povinné)</small><br />
						<label for="comment-author-email">E-mail:</label>
						<input type="text" name="comment[author][email]" id="comment-author-email" /> <small>(povinné; nebude zveřejněno; <a href="http://www.gravatar.com/">Gravatar</a>)</small>
					</div>
				</div>
				<div class="tabs-container">
					<label for="comment-author-website">Web:</label>
					<input type="text" name="comment[author][website]" id="comment-author-website" value="http://" /><br />
				
					<textarea name="comment[content]" cols="64" rows="5"></textarea>
					
					<input type="hidden" name="comment[post_id]" value="{BLOG_POST_ID}" />
					<input type="hidden" name="comment[post_slug]" value="{BLOG_POST_SLUG}" />
					<input type="submit" value="Přidat komentář" />
				</div>
			</div>
			</form>
			<script type="text/javascript">$('#comment-author').tabs(); //{ fxFade: true, fxSpeed: 200 });</script>
			<!--if(BLOG_POSTS.POST_CONTENT)-->
		</div>
