{if $ppo->mode=="html"}
{htmleditor name="content_blog" content=$ppo->content width="98%"}
<input type="hidden" name="typesource_blog" value="html" />
{else}
{wikieditor name="content_blog"}
{$ppo->content}
{/wikieditor}
<input type="hidden" name="typesource_blog" value="wiki" />
{/if}
