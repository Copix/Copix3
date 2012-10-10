{copixhtmlheader kind="jsdomreadycode"}
{literal}
	var myAccordion = new Accordion($('listeModule'), 'h2', '.toggling', {
		opacity: false,
		show: 0,
		onActive: function(toggler, element){
		},
		onBackground: function(toggler, element){
		}
	});
{/literal}	
{/copixhtmlheader}

<div id="listeModule">
  {foreach from=$ppo->arModules item=module}
  	<div class="toggler" onmouseover="this.style.cursor='pointer';" onmouseout="this.style.cursor='default';">
  	<h2>{$module->description}</h2></div>
  	
	<div class="toggling">      
      <table class="CopixTable">
		<tr>
 			<th width="90%" align="left">{i18n key=soap_server.titleTab.name}</th>
 			<th width="10%">{i18n key=soap_server.titleTab.actions}</th>
		</tr>
      	{foreach from=$module->services item=services}
      	<tr {cycle values=",class='alternate'"}>
      		<td>{$services}</td>
			<td><a title="{i18n key="copix:common.buttons.export"}" href="{copixurl dest="soap_server|admin|exportClass" class="`$module->name`|`$services`"}"><img src="{copixresource path="img/tools/export.png"}" alt="{i18n key="copix:common.buttons.export"}" /></a></td>
		</tr>          	
        {/foreach}
        </table>      
	</div>     
  {/foreach}
</div>