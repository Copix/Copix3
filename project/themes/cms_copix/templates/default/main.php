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
<?php 
	_eTag('mootools');
	echo $HTML_HEAD; 
?>
</head>
<body>
<div class="pagewidth">
	<div class="header">
		<img style="float:left;margin-top: -13px" src="<?php echo _resource("img/logo.png"); ?>" />
		<div >
		<h1><a href="<?php echo _url(); ?>">Copix Cms</a></h1>
		<h2>Copix Management System</h2>
		</div>
	</div>
	<div class="nav">
		<?php 
		//MENU PRINCIPAL DU CMS
		if (CopixModule::isEnabled ('heading')){
			echo CopixZone::process('heading|HeadingMenuList', array('type_hem'=>'MAIN',
								'template'=>'heading|menu/headingmenulistnavigation.php'));
			} 
		?>
	</div>
	<div class="content">
		<?php 
			if (CopixPage::get ()->isAdmin ()) {
				echo CopixZone::process ('admin|ModulesToUpdate');
			}
			if (CopixModule::isEnabled ('breadcrumb')) {
				_eTag ('copixzone', array ('process' => 'breadcrumb|show', 'required' => false));
			} else {
				try {
					$module = CopixModule::getInformations ('breadcrumb')->description . ' (breadcrumb)';
				} catch (Exception $e) {
					$module = 'breadcrumb';
				}
				echo '<div class="requireBreadcrumb">' . _i18n ('copix:copix.theme.moduleRequired', $module) . '</div>';
			}
			echo $MAIN; 
		?>
		<div class="clear"></div>		
	</div>
    <div class="footer">
        <p>Site réalisé avec <a href="http://www.copix.org">Copix <?php echo COPIX_VERSION ?> et CopixCms 3</a></p> 
    </div>	
</div>
</body>
</html>
