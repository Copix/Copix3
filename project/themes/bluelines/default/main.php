<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="search" type="application/opensearchdescription+xml" title="Documentation Copix" href="<?php echo _url(); ?>goodies/copix.xml">
	<title><?php echo $TITLE_BAR; ?></title>
	<?php echo $HTML_HEAD; ?>
	<link rel="stylesheet" href="<?php echo _resource ("styles/copix.css.php"); ?>?copixurl=<?php echo _url (); ?>"	type="text/css" />
	<link rel="stylesheet" href="<?php echo _resource ("styles/theme.css.php"); ?>?copixurl=<?php echo _url (); ?>" type="text/css" />
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" width="980px" align="center">
	<tr>
		<td rowspan="3">
			<img src="<?php echo _resource ('img/logo_left.png') ?>" />
		</td>
		<td></td>
		<td valign="top" align="left" height="70px">
			<div style="margin-top:5px">
				<img src="<?php echo _resource ('img/logo_copix.png') ?>" />
			</div>
		</td>
		<td align="right" valign="bottom">
			<div style="align:right; margin-right: 10px; margin-bottom: 10px">
				<?php
				if (_currentUser ()->isConnected ()) {
					echo '<a href="' . _url ('auth|log|out') . '">Déconnexion [' . _currentUser ()->getCaption () . ']</a>';
					if (_currentUser ()->testCredential ('basic:admin')) {
						echo ' | <a href="' . _url ('admin||') . '">Administration</a>';
					}
				} else {
					echo '<a href="' . _url ('auth||') . '">Connexion</a>';
				}
				?>
				<div style="height:5px"></div>
				<?php
				if (CopixModule::isEnabled ('quicksearch')) { 
					echo CopixZone::process ('quicksearch|quicksearchform');
				}
				?>
			</div>
		</td>
		<td rowspan="3">
			<img src="<?php echo _resource ('img/logo_right.png') ?>" />
		</td>
	</tr>
	<tr>
		<td width="8px" height="25px">
			<img src="<?php echo _resource ('img/mainmenu_left.png') ?>" />
		</td>
		<td colspan="2" style="background-repeat: repeat-x; background-image:url(<?php echo _resource ('img/mainmenu_center.png') ?>)" width="100%">
			<div style="margin-top: -5px; margin-left: 5px;">
			<?php
			$isFirst = true;
			foreach ($menuItems as $caption => $url) {
				if (!$isFirst) {
					echo '&nbsp;&nbsp;<img src="' . _resource ('img/mainmenu_separator.png') . '" />&nbsp;&nbsp;';
				}
				echo '<a href="' . $url . '">' . $caption . '</a>';
				
				$isFirst = false;
			}
			?>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div style="height:19px"></div>
		</td>
	</tr>
	
	<tr>
		<td colspan="5" align="center">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td class="block_top_left"> </td>
					<td class="block_top"></td>
					<td class="block_top_right"></td>
				</tr>
				<tr>
					<td class="block_left"></td>
					<td class="block_content">
						<div style="margin-left: 5px; margin-right: 5px; margin-top: 5px;" width="100%">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td><img src="<?php echo _resource ('img/title_left.png') ?>" /></td>
									<td class="title">
										<div class="title">
											<?php echo $TITLE_PAGE; ?>
										</div>
									</td>							
									<td><img src="<?php echo _resource ('img/title_right.png') ?>" /></td>
								</tr>
							</table>
							<div style="margin-left: 15px; margin-right: 10px" width="100%">
								<?php echo $MAIN; ?>
							</div>
						</div>
					</td>
					<td class="block_right"></td>
				</tr>
				<tr>
					<td class="block_bottom_left"></td>
					<td class="block_bottom"></td>
					<td class="block_bottom_right"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br />

<!--<?php echo $TITLE_PAGE; ?>-->

</body>
</html>