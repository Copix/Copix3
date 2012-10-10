<?php
/**
 * @package	tools
 * @subpackage	wikirender
 * @author	 Brice Favre
 * @copyright 2001-2008 CopixTeam
 * @link     http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de tests des plugins
 * @package	tools
 * @subpackage	wikirender
 */

class PluginTest implements iWikiPlugin {
    
    public function render ($pData = null){
        return 'TEST';
    }
    
    public function test ($pLine){
        _classInclude('wikirender|WikiElement');
        
        if (preg_match ('/~~TEST~~/', $pLine)) {
            $pluginElement = new WikiPluginElement ('plugins');
            $pluginElement->setPlugin (new PluginTest());
            return $pluginElement;
        }
        return false;
    }
}

/**
 * Classe derniers changements sur le wiki
 * @package tools
 * @subpackage wikirender
 */

class PluginLastChanges implements iWikiPlugin  {
	
	public function test ($pLine){
		_classInclude('wikirender|WikiElement');
		
		if (preg_match('/~~LASTCHANGES[(.*),(.*)]~~/', $pLine, $matches))  {
			$pluginElement = new WikiPluginElement ('plugins', $matches);
            $pluginElement->setPlugin (new PluginTest());
            return $pluginElement;
		} else if (preg_match('/~~LASTCHANGES~~/', $pLine)) {
			$matches = array ('', 0, 20);
			$pluginElement = new WikiPluginElement ('plugins', $matches);
            $pluginElement->setPlugin (new PluginLastChanges());
            return $pluginElement;
		}
		return false;
	}
	
	public function render ($pData = null) {
		$line = '';
		$arLastChanges = _ioDao ('wikipages')->findBy (_daoSp()-> setLimit ($pData[1],$pData[2])
		                                                       -> orderBy (array ('modificationdate_wiki','DESC')));
		
		foreach ($arLastChanges as $changes) {
			$line .= '<a href="'._url('wiki||', array ('title' => $changes->title_wiki)).'">'.CopixDateTime::yyyymmddhhiissToDateTime($changes->modificationdate_wiki).' '.$changes->title_wiki.'</a><br/>'."\n";
		}
		return $line;
	}
}
?>