<?php 
	CopixHTMLHeader::addJSLink(_resource('portal|js/tools.js'));
	_eTag('mootools', array('plugins'=>'copixformobserver'));
	//initialisation des variables
	$identifiantFormulaire = $portlet->getRandomId ();	
	$params = new CopixParameterHandler();
	$params->setParams($portlet->getOptions ());
?>
<div id="div_<?php echo $identifiantFormulaire;?>">
	<span style="color: red;" id="erreuranchor_<?php echo $identifiantFormulaire;?>"></span>
	<label for="anchorname<?php echo $identifiantFormulaire;?>">Nom de l'ancre</label>
	<input type="text" value="<?php echo $params->getParam('name'); ?>" name="anchorname<?php echo $identifiantFormulaire;?>" id="anchorname<?php echo $identifiantFormulaire;?>" />
	
</div>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("

var observer".$identifiantFormulaire." = new CopixFormObserver ('div_".$identifiantFormulaire."', {
		onChanged: function (){
			updateAnchor('".$identifiantFormulaire."', '".$portlet->getRandomId()."', '"._request('editId')."');
		}
	});
");
?>