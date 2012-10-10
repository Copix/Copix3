<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="copix" />
<link rel="search" type="application/opensearchdescription+xml" title="Documentation Copix" href="<?php echo _url(); ?>goodies/copix.xml" />
<title><?php echo $TITLE_BAR; ?></title>
<link rel="stylesheet"
	href="<?php echo _resource ("styles/copix.css"); ?>"
	type="text/css" />
<link rel="stylesheet"
	href="<?php echo _resource ("styles/theme.css"); ?>"
	type="text/css" />	
<!--[if IE]>
  <link rel="stylesheet" href="<?php echo _resource ("styles/ie.css"); ?>" type="text/css"/>
<![endif]-->
<?php 
	_eTag('mootools');
	echo $HTML_HEAD; 
?>
</head>
<body>
<div id="banner">
	<div class="fonddegrade">
		<div class="bannertop">
			<div class="contentsize">
				<h1><img alt="CopixCMS3" src="<?php echo _resource('img/logo.png');?>" /></h1>
				<div id="log">
					<img src="<?php echo _resource('img/user.png');?>" />
					<?php if (_currentUser()->isConnected()){
						echo "Bonjour <em>"._currentUser()->getLogin()."</em> | <img src='"._resource('img/deconnexion.png')."' /> <a href='"._url('auth|log|out')."'>Se déconnecter</a>";
					} else {?>
						Vous n'êtes pas connecté | <a href="<?php echo _url('heading|element|'); ?>">Se connecter</a>
					<?php }?>
				</div>
				
			</div>
		</div>
	</div>
	<div class="bannerbottom">
		<div class="menuDiv contentsize">
			<ul id="menu">
		 		<?php if (CopixModule::isEnabled("heading")){
		 			$groupcms3 = _class('admin|adminmenu')->getLinks(array_keys(CopixModule::getFullList(true, 'cms3')));
		 			foreach ($groupcms3['cms3']['links'] as $link){
		 				$current = strpos(CopixUrl::getCurrentUrl(), $link->getUrl()) !== false;
		 				echo "<li ".($current ? 'class="current"' : '')."><a href='".$link->getUrl()."'  ><img src='".$link->getIcon()."' />".$link->getCaption()."</a></li>";
		 			}
		 		}?>
			</ul>
		</div>	
		<?php if (CopixMOdule::isEnabled ('heading')){
			/*echo CopixZone::process('heading|HeadingMenuList', array('type_hem'=>'MAIN',
								'template'=>'heading|menu/headingmenulistnavigation.php'));*/
			} 
		?>
	</div>
</div>
 <div id="maincontent">
 
 	<div class="wrapper contentsize">
	 	<div class="wrappermenu" style="float: left;">
		 		

		</div>							
		<div style="clear:both;"></div>
		<h1><?php  echo $TITLE_PAGE; ?></h1>
	    <?php _eTag ('copixzone', array ('process' => 'breadcrumb|show', 'required' => false)); ?>
		<?php echo $MAIN; ?>
		<div style="clear:both;"></div>
	</div>
 </div>
 <div id="footer">
			<?php 
			if (CopixMOdule::isEnabled ('heading')){
				//echo CopixZone::process('heading|HeadingMenuList', array('type_hem'=>'FOOTER',
							//	'template'=>'heading|menu/headingmenulistnavigation.php'));
			}
			?>
 
 <p>
 	CopixCMS3 propulsé par le framework <a href="http://www.copix.org">Copix <?php echo COPIX_VERSION ?></a>
 	<br />
 	<br />
 	<a href="http://www.copix.org">Copix.org</a> | <a href="http://www.copix.org/index.php/wiki/Documentation">Documentation</a> | <a href="http://forum.copix.org/">Forum</a>
 </p>
 	
 	
 <br />
 </div> 
</body>
</html>