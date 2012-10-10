<div id="pageUpdateHeaderMenu">
	<div class="bannerPageUpdateHeaderMenu">
		<div class="contentsize">
			<img alt="CopixCMS3" src="<?php echo _resource('img/logocms3petit.png');?>" />
			<div id="showDivPageUpdateHeaderMenu" class="buttonsPageUpdateHeaderMenu">
				<?php echo _Tag ('showdiv', array ('id' => 'bannerPageUpdateContent', 'userpreference' => 'portal|showBannerPageUpdateContent'));?>
			</div>
			<div class="buttonsPageUpdateHeaderMenu">
				<?php  echo $zoneBoutons; ?> 
			</div>
		</div>
	</div>

	<div id="bannerPageUpdateContent" style="display: <?php echo (CopixUserPreferences::get ('portal|showBannerPageUpdateContent', true)) ? 'block' : 'none' ?>">
		<form id="formPage" action="<?php echo _url ('portal|admin|valid', array ('editId' => _request ('editId'))); ?>" method="POST">
			<input type="hidden" name="publish" id="publish" />
			<input type="hidden" name="published_date" id="published_date" />
			<input type="hidden" name="end_published_date" id="end_published_date" />
			<div id="tabsPageUpdateHeaderMenu" class="tabsPageUpdateHeaderMenu">
				<div class="contentsize">
					<?php 
					$tabs = array();
					$firstTab = 'infos';
					$tabs["infos"] = 'Infos';
					$tabs["themes"] = 'Thèmes graphiques';
					
					echo "<div>"._Tag ('tabgroup', array ('tabs' => $tabs, 'default' => $firstTab))."</div>";
					?>
				</div>
			</div>
			<div class="toolsPageUpdateHeaderMenu">
				<div class="contentsize">
				<?php 
					echo "<div class='toolsPageUpdateHeaderMenuTabContent'>";
					echo "<div id='infos'>";
					if (CopixModule::isEnabled ('breadcrumb')) {
						_eTag ('copixzone', array ('process' => 'breadcrumb|show', 'required' => false));
					}
					echo $zonePagePath; 
					echo "</div>";
					echo "<div id='themes'>".CopixZone::process("heading|themechooser", array('public_id'=>$public_id_hei, 'template'=>'portal|themes.php'))."</div>";
					echo "</div>";				
				?>
				</div>
			</div>
		</form>
	</div>
	<div style="clear: both;"></div>
</div>

<?php
CopixHTMLHeader::addJSDOMReadyCode("
if (window.addEventListener) {
	window.addEventListener('resize', pageSize, false);
} else if (window.attachEvent) {
	window.attachEvent('onresize', pageSize);
}

var pageContent  = new Element('div', {id: 'pageContent'});
//pageContent.setStyle('overflow-y', 'scroll');
pageContent.adopt(document.body.getChildren());
pageContent.inject(document.body);

$('pageUpdateHeaderMenu').inject(document.body, 'top');
pageSize();

$('tabsPageUpdateHeaderMenu').getElements('span').each(function(el){
	el.addEvent('click',function(){
		pageSize();
	});
});
$('bannerPageUpdateContent').addEvent('display',function(){
	pageSize();
});
");