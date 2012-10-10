<?php 

$complex = array ();
$aternat = array ();
$view    = array ();
foreach (CopixConfig::instance ()->i18n_availables as $available ) {
	$flag = _resource ('selectcomplex|img/tools/flags/'.$available.'.png');
	$complex [$available] ='<img src="'.$flag.'" alt="'.$available.'" /> '.CopixI18N::translateLocal ($available);
	$aternat [$available] = CopixI18N::translateLocal ($available);
	$view    [$available] = '<img src="'.$flag.'" alt="'.$available.'" class="copixlanguagechooser_img_selected" />';
}
array_multisort ($aternat, $complex);

_eTag ('selectcomplex|selectcomplex', array(
	'id'=>$ppo->id,
	'name' => $ppo->name,
	'class'=>$ppo->class,
	'emptyShow'=>$ppo->emptyShow,
	'options'=>$complex,
	'alternatives'=>$aternat,
	'selectedView'=>$view,
	'selected'=>$ppo->selected,
	'style'=>'height:12px;'.$ppo->style,
	'extra'=>$ppo->extra,
	'emptyValues'=>$ppo->emptyValues,
	'emptyValuesView'=>'',
	'emptyValuesAlternatives'=>$ppo->emptyValuesAlternatives,
	'extraAlternative'=>$ppo->extraAlternative,
	'extraStyle'=>$ppo->extraStyle,
	'arrow'=>$ppo->arrow,
	'arrowImg'=>_resource ('selectcomplex|img/tools/arrow_flag.gif'),
	'widthSelect'=>$ppo->widthSelect,
	'heightSelect'=>$ppo->heightSelect
));

?>