{literal}
<script type=" text/javascript">

window.addEvent('domready', function() {
	var isChecked = false; 
	$('check_all').addEvent('click', function() {
			if (isChecked == true) {
				$('check_all').checked = false;
				$$('input.check_all').each(function(el) {
					el.checked = false;
					
				});
				isChecked = false;
			} else {
				$$('input.check_all').each(function(el) {
					el.checked = true;
					
				});
				isChecked = true;
			}

	})
});

</script>
{/literal}
<h2>{i18n key=cleaner.parameter.cleanedDirectory}</h2>

<table class="CopixTable">
<tr>
 <th>{i18n key=cleaner.file.links}</th>
 <th>{i18n key=cleaner.file.filter}</th>
</tr>
  {foreach from=$arDirectory item=dir}
     <tr {cycle values=",class='alternate'"}>
      <td><a title="{i18n key="copix:common.buttons.link"}" href="{copixurl dest="cleaner|cleaner|" directory=$dir filter=""}">{$dir}</a></td>
      <td><form action="{copixurl dest="cleaner|cleaner|}"><input type="hidden" name="directory" value="{$dir}"/>
      <input type="text" name="filter"><input type="submit" value="{i18n key="cleaner.action.filter"}"/></form></td>
     </tr>
  {/foreach}
  <tr {cycle values=",class='alternate'"}>
  	<td><a title="{i18n key="copix:common.buttons.link"}" href="{copixurl dest="cleaner|cleaner|" dir=""}">COPIX_TEMP_PATH</a></td>
  	<td><form action="{copixurl dest="cleaner|cleaner|}"><input type="hidden" name="directory" value=""/>
      <input type="text" name="filter"><input type="submit" value="{i18n key="cleaner.action.filter"}"/></form>
  	</td>
  </tr>
</table>
<br/>
<form method="POST" action="{copixurl dest="cleaner|cleaner|deleteFile}">
<input type="hidden" name="directory" value="{$directory}"/> 
<table class="CopixTable">
<tr>
 <th>&nbsp;</th>
 <th>{i18n key=cleaner.file.name}</th>
 <th>{i18n key=cleaner.file.actions}</th>
</tr>
<tr {cycle values=",class='alternate'"}>
 <td><input type="checkbox" name="check_all" id="check_all"></th>
 <td colspan="2">{i18n key=cleaner.file.checkall}</td>
</tr>
  {foreach from=$arFiles item=files}
     <tr {cycle values=",class='alternate'"}>
      <td><input type="checkbox" name="file[]" value="{$files}" class="check_all"></td>
      <td>{$files}
      </td>
      <td><a title="{i18n key="copix:common.buttons.delete"}" href="{copixurl dest="cleaner|cleaner|deleteFile" file=$files directory=$directory}"><img src="{copixresource path="img/tools/delete.png"}" alt="{i18n key="copix:common.buttons.delete"}" /></a></td>
     </tr>
  {/foreach}
</table>
<input type="submit" value="{i18n key="cleaner.action.clean"}"/>
</form>