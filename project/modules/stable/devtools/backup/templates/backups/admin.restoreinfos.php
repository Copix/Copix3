<?php echo CopixZone::process ('backup|BackupInfos', array ('xml' => $ppo->backupxml, 'restore' => true, 'backupFilesPath' => $ppo->backupFilesPath)) ?>

<br />
<?php _eTag ('back', array ('url' => 'backup||restore')) ?>