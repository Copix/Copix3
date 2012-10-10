<style>
.galerieElement {
	float:left;
	padding:12px 7px;
	margin:2px;
}

.galerieElement:hover{
	opacity:0.9;
}

.galerieElement img{
	box-shadow: 1px 1px 12px #555;
	-moz-box-shadow : 1px 1px 12px #555;
}

</style>

<div>
	<?php
	$rel = uniqid();
	foreach ($elementsList as $element){
		$elementParams = new CopixParameterHandler();
		$elementParams->setParams ($element->getOptions());
		$image = _ioClass ('images|imageservices')->getByPublicId ($element->getHeadingElement ()->public_id_hei);					
		?>
	
		<div class="galerieElement">
		<?php 
		if ($params->getParam('eraseimageconfig','oui')){
			echo '<a href="'._url('heading||', array('public_id'=>$image->public_id_hei, 'smoothboxType'=>'image')).'" class="smoothbox" rel="'.$rel.'">';		
		} else {
			if($elementParams->getParam ('link') && $elementParams->getParam ('thumb_show_image') == 'none'){
				echo '<a href="'._url('heading||', array('public_id'=>$elementParams->getParam ('link'))).'" >';
			}
			if($elementParams->getParam ('thumb_show_image') == 'smoothbox'){
				echo '<a href="'._url('heading||', array('public_id'=>$image->public_id_hei, 'smoothboxType'=>'image')).'" class="smoothbox" '.($elementParams->getParam ('thumb_galery_id', false) ? 'rel="'.$elementParams->getParam ('thumb_galery_id').'"' : '').'>';		
			} else if ($elementParams->getParam ('thumb_show_image') == '_blank'){
				echo '<a href="'._url('heading||', array('public_id'=>$image->public_id_hei)).'" target="_blank">';
			}
		}

		switch ($elementParams->getParam('alt_image')){
			case 'caption' : 
				$alt = $image->caption_hei;
				break;
			case 'description' : 
				$alt = $image->description_hei;
				break;
			default:
				$alt = null;	
		}
		
		switch ($elementParams->getParam('title_image')){
			case 'caption' : 
				$title = $image->caption_hei;
				break;
			case 'description' : 
				$title = $image->description_hei;
				break;
			default:
				$title = null;	
		}		
		?>
		<img 
			alt="<?php echo $alt; ?>" 
			src="<?php echo _url('images|imagefront|GetImage', array('id_image'=>$image->id_image, 'width'=>$params->getParam ('thumbwidth', 100), 'height'=>$params->getParam ('thumbheight', 100), 'keepProportions'=>$params->getParam ('thumb_keep_proportions', 0), 'crop'=>1)); ?>" 
			title="<?php echo $title; ?>" 
		/>
		<?php
		if($params->getParam('eraseimageconfig','oui') || $elementParams->getParam ('link') || $elementParams->getParam ('thumb_show_image') == 'smoothbox' || $elementParams->getParam ('thumb_show_image') == '_blank'){
			echo '</a>';		
		}
		?>
		</div>
	<?php } ?>
</div>
<br clear="all" />