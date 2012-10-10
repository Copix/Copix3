<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="generator" content="copix" />
<link rel="search" type="application/opensearchdescription+xml" title="Documentation Copix" href="<?php echo _url(); ?>goodies/copix.xml" />
<title><?php echo $TITLE_BAR; ?></title>
<?php echo $HTML_HEAD; ?>
<link rel="stylesheet" href="<?php echo _resource ('styles/copix.css'); ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo _resource ('styles/theme.css'); ?>" type="text/css" />
<meta name="theme_author" content="Steevan BARBOYON" />
<?php _tag ('mootools'); ?>
<script type="text/javascript">
imageLoading = new Image ();
imageLoading.src = '<?php echo _resource ('img/tools/load.gif'); ?>';
imageLogIn = new Image ();
imageLogIn.src = '<?php echo _resource ('img/tools/login.png'); ?>';

function showLoginForm (pHasErrors) {
	// bien laisser le form avant table, et /form avant /table, sinon si form est après table firefox n'envoi pas les valeurs des champs et IE oui
	inputClass = (pHasErrors) ? 'inputTextError' : 'inputText';
	html = '<table align="right">';
	html += '<tr>';
	html += '<td align="right">Login</td>';
	html += '<td><input type="text" name="login" id="themeLoginForm_login" class="' + inputClass + '" style="width: 100px" /></td>';
	html += '</tr>';
	html += '<tr>';
	html += '<td>Password</td>';
	html += '<td><input type="password" name="password" id="themeLoginForm_password" class="' + inputClass + '" style="width: 100px" /></td>';
	html += '<td width="15">';
	html += '<img id="imgSubmitLoginForm" style="cursor: pointer"; onclick="javascript: sendLogIn ();" src="<?php echo _resource ('img/tools/login.png'); ?>" alt="Connexion" />';
	html += '</td>';
	html += '</tr>';
	html += '</table>';
	$ ('tdFormLogin').innerHTML = html;
}

function showLogged (pUser, pIsAdmin) {
	html = '<a href="<?php echo _url ('auth|log|out'); ?>">Déconnexion [' + pUser + ']</a>';
	if (pIsAdmin) {
		html += ' | <a href="<?php echo _url ('admin||'); ?>">Administration</a>';
	}
	$ ('tdFormLogin').innerHTML = html;
}

function sendLogIn () {
	$ ('imgSubmitLoginForm').src = imageLoading.src;
	$ ('imgSubmitLoginForm').disabled = true;
	$ ('imgSubmitLoginForm').onclick = '';

	new Ajax ('<?php echo _url ('auth|log|AjaxIn'); ?>', {
		method: 'post',
		data: {testCredential0: 'basic:admin', login: $ ('themeLoginForm_login').value, password: $ ('themeLoginForm_password').value},
		onComplete: function (pResponse) {
			if (pResponse.substring (0, 4) == 'true') {
				showLogged ($ ('themeLoginForm_login').value, (pResponse == 'true|true'));
			} else {
				showLoginForm (true);
			}
		},
		onFailure: function (pResponse) {
			alert ('Ajax error : ' + pResponse.responseText);
			showLoginForm (true);
		}
	}).request ();
	$ ('themeLoginForm_login').disabled = true;
	$ ('themeLoginForm_password').disabled = true;
}
</script>
</head>
<body>

<table class="mainContainer">
	<tr>
		<td rowspan="3" class="logoToukan">
			<img src="<?php echo _resource ('img/logo_left.png') ?>" />
		</td>
		<td></td>
		<td class="logoCopix">
			<a href="<?php echo _url () ?>"><img src="<?php echo _resource ('img/logo_copix.png') ?>" /></a>
		</td>
		<td class="logoCenter" id="tdFormLogin"></td>
		<td rowspan="3" class="logoRight">
			<img src="<?php echo _resource ('img/logo_right.png') ?>" />
		</td>
	</tr>
	<tr>
		<td class="mainMenu_left">
			<img src="<?php echo _resource ('img/mainmenu_left.png') ?>" />
		</td>
		<td colspan="2" class="mainMenu_content">
			<?php
			if (_currentUser ()->testCredential ('basic:admin')) {
				$menus = array (
					_url () => array ('icon' => _resource ('img/tools/home.png'), 'caption' => 'Accueil'),
					_url ('admin|install|manageModules') => array ('icon' => _resource ('admin|img/icon/module.png'), 'caption' => 'Modules'),
					_url ('admin|parameters|') => array ('icon' => _resource ('img/tools/config.png'), 'caption' => 'Config'),
					_url ('admin|plugin|') => array ('icon' => _resource ('admin|img/icon/plugin.png'), 'caption' => 'Plugins'),
					_url ('admin|log|') => array ('icon' => _resource ('admin|img/icon/log.png'), 'caption' => 'Logs'),
					_url ('admin|cache|') => array ('icon' => _resource ('admin|img/icon/cache.png'), 'caption' => 'Caches'),
					_url ('admin|temp|') => array ('icon' => _resource ('img/tools/refresh.png'), 'caption' => 'Vider temp'),
					_url ('copixtools|session|') => array ('icon' => _resource ('copixtools|img/icon/session.png'), 'caption' => 'Session')
				);
				$isFirst = true;
				foreach ($menus as $url => $infos) {
					if (!$isFirst) {
						echo '&nbsp;&nbsp;';
					}
					$isFirst = false;
					?>
					<div style="vertical-align: middle; display: inline">
						<a href="<?php echo $url ?>">
							<img src="<?php echo $infos['icon'] ?>" style="vertical-align: middle; display: inline-block" />
							<span style="vertical-align: middle; display: inline-block"><?php echo $infos['caption'] ?></span>
						</a>
					</div>
				<?php } ?>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="betweenMenuContent"></td>
	</tr>
	<tr>
		<td colspan="5">
			<table class="contentContainer">
				<tr>
					<td class="block_top_left"></td>
					<td class="block_top"></td>
					<td class="block_top_right"></td>
				</tr>
				<tr>
					<td class="block_left"></td>
					<td class="block_content">
						<div class="breadcrumb">
				<?php
				if (CopixModule::isEnabled ('breadcrumb')) {
					_eTag ('copixzone', array ('process' => 'breadcrumb|show', 'required' => false));
				} else {
					try {
						$module = CopixModule::getInformations ('breadcrumb')->description . ' (breadcrumb)';
					} catch (Exception $e) {
						$module = 'breadcrumb';
					}
					echo '<div class="requireBreadcrumb">' . _i18n ('copix:copix.theme.moduleRequired', $module) . '</div>';
				}
				?>
			</div>
						<table class="mainTitle">
							<tr>
								<td class="block_left"><img src="<?php echo _resource ('img/title_left.png') ?>" /></td>
								<td class="block_content"><?php echo $TITLE_PAGE; ?></td>
								<td class="block_right"><img src="<?php echo _resource ('img/title_right.png') ?>" /></td>
							</tr>
						</table>
						<div class="content">
							<?php
							if (CopixPage::get ()->isAdmin ()) {
								echo CopixZone::process ('admin|ModulesToUpdate');
							}
							echo $MAIN;
							?>
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
	<tr>
		<td colspan="5" class="footer">
			<a href="http://www.copix.org" target="_blank">Copix <?php echo COPIX_VERSION ?></a>
			| <a href="http://www.gnu.org/licenses/lgpl.html" target="_blank">GNU / LGPL</a>
			</td>
	</tr>
</table>
<script type="text/javascript">
<?php if (_currentUser ()->isConnected ()) { ?>
	showLogged ('<?php echo _currentUser ()->getLogin () ?>', <?php echo (_currentUser ()->testCredential ('basic:admin')) ? 'true' : 'false'; ?>);
<?php } else { ?>
	showLoginForm (false);
<?php } ?>
</script>
</body>
</html>