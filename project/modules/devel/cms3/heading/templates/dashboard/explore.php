<div class="cmsbloc" id="explore" style="display: <?php echo ($show) ? 'block' : 'none' ?>">
	<div class="cmsbloc_title">
		<div id="handleexplore" class="widgethandle">
			<img src="<?php echo _resource ('heading|img/icon_headings.png') ?>" alt="Navigation" title="Navigation" />
			Navigation
		</div>
		<div class="showdivDashboard" id="showdivexplore"><?php _eTag ('showdiv', array ('id' => 'dashboardexplore', 'userpreference' => 'heading|dashboard|explore')) ?></div>
	</div>
	<div style="display: <?php echo (CopixUserPreferences::get ('heading|dashboard|explore', true)) ? 'block' : 'none' ?>" class="cmsbloc_content" id="dashboardexplore">
		<?php echo CopixZone::process('heading|headingelementchooser', array('identifiantFormulaire'=>'dashboardexplorer', 'clickMod' => 1, "linkOnHeading"=>true, "copixwindow"=>false)); ?>
	</div>
</div>