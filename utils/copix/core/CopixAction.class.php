<?php
/**
* @package		copix
* @subpackage	core
* @author		Croes Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Objet de description des actions classiques
* @package   copix
* @subpackage core
* 
* <code>
* //dans un fichier de description
* $action = new CopixAction ('AGName', 'MethodName'); 
* </code>
*/
class CopixAction {
    /**
    * identifiant de l'objet à utiliser
    * @var string
    */
    var $useObj;

    /**
    * le nom de la méthode à utiliser de l'objet.
    * @var string
    */
    var $useMeth;

    /**
    * contient les parametres destinés aux plugins
    * @var array
    */
    var $params;

    /**
    * @var CopixModuleFileSelector le selecteur de fichier à exécuter.
    */
    var $file;
    
    /**
    * Contructeur.
    *
    * @param string $useObj l'identifiant de l'objet à utiliser. 
    *   (le nom de l'objet réel peut être compléter par  des préfixes / suffixes automatiques, 
    *   cf le coordinateur de module)
    * @param string $useMeth l'identifiant de la méthode de l'objet à utiliser.
    * @param mixed  $params tableau associatif de paramètre qui seront traités par les plugins
    */
    public function __construct ($useObj, $useMeth, $params = array ()){
        $this->useMeth = $useMeth;
        $this->useObj  = $useObj;
        $this->params  = $params;
        $this->file    = new CopixModuleFileSelector ($useObj);
    }
}

/**
* Pour les redirections automatiques depuis les fichiers de description.
* @package   copix
* @subpackage core
* 
* <code>
* //Dans un fichier de description
* $redirectAction  = new CopixActionRedirect (CopixUrl::get ('module|desc|action', array ('param'=>'value')));
* $redirectAction2 = new CopixActionRedirect ('http://www.copix.org'); 
* </code>
*/
class CopixActionRedirect  extends CopixAction {
    /**
    * @param string $UseFile nom du fichier à utiliser
    * @param mixed   $params tableau associatif de paramètre qui seront traités par les plugins
    */
    public function __construct ($useUrl, $params =null) {
        $this->file = new CopixModuleFileSelector ('');//current, we don't care, there's no use for that.
        $this->url  = $useUrl;
        if($params != null){
            $this->params = $params;
        }
    }
}

/**
* Pour les fichiers statiques (html souvent)
* @package   copix
* @subpackage core
* 
* <code>
* //dans un fichier de description
* $static = new CopixActionStatic ('module|file.html');
* </code>
*/
class CopixActionStatic  extends CopixAction {
    /**
    * @param string $UseFile nom du fichier à utiliser
    * @param mixed  $params tableau associatif de paramètre qui seront traités par les plugins
    */
    public function __construct ($useFile, $more = array (), $params =null){
        $this->file = new CopixModuleFileSelector ($useFile);
        $this->more  = $more;
        $this->useFile = $useFile;
        if($params != null){
            $this->params = $params;
        }
    }
}

/**
* Pour afficher directement des zones dans la zone principale du template du processus standard
* @package   copix
* @subpackage core
* 
* <code>
* //Dans un fichier de description
* $actionZone = new CopixActionZone ('module|ZoneId', array ('TITLE_PAGE'=>'titre de la page (facultatif)', 'TITLE_BAR'=>'Titre de la barre (facultatif)'));
* </code>
*/
class CopixActionZone  extends CopixAction {
    var $titlePage  = null;
    var $titleBar   = null;
    var $zoneParams = array ();
    var $zoneId     = null;
    var $params     = array ();

    public function __construct ($zoneId, $more = array (), $params = null){
        $this->file = new CopixModuleFileSelector ($zoneId);

        if (isset ($more['TITLE_PAGE'])){
            $this->titlePage = $more['TITLE_PAGE'];
        }
        if (isset ($more['TITLE_BAR'])){
            $this->titleBar = $more['TITLE_BAR'];
        }
        if (isset ($more['Params'])){
            $this->zoneParams = $more['Params'];
        }
        if($params != null){
            $this->params = $params;
        }
        $this->more   = $more;
        $this->zoneId = $zoneId;
    }
}
?>