<table class="CopixTable">
 <thead>
  <tr>
   <th>Nom</th>
   <th>Actions</th>
 </tr>
 </thead>
 <tbody>
 {foreach from=$ppo->arPlugins item=pluginInformations}
  <tr {cycle values=',class="alternate"'}>
   <td>{$pluginInformations.name}</td>
   <td>{if $pluginInformations.enabled}
   				<a
				href="{copixurl dest=plugin|removePlugin plugin=$pluginInformations.name}">
				<img
				src="{copixresource path='img/tools/delete.png'}" />
				</a>
		{else}
		        <a
				href="{copixurl dest=plugin|addPlugin plugin=$pluginInformations.name}">
			    <img src="{copixresource path='img/tools/add.png'}" />
			</a>
			{/if}</td>
  </tr>
 {/foreach}
 </tbody>
</table>

<a href="{copixurl dest="admin||"}"><input type="button" value="{i18n key='copix:common.buttons.back'}" /></a>