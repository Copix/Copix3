<h2 class="first">En quoi consiste l'optimisation</h2>
L'optimisation d'un thème va copier toutes les ressources du répertoire www du thème, vers le répertoire www global.
<br />
Les ressources du répertoire www des modules installés seront également copiées vers le répertoire www global.
<br />
Seuls les fichiers .css ne seront pas copiés, car ils peuvent contenir le tag {copixresource}, parsé par resource.php.

<h2>Pourquoi il est intéressant d'optimiser</h2>
Le fait d'avoir des ressources dans un module, ou dans un thème, est pratique pour les packages (un seul répertoire : celui du module).
<br />
Ces ressources seront alors appelées via un fichier php : resource.php. Il va instancier une petite partie de Copix, mais le fait que
ce ne soit pas un fichier à envoyer mais un script PHP, il y a un temps d'execution à prendre en compte.
<br />
On peut passer de quelques centaines de millisecondes à un temps d'execution nul, ce qui n'est pas négligeable sur plusieurs ressources.

<h2>Droits requis et limitations</h2>
Le temps de cette opération, le répertoire www/themes/ doit avoir les droits d'écriture.
Une fois l'optimisation terminée, pensez à remettre uniquement les droits de lecture sur www/themes/.
<br /><br />
L'opération d'optimisation copie les fichiers à l'instant T. Si un des modules change ses ressources, cette répercution n'est pas automatique,
il faudra alors refaire l'optimisation.
<br />
Le répertoire créé par l'optimisation sera lu en premier lors de la recherche d'une ressource, aussi, lors de mises à jour des ressources
d'un module, elles ne seront pas toujours visibles (puisque prises dans ce nouveau répertoire, et non dans le module). Il faut refaire
l'optimisation pour mettre à jour ces répertoires.

<br /><br />
<?php if ($ppo->is_writable) { ?>
	<center>
		<input type="checkbox" id="overwrite" checked="checked"
		/><label for="overwrite">Ecraser les fichiers dans www/themes/<?php echo $ppo->theme->getId () ?>/ si ils existent</label>
		<br />
		<input type="button" value="Lancer l'optimisation"
			onclick="javascript: document.location = '<?php echo _url ('admin|themes|doOptimize', array ('theme' => $ppo->theme->getId ())) ?>&overwrite=' + $ ('overwrite').checked;" />
	</center>
<?php } else { ?>
	<div class="error">
		Le répertoire www/themes/ n'a pas les droits d'écriture. Changez ces droits et rafraichissez cette page.
	</div>
<?php } ?>

<?php _eTag ('back', array ('url' => 'admin|themes|')); ?>