<?php
//plugin mootools smoothbox pour l'affichage taille réelle
_eTag('mootools', array('plugins'=>'smoothbox'));

echo ($params->getParam ('align_image', 'auto') == 'auto' ? '' : '<div style="text-align:'.$params->getParam ('align_image').'">');

if($params->getParam ('link') && $params->getParam ('thumb_show_image', 'none') == 'none'){
	echo '<a href="'._url('heading||', array('public_id'=>$params->getParam ('link'))).'" >';
}
if($params->getParam ('thumb_show_image') == 'smoothbox'){
	echo '<a href="'._url('heading||', array('public_id'=>$image->public_id_hei, 'smoothboxType'=>'image')).'" class="smoothbox" '.($params->getParam ('thumb_galery_id', false) ? 'rel="'.$params->getParam ('thumb_galery_id').'"' : '').'>';		
} else if ($params->getParam ('thumb_show_image') == '_blank'){
	echo '<a href="'._url('heading||', array('public_id'=>$image->public_id_hei)).'" target="_blank">';
}
?>
<img 
	style="<?php echo $params->getParam ('style_image'); ?>" 
	class="<?php echo $params->getParam ('classe_image'); ?>" 
	alt="<?php echo $alt; ?>" 
	src="<?php echo _url('heading||', array('public_id'=>$image->public_id_hei, 'width'=>$width, 'height'=>$height, 'keepProportions'=>$params->getParam ('thumb_keep_proportions', 1), 'resizeIfNecessary'=>true, 'v'=>$v)); ?>" 
	title="<?php echo $title; ?>" 
	vspace="<?php echo $params->getParam ('vspace'); ?>"
	hspace="<?php echo $params->getParam ('hspace'); ?>"
/>
<?php
if($params->getParam ('link') || $params->getParam ('thumb_show_image') == 'smoothbox' || $params->getParam ('thumb_show_image') == '_blank'){
	echo '</a>';		
}

if ($params->getParam('legend_image', false)){
	?>
	<div class="imageLegend"><?php echo $params->getParam('legend_image'); ?></div>
	<?php 
} 
echo $params->getParam ('align_image', 'auto') == 'auto' ? '' : '</div>';
?>