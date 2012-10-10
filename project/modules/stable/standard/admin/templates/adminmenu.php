<?php
foreach ($ppo->links as $groupId=>$groupInfos){
	$toReturn  = '';
	$toReturn .= '<ul>';
    foreach ($groupInfos['links'] as $moduleIndex=>$moduleInfos){
    	$toReturn .= '<li '._tag ('cycle', array ('values'=>',class="alternate"')).'>';
		$toReturn .= '<a href="'.$moduleInfos->getURL ().'" title="'._i18n ("copix:common.buttons.select").'">'.($moduleInfos->getIcon () ? '<img src="'.$moduleInfos->getIcon ().'" alt="" /> ' : '').$moduleInfos->getCaption ().'</a>';
		$toReturn .= '</li>';
    }
    $toReturn .= '</ul>';
	_eTag ('popupinformation', array ('handler'=>'clickdelay', 'img'=>$groupInfos['icon'], 'alt'=>$groupInfos['caption']), $toReturn);
}