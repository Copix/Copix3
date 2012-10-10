<?php
/**
 * @package standard
 * @subpackage auth 
 * 
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Page de gestion des droits par module
 * @package standard
 * @subpackage auth
 */
class ActionGroupModule extends CopixActionGroup {
    /**
	 * On s'assure que pour ces tâche ce soit bien un administrateur
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser()->assertCredential ('basic:admin');
		if (!CopixConfig::instance()->copixauth_isRegisteredCredentialHandler ('auth|dbmodulecredentialhandler')) {
		    throw new CopixException (_i18n('auth.moduleHandlerNotRegister'));
		}
	}
	
    public function processList () {
        CopixRequest::assert('id_group','handler_group');
        $id_group = _request('id_group');
        $handler_group = _request('handler_group');
        
        $arDroit = array();
        $arDroitSansModule = array();
        foreach (CopixModule::getList() as $module) {
            $arDroitSansModule = array_merge($arDroitSansModule,CopixModule::getInformations($module)->credential_notspecific);
            $arDroit[$module] = CopixModule::getInformations($module)->credential;
            
            //Creation des droits si ils n'existent pas
            foreach ($arDroit[$module] as $name=>$values) {
                 $results = _dao('modulecredentials')->findBy(_daoSP()->addCondition('name_mc','=',$name)
                                                                      ->addCondition('module_mc','=',$module)
                                                             );
                                                             
                $id_mc = null;
                if (count($results) == 0) {
                    $record = _record('modulecredentials');
                    $record->name_mc = $name;
                    $record->module_mc = $module;
                    _dao('modulecredentials')->insert($record);
                    $id_mc = $record->id_mc;
                } else {
                    $id_mc = $results[0]->id_mc;
                }
                
                foreach ($values as $value) {
                    $results = _dao('modulecredentialsvalues')->findBy(_daoSP()->addCondition('id_mc','=',$id_mc)
                                                                         ->addCondition('value_mcv','=',$value->name)
                                                                         ->addCondition('level_mcv','=',$value->level)
                                                             );
                    if (count($results) == 0) {
                        $record = _record('modulecredentialsvalues');
                        $record->id_mc = $id_mc;
                        $record->value_mcv = $value->name;
                        $record->level_mcv = $value->level;
                        _dao('modulecredentialsvalues')->insert($record);
                    }
                }
            }
            //Fin de création des droits
        }
        
        
        $arData = array();
        $arModuleCredential = _dao('modulecredentials')->findBy(_daoSP()->groupBy('module_mc'));
        
        
        foreach ($arModuleCredential as $module) {
            $module = $module->module_mc;
            
            $droits = new StdClass ();
            $droits->name = $module;
            $droits->delete = false;
            if ($module != null) {
                if (!isset($arDroit[$module]) || count($arDroit[$module])==0) {
                    $droits->delete = true;
                }
            }
            
            $arMc = _dao('modulecredentials')->findBy(_daoSP()->addCondition('module_mc', '=', $module));
            
            $arDroitMc = array ();
            foreach ($arMc as $mc) {
                $arDroitMCTemp = new stdClass();
                $arDroitMCTemp->record = $mc;
                $arDroitMCTemp->checked = (count(_dao('modulecredentialsgroups')->findBy(_daoSP()->addCondition('id_mc','=',$mc->id_mc)->addCondition('id_mcv', '=', null)->addCondition('id_group','=',$id_group)->addCondition('handler_group','=',$handler_group)))>0) ? 'checked' : '';
                $arDroitMCTemp->delete = true;
                if ($module != null) {
                    if (isset($arDroit[$module]) && isset ($arDroit[$module][$mc->name_mc])) {
                        $arDroitMCTemp->delete = false;
                    }
                } else {
                    if (isset ($arDroitSansModule[$mc->name_mc])) {
                        $arDroitMCTemp->delete = false;
                    }
                }
                $arValues = array();
                foreach (_dao('modulecredentialsvalues')->findBy(_daoSP()->addCondition('id_mc', '=', $mc->id_mc)->orderBy('level_mcv')) as $value) {
                    $value->checked = (count(_dao('modulecredentialsgroups')->findBy(_daoSP()->addCondition('id_mc','=',$mc->id_mc)->addCondition('id_mcv', '=', $value->id_mcv)->addCondition('id_group','=',$id_group)->addCondition('handler_group','=',$handler_group)))>0) ? 'checked' : '';
                    $value->delete = true;
                    if ($module != null) {
                        if (isset($arDroit[$module]) && isset ($arDroit[$module][$mc->name_mc])) {
                            $valueName = $value->value_mcv;
                            foreach ($arDroit[$mc->module_mc][$mc->name_mc] as $ssDroit) {
                                if ($ssDroit->name == $valueName) {
                                    $value->delete = false;
                                }
                            }
                        }
                    } else {
                        if (isset ($arDroitSansModule[$mc->name_mc])) {
                            $valueName = $value->value_mcv;
                            foreach ($arDroitSansModule[$mc->name_mc] as $ssDroit) {
                                if ($ssDroit->name == $valueName) {
                                    $value->delete = false;
                                }
                            }
                        }
                        
                    }
                    $arValues[] = $value;
                }
                
                $arDroitMCTemp->data = $arValues;
                $arDroitMc[] = $arDroitMCTemp;
            }

            $droits->data = $arDroitMc;

            $arData[] = $droits;
        }        
        return _arPpo (new CopixPpo(array('id_group'=>$id_group,'handler_group'=>$handler_group,'list'=>$arData,'url_return'=>_request('url_return',_url('#')))), 'modules.list.php');
    }
    
    /**
     * Enregistre les droits séléctionné
     */
    public function processRecord () {
        CopixRequest::assert('id_group','handler_group');
        $bool = _request('bool',array());
        foreach (_request('value',array()) as $value) {
            $arValue = explode('|', $value);
            $result = _dao('modulecredentialsgroups')->findBy(_daoSP()->addCondition('id_mc','=',$arValue[0])->addCondition('id_mcv','=',isset($arValue[1]) ? $arValue[1] : null)->addCondition('id_group','=',_request('id_group'))->addCondition('handler_group','=',_request('handler_group')));
            if (!isset($bool[$value]) && isset($result[0])) {
                _dao('modulecredentialsgroups')->delete($result[0]->id_mcg);
            } elseif (isset($bool[$value]) && !isset($result[0])) {
                $record = _record('modulecredentialsgroups');
                $record->id_group = _request('id_group');
                $record->handler_group = _request('handler_group');
                $record->id_mc = $arValue[0];
                $record->id_mcv = isset($arValue[1]) ? $arValue[1] : null;
                _dao('modulecredentialsgroups')->insert($record);
            }
        }
        return _arRedirect (_url('auth|module|list',array('id_group'=>_request('id_group'), 'handler_group'=>_request('handler_group'),'url_return'=>_request('url_return'))));
    }
    
    /**
     * Efface tous les droits associés à un module
     */
    public function processDeleteModule () {
        if (CopixRequest::exists('moduleToDelete')) {
            $module = _request('moduleToDelete');
            foreach (_dao('modulecredentials')->findBy(_daoSP()->addCondition('module_mc','=',$module)) as $mc) {
                _dao('modulecredentials')->delete($mc->id_mc);
                _dao('modulecredentialsgroups')->deleteBy(_daoSP()->addCondition('id_mc','=',$id_mc));
            }
            
        }
        return _arRedirect (_url('auth|module|list',array('id_group'=>_request('id_group'),'handler_group'=>_request('handler_group'),'url_return'=>_request('url_return'))));
    }
    
    /**
     * Efface tous les droits associés a un id_mc ou un id_mcv
     */
    public function processDelete () {
        $id_mc = _request('id_mc');
        if ($id_mc !== null) {
            _dao('modulecredentials')->delete($id_mc);
            _dao('modulecredentialsvalues')->deleteBy(_daoSP()->addCondition('id_mc','=',$id_mc));
            _dao('modulecredentialsgroups')->deleteBy(_daoSP()->addCondition('id_mc','=',$id_mc));
        }
        $id_mcv = _request('id_mcv');
        if ($id_mcv !== null) {
            _dao('modulecredentialsvalues')->delete($id_mcv);
            _dao('modulecredentialsgroups')->deleteBy(_daoSP()->addCondition('id_mcv','=',$id_mcv));
        }
        return _arRedirect (_url('auth|module|list',array('id_group'=>_request('id_group'),'handler_group'=>_request('handler_group'),'url_return'=>_request('url_return'))));
    }
    
}
?>