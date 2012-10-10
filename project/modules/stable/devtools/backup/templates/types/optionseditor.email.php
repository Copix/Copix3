<?php _eTag ('beginblock', array ('title' => 'Configuration de l\'envoi du mail')) ?>
<table class="CopixVerticalTable">
	<tr <?php _eTag ('trclass') ?>>
		<th style="width: 150px">Destinataire <span class="required">*</span></th>
		<th style="width: 1px"><?php _eTag ('popupinformation', array (), 'Adresse e-mail du destinataire. Utiliser la virgule comme séparateur pour plusieurs destinataires.') ?></th>
		<td><input type="text" name="to" value="<?php echo $type->getTo () ?>" style="width: 99%" class="inputText" /></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Copie cachée</th>
		<th><?php _eTag ('popupinformation', array (), 'Adresse e-mail du destinataire de la copie cachée. Utiliser la virgule comme séparateur pour plusieurs destinataires.') ?></th>
		<td><input type="text" name="bcc" value="<?php echo $type->getBCC () ?>" style="width: 99%" class="inputText" /></td>
	</tr>
	<tr <?php _eTag ('trclass') ?>>
		<th>Sujet du mail <span class="required">*</span></th>
		<th><?php _eTag ('popupinformation', array (), 'Variables disponibles :<ul><li><b>%BASEURL%</b> : adresse du site</li><li><b>%TIME%</b> : heure de la sauvegarde</li></ul>') ?></th>
		<td><input type="text" name="subject" value="<?php echo $type->getSubject () ?>" style="width: 99%" class="inputText" /></td>
	</tr>
</table>
<?php _eTag ('endblock') ?>