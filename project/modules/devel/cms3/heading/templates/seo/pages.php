<?php
CopixHTMLHeader::addCSSLink(_resource('heading|css/cms.css'));
$imgFolded = _resource ('heading|img/browser_folded.png');
$imgUnfolded = _resource ('heading|img/browser_unfolded.png');
$jsCode = <<<EOF

// Masquer / afficher les options de recherche
$('searchLegend').setStyle('cursor', 'pointer');
$('searchLegend').addEvent('click', function(){
	var img = $('searchLegend').getElements('img')[0];
	$('searchOptions').toggleClass('cmsHide');
	img.src = ( $('searchOptions').hasClass('cmsHide') )? '$imgFolded' : '$imgUnfolded'; 
});

EOF;
CopixHTMLHeader::addJSDOMReadyCode( $jsCode );

$pagination = CopixZone::process('default|pagination', array(
	'linkBase' => _url('#', array('page'=>'')),
	'current' => $ppo->options->page,
	'max' => $ppo->nbrPages,
	'surround' => 2
));
?>
<?php _eTag ('beginblock', array ('title' => 'Liste des pages', 'isFirst' => true)); ?>
<?php echo $ppo->message; ?>

<form action="<?php echo _url ('heading|seo|editElements') ?>" method="post">
	<fieldset>
		<legend id="searchLegend">
			<img src="<?php echo _resource ('heading|img/browser_folded.png') ?>" alt="" />
			Options de recherche des pages
		</legend>
		<div id="searchOptions" class="cmsHide">
			<table class="CopixVerticalTable">
				<tr>
					<th style="width: 200px">
						<label for="nbrParPage">Nombre de résultats</label>
					</th>
					<td colspan="2">
						<input type="text" class="text numeric" name="nbrParPage" id="nbrParPage" size="3" value="<?php echo $ppo->options->nbrParPage ?>" /> par page
					</td>
				</tr>
				<tr class="alternate">
					<th>Nom</th>
					<td>
						<label for="name_OUI"><input type="radio" name="searchName" value="OUI" id="name_OUI" <?php if ($ppo->options->searchName == 'OUI') echo 'checked="checked"' ?> /> Saisi</label>
						&nbsp;<label for="name_NON"><input type="radio" name="searchName" value="NON" id="name_NON" <?php if ($ppo->options->searchName == 'NON') echo 'checked="checked"' ?> /> Vide</label>
						&nbsp;<label for="name_TOUT"><input type="radio" name="searchName" value="TOUT" id="name_TOUT" <?php if ($ppo->options->searchName == 'TOUT') echo 'checked="checked"' ?> /> Les 2</label>
					</td>
					<td>
						<label for="displayName">
						<input type="checkbox" name="displayName" id="displayName" value="OUI" <?php if ($ppo->options->displayName) { echo 'checked="checked"'; } ?> />
						Affiché</label>
					</td>
				</tr>
				<tr class="alternate">
					<th>Titre</th>
					<td>
						<label for="title_OUI"><input type="radio" name="searchTitle" value="OUI" id="title_OUI" <?php if ($ppo->options->searchTitle == 'OUI') echo 'checked="checked"' ?> /> Saisi</label>
						&nbsp;<label for="title_NON"><input type="radio" name="searchTitle" value="NON" id="title_NON" <?php if ($ppo->options->searchTitle == 'NON') echo 'checked="checked"' ?> /> Vide</label>
						&nbsp;<label for="title_TOUT"><input type="radio" name="searchTitle" value="TOUT" id="title_TOUT" <?php if ($ppo->options->searchTitle == 'TOUT') echo 'checked="checked"' ?> /> Les 2</label>
					</td>
					<td>
						<label for="displayTitle">
						<input type="checkbox" name="displayTitle" id="displayTitle" value="OUI" <?php if ($ppo->options->displayTitle) { echo 'checked="checked"'; } ?> />
						Affiché</label>
					</td>
				</tr>
				<tr>
					<th>Titre des menus</th>
					<td>
						<label for="title_menus_OUI"><input type="radio" name="searchTitleMenu" value="OUI" id="title_menus_OUI" <?php if ($ppo->options->searchTitleMenu == 'OUI') echo 'checked="checked"' ?> /> Saisi</label>
						&nbsp;<label for="title_menus_NON"><input type="radio" name="searchTitleMenu" value="NON" id="title_menus_NON" <?php if ($ppo->options->searchTitleMenu == 'NON') echo 'checked="checked"' ?> /> Vide</label>
						&nbsp;<label for="title_menus_TOUT"><input type="radio" name="searchTitleMenu" value="TOUT" id="title_menus_TOUT" <?php if ($ppo->options->searchTitleMenu == 'TOUT') echo 'checked="checked"' ?> /> Les 2</label>
					</td>
					<td>
						<label for="displayTitleMenu">
						<input type="checkbox" name="displayTitleMenu" id="displayTitleMenu" value="OUI" <?php if ($ppo->options->displayTitleMenu) { echo 'checked="checked"'; } ?> />
						Affiché</label>
					</td>
				</tr>
				<tr class="alternate">
					<th>URL</th>
					<td>
						<label for="url_OUI"><input type="radio" name="searchURL" value="OUI" id="url_OUI" <?php if ($ppo->options->searchURL == 'OUI') echo 'checked="checked"' ?> /> Saisi</label>
						&nbsp;<label for="url_NON"><input type="radio" name="searchURL" value="NON" id="url_NON" <?php if ($ppo->options->searchURL == 'NON') echo 'checked="checked"' ?> /> Vide</label>
						&nbsp;<label for="url_TOUT"><input type="radio" name="searchURL" value="TOUT" id="url_TOUT" <?php if ($ppo->options->searchURL == 'TOUT') echo 'checked="checked"' ?> /> Les 2</label>
					</td>
					<td>
						<label for="displayURL">
						<input type="checkbox" name="displayURL" id="displayURL" value="OUI" <?php if ($ppo->options->displayURL) { echo 'checked="checked"'; } ?> />
						Affiché</label>
					</td>
				</tr>
				<tr>
					<th>Description</th>
					<td>
						<label for="description_OUI"><input type="radio" name="searchDescription" value="OUI" id="description_OUI" <?php if ($ppo->options->searchDescription == 'OUI') echo 'checked="checked"' ?> /> Saisi</label>
						&nbsp;<label for="description_NON"><input type="radio" name="searchDescription" value="NON" id="description_NON" <?php if ($ppo->options->searchDescription == 'NON') echo 'checked="checked"' ?> /> Vide</label>
						&nbsp;<label for="description_TOUT"><input type="radio" name="searchDescription" value="TOUT" id="description_TOUT" <?php if ($ppo->options->searchDescription == 'TOUT') echo 'checked="checked"' ?> /> Les 2</label>
					</td>
					<td>
						<label for="displayDescription">
						<input type="checkbox" name="displayDescription" id="displayDescription" value="OUI" <?php if ($ppo->options->displayDescription) { echo 'checked="checked"'; } ?> />
						Affiché</label>
					</td>
				</tr>
				<tr class="alternate">
					<th>
						<label for="status_hei">Statut</label>
					</th>
					<td colspan="2">
						<?php _eTag ('select', array ('emptyShow' => false, 'name' => 'status_hei', 'values' => $ppo->statusOptions, 'selected' => $ppo->options->status_hei)); ?>
					</td>
				</tr>
				<tr>
					<th>
						<label for="sortBy">Tri</label>
					</th>
					<td>
						<?php _eTag ('select', array ('emptyShow' => false, 'name' => 'sortBy', 'values' => $ppo->sortOptions, 'selected' => $ppo->options->sortBy)); ?>
					</td>
					<td class="quiet">
						Les tris sur du texte sont sensibles à la casse&nbsp;: zzz vient avant AAA.
					</td>
				</tr>
				<tr class="alternate">
					<th>
						<label>A partir de</label>
					</th>
					<td colspan="2">
						<?php 
						if(is_null($ppo->options->inheading)){
							echo CopixZone::process ('heading|headingelement/headingelementchooser', array('inputElement'=>'inheading', 'linkOnHeading'=>true, 'arTypes'=>array('heading'), 'showAnchor'=>true));
						}else {
							echo CopixZone::process ('heading|headingelement/headingelementchooser', array('inputElement'=>'inheading', 'linkOnHeading'=>true, 'arTypes'=>array('heading'), 'showAnchor'=>true, 'selectedIndex'=>$ppo->options->inheading));
						}
						?>
					</td>
				</tr>
			</table>
			<p class="sbumit"><input name="submitfilter" type="submit" value="Filtrer les pages" /></p>
		</div>
	</fieldset>
</form>
<br />

<?php if (count ($ppo->elements) == 0) { ?>
	<div class="notice">
		<p>Aucun élément ne correspond à vos critères de recherche.</p>
	</div>
<?php } else { ?>
<a href="<?php echo _url('heading|seo|editelements', array('export'=>"csv")); ?>">Exporter la liste des urls</a>
<br /><br />
<?php echo $pagination ?>
<form action="<?php echo _url ('heading|seo|editElements') ?>" method="post">
	<input type="hidden" name="inheading" value="<?php echo $ppo->options->inheading; ?>" id="inheading" />
	<table class="CopixTable">
		<tr>
			<th style="width: 200px">Page</th>
			<th style="width: 100px">Champ</th>
			<th>Valeur</th>
			<th style="width: 30px">Lien</th>
		</tr>
		<?php
		$trClass = null;
		$elementsHELT = array ();
		$rowspan = (int)$ppo->options->displayTitle + (int)$ppo->options->displayDescription + (int)$ppo->options->displayURL;
		foreach ($ppo->elements as $index => $element) {
			$trClass = ($trClass == null) ? 'class="alternate"' : null;
			$isEditable = ($element->have_next_status == 0);
			?>
			<tr <?php echo $trClass ?>>
				<th>
					<?php echo ($element->caption_hei == null) ? $element->title_hei : $element->caption_hei ?>
					<?php if (!$isEditable) echo '<br /><font color="red">Page en cours de modification</font>'; ?>
				</th>
				<td style="vertical-align:top">
					<?php if($ppo->options->displayName){ ?>
						<label for="name_<?php echo $element->id_helt ?>">Nom</label><br />
					<?php } ?>
					<?php if($ppo->options->displayTitle){ ?>
						<label for="title_<?php echo $element->id_helt ?>">Titre</label><br />
					<?php } ?>
					<?php if($ppo->options->displayTitle){ ?>
						<label for="title_menus_<?php echo $element->id_helt ?>">Titre menus</label><br />
					<?php } ?>
					<?php if ( $ppo->options->displayURL ) { ?>
						<label for="URL_<?php echo $element->id_helt ?>">URL</label><br />
					<?php } ?>
					<?php if ( $ppo->options->displayDescription ) { ?>
						<label for="description_<?php echo $element->id_helt ?>">Description</label>
					<?php } ?>
				</td>
				<td style="vertical-align:top">
					<?php if($ppo->options->displayName){ ?>
						<input type="text" class="text" style="width: 100%" id="name_<?php echo $element->id_helt ?>" name="elements[<?php echo $element->id_helt ?>][caption_hei]" value="<?php echo $element->caption_hei ?>" <?php if (!$isEditable) { echo 'disabled="disabled"'; } ?> />
					<?php } ?>
					<?php if($ppo->options->displayTitle){ ?>
						<input type="text" class="text" style="width: 100%" id="title_<?php echo $element->id_helt ?>" name="elements[<?php echo $element->id_helt ?>][title_hei]" value="<?php echo $element->title_hei ?>" <?php if (!$isEditable) { echo 'disabled="disabled"'; } ?> />
					<?php } ?>
					<?php if($ppo->options->displayTitleMenu){ ?>
						<input type="text" class="text" style="width: 100%" id="title_menus_<?php echo $element->id_helt ?>" name="elements[<?php echo $element->id_helt ?>][menu_caption_hei]" value="<?php echo $element->menu_caption_hei ?>" <?php if (!$isEditable) { echo 'disabled="disabled"'; } ?> />
					<?php } ?>
					<?php if ( $ppo->options->displayURL ) { ?>
						<input type="text" class="text" style="width: 100%" id="URL_<?php echo $element->id_helt ?>" name="elements[<?php echo $element->id_helt ?>][url_id_hei]" value="<?php echo $element->url_id_hei ?>" <?php if (!$isEditable) { echo 'disabled="disabled"'; } ?> />
					<?php } ?>
					<?php if ( $ppo->options->displayDescription ) { ?>
						<textarea id="description_<?php echo $element->id_helt ?>" name="elements[<?php echo $element->id_helt ?>][description_hei]" style="width: 100%" <?php if (!$isEditable) { echo 'disabled="disabled"'; } ?>><?php echo $element->description_hei ?></textarea>
					<?php } ?>
				</td>
				<td class="center">
					<a href="<?php echo _url ('heading||', array ('public_id' => $element->public_id_hei)) ?>" target="_blank" title="<?php echo $element->caption_hei; ?> (nouvelle fenêtre)"><img src="<?php echo _resource ('img/tools/link.png') ?>" alt="<?php echo $element->caption_hei; ?>" /></a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<p class="submit center">
		<input type="submit" value="Enregistrer les modifications" />
	</p>
</form>
<br />

<?php echo $pagination ?>
<?php } ?>
<?php _eTag ('endblock'); /*?>
<div style="text-align: right; width: 100%;">
	<a href="<?php echo _url('admin||'); ?>">
		<img alt="Retour" src="<?php echo _resource('img/tools/back.png') ?>"/> Retour
	</a>
</div>
*/ ?>