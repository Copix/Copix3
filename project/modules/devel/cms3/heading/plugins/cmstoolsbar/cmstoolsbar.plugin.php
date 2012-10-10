<?php
/**
 *
 */
class PluginCMSToolsBar extends CopixPlugin implements ICopixBeforeDisplayPlugin {
	public function getDescription () {
		return 'Barre d\'outils s\'affichant en haut de la page affichée';
	}

	public function getCaption () {
		return 'Plugin de la barre d\'outils du CMS';
	}

	public function beforeDisplay(&$pContent) {
	    if (CopixAuth::getCurrentUser ()->testCredential ('basic:admin') || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request('public_id', 0))) {
			if (CopixSession::get ('displayedElementsEvents', 'CMS') != null && CopixSession::get ('displayToolsBar', 'CMS', true)){
				$toolsbar = CopixZone::process('heading|cmstoolsbar', array('page_id'=>_request('public_id', false), 'displayedElements'=>CopixSession::get ('displayedElementsEvents', 'CMS')));
				$pContent = str_replace("</body>", $toolsbar . "</body>", $pContent);
			}
	    }
		CopixSession::delete('displayedElementsEvents', 'CMS');
		CopixSession::delete('displayToolsBar', 'CMS');
	}
}