<?php 
	_eTag('mootools', array('plugins'=>'copixformobserver'));
	$tabs = array();
	$id = uniqid();
	$firstTab = null;
	foreach ($groups as $key=>$portletGroup){
		if ($firstTab == null){
			$firstTab = $key;
		}
		$tabs[$id.$key]	= CopixI18N::exists('portal|addportlet.'.$key)? _i18n('portal|addportlet.'.$key) : $key;
	}
	if ($portletClipBoard != null && !empty ($portletClipBoard)){
		$tabs[$id."clipboard"] = 'Presse papier';
	
	}
	
$content = "<div><div class='addPortletTab'>"._Tag ('tabgroup', array ('tabs' => $tabs, 'default' => $id.$firstTab))."</div><div class='addPortletContent'>";
$i = 0;
foreach ($groups as $key=>$portletGroup){
	$content .= "<div id='".$id.$key."'>";
	foreach ($portletGroup as $portletId=>$portletInformations){
		$content.='<a class="addPortletElement" title="' . (array_key_exists('description', $portletInformations) ? $portletInformations['description'] : '').'" href="'._url ('portal|admin|addPortlet', array ('editId'=>$editId, 'type'=>$portletId, 'position'=>$variableName)).'" class="addPortletLink_'.$variableName.'">';
		$content.='<img width="32px" style="vertical-align:bottom" src="'._resource (array_key_exists('icon', $portletInformations) ? $portletInformations['icon'] : 'portal|img/portlet.png').'" />';
		$content.='<br />'.$portletInformations['caption'].'</a>';	
	}
	$content .= '</div>'; 
	$i++;
}
if ($portletClipBoard != null && !empty ($portletClipBoard)){
	$content .= "<div id='".$id."clipboard'>";
	$content .= '<a style="padding:5px;" href="'._url ('portal|admin|emptyPortletClipboard', array ('editId'=>$editId)).'"><img style="vertical-align:middle;" src="'. _resource('portal|img/trash.png') .'" title="Vider le presse papier" alt="Vider le presse papier" />Vider le presse papier</a><br style="clear:both;" />';
	
	foreach ($portletClipBoard as $id => $portlet){
		$content.='<div class="addPortletElement"><a  href="'._url ('portal|admin|pastePortlet', array ('editId'=>$editId, 'portletClipboardId'=>$id, 'position'=>$variableName)).'" class="addPortletLink_'.$variableName.'">';
		$content.='<img style="vertical-align:bottom" src="'._resource (array_key_exists('icon', $arPortletsInfos[$portlet->type_portlet]) ? $arPortletsInfos[$portlet->type_portlet]['icon'] : 'portal|img/portlet.png').'" />';
		if ($arPortletsInfos[$portlet->type_portlet]['group'] == "element"){
			
			$elements = $portlet->getElements();
			
			if (!empty($elements)){
				$content.= '</a><br/>';
				//$content.= '<br />Element'. (sizeof($elements)>1 ? 's' : ''). ' présent'.(sizeof($elements)>1 ? 's' : '').' :<br />';	
				$arCaption = array();
				foreach ($elements as $element){
					$arCaption [] = _tag ('popupinformation', array ('clickerclass'=>'addPortletPreview', 'divclass'=>'addPortletPreviewPopupInformation', 'displayimg'=>false, 'text'=>$element->getHeadingElement()->caption_hei), _ioClass('heading|headingelementinformationservices')->previewById ($element->getHeadingElement()->id_helt, $element->getHeadingElement()->type_hei));
				}
				$content.= implode('<br /> ', $arCaption);
			} else {
				$content.= '<br />Portlet vide';
				$content.= '</a>';
			}
			
		} else {
			$content.= $arPortletsInfos[$portlet->type_portlet]['caption'];
		}
		$content .= '</div>'; 
	}
	$content .= '</div>'; 
}
$content .= '</div></div>';
echo '<div class="ajoutPortlet clear" id="'.$variableName.'">'._tag ('popupinformation', array ('img'=>_resource ('img/tools/add.png'), 'text'=>'Ajouter', 'divclass'=>'addPortletPopupInformation', 'handler'=>'clickdelay'), $content).'</div>';
?>