<h2>{i18n key="blog.new.ticket"}</h2>
<form method="POST" action="{copixurl dest="blog|admin|saveticket"}">
{if $ppo->ticket}
<input type="hidden" name="id" value="{$ppo->ticket->id_blog}" />
{/if}
<p>
{i18n key="blog.ticket.blog"} <select name="heading_blog">
	{foreach from=$ppo->headings item=head}
	<option value="{$head->heading_blog}" {if $ppo->ticket->heading_blog==$head->heading_blog}SELECTED{/if}>{$head->heading_blog}</option>
	{/foreach}
</select>
</p>
<p>
{i18n key="blog.ticket.title"} <input type="text" name="title_blog" value="{$ppo->ticket->title_blog}"/>
<br />
Tags <input type="text" name="tags_blog" value="{$ppo->ticket->tags_blog}"/>
</p>
<p>
{i18n key="blog.content.ticket"}
{wikieditor name="content_blog"}
{$ppo->ticket->content_blog}
{/wikieditor}
</p>
<p>
{i18n key="blog.ticket.author"} <strong>{$ppo->author}</strong>
</p>
<p>
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
</p>
</form>