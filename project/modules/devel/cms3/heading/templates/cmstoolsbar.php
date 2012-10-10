<div id="pageUpdateMenu" class="<?php echo CopixUserPreferences::get('portal|defaultDisplay', 'float') == 'float' ? 'pageUpdateMenu ' : 'pageUpdateMenuFixed '; ?>portalGeneralMenu" style="display: none;">
	<ul class="portalGeneralMenuList">
<?php
if (isset($page) && isset($listeElements)){
	$elements = "<table class='pageUpdateTable'>";
	$i = 0;
	foreach ($listeElements as $element){
		$elements .= "<tr ".($i == 0 ? "style='background-color:#CFCFCF'" : "")."><td>";
		$elements .= "<a href='"._url ('heading|element|prepareedit', array('type'=>$element->type_hei, 'id'=>$element->id_helt, 'heading'=>$element->parent_heading_public_id_hei))."'>";
		$elements .= "<img style='vertical-align:middle;' width='20' height='20' src='"._resource ($listeIcones[$element->type_hei]['image'])."' alt='".$element->type_hei."' />";
		$elements .= "&nbsp;" . $element->caption_hei . "&nbsp;";
		$elements .= "</a>";
		$elements .= "</td><td>";
		$elements .= "<a title='Aller à la rubrique' href='"._url ('heading|element|', array('heading'=>$element->parent_heading_public_id_hei))."'>";
		$elements .= "<img style='vertical-align:middle;' width='20' height='20' src='"._resource ($listeIcones['heading']['image'])."' alt='Aller à la rubrique' />";
		$elements .= "</a>";
		$elements .= "</td></tr>";
		$i++;
	}
	$elements .= "</table>";
	?>
	
	<li>
	<?php
	$url = _url ('heading|element|prepareedit', array('type'=>$element->type_hei, 'id'=>$element->id_helt, 'heading'=>$element->parent_heading_public_id_hei));
	if (sizeof($listeElements) == 1 ){
		$element = $listeElements[0];
		echo "<a href='".$url."'>";
		echo "<img style='vertical-align:middle;' width='20' height='20' src='"._resource ($listeIcones[$element->type_hei]['image'])."' alt='".$element->type_hei."' />";
		echo "Modifier la page</a>";
	} else{
		$element = $listeElements[0];
		_etag ('popupinformation', array ('divclass'=>'popupInformationPageUpdateMenu', 'img'=>_resource('img/tools/update.png'), 'text'=>"Modifier la page", 'url'=> $url, 'handler'=>'clickdelay'), $elements);
	}
	echo "</li>";
	
	//Informations
	$informations = '<table class="pageUpdateTable">';
	$informations .= '<tr><th>Identifiant publique</th><td>'.$page->public_id_hei.'</td></tr>';
	$informations .= '<tr><th>Version</th><td>'.$page->version_hei.'</td></tr>';
	$informations .= '<tr><th>Date de dernière modification</th><td>'.CopixDateTime::yyyymmddhhiissToDateTime($page->date_update_hei).'</td></tr>';
	$informations .= '<tr><th>Auteur / dernier auteur</th><td>'.$page->author_caption_create_hei . ' / ' .$page->author_caption_update_hei.'</td></tr>';
	$informations .= '<tr><th>Rubrique</th><td>'.$parent->caption_hei.'</td></tr>';
	$informations .= '</table>';
	echo "<li>"._tag ('popupinformation', array ('divclass'=>'popupInformationPageUpdateMenu', 'img'=>_resource('img/tools/information.png'), 'text'=>"Informations sur la page", 'url'=> $url, 'handler'=>'clickdelay'), $informations)."</li>";
	

	//Ajout des liens pour créer de nouveaux élements dans la rubrique
	$elements = '<table class="pageUpdateTable">';
	foreach ($listeIcones as $id=>$headingElementType){
		$link = '<a href="'._url ("heading|element|prepareCreate", array ('type'=>$id, 'heading'=>$page->parent_heading_public_id_hei)).'">';
		$elements .= '<tr><td>'.$link;
		$elements .= '<img id="img_'.$id.'" width="16px" height="16px" ';
		$elements .= 'src="'._resource ($headingElementType['image']).'" style="padding-top: 4px;" /> </a></td>';
		$elements .= '<td>'.$link.$headingElementType['caption'].'</a></td></tr>'."\n\r";
	}
	$elements .= '</table>';
	$url = _url ('heading|element|prepareedit', array('type'=>$element->type_hei, 'id'=>$element->id_helt, 'heading'=>$element->parent_heading_public_id_hei));
	echo "<li>";
	_etag ('popupinformation', array ('divclass'=>'popupInformationPageUpdateMenu', 'img'=>_resource('img/tools/add.png'), 'text'=>"Nouveau", 'url'=> $url, 'handler'=>'clickdelay'), $elements);
	echo "</li>";

	
}

//elements affichés qui ont appelé la barre
$tabDisplayedElements = '<table class="pageUpdateTable">';
foreach ($displayedElements as $element){
	//_dump($element);
	switch ($element->getParam('type')){
		case 'menu' :
			$tabDisplayedElements .= '<tr><td>Menu '.$element->getParam('type_menu').'</td></tr>';
			break;
		default :
			if ($element->getParam('element')->public_id_hei != null){
				$tabDisplayedElements .= '<tr><td>Element de type '.$element->getParam('type').' : <a title="Cliquer pour modifier" href="'._url('heading|element|prepareEdit', array('type'=>$element->getParam('type'), 'id'=>$element->getParam('element')->id_helt, 'heading'=>$element->getParam('element')->parent_heading_public_id_hei)).'">'.$element->getParam('element')->caption_hei.'</a> (public_id : '.$element->getParam('element')->public_id_hei.')</td></tr>';
			}
	}
}
$tabDisplayedElements .= '</table>';
echo "<li>"._tag ('popupinformation', array ('divclass'=>'popupInformationPageUpdateMenu', 'img'=>_resource('img/tools/information.png'), 'text'=>"Elements affichés", 'handler'=>'clickdelay'), $tabDisplayedElements)."</li>";


//Lien pour aller au contenu
echo '<li><a href="'._url ('heading|element|', isset($page) ? array ('heading'=> $page->parent_heading_public_id_hei, 'selected'=>array ($page->id_helt.'|'.$page->type_hei.'|'.$page->public_id_hei)) : array ('heading'=> 0)).'"><img src="'._resource ('heading|img/general_view.png').'" alt="" />Voir dans contenu</a></li>';

//menus
$menu = CopixZone::process("heading|configurationmenus", array("record"=>isset($page) ? $page : null, "template"=>'portal|pageupdateconfigurationmenus.php'));
echo "<li><a id='clickerconfigurationmenus' href='javascript:void(0);'><img src='"._resource('heading|img/togglers/menu.png')."' />Menus</a></li>";
_etag ('copixwindow', array ('id'=>'copixwindowconfigurationmenus', 'class'=>'copixwindow elementchooserwindow', 'clicker'=>'clickerconfigurationmenus', 'title'=>'Parcourir', 'min-width' => '400px'), $menu);
?>
</ul>

<div class="pageUpadateMenuOptions">
<?php
	_eTag ('CopixZone', array ('process' => 'admin|UserPreferences', 'preferences' => array ('portal|defaultDisplay'), 'ajaxSave'=>false));
?>
</div>
<div class="clear"></div>
<?php
$updateMenuPosition = CopixUserPreferences::get('portal|defaultDisplay', 'fix') == 'float' ? "document.body.adopt($('pageUpdateMenu'));" : "$('pageUpdateMenu').inject(document.body, 'top');";
$jsCode = <<<EOF
$updateMenuPosition
$('pageUpdateMenu').setStyle('display', '');
EOF;
CopixHTMLHeader::AddJSDomReadyCode($jsCode);
?>
</div>