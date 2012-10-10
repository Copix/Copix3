<h2>{$toShow->title|escape:html}</h2>
<select name="selectServices" id="selectServices" onChange="javascript:location.href= this.options[this.selectedIndex].value ; ">
<option value="titre">{i18n key=cms_portlet_links|links.message.selectALink}</option>
{foreach from=$toShow->links item=link}
<option value="{$link->linkDestination}">{$link->linkName|escape:html}</option>
{/foreach}
</select>