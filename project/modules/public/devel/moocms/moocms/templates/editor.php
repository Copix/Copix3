<style>
	div.webBox { padding: 0; border: 0; position: relative; display: block; margin: 0 0 10px 0; z-index: 1;}
	div.handle { padding: 2px; background: transparent; color: #EDEDED; display: block; cursor: move; }
	div.handle:hover { padding: 2px; background: transparent; color: blue; display: block; cursor: move; }
	div.content { padding: 5px; background: white; }
	div.webBoxMarker { border: 1px dotted black; margin: 0 0 5px 0; }
	div#webBoxContainer { width: 100%; }
	div#webBoxContainer .webBoxCol { float: left; vertical-align: top; margin: 1px}
	#getchoices{display: none; background-color: #FFF; border: 1px solid #EFEFEF}
</style>
<?php
	_eTag('mootools',array('plugin'=>'smoothbox'));
	CopixHTMLHeader::addCSSLink(_resource("js/mootools/css/smoothbox.css"));
?>
<h2>Page de contenu</h2>
<!-- a title="Add new content" href="#TB_inline?inlineId=getchoices&height=450&width=550" class="smoothbox">test</a -->
<p style="padding: 5px; border: 1px solid #AAA">
<a href="<?php echo _url('moocms|admin|getboxes',array('height'=>450,'width'=>550)); ?>" title="Ajouter un moobox" class="smoothbox">Add box</a> - 
<a href="#" onClick="javascript:onValidPage()">Valid</a> - 
<a href="#" onClick="javascript:toolbars.each(function(el){el.toggle()})">Show/hide toolbars</a>
</p>
Titre de la page: 
<input type="text" id="pagename" value="<?php echo $ppo->pagename; ?>" />
<input type="hidden" id="template" value="<?php echo $ppo->template; ?>" />
<div id="messages">
</div>

<?php echo $ppo->display ?>

<script>
window.addEvent('domready',function(){
	<?php echo $ppo->init; ?>
	
})
</script>


