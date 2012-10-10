{foreach from=$ppo->links key=groupId item=groupInfos}
<h2>
<table>
	<tr>
		<td width="100%">
			{if ($groupInfos.icon)}
			<img src="{$groupInfos.icon}" alt="{$moduleInfos.name}" title="{$moduleInfos.name}" />
			{/if}  
			{if $groupInfos.groupcaption}
			{$groupInfos.groupcaption}
			{else}
			{$groupInfos.caption}
			{/if}
		</td>
		<td>
			{showdiv id="group_$groupId"}			
		</td>
	</tr>
</table>
</h2>

<div id="group_{$groupId}">
<table class="CopixVerticalTable">
	{foreach from=$groupInfos.modules item=moduleInfos key=moduleIndex}
	{foreach from=$moduleInfos item=linkCaption key=linkUrl}
	<tr {cycle values=',class="alternate"' name="alternate"}>
		<td width="100%">
			<a href="{$linkUrl}" class="adminLink" title="{i18n key="copix:common.buttons.select"}">{$linkCaption}</a>
		</td>
		<td>
			<a href="{$linkUrl}" title="{i18n key="copix:common.buttons.select"}"
				><img src="{copixresource path="img/tools/select.png"}" alt="{i18n key="copix:common.buttons.select"}"
			/></a>
		</td>
	</tr>
	{/foreach}
	{/foreach}
</table>
</div>
{/foreach}

{copixtips tips=$ppo->tips warning=$ppo->warning titlei18n="install.tips.title"}