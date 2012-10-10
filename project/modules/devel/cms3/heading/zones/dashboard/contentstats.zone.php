<?php

class ZoneContentStats extends CopixZone {
	
	protected function _createContent (&$pToReturn) {
		$service = new HeadingElementInformationServices ();
		$tpl = new CopixTpl ();
		
		$arTypes = _class ('heading|HeadingElementType')->getList ();
		$arStats = array ();
		$tpl->assign ('justTable', $this->getParam ('justTable', false));
		$selectedHeading = $this->getParam ('heading', CopixUserPreferences::get ('heading|dashboard|headingcontentstatsoption', 0));
		$tpl->assign ('selectedHeading', $selectedHeading);
		$archives = 0;
		$published = 0;
		$drafts = 0;
		foreach ($arTypes as $type => $infos) {
			$stats = array ();
			$stats['infos'] = $infos;
			$stats['published'] = count ($service->find (array ('type_hei' => $type, 'status_hei' => HeadingElementStatus::PUBLISHED, 'hierarchy_hei' => $selectedHeading)));
			$published += $stats['published'];
			$stats['drafts'] = count ($service->find (array ('type_hei' => $type, 'status_hei' => HeadingElementStatus::DRAFT, 'hierarchy_hei' => $selectedHeading)));
			$drafts += $stats['drafts'];
			$stats['archives'] = count ($service->find (array ('type_hei' => $type, 'status_hei' => HeadingElementStatus::ARCHIVE, 'hierarchy_hei' => $selectedHeading)));
			$archives += $stats['archives'];
			$arStats[$type] = $stats;
		}	
		$tpl->assign ('arStats', $arStats);
		$tpl->assign ('published', $published);
		$tpl->assign ('drafts', $drafts);
		$tpl->assign ('show', $this->getParam ('show', true));
		$tpl->assign ('archives', $archives);
		$pToReturn = $tpl->fetch ("dashboard/contentstats.php");
	}
}