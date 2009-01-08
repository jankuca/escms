		<div id="side">
			<ul>
				<li class="feed"><a href="#rss-feed">RSS</a></li>
			</ul>
			<h2>Kategorie</h2>
			<ul>
				<loop(BLOG_CATEGORIES)><li><a href="<var(CATEGORY_LINK)>"><var(CATEGORY_HEADER)></a> <small>(<var(POSTS_COUNT)>)</small></li>
				</loop(BLOG_CATEGORIES)><!--
				<li><a href="#category-software">Software</a></li>
				<li><a href="#category-works">Works</a></li>
				<li><a href="#category-ostatni">Ostatní</a></li-->
			</ul>
			<h2>Tagy</h2>
			<p class="tags"><a href="#tag-xhtml-css"><em>XHTML/CSS</em></a>&nbsp; <a href="#tag-javascript"><strong>JavaScript</strong></a>&nbsp; <a href="#tag-php"><em><strong>PHP</strong></em></a>&nbsp; <a href="#tag-reference"><strong>Reference</strong></a>&nbsp; <a href="#tag-fotografie">fotografie</a>&nbsp; <a href="#tag-trendy"><strong>Trendy</strong></a></p>
		</div>
