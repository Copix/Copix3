<table class="CopixTable">
	<tr>
		<th style="width: 140px">Nom en base</th>
		<th style="width: 140px">Propriété privée <span class="required">*</span></th>
		<th style="width: 140px">Suffixe méthodes <span class="required">*</span></th>
		<th>Description <span class="required">*</span></th>
		<th style="width: 1px">Type <span class="required">*</span></th>
		<th style="width: 1px"><img src="<?php echo _resource ('|img/caption.png') ?>" alt="Définir comme libellé principal" title="Définir comme libellé principal" /></th>
		<th style="width: 1px"><img src="<?php echo _resource ('|img/list.png') ?>" alt="Voir dans la liste des éléments" title="Voir dans la liste des éléments" /></th>
		<th style="width: 1px"><img src="<?php echo _resource ('|img/search.png') ?>" alt="Recherche possible sur ce champ" title="Recherche possible sur ce champ" /></th>
		<th style="width: 1px" class="last"><img src="<?php echo _resource ('|img/editable.png') ?>" alt="Modifiable" title="Modifiable" /></th>
	</tr>
	<?php
	$selectValues = array ('Chiffre' => array ('boolean' => 'Booléen', 'int' => 'Chiffre', 'stars' => 'Etoiles', 'position' => 'Position', 'status' => 'Statut'));
	if (CopixModule::isEnabled ('heading')) {
		$selectValues['CMS'] = array ('cms_page' => 'Sélection page', 'cms_heading' => 'Sélection rubrique');
	}
	$selectValues['Date'] = array ('date' => 'Date', 'datetime' => 'Date et heure', 'time' => 'Heure');
	$selectValues['Texte'] = array ('email' => 'E-mail', 'string' => 'Texte', 'tinymce' => 'TinyMCE', 'url' => 'URL', 'varchar' => 'VarChar');
	$selectValues['Autres'] = array ('id' => 'Identifiant', 'theme' => 'Sélection thème');
	foreach ($ppo->fields as $field) {
		?>
		<tr <?php _eTag ('trclass', array ('id' => 'fields')) ?>>
			<td><?php echo $field->name ?></td>
			<td><?php _eTag ('inputtext', array ('name' => 'field_' . $field->name . '_property', 'value' => $field->__COPIX__EXTRAS__->property, 'style' => 'width: 99%')) ?></td>
			<td><?php _eTag ('inputtext', array ('name' => 'field_' . $field->name . '_method', 'value' => $field->__COPIX__EXTRAS__->method, 'style' => 'width: 99%')) ?></td>
			<td><?php _eTag ('inputtext', array ('name' => 'field_' . $field->name . '_caption', 'value' => $field->caption, 'style' => 'width: 99%')) ?></td>
			<td>
				<?php
				_eTag ('select', array (
					'name' => 'field_' . $field->name . '_type',
					'emptyShow' => false,
					'extra' => 'onchange="onChangeType (\'' . $field->name . '\', this.value);"',
					'selected' => $field->__COPIX__EXTRAS__->type,
					'values' => $selectValues
				));
				?>
			</td>
			<td style="text-align: center">
				<input type="radio" name="field_caption" value="<?php echo $field->name ?>" />
			</td>
			<td style="text-align: center">
				<input type="checkbox" name="<?php echo 'field_' . $field->name . '_list' ?>" />
			</td>
			<td style="text-align: center">
				<input type="checkbox" name="<?php echo 'field_' . $field->name . '_searchable' ?>" checked="checked" />
			</td>
			<td style="text-align: center">
				<input type="checkbox" name="<?php echo 'field_' . $field->name . '_editable' ?>" <?php if ($field->__COPIX__EXTRAS__->isEditable) echo 'checked="checked"' ?> />
			</td>
		</tr>
	<?php } ?>
</table>

<script type="text/javascript">
<?php foreach ($ppo->fields as $field) { ?>
onChangeType ('<?php echo $field->name ?>', '<?php echo $field->__COPIX__EXTRAS__->type ?>');
<?php } ?>
</script>