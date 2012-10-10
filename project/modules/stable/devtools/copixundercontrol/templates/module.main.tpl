<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
	<head>
		<title>[CopixUnderControl] Module d'administration pour intégration continue</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<!-- Framework CSS -->
		<link rel="stylesheet" href="{copixresource path='copixundercontrol|css/screen.css'}" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="{copixresource path='copixundercontrol|css/print.css'}" type="text/css" media="print">
		<!--[if lt IE 8]><link rel="stylesheet" href="{copixresource path='copixundercontrol|css/ie.css'} type="text/css" media="screen, projection"><![endif]-->

		<link rel="stylesheet" href="{copixresource path='copixundercontrol|css/copixundercontrol.css'}" type="text/css" media="screen, projection, print">
	</head>
	<body>
		<div id="header">
			<h1 id="logo">&nbsp;</h1>
			<a id="admin_return" href="{copixurl dest='admin||'}">Retour à l'administration</a>
		</div>
		<div class="container">
			<div id="nav" class="span-24">
				<ul class="navigation">
					<li><a title="Overview" href="{copixurl dest='copixundercontrol|admin|'}">Overview</a></li>
					<li><a title="Tests" href="{copixurl dest='copixundercontrol|admin|test'}">Tests</a></li>
					<li><a title="XML Log File" href="{copixurl dest='copixundercontrol|admin|xmlLog'}">XML Log File</a></li>
					<li><a title="Metrics" href="{copixurl dest='copixundercontrol|admin|metrics'}">Metrics</a></li>
					<li><a title="Code Coverage" href="{copixurl dest='copixundercontrol|admin|codeCoverage'}">Code Coverage</a></li>
					<li><a title="Documentation" href="{copixurl dest='copixundercontrol|admin|documentation'}">Documentation</a></li>
					<li><a title="CodeSniffer" href="{copixurl dest='copixundercontrol|admin|checkstyle'}">CodeSniffer</a></li>
					<li><a title="PHPUnit PMD" href="{copixurl dest='copixundercontrol|admin|pmd'}">PHPUnit PMD</a></li>
					<li><a title="PHP Depend" href="{copixurl dest='copixundercontrol|admin|phpDepend'}">PHP Depend</a></li>
				</ul>
			</div>
			<div id="content" class="span-24">
				{$MAIN}
			</div>
			<div id="footer" class="span-24">
				<span>CopixUnderControl : module d'intégration continue.</span>
			</div>
		</div>
	</body>

</html>