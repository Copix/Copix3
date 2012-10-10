<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr_FR" lang="fr_FR">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type"/>
    <title>Bench</title>
    <link rel="stylesheet" type="text/css" href="<?php echo _resource ("styles/styles.css"); ?>" />
    <?php echo $HTML_HEAD; ?>  
    </head>
<body >
<div id="header"> Page de test </div>

<div id="main">
<p><?php echo $TITLE_PAGE; ?></p>
<?php echo $MAIN; ?>
</div>
<div id="sidemenu">
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

<div id="footer">
<p>Site réalisé avec <a href="http://www.copix.org">Copix 3</a></p>
</div>
</body></html>