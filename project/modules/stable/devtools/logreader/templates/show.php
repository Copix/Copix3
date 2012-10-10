<h2 class="first">Fichier de log</h2>

<select id="rotation" onchange="javascript: document.location = '<?php echo _url ('logreader||show', array ('file' => $ppo->file->getId (), 'rotation' => null)) ?>' + this.value">
	<option value=""><?php echo $ppo->file->getFileName () ?></option>
	<?php
	foreach ($ppo->rotations as $rotation) {
		$selected = ($ppo->rotation == $rotation->getFileName ()) ? 'selected="selected"' : null;
		?>
		<option value="<?php echo $rotation->getFileName () ?>" <?php echo $selected ?>><?php echo $rotation->getFileName () ?></option>
	<?php } ?>
</select>

<h2>Dernières lignes ajoutées</h2>

<?php if (count ($ppo->lastLines) == 0) { ?>
	Pas de nouvelles lignes.
<?php } else { ?>
	<table class="CopixTable">
		<tr>
			<th style="width: 40px">Ligne</th>
			<th style="width: 100px">Date</th>
			<th>Contenu</th>
		</tr>
		<?php foreach ($ppo->lastLines as $line) { ?>
			<tr <?php _eTag ('cycle', array ('values' => ',class="alternate"')) ?>>
				<td><?php echo $line->getIndex () ?></td>
				<td><?php echo $line->getDate () ?></td>
				<td><?php echo $line->getShortText () ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>

<h2>Contenu du log</h2>

<center>
	<?php if ($ppo->first > 1) { ?>
		<a href="<?php echo _url ('logreader||show', array ('file' => $ppo->file->getId (), 'rotation' => $ppo->rotation, 'first' => max ($ppo->first - 20, 1))) ?>">&lt;&lt; Précédents</a>
		&nbsp;&nbsp;
	<?php } ?>
	<?php if ($ppo->linesCount > $ppo->first + $ppo->linesPerPage) { ?>
		<a href="<?php echo _url ('logreader||show', array ('file' => $ppo->file->getId (), 'rotation' => $ppo->rotation, 'first' => $ppo->first + 20)) ?>">Suivants &gt;&gt;</a>
	<?php } ?>
</center>

<br />
<table class="CopixTable">
	<tr>
		<th style="width: 40px">Ligne</th>
		<th style="width: 100px">Date</th>
		<th>Contenu</th>
	</tr>
	<?php foreach ($ppo->lines as $line) { ?>
		<tr <?php _eTag ('cycle', array ('values' => ',class="alternate"')) ?>>
			<td><?php echo $line->getIndex () ?></td>
			<td><?php echo $line->getDate () ?></td>
			<td><?php echo $line->getShortText () ?></td>
		</tr>
	<?php } ?>
</table>

<br />
<center>
	<?php if ($ppo->first > 1) { ?>
		<a href="<?php echo _url ('logreader||show', array ('file' => $ppo->file->getId (), 'rotation' => $ppo->rotation, 'first' => max ($ppo->first - 20, 1))) ?>">&lt;&lt; Précédents</a>
		&nbsp;&nbsp;
	<?php } ?>
	<?php if ($ppo->linesCount > $ppo->first + $ppo->linesPerPage) { ?>
		<a href="<?php echo _url ('logreader||show', array ('file' => $ppo->file->getId (), 'rotation' => $ppo->rotation, 'first' => $ppo->first + 20)) ?>">Suivants &gt;&gt;</a>
	<?php } ?>
</center>

<br />
<?php _eTag ('back', array ('url' => 'logreader||')) ?>