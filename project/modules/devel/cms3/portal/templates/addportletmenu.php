<div id="addPortletMenu" class="addPortletMenu addPortletMenuHide">
	<div class="addPortletMenuHeader">
		<strong>Ajouter un élément</strong>
		<div id="closeAddPortletMenu"><img src="<?php echo _resource('img/tools/close.png'); ?>" /></div>
	</div>
	<div class='addPortletTab'>
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
			
		$content = _Tag ('tabgroup', array ('tabs' => $tabs, 'default' => $id.$firstTab))."</div><div class='addPortletContent'>";
		$i = 0;
		foreach ($groups as $key=>$portletGroup){
			$content .= "<div id='".$id.$key."'>";
			foreach ($portletGroup as $portletId=>$portletInformations){
				$content.='<a class="addPortletElement" title="' . (array_key_exists('description', $portletInformations) ? $portletInformations['description'] : '').'" href="javascript:;" rel="'._url ('portal|admin|addPortlet', array ('editId'=>$editId, 'type'=>$portletId)).'" >';
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
				$content.='<div class="addPortletElement" rel="'._url ('portal|admin|pastePortlet', array ('editId'=>$editId, 'portletClipboardId'=>$id)).'"><a  href="javascript:;" class="addPortletLink_'.$variableName.'">';
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
					$content.= '<br/>' . $arPortletsInfos[$portlet->type_portlet]['caption'];
				}
				$content .= '</div>'; 
			}
			$content .= '</div>'; 
		}
		echo $content;
	
		?>
	</div>
</div>
<div class="addPortletMenuAddButton addPortletMenuHide"><a id="addPortletMenuAddButton" href="javascript:;"><img src="<?php echo _resource ('img/tools/add.png');?>" />Ajouter un élément</a></div>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
	$('addPortletMenuAddButton').addEvent('click', function(){
		$('addPortletMenuAddButton').getParent().addClass('addPortletMenuHide');
		$('addPortletMenu').removeClass('addPortletMenuHide');
	});
	$('closeAddPortletMenu').addEvent('click', function(){
		$$('.clone').each(function(el){
			el.dispose();
		});
    	$('addPortletMenuAddButton').getParent().removeClass('addPortletMenuHide');
		$('addPortletMenu').addClass('addPortletMenuHide');
    });
    
	$('pageContent').adopt($('addPortletMenu'));

	$('addPortletMenu').setStyle('top' , $('pageContent').getStyle('padding-top'));
	
	var browserWindowSize = window.getSize();
    var elementSize = $('addPortletMenuAddButton').getParent().getSize();
    y = (browserWindowSize.y / 2) - (elementSize.y / 2);
    $('addPortletMenuAddButton').getParent().setStyle('top' , y);
    $('addPortletMenuAddButton').getParent().removeClass('addPortletMenuHide');
    
    $('addPortletMenu').getElements('.addPortletElement').each(function(el){
    	createDraggableElementFromMenu (el, '"._request('editId')."');	
    });
");
?>