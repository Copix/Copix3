<?php
/**
 * @package		languages
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		tools 
 * @subpackage	menu 
 */
class ActionGroupLocks extends CopixActionGroup {
	
	private $_pathFlags = 'img/tools/flags/';
	private $_unkonwFlag = 'unknow.png';
	
	public function beforeAction ($actionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		_class ('functions')->updateLockedFiles ();
	}

    /**
     * Liste les fichiers lockés
     */
    public function processDefault () {
    	$ppo = new CopixPPO ();
    	$ppo->TITLE_PAGE = _i18n ('global.title.lockedFiles');
    	
    	$ppo->lockedFiles = array ();
    	$query = 'SELECT u.login_dbuser, l.module_lock, l.file_lock, l.time_lock
    			  FROM dbuser u, languageslocks l
    			  WHERE u.id_dbuser = l.id_dbuser
    			  ORDER BY module_lock, file_lock';
    	$lockedFiles = _doQuery ($query);
    			  	  
    	foreach ($lockedFiles as $fileIndex => $fileLocked) {
    		$fileInfos = _class ('functions')->getFileInfos ($fileLocked->file_lock);
    		
    		$lockedFilesIndex = count ($ppo->lockedFiles) - 1;
    		$ppo->lockedFiles[$lockedFilesIndex]['module'] = $fileLocked->module_lock;
    		$ppo->lockedFiles[$lockedFilesIndex]['file'] = $fileInfos->name;
    		$ppo->lockedFiles[$lockedFilesIndex]['icon'] = _resource ($this->_pathFlags . strtolower ($fileInfos->country) . '.png');
    		$ppo->lockedFiles[$lockedFilesIndex]['user'] = $fileLocked->login_dbuser;
    		$ppo->lockedFiles[$lockedFilesIndex]['timeLeft'] = CopixConfig::get ('languages|lockWaitTimeOut') - floor ((mktime () - $fileLocked->time_lock) / 60);
    	}
    	
    	return _arPPO ($ppo, 'locks.list.tpl');
    }
    
    /**
     * Délock un fichier
     */
    public function processUnlock () {
    	CopixRequest::assert ('moduleName', 'file');
    	
    	$daoSp = _daoSp ()
    		->addCondition ('module_lock', '=', _request ('moduleName'))
    		->addCondition ('file_lock', '=', _request ('file'));
    	
    	$result = _ioDao ('languageslocks')->findBy ($daoSp);
    	
    	if (count ($result) <> 1) {
    		throw new CopixException (_i18n ('global.error.fileNotFound'));
    	}
    	
    	_ioDao ('languageslocks')->deleteBy ($daoSp);
    	
    	return _arRedirect (_url ('locks|'));
    }
}
?>