<style>
{literal}
	div.webBox { padding: 0; border: 0; position: relative; display: block; margin: 5px; z-index: 1}
	div.handle { padding: 2px; background: transparent; color: #EDEDED; display: block; cursor: move; }
	div.handle:hover { padding: 2px; background: transparent; color: blue; display: block; cursor: move; }
	div.content { padding: 5px; background: white; }
	div.webBoxMarker { border: 1px dotted black; margin: 0 0 5px 0; }
	div#webBoxContainer { width: 100%; }
	div#webBoxContainer .webBoxCol { float: left; vertical-align: top; margin: 1px}
	#getchoices{display: none; background-color: #FFF; border: 1px solid #EFEFEF}
{/literal}
</style>
<div id="webBoxContainer">			
		<div class="webBoxCol" id="zone1" style="width: 49%">
			{$zone1}
		</div>
		<div class="webBoxCol" id="zone2" style="width: 24%">
			{$zone2}
		</div>
		<div class="webBoxCol" id="zone3" style="width: 24%">
			{$zone3}
		</div>
</div>