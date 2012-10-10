<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $TITLE_BAR; ?></title>
<link rel="stylesheet" href="<?php echo _resource ("styles/copix.css"); ?>" type="text/css"/>
<link rel="stylesheet" href="<?php echo _resource ("styles/theme.css"); ?>" type="text/css"/>
<?php
_tag ('mootools', array ('plugin'=>'elementmover;transcorners;toolbar;shadows;fixhrefbutton')); 
echo $HTML_HEAD; 
?>
</head>
<body>

<div id="allcontent">
<div id="banner">
<img src="<?php echo CopixUrl::get();?>themes/bigtoukan/img/logo.png"/>
<span id="slogan">100% communautaire, 100% professionnel... 200% efficace.</span>
</div><!-- end banner -->
<br />
<div id="mainview">
<div id="maincontent">
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
</div><!-- end menu -->
 <h1 class="main"><?php echo $TITLE_PAGE; ?></h1>
 <?php echo $MAIN; ?>  

</div><!-- end maincontent -->
<div id="footer">
<p>Site réalisé avec <a href="http://www.copix.org">Copix 3</a></p>
</div>
</div><!-- end mainview -->

</div><!-- end allcontent -->
</body>
</html>