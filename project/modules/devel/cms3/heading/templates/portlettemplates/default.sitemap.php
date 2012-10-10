<?php
$portletId = $portlet->getRandomId ();
$options = $portlet->getOptions();
$siteMapId = $options['sitemapId'];
$sitemap = SiteMapServices::getSiteMap($siteMapId);
$siteMapLink = $sitemap->getSiteMapLink();

echo CopixZone::process('heading|SiteMapLink', array('isRoot' => true, 'sitemapLink' => $siteMapLink));
?>