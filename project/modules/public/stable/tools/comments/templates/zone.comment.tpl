{if $mode eq "list"}
	{if count ($arrComments) neq 0}
	    <dl class="comments">
		{foreach from=$arrComments item=comment name=comments}
			<dt><a href="#c{$comment->comment_id}" class="comment-id">{$smarty.foreach.comments.iteration}</a> {$comment->date_comment|datetimei18n:text}, {if $comment->authorsite_comment}<a href="{$comment->authorsite_comment}">{/if}{$comment->authorlogin_comment|escape}{if $comment->authorsite_comment}</a>{/if}
			{if $isAdmin}
				<a href="{copixurl dest="comments|admin|editcomment" id=$comment->comment_id url_return=$newUrl}"><img src="{copixresource path=img/tools/update.png}" /></a>
				<a href="{copixurl dest="comments|admin|deletecomment" id=$comment->comment_id url_return=$newUrl}"><img src="{copixresource path=img/tools/delete.png}" /></a>				
			{/if}
			
			</dt>
			<dd class="comment_content"><p>{$comment->content_comment|escape|nl2br}</p></dd>
		{/foreach}
		</dl>
	{else}<p>
		{i18n key="comments.list.nocomment"}
		</p>
	{/if}
	{if isset ($errors)}
	<div class="errorMessage">
 		<h1>{i18n key=copix:common.messages.error}</h1>
		{ulli values=$errors}
	</div>	
	{/if}
	{if $locked == 0}

	{* bloc affichant le formulaire d'ajout de commentaires *}
	<form action="{copixurl dest="comments||addComment" id=$idComment}"  method="post" class="comments-form">
	<fieldset>
	<legend>{i18n key="comments.list.addcomment"}</legend>
	{if isset ($preview)}
		{i18n key="comments.list.previewcomment"}
		<dl class="comments">
			<dt>{$previewDate|datetimei18n:text}, {if $newComment->authorsite_comment}<a href="{$newComment->authorsite_comment}">{/if}{$newComment->authorlogin_comment|escape}{if $newComment->authorsite_comment}</a>{/if}</dt>
			<dd><pre>{$newComment->content_comment|escape}</pre></dd>
		</dl>
	{/if}
	<table class="CopixVerticalTable">
  	<tr>
  	 <th>
	  <label for="author">{i18n key="comments.list.author"}</label>
	 </th>
	 <td>
	  <input type="text" id="author" name="author" value="{$newComment->authorlogin_comment|escape}"/>
	 </td>
	</tr>
	<tr>
	 <th>
	  <label for="mail">{i18n key="comments.list.mail"}</label>
	 </th>
	 <td>
	  <input type="text" id="mail" name="mail" value="{$newComment->authoremail_comment|escape}"/>
	 </td>
	</tr>
	<tr>
	 <th> 
	  <label for="site">{i18n key="comments.list.site"}</label>
	 </th>
	 <td>
	  <input type="text" id="site" name="site" value="{$newComment->authorsite_comment|escape}"/>
	 </td>
	</tr>
	<tr>
	 <th>
  	  <label for="content">{i18n key="comments.list.content"}</label>
  	 </th>
  	 <td>
  	  <textarea id="content" name="content">{$newComment->content_comment|escape}</textarea>
  	 </td>
  	</tr>

	{if isset($captcha)}
	 <tr>
	  <th>{i18n key="comments.captcha.libelle"} <input type="hidden" name="captcha_id" value="{$captcha->captcha_id}">{$captcha->captcha_question}</th>
	  <td><input type="text" name="captcha_answer"></td>
	 </tr>
	{/if}
	</table>
	<br/>
	<input type="submit" name="add" class="submit" value="{i18n key="comments.list.doadd"}">
	<input type="submit" name="preview" class="preview" value="{i18n key="comments.list.dopreview"}">
	</fieldset>
	</form>
	{if $isAdmin}
		{i18n key="comments.list.lockcomments"}<a href="{copixurl dest="comments|admin|lock" id=$idComment url_return=$newUrl lock_status=1}"><img src="{copixresource path=img/tools/open.png}" /></a>		
	{/if}
	{else}
	{i18n key="comments.list.locked"}	
	{if $isAdmin}
		{i18n key="comments.list.unlockcomments"}<a href="{copixurl dest="comments|admin|lock" id=$idComment url_return=$newUrl  lock_status=0}"><img src="{copixresource path=img/tools/close.png}" /></a>		
	{/if}
	{/if}
	{* fin du bloc *}
{else}
	{if $nbComments neq 0}
	<a href="{$newUrl}"><img src="{copixresource path="img/tools/comments.png"}" alt="" /> {i18n key="comments.summary.nbcomment" args="$nbComments"}</a>
	{else}
	<a href="{$newUrl}"><img src="{copixresource path="img/tools/comments.png"}" alt="" />{i18n key="comments.summary.nocomment"}</a>
	{/if}
{/if}