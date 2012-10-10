<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<form action="<?php echo _url ('comments|comments|doEdit') ?>" method="POST">
<input type="hidden" name="id" value="<?php echo $ppo->element->getId () ?>" />
<input type="hidden" name="mode" value="<?php echo $ppo->mode ?>" />

<?php _eTag ('beginblock', array ('title' => _i18n ('comments|comments.template.adminEdit.informations'), 'isFirst' => true)) ?>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" >Auteur</th>
		<td><?php _eTag ('inputtext', array ('name' => 'author', 'value' => $ppo->element->getAuthor (), 'style' => 'width: 99%', 'error' => isset ($ppo->errors['author']))) ?></td>
	</tr>
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" >Site web</th>
		<td><?php _eTag ('inputtext', array ('name' => 'website', 'value' => $ppo->element->getWebsite (), 'style' => 'width: 99%', 'error' => isset ($ppo->errors['website']))) ?></td>
	</tr>
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" >E-mail</th>
		<td><?php _eTag ('inputtext', array ('name' => 'email', 'value' => $ppo->element->getEmail (), 'style' => 'width: 99%', 'error' => isset ($ppo->errors['email']))) ?></td>
	</tr>
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" class="last">Commentaire <span class="required">*</span></th>
		<td><?php _eTag ('textarea', array ('name' => 'value', 'value' => $ppo->element->getComment (), 'style' => 'width: 99%; height: 80px', 'error' => isset ($ppo->errors['value']))) ?></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 33%"></td>
		<td style="text-align: center"><?php _eTag ('button', array ('action' => 'save')) ?></td>
		<td style="width: 33%; text-align: right"><?php _eTag ('back', array ('url' => _url ('comments|comments|'))) ?></td>
	</tr>
</table>
</form>
