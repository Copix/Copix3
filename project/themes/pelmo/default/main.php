<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $TITLE_BAR; ?></title>
<?php
	/**
	 * Stylesheets used by theme
         */
	//don't remove this, this is the Copix css used for admins pages	
	echo CopixHTMLHeader::addCSSLink(_resource('styles/copix.css.php'));

	//this is *your* stylesheet you can change. Try to uncomment
	//to see the "Copix" blog theme, use it to create yours !
	echo CopixHTMLHeader::addCSSLink(_resource('styles/theme.css'));

	//add mootools and some plugins
	_eTag('mootools',array('plugin'=>'fixhrefbutton;elementmover'));

	//this is a javascript to set some properties, you could add effects if you want
	echo CopixHTMLHeader::addJSLink(_resource('js/site.js'));


?>
<?php echo $HTML_HEAD; ?>
</head>
<body>
<div id="wrapcontent">
	<div id="content">
		<div id="banner">
			<!-- Banner, use background image on CSS -->
			<h1><?php echo $TITLE_BAR; ?></h1>
		</div>
		<div id="menu">
			<!-- Menus -->
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
			<div class="pagecontent">
				<!-- Here the page content -->
				<?php echo $MAIN;?>
			</div>
		<div id="toright">
			<!-- This have to be on right -->
			<div id="searchengine">
			<?php if (CopixModule::isEnabled ('quicksearch')) echo CopixZone::process ('quicksearch|quicksearchform'); ?>
			</div>
			<!-- Here you can put google widgets, plugoo, etc.. -->
		</div>
	</div>
	<div id="footer">
		Site made with <a href="http://www.copix.org" title="copix">Copix 3</a> with the help of <a href="http://www.stellou.fr" alt="Stellou" title="Stellou">Stellou</a>
	</div>
</div>
</div>

</body>
</html>
