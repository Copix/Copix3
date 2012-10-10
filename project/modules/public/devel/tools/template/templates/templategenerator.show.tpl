{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
   var myForm = document.templateEdit;
   myForm.action = pUrl;
   myForm.submit ('{copixurl dest="template|admin|validForm"}');
   return false;
{literal}
}

function popUpGenerator(url){
	window.open(url,'', 'resizable=no, location=no, width=800, height=600, menubar=no, status=no, scrollbars=no, menubar=no');
}

function toggleSelect (){
	var inputForm;
	inputForm = document.getElementById ("modulequalifier_ctpl");
	if (inputForm)
       inputForm.disabled = ! inputForm.disabled;

    inputForm = document.getElementById ("caption_ctpl");
	if (inputForm)
       inputForm.disabled = ! inputForm.disabled;
       
}
//]]>
</script>
{/literal}

{if $showErrors}
<div class="errorMessage">
<h1>{i18n key="copix:common.messages.error"}</h1>
{ulli values=$errors}
</div>
{/if}

<form action="{copixurl dest="template|admin|valid" editId=$editId}" name="templateEdit" method="post" class="copixForm">
<div class="tplGenGlobal">
	{$CONTENT}
</div>
<br />
<p class="validButtons">
 <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
 <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.close()" />
</p>
</form>