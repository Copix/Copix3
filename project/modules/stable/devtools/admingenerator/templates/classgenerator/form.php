<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<form action="<?php echo _url ('admingenerator|classgenerator|generate') ?>" method="POST">
<?php _eTag ('beginblock', array ('title' => 'Informations', 'isFirst' => true)) ?>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 15%">Module <span class="required">*</span></th>
		<td style="width: 35%">
			<?php
			_eTag ('select', array (
				'name' => 'moduleName',
				'values' => $ppo->modules,
				'error' => isset ($ppo->errors['module']),
				'selected' => $ppo->generator->getModule ()
			))
			?>
		</td>
		<th style="width: 15%">Répertoire</th>
		<td style="width: 35%"><?php _eTag ('inputtext', array ('name' => 'directory', 'style' => 'width: 99%', 'error' => isset ($ppo->errors['directory']), 'value' => $ppo->generator->getDirectory ())) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th class="last">Classe <span class="required">*</span></th>
		<td><?php _eTag ('inputtext', array ('name' => 'className', 'style' => 'width: 99%', 'error' => isset ($ppo->errors['className']), 'value' => $ppo->generator->getClassName ())) ?></td>
		<td colspan="2"></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('title' => 'Propriétés')) ?>
<table class="CopixTable" id="tbl_properties">
	<tr>
		<th style="width: 20%">Nom <span class="required">*</span></th>
		<th>Commentaire <span class="required">*</span></th>
		<th style="width: 15%">
			Type <span class="required">*</span>
			<?php
			$html = null;
			foreach ($ppo->types as $id => $caption) {
				$html .= '<li>' . $id . '</li>';
			}
			_eTag ('popupinformation', array (), 'Types spéciaux : <ul>' . $html . '</ul>')
			?>
		</th>
		<th style="width: 15%" class="last">Valeur par défaut</th>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 33%">
			<a href="#" onclick="javascript: addProperty (); return false;"
			   ><img src="<?php echo _resource ('img/tools/add.png') ?>" alt="Ajouter" title="Ajouter" style="vertical-align: middle" /> Ajouter une propriété
			</a>
		</td>
		<td style="width: 33%; text-align: center"><?php _eTag ('button', array ('caption' => 'Générer le code PHP', 'img' => 'admingenerator|img/admingenerator.png')) ?></td>
		<td><?php _eTag ('back', array ('url' => 'admin||')) ?></td>
	</tr>
</table>
</form>

<script type="text/javascript">
function addProperty (pName, pComment, pType, pValue) {
	var tbl = $ ('tbl_properties');
	var row = document.createElement ('tr');
	var rowId = Math.floor (Math.random () * 99999);
	row.id = rowId;

	var cell = document.createElement ('td');
	var input = document.createElement ('input');
	input.className = 'inputText';
	input.style.width = '99%';
	input.name = 'name_' + rowId;
	input.value = (pName != undefined) ? pName : '';
	cell.appendChild (input);
	row.appendChild (cell);

	var cell = document.createElement ('td');
	var input = document.createElement ('input');
	input.className = 'inputText';
	input.style.width = '99%';
	input.name = 'comment_' + rowId;
	input.value = (pComment != undefined) ? pComment : '';
	cell.appendChild (input);
	row.appendChild (cell);

	var cell = document.createElement ('td');
	var input = document.createElement ('input');
	input.className = 'inputText';
	input.style.width = '99%';
	input.name = 'type_' + rowId;
	input.value = (pType != undefined) ? pType : '';
	cell.appendChild (input);
	row.appendChild (cell);

	var cell = document.createElement ('td');
	var input = document.createElement ('input');
	input.className = 'inputText';
	input.style.width = '99%';
	input.name = 'value_' + rowId;
	input.value = (pValue != undefined) ? pValue : '';
	cell.appendChild (input);
	row.appendChild (cell);

	tbl.appendChild (row);
}

<?php foreach ($ppo->generator->getProperties () as $name => $infos) { ?>
	addProperty ('<?php echo $name ?>', '<?php echo $infos['comment'] ?>', '<?php echo $infos['type'] ?>', '<?php echo $infos['value'] ?>');
<?php } ?>
<?php if (count ($ppo->generator->getProperties ()) == 0) { ?>
	addProperty ();
<?php } ?>
</script>