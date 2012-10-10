<?php if (!$justTable) { ?>
	<div class="cmsbloc" id="contentstats" style="display: <?php echo ($show) ? 'block' : 'none' ?>">
		<div class="cmsbloc_title">
			<div id="handlecontentstats" class="widgethandle">
				<img src="<?php echo _resource ('heading|img/stats.png') ?>" alt="Statistiques" title="Statistiques" />
				Statistiques
			</div>
			<div class="showdivDashboard" id="showdivcontentstats">
				<a href="#" id="contentstatsoptions">
					<img src="<?php echo _resource ('img/tools/config.png'); ?>" title="Options des statistiques" alt="Options des statistiques" />
				</a>
				<?php
					$content = "<table class='CopixVerticalTable'><tr><th>Afficher les statistiques à partir du dossier : </th>";
					$content .= "<td>".CopixZone::process("headingelementchooser", array('selectedIndex'=>$selectedHeading, 'inputElement'=>'headingcontentstatsoption', 'id'=>'elementchoosercontentstatsoptions', 'identifiantFormulaire'=>'contentstatsoptions', 'linkOnHeading'=>true, "arTypes"=>array("heading")))."</td></tr></table>";
					$content .= "<div style='text-align:right'><button onclick='updateContentStats();$(\"copixWindowContentStatsOption\").fireEvent(\"close\");return false;' class='button' id='submitcontentstatsoptions'>Appliquer</button></div>";
					_etag ('copixwindow', array ('id'=>'copixWindowContentStatsOption', 'clicker'=>'contentstatsoptions', 'title'=>"Options des statistiques"), $content);
				?>
				 |
				<?php _eTag ('showdiv', array ('id' => 'dashboardcontentstats', 'userpreference' => 'heading|dashboard|contentstats')) ?>
			</div>
		</div>
		<div style="display: <?php echo (CopixUserPreferences::get ('heading|dashboard|contentstats', true)) ? 'block' : 'none' ?>" class="cmsbloc_content" id="dashboardcontentstats">
<?php } ?>

<table class="CopixTable">
	<tr>
		<th>Type de contenu</th>
		<th style="width: 50px; text-align: right"><img src="<?php echo _resource ('heading|img/actions/publish.png') ?>" alt="Publiés" title="Publiés" /></th>
		<th style="width: 50px; text-align: right"><img src="<?php echo _resource ('heading|img/actions/draft.png') ?>" alt="Brouillons" title="Brouillons" /></th>
		<th style="width: 50px; text-align: right"><img src="<?php echo _resource ('heading|img/actions/archive.png') ?>" alt="Archivés" title="Archivés" /></th>
	</tr>
	<?php foreach ($arStats as $stat) { ?>
		<tr <?php echo _tag ('trclass') ?>>
			<td>
				<img src="<?php echo _resource ($stat['infos']['icon']) ?>" alt="<?php echo $stat['infos']['caption'] ?>" title="<?php echo $stat['infos']['caption'] ?>" style="vertical-align: text-bottom" />
				<?php echo $stat['infos']['caption'] ?>
			</td>
			<td style="text-align: right"><span class="status3"><?php echo number_format ($stat['published'], 0, ',', ' ') ?></span></td>
			<td style="text-align: right"><span class="status0"><?php echo number_format ($stat['drafts'], 0, ',', ' ') ?></span></td>
			<td style="text-align: right"><span class="status4"><?php echo number_format ($stat['archives'], 0, ',', ' ') ?></span></td>
		</tr>
	<?php } ?>
	<tr <?php _eTag ('trclass') ?>>
		<td></td>
		<td style="text-align: right"><span class="status3"><b><?php echo $published ?></b></span></td>
		<td style="text-align: right"><span class="status0"><b><?php echo $drafts ?></b></span></td>
		<td style="text-align: right"><span class="status0"><b><?php echo $archives ?></b></span></td>
	</tr>
</table>

<?php if (!$justTable) { ?>
		</div>
	</div>
	<?php
	CopixHTMLHeader::addJSCode ("
	function updateContentStats(){
		var contentStatsHeadingValue = $('headingcontentstatsoption').value ? $('headingcontentstatsoption').value : 0;
		new Request.HTML({
			url : '"._url('heading|dashboard|getContentStats')."',
			update : 'dashboardcontentstats'
		}).post({'heading':contentStatsHeadingValue});
		Copix.savePreference ('heading|dashboard|headingcontentstatsoption', contentStatsHeadingValue);
	}
	");
}
?>