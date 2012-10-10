<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="copix" />
<title><?php echo $TITLE_BAR; ?></title>
<?php echo $HTML_HEAD ?>

<style style="text/css">
.CopixTable, .CopixVerticalTable {
	border: solid 1px green;
	width: 100%;
}

img {
	border: none;
}
</style>
</head>
<body>
<div style="background-color: red; border: solid 1px red; text-align: center">
	<p style="font-weight: bold; font-size: 20px">:::: Mode Maintenance du site ::::</p>
	<a href="<?php echo _url ('admin|maintenance|disable') ?>">Désactiver le mode maintenance</a>
</div>
<br />
<?php
if (CopixPage::get ()->isAdmin ()) {
	echo CopixZone::process ('admin|ModulesToUpdate');
}
echo $MAIN;
?>
</body>
</html>