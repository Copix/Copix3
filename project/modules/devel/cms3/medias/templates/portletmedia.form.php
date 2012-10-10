<?php 
	//TODO : à decouper en plusieurs template

	//initialisation des variables
	$identifiantFormulaire = $portlet->getRandomId ()."_pos_".$position;
	$params = new CopixParameterHandler();
	$mediaOptions = array();
	if(isset($media)){
		$mediaOptions = $portlet->getPortletElementAt($position)->getOptions ();
		$params->setParams($mediaOptions);
	}
    //si on veut afficher un media de la portlet
	if(!$justAddMedia){
		//si on veut afficher un media existant, on l'insere dans une div qui l'identifie, sinon pour un nouvel media ce sera fait par l'appel javascript
		if(!$newMediaVide){?>
<div id="div_<?php echo $identifiantFormulaire;?>">
<?php 	} 

	//div des options 
	echo CopixZone::process ('medias|mediaOptionMenu', array('options'=>$mediaOptions, 'identifiantFormulaire'=>$identifiantFormulaire, 'portlet_id'=>$portlet->getRandomId (), 'position'=>$position, 'mediaType' => $portlet->getMediaType()));

	//formulaire de choix de media ?>
	<form id="form_<?php echo $identifiantFormulaire;?>" class="headForm">
		<input type="hidden" id="type_media_<?php echo $identifiantFormulaire; ?>" value="<?php echo $mediaType;?>" />
		<label style="font-weight:bold;">Choix d'un média <?php echo ucfirst($mediaType);?> : </label>
		<?php
		$selected = (isset($media)) ? $media->public_id_hei : '';
		echo CopixZone::process ('heading|headingelement/headingelementchooser', array('arTypes'=>array($mediaType), 'mode'=>ZoneHeadingElementChooser::MEDIA_CHOOSER_MOD, 'selectedIndex'=>$selected, 'inputElement'=>'id_media_'.$identifiantFormulaire, 'identifiantFormulaire'=>$identifiantFormulaire));
		?>	
		<input type="hidden" id="position_media_<?php echo $identifiantFormulaire; ?>" name="position_media_<?php echo $identifiantFormulaire; ?>" value="<?php echo $position; ?>" />
	</form>
	
	<?php //div d'affichage du contenu du media selectionné ?>
	<div id="media_<?php echo $identifiantFormulaire;?>" style="margin-bottom:10px;">
	<?php		
		//affichage du media
		if(isset($media)){
			echo CopixZone::process ('medias|mediaformview', array('options'=>$mediaOptions, 'media'=>$media, 'mediaType'=> $mediaType, 'identifiantFormulaire' => $identifiantFormulaire));
		}
	?>
	</div>
		<?php 
		//pour le media existant, on ferme la div
		if(!$newMediaVide){ ?>
</div>
		<?php
		}
		CopixHTMLHeader::addJSDOMReadyCode("
		$('id_media_".$identifiantFormulaire."').addEvent('change', function(){updateMedia('".$identifiantFormulaire."', '".$portlet->getRandomId ()."', '"._request('editId')."');});
		");
	}
	//si on ne veut afficher que le bouton ajouter
	else{ 
		CopixHTMLHeader::addJSLink(_resource('medias|js/tools.js'));
	?>
	<div id="addMedia_<?php echo $identifiantFormulaire;?>">
		<input type="submit" value="ajouter un média" onclick="addMedia('<?php echo $identifiantFormulaire;?>', '<?php echo $portlet->getRandomId ();?>', '<?php echo _request('editId');?>');"/> 
		<input type="hidden" id="position_<?php echo $identifiantFormulaire;?>" value="1"/>
	</div> 
	<?php
	}
?>