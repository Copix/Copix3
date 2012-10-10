<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<form action="<?php echo _url ('comments|groups|doEdit') ?>" method="POST">
<input type="hidden" name="id" value="<?php echo $ppo->element->getId () ?>" />
<input type="hidden" name="mode" value="<?php echo $ppo->mode ?>" />

<?php _eTag ('beginblock', array ('title' => _i18n ('comments|commentsgroups.template.adminEdit.informations'), 'isFirst' => true)) ?>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" >Identifiant <?php if ($ppo->mode == 'add') { ?><span class="required">*</span><?php } ?></th>
		<td>
			<?php
			if ($ppo->mode == 'add') {
				_eTag ('inputtext', array ('name' => 'id', 'value' => $ppo->element->getId (), 'style' => 'width: 150px', 'error' => isset ($ppo->errors['id'])));
			} else {
				echo $ppo->element->getId ();
			}
			?>
		</td>
	</tr>
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" >Libellé <span class="required">*</span></th>
		<td><?php _eTag ('inputtext', array ('name' => 'caption', 'value' => $ppo->element->getCaption (), 'style' => 'width: 99%', 'error' => isset ($ppo->errors['caption']))) ?></td>
	</tr>
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" >Auteur requis <span class="required">*</span></th>
		<td><?php _eTag ('radiobutton', array ('name' => 'authorRequired', 'values' => array ('true' => _i18n ('copix:common.buttons.yes'), 'false' => _i18n ('copix:common.buttons.no')), 'selected' => ($ppo->element->isAuthorRequired () ? 'true' : 'false'))) ?></td>
	</tr>
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" >Site web requis <span class="required">*</span></th>
		<td><?php _eTag ('radiobutton', array ('name' => 'websiteRequired', 'values' => array ('true' => _i18n ('copix:common.buttons.yes'), 'false' => _i18n ('copix:common.buttons.no')), 'selected' => ($ppo->element->isWebsiteRequired () ? 'true' : 'false'))) ?></td>
	</tr>
	<tr <?php _eTag ('trclass', array ('id' => 'editElement')) ?>>
		<th style="width: 140px; vertical-align: top" class="last">E-mail requis <span class="required">*</span></th>
		<td><?php _eTag ('radiobutton', array ('name' => 'emailRequired', 'values' => array ('true' => _i18n ('copix:common.buttons.yes'), 'false' => _i18n ('copix:common.buttons.no')), 'selected' => ($ppo->element->isEmailRequired () ? 'true' : 'false'))) ?></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 33%"></td>
		<td style="text-align: center"><?php _eTag ('button', array ('action' => 'save')) ?></td>
		<td style="width: 33%; text-align: right"><?php _eTag ('back', array ('url' => _url ('comments|groups|'))) ?></td>
	</tr>
</table>
</form>
