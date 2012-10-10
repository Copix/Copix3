<?php

CopixHTMLHeader::addCSSLink(_resource('heading|css/headingelementchooser.css'));
CopixHTMLHeader::addCSSLink(_resource('heading|css/mycmscssconfig.css'));
CopixHTMLHeader::addJSLink(_resource('heading|js/elementChooser.js'));
_etag('mootools', array('plugins'=>'mootree;overlayfix;observer;autocompleter;autocompleter.request'));
ob_start();
$tabs = array(
	$ppo->identifiantFormulaire.'fromcms'=>"Depuis la bibliothèque du CMS",
	$ppo->identifiantFormulaire.'fromcomputer'=>"Depuis votre ordinateur"
);
_eTag ('tabgroup', array ('tabs' => $tabs, 'default' => $ppo->identifiantFormulaire.'fromcms'));
?>
<div id="<?php echo $ppo->identifiantFormulaire; ?>fromcms">
	<h2>Ajouter un document depuis la bibliothèque du CMS</h2>
	<div class="portalGeneralMenu">
		<ul class="portalGeneralMenuList">
			<li>
				<img style="vertical-align: middle;" src="<?php echo _resource("img/tools/loupe.png"); ?>" title="Recherche rapide"/> 
				<input type="text" name="quickSearch" id="quickSearch<?php echo $ppo->identifiantFormulaire; ?>" />
			</li>
			<li>
				Selectionner 
				<a href="javascript:void(0);" onclick="selectAll('<?php echo $ppo->identifiantFormulaire; ?>')" >tout</a> /
				<a href="javascript:void(0);" onclick="unSelectAll('<?php echo $ppo->identifiantFormulaire; ?>')" >rien</a>
			</li>
			<li>
				<?php echo CopixZone::process('heading|headingbookmarks', array('treeId'=>$ppo->identifiantFormulaire, 'filters'=>array('document'), 'public_id'=>$ppo->selectedIndex ? $ppo->selectedIndex : _request('heading', CopixSession::get('heading', _request('editId'))))); ?>
			</li>
			<li>		
				<?php 
				ob_start();
				?>
				<table class="pageUpdateTable">
					<tr><td><a href="javascript:void(0);" onclick="changeView('<?php echo $ppo->identifiantFormulaire; ?>', '<?php echo HeadingElementChooserServices::AFFICHAGE_MINIATURES; ?>', $('selectedTreeElement<?php echo $ppo->identifiantFormulaire; ?>').value, 'document');" >Miniatures</a><br /></td></tr>
					<tr><td><a href="javascript:void(0);" onclick="changeView('<?php echo $ppo->identifiantFormulaire; ?>', '<?php echo HeadingElementChooserServices::AFFICHAGE_DETAIL; ?>', $('selectedTreeElement<?php echo $ppo->identifiantFormulaire; ?>').value, 'document');" >Détail</a></td></tr>
				</table>
				<?php
				$content = ob_get_contents();
				ob_end_clean (); 					
				_eTag('popupinformation', array('text'=>"Affichage",'img'=>_resource('heading|img/view.png'), 'handler'=>'clickdelay'), $content); ?>			 
			</li>
		</ul>
		<div id="divElementChooserTreeLoad<?php echo $ppo->identifiantFormulaire; ?>" class="loading_img">
			<img src="<?php echo _resource ('img/tools/load.gif'); ?>" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="elementchoosercontenttree" id="elementChooserTree<?php echo $ppo->identifiantFormulaire; ?>">
	</div>
	<div class="SplitterBarV" id="splitter<?php echo $ppo->identifiantFormulaire; ?>"/></div>
	<div class="elementchoosercontentfiles" id="elementchoosercontentfiles<?php echo $ppo->identifiantFormulaire; ?>">
	<?php 
	if ($ppo->selectedIndex){
		if (empty($ppo->arElementsPreview)){
			echo "Pas de documents dans cette rubrique.";
		} else {
			foreach ($ppo->arElementsPreview as $document){ 
				if (CopixAuth::getCurrentUser()->testCredential("basic:admin") || HeadingElementCredentials::canWrite($document->public_id_hei)){
				$documentInfo = _ioClass('document|documentservices')->getByPublicId($document->public_id_hei);	?>
				<div style="line-height: normal;" class="<?php echo sizeof($ppo->arElementsPreview) == 1 ? 'elementchooserfileselected elementchooserfileselectedstate' : 'elementchooserfile elementchooserfilenoselectedstate'; ?>" libelle="<?php echo $document->caption_hei; ?>" pih="<?php echo $document->public_id_hei; ?>" title="<?php echo $document->caption_hei; ?>">
					<?php $extension = pathinfo($documentInfo->file_document, PATHINFO_EXTENSION); ?>
					<img class="docelementchooserfile" src="<?php echo _resource('heading|'.(array_key_exists($extension, $ppo->arElementsPreview) ? $ppo->arDocIcons[$extension] : 'img/docicons/unknow.png')); ?>" />
					<span style="font-size: 0.9em;line-height: 10px;">
						<?php 
						echo strlen($document->caption_hei) > 15 ? substr($document->caption_hei, 0 ,12) . '...' : $document->caption_hei; 
						?>
					</span>
				</div>
		<?php 	} 
			}
		}
	} else {
		echo "Selectionnez une rubrique ou un élément";
	}
	?>
	</div>
	<div class="elementchooserstate">
		<input type='hidden' value='<?php echo $ppo->selectedIndex; ?>' name="selectedTreeElement<?php echo $ppo->identifiantFormulaire; ?>" id="selectedTreeElement<?php echo $ppo->identifiantFormulaire; ?>" />	
		<input type='hidden' value='<?php echo CopixUserPreferences::get('heading|documentChooser', 0); ?>' name="elementchoosercontentfilesview<?php echo $ppo->identifiantFormulaire; ?>" id="elementchoosercontentfilesview<?php echo $ppo->identifiantFormulaire; ?>" />	
		<span id="stateelementchooser<?php echo $ppo->identifiantFormulaire; ?>"></span>
		<input type="submit" id="filechoosersubmit<?php echo $ppo->identifiantFormulaire; ?>" value="Valider" onclick="addElements('<?php echo $ppo->identifiantFormulaire; ?>', '<?php echo $ppo->id ; ?>', <?php echo $ppo->multipleSelect ? 'true' : 'false'; ?>);$('copixwindowdocumentchooser<?php echo $ppo->identifiantFormulaire; ?>').fireEvent('close');"/>
		<?php if ($ppo->selectHeading){?>
		<input type="button" id="documentchoosersubmitheading<?php echo $ppo->identifiantFormulaire; ?>" value="Utiliser la rubrique" onclick="addHeading('<?php echo $ppo->identifiantFormulaire; ?>', '<?php echo $ppo->id ; ?>'); $('copixwindowdocumentchooser<?php echo $ppo->identifiantFormulaire; ?>').fireEvent('close');" <?php echo (isset($selectedElement) && $selectedElement->type_hei != "heading" ? "disabled='disabled'" : ''); ?>/>
		<?php } ?>
	</div>
</div>
<div id="<?php echo $ppo->identifiantFormulaire; ?>fromcomputer">
	<h2>Ajouter un document depuis votre ordinateur</h2>
	<iframe style="border:none;" width="740px" height="500px" src="<?php echo _url("heading|element|prepareCreate", array('type'=>"document", 'heading'=>_request('heading', CopixSession::get('heading', _request('editId'))), 'then'=>_url('document|upload|confirmDocumentChooser', array('heading'=>CopixSession::get('heading', _request('editId')))))); ?>"></iframe>
</div>
<?php
if(!empty($ppo->arHeadingElements)){
	if($ppo->selectedIndex){
		try{
			$element = _ioClass ('headingelementinformationservices')->get($ppo->selectedIndex ? $ppo->selectedIndex : _request('heading'));
			$ppo->selectedIndexPath = explode('-', $element->hierarchy_hei);
		} catch (HeadingElementInformationNotFoundException $e){
			_log ($e->getMessage(), "errors", CopixLog::WARNING);
		}		
	}
}

$content = ob_get_contents ();
ob_end_clean ();

$selectedCaption = '';
if($ppo->selectedIndex){
	try{
		$element = _ioClass ('headingelementinformationservices')->get($ppo->selectedIndex);
		$selectedCaption = $element->caption_hei;
	}
	catch (CopixException $e){
		$selectedCaption = "!! ELEMENT INTROUVABLE !!";
	}
} else if (!$ppo->selectedIndexExists) {
	$selectedCaption = '<span style="color: red">[Document "' . $ppo->askedSelectedIndex . '" introuvable]</span>';
}
?>
<span style="color:black;<?php echo !$ppo->showSelection ? 'display:none;' : ''; ?>" id="libelleElement<?php echo $ppo->identifiantFormulaire; ?>"><?php echo $selectedCaption; ?></span>
<a id="clicker<?php echo $ppo->identifiantFormulaire; ?>" href="javascript:void(0);"><img src="<?php echo $ppo->img ? $ppo->img : _resource ('img/tools/open.png'); ?>" /></a>
<?php
_eTag('copixwindow', array('id'=>'copixwindowdocumentchooser'.$ppo->identifiantFormulaire, 'fixed' => 1, 'clicker'=>'clicker'.$ppo->identifiantFormulaire, 'title'=>'Ajouter un document', 'modal'=>true), $content);
//_etag ('popupinformation', array ('id'=>'elementChooser'.$ppo->identifiantFormulaire, 'divclass'=>'elementchooser', 'img'=>$ppo->img ? $ppo->img : _resource ('img/tools/open.png'), 'handler'=>'clickdelay'), $content);
if ($ppo->showSelection) {
?>

<img id="deleteElement<?php echo $ppo->identifiantFormulaire; ?>" width="14px" src="<?php echo _resource ('img/tools/delete.png'); ?>" title="supprimer" alt="supprimer" style="cursor:pointer;display:<?php echo ($ppo->selectedIndex != null || !$ppo->selectedIndexExists) ? 'inline' : 'none'; ?>;"/>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
		$('deleteElement".$ppo->identifiantFormulaire."').addEvent('click', function(){
			chooseElement('', '', '".$ppo->identifiantFormulaire."', '".$ppo->id."', '');
			tree" . $ppo->identifiantFormulaire . ".select(tree" . $ppo->identifiantFormulaire . ".root);
		});
	");
}
?>
<input type="hidden" id="<?php echo $ppo->id ; ?>" name="<?php echo $ppo->inputElement; ?>" value="<?php echo $ppo->selectedIndex; ?>" />
<input type="hidden" id="selectedIndex<?php echo $ppo->identifiantFormulaire; ?>"  value="<?php echo $ppo->selectedIndex; ?>" />
<script type="text/javascript">
	var tree<?php echo $ppo->identifiantFormulaire; ?> = null;
</script>

<?php
CopixHTMLHeader::addJSDOMReadyCode("
		new Autocompleter.Request.JSON('quickSearch".$ppo->identifiantFormulaire."', '"._url('heading|ajax|QuickSearchAutoCompleter', array('filter'=>implode(":",$ppo->filters)))."', {
	        'postVar': 'search',
	        'selectMode' : false,
	        'onSelection' : function(el){
	        	$$('.mooTree_search').each(function(el){
	        		el.removeClass ('mooTree_search');
	        	});
	        	var informations = el.value.split(' - ');
	        	startLoading('elementChooserTree', '".$ppo->identifiantFormulaire."');
	        	var AjaxRequest = new Request.HTML({
				    url: Copix.getActionURL ('heading|ajax|selectNode', {'caption_hei':informations[0], 'type_hei':informations[1], 'formId':'".$ppo->identifiantFormulaire."', 'filter':'".implode(":",$ppo->filters)."'}),
					evalScripts: true,
					update:'divTreeConstruct".$ppo->identifiantFormulaire."'
				}).send ();
	        }
	    });

		if($('li_" . $ppo->identifiantFormulaire . "_" . $ppo->selectedIndex . "')){
			$('li_" . $ppo->identifiantFormulaire . "_" . $ppo->selectedIndex . "').addClass('headingelementchooser_selected');
		}
		
		tree" . $ppo->identifiantFormulaire . " = new MooTreeControl({
			div: 'elementChooserTree" . $ppo->identifiantFormulaire . "',
			mode: 'folders',
			theme : '" . _url(). "js/mootools/img/mootree.gif',
			grid: true,
			onSelect: function(node, state) {
				if (state) {
					if($('documentchoosersubmitheading".$ppo->identifiantFormulaire."')){
						$('documentchoosersubmitheading".$ppo->identifiantFormulaire."').disabled = (node.data.type_hei == 'heading' ? false : true);
					}
					filepreview(node.data.public_id_hei, '".$ppo->identifiantFormulaire."', 'document');
					$('selectedTreeElement" . $ppo->identifiantFormulaire . "').value = node.data.public_id_hei;
					$('selectedTreeElement" . $ppo->identifiantFormulaire . "').set('rel',node.data.caption_hei);
				}
			}
		},{
			text: 'Racine du site',
			open: true,
			data :{'type_hei':'heading','public_id_hei':0}
		});
		
		arDocumentTreeId.push('" . $ppo->identifiantFormulaire . "'); 
		var elements = $('elementchoosercontentfiles".$ppo->identifiantFormulaire."').getElements ('.elementchooserfile').extend($('elementchoosercontentfiles".$ppo->identifiantFormulaire."').getElements ('.elementchooserfileselected'));
		elements.each(function(el){
			el.addEvent('click', function(event){
				selectElement(this, '".$ppo->identifiantFormulaire."', event);
			});
		});
		checkSelection('".$ppo->identifiantFormulaire."');
		tree" . $ppo->identifiantFormulaire . ".disable ();
		
		//resizable splitter
		$('elementChooserTree".$ppo->identifiantFormulaire."').makeResizable({
	    	handle: $('splitter".$ppo->identifiantFormulaire."'),
	    	modifiers:{x: 'width', y:false}, 
	    	limit: {x: [150, 600]}
		});
	");
?>
<div id="divTreeConstruct<?php echo $ppo->identifiantFormulaire; ?>" style="display:none;"></div>
<script type="text/javascript">
var mutex<?php echo $ppo->identifiantFormulaire; ?> = null;
</script>
<?php 
//aprés avoir ajouté dynamiquement un document 
CopixHTMLHeader::addJSCode("
function refreshTree".$ppo->identifiantFormulaire."(selected){
	tree".$ppo->identifiantFormulaire.".root.clear();
	
	var ajax = new Request.HTML({
		url : Copix.getActionURL ('heading|ajax|getElementChooserNode'),
		update : 'divTreeConstruct".$ppo->identifiantFormulaire."',
		evalScripts : true
	}).post({'public_id_hei':0, 'formId':'".$ppo->identifiantFormulaire."', 'selectedIndex':$('selectedIndex".$ppo->identifiantFormulaire."').value, 'searchIndex':selected, 'open':true, 'options':".$ppo->jsonOptions."});
}
");
CopixHTMLHeader::addJSDOMReadyCode ("
mutex".$ppo->identifiantFormulaire." = new Mutex (function (){
	tree".$ppo->identifiantFormulaire.".enable ();
	stopLoading('elementChooserTree', '".$ppo->identifiantFormulaire."');
});
var tree".$ppo->identifiantFormulaire."Created = false;
$('clicker".$ppo->identifiantFormulaire."').addEvent ('click', function(){
if (!tree".$ppo->identifiantFormulaire."Created){
startLoading('elementChooserTree', '".$ppo->identifiantFormulaire."');
$ppo->treeGenerator
}
mutex".$ppo->identifiantFormulaire.".execute();
tree".$ppo->identifiantFormulaire."Created = true;
});
"); ?>