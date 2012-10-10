<?php
//initialization des variables
$identifiantFormulaire = $portlet->getRandomId ()."_pos_".$position;
$documentOptions = array ();
if (isset($document)) {
    $documentOptions = $portlet->getPortletElementAt($position)->getOptions ();
}

//si on veut afficher un document de la portlet
if (!$justAddDocument) {
    //si on veut afficher un document existant, on l'insere dans une div qui l'identifie, sinon pour un nouvel document ce sera fait par l'appel javascript
    if (!$newDocumentVide) {?>
<div id="div_<?php echo $identifiantFormulaire;?>">
            <?php 	} ?>
	<div class="fileBloc fileDoc">
		<div class="thumbDocument" id="document_<?php echo $identifiantFormulaire;?>">
		<?php		
			//affichage du image
			if(isset($document)){
				if ($document->type_hei == "heading"){
					$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($document->public_id_hei, 'document');
					$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);
					foreach ($children as $child){
						$listeDocs[] = _ioClass('document|documentservices')->getByPublicId ($child->public_id_hei);
					} 				
				} else {
					$listeDocs[] = $document;			
				}
				$tpl = new CopixTpl();
				$tpl->assign('listeDocs', $listeDocs);
				$tpl->assign('arDocIcons', ZoneHeadingElementChooser::getArDocIcons());
				$tpl->assign('identifiantFormulaire', $identifiantFormulaire);
				echo $tpl->fetch('documentformadminview.php');
			} else {
				echo "<a href='javascript:void(0)' id='docClicker".$identifiantFormulaire."' ><img id='docChoix".$portlet->getRandomId ()."' src='"._resource('document|img/choisirdoc.png')."' /></a>";	
				CopixHTMLHeader::addJSDOMReadyCode("$('docClicker".$identifiantFormulaire."').addEvent('click', function(){ $('clicker".$identifiantFormulaire."').fireEvent('click');});");
			}
		?>
		</div>
		<div class="optionsFile">
			<?php //div des options 
		
			//options
			echo CopixZone::process ('document|documentOptionMenu', array('options'=>$documentOptions,
		        'identifiantFormulaire'=>$identifiantFormulaire,
		        'position'=>$position,
		        'portlet_id'=>$portlet->getRandomId ()));
			
			$selected = (isset($document)) ? $document->public_id_hei : '';
			
			echo CopixZone::process ('heading|headingelement/headingelementchooser', array('arTypes'=>array('document'), 'mode'=>ZoneHeadingElementChooser::DOCUMENT_CHOOSER_MOD, 'selectedIndex'=>$selected, 'inputElement'=>'id_document_'.$identifiantFormulaire, 'identifiantFormulaire'=>$identifiantFormulaire, 'multipleSelect'=>true));
			CopixHTMLHeader::addJSDOMReadyCode("
				$('libelleElement".$identifiantFormulaire."').setStyle('display','none');
				$('clicker".$identifiantFormulaire."').setStyle('display','none');");
		
			?>
		    <form id="form_<?php echo $identifiantFormulaire;?>" class="headForm">
		        <input type="hidden" id="position_doc_<?php echo $identifiantFormulaire; ?>" name="position_doc_<?php echo $identifiantFormulaire; ?>" value="<?php echo $position; ?>" />
		    </form>
		</div>
	</div>
        <?php
        //pour le document existant, on ferme la div
        if (!$newDocumentVide) { ?>
</div>
        <?php
    }
    CopixHTMLHeader::addJSDOMReadyCode ("
		$('id_document_".$identifiantFormulaire."').addEvent('change', function(){updateDocument('".$identifiantFormulaire."', '".$portlet->getRandomId()."', '"._request('editId')."');});
		");
} else {
    //si on ne veut afficher que le bouton ajouter
    CopixHTMLHeader::addJSLink(_resource('document|js/tools.js'));
    ?>
<div id="addDocument_<?php echo $portlet->getRandomId ();?>">
	<div style="clear:both;"></div>
    <input type="hidden" id="position_<?php echo $portlet->getRandomId ();?>" value="<?php echo $position; ?>"/>
</div> 
    <?php } ?>

<script>
    function addElements<?php echo $identifiantFormulaire;?> (mutex){
        var selectedElements = getSelectedElements ('<?php echo $identifiantFormulaire;?>');
        selectedElements.each(function (el){
            if(el.get('th') != 'heading'){
                addDocument('<?php echo $identifiantFormulaire;?>', '<?php echo $portlet->getRandomId ();?>', '<?php echo _request('editId');?>', el.get('pih'), mutex);
            }
        });
    }
</script>