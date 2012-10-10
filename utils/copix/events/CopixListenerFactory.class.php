<?php
/**
* @package		copix
* @subpackage	event
* @author		Croes Gérald, Patrice Ferlet
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe abstraite pour les listener
* @package		copix
* @subpackage 	event
*/
abstract class CopixListener {
   /**
   * Demande de traitement d'un événement donné
   * @param CopixEvent			$pEvent			l'événement à traiter
   * @param CopixEventResponse	$pEventResponse	la réponse à renseigner
   */
   public function perform ($pEvent, $pEventResponse) {
      $methodName = 'process'.$pEvent->getName ();
      $this->$methodName ($pEvent, $pEventResponse);
   }
}

/**
* Factory de listener
* @package		copix
* @subpackage	event
*/
class CopixListenerFactory {
    /**
    * Liste des listeners chargés en mémoire
    * @var array of CopixListener
    */
    private $_listenersSingleton = array ();

    /**
    * Table de hashage pour les événements
    * $_hashListened['eventName'] = array of listeners
    * @var array of object
    */
    private $_hashListened = array ();

    /**
     * Tableau qui indique pour chaque événément la liste des listeners (le nom) présent.
     * @var array
     */
    private $_eventInfos = null;

    /**
     * Singleton
     * @var CopixListenerFactory
     */
    private static $_instance = false;

    /**
    * singleton
    * @return CopixListenerFactory.
    */
    public static function instance () {
        if (self::$_instance === false) {
            self::$_instance = new CopixListenerFactory ();
        }
        return self::$_instance;
    }

    /**
    * Création d'un listener donné
    * @param 	string	$pModule		le nom du module auquel appartient le listener
    * @param 	string 	$pListenerName	le nom du listener à charger
    * @return 	CopixListener 
    */
    public function create ($pModule, $pListenerName){
        CopixListenerFactory::instance ()->_create ($pModule, $pListenerName);
    }

    /**
     * Création des listeners pour l'événement de nom donné
    * @param	string $pEventName	le nom de l'événement pour lequel on veut créer les listeners
    * @return	array of CopixListeners
    */
    public static function createFor ($pEventName) {
        $me = CopixListenerFactory::instance ();
        $me->_loadListeners ();
        $me->_createForEvent ($pEventName);
        return $me->_hashListened[$pEventName];
    }

    /**
    * Indique s'il est nécessaire de rechercher les listeners existants ou si l'on peut 
    * réutiliser le fichier de cache créée.
    * @return boolean
    */
    private function _mustCompile (){
        $config = CopixConfig::instance ();    	
        if ($config->force_compile){
            return true;
        }

        //no compiled file ?
        if (!is_readable ($this->_compiledFileName())){
            return true;
        }

        if ($config->compile_check){
            $compilationTime = filemtime ($this->_compiledFileName ());
            $modulesList = CopixModule::getList();
            foreach ($modulesList as $dir){
                $xmlFilename = CopixModule::getPath ($dir).'module.xml';
                if (is_readable ($xmlFilename)){
                    if (filemtime ($xmlFilename) > $compilationTime){
                        return true;
                    }
                }
            }
        }
        return false;//no need to compile again
    }

    /**
    * Lecture des informations sur les listeners (qui écoute quel événement)
    */
    private function _loadListeners () {
        //have we compiled or load this before ?
        if ($this->_eventInfos === null) {
            //we have to compile, then go trhougth the modules.
            if ($this->_mustCompile ()){
                //$listenersToLoad = array ();
                $eventList = array ();
                $modulesList = CopixModule::getList();
                foreach ($modulesList as $dir) {
                    $xmlFilename = CopixModule::getPath ($dir).'module.xml';
                    if (is_readable ($xmlFilename)){
                        $xml = simplexml_load_file ($xmlFilename); 
                        if (@isset ($xml->events->listeners->listener)){
                            foreach (is_array ($xml->events->listeners->listener) ? $xml->events->listeners->listener : array ($xml->events->listeners->listener) as $listener){
                                //$listenTo = array ();
                                $attributes   = $listener->attributes ();
                                $listenerName = (string)$attributes['name'];
                                foreach ($listener->event as $eventListened){//DDT modification de la boucle for, car cela occasionnait des plantages avec le nouveau parser XML.
                                    $attributes = $eventListened->attributes ();
                                    //$listenTo[] = $attributes['NAME'];
                                    if(!isset($eventList[(string)$attributes['name']])){
                                        $eventList[(string)$attributes['name']]=array();
                                    }
                                    // clé = nom de l'event, valeur = liste des fichiers listener
                                    $eventList[(string)$attributes['name']][] = array($dir, $listenerName);
                                }
                            }
                        }
                    }
                }
                $this->_writePHPCode ($eventList);
            } else {
                //We load the PHP Code
                require ($this->_compiledFileName ());
            }
            $this->_eventInfos = $eventList;
        }
    }

    /**
    * Ecriture du code PHP pour les listeners.
    * @param	array	$pEventsInfo	Tableau des informations sur les événements (qui écoute quoi)
    */
    private function _writePHPCode ($pEventsInfos){
        $generator = new CopixPHPGenerator ();
        $_resources = $generator->getPHPTags ($generator->getVariableDeclaration ('$eventList', $pEventsInfos));
        
        //writing the PHP code to the disk
        CopixFile::write ($this->_compiledFileName (), $_resources);
    }

    /**
    * Indique le nom du fichier cache à utiliser
    * @return string
    */
    private static function _compiledFileName (){
        return COPIX_CACHE_PATH.'php/listeners.instance.php';
    }

    /**
    * Supprime le fichier de cache
    */
    public static function clearCompiledFile (){
        if (is_file (CopixListenerFactory::_compiledFileName())){
            unlink (CopixListenerFactory::_compiledFileName());
        }
    }
    
    /**
    * Création des objets d'écoute pour un événement de nom donné
    * @param	string	$pEventName Le nom de l'événement pour lequel on crée les listeners
    */
    private function _createForEvent ($pEventName) {
        if (! isset ($this->_hashListened[$pEventName])){
            $this->_hashListened[$pEventName] = array();
            if(isset($this->_eventInfos[$pEventName])){
                foreach ($this->_eventInfos[$pEventName] as $listener){
                    $this->_hashListened[$pEventName][] = $this->_create ($listener[0], $listener[1]);
                }
            }
        }
    }

    /**
    * Création d'un listener
    * @param	string	$pModule		le nom du module auquel appartient le listener que l'on souhaite créer
    * @param 	string 	$pListenerName	le nom du listener
    * @return 	CopixListener
    */
    private function _create ($pModule, $pListenerName){
        if (! isset ($this->_listenersSingleton[$pModule][$pListenerName])){
            Copix::RequireOnce (CopixModule::getPath ($pModule).'/'.COPIX_CLASSES_DIR.strtolower ($pListenerName).'.listener.php');
            $className = 'Listener'.$pListenerName;
            $this->_listenersSingleton[$pModule][$pListenerName] = new $className ();
        }
        return $this->_listenersSingleton[$pModule][$pListenerName];
    }
}
?>