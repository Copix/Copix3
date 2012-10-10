<?php 

$customUrlCode = SiteMapLink::URL_MODE_CUSTOM;
$cmsUrlCode = SiteMapLink::URL_MODE_CMS;

$childModeHeadingCode = SiteMapLink::CHILD_MODE_HEADING;
$childModeManualCode = SiteMapLink::CHILD_MODE_MANUAL;

$js = <<<EOJS

$$('#url_mode_$customUrlCode, #url_mode_$cmsUrlCode').each(
	function (el){
		el.addEvent('change', function(event){
			var lineid = el.id.replace('url_mode_', '');
			$('link_$customUrlCode').setStyle('display','none');
			$('link_$cmsUrlCode').setStyle('display','none');
			$('link_'+lineid).setStyle('display','table-row');
		});
	}
);


$$('#child_mode_$childModeHeadingCode, #child_mode_$childModeManualCode').each(
	function (el){
		el.addEvent('change', function(event){
			var lineid = el.id.replace('child_mode_', '');
			$('child_mode_line_$childModeHeadingCode').setStyle('display','none');
			$('child_mode_line_$childModeManualCode').setStyle('display','none');
			$('child_mode_line_'+lineid).setStyle('display','table-row');
		});
	}
);


EOJS;

CopixHTMLHeader::addJSDOMReadyCode($js, 'sitemapFunctions');

?>

<?php _eTag ('error', array ('message' => $ppo->errors)); ?>
<?php _eTag ('beginblock', array ('title' => 'Edition d\'une catégorie', 'isFirst' => true)); ?>

<?php if(_request('success')){?>
<div class="box standout success">
		<div class="content">
			<h2>La catégorie a été sauvegardée</h2>
		</div>
</div>
<?php }?>

<form method="post" action="<?php echo _url('heading|sitemap|DoEditSitemap')?>">
	<input name="parentId"   type="hidden" value="<?php echo $ppo->link->getParentId(); ?>" />
	<input name="linkId"   type="hidden" value="<?php echo $ppo->link->getId(); ?>" />
	<table class="CopixVerticalTable">
		<tbody>
			<tr>
				<th>
					<label for="caption" >Nom</label>
				</th>
				<td>
					<input name="caption" id="caption" type="text" class="text" value="<?php echo $ppo->link->getCaption(); ?>" />
				</td>
			</tr>
			<tr class="alternate">
				<th>
					Type de lien
				</th>
				<td>
				<?php 
					_eTag('radiobutton', array('name'=>'url_mode', 'values'=>array(SiteMapLink::URL_MODE_CUSTOM =>'Adresse extérieure', SiteMapLink::URL_MODE_CMS=>'Element du CMS'), 'selected'=>$ppo->link->getUrlMode())); 
					?>  
				</td>
			</tr>
			<tr id="link_<?php echo SiteMapLink::URL_MODE_CUSTOM?>" style="display:<?php echo ($ppo->link->getUrlMode() != SiteMapLink::URL_MODE_CUSTOM) ? 'none'  : 'table-row' ?>">
				<th>
					<label for="custom_url" >Adresse extérieure</label>
				</th>
				<td>
					<input name="custom_url" id="custom_url" type="text" class="text" value="<?php echo $ppo->link->getCustomUrl(); ?>" />
				</td>
			</tr>
			<tr id="link_<?php echo SiteMapLink::URL_MODE_CMS?>" style="display: <?php echo ($ppo->link->getUrlMode() != SiteMapLink::URL_MODE_CMS) ? 'none'  : 'table-row' ?>">
				<th>Elément du CMS</th>
				<td>
					<?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array('selectedIndex'=>(!is_null($ppo->link->getCmsLink())) ? $ppo->link->getCmsLink() : '', 'inputElement'=>'cms_link', 'linkOnHeading'=>true, 'showAnchor'=>true, 'arTypes' => array('heading', 'page', 'link'))); ?>
				</td>
			</tr>
			<tr class="alternate">
				<th>
				<label for="new_window">Ouvrir dans une nouvelle fenêtre</label>
					
				</th>
				<td>
					<input id="new_window" name="new_window" type="checkbox" value="1" <?php if($ppo->link->getNewWindow()){echo 'checked="checked"';}?>/>  
				</td>
			</tr>
		</tbody>
	</table> 
	
	<div>
	<?php if($ppo->link->getId()){?>
		<h3>Ajout des enfants</h3>
		<div>
			<table class="CopixVerticalTable">
				<tbody>
					<tr>
						<th>
							Methode d'ajout
						</th>
						<td>
							<?php 
								_eTag('radiobutton', array('name'=>'child_mode', 'values'=>array(SiteMapLink::CHILD_MODE_MANUAL =>'Ajout manuel', SiteMapLink::CHILD_MODE_HEADING=>'Ajout du contenu d\'une rubrique du CMS (Rubriques et liens)'), 'selected'=>$ppo->link->getChildMode()));
							?>
						</td>
					</tr>
					<tr id="child_mode_line_<?php echo SiteMapLink::CHILD_MODE_MANUAL?>" class="alternate" style="display: <?php echo ($ppo->link->getChildMode() != SiteMapLink::CHILD_MODE_MANUAL) ? 'none'  : 'table-row';?>">
						<th>Ajout Manuel</th>
						<td><a href="<?php echo _url('heading|sitemap|editSiteMap', array('parentId' => $ppo->link->getId()));?>">Ajouter une catégorie</a></td>
					</tr>
					<tr id="child_mode_line_<?php echo SiteMapLink::CHILD_MODE_HEADING?>" class="alternate" style="display: <?php echo ($ppo->link->getChildMode() != SiteMapLink::CHILD_MODE_HEADING) ? 'none'  : 'table-row';?>">
						<th>Ajout automatique</th>
						<td>
							<?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array('selectedIndex'=>(!is_null($ppo->link->getCmsHeading())) ? $ppo->link->getCmsHeading() : '', 'inputElement'=>'cms_heading', 'linkOnHeading'=>true, 'showAnchor'=>true, 'showJustHeading' => true, 'arTypes' => array('heading'))); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php }?>
	</div>
	<input type="submit" value="Sauver"/>
</form>
<div>
<?php if($ppo->link->getId()){
	if($ppo->link->getChildMode() == SiteMapLink::CHILD_MODE_MANUAL){
		$children = SiteMapServices::getChildHeading($ppo->link->getId());
		if(count($children) == 0){?>
			Cette catégorie ne contient pas d'enfants
		<?php }else{
			
		?>
		<table class="CopixTable">
			<thead>
				<tr>
					<th>
						Nom de la catégorie
					</th>
					<th colspan="5">
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($children as $heading){?>
					<tr>
						<td>
							<a href="<?php echo _url('heading|sitemap|editSiteMap', array('linkId' =>$heading->getId(), 'parentId' => $heading->getParentId()));?>">
								<?php echo $heading->getCaption();?>
							</a>
						</td>
						
						
						<td class="action" style="width: 20px;">
							<?php if($heading->getPosition() != 0){?>
								<a href="<?php echo _url ('heading|sitemap|moveUp', array('linkId' =>$heading->getId(), 'parentId' => $heading->getParentId()));?>">
									<img src="<?php echo _resource ('img/tools/up.png') ?>" alt="monter" title="monter"/>
								</a>
							<?php }?>
						</td>
						<td class="action" style="width: 20px;">
							<?php if($heading->getPosition() != (count($children) - 1)){?>
								<a href="<?php echo _url ('heading|sitemap|moveDown', array('linkId' =>$heading->getId(), 'parentId' => $heading->getParentId()));?>">
									<img src="<?php echo _resource ('img/tools/down.png') ?>" alt="descendre" title="descendre"/>
								</a>
							<?php }?>
						</td>
						
						<td class="action" style="width: 20px;">
							<a href="<?php echo _url ('heading|sitemap|editSitemap', array('linkId' =>$heading->getId(), 'parentId' => $heading->getParentId()));?>">
								<img src="<?php echo _resource ('img/tools/update.png') ?>" alt="Modifier" title="Modifier"/>
							</a>
						</td>
						<td class="action" style="width: 20px;">
							<a href="<?php echo _url ('heading|sitemap|deleteSitemapLink', array('linkId' =>$heading->getId(), 'parentId' => $heading->getParentId()));?>">
								<img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="Supprimer" title="Supprimer"/>
							</a>
						</td>
						<td class="action" style="width: 20px;">
							<a target="sitemap_link_preview" href="<?php echo _url ('heading|sitemap|getSitemapLink', array ('id' => $heading->getId())) ?>">
								<img src="<?php echo _resource ('heading|img/generalicons/cms_show.png') ?>" alt="Apercu" title="Apercu" />
							</a>
						</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
		<?php 
		}
	}else{
		
		
	}

}?>
</div>
<?php 

if($ppo->link->getParentId()){
	$parent = SiteMapServices::getSiteMapLink($ppo->link->getParentId());
	$previousUrl = _url('heading|sitemap|editSiteMap', array('linkId' => $parent->getId(), 'parentId' => $parent->getParentId()));	
}else{
	$previousUrl = _url('heading|sitemap|');
}

 

?>
<br/>
<div>
	<a style="float:left;" href="<?php echo $previousUrl;?>">
	<img src="<?php echo _resource('img/tools/back.png');?>" alt="retour"/> 
	Remonter à l'élément parent</a>
	<a style="float:right;" href="<?php echo _url('heading|sitemap|');?>">
	<img src="<?php echo _resource('img/tools/back.png');?>" alt="retour"/>
	Remonter à la liste des sitemap</a>
</div>
<div class="clear"></div>
<?php _eTag ('endblock'); ?>