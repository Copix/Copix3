<?php
CopixHTMLHeader::addCSSLink(_resource ('heading|css/headingelementchooser.css'));
CopixHTMLHeader::addCSSLink(_resource ('heading|css/cms.css'));
CopixHTMLHeader::addCSSLink(_resource ('heading|css/mycmscssconfig.css'));
CopixHTMLHeader::addJSLink(_resource ('heading|js/elementChooser.js'));
_etag('mootools', array('plugins'=>'mootree;overlayfix;observer;autocompleter;autocompleter.request'));

ob_start();
?>
<div class="portalGeneralMenu">
	<ul class="portalGeneralMenuList">
		<li>
			<label for="quickSearch<?php echo $ppo->identifiantFormulaire; ?>"><img style="vertical-align: middle;" src="<?php echo _resource("img/tools/loupe.png"); ?>" title="Recherche rapide"/></label>
			<input type="text" name="quickSearch" id="quickSearch<?php echo $ppo->identifiantFormulaire; ?>" />
		</li>
		<?php if (!(empty($ppo->filters) || sizeof($ppo->filters) == 1)){ ?>
		<li>
		<label for="typeFilter<?php echo $ppo->identifiantFormulaire; ?>">Filtrer par type</label>
		<select name="typeFilter" id="typeFilter<?php echo $ppo->identifiantFormulaire; ?>" >
			<option value="all">--Pas de filtre--</option>
			<?php
			foreach ($ppo->filters as $key=>$type){
				echo '<option value="' . $key . '">' . $type . '</option>';
			}
			?>
		</select>
		</li>
		<?php
		}
		if ($ppo->newElementOption){
			//il ne devrait y en avoir qu'un
			foreach ($ppo->filters as $key=>$type){	
				?>	
				 <li><a href="#" onclick="createHeadingElement('<?php echo _request('editId'); ?>', '<?php echo $type; ?>', '<?php echo CopixRequest::get('group'); ?>');"><img src="<?php echo _resource('img/tools/add.png'); ?>" />Créer un élément <?php echo $type; ?></a></li>
				 <?php
			}
		}
		?>
		<li>
			<?php echo CopixZone::process('heading|HeadingBookmarks', array('treeId'=>$ppo->identifiantFormulaire, 'filters'=>$ppo->filters, 'public_id'=>$ppo->selectedIndex ? $ppo->selectedIndex : _request('heading', CopixSession::get('heading', _request('editId'))))); ?>
		</li>
	</ul>
	<div id="divElementChooserTreeLoad<?php echo $ppo->identifiantFormulaire; ?>" class="loading_img">
		<img src="<?php echo _resource ('img/tools/load.gif'); ?>" />
	</div>
	<div class="clear"></div>
</div>
<?php
if (!empty($ppo->arHeadingElements)){
	
	if ($ppo->selectedIndex){
		try {
			$element = _ioClass ('headingelementinformationservices')->get($ppo->selectedIndex ? $ppo->selectedIndex : _request('heading'));
			$ppo->selectedIndexPath = explode('-', $element->hierarchy_hei);
		} catch (HeadingElementInformationNotFoundException $e){
			_log ($e->getMessage(), "errors", CopixLog::WARNING);
		}		
	}
}
?>
<div class="elementChooser" id="elementChooserTree<?php echo $ppo->identifiantFormulaire; ?>"></div>
<?php
$content = ob_get_contents ();
ob_end_clean ();

$selectedCaption = '';
if ($ppo->selectedIndex != null){
	try{
		$element = _ioClass ('headingelementinformationservices')->get($ppo->selectedIndex);
		
		$selectedCaption = $element->caption_hei;
	} catch (CopixException $e) {
		$selectedCaption = "!! ELEMENT INTROUVABLE !!";
	}
} else if (!$ppo->selectedIndexExists) {
	$selectedCaption = '<span style="color: red">[Elément "' . $ppo->askedSelectedIndex . '" introuvable]</span>';
}
if ($ppo->copixwindow){
	?>
	<span style="color:black;<?php echo !$ppo->showSelection ? 'display:none;' : ''; ?>" id="libelleElement<?php echo $ppo->identifiantFormulaire; ?>"><?php echo $selectedCaption; ?></span>
	<a id="clicker<?php echo $ppo->identifiantFormulaire; ?>" href="javascript:void(0);"><img src="<?php echo $ppo->img ? $ppo->img : _resource ('img/tools/open.png'); ?>" /> <?php echo $ppo->clickerCaption ?></a>
	<?php
	_eTag ('copixwindow', array (
		'id' => 'copixwindowelementchooser' . $ppo->identifiantFormulaire,
		'class' => 'copixwindow elementchooserwindow',
		'clicker' => 'clicker' . $ppo->identifiantFormulaire,
		'fixed' => $ppo->fixed,
		'title' => 'Parcourir',
		'canDrag' => $ppo->canDrag,
		'min-width' => '400px'
	), $content);
} else {
	echo $content;
}
?>

<?php if ($ppo->showSelection && $ppo->copixwindow){ ?>
<img id="deleteElement<?php echo $ppo->identifiantFormulaire; ?>" width="14px" src="<?php echo _resource ('img/tools/delete.png'); ?>" title="supprimer" alt="supprimer" style="cursor:pointer;display:<?php echo ($ppo->selectedIndex != null || !$ppo->selectedIndexExists) ? 'inline' : 'none'; ?>;"/>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
		$('deleteElement".$ppo->identifiantFormulaire."').addEvent('click', function(){
			chooseElement('', '', '".$ppo->identifiantFormulaire."', '".$ppo->id."', '');
		});
	");
} ?>
<input type="hidden" id="<?php echo $ppo->id; ?>" name="<?php echo $ppo->inputElement; ?>" value="<?php echo $ppo->selectedIndex; ?>" />
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

	    if ($('typeFilter".$ppo->identifiantFormulaire."')){
			$('typeFilter".$ppo->identifiantFormulaire."').addEvent('change', function(){
				typeFilter(tree" . $ppo->identifiantFormulaire . ", '".$ppo->identifiantFormulaire."', $('typeFilter".$ppo->identifiantFormulaire."').value);
			});
		}
		
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
					" . ($ppo->clickMod ? 
					"document.location.href = node.data.type_hei == 'heading' ? Copix.getActionURL('heading|element|', {'heading':node.data.public_id_hei}) : Copix.getActionURL('heading|element|prepareexploreredit', {'public_id_hei':node.data.public_id_hei, 'elementChooser':true});" : 
					"if (node.data.type_hei != 'heading' || " . ($ppo->linkOnHeading ? 'true' : 'false'). ") {chooseElement(node.text, node.data.public_id_hei, '".$ppo->identifiantFormulaire."', '".$ppo->id."', node.data.type_hei); $('copixwindowelementchooser".$ppo->identifiantFormulaire."').fireEvent('close');}") ."
				}
			}
		},{
			text: 'Racine du site',
			open: true,
			data :{'type_hei':'heading','public_id_hei':0}
		});
		tree" . $ppo->identifiantFormulaire . ".disable ();
	");
?>
<div id="divTreeConstruct<?php echo $ppo->identifiantFormulaire; ?>" style="display:none;"></div>
<script type="text/javascript">
var mutex<?php echo $ppo->identifiantFormulaire; ?> = null;
</script>
<?php 
CopixHTMLHeader::addJSDOMReadyCode ("
mutex".$ppo->identifiantFormulaire." = new Mutex (function (){
	tree".$ppo->identifiantFormulaire.".enable ();
	stopLoading('elementChooserTree', '".$ppo->identifiantFormulaire."');
});
");
if ($ppo->copixwindow){
	CopixHTMLHeader::addJSDOMReadyCode ("	
	var tree".$ppo->identifiantFormulaire."Created = false;
	$('clicker".$ppo->identifiantFormulaire."').addEvent ('click', function(){
	if (!tree".$ppo->identifiantFormulaire."Created){
	$ppo->treeGenerator
	}
	mutex".$ppo->identifiantFormulaire.".execute();
	tree".$ppo->identifiantFormulaire."Created = true;
	});
	"); 
} else {
	CopixHTMLHeader::addJSDOMReadyCode ("
	$ppo->treeGenerator
	mutex".$ppo->identifiantFormulaire.".execute();
	");
}
?>