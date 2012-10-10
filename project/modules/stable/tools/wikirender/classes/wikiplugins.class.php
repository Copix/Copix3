<?php
/**
 * @package	tools
 * @subpackage	wikirender
 * @author	 Brice Favre
 * @copyright 2001-2008 CopixTeam
 * @link     http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

_classInclude ('wikirender|wikielement');
/**
 * Classe de gestion des plugins Wiki
 * @package	tools
 * @subpackage	wikirender
 */
class WikiPlugins {

    /**
     * Tableau contenant les 
     *
     * @var array
     */
    private $_registeredPlugins = array ();
    
    /**
     * Plugin courant
     *
     * @var iWikiPlugin
     */
    public $currentPlugin;
    
    
    public function __construct (){
        $this->_registerPlugins ();
    }
    /**
     * Recupération des plugins définis
     *
     */
    private function _registerPlugins (){
        $listPlugins = CopixConfig::get ('wikirender|plugins');
        $arPlugins = explode (";", $listPlugins);
        foreach ($arPlugins as $plugin) {
            $infoPlugin = explode ("|", $plugin);
            if (count ($infoPlugin) == 2) {
                _classInclude ('wikirender|'.$infoPlugin[0]);
                $this->_registeredPlugins[] = new $infoPlugin[1];
            } else {
                $this->_registeredPlugins[] = _ioClass ('wikirender|'.$infoPlugin[0]);
            }
        }
    }
    
    /**
     * Tests des différents plugins
     *
     */
    public function isPluginMatch ($pLine){
        foreach ($this->_registeredPlugins as $plugin) {
        	$element = $plugin->test ($pLine);
            if ($plugin->test ($pLine) !== false) {
                $this->currentPlugin = $plugin;
                return $element;
            }
        }
        $this->currentPlugin = null;
        return false;
    }
}

/**
 * Classe WikiPluginsElement étends WikiElement en ajoutant une propriété Plugin 
 */
class WikiPluginElement extends WikiElement {
    
    /**
     * Le plugin lié à l'élément
     */
    public $plugin;
    
    
    /**
     * Fonction d'assignation du plugin 
     */
    function setPlugin ($pPlugin) {
        $this->plugin = $pPlugin;
    }
}

/**
 * Interface d'un plugin
 * @package tools
 * @subpackage wikirender
 */
interface iWikiPlugin {
    
    /**
     * Fonction de rendu d'un plugin
     * @param string données à intégrer (si besoin)
     * @return string code résultat
     */
    public function render ($pData = null);
    
    /**
     * Fonction de test d'un plugins
     *
     * @param string $pLine La ligne testée
     * @return boolean true si le plugin peut traiter le pattern, faux sinon
     */
    public function test ($pLine);
    
    
}

?>