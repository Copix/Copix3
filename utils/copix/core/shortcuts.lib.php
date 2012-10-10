<?php
/**
* @package		copix
* @subpackage	core
* @author		Croes Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license 		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Alias à CopixI18N::get
 * 
 * @see CopixI18N::get
 * @param string $pKey Clef i18n
 * @param mixed $pArgs Paramètres à remplacer dans $pKey. Pour une seule valeur, $pArgs = string, sinon $pArgs = array ('maValeur', 'maValeur2')
 * @param string $pLocale Information sur la langue à rechercher, de la forme lang_COUNTRY (exemple : fr_FR, en_US)
 * @param boolean $pTrigger Paramètre inutilisé
 * @return string
 */
function _i18n ($pKey, $pArgs = null, $pLocale = null, $pTrigger = true) {
	return CopixI18N::get ($pKey, $pArgs, $pLocale, $pTrigger);
}

/**
 * Une fonction pour echapper les caractères HTML d'une chaine UTF8
 * 
 * @param string $pString Chaine à transformer
 * @param string $pEncoding Encoding à utiliser
 * @return string
 */
function _copix_utf8_htmlentities ($pString, $pEncoding = null) {	
	if ($pEncoding !== null) {
		return htmlentities ($pString, null, $pEncoding);
	}
	if (CopixI18N::getCharset () == 'UTF-8'){
   		return htmlentities ($pString, null, 'UTF-8');
   	}
   	return htmlentities ($pString);
}

/**
 * Décode uniquement si nous sommes en mode différent de UTF8
 * 
 * @param string $pString Chaine à décoder
 * @return string
 */
function _copix_utf8_decode ($pString){
	if (CopixI18N::getCharset () != 'UTF-8'){
		return utf8_decode ($pString);
	}
	return $pString;
}

/**
 * Alias à CopixURL::get
 * 
 * @see CopixURL::get
 * @param	string	$pDest	sélecteur pour l'url destination
 * @param	array	$pParams	tableau des paramètres supplémentaires
 * @param	bool	$pForXML	Si l'on souhaite générer l'url en XML
 * @return string
 */
function _url ($pDest = null, $pParams = array (), $pForXML = false){
	return CopixUrl::get ($pDest, $pParams, $pForXML);
}

/**
 * Alias à CopixURL::getResource ();
 * @see CopixURL::getResource ()
 * @param	string	$pResourcePath	le chemin de la ressource que l'on souhaite aller chercher
 * @return string 
 */
function _resource ($pResourcePath){
	return CopixUrl::getResource ($pResourcePath);
}

/**
 * Alias à CopixURL::getResourcePath ();
 * @see CopixURL::getResourcePath ()
 * @param	string	$pResourcePath	le chemin de la ressource que l'on souhaite aller chercher
 * @return string 
 */
function _resourcePath ($pResourcePath){
	return CopixUrl::getResourcePath ($pResourcePath);
}

/**
 * Alias à CopixClassesFactory::create ();
 * @param 	string	$pClassId	identifiant de la classe à créer (module|classe)
 * @param array $pArgs Arguments de création
 * @return object
 * @see CopixClassesFactory::create
 */
function _class ($pClassId, $pArgs = null){
	return CopixClassesFactory::create ($pClassId, $pArgs);
}

/**
 * Alias à CopixClassesFactory::getInstanceOf
 * @param 	string	$pClassId	identifiant de la classe à instancier (module|classe)
 * @param 	mixed	$pInstanceId	identifiant de l'instance "unique"
 * @return object
 * @see CopixClassesFactory::getInstanceOf
 */
function _ioClass ($pClassId, $pInstanceId = 'default'){
	return CopixClassesFactory::getInstanceOf ($pClassId, $pInstanceId);
}

/**
 * Alias à CopixClassesFactory::fileInclude ($pClassId);
 * @param	string 	$pClassId	l'identifiant de la classe que l'on souhaite inclure
 * @return boolean
 */
function _classInclude ($pClassId){
	return CopixClassesFactory::fileInclude ($pClassId);
}

/**
 * Alias à CopixDAOFactory::create
 * @param	string	$pDAOid 	identifiant de la DAO à créer
 * @param 	string	$pConnectionName	identifiant de la connection à utiliser pour la DAO à créer.
 * @return 	ICopixDAO
 * @see CopixDAOFactory::create
 */
function _dao ($pDAOid, $pConnectionName = null){
	return CopixDAOFactory::create ($pDAOid, $pConnectionName);
}

/**
 * Alias à CopixDAOFactory::fileInclude
 * @param 	string	$pDAOId	l'identifiant de la DAO que l'on souhaites connaitre dans les sources de l'application
 * @return bool
 */
function _daoInclude ($pDAOId, $pConnectionName = null){
	CopixDAOFactory::fileInclude ($pDAOId, $pConnectionName);
}

/**
 * Alias à CopixDAOFactory::getInstanceOf
 * @param 	string	$pDAOid	identifiant de la DAO à instancier de façon unique
 * @param 	string	$pConnectionName
 * @return	ICopixDAO	
 * @see CopixDAOFactory::instanceOf
 */
function _ioDAO ($pDAOid, $pConnectionName = null) {
	return CopixDAOFactory::getInstanceOf ($pDAOid, $pConnectionName);
}

/**
 * Alias à CopixDAOFactory::createSearchParams
 * @param	string	$pKind	Le type de gestion des conditions par défaut 
 * @return CopixDAOSearchParams 
 */
function _daoSP ($pKind = 'AND'){
	return CopixDAOFactory::createSearchParams ($pKind);
}

/**
 * Alias à CopixDAOFactory::createRecord ()
 * @param	string	$pRecordName	le nom du record que l'on souhaite créer
 * @param	string	$pConnection	la base a utilisé
 * @see CopixDAOFactory::createRecord
 * @return CopixDAORecord
 */
function _record ($pRecordName, $pConnection = null){
	return CopixDAOFactory::createRecord ($pRecordName, $pConnection);
}

/**
 * Alias à CopixTPL::tag
 * @see CopixTpl::tag
 * @return mixed
 */
function _tag ($pTagName, $pParams = array (), $pContent = null){
	return CopixTpl::tag ($pTagName, $pParams, $pContent);
}

/**
 * Alias à echo CopixTPL::tag ()
 * @see CopixTpl::tag
 * @see _tag
 * @return void
 */
function _eTag ($pTagName, $pParams = array (), $pContent = null) {
	echo _tag ($pTagName, $pParams, $pContent);
}

/**
 * Alias à CopixServices::process ()
 * @return mixed
 */
function _service ($pServiceId, $pParams=array (), $pTransactionContext = null){
	return CopixServices::process ($pServiceId, $pParams, $pTransactionContext);
}

/**
 * Alias de CopixEventNotifier::notify ()
 * @param	mixed 	$pEvent CopixEvent ou string qui représente l'événement levé
 * @param	array	$pParams	Tableau de paramètres relatifs à l'événement (si $pEvent est une chaine)	
 * @see CopixEventNotifier::notify ()
 */
function _notify ($pEvent, $pParams = array ()){
   return CopixEventNotifier::notify ($pEvent, $pParams);	
}

/**
 * Alias à new CopixActionReturn (CopixActionReturn::PPO, $ppo, $options) 
 * @return CopixActionReturn 
 */
function _arPPO ($pPPO, $pOptions){
	return new CopixActionReturn (CopixActionReturn::PPO, $pPPO, $pOptions);
}

/**
 * Alias à new CopixActionReturn (CopixActionReturn::PPO, $ppo, array ('mainTemplate'=>null, 'template'=>$template))
 */
function _arDirectPPO ($pPPO, $pTemplateName, $pOptions = array ()){
	return new CopixActionReturn (CopixActionReturn::PPO, $pPPO, array_merge (array ('mainTemplate'=>null, 'template'=>$pTemplateName), $pOptions));
}

/**
 * Alias à new CopixActionReturn (CopixActionReturn::REDIRECT, ...
 * @param	string	$pUrl	L'url ou aller
 * @return CopixActionReturn
 */
function _arRedirect ($pUrl){
	return new CopixActionReturn (CopixActionReturn::REDIRECT, $pUrl);
}

/**
 * Alias à new CopixActionReturn (CopixActionReturn::FILE, ...
 *
 */
function _arFile ($pFilePath, $pOptions = null){
	return new CopixActionReturn (CopixActionReturn::FILE, $pFilePath, $pOptions);
}

/**
 * Alias à new CopixActionReturn (CopixActionReturn::CONTENT, ...
 *
 * @param mixed $pContent	contenu à afficher directement
 * @param array $pOptions	options supplémentaires
 * @return CopixActionReturn
 */
function _arContent ($pContent, $pOptions = null){
	return new CopixActionReturn (CopixActionReturn::CONTENT, $pContent, $pOptions);
}

/**
 * Alias à new CopixActionReturn (CopixActionReturn::NONE, ... 
 * @see CopixActionReturn
 * @return CopixActionReturn
 */
function _arNone (){
	return new CopixActionReturn (CopixActionReturn::NONE);
}

/**
 * Alias à new CopixActionReturn (CopixActionReturn::Display, ... 
 * @see CopixActionReturn
 * @param	CopixTpl	$pTpl	Le template à afficher
 * @param	array		$pOptions	Options supplémentaires	
 * @return CopixActionReturn
 */
function _arDisplay ($pTpl, $pOptions = null){
	return new CopixActionReturn (CopixActionReturn::DISPLAY, $pTpl, $pOptions);
}

/**
 * Alias à CopixDB::getConnection ($base)->doQuery ($query, $params)
 * @param	string	$pQuery	La requête à lancer
 * @param 	array	$pParams	tableau des paramètres à passer à la base
 * @param	string	$pBase		La connexion à utiliser
 * @return mixed		
 */
function _doQuery ($pQuery, $pParams = array (), $pBase = null){
	return CopixDB::getConnection ($pBase)->doQuery ($pQuery, $pParams);
}

/**
 * Alias à CopixDB::getConnection ($base)->iDoQuery ($query, $params)
 * @param	string	$pQuery	La requête à lancer
 * @param 	array	$pParams	tableau des paramètres à passer à la base
 * @param	string	$pBase		La connexion à utiliser
 * @return mixed		
 */
function _iDoQuery ($pQuery, $pParams = array (), $pBase = null){
	return CopixDB::getConnection ($pBase)->iDoQuery ($pQuery, $pParams);
}

/**
 * Alias à CopixLog::log
 * @see CopixLog::log
 * @param 	string	$pChaine	Le message à loguer
 * @param	string	$pType		le type d'élément à loguer
 * @param	int		$pLevel		Le niveau d'information à loguer
 * @param	array	$arExtra	Tableau d'éléments supplémentaires	
 */
function _log ($pChaine, $pType = "default", $pLevel = CopixLog::INFORMATION, $arExtra = array ()){
	CopixLog::log ($pChaine, $pType, $pLevel, $arExtra);
}

/**
* Alias à CopixRequest::get ()
* @param	string	$pVarName	le nom de la variable que l'on veut récupérer
* @param 	mixed	$pDefaultValue	la valeur par défaut si rien n'est dans l'url
* @param 	boolean	$pDefaultIdEmpty	demande de retourner la valeur par défaut si jamais le paramètre est vide (0, null, '')
* @return 	mixed	valeur de la variable dans l'url
*/
function _request ($pVarName, $pDefaultValue = null, $pDefaultIfEmpty = true){
	return CopixRequest::get ($pVarName, $pDefaultValue, $pDefaultIfEmpty);
}

/**
* Alias pour CopixAuth::getCurrentUser ()
* @return CopixUser
*/
function _currentUser (){
   return CopixAuth::getCurrentUser ();
}

/**
 * Alias pour CopixDebug::var_dump ()
 * @param var $pVar Variable
 * @param bool $pReturn False : affiche le résultat avec echo, true : renvoie le résultat sous forme de chaine
 * @param bool $pFormatReturn Formater le résultat retourné, avec des couleurs et un affichage moins "lourd"
 */
function _dump ($pVar, $pReturn = false, $pFormatReturn = true) {
	return CopixDebug::var_dump ($pVar, $pReturn, $pFormatReturn);
}

/**
 * Alias pour CopixSession::get
 * @param	string	$pPath	le chemin de l'élément en session que l'on souhaite récupérer
 * @param	string	$pNamespace	le nom du namespace de session dans lequel on souhaite récupérer l'élément
 * @return mixed 
 */
function _sessionGet ($pPath, $pNamespace = 'default'){
	return CopixSession::get ($pPath, $pNamespace);
}

/**
 * Alias à CopixSession::set ()
 * @param	string	$pPath	le chemin de l'élément en session que l'on souhaite récupérer
 * @param	mixed	$pValue	la valeur de l'élément à définir
 * @param	string	$pNamespace	le nom du namespace de session dans lequel on souhaite récupérer l'élément
 * @return void
 */
function _sessionSet ($pPath, $pValue, $pNamespace = 'default'){
	CopixSession::set ($pPath, $pValue, $pNamespace);
}

/**
 * Racourcis pour new CopixPPO ()
 * 
 * @param array $pParams tableau a passer au constructeur de CopixPPO 
 * @return CopixPPO
 */
function _ppo ($pParams = array ()){
	return new CopixPPO ($pParams);
}

/**
 * Racourcis pour new CopixRPPO ()
 * 
 * @param array $pParams tableau a passer au constructeur de CopixRPPO 
 * @return CopixRPPO
 */
function _rppo ($pParams = array ()){
	return new CopixRPPO ($pParams);
}

if(version_compare(PHP_VERSION, '5.2.0', '>=')) {
	/**
	 * Convertit une valeur en chaîne, en utilisant __toString si possible (comportement PHP 5.2.0).
	 *
	 * @param mixed $pValue Valeur à convertir.
	 * @return string
	 */
	function _toString ($pValue) {
		return (string)$pValue;
	}
} else {
	/** @ignore */
	function _toString ($pValue) {
		return method_exists ($pValue, '__toString') ? $pValue->__toString() : (string)$pValue;
	}
}

/**
 * Racourcis pour la création d'un validateur
 *
 * @param string $pName		le nom du validateur à créer
 * @param array	 $pParams	les paramètres à passer au validateur
 * @param string $pMessage	Le message d'erreur à afficher en cas de problème	
 * @return ICopixValidator
 */
function _validator ($pName, $pParams = array (), $pMessage = null){
	return CopixValidatorFactory::create ($pName, $pParams, $pMessage);
}

/**
 * Racourcis pour la création d'un validateur composite
 *
 * @param	array	$pParams	les paramètres à passer au validateur
 * @param	string	$pMessage	l'erreur à afficher en cas de problème
 * @return ICopixCompositeValidator
 */
function _cValidator ($pMessage = null){
	return CopixValidatorFactory::createComposite ($pMessage);
}

/**
 * 
 * @return IComplexTypeValidator
 */
function _ctValidator ($pMessage = null){
	return CopixValidatorFactory::createComplexType ($pMessage);
}
?>