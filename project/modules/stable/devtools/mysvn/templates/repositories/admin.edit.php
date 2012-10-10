<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<form action="<?php echo _url ('mysvn|repositories|doEdit') ?>" method="POST">
<input type="hidden" name="id" value="<?php echo $ppo->element->getId () ?>" />
<input type="hidden" name="mode" value="<?php echo $ppo->mode ?>" />

<?php _eTag ('beginblock', array ('title' => _i18n ('mysvn|repositories.template.adminEdit.informations'), 'isFirst' => true)) ?>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" >Libellé <span class="required">*</span></th>
		<td><?php _eTag ('inputtext', array ('name' => 'caption', 'value' => $ppo->element->getCaption (), 'style' => 'width: 99%', 'error' => isset ($ppo->errors['caption']))) ?></td>
	</tr>
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" class="last">Adresse <span class="required">*</span></th>
		<td><?php _eTag ('inputtext', array ('name' => 'url', 'value' => $ppo->element->getUrl (), 'style' => 'width: 99%', 'error' => isset ($ppo->errors['url']))) ?></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 33%"></td>
		<td style="text-align: center"><?php _eTag ('button', array ('action' => 'save')) ?></td>
		<td style="width: 33%; text-align: right"><?php _eTag ('back', array ('url' => _url ('mysvn|repositories|'))) ?></td>
	</tr>
</table>
</form>
