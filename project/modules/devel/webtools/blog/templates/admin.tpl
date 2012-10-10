<h2>{i18n key="blog.admin"}</h2>

<ul>
{foreach from=$ppo->headings item=head}
	<li><p>{$head->heading_blog}</p>
	    <p style=" font-style: italic">{$head->description_blog|nl2br}</p>
	</li>
{/foreach}
</ul>

{i18n key="blog.admin.add.blog"}
<form method="POST" action="{copixurl dest="blog|admin|addheading"}">
<p>
{i18n key="blog.admin.heading.title"}<br /><input type="text" name="heading_blog" />
</p>
<p>
{i18n key="blog.admin.heading.desc"}<br /><textarea rows="5" cols="50" name="description_blog"></textarea>
</p>
<p>
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />&nbsp;<a href="{copixurl dest="admin||"}"> <input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>
</p>
</form>
