<?php
$clicker = uniqid ('themeChooser');
echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Thème graphique', 'icon' => _resource ('heading|img/togglers/theme.png')));
		?>

<div class="element">
	<div class="elementContent">
		<label><input type="checkbox" name="theme_use_parent" id="theme_use_parent" /> <?php if ($record->public_id_hei != 0) echo 'Utiliser le thème de la rubrique parente'; else echo 'Utiliser le thème configuré dans Copix' ?></label>
		<br /><br />
		<table class="CopixVerticalTable" id="TableThemeChooser">
			<tr <?php _eTag ('trclass') ?>>
				<th>Définit par</th>
				<td>
					<?php if ($theme_inherited_from === null) { ?>
						<a href="<?php echo _url ('admin|theme|') ?>">Configuration de Copix</a>
					<?php } else if ($theme_inherited_from === false) { ?>
						Cet élément
					<?php } else { ?>
						<a href="<?php echo _url ('heading|element|', array ('heading' => $theme_inherited_from->public_id_hei)) ?>"><?php echo $theme_inherited_from->caption_hei ?></a>
					<?php } ?>
				</td>
			</tr>
			<tr <?php _eTag ('trclass') ?>>
				<th style="vertical-align: top">Thème</th>
				<td id="td_themeChooser"><?php _eTag ('themechooser', array ('input' => 'theme_id', 'selected' => $theme->getId (), 'clicker' => $clicker)); ?></td>
			</tr>
			<tr <?php _eTag ('trclass') ?>>
				<th class="last">Gabarit</th>
				<td>
					
					<?php foreach ($themes as $themeInfos) { ?>
						<div id="template_<?php echo $themeInfos->getId () ?>" class="theme_template" rel="<?php echo $themeInfos->getId () ?>">
							<select name="theme_template_<?php echo $themeInfos->getId () ?>" id="theme_template_<?php echo $themeInfos->getId () ?>">
								<?php foreach ($themeInfos->getTemplates () as $id => $caption) { ?>
									<option value="<?php echo $id ?>" <?php if ($theme_template == 'default|' . $id) echo 'selected="selected"'?>><?php echo $caption ?></option>
								<?php } ?>
							</select>
						</div>
					<?php } ?>
				</td>
			</tr>
		</table>
	</div>
</div>

<?php
$js = <<<JS
$ ('theme_use_parent').addEvent ('change', function (pEl) {
	var theme_id = $ ('theme_id').value;
	$$ ('.theme_template').each (function (pElement) {
		$ ('theme_template_' + pElement.get ('rel')).disabled = pEl.target.checked;
	});
	$ ('$clicker').style.cursor = (pEl.target.checked) ? 'default' : 'pointer';
	$ ('TableThemeChooser').setStyle ('opacity', (pEl.target.checked) ? 0.5 : 1);
});

$ ('theme_id').addEvent ('change', function (pEl) {
	var theme_id = $ ('theme_id').value;
	$$ ('.theme_template').each (function (pEl) {
		pEl.setStyle ('display', (theme_id == pEl.get ('rel')) ? 'block' : 'none');
	});
});

$ ('theme_id').fireEvent ('change', {target: $ ('theme_id')});
JS;

if (is_object ($theme_inherited_from) || $theme_inherited_from === null) {
	$js .= '$ (\'theme_use_parent\').checked = true;';
	$js .= '$ (\'theme_use_parent\').fireEvent (\'change\', {target: $ (\'theme_use_parent\')});';
}
CopixHTMLHeader::addJSDOMReadyCode ($js);