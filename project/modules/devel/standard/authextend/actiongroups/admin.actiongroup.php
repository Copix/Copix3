<?php
/**
 * @package     standard
 * @subpackage  authextend
 * @author      Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions réalisées par le framework
 * @package standard
 * @subpackage authextend
 */
class ActionGroupAdmin extends CopixActionGroup {
	
	/**
	 * On protège la page avec les droits d'administration
	 */
	protected function beforeAction ($pActionName) {
		_currentUser ()->assertCredential ('basic:admin');
		_notify ('breadcrumb', array ('path' => array ('authextend|admin|' => _i18n ('authextend|breadcrumb.admin')) ));
	}
	
	
	/**
	* Redirige vers l'edition
	 * @return CopixActionReturn
	*/
	protected function processDefault () {
		return $this->processEdit ();
	}
	
	
	/**
	 * Edition des parametres utilisateur personnalisés
	 * @return CopixActionReturn
	 */
	protected function processEdit () {
		
		$ppo         = _rPPO ();
		$ppo->errors = _request('errors', array ());
		
		// Assignation de valeur au tempalte
		$ppo->form->type        = _request ('type', AuthExtend::TYPE_TEXT );
		$ppo->form->caption     = _request ('caption');
		$ppo->form->name        = _request ('name');
		$ppo->form->required    = CopixRequest::getBoolean ('required', CopixRequest::getBoolean('required_cb', false));
		$ppo->form->active      = CopixRequest::getBoolean ('active'  , CopixRequest::getBoolean('active_cb'  , true));
		$ppo->form->maxsize     = CopixRequest::getInt('maxsize'  , 300000);
		$ppo->form->width       = CopixRequest::getInt('width'    , 50);
		$ppo->form->height      = CopixRequest::getInt('height'   , 50);
		$ppo->form->maxlength   = CopixRequest::getInt('maxlength', 255);
		$ppo->form->filtersText = _request ('filtersText', AuthExtend::FILTER_TEXT_NONE);
		
		
		$ppo->arType         = _ioClass ('authextend|authextend')->getTypes ();
		$ppo->arFiltersText  = _ioClass ('authextend|authextend')->getFiltersText ();
		
		try {
			if (_request('adder')) {
			
				if ($ppo->form->name == '')                        { throw new CopixException (_i18n ('authextend.error.name_empty')); }
				if (preg_match('/[^0-9A-Za-z]/',$ppo->form->name)) { throw new CopixException (_i18n ('authextend.error.noalphanum')); }
				if ($ppo->form->caption == '')                     { throw new CopixException (_i18n ('authextend.error.caption_empty')); }
				
				$parameters = array ();
				switch ($ppo->form->type) {
					
					case AuthExtend::TYPE_TEXT : {
						$parameters['maxlength'] = $ppo->form->maxlength;
						$parameters['filter'] = $ppo->form->filtersText;
						break;
					}
					
					case AuthExtend::TYPE_PICTURE : {
						$parameters['maxsize'] = $ppo->form->maxsize;
						$parameters['height']  = $ppo->form->height;
						$parameters['width']   = $ppo->form->width;
						break;
					}
					
					default : throw new CopixException (_i18n ('authextend.error.type_unknow'));
				}
				
				//Insertion d'un nouvelle element
				_ioClass ('authextend|authextend')->insert (
					$ppo->form->type,
					'authextend|'.$ppo->form->name,
					$ppo->form->caption,
					$ppo->form->required,
					$ppo->form->active,
					$parameters
				);
				
			}
		} catch (CopixException $e){
			$ppo->errors[] = $e->getMessage ();
		}
		
		//recuperation de la liste des elemens
		
		$ppo->extends = _ioClass ('authextend|authextend')->getAll ();
		$ppo->MAX_FILE_SIZE = CopixUploadedFile::getMaxFileSize ();
		
		return _arPPO($ppo, 'admin.edit.tpl');
	}
	
	
	/**
	 * Supression du champs personnalisé
	 * @return CopixActionReturn
	 */
	protected function processDelete () {
		
		CopixRequest::assert ('id');
		// Confirmation de supression
		if (_request ('confirm') == 1){
			$errors = array ();
			try {
				_ioClass ('authextend|authextend')->delete (CopixRequest::getInt('id'));
			} catch (CopixException $e){
				$errors[] = $e->getMessage ();
			}
			
			return _arRedirect (_url ('authextend|admin|edit', array ('errors'=>$errors)));
		
		} else {
			
			if (! ($extend = _ioDAO ('dbuserextend')->get (CopixRequest::getInt ('id')))){
				return _arRedirect (_url ('authextend|admin|edit', array ('errors'=>_i18n ('authextend.error.id_unknow'))));
			}
			return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>_i18n ('authextend.confirmDeleteUser', $extend->name),
						'confirm'=>_url ('authextend|admin|delete', array ('id'=>$extend->id, 'confirm'=>1)),
						'cancel'=>_url ('authextend|admin|edit')));
		}
	}
	
	
	/**
	 * Mise à jour du champs personnalisé
	 * @return CopixActionReturn
	 */
	protected function processUpdate () {
		CopixRequest::assert ('id');
		$id = CopixRequest::getInt('id');
		$errors = array ();
		
		try {
			
			if (_request ('name_'.$id) == '')                        { throw new CopixException (_i18n ('authextend.error.name_empty')); }
			if (preg_match('/[^0-9A-Za-z]/',_request ('name_'.$id))) { throw new CopixException (_i18n ('authextend.error.noalphanum')); }
			if (_request ('caption_'.$id) == '')                     { throw new CopixException (_i18n ('authextend.error.caption_empty')); }
				
			switch (_request ('type_'.$id)) {
				
				case AuthExtend::TYPE_TEXT : {
					$parameters['maxlength'] = _request ('maxlength_'.$id);
					$parameters['filter']    = _request ('filtersText_'.$id, AuthExtend::FILTER_TEXT_NONE);
					break;
				}
				
				case AuthExtend::TYPE_PICTURE : {
					$parameters['maxsize'] = _request ('maxsize_'.$id);
					$parameters['height']  = _request ('height_'.$id);
					$parameters['width']   = _request ('width_'.$id);
					break;
				}
				
				default : throw new CopixException (_i18n ('authextend.error.type_unknow'));
			}
			
			//Modificationd'un element
			_ioClass ('authextend|authextend')->update (
				$id,
				_request ('type_'.$id),
				'authextend|'._request ('name_'.$id),
				_request ('caption_'.$id),
				_request ('required_'.$id),
				_request ('active_'.$id),
				$parameters
			);
			
		} catch (CopixException $e){
			$errors[] = $e->getMessage ();
		}
		
		return _arRedirect (_url ('authextend|admin|edit', array ('errors'=>$errors)));
	}
	
	
	/**
	 * Deplacement vers le haut d'un champs personnalisé
	 * @return CopixActionReturn
	 */
	protected function processUp () {
		
		CopixRequest::assert ('id');
		
		_ioClass ('authextend|authextend')->up (CopixRequest::getInt('id'));
		
		return _arRedirect (_url ('authextend|admin|edit'));
	}
	

	/**
	 * Deplacement vers le bas d'un champs personnalisé
	 * @return CopixActionReturn
	 */
	protected function processDown () {
		
		CopixRequest::assert ('id');
		
		_ioClass ('authextend|authextend')->down (CopixRequest::getInt('id'));
		
		return _arRedirect (_url ('authextend|admin|edit'));
	}
	
	
}
?>