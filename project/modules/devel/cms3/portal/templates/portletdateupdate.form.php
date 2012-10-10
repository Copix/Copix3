<?php 
	_eTag('mootools', array('plugins'=>'copixformobserver'));
	CopixHTMLHeader::addJSLink(_resource('portal|js/tools.js'));
	CopixHTMLHeader::addCSSLink(_resource('portal|styles/style.css'));
	//initialisation des variables
	$identifiantFormulaire = $portlet->getRandomId ();	
?>
<div id="div_<?php echo $identifiantFormulaire;?>">
<?php echo $tpl; ?>
</div>
<?php
CopixHTMLHeader::addJSDOMReadyCode("
if ($('portletTemplate".$identifiantFormulaire."')){
	$('portletTemplate".$identifiantFormulaire."').addEvent('change', function (){
		updateDateUpdate('".$identifiantFormulaire."', '".$portlet->getRandomId()."', '"._request('editId')."');
	});
}
");
?>