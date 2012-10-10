<?php 
CopixHTMLHeader::addStyle('.imageCaptionElementChooser', 'white-space:nowrap; text-overflow:ellipsis; overflow:hidden; width:90px;display:block;');

	//initialisation des variables
	$identifiantFormulaire = $portlet->getRandomId ()."_pos_".$position;
	$imageOptions = array();
	if(isset($image)){
		$imageOptions = $portlet->getPortletElementAt($position)->getOptions ();
	}
	//si on veut afficher un image de la portlet
	if(!$justAddImage){
		//si on veut afficher un image existant, on l'insere dans une div qui l'identifie, sinon pour un nouvel image ce sera fait par l'appel javascript
		if(!$newImageVide){?>
	<div id="div_<?php echo $identifiantFormulaire;?>">
<?php 	} ?>
	<div class="fileBloc<?php echo ucfirst($editionMode); ?>">
	<?php  //div d'affichage du contenu du image selectionné ?>
		<div class="thumbImage" id="image_<?php echo $identifiantFormulaire;?>" style="position: relative;">
		<?php		
			//affichage du image
			if(isset($image)){
				if ($image->type_hei == "heading"){
					$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($image->public_id_hei, 'image');
					$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);
					foreach ($children as $child){
						$listeImage[] = _ioClass('images|imageservices')->getByPublicId ($child->public_id_hei);
					} 				
				} else {
					$listeImage[] = $image;			
				}
				$tpl = new CopixTpl();
				$tpl->assign('options', $imageOptions);
				$tpl->assign('listeImage', $listeImage);
				$tpl->assign('identifiantFormulaire', $identifiantFormulaire);
				echo $tpl->fetch("imageformadminview".$editionMode.".php");
			} else {
				echo "<a href='javascript:void(0)' id='imgClicker".$identifiantFormulaire."' ><img class='galleryImageAdd' id='imgChoix".$portlet->getRandomId ()."' src='"._resource('images|img/choisirimage.png')."' /></a>";	
				CopixHTMLHeader::addJSDOMReadyCode("$('imgClicker".$identifiantFormulaire."').addEvent('click', function(){ $('clicker".$identifiantFormulaire."').fireEvent('click');});");
			}
		?>
		</div>
		<div class="optionsFile" id="divImageOptions<?php echo $identifiantFormulaire;?>" style="display:<?php echo isset($image) ? '' : 'none';?> ">
		<?php //div des options 
			
			//options
			echo CopixZone::process ('images|imageOptionMenu', array('options'=>$imageOptions,
		        'identifiantFormulaire'=>$identifiantFormulaire,
				'image'=>isset($image) ? $image : null,
		        'position'=>$position,
		        'portlet_id'=>$portlet->getRandomId ()));
			
			$selected = (isset($image)) ? $image->public_id_hei : '';
			
			echo CopixZone::process ('heading|headingelement/headingelementchooser', array('arTypes'=>array('image'), 'mode'=>ZoneHeadingElementChooser::IMAGE_CHOOSER_MOD, 'selectedIndex'=>$selected, 'inputElement'=>'id_image_'.$identifiantFormulaire, 'identifiantFormulaire'=>$identifiantFormulaire, 'multipleSelect'=>true));
			CopixHTMLHeader::addJSDOMReadyCode("
				$('libelleElement".$identifiantFormulaire."').setStyle('display','none');
				$('clicker".$identifiantFormulaire."').setStyle('display','none');");
		
		//formulaire de choix d'image ?>
				<form id="form_<?php echo $identifiantFormulaire;?>" class="headForm">
				<input type="hidden" id="updateImage_<?php echo $identifiantFormulaire;?>" value="<?php echo $editionMode || !isset($image) ? 'true'  : 'false'; ?>"/>
				
				<input type="hidden" id="position_image_<?php echo $identifiantFormulaire; ?>" name="position_image_<?php echo $identifiantFormulaire; ?>" value="<?php echo $position; ?>" />
			</form>
		</div>
	</div>
		<?php 
		//pour le image existant, on ferme la div
		if(!$newImageVide){ ?>
			</div>

		<?php
		}
		CopixHTMLHeader::addJSDOMReadyCode("
		$('id_image_".$identifiantFormulaire."').addEvent('change', function(){updateImage('".$identifiantFormulaire."', '".$portlet->getRandomId ()."', '"._request('editId')."');});
		");
	}
	//si on ne veut afficher que le bouton ajouter
	else{ 
		CopixHTMLHeader::addJSLink(_resource('images|js/tools.js'));
	?>

<div id="addImage_<?php echo $portlet->getRandomId ();?>">
	<div style="clear:both;"></div>
	<a href="<?php echo _url('images|admin|convertImageToDiapo', array('editId' => _request('editId'), 'portal_id' => $portlet->getRandomId ()))?>">
		<img src="<?php echo _resource('img/tools/convert.png'); ?>" />convertir en diaporama
	</a>
    <input type="hidden" id="position_<?php echo $portlet->getRandomId ();?>" value="<?php echo $position; ?>"/>
    <input type="hidden" id="editionMode_<?php echo $portlet->getRandomId ();?>" value="<?php echo $editionMode; ?>"/>
    <input type="hidden" id="currentTemplate_<?php echo $portlet->getRandomId ();?>" value="<?php echo $currentTemplate; ?>"/>
</div> 
	<?php } ?>
<script>
function addElements<?php echo $identifiantFormulaire;?> (mutex){
	
	var selectedElements = getSelectedElements ('<?php echo $identifiantFormulaire;?>');
	if (selectedElements.length == 1){
		updateImage('<?php echo $identifiantFormulaire;?>', '<?php echo $portlet->getRandomId ();?>', '<?php echo _request('editId');?>');
	} else {
		selectedElements.each(function (el){
			addImage('<?php echo $identifiantFormulaire;?>', '<?php echo $portlet->getRandomId ();?>', '<?php echo _request('editId');?>', el.get('pih'), false);
		});
	}
}
</script>
<?php CopixHTMLHeader::addJSDOMReadyCode("
if ($('portletTemplate".$portlet->getRandomId ()."')){
	$('portletTemplate".$portlet->getRandomId ()."').addEvent('change', function(){
		updateView($('portletTemplate".$portlet->getRandomId ()."').value, '".$portlet->getRandomId ()."', '$identifiantFormulaire', '"._request('editId')."');
	});
}
");?>