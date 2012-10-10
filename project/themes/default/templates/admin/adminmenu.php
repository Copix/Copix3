<div style="position: absolute; background-color: #ffffff; border: 1px solid #000000; right: 10px; margin-top: -15px;padding-top: 2px;padding-left: 2px; padding-right: 2px;border-bottom: 0px none; padding-bottom: 0px;"> 
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
?>
</div>