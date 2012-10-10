<?php 
	CopixHTMLHeader::addJSLink(_resource('portal|js/tools.js'));
	CopixHTMLHeader::addCSSLink(_resource('portal|styles/style.css'));
	//initialisation des variables
	$identifiantFormulaire = $portlet->getRandomId ();	
	$params = new CopixParameterHandler();	
	$params->setParams($portlet->getOptions ());
?>

<form id="menu_<?php echo $identifiantFormulaire;?>" method="post">
	<label>Element : </label><?php echo CopixZone::process('heading|headingelement/headingelementchooser', array('linkOnHeading'=>true, 'id'=>'portletMenuElementChooser'.$identifiantFormulaire, 'selectedIndex'=>$params->getParam('public_id_hem')>0 ? $params->getParam('public_id_hem') : "", 'inputElement'=>'menu_public_id_hem_'.$identifiantFormulaire, 'identifiantFormulaire'=>$identifiantFormulaire)); ?><br />
	<label for="level_<?php echo $identifiantFormulaire; ?>">Niveau : </label>
	<input class="text inputMenu" size="2" type="text" name="menu_level_<?php echo $identifiantFormulaire; ?>" id="menu_level_<?php echo $identifiantFormulaire; ?>" value="<?php echo $params->getParam('level_hem'); ?>" <?php echo $params->getParam('portlet_hem') ? 'disabled="disabled"' : ""; ?> />&nbsp;&nbsp;
	<label for="depth_<?php echo $identifiantFormulaire; ?>">Profondeur : </label>
	<input class="text inputMenu" size="2" type="text" name="menu_depth_<?php echo $identifiantFormulaire; ?>" value="<?php echo $params->getParam('depth_hem'); ?>" id="menu_depth_<?php echo $identifiantFormulaire; ?>" <?php echo $params->getParam('portlet_hem') ? 'disabled="disabled"' : ""; ?> />
</form>

<?php 
_tag('mootools', array ('plugin'=>array('copixformobserver')));
CopixHTMLHeader::addJSDOMReadyCode("
	var formObserver".$identifiantFormulaire." = new CopixFormObserver ('menu_".$identifiantFormulaire."', {
     	onChanged : function (){
        	updateMenu ('".$identifiantFormulaire."', '".$portlet->getRandomId()."', '"._request('editId')."');
      	},
     	checkIntervall :50
   	});

	/*$('menu_portlet_$identifiantFormulaire').addEvent('change', function(){
		$('level_$identifiantFormulaire').disabled = $('menu_portlet_$identifiantFormulaire').checked;
		$('depth_$identifiantFormulaire').disabled = $('menu_portlet_$identifiantFormulaire').checked;
	});
	$('menu_public_id_hem_$identifiantFormulaire').addEvent('change', function (){
		if ($('menu_public_id_hem_$identifiantFormulaire').get('type_hei') == 'portlet'){
			$('menu_portlet_$identifiantFormulaire').set ('disabled', '');
		}
	}.bind(this));*/
");

?>