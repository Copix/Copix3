{if count($ppo->arThemes)}
 <table class="CopixTable">
 <thead>
  <tr>
   <th>Themes</th>
   <th>&nbsp;</th>
  </tr>
 </thead>
 <tbody>
  <tr {cycle values=',class="alternate"'}>
   <td>{i18n key=copix:common.none} (default)</td>
   <td><a href="{copixurl dest='themechooser||doSelectTheme' id_ctpt=''}"><img src="{if $ppo->selectedTheme == $theme->id_ctpt}{copixresource path="img/tools/selected.png"}{else}{copixresource path="img/tools/select.png"}{/if}" alt="{i18n key=copix:common.buttons.select}"></a></td>
  </tr>
 {foreach from=$ppo->arThemes item=theme}
  <tr {cycle values=',class="alternate"'}>
   <td>
{popupinformation}
<table>
	<tr>
{if $theme->image!=null}
		<td>
		<img src="{copixurl dest='admin|themes|getImage' id=$theme->id name=$theme->image}">
		</td>
{/if}
		<td>
			{i18n key=admin|themes.theme.name} : {$theme->name}<br />
			{if $theme->author!=null}
			{i18n key=admin|themes.theme.author} : {$theme->author}<br />
			{/if}
			{if $theme->website!=null}
			{i18n key=admin|themes.theme.website} : <a href="{$theme->website}">{$theme->website}</a><br />
			{/if}
			{if $theme->description!=null}
			{i18n key=admin|themes.theme.description} : {$theme->description}<br />
			{/if}

		</td>
	</tr>
</table>
{/popupinformation}
  {if $ppo->selectedTheme == $theme->id}<b>{/if}{$theme->name}{if $ppo->selectedTheme == $theme->id}</b>{/if}
  </td>
  <td><a href="{copixurl dest='themechooser||doSelectTheme' id_ctpt=$theme->id}"><img src="{if $ppo->selectedTheme == $theme->id}{copixresource path="img/tools/selected.png"}{else}{copixresource path="img/tools/select.png"}{/if}" alt="{i18n key=copix:common.buttons.select}"></a>
  </td>
  </tr>
 {/foreach}
 </tbody>
 </table>
<input
	type="submit" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:window.location='{copixurl}'">
{/if}