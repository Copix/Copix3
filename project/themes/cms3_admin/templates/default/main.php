<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="generator" content="copix" />
	<link rel="search" type="application/opensearchdescription+xml" title="Documentation Copix" href="<?php echo _url () ?>goodies/copix.xml" />
	<title><?php echo $TITLE_BAR; ?></title>
	<link rel="stylesheet" href="<?php echo _resource ("styles/copix.css") ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo _resource ("styles/theme.css") ?>" type="text/css" />
	<?php
	_eTag ('mootools');
	echo $HTML_HEAD; 
	?>
	<link rel="stylesheet" id="userpreftheme" href="<?php echo CopixUserPreferences::get ('heading|themecms3|style', _resource ("styles/theme_gris.css")) ?>" type="text/css" />
</head>
<body>
<div id="banner">
	<div class="bannertop">
		<div class="contentsize">						
			<ul id="navigation">	
				<li class="lilogo">
					<a title="Retour accueil du site" href="<?php echo _url () ?>">&nbsp;</a>
				</li>
				<li><a href="<?php echo _url ('admin||') ?>">Administration</a></li>
				<?php 
				if (CopixModule::isEnabled ("heading")) {
					//on créé le menu avec un tri pas trés joli mais nécessaire
					$groupcms3 = _class ('admin|adminmenu')->getLinks (array_keys (CopixModule::getFullList (true, 'cms3')));
					$arMenuLinks = array();
					$arSubMenuLinks = array();
					foreach ($groupcms3['cms3']['links'] as $link) {
						if (strpos($link->getUrl(), "heading/dashboard") !== false){
							$arMenuLinks[0] = $link;
						}
						elseif (strpos($link->getUrl(), "heading/element") !== false){
							$arMenuLinks[1] = $link;
						}
						elseif (strpos($link->getUrl(), "heading/menueditor") !== false){
							$arMenuLinks[2] = $link;
						}
						elseif (strpos($link->getUrl(), "heading/actionslogs") !== false){
							$arMenuLinks[3] = $link;
						}
						elseif (strpos($link->getUrl(), "heading/advancedsearch") !== false){
							$arMenuLinks[4] = $link;
						}
						else {
							$arSubMenuLinks[] = $link;
						}
					}
					foreach ($arMenuLinks as $link) {
						$current = strpos (strtolower(CopixUrl::getCurrentUrl ()), strtolower ($link->getUrl())) !== false;
						echo "<li " . ($current ? 'class="limenu current"' : 'class="limenu"') . "><a href='" . $link->getUrl () . "'><img src='" . $link->getIcon () . "' /> " . $link->getCaption () . "</a></li> ";
					}
					if (!empty($arSubMenuLinks)){
						echo "<li class='limenu'><a href='javascript:;'><img src='" . _resource('img/tools/advanced.png') . "' />Avancé</a><ul>";
						foreach ($arSubMenuLinks as $link) {
							$current = strpos (strtolower(CopixUrl::getCurrentUrl ()), strtolower ($link->getUrl())) !== false;
							echo "<li " . ($current ? 'class="limenu current"' : 'class="limenu"') . "><a href='" . $link->getUrl () . "'><img src='" . $link->getIcon () . "' /> " . $link->getCaption () . "</a></li> ";
						}
						echo "</ul></li>";
					}
				}
				CopixHTMLHeader::addJSDOMReadyCode("
					var sfEls = $('navigation').getElements('li').each(function(el){
						el.addEvent('mouseOver', function(){
							el.addClass('sfhover');
						});
						el.addEvent('mouseOut', function(){
							el.removeClass('sfhover');
						});
					});
				");
				?>
			</ul>       
			<div id="login">
				<img src="<?php echo _resource ('img/reduser.png');?>" alt="Utilisateur" title="Utilisateur" />
				<?php
				$dirname = CopixTPL::getThemePath ('cms3_admin') . 'www/styles/';
				$dir = opendir ($dirname);
				$arThemeFile = array ();
				while ($file = readdir ($dir)) {
					if ($file != '.' && $file != '..' && !is_dir ($dirname.$file) && strpos ($file, "theme_") !== false) {
						$arThemeFile[] = $file;
					}
				}
				$content = "";
				if (count ($arThemeFile) > 1) {
					$content .= "Couleur thème administration";
					$content .= "<ul>";
					foreach ($arThemeFile as $themeFile) {
						$content .= "<li><a href='javascript:void(0)' class='linkUserPrefTheme' rel='" . _resource ('styles/' . $themeFile) . "'>" . ucfirst (str_replace (".css", "", str_replace ("_", " ", $themeFile))) . "</a></li>";
					}
					$content .= "</ul>";
				} 
				CopixHTMLHeader::addJSDOMReadyCode ("
					$$ ('.linkUserPrefTheme').each(function(el) {
						el.addEvent ('click', function () {
							$ ('userpreftheme').set ('href', el.get ('rel'));
							Copix.savePreference ('heading|themecms3|style', el.get ('rel'));
						});
					});
				");	

				if (_currentUser ()->isConnected ()) {
					echo "Bonjour ";
					echo _tag("popupinformation", array('handler'=>'clickdelay','clickerclass'=>'clickerpopupinformationtheme', 'displayimg'=>false, 'text'=>"<em>"._currentUser()->getLogin()."</em><span class='moreuseroptions'> ▼</span>"), $content);
					echo "| <a href='"._url('auth|log|out')."'>Se déconnecter</a>";
				} else { ?>
					Vous n'êtes pas connecté | <a href="<?php echo _url (CopixConfig::get ('default|authUrl'), array ('auth_url_return' => _url ('heading|dashboard|'))) ?>">Se connecter</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="bannerbottom">
		<div class="contentsize">
			<div class="search">
				<table style="text-align: left">
					<tr>
						<td>
							<form action="<?php echo _url ('heading|advancedsearch|search'); ?>" method="get">
							<input type="text" name="search" class="inputText" />
							<input type="hidden" name="heading" value="<?php echo _request ('heading', 0); ?>" />
							<input type="image" src="<?php echo _resource ('img/tools/search.png') ?>" style="vertical-align: text-bottom" />
							</form>
						</td>
					</tr>
					<tr>
						<td>
							<img src="<?php echo _resource ('img/tools/next.png') ?>" />
							<a href="<?php echo _url ('heading|advancedsearch|ShowElements') ?>" title="Recherche avancée">Recherche avancée</a>
						</td>
					</tr>
				</table>
			</div>
			<div class="title"><h1><?php echo $TITLE_PAGE ?></h1></div>
		</div>
		<div class="bannerbreadcrumb">
			<div class="contentsize">
				<?php if (_currentUser()->isConnected()){?>
				<div style="float: right">
					<?php if (ZoneHeadingScreenOptions::getZone () != null) { ?>
						<a class="screenOptionsToggler" href="javascript:void(0);"><img src="<?php echo _resource ('heading|img/screen_options.png') ?>" /> Affichage ▼</a>
						&nbsp;&nbsp;
					<?php } ?>
					<?php echo CopixZone::process ('heading|HeadingAdminConfiguration') ?>
				</div>
				<div id="leftbannerbreadcrumb">
					<?php
					echo CopixZone::process ('heading|HeadingBookmarks', array ('caption' => null));
					echo CopixZone::process ('heading|HeadingAdminBreadcrumb');
					?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="wrapper">
	<div id="maincontent" class="contentsize">
		<?php
		echo CopixZone::process ('heading|HeadingScreenOptions');
		echo $MAIN;
		?>
		<div style="clear:both;"></div>
	</div>
</div>
<div id="footer">
	<p>
		CopixCMS <?php echo CopixModule::getInformations ('heading')->getVersion () ?> propulsé par le framework <a href="http://www.copix.org">Copix <?php echo COPIX_VERSION ?></a>
		<br />
		<a href="http://www.copix.org">Copix.org</a> | <a href="http://www.copix.org/index.php/wiki/Documentation">Documentation</a> | <a href="http://forum.copix.org/">Forum</a>
	</p>
	<br />
</div>
</body>
</html>