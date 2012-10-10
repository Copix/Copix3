<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="copix" />
<title>[Installation Copix] <?php echo $TITLE; ?></title>
<link rel="stylesheet" href="theme/styles.css" type="text/css" />
</head>
<body>
<div id="banner">
	<img src="theme/img/logo.png" alt="Copix" />
	<h1><?php echo $TITLE; ?></h1>
</div>
<div id="maincontent">
	<center>
		<?php
		$etapes = array (
			'index.php' => array ('caption' => 'Vérifications', 'icon' => 'verifications.png'),
			'database.php' => array ('caption' => 'Base de données', 'icon' => 'database.png'),
			'config.php' => array ('caption' => 'Configuration', 'icon' => 'config.png'),
			'install.php' => array ('caption' => 'Installation', 'icon' => 'install.png')
		);
		$index = 0;
		foreach ($etapes as $url => $etape) {
			$index++;

			$showLink = false;
			if ($index == 1) {
				$showLink = true;
			} else if ($index == 2) {
				$showLink = (count ($verificationsErrors) == 0);
			} else if ($index == 3) {
				$showLink = (count ($verificationsErrors) == 0 && count ($databaseErrors) == 0);
			} else if ($index == 4) {
				$showLink = (count ($verificationsErrors) == 0 && count ($databaseErrors) == 0 && count ($configErrors) == 0);
			}

			if ($index == $ETAPE) {
				echo '<b>';
			} else if ($showLink) {
				echo '<a href="' . $url . '">';
			}

			echo '<img src="theme/img/' . $etape['icon'] . '" alt="' . $etape['caption'] . '" title="' . $etape['caption'] . '" /> ' . $etape['caption'];

			if ($index == $ETAPE) {
				echo '</b>';
			} else if ($showLink) {
				echo '</a>';
			}

			if ($index < count ($etapes)) {
				echo '&nbsp;&nbsp;&nbsp;';
			}
		}
		?>
	</center>
	<hr />

	<div id="content">