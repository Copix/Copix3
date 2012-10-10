<?php
CopixHTMLHeader::addCSSLink (_resource ('heading|css/headingadmin.css'));
CopixHTMLHeader::addCSSLink (_resource ('heading|css/mycmscssconfig.css'));

_tag ('mootools', array ('plugins'=>'copixformobserver;eyecandybar;mootblsort'));

// pour ne pas avoir à passer trop de variables en paramètre aux fonctions, on passe par global
$GLOBALS['prefDisplay'] = $ppo->prefDisplay;
$GLOBALS['prefThumbHeight'] = $ppo->prefThumbHeight;
$GLOBALS['prefThumbWidth'] = $ppo->prefThumbWidth;
$GLOBALS['toPublish'] = array();

/**
 * Création des classes CSS pour l'élément en fonction des opérations possible sur ce dernier
 *
 * @param CopixPpo $pOperations
 * @return string
 */
function _headingElementInformationCSSClass ($pOperations){
	$cssStyles = array ('elementCheckbox');
	foreach (array ('edit', 'publish', 'delete', 'archive', 'move', 'cut', 'copy') as $action){
		if ($pOperations->$action){
			$cssStyles[] = 'can_'.$action;
		}
	}
	return implode (' ', $cssStyles);
}

/**
 * Récupère le statut "principal" qu'il convient d'afficher
 *
 * @param array $pArStatus
 * @return le tableau des status a afficher (ne contiendra que des status valide pour l'élément passé en paramètre)
 */
function getStatusDisplayOrderFor ($pArStatus){
	$arPositions = array ();

	//S'il existe une version publiée, c'est facile, c'est elle qu'il faut afficher
	if (isset ($pArStatus[HeadingElementStatus::PUBLISHED])){
		$arPositions[] = HeadingElementStatus::PUBLISHED;
		if (isset ($pArStatus[HeadingElementStatus::PLANNED])){
			$arPositions[] = HeadingElementStatus::PLANNED;
			
		}
		if (isset ($pArStatus[HeadingElementStatus::DRAFT])){
			$arPositions[] = HeadingElementStatus::DRAFT;
		}
		return $arPositions; 		
	}
	
	//Ensuite on prend l'archive
	if (isset ($pArStatus[HeadingElementStatus::ARCHIVE])){
		$arPositions[] = HeadingElementStatus::ARCHIVE; 
		if (isset ($pArStatus[HeadingElementStatus::PLANNED])){
			$arPositions[] = HeadingElementStatus::PLANNED;
			
		}
		if (isset ($pArStatus[HeadingElementStatus::DRAFT])){
			$arPositions[] = HeadingElementStatus::DRAFT;
		}
		return $arPositions; 		
	}
	
	//Puis les versions planifiée
	if (isset ($pArStatus[HeadingElementStatus::PLANNED])){
		$arPositions[] = HeadingElementStatus::PLANNED; 
		if (isset ($pArStatus[HeadingElementStatus::DRAFT])){
			$arPositions[] = HeadingElementStatus::DRAFT;
		}
		return $arPositions; 		
	}
	
	//Puis enfin les brouillons
	if (isset ($pArStatus[HeadingElementStatus::DRAFT])){
		return array (HeadingElementStatus::DRAFT);
	}

	//Puis enfin les éléments supprimés
	if (isset ($pArStatus[HeadingElementStatus::DELETED])){
		return array (HeadingElementStatus::DELETED);
	}
} 

/**
 * Récupère le code HTML pour afficher un élément (ainsi que les sous status en cours)   
 * 
 * @param array $pArStatus 
 * @param unknown_type $pKind
 * @return unknown
 */
function getHeadingElementRow ($pArStatus, $pKind, $pArHeadingElementTypes, $pShowDragDrop, $pSelectedItems = array ()) {
	global $prefDisplay, $prefThumbHeight, $prefThumbWidth;

	//Gestion des couleurs alternées
	static $cycle = false;
	$cycle = !$cycle;
	$trStyle = ' style="border-bottom: 1px dotted #cccccc;" ';
	$sortable = ($pShowDragDrop) ? 'sortable' : null;
	if ($cycle){
		$trStyle .= 'class = "alternate ' . $sortable . '"';
	}else{
		$trStyle .= 'class = "' . $sortable . '"';
	}
	
	//demande d'affichage des lignes dans l'ordre voulu des status
	$htmlBuffer = '';
	foreach (getStatusDisplayOrderFor ($pArStatus) as $position=>$status){
		$htmlBuffer .= getHeadingStatusElementRow ($pArStatus[$status], $position === 0, $pKind, $trStyle, $pArHeadingElementTypes, $pSelectedItems, $pShowDragDrop);
	}
	
	if(!$htmlBuffer){
		$cycle = !$cycle;
	}
	
	return $htmlBuffer;
}

/**
 * Affichage des lignes pour un statut donné
 *
 * @param array  $pArStatus
 * @param int    $pStatus
 * @param bool   $pShowMove
 * @param string $pTypeElement
 */
function getHeadingStatusElementRow ($pElement, $pMainElement, $pTypeElement, $pTrStyle, $pArHeadingElementTypes, $pSelectedItems, $pShowDragDrop, $pLast = true) {
	global $prefDisplay, $prefThumbHeight, $prefThumbWidth, $toPublish;

	$htmlBuffer = '';
	//Si l'élément donné est un tableau, on va parcourir pour afficher la liste complète
	if (is_array ($pElement)){
		foreach ($pElement as $position=>$element){
			$htmlBuffer .= getHeadingStatusElementRow ($element, ($position === 0) && $pMainElement, $pTypeElement, $pTrStyle, $pArHeadingElementTypes, $pSelectedItems, $pShowDragDrop, $position == count($pElement)-1);
		}
	}else{
		$services = _ioClass ('HeadingElementInformationServices');
		$actions = $services->getActions ($pElement->id_helt, $pElement->type_hei);
	
		//Libellés des status
		static $arStatus = false;
		if ($arStatus === false){
			$arStatus = _class ('headingelementstatus')->getList ();
		}
		if (HeadingElementCredentials::canWrite($pElement->public_id_hei)){
			$imgSize = ($prefDisplay == 'list') ? '16px' : '32px';

			$htmlBuffer .= '<tr '.$pTrStyle.' order="'.$pElement->display_order_hei.'" id_helt="'.$pElement->id_helt.'" type="'.$pElement->type_hei.'">';
			$htmlBuffer .= '<td class="display_' . $prefDisplay .'"><input class="'._headingElementInformationCSSClass ($services->getActions ($pElement->id_helt, $pElement->type_hei)).'"';
			$htmlBuffer .= ' onchange="return showMultipleHeadingElementInformationsIn (\''.$pElement->id_helt.'\',\''.$pElement->type_hei.'\',\''.$pElement->public_id_hei.'\', \'HeadingElementInformationDiv\', this.checked)"';
	 		$htmlBuffer .= ' type="checkbox" id="checkbox_'.$pElement->id_helt.'_'.$pElement->type_hei.'" name="elements[]" value="'.$pElement->id_helt.'|'.$pElement->type_hei.'|'.$pElement->public_id_hei.'" /></td>';
	 		$htmlBuffer .= '<td class="display_' . $prefDisplay . ($pMainElement ? '' : ' childTree'). '" style="width: ' . ($imgSize + 4) . 'px">';
			if ($pMainElement){
				if ($pElement->type_hei == 'heading') {
					$htmlBuffer .= '<a href="' . _url ('heading|element|', array ('heading' => $pElement->public_id_hei)) . '">';
				} else {
					$htmlBuffer .= '<a href="'._url ('heading|element|prepareedit', array ("type" => $pElement->type_hei, "id" => $pElement->id_helt, "heading"=>$pElement->parent_heading_public_id_hei)).'">';
				}
				if ($prefDisplay == 'thumbnail') {
					$htmlBuffer .= '<div style="width: ' . $prefThumbWidth . 'px; text-align: center;';
					'height: ' . $prefThumbHeight . 'px; ">';
					if ($pElement->type_hei == 'image') {
						$htmlBuffer .= 'height: ' . $prefThumbHeight . 'px;">';
						$htmlBuffer .= '<img src="' . _url ('images|imagefront|GetImage', array ('id_image' => $pElement->id_helt, 'width' => $prefThumbWidth, 'height' => $prefThumbHeight, 'keepProportions' => true)) . '" />';
					} else {
						$htmlBuffer .= '">';
						$htmlBuffer .= '<img src="' . _resource ($pArHeadingElementTypes[$pElement->type_hei]['image']) . '"';
						$htmlBuffer .= ' width="32px" height="32px"';
						$htmlBuffer .= ' title="'.ucfirst ($pElement->type_hei).'"';
						$htmlBuffer .= ' alt="'.ucfirst ($pElement->type_hei).'" />';
					}
					$htmlBuffer .= '</div>';
				} else {
					$icon = ($prefDisplay == 'advanced') ? _resource ($pArHeadingElementTypes[$pElement->type_hei]['image']) : _resource ($pArHeadingElementTypes[$pElement->type_hei]['icon']);
					$htmlBuffer .= '<img width="' . $imgSize . '" src="'.$icon.'" ';
					$htmlBuffer .= ' title="'.ucfirst ($pElement->type_hei).'"';
					$htmlBuffer .= ' alt="'.ucfirst ($pElement->type_hei).'" />';
				}
				$htmlBuffer .= '</a>';
	 		} else {
	 			$htmlBuffer .= "<div class='" .($pLast ? 'elementLastChild' : 'elementChild') . "'></div>"; 
	 		}
			$htmlBuffer .= '</td>';
			$htmlBuffer .= '<td onclick="return showHeadingElementInformationsIn (\''.$pElement->id_helt.'\',\''.$pElement->type_hei.'\',\''.$pElement->public_id_hei.'\', \'HeadingElementInformationDiv\')" class="display_' . $prefDisplay . '" style="cursor:pointer;padding-left: 0px">';

	 		if ($pMainElement){
				$htmlBuffer .= $pElement->caption_hei ? $pElement->caption_hei : "<em>(Pas de nom)</em>";
				if ($prefDisplay == 'advanced' || $prefDisplay == 'thumbnail') {
					$htmlBuffer .= '<div class="elementInformations">';
					$htmlBuffer .= HeadingElementServices::call ($pElement->type_hei, 'getDisplayDescription', $pElement->id_helt);
					$htmlBuffer .= '</div>';
				}
				if($pElement->published_date_hei || $pElement->end_published_date_hei){
					$htmlBuffer .= "<br /><span style='font-style: italic;font-size: 0.8em;'>Publication planifiée ";
					if ($pElement->published_date_hei){
	 					$htmlBuffer .= ' au '.$pElement->published_date_hei;
	 				}
					if ($pElement->end_published_date_hei){
	 					$htmlBuffer .= ' jusqu\'au '.$pElement->end_published_date_hei;
	 				}
	 				$htmlBuffer .= "</span>";
				}
	 		}else{
	 			$htmlBuffer .= '<span style="font-style: italic;font-size: 0.8em;">';
	 			if ($pElement->status_hei == HeadingElementStatus::DRAFT){
	 				$htmlBuffer .= '<img src="'._resource('heading|img/actions/draft.png').'" /> ';
	 				$htmlBuffer .= $pElement->caption_hei . ' - En cours de modification par '.$pElement->author_caption_update_hei;
	 			}elseif ($pElement->status_hei == HeadingElementStatus::PLANNED){
	 				$htmlBuffer .= '<img src="'._resource('heading|img/actions/planned.png').'" /> ';
	 				$htmlBuffer .= $pElement->caption_hei;
	 				$htmlBuffer .= ' - Publication planifiée au '.$pElement->published_date_hei;
	 				if ($pElement->end_published_date_hei){
	 					$htmlBuffer .= ' jusqu\'au '.$pElement->end_published_date_hei;
	 				}
	 			}
	 			$htmlBuffer .= '</span>';
	 		}
			$htmlBuffer .= '</td>';

			$statusStr = ($pElement->type_hei == 'heading') ? null : '<span class="status' . $pElement->status_hei. '">' . $arStatus[$pElement->status_hei] . '</span>';
		    $htmlBuffer .= '<td style="cursor:pointer" onclick="return showHeadingElementInformationsIn (\''.$pElement->id_helt.'\',\''.$pElement->type_hei.'\',\''.$pElement->public_id_hei.'\', \'HeadingElementInformationDiv\')" class="display_' . $prefDisplay . '">' . $statusStr . '</td>';
		    
			// modification
			$htmlBuffer .= '<td class="action display_' . $prefDisplay . '">';
			if ($actions->edit) {
				$htmlBuffer .= '<a href="'._url ("heading|element|prepareEdit", array ("type" => $pElement->type_hei, "id" => $pElement->id_helt, "heading"=>$pElement->parent_heading_public_id_hei)).'">'._tag('copixicon', array('type' => 'update')).'</a>';
			}

			// publication
			$htmlBuffer .= '<td class="action display_' . $prefDisplay . '">';
			if ($actions->publish) {
				$toPublish[] = $pElement->id_helt.'|'.$pElement->type_hei;
			   	$htmlBuffer .= '<a href="'._url ("heading|element|publish", array ("heading"=>$pElement->parent_heading_public_id_hei, 'elements[]' => $pElement->id_helt.'|'.$pElement->type_hei)).'"><img title="Publier" alt="Publier" src="'._resource('heading|img/actions/publish.png').'" /></a>';
			}
			$htmlBuffer .= '</td>';

			// ordre d'affichage
			$htmlBuffer .= '<td class="action display_' . $prefDisplay . '">';
			if ($pShowDragDrop && HeadingElementCredentials::canModerate($pElement->public_id_hei)){
	 			$htmlBuffer .= '<div class="handle" title="Glissez pour changer l\'ordre de l\'élément" style="background:url('._resource('heading|img/actions/move_up_down.png').') no-repeat 0;">&nbsp;</div>';
	 		}
			$htmlBuffer .= '</td>';
			
		    $htmlBuffer .= '</tr>';
		}
	}
	return $htmlBuffer;
}
?>

<?php if (HeadingElementCredentials::canWrite ($ppo->heading)) { ?>
	<?php _eTag ('beginblock', array ('title' => 'Ajouter un élément', 'isFirst' => true)) ?>
		<div class="HeadingElementsMenu">
			<table class="HeadingElementsMenuContainer">
				<tr>
					<?php foreach ($ppo->arHeadingElementTypes as $id=>$headingElementType) { ?>
						<td class="HeadingElementsMenuItem">
							<a href="<?php echo _url ("heading|element|prepareCreate", array ('type'=>$id, 'heading'=>$ppo->heading)); ?>" style="text-decoration: none; font-weight: bold;">
								<img id="img_<?php echo $id; ?>"
									width="32px" height="32px"
									class="headingElementTypeImage"
									src="<?php echo _resource ($headingElementType['image']); ?>"
								/>
								<br />
								<?php echo $headingElementType['caption']; ?>
							</a>
						</td>
					<?php } ?>
				</tr>
			</table>
		</div >
	<?php _eTag ('endblock') ?>
<?php } ?>

<?php _eTag ('beginblock', array ('title' => 'Eléments du dossier', 'isFirst' => !HeadingElementCredentials::canWrite ($ppo->heading))) ?>
	<table style="width: 100%">
		<tr>
			<td style="vertical-align: top; width: 100%; padding-right: 10px">
				<form id="actionForm" name="actionForm" action="<?php echo _url('heading|element|formaction') ?>" method="POST">
					<input type="hidden" name="heading" value="<?php echo $ppo->heading ?>" id="heading" />
					<!--  Contenus existants -->
					<div id="arrow" class="ElementIndicator" style="display: none;"></div>

					<?php if ($ppo->canPaste) { ?>
						<div class="HeadingClipBoard">
							<table style="width: 100%">
								<tr>
									<td style="width: 1px; text-align: center">
										<a href="<?php echo _url ('heading|element|paste', array ('heading' => $ppo->heading)) ?>">
											<img src="<?php echo _resource ('heading|img/actions/paste_big.png') ?>" alt="Coller" title="Coller le contenu du presse-papier" />
											<br />
											Coller
										</a>
									</td>
									<td style="vertical-align: top; padding-left: 10px; text-align: left">
										<div class="HeadingClipBoardTitle">Presse-papier</div>
										Le presse-papier contient
										<?php
										$countElements = (count ($ppo->clipboard) <= 1) ? count ($ppo->clipboard) . ' élément' : count ($ppo->clipboard) . ' éléments';
										$html = '<table>';
										foreach ($ppo->clipboard as $record) {
											$html .= '<tr ' . _tag ('trclass', array ('id' => 'clipboard')) . '>';
											$html .= '<td style="width: 20px"><img src="' . _resource ($ppo->arHeadingElementTypes[$record->type_hei]['icon']) . '" style="vertical-align: middle" />';
											$html .= '<td>' . $record->caption_hei . '</td>';
											$html .= '</tr>';
										}
										$html .= '</table>';
										_eTag ('popupinformation', array ('displayimg' => false, 'text' => $countElements), $html);
										?>
										,
										<?php
										if ($ppo->clipboardMode == 'cut') {
											echo (count ($ppo->clipboard) <= 1) ? 'coupé' : 'coupés';
										} else {
											echo (count ($ppo->clipboard) <= 1) ? 'copié' : 'copiés';
										}
										?>
										depuis :
										<br />
										<?php echo implode (' > ', $ppo->clipboardPath) ?>
									</td>
									<td style="width: 1px;">
										<a href="<?php echo _url ('heading|element|clearClipboard', array ('heading' => $ppo->heading)) ?>">
											<img src="<?php echo _resource ('heading|img/actions/delete_big.png') ?>" alt="Vider" title="Vider le contenu du presse-papier" />
											<br />
											Vider
										</a>
									</td>
								</tr>
							</table>
						</div>
					<?php } ?>

					<table class="CopixTable cmsAdminElementTable" id="elementTable">
						<thead>
							 <tr>
								  <th style="width: 18px;text-align:center"><a onclick="checkUncheck()" title="Tout sélectionner / tout déselectionner" style="cursor: pointer"><?php echo _etag('copixicon', array ('type' => 'select', 'alt'=>"Tout sélectionner / tout déselectionner", 'title' => 'Tout sélectionner / tout déselectionner')) ?></a></th>
								  <th colspan="2">Libellé</th>
								  <th style="width: 50px">Statut</th>
								  <th colspan="3" class="last">Actions</th>
							</tr>
							<tr id="trParentHeading" class="trParentHeading <?php echo empty($ppo->selectedItems) && (CopixAuth::getCurrentUser()->testCredential("basic:admin") || $ppo->parentHeading->public_id_hei != 0) ? 'trSelectedElement' : ''; ?>" id_helt="<?php echo $ppo->parentHeading->id_helt; ?>" type="<?php echo $ppo->parentHeading->type_hei; ?>" public_id="<?php echo $ppo->parentHeading->public_id_hei; ?>">
								<td colspan="10" <?php if (CopixAuth::getCurrentUser()->testCredential("basic:admin")){ ?> style="cursor: pointer;" onclick="return showHeadingElementInformationsIn ('<?php echo $ppo->parentHeading->id_helt; ?>','heading','<?php echo $ppo->parentHeading->public_id_hei; ?>', 'HeadingElementInformationDiv')"> <?php } ?>
									<img src="<?php echo _resource("heading|img/headings.png") ?>" height="16" width="16" />
									<?php echo $ppo->parentHeading->caption_hei; ?>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$countArHeadingElementInformations = count ($ppo->arHeadingElementInformations);
							$i = 0;
							foreach ($ppo->arHeadingElementInformations as $id => $headingElementInformation) {
								$i++;
								$last  = $i == $countArHeadingElementInformations;
								$first = $i == 1;

								if ($last){	$type = 'last'; }elseif ($first){ $type = 'first'; }else{ $type = 'middle'; }
								$show = ($ppo->sort == HeadingElementInformationServices::SORT_SHOW);
								echo getHeadingElementRow ($headingElementInformation, $type, $ppo->arHeadingElementTypes, $show, $ppo->selectedItems);
							}
							if (!$countArHeadingElementInformations){
								?>
								<tr><td colspan="9">Cette rubrique ne contient pas d'éléments</td></tr>
								<?php
							}
							?>
						</tbody>
						<tfoot>
							<?php
							if ($countArHeadingElementInformations && (HeadingElementCredentials::canModerate($ppo->heading)
							|| HeadingElementCredentials::canPublish($ppo->heading)))
							{
								?>
								<tr>
									<td colspan="3">
										<!-- Actions sur les éléments séléctionnés -->
										<img src="<?php echo _resource ('|img/with_selection.png'); ?>" alt="Avec la sélection : " />
										<button name="fonction" value="publish" class="image" id="action_publish" class="image">
											<image src="<?php echo _resource('heading|img/actions/publish.png') ?>" title="Publier la sélection" alt="Publier" />
										</button>
										<button name="fonction" value="copy" class="image" id="action_copy">
											<image src="<?php echo _resource ('heading|img/actions/copy.png') ?>" title="Copier la sélection" alt="Copier"/>
										</button>
										<button name="fonction" value="cut" class="image" id="action_cut">
											<image src="<?php echo _resource ('heading|img/actions/cut.png') ?>" title="Couper la sélection" alt="Couper"/>
										</button>
										<button name="fonction" value="archive" class="image" id="action_archive">
											<image src="<?php echo _resource('heading|img/actions/archive.png') ?>" title="Archiver la sélection" alt="Archiver"/>
										</button>
										<button name="fonction" value="delete" class="image" id="action_delete" >
											<image title="Supprimer la sélection" alt="Supprimer" src="<?php echo _resource ('heading|img/actions/delete.png') ?>" />
										</button>
										
									</td>
									<td colspan="4">
										<?php if (!empty($GLOBALS['toPublish'])){?>
										<a href="<?php echo _url ("heading|element|publish", array ("heading"=>_request('heading'), 'elements' => $GLOBALS['toPublish']));  ?>"><img title="Tout publier" alt="Tout publier" src="<?php echo _resource('heading|img/actions/publish.png'); ?>" style="vertical-align: middle" /> Tout publier (<?php echo count($GLOBALS['toPublish']); ?>)</a>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tfoot>
					</table>
					<table align="right" id="tableOptions">
						<tr>
							<td>Affichage :</td>
							<td>
								<?php
								$values = array ('advanced' => 'Détails', 'thumbnail' => 'Miniatures', 'list' => 'Liste');
								$extra = 'onchange="javascript: Copix.savePreference (\'heading|elements|display|' . $ppo->heading . '\', this.value, false)"';
								_eTag ('select', array ('name' => 'heading|elements|display', 'emptyShow' => false, 'selected' => $ppo->prefDisplay, 'values' => $values, 'extra' => $extra));
								?>
							</td>
							<td>&nbsp;&nbsp;&nbsp;</td>
							<td>Tri :</td>
							<td>
								<?php
								$values = array (
									HeadingElementInformationServices::SORT_SHOW => 'Ordre d\'affichage',
									HeadingElementInformationServices::SORT_TYPE => 'Type',
									HeadingElementInformationServices::SORT_CAPTION => 'Libellé',
									HeadingElementInformationServices::SORT_STATUS => 'Statut'
								);
								$extra = 'onchange="javascript: Copix.savePreference (\'heading|elements|sort|' . $ppo->heading . '\', this.value, false)"';
								_eTag ('select', array ('name' => 'heading|elements|sort', 'emptyShow' => false, 'selected' => $ppo->sort, 'values' => $values, 'extra' => $extra));
								?>
							</td>
							<td><?php _eTag ('popupinformation', array ('img' => _resource ('img/tools/help.png')), 'Pour gérer l\'ordre d\'affichage, vous devez choisir le tri Ordre d\'affichage.'); ?></td>
						</tr>
					</table>
				</form>
			</td>
			<td style="width: 400px; vertical-align: top" id="HeadingElementInformationDiv"></td>
		</tr>
	</table>
<?php _eTag ('endblock') ?>

<?php
$javaScript = '
	var actionCheck = new CopixFormObserver ("actionForm", {onChanged: function (){
	   checkActionIcons();
	},
	waitForCycle: 1
	});  

	var tabOrder = new Array ();
	checkActionIcons ();
	
	';
if ($ppo->sort == HeadingElementInformationServices::SORT_SHOW) {
	$javaScript .= '
	var toIgnore = $$("#elementTable thead tr");
	if (typeof ($E) != "function"){
		var $E = function(selector, filter){
			return ($(filter) || document).getElement(selector);
		};
	}
	toIgnore.push($E("#elementTable tfoot tr"));
	
	 var myTblSort = new mooTblSort("elementTable",{  
		 //ignore:$E("#elementTable thead tr"),
		 //ignore:$E("#elementTable tfoot tr, #elementTable thead tr"),
		 ignore: toIgnore,
		 handle: $$(".handle"),

		 onStart : function(el){
			el.setStyle("background-color", "#AAA");
			$$(".sortable").each(function(el, index){
					tabOrder[index] = el.get("order");
				}
			);
		 },
		 onComplete : function(el){
			el.setStyle("background-color", "");
			var elements = $$(".sortable");
			for(i=0 ; i<elements.length;i++){
				if(elements[i].get("order") == el.get("order") && elements[i].get("order") != tabOrder[i]){
					moveElement (elements[i].get("order"), tabOrder[i], elements[i].get("id_helt"), elements[i].get("type"));
					break;
				}
			}
		  }
	 });';
}

$javaScript .= '
	' . (empty($ppo->selectedItems) && (CopixAuth::getCurrentUser()->testCredential("basic:admin") || $ppo->parentHeading->public_id_hei != 0) ? 'showHeadingElementInformationsIn ("' . $ppo->parentHeading->id_helt . '","heading",0 , "HeadingElementInformationDiv");' : '') . '
	if (window.addEventListener) {
		window.addEventListener("resize", arrowPosition, false);
	} else if (window.attachEvent) {
		window.attachEvent("onresize", arrowPosition);
	}

';

// Après publication d'un élément, affichage du formulaire d'envoi de notification
if ((_request('prevaction') == 'publish') && (!empty ($ppo->selectedItems))) {
    $windowId = 'notificationWindow'.uniqid();
    $javaScript .= <<<EOF
    // Affichage de la popup de notification de publication
    Copix.get_copixwindow('{$windowId}').display();
EOF;
    $tpl = new CopixTpl ();
    $tpl->assign('windowId', $windowId);
    $content = $tpl->fetch ('headingnotification.php');
    $notificationWindow = _tag ('copixwindow', array (
        'id' => $windowId,
        'title' =>'Notification de publication',
        'fixed' => true,
        'width' => 410
    ), $content);
    echo $notificationWindow;
}

CopixHTMLHeader::addJSCode("var lastTab = null;var lastOpenElement = null;");
CopixHTMLHeader::addJSDOMReadyCode ($javaScript);
CopixHtmlHeader::addJSLink (_resource ('|js/heading.admin.js'));

if (!empty($ppo->selectedItems)){
	if (sizeof($ppo->selectedItems) == 1){
		$element = $ppo->selectedItems[0];
		CopixHtmlHeader::addJsDomReadyCode ('showHeadingElementInformationsIn (\''.$element->id_helt.'\',\''.$element->type_hei.'\',\''.$element->public_id_hei.'\', \'HeadingElementInformationDiv\')');
	} else {
		foreach ($ppo->selectedItems as $element){
			CopixHtmlHeader::addJsDomReadyCode ('showMultipleHeadingElementInformationsIn (\''.$element->id_helt.'\',\''.$element->type_hei.'\',\''.$element->public_id_hei.'\', \'HeadingElementInformationDiv\', true)');
		}
	}
} else if ($ppo->id_helt !== null && $ppo->type_hei !== null){
	CopixHtmlHeader::addJsDomReadyCode ('showHeadingElementInformationsIn (\''.$ppo->id_helt.'\',\''.$ppo->type_hei.'\',\''.$ppo->public_id_hei.'\', \'HeadingElementInformationDiv\')');
}