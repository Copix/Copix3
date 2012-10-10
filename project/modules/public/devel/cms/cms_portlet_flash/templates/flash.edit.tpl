{*
* param objDocs .
* param: possibleKinds - tableau des templates possibles.
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
   var myForm = document.docsEdit;
   myForm.action = pUrl;
   myForm.submit ();
{literal}
}
//]]>
</script>
{/literal}

<ul class="copixCMSNav">
 <li {if $kind=="general"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_flash||edit" kind="0"}')">{i18n key="document.tab.general"}</a></li>
 <li {if $kind=="preview"}class="copixCMSNavSelected"{/if}><a href="javascript:doUrl ('{copixurl dest="cms_portlet_flash||edit" kind="1"}')">{i18n key="document.tab.preview"}</a></li>
</ul>

<form name="docsEdit" action="{copixurl dest="cms_portlet_flash||valid"}" method="post">
{if $kind == "general"}
<fieldset>
<table>
  <tr>
   <th>Id</th>
       <td>{copixtag type='select' name='id_flash' values=$arFlash objectMap="id_flash;name_flash" selected=$flash->id_flash|escape}</td>
<!--   <td><input type="text" size="48" name="id_flash" value="{$flash->id_flash|escape}" /></td> -->
  </tr>
</table>
</fieldset>
{else}
   {$preview}
{/if}
<p class="validButtons">
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
</p>
</form>