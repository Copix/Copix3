<div id="blog_admin_editticket">
<h2>{i18n key="blog.new.ticket"}</h2>
<form method="POST" action="{copixurl dest="blog|admin|saveticket"}">
{if $ppo->ticket}
<input type="hidden" id="blog_ticket_id" name="id" value="{$ppo->ticket->id_blog}" />
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
</p>
{** Future évoluition 
<br />
<table>
	<tr>
	<td>{i18n key="blog.publication.date"} : </td>
	<td>{calendar name="futurdate" value="" caption="test"}</td>
	</tr>
</table> **}


<div>
<input type="button" id="wikimode" value="wiki"/>  <input type="button" id="htmlmode" value="html"/> 
</div>

<div id="blog_edition_pane">
{if $ppo->ticket->typesource_blog=="html"}
{htmleditor name="content_blog" content=$ppo->ticket->content_blog width="98%"}
<input type="hidden" name="typesource_blog" value="html" />
{else}
{wikieditor name="content_blog"}
{$ppo->ticket->content_blog}
{/wikieditor}
<input type="hidden" name="typesource_blog" value="wiki" />
{/if}
</div>
<p>
{i18n key="blog.ticket.author"} <strong>{$ppo->author}</strong>
</p>
<p>
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
</p>
</form>
</div>
{copixhtmlheader kind="jsdomreadycode"}
  {literal}  
   function  callChangeEditor (ev){
        var url=Copix.getActionURL('blog|ajax|geteditor');        
        var content = '';
        $('blog_edition_pane').getElementsBySelector('input, textarea').each(function(inputs){
            if(inputs.name=="content_blog"){
                content = inputs.value;
            }
        });        
        if($('blog_ticket_id') && $('blog_ticket_id').value)
        	var id = $('blog_ticket_id').value;
        else
        	id = 0;
        ev = new Event(ev);
        var mode = ev.target.value;
        
        $('blog_edition_pane').empty();
        new Ajax(url, {
            data: "id="+id+"&mode="+mode+"&content="+content,
            update: "blog_edition_pane"
        }).request();
    }  
    $('wikimode').addEvent('click',callChangeEditor);    
    $('htmlmode').addEvent('click',callChangeEditor);
    {/literal}
{/copixhtmlheader}

