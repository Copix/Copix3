<?php 
	_eTag('mootools', array('plugins'=>'copixformobserver'));
	CopixHTMLHeader::addJSLink(_resource('cms_rss|js/tools.js'));
	CopixHTMLHeader::addCSSLink(_resource('portal|styles/style.css'));
	//initialisation des variables
	$identifiantFormulaire = $portlet->getRandomId ();	
	$params = new CopixParameterHandler();
	$options = $portlet->getOptions ();	
	$params->setParams($options);
?>
<div id="div_<?php echo $identifiantFormulaire;?>">
	<form id="rssform_<?php echo $identifiantFormulaire;?>" >
		<label for="clicker<?php echo $identifiantFormulaire;?>">Flux : </label>
		<?php 
		echo CopixZone::process('heading|headingelement/headingelementchooser', array('inputElement'=>"id_flux".$identifiantFormulaire, 'identifiantFormulaire'=>$identifiantFormulaire, 'arTypes'=>array('rss'))); ?>
		<br />
		<label for="caption_rss<?php echo $identifiantFormulaire;?>">Libellé du lien : </label>
		<input type="text" id="caption_rss<?php echo $identifiantFormulaire;?>" name="caption_rss" value="<?php echo $params->getParam('caption_rss', "S'abonner au flux"); ?>" />
	</form>
</div>
<?php
CopixHTMLHeader::addJSDOMReadyCode("
var formTextIdentifiantObserver".$identifiantFormulaire." = new CopixFormObserver ('rssform_".$identifiantFormulaire."', {
      onChanged : function (){
         updateRss($('id_flux".$identifiantFormulaire."').value, $('caption_rss".$identifiantFormulaire."').value, '".$portlet->getRandomId()."', '"._request('editId')."');
      }
   });
");
?>