<?php
$showtables = & new CopixAction ('XMLGenerator', 'getTablesFromDB');
$showXML    = & new CopixAction ('XMLGenerator','getXMLDao');
$download   = & new CopixAction ('XMLGenerator','doDownload');

$default = & $showtables;
?>