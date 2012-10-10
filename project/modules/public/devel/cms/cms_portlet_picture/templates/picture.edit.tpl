{*
* param objDocs .
*}

{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
{/literal}
   var myForm = document.browserEdit;
   myForm.action = pUrl;
   myForm.submit ();
{literal}
}
//]]>
</script>
{/literal}

<form name="browserEdit" action="{copixurl dest="cms_portlet_picture||valid"}" method="post" class="copixForm">
<fieldset>
 <table class="verticalTable">
  <tr>
  <th>{i18n key="cms_portlet_picture|cms_portlet_picture.messages.picture"}</th>
  <td>
      {if $toEdit->url_pict}
      <img src="{$toEdit->url_pict}"/>
      {else}{if $toEdit->id_pict}
      <img src="{copixurl dest="pictures||get" id_pict=$toEdit->id_pict}" />
      {else}{i18n key="copix:common.none"}{/if}{/if}
      <a href="#" onclick="javascript:doUrl('{copixurl dest="cms_portlet_picture||selectPicture"}');"><img src="{copixresource path="img/tools/selectin.png"}" alt="{i18n key="cms_portlet_picture|cms_portlet_picture.messages.select"}" /></a></td>
  </tr>
  <tr>
  <th>{i18n key="cms_portlet_picture|cms_portlet_picture.messages.width"}</th>
  <td><input type="text" size="48" value="{$toEdit->width}" name="width" /></td>
  </tr>
  <tr>
  <th>{i18n key="cms_portlet_picture|cms_portlet_picture.messages.height"}</th>
  <td><input type="text" size="48" value="{$toEdit->height}" name="height" /></td>
  </tr>
  <tr>
  <th>{i18n key="cms_portlet_picture|cms_portlet_picture.messages.force"}</th>
  <td><input name="force" type="checkbox" class="checkbox" value="0" {if $toEdit->force == 0}checked="checked"{/if} /></td>
  </tr>
</table>
</fieldset>
<p class="validButtons">
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
<input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="cms|admin|cancelPortlet"}'" />
</p>
</form>
