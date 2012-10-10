<?php
$create             = & new CopixAction ('Admin', 'doCreate');
$edit               = & new CopixAction ('Admin', 'getEdit');
$prepareEdit        = & new CopixAction ('Admin', 'doPrepareEdit');
$download           = & new CopixAction ('Admin', 'doDownload');
$valid              = & new CopixAction ('Admin', 'doValid');
$delete             = & new CopixAction ('Admin', 'doDelete');
$validForm          = & new CopixAction ('Admin', 'doValidForm');
$cancelEdit         = & new CopixAction ('Admin', 'doCancelEdit');

$importStandardTemplate    = & new CopixAction ('Admin', 'getImportStandardTemplate');
$importNonStandardTemplate = & new CopixAction ('Admin', 'getImportNonStandardTemplate');
$selectStandardTemplate    = & new CopixAction ('Admin', 'getSelectStandardTemplate');

$templateGenerator = & new CopixAction ('Admin', 'getTemplateGenerator');

$addElement = & new CopixAction ('Admin', 'doAddElementTemplateGenerator');
$HTMLParse   = & new CopixAction ('Admin', 'getHTMLParse');

$getHtml = new CopixAction ('Admin', 'getHtmlParse');
$validProperties = new CopixAction ('Admin', 'doValidProperties');
$getProperties   = new CopixAction ('Admin', 'getProperties');
$getAddElement   = new CopixAction ('Admin', 'getAddElement');
$getStructure    = new CopixAction ('Admin', 'getStructure');
$doRemoveElement = new CopixAction ('Admin', 'doRemoveElement');
?>