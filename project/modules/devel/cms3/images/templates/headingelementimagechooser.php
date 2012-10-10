<?php
CopixHTMLHeader::addCSSLink(_resource('heading|css/headingelementchooser.css'));
CopixHTMLHeader::addCSSLink(_resource('heading|css/mycmscssconfig.css'));
CopixHTMLHeader::addJSLink(_resource('heading|js/elementChooser.js'));
CopixHTMLHeader::addJSLink(_resource('images|js/tools.js'));
_etag('mootools', array('plugins'=>'mootree;overlayfix;observer;autocompleter;autocompleter.request'));


$selectedCaption = null;
$selectedElement = null;
if($ppo->selectedIndex != null){
	try{
		$selectedElement = _ioClass ('headingelementinformationservices')->get($ppo->selectedIndex);
		$selectedCaption = ($selectedElement->type_hei == 'heading' ? 'Rubrique ' : '' ).$selectedElement->caption_hei;
	}
	catch (CopixException $e){
		$selectedCaption = "!! ELEMENT INTROUVABLE !!";
	}
} else if (!$ppo->selectedIndexExists) {
	$selectedCaption = '<span style="color: red">[Image "' . $ppo->askedSelectedIndex . '" introuvable]</span>';
}

ob_start();

$tabs = array(
	$ppo->identifiantFormulaire.'fromcms'=>"Depuis la bibliothèque du CMS",
	$ppo->identifiantFormulaire.'fromcomputer'=>"Depuis votre ordinateur"
);
_eTag ('tabgroup', array ('tabs' => $tabs, 'default' => $ppo->identifiantFormulaire.'fromcms'));
?>
<div id="<?php echo $ppo->identifiantFormulaire; ?>fromcms">
	<h2>Ajouter une image depuis la bibliothèque du CMS</h2>
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
				<?php echo CopixZone::process('heading|headingbookmarks', array('treeId'=>$ppo->identifiantFormulaire, 'filters'=>$ppo->filters, 'public_id'=>$ppo->selectedIndex ? $ppo->selectedIndex : _request('heading', CopixSession::get('heading', _request('editId'))))); ?>
			</li>
			<li>
				<?php 
				ob_start();
				?>
				<table class="pageUpdateTable">
					<tr><td><a href="javascript:void(0);" onclick="changeView('<?php echo $ppo->identifiantFormulaire; ?>', '<?php echo HeadingElementChooserServices::AFFICHAGE_MINIATURES; ?>', $('selectedTreeElement<?php echo $ppo->identifiantFormulaire; ?>').value, 'image');" >Miniatures</a><br /></td></tr>
					<tr><td><a href="javascript:void(0);" onclick="changeView('<?php echo $ppo->identifiantFormulaire; ?>', '<?php echo HeadingElementChooserServices::AFFICHAGE_DETAIL; ?>', $('selectedTreeElement<?php echo $ppo->identifiantFormulaire; ?>').value, 'image');" >Détails</a></td></tr>
				</table>
				<?php
				$content = ob_get_contents();
				ob_end_clean (); 					
				_eTag('popupinformation', array('text'=>"Affichage",'img'=>_resource('heading|img/view.png'), 'handler'=>'clickdelay'), $content); ?>			 
			</li>
			<li>
			<?php
			/*if ($ppo->newElementOption){
				//il ne devrait y en avoir qu'un
				foreach ($ppo->filters as $key=>$type){
					$group = CopixRequest::get('group');
					if($group == 'ajax'){
						if(CopixSession::get('portal|'._request ('portletId'), _request('editId'))){
							$group = 'admin';	
						}else{
							$group = 'adminportlet';	
						}
					}
					?>	
					 <li><a href="#" onclick="$('clickerQuickAddImage').fireEvent('click'); return false;createHeadingElement('<?php echo _request('editId'); ?>', '<?php echo $type; ?>', '<?php echo $group; ?>');"><img src="<?php echo _resource('img/tools/add.png'); ?>" />Ajouter une image</a></li>
					 <?php
				}
			}*/
			?>
		</ul>
		<div id="divElementChooserTreeLoad<?php echo $ppo->identifiantFormulaire; ?>" class="loading_img">
			<img src="<?php echo _resource ('img/tools/load.gif'); ?>" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="elementchoosercontenttree" id="elementChooserTree<?php echo $ppo->identifiantFormulaire; ?>"></div>
	<div class="SplitterBarV" id="splitter<?php echo $ppo->identifiantFormulaire; ?>"/></div>
	<div class="elementchoosercontentfiles" id="elementchoosercontentfiles<?php echo $ppo->identifiantFormulaire; ?>">
	<?php 
	if ($ppo->selectedIndex != null){
		if (empty($ppo->arElementsPreview)){
			echo "Pas d'images dans cette rubrique.";
		} else {
			foreach ($ppo->arElementsPreview as $image){ 
				if (CopixAuth::getCurrentUser()->testCredential("basic:admin") || HeadingElementCredentials::canWrite($image->public_id_hei)){
				?>
				<div class="<?php echo sizeof($ppo->arElementsPreview) == 1 ? 'elementchooserfileselected' : 'elementchooserfile'; ?>" libelle="<?php echo $image->caption_hei; ?>" pih="<?php echo $image->public_id_hei; ?>">
					<img class="imgelementchooserfile" title="<?php echo $image->caption_hei; ?>" src="<?php echo _url('images|imagefront|GetImage', array('id_image'=>$image->id_helt, 'width'=>75, 'height'=>75, 'keepProportions'=>true, 'resizeIfNecessary'=>true));  ?>" alt="<?php echo $image->caption_hei; ?>"/>
				</div>
		<?php	} 
			}
		}
	} else {
		echo "Selectionnez une rubrique ou un élément";
	}
	?>
	</div>
	<div class="elementchooserstate">
		<input type='hidden' value='<?php echo $ppo->selectedIndex; ?>' name="selectedTreeElement<?php echo $ppo->identifiantFormulaire; ?>" id="selectedTreeElement<?php echo $ppo->identifiantFormulaire; ?>" />	
		<input type='hidden' value='<?php echo CopixUserPreferences::get('heading|imageChooser', 0); ?>' name="elementchoosercontentfilesview<?php echo $ppo->identifiantFormulaire; ?>" id="elementchoosercontentfilesview<?php echo $ppo->identifiantFormulaire; ?>" />
		<span id="stateelementchooser<?php echo $ppo->identifiantFormulaire; ?>"></span>
		<input type="button" id="filechoosersubmit<?php echo $ppo->identifiantFormulaire; ?>" value="Valider la selection" onclick="addElements('<?php echo $ppo->identifiantFormulaire; ?>', '<?php echo $ppo->id ; ?>', <?php echo $ppo->multipleSelect ? 'true' : 'false'; ?>); $('copixwindowimagechooser<?php echo $ppo->identifiantFormulaire; ?>').fireEvent('close');"/>
		<?php if ($ppo->selectHeading){?>
		<input type="button" id="imagechoosersubmitheading<?php echo $ppo->identifiantFormulaire; ?>" value="Utiliser la rubrique" onclick="addHeading('<?php echo $ppo->identifiantFormulaire; ?>', '<?php echo $ppo->id ; ?>'); $('copixwindowimagechooser<?php echo $ppo->identifiantFormulaire; ?>').fireEvent('close');" <?php echo (isset($selectedElement) && $selectedElement->type_hei != "heading" ? "disabled='disabled'" : ''); ?>/>
		<?php }?>
	</div>
</div>
<div id="<?php echo $ppo->identifiantFormulaire; ?>fromcomputer">
	<h2>Ajouter une image depuis votre ordinateur</h2>
	<iframe style="border:none;" width="740px" height="500px" src="<?php echo _url("heading|element|prepareCreate", array('type'=>"image", 'heading'=>_request('heading', CopixSession::get('heading', _request('editId'))), 'then'=>_url('images|upload|confirmImageChooser', array('heading'=>CopixSession::get('heading', _request('editId')))))); ?>"></iframe>
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
?>
<span class="imageCaptionElementChooser" style="color:black;<?php echo !$ppo->showSelection ? 'display:none;' : ''; ?>" id="libelleElement<?php echo $ppo->identifiantFormulaire; ?>"><?php echo $selectedCaption; ?></span>
<a class="fileChooserImage" id="clicker<?php echo $ppo->identifiantFormulaire; ?>" href="javascript:void(0);"><img src="<?php echo $ppo->img ? $ppo->img : _resource ('img/tools/open.png'); ?>" /></a>
<?php
_eTag('copixwindow', array('id'=>'copixwindowimagechooser'.$ppo->identifiantFormulaire, 'fixed' => 1, 'class'=>'copixwindow copixwindowimagechooser', 'clicker'=>'clicker'.$ppo->identifiantFormulaire, 'title'=>'Ajouter une image', 'modal'=>true), $content);
//_etag ('popupinformation', array ('id'=>'elementChooser'.$ppo->identifiantFormulaire, 'divclass'=>'elementchooser', 'img'=>$ppo->img ? $ppo->img : _resource ('img/tools/open.png'), 'handler'=>'clickdelay'), $content);
if ($ppo->showSelection) {
	?>
	<img class="deleteElement" id="deleteElement<?php echo $ppo->identifiantFormulaire; ?>" width="14px" src="<?php echo _resource ('img/tools/delete.png'); ?>" title="supprimer" alt="supprimer" style="cursor:pointer;display:<?php echo ($ppo->selectedIndex != null || !$ppo->selectedIndexExists) ? 'inline' : 'none'; ?>;"/>
	<?php
	CopixHTMLHeader::addJSDOMReadyCode("
		$('deleteElement".$ppo->identifiantFormulaire."').addEvent('click', function(){
			chooseElement('', '', '".$ppo->identifiantFormulaire."', '".$ppo->id."', '');
			tree" . $ppo->identifiantFormulaire . ".select(tree" . $ppo->identifiantFormulaire . ".root);
		});
	");
	}
?>

<input type="hidden" id="<?php echo $ppo->id ; ?>" name="<?php echo $ppo->name; ?>" value="<?php echo $ppo->selectedIndex; ?>" />
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
					if($('imagechoosersubmitheading".$ppo->identifiantFormulaire."')){
						$('imagechoosersubmitheading".$ppo->identifiantFormulaire."').disabled = (node.data.type_hei == 'heading' ? false : true);
					}
					filepreview(node.data.public_id_hei, '".$ppo->identifiantFormulaire."', 'image');
					$('selectedTreeElement" . $ppo->identifiantFormulaire . "').value = node.data.public_id_hei;
					$('selectedTreeElement" . $ppo->identifiantFormulaire . "').set('rel',node.data.caption_hei);
				}
			}
		},{
			text: 'Racine du site',
			open: true,
			data :{'type_hei':'heading','public_id_hei':0,'caption_hei':'Racine du site'}
		});
		
		arImageTreeId.push('" . $ppo->identifiantFormulaire . "'); 
		var elements = $('elementchoosercontentfiles".$ppo->identifiantFormulaire."').getElements ('.elementchooserfile').extend($('elementchoosercontentfiles".$ppo->identifiantFormulaire."').getElements ('.elementchooserfileselected'));
		elements.each(function(el){
			el.addEvent('click', function(event){
				selectElement(this, '".$ppo->identifiantFormulaire."', event);
			});
		});
		
		checkSelection('".$ppo->identifiantFormulaire."');		
		tree" . $ppo->identifiantFormulaire . ".disable ();
		//$('elementChooserTree" . $ppo->identifiantFormulaire . "').setStyle('display', 'none');

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