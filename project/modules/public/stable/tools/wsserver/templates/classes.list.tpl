<script type="text/javascript">
{literal}
window.addEvent('domready',function() {
  	var mySlides = new Array();
	  	
	$$('#listeModule div.toggling').each(function (el,i){
		el.getPrevious().addEvent('click', function(e) {
			e = new Event(e);
			mySlides[i].toggle();
			e.stop();
		});
		mySlides[i] = new Fx.Slide(el);
		mySlides[i].hide();
	});  

});
{/literal}
 
</script>
<div id="listeModule">
  {foreach from=$arModules item=module}
  	<div class="toggler" onmouseover="this.style.cursor='pointer';" onmouseout="this.style.cursor='default';">
  	<h2>{$module->description}</h2></div>
  	
	<div class="toggling">      
      <table class="CopixTable">
		<tr>
 			<th width="90%" align="left">{i18n key=wsserver.titleTab.name}</th>
 			<th width="10%">{i18n key=wsserver.titleTab.actions}</th>
		</tr>
      	{foreach from=$module->services item=services}
      	
      	<tr {cycle values=",class='alternate'"}>
      		<td>&nbsp;
          	{$services}
          	</td>
			<td><a title="{i18n key="copix:common.buttons.export"}" href="{copixurl dest="wsserver|admin|exportClass" moduleName=$module->name classFileName=$services}"><img src="{copixresource path="img/tools/export.png"}" alt="{i18n key="copix:common.buttons.export"}" /></a></td>
		</tr>          	
		
        {/foreach}
        </table>      
	</div>     
  {/foreach}
</div>
<br />
{i18n key=wsserver.libelle.toggle}
<br />