<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="search" type="application/opensearchdescription+xml" title="Documentation Copix" href="<?php echo _url(); ?>goodies/copix.xml" />
<title><?php echo $TITLE_BAR; ?></title>
<?php
_tag ('mootools', array ('plugin'=>'elementmover;transcorners;toolbar;shadows;fixhrefbutton;moopanes')); 
echo $HTML_HEAD; 
?>
<link rel="stylesheet"
	href="<?php echo _resource ("styles/copix.css.php"); ?>?copixurl=<?php echo _url (); ?>"
	type="text/css" />
<link rel="stylesheet"
	href="<?php echo _resource ("styles/theme.css.php"); ?>?copixurl=<?php echo _url (); ?>"
	type="text/css" />
<link rel="stylesheet"
	href="<?php echo _resource ("js/mootools/css/moopanes.css"); ?>"
	type="text/css" />
<script type="text/javascript"
	src="<?php echo _resource ("js/site.js.php"); ?>"></script>
<!--[if IE]>
  <link rel="stylesheet" href="<?php echo _resource ("styles/ie.css"); ?>" type="text/css"/>
<![endif]-->
</head>
<body>
<div id="allcontent">
<div id="banner"><span id="slogan">100% communautaire, 100%
professionnel... 200% efficace.</span></div>
<!-- end banner -->
<div id="menu">
<?php
if (CopixModule::isEnabled ('quicksearch')){ 
echo CopixZone::process ('quicksearch|quicksearchform');
} 
?>
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
  <h1 class="main"><?php echo $TITLE_PAGE; ?></h1>
  <?php echo $MAIN; ?>
  <br />
  <div id="footer">Site réalisé avec <a href="http://www.copix.org">Copix 3</a></div>
 </div>
 
</div>
</body>
</html>