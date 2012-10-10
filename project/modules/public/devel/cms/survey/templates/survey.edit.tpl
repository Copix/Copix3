{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.surveyEdit;
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

<form name="surveyEdit" action="{copixurl dest="survey|admin|valid"}" method="post" class="copixForm">
   <fieldset>   
	   <table>
	      <tr>
	        <th>{i18n key=dao.survey.fields.caption_head}</th>
	        <td colspan="2">{$toEdit->caption_head}</td>
	      </tr>
	      <tr>
	        <th><label for="title_svy">{i18n key=dao.survey.fields.title_svy}</label></th>
	        <td colspan="2"><input type="text" id="title_svy" name="title_svy" value="{$toEdit->title_svy}" /></td>
	      </tr>
	      <tr>
	        <th><label for="authuser_svy">{i18n key=dao.survey.fields.authuser_svy}</label></th>
	        <td colspan="2"><input type="checkbox" class="checkbox" value="1" id="authuser_svy" name="authuser_svy" {if $toEdit->authuser_svy eq 1}checked="checked"{/if} /></td>
	      </tr>
	
	      {if $toEdit->option_svy|@count > 0}
	       {foreach from=$toEdit->option_svy item=option key=index name=countOption}
	        <tr>
	          <th>{i18n key=survey.messages.option} {$smarty.foreach.countOption.iteration}</th>
	          <td><input type="text" name="option{$index}" value="{$option->title}" /></td>
	          <td><a href="#" onclick="javascript:doUrl('{copixurl dest="survey|admin|deleteOption" index=$index}')"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key=copix:common.buttons.delete}" /></a></td>
	        </tr>
	       {/foreach}
	      {else}
	       {assign var=index value=-1}
	      {/if}
	      <tr>
	        <th>{i18n key=survey.messages.option} {$smarty.foreach.countOption.iteration+1}</th>
	        <td><input type="text" name="newoption" value="" /></td>
	        <td><input type="button" class="noSize" value="{i18n key=survey.button.addOption}" onclick="javascript:doUrl('{copixurl dest="survey|admin|addOption"}')" /></td>
	      </tr>
	   </table>
   </fieldset>
   <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:window.location='{copixurl dest="survey|admin|cancelEdit"}'" />
</form>
