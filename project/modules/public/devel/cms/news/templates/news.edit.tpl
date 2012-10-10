{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.newsEdit;
   myForm.action = pUrl;
   if (typeof myForm.onsubmit == "function")// Form is submited only if a submit event handler is set.
      myForm.onsubmit();
   myForm.submit ();
}
//]]>
</script>
{/literal}

{if $showErrors}
<div class="errorMessage">
 <h1>{i18n key=copix:common.messages.error}</h1>
 {ulli values=$errors}
</div>
{/if}

<form action="{copixurl dest="news|admin|valid"}" method="post" name="newsEdit" class="copixForm">
   <fieldset>
   <table>
      {if $toEdit->author_news}
      <tr>
       <th>{i18n key=dao.news.fields.author_news}</th>
       <td>{$toEdit->author_news}</td>
      </tr>
      {/if}
      <tr>
       <th>{i18n key=dao.copixheading.fields.caption_head}</th>
       <td>{$toEdit->caption_head}</td>
      </tr>

      <tr>
        <th><label for="title_news">{i18n key=dao.news.fields.title_news}</label></th>
        <td><input type="text" size="48" id="title_news" name="title_news" value="{$toEdit->title_news}" /></td>
      </tr>
      <tr>
        <th><label for="datewished_news">{i18n key=dao.news.fields.datewished_news}</label></th>
        <td>{calendar value=$toEdit->datewished_news|datei18n" name=datewished_news}</td>
      </tr>
       <tr>
   		<!--ajout image accroche-->
	  <th>{i18n key="cms_portlet_picture|cms_portlet_picture.messages.picture"}</th>
	  <td>
	      {if $toEdit->url_pict}
	      <img src="{$toEdit->url_pict}"/>
	      {else}{if $toEdit->id_pict}
	      <img src="{copixurl dest="pictures||get" id_pict=$toEdit->id_pict}" />
	        <a href="#" onclick="javascript:doUrl('{copixurl dest="news|admin|deletePictureNews" id_news=$toEdit->id_news}');"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a>
		{else}{i18n key="copix:common.none"}{/if}{/if}
	      <a href="#" onclick="javascript:doUrl('{copixurl dest="news|admin|selectPicture"}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="cms_portlet_picture|cms_portlet_picture.messages.select"}" /></a></td>
	  </tr>


  </table>
</fieldset>
<br />
<fieldset>
   <table>
      <tr>
       <th><label for="summary_news">{i18n key=dao.news.fields.summary_news}</label></th>
      </tr>
      <tr>
        <td>
         <textarea id="summary_news" name="summary_news" cols="40" rows="8">{$toEdit->summary_news}</textarea>
        </td>
      </tr>
      <tr>
       <th><label for="content_news">{i18n key=dao.news.fields.content_news}</label></th>
      </tr>
      <tr>
       <td>{if $toEdit->editionkind_news == 'HTMLAREA'}
            {htmleditor content=$toEdit->content_news|stripslashes name=content_news}
            {else}
            <textarea id="content_news" name="content_news" cols="40" rows="8">{$toEdit->content_news}</textarea>
            {/if}
       </td>
      </tr>
   </table>
</fieldset>
   <p class="validButtons">
   <input type="submit" value="{i18n key="copix:common.buttons.save"}" />
   <input type="button" value="{$WFLBestActionCaption}" onclick="return doUrl('{copixurl dest="news|admin|valid" doBest=1}')" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="news|admin|cancelEdit"}'" />
   </p>
</form>
{formfocus id='title_news'}