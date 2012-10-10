<?php _eTag ('error', array ('message' => $ppo->errors)) ?>

<?php _eTag ('beginblock', array ('title' => 'Envoi d\'une archive ZIP', 'isFirst' => true)) ?>
Si vous avez généré une sauvegarde sans créer automatiquement d'archive au format ZIP, vous devez en faire une contenant tous les fichiers de la sauvegarde.
<br />
Sinon, envoyez directement l'archive ZIP.
<br /><br />
<form enctype="multipart/form-data" method="POST" action="<?php echo _url ('backup||restoreinfos') ?>">
<input type="hidden" name="type" value="upload" />
<input type="file" name="zip" />
<br /><br />
<center><input type="submit" value="Informations sur la sauvegarde" /></center>
</form>
<?php _eTag ('endblock') ?>

<?php _eTag ('beginblock', array ('title' => 'Sauvegarde sur le serveur')) ?>
Vous pouvez indiquer un répertoire du serveur pour utiliser les fichiers présents, ou le chemin de l'archive ZIP.
<form method="POST" action="<?php echo _url ('backup||restoreinfos') ?>">
<input type="hidden" name="type" value="local" />
<br />
<input type="text" name="path" value="<?php echo $ppo->path ?>" class="inputText" size="80" />
<br /><br />
<center><input type="submit" value="Informations sur la sauvegarde" /></center>
</form>
<?php _eTag ('endblock') ?>

<?php _eTag ('back', array ('url' => 'backup|profiles|')) ?>