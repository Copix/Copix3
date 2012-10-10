<ul>
<?php
foreach ($elements as $element){
	if($element->menu_html_class_name_hei != "HR"){
		if(($element->type_hei == 'link' || $element->type_hei == 'page') && $element->status_hei == HeadingElementStatus::PUBLISHED){
			$url = CopixURL::get ('heading||', array ('public_id' => $element->public_id_hei, 'caption_hei' => $element->caption_hei));
			echo '<li><a href="'.$url.'">'.$element->caption_hei.'</a></li>';
	
		}else if($element->type_hei == 'heading'){
			$url = CopixURL::get ('heading||', array ('public_id' => $element->public_id_hei, 'caption_hei' => $element->caption_hei));
	
			$headingservices = new HeadingServices();
			$heading = null;
			try{
				$heading = $headingservices->getByPublicId($element->public_id_hei);
			}catch(HeadingElementInformationNotFoundException $e){}
			if($heading){
				if(!isset($heading->home_heading) || !$heading->home_heading){
					$url = '#';
				}
			}
			?>
			<li> 
			<a href="<?php echo $url;?>"><?php echo $element->caption_hei;?></a>
			<?php
			$children= _ioClass('HeadingElementInformationServices')->getTree ($element->public_id_hei);
			if(count($children) != 0){
				echo CopixZone::process('heading|SiteMapLinkCms', array('elements' => $children));
			}
		?></li>
	<?php }
	}
}?>
</ul>
