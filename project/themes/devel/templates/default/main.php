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
<?php echo $HTML_HEAD; ?>
</head>
<body>
<div style="position: absolute; right: 10px;top: 0px;">
<?php _etag ('popupinformation', array ('ajax'=>true, 'zone'=>'admin|AdminMenu', 'img'=>_resource ('img/admin_menu.png'), 'handler'=>'clickdelay'));?>
</div>
<div id="banner">
	<h1><?php  echo $TITLE_PAGE; ?></h1>
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
    <?php _eTag ('copixzone', array ('process' => 'breadcrumb|show', 'required' => false)); ?>
	<?php echo $MAIN; ?>
 </div>
 <div id="footer">Site réalisé avec <a href="http://www.copix.org">Copix <?php echo COPIX_VERSION ?></a></div>
</body>
</html>