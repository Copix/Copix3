<?php _eTag ('error', array ('message' => array ('Erreur 1', 'Erreur 2', 'Erreur 3'))) ?>
<?php _eTag ('notification', array ('message' => 'Message de notification')) ?>

<?php _eTag ('beginblock', array ('isFirst' => true, 'title' => 'Formulaire')) ?>
<table class="CopixTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 150px">inputtext</th>
		<td><?php _eTag ('inputtext', array ('name' => 'inputtext1')) ?></td>
		<td><?php _eTag ('inputtext', array ('name' => 'inputtext2', 'error' => true)) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>checkbox</th>
		<td><?php _eTag ('checkbox', array ('name' => 'checkbox1', 'values' => array ('Valeur 1', 'Valeur 2'))) ?></td>
		<td><?php _eTag ('checkbox', array ('name' => 'checkbox2', 'values' => array ('Valeur 1', 'Valeur 2'), 'error' => true)) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>radiobutton</th>
		<td><?php _eTag ('radiobutton', array ('name' => 'radiobutton1', 'values' => array ('Valeur 1', 'Valeur 2'))) ?></td>
		<td><?php _eTag ('radiobutton', array ('name' => 'radiobutton2', 'values' => array ('Valeur 1', 'Valeur 2'), 'error' => true)) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>calendar2</th>
		<td><?php _eTag ('calendar2', array ('name' => 'calendar1')) ?></td>
		<td><?php _eTag ('calendar2', array ('name' => 'calendar2', 'error' => true)) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>select</th>
		<td><?php _eTag ('select', array ('name' => 'select1', 'values' => array ('Valeur 1', 'Valeur 2', 'Valeur 3'))) ?></td>
		<td><?php _eTag ('select', array ('name' => 'select2', 'values' => array ('Valeur 1', 'Valeur 2', 'Valeur 3'), 'error' => true)) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>multipleselect</th>
		<td><?php _eTag ('multipleselect', array ('name' => 'multipleselect1', 'values' => array ('Valeur 1', 'Valeur 2', 'Valeur 3'))) ?></td>
		<td><?php _eTag ('multipleselect', array ('name' => 'multipleselect2', 'values' => array ('Valeur 1', 'Valeur 2', 'Valeur 3'), 'error' => true)) ?></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>textarea</th>
		<td><?php _eTag ('textarea', array ('name' => 'textarea1', 'rows' => 2)) ?></td>
		<td><?php _eTag ('textarea', array ('name' => 'textarea2', 'rows' => 2, 'error' => true)) ?></td>
	</tr>
</table>
<center>
	<?php _eTag ('button', array ('action' => 'save')) ?>
	<?php _eTag ('button', array ('action' => 'cancel')) ?>
</center>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('title' => 'Liste d\'éléments')) ?>
<?php
function _generateLine ($pIndex) {
	$icons = array (
		'select', 'update', 'move_up', 'move_down', 'copy', 'cut', 'paste', 'enable', 'disable', 'delete'
	);
	?>
	<tr <?php _eTag ('trclass', array ('id' => '_generateLine')) ?>>
		<td>Libellé <?php echo $pIndex ?></td>
		<td>Valeur <?php echo $pIndex ?></td>
		<td>Texte <?php echo $pIndex ?></td>
		<?php foreach ($icons as $type) { ?>
			<td class="action"><?php _eTag ('copixicon', array ('type' => $type)) ?></td>
		<?php } ?>
	</tr>
	<?php
}
?>
<table class="CopixVerticalTable">
	<tr>
		<th>Libellé</th>
		<th>Valeur</th>
		<th>Texte</th>
		<th colspan="100">Actions</th>
	</tr>
	<?php
	_generateLine (1);
	_generateLine (2);
	_generateLine (3);
	?>
</table>
<?php _eTag ('endblock') ?>
	
<?php
_eTag ('beginblock', array ('title' => 'Icones'));
$icons = array (
	'add', 'cancel', 'clone', 'collapse', 'copy', 'cut', 'delete', 'disable', 'down', 'download', 'email', 'enable', 'expand', 'export', 'hidden', 'history', 'import', 'important',
	'link', 'locked', 'loupe', 'mail', 'move_down', 'move_up', 'new', 'paste', 'print', 'properties', 'publish', 'refresh', 'restore', 'search', 'select', 'selected',
	'send', 'show', 'split', 'test', 'trash', 'unlock', 'up', 'update', 'upload', 'valid', 'visible', 'warning'
);
foreach ($icons as $type) {
	_eTag ('copixicon', array ('type' => $type));
	echo '&nbsp;';
}
_eTag ('endblock');
?>
	
<?php _eTag ('beginblock', array ('title' => 'Autres')) ?>
<table class="CopixTable">
	<tr>
		<th style="width: 150px">copixwindow</th>
		<td>
			<span id="copixwindow_clicker" style="cursor: pointer">Cliquer pour afficher</span>
			<?php _eTag ('copixwindow', array ('name' => 'copixwindow1', 'clicker' => 'copixwindow_clicker', 'title' => 'Titre'), 'Contenu<br />Contenu 2<br />Contenu 3') ?>
		</td>
	</tr>
	<tr>
		<th style="width: 150px">popupinformation</th>
		<td>
			<?php _eTag ('popupinformation', array ('name' => 'popupinformation1', 'title' => 'Titre'), 'Contenu<br />Contenu 2<br />Contenu 3') ?>
		</td>
	</tr>
</table>

<br />
<center>
	Certaines éléments ne peuvent être affichés directement. Cliquez sur les boutons ci-dessous pour prévisualiser ces éléments.
	<br />
	<?php
	_eTag ('button', array ('caption' => 'Exception', 'img' => 'img/tools/error.png', 'url' => _url ('admin|themes|PreviewException', array ('theme' => $ppo->theme))));
	echo '&nbsp;&nbsp;';
	_eTag ('button', array ('caption' => 'GetError', 'img' => 'img/tools/error.png', 'url' => _url ('admin|themes|PreviewGetError', array ('theme' => $ppo->theme))));
	echo '&nbsp;&nbsp;';
	_eTag ('button', array ('caption' => 'GetConfirm', 'img' => 'img/tools/select.png', 'url' => _url ('admin|themes|PreviewGetConfirm', array ('theme' => $ppo->theme))));
	echo '&nbsp;&nbsp;';
	_eTag ('button', array ('caption' => 'GetInformation', 'img' => 'img/tools/information.png', 'url' => _url ('admin|themes|PreviewGetInformation', array ('theme' => $ppo->theme))));
	?>
</center>
<?php _eTag ('endblock') ?>
	
<?php _eTag ('back', array ('url' => 'admin|themes|')) ?>