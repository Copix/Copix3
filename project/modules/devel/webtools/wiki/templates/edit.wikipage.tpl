<form id="_wiki_form" method="POST" action="{copixurl dest="wiki|admin|save" title_wiki=$page->title_wiki}">
<fieldset title="{i18n key="wiki.content"}">
<legend>{i18n key="wiki.content"}</legend> 

{i18n key="wiki.title.displayed"}: <input type="text" name="displayedtitle_wiki" value="{if $displayedtitle}{$displayedtitle}{else}{$page->title_wiki}{/if}" />

<!-- begin Toolbar -->
<div id="wiki_toolbar" style="clear:both">
<a class="wiki_toolbar" onclick="javascript:fontStyle('**','**','{i18n key="wiki|wiki.bold"}');" title="{i18n key="wiki|wiki.bold"}"><img src="{copixresource path="|img/bold.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:fontStyle('//','//','{i18n key="wiki|wiki.italic"}');" title="{i18n key="wiki|wiki.italic"}"><img
	src="{copixresource path="|img/italic.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:fontStyle('__','__','{i18n key="wiki|wiki.underline"}');" title="{i18n key="wiki|wiki.underline"}"><img
	src="{copixresource path="|img/underline.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:fontStyle('    *','','{i18n key="wiki|wiki.listitem"}');" title="{i18n key="wiki|wiki.listitem"}"><img
	src="{copixresource path="|img/list.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:fontStyle('<del>','</del>','{i18n key="wiki|wiki.strike"}');" title="{i18n key="wiki|wiki.strike"}"><img
	src="{copixresource path="|img/strike.png"}" /></a>
<a class="wiki_toolbar" onclick='javascript:fontStyle("\n----\n","","");' title="{i18n key="wiki|wiki.hr"}"><img
	src="{copixresource path="|img/hr.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:fontStyle('\'\'','\'\'','{i18n key="wiki|wiki.code"}');" title="{i18n key="wiki|wiki.code"}"><img
	src="{copixresource path="|img/code.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:addHeader(1);" title="{i18n key="wiki|wiki.header" level=1}"><img
	src="{copixresource path="|img/h1.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:addHeader(2);" title="{i18n key="wiki|wiki.header" level=2}"><img
	src="{copixresource path="|img/h2.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:addHeader(3);" title="{i18n key="wiki|wiki.header" level=3}"><img
	src="{copixresource path="|img/h3.png"}" /></a> 
<a class="wiki_toolbar" onclick="javascript:addHeader(4);" title="{i18n key="wiki|wiki.header" level=4}"><img
	src="{copixresource path="|img/h4.png"}" /></a> 
<a class="wiki_toolbar" onclick="javascript:addHeader(5);" title="{i18n key="wiki|wiki.header" level=5}"><img src="{copixresource path="|img/h5.png"}" /></a> 
<a class="wiki_toolbar" onclick="javascript:fontStyle('[newcol]','','');" title="{i18n key="wiki|wiki.newcol" level=5}"><img
	src="{copixresource path="|img/newcol.png"}" /></a> 
<a class="wiki_toolbar" onclick="javascript:window.open('{copixurl dest="wiki|file|Show" heading=$heading page=$parent}','image','toolbar=no,width=600,height=500')" name="AddImage" title="{i18n key="wiki|wiki.add.image"}"><img
	src="{copixresource path="|img/image.png"}" /></a>
<a class="wiki_toolbar" onclick="javascript:sendForPreview()" title="{i18n key="wiki|wiki.show.preview"}"><img
	src="{copixresource path="|img/preview.png"}" /></a>
<!-- end Toolbar -->
</div>

<textarea class="resizable" id="wiki_area_content" name="content_wiki"
	cols="100" rows="30">{$page->content_wiki|escape}</textarea>
	<script>
	new postEditor.create('wiki_area_content',null,language.COPIXWIKI);
	</script>
	<div id="wiki_preview" style="display: none">
	</div>
</fieldset>

<br />

<fieldset title={i18n key="wiki.info"}>
	<legend>"{i18n key="wiki.info"}"</legend> 
	{i18n key="wiki.author"}: 
	{if $user}
	<strong>{$user}</strong><br />
	{else}
		<input type="text" name="author" value="" /><br />
	{/if}
	<input type="hidden" name="frompage" value="{$pagesource}" />
	<input type="hidden" name="fromlang" value="{$fromlang}" />
	{if $pagesource}
		{i18n key="wiki.translatedfrom"} : <a href="{copixurl dest="wiki||show" title=$pagesource lang=$fromlang}" onclick="window.open(this)">{$pagesource}</a> ({$fromlang})<br />
	{/if}
	{i18n key="wiki.languages"} : <select name="lang">
	{foreach from=$langs item=zlang}
		<option value="{$zlang}"{if $lang==$zlang} SELECTED{/if}>{$zlang}</option>
	{/foreach}
	</select>
	<br />
	{i18n key="wiki.heading"}: {autocomplete dao="wikiheadings" field="heading_wikihead" name="heading" value=$heading}<br />
	{i18n key="wiki.description"} : <input type="text" name="description_wiki" value="{$page->description_wiki}" /><br />
	{i18n key="wiki.keywords"} : <input type="text" name="keywords_wiki" value="{$page->keywords_wiki}" /><br />
</fieldset>
<input type="submit" name="sendpage" value="OK" /> 
<input type="button" name="return" value="Back"
	onclick="javascript:document.location.href='{copixurl dest="wiki||show" title=$page->title_wiki heading=$heading}'"/></form>
