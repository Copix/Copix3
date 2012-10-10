<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour les contrôlleurs
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixActionController {

	/**
	 * Instances crées
	 * 
	 * @var array
	 */
	private static $_instances = array ();

	/**
	 * Extraction du chemin à partir de l'identifiant donné, de la forme module|ag::methName ou module|ag|methName
	 * Si aucun module n'est donné, on utilise le contexte courant.
	 * 
	 * @param string $pAGId Identifiant d'action que l'on souhaite exécuter
	 * @return object Objet avec 3 propriétés : module, actiongroup et method
	 * @throws CopixException
	 */
	private static function _extractPath ($pAGId) {
		$extract = explode ('|', $pAGId);
		if (count ($extract) == 1) {
			return CopixActionGroup::_extractPath (CopixContext::get () . '|' . $pAGId);
		}
		if (count ($extract) == 3) {
			$extractMethod = array ($extract[1], $extract[2]);
		} else {
			$extractMethod = explode ('::', $extract[1]);
		}

		if (count ($extractMethod) !== 2) {
			throw new CopixException (_i18n ('copix:copixactiongroup.wrongPath', $pAGId));
		}

		$extracted = new StdClass ();
		$extracted->module = strtolower ($extract[0] === '' ? null : $extract[0]);
		$extracted->group = $extractMethod[0];
		$extracted->action = $extractMethod[1];

		return $extracted;
	}

	/**
	 * Récupère l'instance de l'actiongroup donné.
	 * 
	 * @param object $pActionGroupDescription Description de l'actiongroup dont on souhaite récupérer l'instance. Doit avoir 2 propriétés : module et actiongroup
	 * @return CopixActionGroup
	 * @throws CopixException
	 */
	public static function instance ($pActionGroupDescription) {
		$actionGroupID = $pActionGroupDescription->module . '|' . $pActionGroupDescription->group;

		if (!isset (self::$_instances[$actionGroupID])) {
			$execPath = CopixModule::getPath ($pActionGroupDescription->module);
			$fileName = $execPath . 'actiongroups/' . strtolower ($pActionGroupDescription->group) . '.actiongroup.php';
			if (!is_readable ($fileName)){
				throw new CopixActionGroupNotFoundException (_i18n ('copix:copixactiongroup.loadError', $fileName));
			}
			Copix::RequireOnce ($fileName);
			// nom des objets/méthodes à utiliser.
			$objName = 'ActionGroup' . $pActionGroupDescription->group;
			self::$_instances[$actionGroupID] = new $objName ();
		}

		return self::$_instances[$actionGroupID];
	}

	/**
	 * Execution d'une action
	 * 
	 * @param string $pPath Identifier 'module|AG::method'
	 * @param array $pVars Paramètres
	 * @return CopixActionReturn
	 * @throws CopixException
	 */
	public static function process ($pPath, $pVars = array ()) {
		if (is_object ($pPath)){
			$extractedPath = $pPath;
		}else{
			$extractedPath = CopixActionGroup::_extractPath ($pPath);			
		}

		if ($extractedPath === null) {
			throw new CopixException (_i18n ('copix:copixactiongroup.loadError', $pPath));
		}

		$actiongroup = CopixActionGroup::instance ($extractedPath);
		$methName = 'process' . $extractedPath->action;

		if (!method_exists ($actiongroup, $methName)) {
			$methName = 'otherAction';
		}

		// on défini le module
		CopixContext::push ($extractedPath->module);
		foreach ($pVars as $varName => $varValue) {
			CopixRequest::set ($varName, $varValue);
		}

		// on essaye d'exécuter l'action
		try {
			if (($result = $actiongroup->_beforeAction ($extractedPath->action)) === null) {
				if ($methName == 'otherAction') {
					$toReturn = $actiongroup->$methName ($extractedPath->action);
				} else {
					$toReturn = $actiongroup->$methName ();
				}
			} else {
				$extractedPath->action = '_beforeAction';
				$toReturn = $result;
			}
				
			if (($result = $actiongroup->_afterAction ($extractedPath->action, $toReturn)) !== null) {
				$toReturn = $result;
			}

			// si on n'a pas fait de return valide
			if (!($toReturn instanceof CopixActionReturn)) {
				$reflection = new ReflectionClass ($actiongroup);
				$method = $reflection->getMethod ($methName);
				$extras = array (
					'Action' => $extractedPath,
					'Method' => $methName,
					'Return' => $toReturn,
					'StartLine' => $method->getStartLine(),
					'ActionGroupFile' => $reflection->getFileName ()
				);
				throw new CopixException (_i18n ('copix:copixactiongroup.invalidActionReturn', array (gettype ($toReturn))), null, $extras);
			}
				
		} catch (Exception $e) {
			try {
				$toReturn = $actiongroup->_catchActionExceptions ($e, $extractedPath->action);
			} catch (Exception $e) {
				// on est obligé de relancer un try/catch pour pouvoir faire un pop du contexte
				CopixContext::pop ();
				throw $e;
			}
		}
		CopixContext::pop ();
		return $toReturn;
	}

	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs avant chaque actions. Destinée à être surchargée
	 * 
	 * @param string $pActionName Nom de l'action
	 * @return mixed
	 */
	protected function _beforeAction ($pActionName){
		return $this->beforeAction ($pActionName);
	}
	
	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs avant chaque actions. Destinée à être surchargée au besoin
	 * 
	 * @param string $pActionName Nom de l'action
	 * @return mixed
	 */
	protected function beforeAction ($pActionName) {}

	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs après chaque action. Destinée à être surchargée au besoin
	 * 
	 * @param string $pActionName Nom de l'action
	 * @param CopixActionReturn Retour de l'action (processXXX)
	 * @return mixed
	 */
	protected function _afterAction ($pActionName, $pActionReturn){
		return $this->afterAction ($pActionName, $pActionReturn);
	}
	
	/**
	 * Donne l'opportunité à l'actiongroup de gérer des éléments communs après chaque action. Destinée à être surchargée au besoin
	 * 
	 * @param string $pActionName Nom de l'action
	 * @param CopixActionReturn Retour de l'action (processXXX)
	 */
	protected function afterAction ($pActionName, $pActionReturn) {}

	/**
	 * Donne la possibilité à chaque actiongroup de traiter les erreurs
	 * 
	 * @param Exception $pException Exception à traiter
	 * @throws Exception
	 */
	protected function _catchActionExceptions ($pException){
		return $this->catchActionExceptions ($pException);
	}
	
	/**
	 * Donne la possibilité à chaque actiongroup de traiter les erreurs
	 * 
	 * @param Exception $pException Exception à traiter
	 * @throws Exception
	 */
	protected function catchActionExceptions ($pException) {
		throw $pException;
	}

	/**
	 * Si l'action n'est pas gérée par l'actiongroup actuel, c'est cette méthode qui récupère le traitement
	 * 
	 * @return CopixActionReturn
	 */
	protected function otherAction () {
		if (CopixConfig::instance ()->notFoundDefaultRedirectTo !== false) {
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get (CopixConfig::instance ()->notFoundDefaultRedirectTo));
		}
		return new CopixActionReturn (CopixActionReturn::HTTPCODE, CopixHTTPHeader::get404 (), _i18n ('copix:copix.error.404'));
	}
}