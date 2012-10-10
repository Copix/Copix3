<?php 
	CopixHTMLHeader::addJSLink(_resource('portal|js/tools.js'));
	CopixHTMLHeader::addCSSLink(_resource('portal|styles/style.css'));
	//initialisation des variables
	$identifiantFormulaire = $portlet->getRandomId ();	
	$params = new CopixParameterHandler();
	$nuageOptions = $portlet->getOptions ();
	$params->setParams($nuageOptions);
?>
<div id="div_<?php echo $identifiantFormulaire;?>">
<?php 		
	//div des options 
	echo CopixZone::process ('portal|portletOptionMenu', array ('options'=>$nuageOptions, 'identifiantFormulaire'=>$identifiantFormulaire, 'portlet_id'=>$portlet->getRandomId (), 'template'=>'nuagetagoptionmenu.php'));
	//div d'affichage du contenu de l'titre selectionné ?>
	<div id="titre_<?php echo $identifiantFormulaire;?>" style="margin-bottom:10px;">
		<div style="vertical-align:bottom;">
			<span title="Link vers un élément" class="boutonElementChooser">
                <?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array('class'=>"bouton-image", 'img'=>_resource('heading|img/links.png'), 'inputElement'=>'portletTexteAddLink_'.$identifiantFormulaire, 'identifiantFormulaire'=>'link'.$identifiantFormulaire, 'showSelection'=>false)); ?>
            </span>
		</div>
		<textarea name="text_<?php echo $identifiantFormulaire;?>" id="text_<?php echo $identifiantFormulaire;?>" style="width:99%"><?php echo $params->getParam('text'); ?></textarea>
	</div>
</div>
<?php
CopixHTMLHeader::addJSDOMReadyCode("
$('text_".$identifiantFormulaire."').addEvent('keyup', function(){updateText($('text_".$identifiantFormulaire."').value, '".$identifiantFormulaire."', '"._request('editId')."');});
$('portletTexteAddLink_".$identifiantFormulaire."').addEvent('change', function (){
    var text = $('libelleElementlink".$identifiantFormulaire."').innerHTML;
	setPolice('[', text+']((cms:'+$(\"portletTexteAddLink_".$identifiantFormulaire."\").value+') \"'+text+'\")'+\"\\n\", 'text_".$identifiantFormulaire."');
});
");
?>