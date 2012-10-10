<?php if (!$justTable){?>
<div class="cmsbloc" id="drafts" style="display: <?php echo ($show) ? 'block' : 'none' ?>">
	<div class="cmsbloc_title">
		<div id="handledrafts" class="widgethandle">
			<img src="<?php echo _resource ('heading|img/actions/draft.png') ?>" alt="Brouillons" title="Brouillons" />
			Brouillons <span id="nbBrouillons"><?php echo count($listeBrouillons) > 0 ? "(".count($listeBrouillons).")" : "";?></span>
		</div>			
		<div class="showdivDashboard" id="showdivdrafts">
			<a href="#" id="draftsoptions">
				<img src="<?php echo _resource ('img/tools/config.png'); ?>" title="Options des brouillons" alt="Options des brouillons" />
			</a>
			<?php 
				$content = "<table class='CopixVerticalTable'><tr><th>Afficher les brouillons à partir du dossier : </th>";
				$content .= "<td>".CopixZone::process("headingelementchooser", array('selectedIndex'=>$selectedHeading, 'inputElement'=>'headingdraftoption', 'id'=>'elementchooserdraftoptions', 'identifiantFormulaire'=>'draftoptions', 'linkOnHeading'=>true, "arTypes"=>array("heading")))."</td></tr></table>";
				$content .= "<div style='text-align:right'><button onclick='updateDrafts();$(\"copixWindowDraftOption\").fireEvent(\"close\");return false;' class='button' id='submitdraftoptions'>Appliquer</button></div>";
				_etag ('copixwindow', array ('id'=>'copixWindowDraftOption', 'clicker'=>'draftsoptions', 'title'=>"Options des brouillons"), $content); 
			?>	
			 | 
			<?php _eTag ('showdiv', array ('id' => 'dashboarddrafts', 'userpreference' => 'heading|dashboard|drafts')) ?>
		</div>
	</div>
	<div style="display: <?php echo (CopixUserPreferences::get ('heading|dashboard|drafts', true)) ? 'block' : 'none' ?>" class="cmsbloc_content" id="dashboarddrafts">
<?php }?>
	<table class="CopixTable" id="draftstable">
		<tr>
			<th colspan="2">Elément</th>
			<th style="width: 130px">Date</th>
			<th class="last" style="width: 100px">Auteur</th>
		</tr>
		<?php foreach ($listeBrouillons as $brouillon) { ?>
			<tr <?php _eTag ('trclass') ?>>
				<td><img src="<?php echo _resource ($elementsTypes[$brouillon->type_hei]['icon']) ?>" /></td>
				<td>
					<a href="<?php echo _url ('heading|element|prepareedit', array ('type' => $brouillon->type_hei, 'id' => $brouillon->id_helt, 'heading' => $brouillon->parent_heading_public_id_hei)) ?>">
						<?php echo ($brouillon->caption_hei ? $brouillon->caption_hei : "(Pas de titre)") ?>
					</a>
				</td>
				<td><?php echo CopixDateTime::yyyymmddhhiissToDateTime($brouillon->date_update_hei) ?></td>
				<td><?php echo $brouillon->author_caption_update_hei ?></td>
			</tr>
		<?php } ?>
	</table>
<?php if (!$justTable){?>
	</div>
</div>
<?php 
CopixHTMLHeader::addJSCode("
function updateDrafts(){
	var draftHeadingValue = $('headingdraftoption').value ? $('headingdraftoption').value : 0;
	new Request.HTML({
		url : '"._url('heading|dashboard|getDrafts')."',
		update : 'dashboarddrafts',
		onComplete : function(){
			var nbBrouillons = $('draftstable').getElements('tr').length;
			$('nbBrouillons').innerHTML = '('+nbBrouillons+')';
		}
	}).post({'heading':draftHeadingValue});
	Copix.savePreference ('heading|dashboard|headingdraftoption', draftHeadingValue);
}
");
}?>