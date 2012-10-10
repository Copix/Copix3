
<script type="text/javascript">
{literal}
window.addEvent('domready',function() {
  	var mySlides = new Array();
	  	
	$$('span.comment_content').each(function (el,i){
		
		el.getPrevious().addEvent('click',function(e) {
			e = new Event(e);
			mySlides[i].toggle();
			e.stop();
		});
		mySlides[i] = new Fx.Slide(el);
		mySlides[i].hide();
	});  
	
});
{/literal}
</script>

<table class="CopixTable">
	<thead>
	<tr>
		<th>{i18n key=comments.list.link}</th>
		<th>{i18n key=comments.list.date}</th>
		<th>{i18n key=comments.list.author}</th>
		<th>{i18n key=comments.list.content}</th>
		<th>{i18n key=comments.admin.action}</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$ppo->arrComments item=comment name=comments}
	<tr {cycle values=',class="alternate"' name="commentlist"}>
		<td>
			<a href="{$ppo->baseUrl}?{$comment->page_comment}" class="comment-id">{$comment->comment_id}</a> 
		</td>
		
		<td>
			{$comment->date_comment|datetimei18n:text} 
		</td>
		<td>
			<a href="mailto:{$comment->authoremail_comment}">{$comment->authorlogin_comment|escape}</a>	
			{if $comment->authorsite_comment}<a href="{$comment->authorsite_comment}">{i18n key=comments.list.site}</a>{/if}
			
		</td>
		<td>
			<a href=""><img src="{copixresource path=img/tools/page.png}" /></a> {$comment->content_comment|escape|truncate:30:'...':false:true}
			<span class="comment_content">
				{$comment->content_comment|escape}
			</div>
		</td>
			
		<td>
			<a href="{copixurl dest="comments|admin|editcomment" id=$comment->comment_id}"><img src="{copixresource path=img/tools/update.png}" /></a>
			<a href="{copixurl dest="comments|admin|deletecomment" id=$comment->comment_id}"><img src="{copixresource path=img/tools/delete.png}" /></a>
		</td>
	</tr>
	{/foreach}

	</tbody>
</table>
{if $ppo->nbPage != 1 }
<p align="center">
{linkbar url=$ppo->pagerUrl nbLink=5 pageNum=$ppo->pageNum nbTotalPage=$ppo->nbPage}
</p>	
{/if}
{* <dl class="comments">
	{foreach from=$ppo->arrComments item=comment name=comments}
		<dt><a href="{$ppo->baseUrl}?{$comment->page_comment}" class="comment-id">{$comment->comment_id}</a> {$comment->date_comment|datetimei18n:text}, {if $comment->authorsite_comment}<a href="{$comment->authorsite_comment}">{/if}{$comment->authorlogin_comment|escape}{if $comment->authorsite_comment}</a>{/if}
		<a href="{copixurl dest="comments|admin|editcomment" id=$comment->comment_id}"><img src="{copixresource path=img/tools/update.png}" /></a>
		<a href="{copixurl dest="comments|admin|deletecomment" id=$comment->comment_id}"><img src="{copixresource path=img/tools/delete.png}" /></a>				
		</dt>
		<dd><pre>{$comment->content_comment|escape}</pre></dd>
	{/foreach}
</dl>
<p align="center">
{linkbar url=$ppo->pagerUrl nbLink=5 pageNum=$ppo->pageNum nbTotalPage=$ppo->nbPage}
</p> *}
<br/>
<a href="{copixurl dest="admin||"}"> <input type="button" value="{i18n key="copix:common.buttons.back"}" /></a>