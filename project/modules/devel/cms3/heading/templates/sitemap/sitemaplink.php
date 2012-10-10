<?php
if($sitemapLink){
	$url = '#';
	if($sitemapLink->getUrlMode() == SiteMapLink::URL_MODE_CUSTOM){
		$url = $sitemapLink->getCustomUrl(); 	
	}else{
		$public_id = $sitemapLink->getCmsLink();
		$element = _ioClass ('headingelementinformationservices')->get($public_id);
		
		if($element->type_hei == 'heading'){
			$url = CopixURL::get ('heading||', array ('public_id' => $public_id, 'caption_hei' => /*$sitemapLink->getCaption()*/ $element->caption_hei));
			$headingservices = new HeadingServices();
			$heading = null;
			try{
				$heading = $headingservices->getByPublicId($element->public_id_hei);
			}catch(HeadingElementInformationNotFoundException $e){}
			if($heading){
				if(!isset($heading->home_heading) || !$heading->home_heading){
					$url = '#';
				}else{
					$url = CopixURL::get ('heading||', array ('public_id' => $heading->home_heading, 'caption_hei' => /*$sitemapLink->getCaption()*/ $element->caption_hei));
				}
			}
		}else if($element->type_hei == 'page'){
			$url = CopixURL::get ('heading||', array ('public_id' => $public_id, 'caption_hei' => /*$sitemapLink->getCaption()*/ $element->caption_hei));
		}else if($element->type_hei == 'link'){
			$url = CopixURL::get ('heading||', array ('public_id' => $public_id, 'caption_hei' => /*$sitemapLink->getCaption()*/ $element->caption_hei));
		}
	}
	?>
	<?php if($isRoot){?><ul><?php }?>
		<li>
			<a <?php if($sitemapLink->getNewWindow()){echo 'target="_blank"';}?> href="<?php echo $url?>">
				<?php echo $sitemapLink->getCaption();?>
			</a>
			<?php
			if($sitemapLink->getChildMode() == SiteMapLink::CHILD_MODE_HEADING){
				$headingId = $sitemapLink->getCmsHeading();
				$children = _ioClass('HeadingElementInformationServices')->getTree($headingId);
				if(count($children) != 0){
					echo CopixZone::process('heading|SiteMapLinkCms', array('elements' => $children));
				}
			}else{?>
				<?php $children = SiteMapServices::getChildHeading($sitemapLink->getId());
				if(count($children) != 0){?>
					<ul>
						<?php foreach ($children as $link){
							echo CopixZone::process('heading|SiteMapLink', array('sitemapLink' => $link));
						}?>
					</ul>
				<?php }
			}?>
		</li>
	<?php if($isRoot){?></ul><?php }?>
<?php }else{?>
	Aucun lien n'a été défini.
<?php }?>