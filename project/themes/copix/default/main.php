<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="search" type="application/opensearchdescription+xml" title="Documentation Copix" href="<?php echo _url(); ?>goodies/copix.xml">
<title><?php echo $TITLE_BAR; ?></title>
<?php
_tag ('mootools', array ('plugin'=>'elementmover;transcorners;toolbar;shadows;fixhrefbutton,moopanes')); 
echo $HTML_HEAD; 
?>
<link rel="stylesheet"
	href="<?php echo _resource ("styles/copix.css.php"); ?>?copixurl=<?php echo _url (); ?>"
	type="text/css" />
<link rel="stylesheet"
	href="<?php echo _resource ("styles/theme.css.php"); ?>?copixurl=<?php echo _url (); ?>"
	type="text/css" />	
<script type="text/javascript"
	src="<?php echo _resource ("js/site.js"); ?>"></script>
<!--[if IE]>
  <link rel="stylesheet" href="<?php echo _resource ("styles/ie.css"); ?>" type="text/css"/>
<![endif]-->
</head>
<body>
<div id="allcontent">
<div id="banner">
	<span id="slogan">100% communautaire,100% professionnel,200% efficace</span>
<h1 class="main"><?php echo $TITLE_PAGE; ?></h1>
</div>
<!-- end banner -->

<div id="menu">
<ul>
<?php
if (isset ($menuItems)){
	foreach ($menuItems as $menuCaption=>$menuUrl){
		echo '<li><a href="'.$menuUrl.'">'.$menuCaption.'</a></li>';
	}
}
?>
</ul>
</div>
 <div id="maincontent">
		<div id="oncenter">
  		<?php echo $MAIN; ?>
		</div>
		
		<div id="onright" class="tiers1">
			<!-- news, etc... -->
			<div id="searchengine">
					 <?php
if (CopixModule::isEnabled ('quicksearch')){ 
echo CopixZone::process ('quicksearch|quicksearchform');
} 
?></div>
			<h3>Version actuelle</h3>
			<p>La version actuelle est 3.0 RC2. Vous pouvez consulter les changelogs...
			</p>
			<h3>Dernières nouvelles</h3>
			<ul>
				<li>Copix soutient l'initiative "Go PHP5"</li>
				<li>Un nouveau module "blogger" vous permet désormais de créer un Blog sous Copix... Suivez le guide!</li>
			</ul>
		</div>
 </div>
</div>
<div id="footer">Site réalisé avec <a href="http://www.copix.org">Copix 3</a></div>
</body>
</html>