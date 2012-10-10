{** Show Images **}
<style type="text/css">
   {literal}
   body{
   		font-family: Sans,Verdana;
   }
   img{
   	border: 0px;
   }
   {/literal}
</style>
<script>
{literal}
function addImage(image){
	var content;
	content = window.opener.document.getElementById('wiki_area_content').value
	content+="{{"+image+"}}";
	window.opener.document.getElementById('wiki_area_content').value = content
	window.close();
}
function addFile(image){
	var content;
	content = window.opener.document.getElementById('wiki_area_content').value
	content+="{{file:"+image+"}}";
	window.opener.document.getElementById('wiki_area_content').value = content
	window.close();
}
{/literal}
</script>
<form method="POST" id="page_form" action="{copixurl dest="wiki|file|Show"}">
<fieldset title="{i18n key="wiki.page.with.image"}">
<legend>{i18n key="wiki.page.with.image"}</legend>
<select name="page"
	onchange="javascript:document.getElementById('page_form').submit()">
	<option value="">{i18n key="wiki|wiki.all.images"}</option>
	{foreach from=$pageswithimage item=pageimg}
	<option value="{$pageimg}" {if $pageimg==$selected}SELECTED{/if}>{$pageimg}</option>
	{/foreach}
</select>
</fieldset>
<input type="submit" value="ok" />
</form>

{assign var=i value=0}
<div style="height: 200px; width:100%; overflow: auto">
<table style="border: 1px solid black">
<tr>
{foreach from=$images item=image}
		<td
			style="width:130px;text-align: center; vertical-align: top;border: 1px solid black">
		<em><strong>{$image->title_wikiimage}</strong></em><br />
		{if $image->type=="image"} <a href="#"
			onclick="javascript:addImage('{$image->title_wikiimage}');"
			title="{$image->title_wikiimage}"><img src="{copixurl dest="wiki|file|getFile" title=$image->title_wikiimage}"
		alt="{$image->title_wikiimage}" width="120"/></a> {else} <a href="#"
			onclick="javascript:addFile('{$image->title_wikiimage}');"
			title="{$image->title_wikiimage}">Fichier: {$image->title_wikiimage}</a>
		{/if} {assign var=i value=$i+1} {if $i==4} {assign var=i value=0}
	</tr><tr> 
{/if} 
{foreachelse} 
{i18n key="wiki|wiki.no.image"}
</td>
{/foreach}
</tr>
</table>
</div>
<form method='POST' enctype="multipart/form-data" action="{copixurl dest="wiki|file|saveImage" page=$page heading=$heading}">
<fieldset title="{i18n key="wiki.add.image"}">
<legend>{i18n key="wiki.add.imagename"}</legend>
<input type="text" name="title_wikiimage" value="" /><br />
<legend>{i18n key="wiki.add.image"}</legend>
<input type="file" name="image" />
</fieldset>
<input type="submit" value="{i18n key="wiki.send.image"}"/>
</form>