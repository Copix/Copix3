<form method="POST" action="{copixurl dest="trackback||send"}">
<h2>{$ppo->title}</h2>
{i18n key="trackback.url"}: {$ppo->url}
<input type="hidden" name="title" value="{$ppo->title}"/>
<input type="hidden" name="url" value="{$ppo->url}"/>
<br />
{i18n key="trackback.blogname"}: <input type="text" name="blogname" value="Metal3d" />
<br />
{i18n key="trackback.server"}: <input type="text" name="target" value="" />
<br />
{i18n key="trackback.excerpt"}:<br />
<textarea name="excerpt" cols="50" rows="20">
{$ppo->excerpt}
</textarea>
<br />
<input type="submit" value="{i18n key="copix:common.buttons.send"}" />
</form>