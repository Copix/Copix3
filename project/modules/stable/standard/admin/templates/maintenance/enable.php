Le mode maintenance passe le site dans un mode ou seul l'administrateur pourra accéder au site,
pour mettre à jour des modules, vérifier que le site fonctionne après une mise à jour des fichiers, etc.

<h2>Ce que les utilisateurs verront</h2>
Les utilisateurs verront la page <a href="<?php echo _url ('admin|maintenance|showFrontMaintenance') ?>" onclick="window.open('<?php echo _url ('admin|maintenance|showFrontMaintenance') ?>','_blank');return false;" >default|maintenancefront.php</a> uniquement, sans le contenu du site.

<h2>Ce que l'administrateur verra</h2>
L'administrateur verra le site avec pour template principal <a href="<?php echo _url ('admin|maintenance|showAdminMaintenance') ?>" onclick="window.open('<?php echo _url ('admin|maintenance|showAdminMaintenance') ?>','_blank');return false;" >default|maintenance.php</a> au lieu de default|main.php.

<h2>Comment se connecter en admin avec le mode maintenance</h2>
Si vous vous déconnectez sans désactiver le mode maintenance du site, pour vous reconnecter,
il faut ouvrir n'importe quelle page en ajoutant le paramètre <b>maintenance=<?php echo $ppo->maintenanceParam ?></b>
 (valeur configurable dans le module default).
 <br />
 Vous aurez ensuite accès au site avec un thème simplifié, vous permettant d'aller où vous voulez sans risquer d'avoir un problème de thème.

<br /><br /><br />
<div style="text-align: center;" ><?php _eTag ('button', array ('img' => 'img/tools/locked.png', 'caption' => 'Activer le mode maintenance', 'url' => 'admin|maintenance|enable')) ?></div>
<?php _eTag ('back', array('url'=>'admin||')); ?>