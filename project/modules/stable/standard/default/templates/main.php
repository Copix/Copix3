<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="copix" />
<link rel="search" type="application/opensearchdescription+xml" title="Documentation Copix" href="<?php echo _url(); ?>goodies/copix.xml" />
<title><?php echo $TITLE_BAR; ?></title>
<!--[if IE]>
  <link rel="stylesheet" href="<?php echo _resource ("styles/ie.css"); ?>" type="text/css"/>
<![endif]-->
<?php 
CopixHTMLHeader::addCSSLink (_resource ('styles/copix.css')); 
CopixHTMLHeader::addCSSLink (_resource ('styles/theme.css'));
echo $HTML_HEAD;
?>
</head>
<body>
<div id="banner">
	 <a href="<?php echo _url (); ?>"><img src="<?php echo _resource ('/img/logo.png'); ?>" alt="Copix" /></a>
	<h1><?php  echo $TITLE_PAGE; ?></h1>

	<div id="searchengine">
		<?php _eTag ('copixzone', array ('process'=>'quicksearch|quicksearchform', 'required'=>false)); ?>
	</div>

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
</div>
 <div id="maincontent">
    <?php _eTag ('copixzone', array ('process'=>'admin|AdminMenu', 'required'=>false)); ?>
    <?php _eTag ('copixzone', array ('process' => 'breadcrumb|show', 'required' => false)); ?>
	<?php echo $MAIN; ?>
 </div>
 <div id="footer">Site réalisé avec <a href="http://www.copix.org">Copix <?php echo COPIX_VERSION ?></a></div> 
</body>
</html>
