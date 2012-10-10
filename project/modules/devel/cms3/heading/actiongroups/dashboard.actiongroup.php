<?php
/**
 * @package     cms3
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain VUIDART
 */

/**
 * Dashboard
 * Tableau de bord du CMS
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupDashboard extends CopixActionGroup {
	
	protected function _beforeAction ($pAction) {
        //On test si l'on est admin ou si l'on peut écrire dans la rubrique donnée
        if (! (CopixAuth::getCurrentUser ()->testCredential ('basic:admin') || CopixAuth::getCurrentUser ()->testCredential ('cms:write@0'))) {
            throw new CopixCredentialException ('basic:admin');
        }
        
        CopixPage::add ()->setIsAdmin (true);
        if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
        _ioClass ('HeadingElementInformationServices')->breadcrumbAdmin ();
		ZoneHeadingAdminConfiguration::setDefaultTab ('headingDashboard');
    }
    
    public function processDefault (){
    	CopixHTMLHeader::addCSSLink(_resource('heading|css/cms.css'));
    	CopixHTMLHeader::addCSSLink(_resource('heading|css/headingadmin.css'));
    	
    	$this->_checkPositions();
    	$ppo = _ppo();
		$ppo->columns = CopixUserPreferences::get ('heading|dashBoardColumns');
    	$ppo->positions = (array)json_decode(CopixUserPreferences::get('heading|dashboard|positions'));
    	$zones = array();
    	$zones['publish'] = CopixZone::process("heading|cmsactions", array (
			'id'=>'publish',
			'show' => CopixUserPreferences::get ('heading|dashBoardShowPublishs'),
			'title'=>"Dernières publications",
			'showMessage' => false,
			'params'=>_ppo()->loadFrom(array('types'=>array(HeadingActionsService::PUBLISH)))
		));
		$zones['actions'] = CopixZone::process("heading|cmsactions", array (
			'id'=>'actions',
			'show' => CopixUserPreferences::get ('heading|dashBoardShowHistory'),
			'icon' => '|img/actionslogs.png',
			'link' => '<a href="' . _url ('heading|actionslogs|') . '">Afficher tout l\'historique</a>'
		));
		$zones['drafts'] = CopixZone::process("heading|drafts", array ('show' => CopixUserPreferences::get ('heading|dashBoardShowDrafts')));
		$zones['contentstats'] = CopixZone::process("heading|contentstats", array ('show' => CopixUserPreferences::get ('heading|dashBoardShowStats')));
		$zones['bookmarks'] = CopixZone::process("heading|HeadingBookmarks", array("template"=>"dashboard/bookmarks.php", 'show' => CopixUserPreferences::get ('heading|dashBoardShowBookmarks')));
		$tpl = new CopixTpl();
		$tpl->assign ('show', CopixUserPreferences::get ('heading|dashBoardShowNavigation'));
		$zones['explore'] = $tpl->fetch("dashboard/explore.php");
		
    	$ppo->TITLE_PAGE = "Tableau de bord";
    	$ppo->zones = $zones;
		

		ZoneHeadingScreenOptions::setZone ('heading|DBScreenOptions');

    	return _arPPO($ppo, "dashboard/dashboard.php");
    }
    
    private function _checkPositions (){
    	
    	if (!CopixUserPreferences::get('heading|dashboard|positions')){
    		$positions = array();
    		$positions['column1'] = array("publish", "actions", "drafts", "contentstats");
    		$positions['column2'] = array("bookmarks","explore");
    		CopixUserPreferences::set('heading|dashboard|positions', json_encode($positions));
    	} else {
    		$positions = (array)json_decode(CopixUserPreferences::get('heading|dashboard|positions'), true);
    	   	if (empty($positions['column1']) && !empty($positions['column2'])){
    	   		$positions['column1'] = $positions['column2'];
    	   		$positions['column2'] = array();
				CopixUserPreferences::set('heading|dashBoardColumns', 1);
    	   	}

			if (CopixUserPreferences::get ('heading|dashBoardColumns') == 1 && !empty ($positions['column2'])) {
				$positions['column1'] = array_merge ($positions['column1'], $positions['column2']);
				$positions['column2'] = array ();
			}
			
			$positions['column1'] = array_unique ($positions['column1']);
			$positions['column2'] = array_unique ($positions['column2']);
			
    	   	CopixUserPreferences::set('heading|dashboard|positions', json_encode($positions));	
    	}
    }
    
    public function processGetDrafts(){
    	$ppo = _ppo();
    	$ppo->MAIN = CopixZone::process ('heading|drafts', array('justTable'=>true, 'heading'=>_request('heading', 0)));
    	return _arDirectPPO($ppo, 'generictools|blank.tpl');
    }
    
    public function processGetContentStats(){
    	$ppo = _ppo();
    	$ppo->MAIN = CopixZone::process ('heading|contentstats', array('justTable'=>true, 'heading'=>_request('heading', 0)));
    	return _arDirectPPO($ppo, 'generictools|blank.tpl');
    }
    
    public function processGetPublish(){
    	$ppo = _ppo();
    	$ppo->MAIN = CopixZone::process("heading|cmsactions", array('justTable'=>true, 'heading'=>_request('heading', 0), 'id'=>'publish', 'title'=>"Dernières publications", 'params'=>_ppo()->loadFrom(array('types'=>array(HeadingActionsService::PUBLISH)))));
    	return _arDirectPPO($ppo, 'generictools|blank.tpl');
    }
    
    public function processGetActions(){
    	$ppo = _ppo();
    	$ppo->MAIN = CopixZone::process("heading|cmsactions", array('justTable'=>true, 'heading'=>_request('heading', 0), 'id'=>'actions'));
    	return _arDirectPPO($ppo, 'generictools|blank.tpl');
    }
	
}