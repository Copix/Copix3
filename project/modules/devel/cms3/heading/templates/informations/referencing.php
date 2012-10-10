<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Référencement', 'icon' => _resource ('heading|img/seo.png'))) ?>

<div class="element" style="height: auto;">
	<div class="elementContent">
		<input type="radio" name="meta_robots_inherited" id="meta_robots_inherited_1" value="1"
			<?php if ($record->robots_hei === null) { ?>checked="checked"<?php } ?>
			<?php if ($robots_inherited === false) { ?>disabled="disabled"<?php } ?>
		/>
		<label for="meta_robots_inherited_1">
			Hériter de la rubrique parente
			<?php if ($robots_inherited !== false) { ?>
				(<?php echo $robots_inherited ?>)
			<?php } ?>
		</label>
		<br />
		<input type="radio" name="meta_robots_inherited" id="meta_robots_inherited_0" value="0"
			<?php if ($record->robots_hei !== null) { ?>checked="checked"<?php } ?>
		/>
		<label for="meta_robots_inherited_0">Définir à :</label>
		<table id="meta_robots_table" width="100%">
			<tr>
				<td>
					<input type="checkbox" name="meta_robots[]" id="meta_robots_noindex" value="noindex"
						<?php if ($record->robots_hei === null && $robots_inherited !== false) { ?>disabled="disabled"<?php } ?>
						<?php if ($record->robots_hei != null && $record->robots_hei->noindex) { ?>checked="checked"<?php } ?>
					/>
					<label for="meta_robots_noindex">
						<?php _eTag ('popupinformation', array ('displayimg' => false, 'text' => 'noindex'), 'Empêche l\'indexation de la page') ?>
					</label>
				</td>
				<td>
					<input type="checkbox" name="meta_robots[]" id="meta_robots_nofollow" value="nofollow"
						<?php if ($record->robots_hei === null && $robots_inherited !== false) { ?>disabled="disabled"<?php } ?>
						<?php if ($record->robots_hei != null && $record->robots_hei->nofollow) { ?>checked="checked"<?php } ?>
					/>
					<label for="meta_robots_nofollow">
						<?php _eTag ('popupinformation', array ('displayimg' => false, 'text' => 'nofollow'), 'Empêche les robots de suivre les liens de cette page') ?>
					</label>
				</td>
				<td>
					<input type="checkbox" name="meta_robots[]" id="meta_robots_noarchive" value="noarchive"
						<?php if ($record->robots_hei === null && $robots_inherited !== false) { ?>disabled="disabled"<?php } ?>
						<?php if ($record->robots_hei != null && $record->robots_hei->noarchive) { ?>checked="checked"<?php } ?>
					/>
					<label for="meta_robots_noarchive">
						<?php _eTag ('popupinformation', array ('displayimg' => false, 'text' => 'noarchive'), 'Empêche Google d\'afficher le lien "En cache" associé à cette page') ?>
					</label>
				</td>
			</tr>
		</table>
	</div>
</div>

<?php
$js = <<<JS
$ ('meta_robots_inherited_1').addEvent ('click', function () {
	if (this.checked == true) {
		$ ('meta_robots_table').getElements ('input[id^=meta_robots_]').set ('disabled', true);
	} else {
		$ ('meta_robots_table').getElements ('input[id^=meta_robots_]').set ('disabled', false);
	}
});
$ ('meta_robots_inherited_0').addEvent ('click', function () {
	if (this.checked == true) {
		$ ('meta_robots_table').getElements ('input[id^=meta_robots_]').set ('disabled', false);
	} else {
		$ ('meta_robots_table').getElements ('input[id^=meta_robots_]').set ('disabled', true);
	}
});
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);