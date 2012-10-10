<?php
_tag ('mootools', array ('plugins'=>'contextmenu'));
CopixHTMLHeader::addCSSLink (_resource ('developerbar|styles/developerbar.css'));
CopixHTMLHeader::addJSLink (_resource ('developerbar|developerbar.js'));
$jsCode = '
tempBar = Copix.create_developerBar (true);
tempBar.setId (\'' . $idBar . '\');
tempBar.setIsLoaded (\'logs\', ' . (($logs_ajax) ? 'false' : 'true') . ');
tempBar.setIsLoaded (\'logs\', ' . (($querys_ajax) ? 'false' : 'true') . ');
tempBar.setIsLoaded (\'logs\', ' . (($vars_ajax) ? 'false' : 'true') . ');
tempBar.setIsLoaded (\'logs\', ' . (($errors_ajax) ? 'false' : 'true') . ');
tempBar.setPosition (' . $positionX . ', ' . $positionY . ');
tempBar.setPositioning (\'' . CopixUserPreferences::get ('developerbar|positioning') . '\');
';
if ($show != null) {
	$jsCode .= 'tempBar.show (\'' . $show . '\');';
}
if ($errors_enabled && $errors_count > 0) {
	$jsCode .= 'tempBar.highlight.delay (800, tempBar, \'errors\');';
}

CopixHTMLHeader::addJSDOMReadyCode ($jsCode);
?>

<div class="developerBar" id="<?php echo $idBar ?>" style="left: <?php echo $positionX ?>px; top: <?php echo $positionY ?>px">
	<?php
	$isFirst = true;
	if ($isMain) {
		$isFirst = false;
		?>
		<span class="developerBarUserPreferences" id="<?php echo $idBar ?>preferences" title="Configuration"></span>
		<?php _eTag ('CopixZone', array ('process' => 'admin|UserPreferences', 'modulePref' => 'developerbar', 'clicker' => $idBar . 'preferences', 'tabs' => true, 'ajaxSave' => false)); ?>
		<span class="developerBarGroupSeparator"></span>
		<span class="developerBarClearTemp" id="<?php echo $idBar ?>clearTemp" title="Vider les fichiers temporaires"></span>
		<?php
		$jsCode = '$ (\'' . $idBar . 'clearTemp\').addEvent (\'click\', function () { document.location = \'' . _url ('admin|temp|doClear', array ('url_return' => CopixURL::getRequestedUrl ())) . '\'; });';
		CopixHTMLHeader::addJSDOMReadyCode ($jsCode);
	}

	$contents = array (
		'vars' => array ('', 'Variables serveur'),
		'memory' => array ($memory['script'] . ' Kb', 'Mémoire du script'),
		'timers' => array ($timers['global'], 'Temps d\'execution'),
		'querys' => array ($querys_count, 'Requêtes'),
		'logs' => array ($logs_count, 'Logs'),
		'errors' => array ($errors_count, 'Erreurs PHP')
	);
	foreach ($contents as $name => $infos) {
		if (${$name . '_enabled'}) {
			if (!$isFirst) {
				echo '<span class="developerBarGroupSeparator"></span>';
			}
			echo '<span class="developerBarGroup' . ucfirst ($name) . '" id="' . $idBar . 'group_' . $name . '" title="' . $infos[1] . '" ';
			echo 'onclick="Copix.get_developerBar (\'' . $idBar . '\').show (\'' . $name . '\')">' . $infos[0] . '</span>';
			$isFirst = false;
		}
	}
	?>
</div>

<?php if ($memory_enabled) { ?>
	<div class="developerBarContent" id="<?php echo $idBar ?>content_memory">
		<table class="CopixVerticalTable">
			<tr>
				<th>Mémoire du script</th>
				<td><?php echo $memory['script'] ?> Kb</td>
			</tr>
			<tr class="alternate">
				<th>Mémoire autorisée</th>
				<td><?php echo $memory['limit'] ?></td>
			</tr>
			<tr>
				<th>Mémoire de PHP</th>
				<td><?php echo $memory['php'] ?> Kb</td>
			</tr>
		</table>
	</div>
<?php } ?>

<?php if ($timers_enabled) { ?>
	<div class="developerBarContent" id="<?php echo $idBar ?>content_timers">
		<table class="CopixVerticalTable">
			<tr>
				<th>Temps Copix</th>
				<td><?php echo $timers['copix'] ?> sec</td>
			</tr>
			<tr class="alternate">
				<th>Temps de l'action</th>
				<td><?php echo $timers['action'] ?> sec</td>
			</tr>
			<tr>
				<th>Temps total</th>
				<td><?php echo $timers['global'] ?> sec</td>
			</tr>
		</table>
	</div>
<?php } ?>

<?php if ($querys_enabled) { ?>
	<div class="developerBarContent" id="<?php echo $idBar ?>content_querys">
		<?php
		if (!$querys_ajax) {
			echo CopixZone::process ('developerbar|DeveloperBarValues', array ('values' => $querys, 'type' => 'querys', 'idBar' => $idBar));
		} else {
			echo '<img src="' . _resource ('img/tools/load.gif') . '" alt="Chargement ..." title="Chargement ..." />';
		}
		?>
	</div>
<?php } ?>

<?php if ($logs_enabled) { ?>
	<div class="developerBarContent" id="<?php echo $idBar ?>content_logs">
		<?php
		if (!$logs_ajax) {
			echo CopixZone::process ('developerbar|DeveloperBarValues', array ('values' => $logs, 'type' => 'logs', 'idBar' => $idBar));
		} else {
			echo '<img src="' . _resource ('img/tools/load.gif') . '" alt="Chargement ..." title="Chargement ..." />';
		}
		?>
	</div>
<?php } ?>

<?php if ($vars_enabled) { ?>
	<div class="developerBarContent" id="<?php echo $idBar ?>content_vars">
		<?php
		if (!$vars_ajax) {
			echo CopixZone::process ('developerbar|DeveloperBarValues', array ('values' => $vars, 'type' => 'vars', 'idBar' => $idBar));
		} else {
			echo '<img src="' . _resource ('img/tools/load.gif') . '" alt="Chargement ..." title="Chargement ..." />';
		}
		?>
	</div>
<?php } ?>

<?php if ($errors_enabled) { ?>
	<div class="developerBarContent" id="<?php echo $idBar ?>content_errors">
		<?php
		if (!$errors_ajax) {
			echo CopixZone::process ('developerbar|DeveloperBarValues', array ('values' => $errors, 'type' => 'errors', 'idBar' => $idBar));
		} else {
			echo '<img src="' . _resource ('img/tools/load.gif') . '" alt="Chargement ..." title="Chargement ..." />';
		}
		?>
	</div>
<?php } ?>