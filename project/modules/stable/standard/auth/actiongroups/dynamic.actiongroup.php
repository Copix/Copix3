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
 * Page de gestion des droits dynamic
 * @package standard
 * @subpackage auth
 */
class ActionGroupDynamic extends CopixActionGroup {
    /**
	 * On s'assure que pour ces tâche ce soit bien un administrateur
	 */
	public function beforeAction ($pActionName){
		CopixAuth::getCurrentUser()->assertCredential ('basic:admin');
		if (!CopixConfig::instance()->copixauth_isRegisteredCredentialHandler ('auth|dbdynamiccredentialhandler')) {
		    throw new CopixException (_i18n('auth.dynamicHandlerNotRegister'));
		}
		
	}
	
    public function processList () {
        CopixRequest::assert('id_group','handler_group');
        $id_group = _request('id_group');
        $handler_group = _request('handler_group');
        
        $arData = array();

        $arDc = _dao('dynamiccredentials')->findAll();

        $arDroitDc = array ();
        foreach ($arDc as $dc) {
            $arDroitDCTemp = new StdClass();
            $arDroitDCTemp->record = $dc;
            $arDroitDCTemp->checked = (count(_dao('dynamiccredentialsgroups')->findBy(_daoSP()->addCondition('id_dc','=',$dc->id_dc)->addCondition('id_dcv', '=', null)->addCondition('id_group','=',$id_group)->addCondition('handler_group','=',$handler_group)))>0) ? 'checked' : '';
            $arDroitDCTemp->delete = true;
            $arValues = array();
            foreach (_dao('dynamiccredentialsvalues')->findBy(_daoSP()->addCondition('id_dc', '=', $dc->id_dc)->orderBy('level_dcv')) as $value) {
                $value->checked = (count(_dao('dynamiccredentialsgroups')->findBy(_daoSP()->addCondition('id_dc','=',$dc->id_dc)->addCondition('id_dcv', '=', $value->id_dcv)->addCondition('id_group','=',$id_group)->addCondition('handler_group','=',$handler_group)))>0) ? 'checked' : '';
                $arValues[] = $value;
            }
            
            $arDroitDCTemp->data = $arValues;
            $arDroitDc[] = $arDroitDCTemp;
        }


        return _arPpo (new CopixPpo(array('id_group'=>$id_group,'handler_group'=>$handler_group,'list'=>$arDroitDc,'url_return'=>_request('url_return',_url('#')))), 'dynamics.list.php');
    }
    
    /**
     * Enregistre les droits séléctionné
     */
    public function processRecord () {
        CopixRequest::assert('id_group','handler_group');
        $bool = _request('bool',array());
        foreach (_request('value',array()) as $value) {
            $arValue = explode('|', $value);
            $result = _dao('dynamiccredentialsgroups')->findBy(_daoSP()->addCondition('id_dc','=',$arValue[0])->addCondition('id_dcv','=',isset($arValue[1]) ? $arValue[1] : null)->addCondition('id_group','=',_request('id_group'))->addCondition('handler_group','=',_request('handler_group')));
            if (!isset($bool[$value]) && isset($result[0])) {
                _dao('dynamiccredentialsgroups')->delete($result[0]->id_dcg);
            } elseif (isset($bool[$value]) && !isset($result[0])) {
                $record = _record('dynamiccredentialsgroups');
                $record->id_group = _request('id_group');
                $record->handler_group = _request('handler_group');
                $record->id_dc = $arValue[0];
                $record->id_dcv = isset($arValue[1]) ? $arValue[1] : null;
                _dao('dynamiccredentialsgroups')->insert($record);
            }
        }
        return _arRedirect (_url('auth|dynamic|list',array('id_group'=>_request('id_group'), 'handler_group'=>_request('handler_group'),'url_return'=>_request('url_return'))));
    }

    
    /**
     * Efface tous les droits associés a un id_dc ou un id_dcv
     */
    public function processDelete () {
        $id_dc = _request('id_dc');
        if ($id_dc !== null) {
            _dao('dynamiccredentials')->delete($id_dc);
            _dao('dynamiccredentialsvalues')->deleteBy(_daoSP()->addCondition('id_dc','=',$id_dc));
            _dao('dynamiccredentialsgroups')->deleteBy(_daoSP()->addCondition('id_dc','=',$id_dc));
        }
        $id_dcv = _request('id_dcv');
        if ($id_dcv !== null) {
            _dao('dynamiccredentialsvalues')->delete($id_dcv);
            _dao('dynamiccredentialsgroups')->deleteBy(_daoSP()->addCondition('id_dcv','=',$id_dcv));
        }
        return _arRedirect (_url('auth|dynamic|list',array('id_group'=>_request('id_group'),'handler_group'=>_request('handler_group'),'url_return'=>_request('url_return'))));
    }
}