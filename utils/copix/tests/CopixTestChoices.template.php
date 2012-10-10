<?php 
/** 
* @package		copix
* @subpackage	tests
*/ 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>[Copix] Tests 1/2</title>
<link rel="stylesheet"
	style="color: black" href="<?php echo _resource ("copix.css.php"); ?>?copixurl=<?php echo CopixUrl::get(); ?>"
	type="text/css" />
<link rel="stylesheet"
	style="color: black" href="<?php echo _resource ("js/niftycorners/niftyCorners.css"); ?>"
	type="text/css" />
<script type="text/javascript"
	src="<?php echo _resource ("js/niftycorners/niftycube.js"); ?>"></script>
<script type="text/javascript"
	src="<?php echo _resource ("site.js"); ?>"></script>
<!--[if IE]>
  <link rel="stylesheet" style="color: black" href="<?php echo _resource ("ie.css"); ?>" type="text/css"/>
<![endif]-->
</head>
<body>

<div id="allcontent">
<div id="banner"><span id="slogan">100% communautaire, 100%
professionnel... 200% efficace.</span></div>
<!-- end banner -->
<div id="menu">
</div>
<!-- end menu -->
<div id="mainview">
<div id="maincontent">
<h1 class="main">Tests 1/2</h1>

<?php if($PHPUnitTest){ ?>

<form action="test.php"
	method="POST" />
<ul>
	<li><a style="color: black" href="./test.php?tests=all" />Tout</a></li>
	<?php
	$i = 0;
	foreach ($arTests as $moduleName=>$possibleTests){
		echo '<li><input type="checkbox" name="tests[]" value="'.$moduleName.'|" /><a style="color: black" href="./test.php?tests[]='.$moduleName.'|" />Module '.$moduleName.'</a><ul>';
		foreach ($possibleTests as $idTest){
			echo '<li style="'.(($i++%2 == 0) ? "background-color: #cccccc;" : "").'"><input type="checkbox" name="tests[]" value="'.$idTest.'" /><a style="color: black" href="./test.php?tests[]='.$idTest.'" />'.$idTest.'</a></li>';
		}
		echo "</li></ul>";
	}
	?>
</ul>
<input type="submit" value="Ok" />
</form>
	<?php }else{ ?> <a style="color: black" href="http://www.phpunit.de">PHPUnit</a> is required
for the test system to work. Either user pear install PHPUnit or <a
	style="color: black" href="http://pear.phpunit.de/get/">download it</a> and copy it in your
DocumentRoot directory (www). <?php }?></div>
<!-- end maincontent -->
<div id="footer">Site réalisé avec <a href="http://www.copix.org">Copix
3</a></div>
</div>
<!-- end mainview --></div>
<!-- end allcontent -->
</body>
</html>