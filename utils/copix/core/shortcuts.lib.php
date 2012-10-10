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
 * @see CopixI18N::get
 * @return string
 */
function _i18n ($key, $args = null, $locale = null, $trigger = true) {
   return CopixI18N::get ($key, $args, $locale, $trigger);
}

/**
 * Une fonction pour echapper les caractères HTML d'une chaine UTF8
 * @param string $pString
 * @return string
 */
function _copix_utf8_htmlentities ($pString){
	if (CopixI18N::getCharset () == 'UTF-8'){
   		return htmlentities ($pString, null, 'UTF-8');
   	}
   	return htmlentities ($pString);
}

/**
 * Décode uniquement si nous sommes en mode différent de UTF8
 * @param string $pString
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
 * @return object
 * @see CopixClassesFactory::create
 */
function _class ($pClassId){
	return CopixClassesFactory::create ($pClassId);
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
 * @return  CopixDAO
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
function _daoInclude ($pDAOId){
	CopixDAOFactory::fileInclude ($pDAOId);
}

/**
 * Alias à CopixDAOFactory::getInstanceOf
 * @param 	string	$pDAOid	identifiant de la DAO à instancier de façon unique
 * @param 	string	$pConnectionName
 * @return CopixDAO	
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
?>