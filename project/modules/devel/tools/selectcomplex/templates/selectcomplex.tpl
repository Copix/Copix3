{* alternative si le javascript est désactivé cela permet l'accessibilité *}
{if $ppo->alternatives}
	{select id=$ppo->id name=$ppo->name values=$ppo->alternatives
	        emptyShow=$ppo->emptyShow extra=$ppo->extra 
	        emptyValues=$ppo->emptyValuesAlternatives selected=$ppo->selected}
{/if}
<div id="{$ppo->id}{if $ppo->alternatives}_alt{/if}" style="{if $ppo->alternatives}display:none;{/if}" class="selectcomplex {$ppo->class}" {$ppo->extra} >
	<div id="{$ppo->id}_value" class="selectcomplex_box" style="visibility: hidden;{$ppo->style}" >
	</div>
	<div id="{$ppo->id}_clicker" class="selectcomplex_button" style="visibility: hidden;">
	{if $ppo->arrow}<img alt="" src="{$ppo->arrowImg}" />{/if}
	</div>
	<div style="clear: both"></div>
</div>
<div id="{$ppo->id}_values" style="visibility: hidden;" class="selectcomplex_values">
	<div id="{$ppo->id}_values_list" class="selectcomplex_values_list">
		{* Valeur vide *}
		{if $ppo->emptyShow}
			<div class="selectcomplex_option_alternate selectcomplex_option_value_{$ppo->id}_" >
				<input class="selectcomplex_value_var" type="button" value="" style="display: none"/>
				{$ppo->emptyValues}
			</div>
		{/if}
		
		{* Liste de valeurs *}
		{assign var='alternate' value='_alternate'}
		{foreach from=$ppo->options key=key item=option}
			{if $alternate=='_alternate'}{assign var='alternate' value=''}{else}{assign var='alternate' value='_alternate'}{/if}
			<div class="selectcomplex_option{$alternate} selectcomplex_option_value_{$ppo->id}_{$key}" >
				<input class="selectcomplex_value_var" type="button" value="{$key}" style="display: none"/>
				{$option}
			</div>
		{/foreach}
	</div>
</div>
{if $ppo->selectedView}
	<div id="{$ppo->id}_values_view" style="visibility: hidden; position: absolute;" >
		{* Valeur vide *}
		{if $ppo->emptyShow}
			<div class="selectcomplex_selectedView_value_{$ppo->id}_"  >
				{$ppo->emptyValuesView}
			</div>
		{/if}
		
		{* Liste de valeurs *}
		{foreach from=$ppo->selectedView key=key item=option}
			<div class="selectcomplex_selectedView_value_{$ppo->id}_{$key}" >
				{$option}
			</div>
		{/foreach}
	</div>
{/if}
{if $ppo->alternatives}
	<script type="text/javascript">
		$ ('{$ppo->id}_alt').setStyle ('display', 'block');
		$ ('{$ppo->id}').destroy ();
		$ ('{$ppo->id}_alt').id='{$ppo->id}';
	</script>
{/if}
<script type="text/javascript">
	//<!--
	//{literal}
		$ ('{/literal}{$ppo->id}{literal}').value='{/literal}{$ppo->selected}{literal}';
		window.addEvent ('load', function () {
			var boxWidth = parseInt ('{/literal}{$ppo->width}{literal}') - $('{/literal}{$ppo->id}{literal}_clicker').getSize ().x - 8;
			var id= '{/literal}{$ppo->id}{literal}';
			
			if (boxWidth < 0) {
				var max = 0;

				var list = $(id+'_values_view');
				if (!list) {
					list = $(id+'_values_list');
				}
				
				list.getElements ('div').each (function (el) {
					if (el.getSize ().x > max) {
						max = el.getSize ().x;
					}
				});
				$(id).setStyle('width', (max+$(id+'_clicker').getSize ().x+6) + 'px');
				$(id+'_value').setStyle('width', (max-6)+'px');
			} else {
				$(id).setStyle('width', (parseInt ('{/literal}{$ppo->width}{literal}'))+'px');
				$(id+'_value').setStyle('width', boxWidth+'px');
			}

			// Affectation de la largeur imposé
			if ('{/literal}{$ppo->widthSelect}{literal}' == 'auto') {
				$(id+'_values').setStyle ('width', ($(id+'_values_list').getSize().x+25)+'px');
			} else {
				$(id+'_values').setStyle ('width', '{/literal}{$ppo->widthSelect}{literal}px');
			}
			
			// Affection de la hauteur
			$(id+'_values').setStyle ('height', '{/literal}{$ppo->heightSelect}{literal}px');
			
			if ($(id+'_values').getScrollHeight () > parseInt ('{/literal}{$ppo->heightSelect}{literal}px')) {
				$(id+'_values').addClass ('selectcomplex_useScroll');
			}

			$(id+'_value').setStyle('visibility', '');
			$(id+'_clicker').setStyle('visibility', '');
			
		});
	//{/literal}
	//-->
</script>
