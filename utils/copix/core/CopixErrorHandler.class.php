<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald
* @copyright	CopixTeam
* @link 		http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Gestionnaire d'erreur par défaut activé dans Copix
* @package copix
* @subpackage core
*/
class CopixErrorHandler {
	
	/**
	 * Singleton
	 * @var CopixErrorHandler
	 */
	private static $_instance = false;
	
	/**
	* Gestion d'une erreur
	*/
	public static function handle ($pErrNo, $pErrMsg, $pFilename, $pLinenum, $pVars){
		//Si on a explicitement demandé à ne pas voir les messages d'erreur (avec un "@" devant la ligne en question)
		if (error_reporting() == 0){
	        return;
		}
	
		$config = CopixConfig::instance ();
		$errorReaction = isset ($config->errorHandlerActions[$pErrNo]) ? $config->errorHandlerActions[$pErrNo] : $config->errorHandlerDefaultAction;
		
		$message = self::_format ($pErrNo, $pErrMsg, $pFilename, $pLinenum, $pVars);
		
		if ($errorReaction->getLogLevel () !== null){
			_log ($message, $errorReaction->getLogProfile (), $errorReaction->getLogLevel (), array ('file'=>$pFilename, 'line'=>$pLinenum, 'function'=>' ', 'class'=>' '));
		}
		
		if ($errorReaction->getLaunchException ()){
			throw new CopixErrorHandlerException ($message);
		}
	}
	
	/**
	 * Formattage du message d'erreur de l'exception
	 *
	 * @param int 		$pErrNo		numéro d'erreur
	 * @param string 	$pErrMsg	message d'erreur	
	 * @param string	$pFilename	nom du fichier
	 * @param int		$pLinenum	Numéro de ligne
	 * @param array		$pVars		Variables 
	 */
	private static function _format ($pErrNo, $pErrMsg, $pFilename, $pLinenum, $pVars){
		return self::_errorConstantName ($pErrNo).$pErrMsg;
	}
	
	/**
	 * Retourne le nom de la constante en fonction de la valeur de cette dernière
	 * @param	int	$pErrorCode	Le code de l'erreur dont on souhaite connaitre la valeur
	 * @return string
	 */
	private function _errorConstantName ($pErrorCode){
		static $errors = array ( E_ERROR=>'E_ERROR', 
				E_WARNING=>'E_WARNING', 
				E_PARSE=>'E_PARSE',
				E_NOTICE=>'E_NOTICE',
				E_CORE_ERROR=>'E_CORE_ERROR',
				E_CORE_WARNING=>'E_CORE_WARNING',
				E_COMPILE_ERROR=>'E_COMPILE_ERROR',
				E_COMPILE_WARNING=>'E_COMPILE_WARNING',
				E_USER_ERROR=>'E_USER_ERROR',
				E_USER_WARNING=>'E_USER_WARNING',
				E_USER_NOTICE=>'E_USER_NOTICE',
				E_STRICT=>'E_STRICT',
				E_RECOVERABLE_ERROR=>'E_RECOVERABLE_ERROR');
		if (isset ($errors[$pErrorCode])){
			return '['.$errors[$pErrorCode].']';
		}
		return '[UNKNOW ERROR]'; 
	}
}

/**
 * Classe de base lancée par le gestionnaire d'erreur de Copix lorsque l'on demande de lancer une exception
 * @package copix
 * @subpackage core
 */
class CopixErrorHandlerException extends CopixException {}

/**
 * Description des actions a effectuer dans le cas d'erreurs
 * @package copix
 * @subpackage core 
 */
class CopixErrorHandlerAction {
	/**
	 * le niveau de log
	 * @var int
	 */
	private $_logLevel;
	
	/**
	 * Le log à utiliser
	 * @var string
	 */
	private $_logProfile;
	
	/**
	 * Indique s'il faut lancer ou non une exception lors du déclenchement de l'erreur
	 * @var	boolean
	 */
	private $_launchException;

	/**
	 * Construction de l'action à effectuer sur une erreur
	 *
	 * @param 	boolean	$pException	S'il faut lancer une exception ou non
	 * @param	int		$pLogLevel	Le niveau de log à demander
	 * @param	string	$pLogProfile	Le nom du fichier de log à utiliser 	
	 */
	public function __construct ($pException, $pLogLevel, $pLogProfile = 'errors'){
		$this->_launchException = $pException;
		$this->_logLevel = $pLogLevel;
		$this->_logProfile = $pLogProfile;
	}
	
	/**
	 * Indique le niveau de log à utiliser pour le type d'erreur
	 * @return int
	 */
	public function getLogLevel (){
		return $this->_logLevel;
	}
	
	/**
	 * Indique le nom du profil à utiliser
	 * @return string
	 */
	public function getLogProfile (){
		return $this->_logProfile;
	}
	
	/**
	 * Indique s'il faut lancer une exception ou non lorsque l'erreur survient
	 * @return boolean
	 */
	public function getLaunchException (){
		return $this->_launchException;
	}
}
?>