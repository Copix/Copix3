<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'URL', 'icon' => _resource ('heading|img/togglers/url.png'))) ?>

<div class="element" style="height: auto;">
	<div class="elementContent">
		<div class="titleHeadingMenu">Base de l'URL</div>
		<input type="radio" name="base_url_inherited" id="base_url_inherited<?php echo $record->id_helt ?>" value="true"
			<?php if ($record->public_id_hei == 0 && $uniqueElement) { ?>disabled="disabled"<?php } ?>
			<?php if ($base_url_inherited_from !== false && ($record->public_id_hei != 0 || !$uniqueElement)) { ?>checked="checked"<?php } ?>
		/>
		<label for="base_url_inherited<?php echo $record->id_helt ?>">Hériter
			<?php if ($base_url_inherited_from) { ?>
				de "<?php echo $base_url_inherited_from ?>"
			<?php } else { ?>
				du parent.
			<?php } ?>
		</label>
		<?php if ($base_url_inherited_from) { ?>
			<br />(<?php echo $base_url ?>)
		<?php } ?>
		<br />
		<?php if ($uniqueElement) { ?>
			<input type="radio" name="base_url_inherited" id="base_url<?php echo $record->id_helt ?>" value="false"
				<?php if ($base_url_inherited_from === false || $record->public_id_hei == 0) { ?>checked="checked"<?php } ?>
			/>
		<?php } else { ?>
			<input type="radio" name="base_url_inherited" id="base_url<?php echo $record->id_helt ?>" value="false"
				<?php if ($base_url_inherited_from === false && $base_url > 0) { ?>checked="checked"<?php } ?>
			/>
		<?php } ?>
		<label for="base_url<?php echo $record->id_helt ?>">Définir à&nbsp;:</label>
		<br />
		<?php if ($uniqueElement) { ?>
			<?php _eTag ('inputtext', array ('id' => 'base_url', 'class' => 'longInputMenu', 'name' => 'base_url', 'value' => $base_url)) ?>

			<br /><br />
			<div class="titleHeadingMenu">URL de fin</div>
			<?php _eTag ('inputtext', array ('id' => 'url_id', 'class' => 'longInputMenu', 'name' => 'url_id', 'value' => $record->url_id_hei)) ?>
		<?php } else { ?>
			<?php _eTag ('inputtext', array ('id' => 'base_url', 'class' => 'text', 'name' => 'base_url', 'size' => 34, 'value' => ($base_url > 0) ? $base_url : '******')) ?>
		<?php } ?>
	</div>
 </div>