<?php 

function getSubMenu($pArInfos, $pIgnore = array()){
	foreach ($pArInfos as $key=>$group){
		if(!in_array($key, $pIgnore)){
			echo "<li><a href='#'>";
			if($group['icon']){
				echo "<img src='" . $group['icon'] . "' /> ";
			} else {
				echo "<span style='padding-left:18px;'></span>";
			}
			echo  $group['caption'] . "</a>";
			if ($group['links']){
				echo "<ul>";
				foreach ($group['links'] as $linkInfos){
					$current = strpos (strtolower(CopixUrl::getCurrentUrl ()), strtolower ($linkInfos->getUrl())) !== false;
					echo "<li " . ($current ? 'class="limenu current"' : 'class="limenu"') . "><a href='" . $linkInfos->getUrl () . "'>";
					if ($linkInfos->getIcon ()){
						echo "<img src='" . $linkInfos->getIcon () . "' /> ";				
					} else {
						echo "<span style='padding-left:18px;'></span>";
					}
					echo  $linkInfos->getCaption () . "</a></li> ";
				}
				echo "</ul>";
			}
			echo "</li> ";
		}
	}
}

?>


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
</head>
<body>
<div id="banner">
	<div class="bannertop">
		<div class="contentsize">		
			
			<ul id="navigation">	
				<?php if (_currentUser()->isConnected()){ ?>	
				<li>
					<a href="<?php echo _url(); ?>">Accueil</a>
				</li>			
				<li>				
					<?php $adminInfos = _class ('admin|adminmenu')->getLinks (array('admin')); ?>
					<a href="<?php echo _url('admin||'); ?>"><?php echo $adminInfos['admin']['caption']; ?></a>
					<ul>
					<?php 
					foreach ($adminInfos['admin']['links'] as $key=>$linkInfos){
						$current = strpos (strtolower(CopixUrl::getCurrentUrl ()), strtolower ($linkInfos->getUrl())) !== false;
						echo "<li " . ($current ? 'class="limenu current"' : 'class="limenu"') . "><a href='" . $linkInfos->getUrl () . "'><img src='" . $linkInfos->getIcon () . "' /> " . $linkInfos->getCaption () . "</a></li> ";					
					}
					?>
					</ul>
				</li>		
				<li>	
					<a href="<?php echo _url('admin||', array('modules'=>array('auth', 'dbhandlers'))); ?>">Gestion des droits</a>
					<ul>			
					<?php getSubMenu(_class ('admin|adminmenu')->getLinks (array('auth', 'dbhandlers', 'admin')), array('admin')); ?>
					</ul>
				</li>			
				<li>
					<a href="<?php echo _url('admin|install|managemodules'); ?>">Modules</a>
					<ul>
					<?php getSubMenu(_class ('admin|adminmenu')->getLinks (), array('admin', 'auth', 'dbhandlers')); ?>
					</ul>
				</li>
				<?php 					
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
				}
				?>  
			</ul> 
			<div id="login">
				<?php			
				if (_currentUser ()->isConnected ()) {
					echo "Bonjour "._currentUser()->getLogin();
					echo " | <a href='"._url('auth|log|out')."'>Se déconnecter</a>";
				} else { ?>
					Vous n'êtes pas connecté | <a href="<?php echo _url (CopixConfig::get ('default|authUrl'), array ('auth_url_return' => _url ('admin||'))) ?>">Se connecter</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="bannerbottom">
		<div class="contentsize">			
			<img style="float:right;" src="<?php echo _resource('img/copix.png'); ?>" />
			<div class="title"><h1><?php echo $TITLE_PAGE ?></h1></div>
				
		</div>
	</div>
</div>
<div class="wrapper">
	<div id="maincontent" class="contentsize">
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
		if (CopixModule::isEnabled ('heading')) {
			echo CopixZone::process ('heading|HeadingScreenOptions');
		}
		if (CopixPage::get ()->isAdmin ()) {
			echo CopixZone::process ('admin|ModulesToUpdate');
		}
		echo $MAIN;
		?>
		<div style="clear:both;"></div>
	</div>
</div>
<div id="footer">
	<p>
		Copix framework <?php echo COPIX_VERSION ?>
		<br />
		<a href="http://www.copix.org">Copix.org</a> | <a href="http://www.copix.org/index.php/wiki/Documentation">Documentation</a> | <a href="http://forum.copix.org/">Forum</a>
	</p>
	<br />
</div>
</body>
</html>