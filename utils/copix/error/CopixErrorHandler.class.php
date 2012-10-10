<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestionnaire d'erreur par défaut activé dans Copix
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixErrorHandler implements ICopixErrorHandler {

	/**
	 * Configuration
	 * @var CopixConfig
	 */
	private $_config;
	
	/**
	 * Erreurs de type E_STRICT.
	 *
	 * @var array
	 */
	private $_stricts = array();
	
	/**
	 * Initialise le gestionnaire d'erreurs.
	 *
	 * @param CopixConfig $pConfig Configuration à utiliser.
	 */
	public function __construct(CopixConfig $pConfig) {
		$this->_config = $pConfig;
	}

	/**
	 * Traite une erreur.
	 *
	 * @param array $pError Erreur à traiter
	 */
	private function _processError($pError) {
		
		extract($pError); // J'aime pas trop, mais on va faire avec
		
		$errorReaction = isset ($this->_config->copixerrorhandler_actions[$pErrNo]) ? $this->_config->copixerrorhandler_actions[$pErrNo] : $this->_config->copixerrorhandler_defaultaction;
		
		if ($errorReaction instanceof CopixErrorHandlerAction && ($errorReaction->getLogLevel () !== null || $errorReaction->getLaunchException ())) {
			
			$message = $this->_format ($pErrNo, $pErrMsg, $pFilename, $pLinenum, $pVars);

			if ($errorReaction->getLogLevel () !== null) {
				_log ($message, $errorReaction->getLogProfile (), $errorReaction->getLogLevel (), array ('file' => $pFilename, 'line' => $pLinenum));
			}
			
			if ($errorReaction->getLaunchException ()) {
				throw new CopixErrorHandlerException ($message);
			}
			
			return true;
			
		} elseif ( (error_reporting () & $pErrNo) == $pErrNo) {
			// Laisse PHP gérer l'erreur si l'error_reporting est actif pour cette erreur
			return false;
		}
	}
	
	/**
	 * Gestion d'une erreur
	 * 
	 * @param int $pErrNo Numéro de l'erreur
	 * @param string $pErrMsg Message
	 * @param string $pFilename Fichier qui a généré l'erreur
	 * @param int $pLinenum Numéro de la ligne de l'erreur
	 * @param array $pVars Variables
	 * @throws CopixErrorHandlerException
	 */
	public function handle ($pErrNo, $pErrMsg, $pFilename, $pLinenum, $pVars) {
		//Si on a explicitement demandé à ne pas voir les messages d'erreur (avec un "@" devant la ligne en question)
		if (error_reporting () == 0) {
			return true;
		}
		
		$error = compact('pErrNo', 'pErrMsg', 'pFilename', 'pLinenum', 'pVars');
		if($pErrNo == E_STRICT) {
			// Met l'erreur en attente : on pourrait avoir besoin de l'autoloader or
			// il est désactivé au moment où les E_STRICT sont émises. 
			$this->_stricts[] = $error;
		} else {
			// Traite l'erreur immédiatement
			$this->_processError($error);
		}
	}
	
	/**
	 * Traite les erreurs E_STRICT en attente.
	 * 
	 * Traite un maximum de 20 erreurs. Les suivantes sont ignorées (dans ce cas on émet une notice). 
	 * 
	 */
	public function processStricts() {
		$maxIter = 20;
		while($maxIter-- > 0 && $error = array_shift($this->_stricts)) {
			$this->_processError($error);
		}
		if(($remaining = count($this->_stricts)) > 0) {
			_log(_i18n('copix:copix.error.ignoredErrors', $remaining), 'errors', CopixLog::NOTICE);
			$this->_stricts = array();
		}
	}
	
	/**
	 * Formattage du message d'erreur de l'exception
	 *
	 * @param int $pErrNo Numéro de l'erreur
	 * @param string $pErrMsg Message
	 * @param string $pFilename Fichier qui a généré l'erreur
	 * @param int $pLinenum Numéro de la ligne de l'erreur
	 * @param array $pVars Variables
	 */
	private function _format ($pErrNo, $pErrMsg, $pFilename, $pLinenum, $pVars) {
		return $this->_errorConstantName ($pErrNo) . ' ' . $pErrMsg;
	}
	
	/**
	 * Retourne l'erreur au format texte en fonction de la valeur de $pErrorCode
	 * 
	 * @param int $pErrorCode Code de l'erreur dont on souhaite connaitre la valeur
	 * @return string
	 */
	private function _errorConstantName ($pErrorCode) {
		static $errors = array (
			E_ERROR => 'E_ERROR', 
			E_WARNING => 'E_WARNING', 
			E_PARSE => 'E_PARSE',
			E_NOTICE => 'E_NOTICE',
			E_CORE_ERROR => 'E_CORE_ERROR',
			E_CORE_WARNING => 'E_CORE_WARNING',
			E_COMPILE_ERROR => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING => 'E_COMPILE_WARNING',
			E_USER_ERROR => 'E_USER_ERROR',
			E_USER_WARNING => 'E_USER_WARNING',
			E_USER_NOTICE => 'E_USER_NOTICE',
			E_STRICT => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED => 'E_DEPRECATED',
		);
				
		return (isset ($errors[$pErrorCode])) ? '[' . $errors[$pErrorCode] . ']' : '[UNKNOW ERROR]';
	}
}