<?php 
$portletId = $portlet->getRandomId ();
$options = $portlet->getOptions();
$siteMapId = isset($options['sitemapId']) ? $options['sitemapId'] : ''; 
$editId = _request('editId');
$js = <<<EOJS
function updateSiteMapOption(portletId, editId, siteMapId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, editId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('heading|ajax|updateSiteMapId'),
		evalScripts: true,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : editId,
				'portletId' : portletId,
				'sitemapId' : siteMapId
				});
}
EOJS;

CopixHTMLHeader::addJSCode($js, 'commonSiteMapPortlet', CopixHTMLHeader::DOMREADY_NEVER);


$js = <<<EODRJS
function updateSiteMapOption_$portletId(siteMapId){
	updateSiteMapOption('$portletId', '$editId', siteMapId);
}

$('sitemapId_$portletId').addEvent('change', function(e){
	var value = $('sitemapId_$portletId').value;
	updateSiteMapOption_$portletId(value);
});

$('sitemapId_$portletId').fireEvent('change');

EODRJS;

CopixHTMLHeader::addJSCode($js, 'initSiteMapPortlet', CopixHTMLHeader::DOMREADY_ALWAYS);
?>



<label for="sitemap">Choisissez votre sitemap dans la liste suivante :</label>
<?php
$siteMapList = SiteMapServices::getSiteMapList();
$list = array();
foreach ($siteMapList as $siteMap){
	$list[$siteMap->getId()] = $siteMap->getSiteMapLink()->getCaption(); 
}
_eTag('select', array('name' => 'sitemapId', 'id' => 'sitemapId_'.$portletId ,'selected' => $siteMapId, 'emptyShow' => false, 'values' => $list ));
