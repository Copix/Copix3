<div style="position:relative; float: left" >
	<input id="{$ppo->id}" name="{$ppo->name}" class="{$ppo->class}" style="{$ppo->style}" value="{$ppo->value}" {$ppo->extra} />
	<div id="{$ppo->id}_upbutton"   title="{i18n key='spinbutton|spinbutton.Up'}"   style="position:absolute; right:0px;top: 1px; cursor:pointer; background-image:url({copixresource path='spinbutton|img/spinbutton-up.gif'})  ; height:10px; width:17px;" ></div>
	<div id="{$ppo->id}_downbutton" title="{i18n key='spinbutton|spinbutton.Down'}" style="position:absolute; right:0px;top:11px; cursor:pointer; background-image:url({copixresource path='spinbutton|img/spinbutton-down.gif'}); height:10px; width:17px;" ></div>
	
	<script type="text/javascript">
	//<!--
	//{literal}
		
		$('{/literal}{$ppo->id}{literal}').addEvent ('loadPersoSpinbutton', function () {
			
			var up    = $('{/literal}{$ppo->id}{literal}_upbutton');
			var down  = $('{/literal}{$ppo->id}{literal}_downbutton');
			var input = this;
			var oldForChange = input.value;
			// Fonction forçant les valeurs numeriques
			var spinnumeric_lock = function (elem){
				
				if (elem.value == '') {
					//{/literal}
					//	{if $ppo->useMin}
					//	{literal}
							
							elem.value = '{/literal}{$ppo->min}{literal}';
							return;
					//	{/literal}
					//	{else}
					//	{literal}
							
							elem.value = '0';
							return;
					//	{/literal}
					//	{/if}
					//{literal}
				} else {
	
					// recherche de la virgule et du -
					var virgule = false;
					//{/literal}
					//	{if !$ppo->integer}
					//	{literal}
							
						if (elem.value[elem.value.length-1] == ',' || elem.value[elem.value.length-1] == '.') {
							virgule = true;
							elem.value = elem.value.substring(0, elem.value.length-1);
						}
					//	{/literal}
					//	{/if}
					//{literal}
					
					//{/literal}
					//	{if $ppo->useMin && $ppo->min <= 0}
					//	{literal}
						
						if (elem.value[0] == '-') {
							elem.value =  '{/literal}{$ppo->min}{literal}';
						}
					//	{/literal}
					//{else}
					//	{literal}
						
						if (elem.value == '-' || (elem.value == '0-')) {
							elem.value =  '-';
							return;
						}
					//	{/literal}
					//	{/if}
					//{literal}
					
					
					
					if (elem.value[0] == '0' && elem.value[1] && elem.value[1] != '.' && elem.value[1] != ',') {
						elem.value = elem.value.substring(1);
					}
					
					//{/literal}
					//	{if $ppo->integer}
					//	{literal}
						
						elem.value = parseInt (elem.value);
					//	{/literal}
					//	{else}
					//	{literal}
						
						elem.value = parseFloat (elem.value);
					//	{/literal}
					//	{/if}
					//{literal}
					
					
					if (elem.value == 'NaN' || elem.value == 'NaN.') {
						elem.value = 0;
					}
					
					if (virgule) {
						elem.value = elem.value+'.';
					}
					if (elem.value[0] == '0' && elem.value[1] && elem.value[1] != '.' && elem.value[1] != ',') {
						elem.value = elem.value.substring(1);
					}
					
					if (parseFloat (elem.value) < parseFloat ('{/literal}{$ppo->min }{literal}')) {
						elem.value = parseFloat ('{/literal}{$ppo->min }{literal}');
					}
					if (parseFloat (elem.value) > parseFloat ('{/literal}{$ppo->max }{literal}')) {
						elem.value = parseFloat ('{/literal}{$ppo->max }{literal}');
					}
				}
				if (oldForChange != elem.value) {
					oldForChange = elem.value;
					input.fireEvent ('changePerso');
				}
			};
			
			// Enfoncer
			up.addEvent ('mousedown', function () {
				up.setStyle ('background-position', 'right');
				input.value=parseFloat (input.value) + parseFloat('{/literal}{$ppo->step}{literal}');
				spinnumeric_lock (input);
			});
			down.addEvent ('mousedown', function () {
				down.setStyle ('background-position', 'right');
				input.value=parseFloat (input.value) - parseFloat('{/literal}{$ppo->step}{literal}');
				spinnumeric_lock (input);
			});
			
			// Relacher
			up.addEvent ('mouseup', function () {
				up.setStyle ('background-position', 'left');
			});
			down.addEvent ('mouseup', function () {
				down.setStyle ('background-position', 'left');
			});
			up.addEvent ('mouseout', function () {
				up.setStyle ('background-position', 'left');
			});
			down.addEvent ('mouseout', function () {
				down.setStyle ('background-position', 'left');
			});
			
			
			// Forcer les valeur numérique
			input.addEvent ('keydown', function () {
				spinnumeric_lock (input);
				//{/literal}
					
					{$ppo->onkeydown}
				//{literal}
			});
			input.addEvent ('keyup', function () {
				spinnumeric_lock (input);
				//{/literal}
					
					{$ppo->onkeyup}
				//{literal}
			});
			
			input.addEvent ('change', function () {
				input.fireEvent ('changePerso');
			});
			input.addEvent ('changePerso', function () {
				//{/literal}
					
					{$ppo->onchange}
				//{literal}
			});
		});
		$('{/literal}{$ppo->id}{literal}').fireEvent ('loadPersoSpinbutton');
		
	//{/literal}
	//-->
	</script>
		
</div>
