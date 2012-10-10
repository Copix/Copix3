{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.documentEdit;
   myForm.action = pUrl;
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

<form action="{copixurl dest="document|admin|valid"}" name="documentEdit" method="post" enctype="multipart/form-data" class="copixForm">
   <fieldset>
   <table>
      <tr>
        <th>{i18n key=dao.document.fields.caption_head}</th>
        <td>{$toEdit->caption_head|escape:html}</td>
      </tr>
      <tr>
        <th><label for="title_doc">{i18n key=dao.document.fields.title_doc}</label></th>
        <td><input type="text" id="title_doc" name="title_doc" size="48" value="{$toEdit->title_doc|escape:html}" /></td>
      </tr>
      <tr>
        <th><label for="desc_doc">{i18n key=dao.document.fields.desc_doc}</label></th>
        <td><textarea id="desc_doc" name="desc_doc" cols="40" rows="5">{$toEdit->desc_doc|escape:html}</textarea></td>
      </tr>
      <tr>
       <th><label for="docFile">{i18n key=document.messages.file}</label>({i18n key='document.fields.maxWeight_up'} {$max_upload_size})</th>
       <td><input type='file' size="35" id="docFile" name='docFile'></td>
      </tr>
   </table>
   </fieldset>
   <p class="validButtons">
   <input type="submit" value="{i18n key="copix:common.buttons.save"}" />
   <input type="button" value="{$WFLBestActionCaption}" onclick="return doUrl('{copixurl dest="document|admin|valid" doBest=1}')" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="document|admin|cancelEdit"}'" />
   </p>
</form>

<script type="text/javascript">
{literal}
var inputForm = document.getElementById ('title_doc');
if (inputForm){
	inputForm.focus ();
}
{/literal}
</script>