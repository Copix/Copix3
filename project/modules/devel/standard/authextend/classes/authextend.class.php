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
 * Class permettant la gestion des paramètre utilisateur supplémentaire
 */
class AuthExtend {
	
	
	const TYPE_PICTURE = 'picture';
	const TYPE_TEXT    = 'text';
	
	const FILTER_TEXT_NONE = 'filtre_text_none';
	const FILTER_TEXT_MAIL = 'filtre_text_mail';
	const FILTER_TEXT_TEL  = 'filtre_text_tel';
	
	/**
	 * Contient tous les types de champs possibles
	 *
	 * @var array
	 */
	private $_types = array ();
	
	/**
	 * Filtre applicable au champs de type text
	 * @var array ()
	 */
	private $_filtersText = array ();
	
	/**
	 * Liste des typemimes supportés par authextend pour le type picture
	 * @var array
	 */
	public $arPictTypemimeList = array (
		'image/jpeg'=>'.jpg',
		'image/png'=>'.png',
		'image/gif'=>'.gif',
	);
	
	/**
	 * Constructeur
	 */
	public function __construct () {
		
		$this->_types = array (
			self::TYPE_TEXT=>_i18n ('authextend|authextend.inputtext'),
			self::TYPE_PICTURE=>_i18n ('authextend|authextend.picture')
		);
		
		$this->_filtersText = array (
			self::FILTER_TEXT_NONE=>_i18n ('authextend|authextend.filter_text_none'),
			self::FILTER_TEXT_MAIL=>_i18n ('authextend|authextend.filter_text_mail'),
			self::FILTER_TEXT_TEL=>_i18n ('authextend|authextend.filter_text_tel')
		);
		
	}
	
	/**
	 * valide la liste des parametres personnalisés
	 */
	public function valid ($idForm = NULL) {
		$errors = array ();
		
		$extends = $this->getAll ();
		
		
		foreach ($extends as $extend) {
			
			$request = 'authextend_'.$extend->module.'_'.$extend->id;
		
			switch ($extend->type) {
				
				case self::TYPE_TEXT : {
					
					$value = _request($request, '');
					
					if ($value !== '') {
						
						switch ($extend->parameters ['filter']) {
							
							case self::FILTER_TEXT_MAIL : {
								try {
									CopixFormatter::getMail($value);
								} catch (CopixException $e) {
									$errors['err_filter_mail_'.$extend->id] = array ('authextend|authextend.error.filter_mail', $extend->caption);;
								}
								break;
							}
						
							case self::FILTER_TEXT_TEL : {
								try {
									CopixFormatter::getTelephone ($value);
								} catch (CopixException $e) {
									$errors['err_filter_tel_'.$extend->id] = array ('authextend|authextend.error.filter_tel', $extend->caption);;
								}
								break;
							}
							
							default : 
						}
						
					} else if ($extend->required) {
						$errors ['err_field_empty_'.$extend->id] = array ('authextend|authextend.error.field_empty', $extend->caption);
					}
					break;
				}
				
				case self::TYPE_PICTURE : {
					
					$file = CopixRequest::getFile ($request);
					if ($file) {
						
						if ($file->getSize () > $extend->parameters ['maxsize']) {
							$errors ['err_file_sizemax_'.$extend->id] = array ('authextend|authextend.error.file_sizemax', $extend->parameters ['maxsize'].' '. _i18n ('authextend|authextend.Bytes'));
						}
						
						if (! isset ($this->arPictTypemimeList [$file->getType()])) {
							$errors ['err_file_notypesupport_'.$extend->id] = array ('authextend|authextend.error.file_notypesupport', $file->getType());
						}
					
					// Test les erreurs d'envoie (le champs vide n'est pas une erreur)
					} else if (($err = CopixUploadedFile::getError ($request)) && $err != UPLOAD_ERR_NO_FILE) {
						$errors ['err_file_updload_'.$extend->id] = 'authextend|authextend.error.file_updload';
					
					// Test si le champs est vide mais requis et renvoie une erreur dans cette condition
					} else if ($extend->required && !_request ($request.'_exist', 0)){
						$errors ['err_field_empty_'.$extend->id] = array ('authextend|authextend.error.field_empty', $extend->caption);
					}
					break;
				}
				
				default : throw new CopixException (_i18n ('authextend|authextend.error.type_unknow'));
			}
		}
		
		return $errors;
	}
	
	/**
	 * Insert les valeur de la liste des parametres personnalisés
	 * @param $pId_user Identifiant de l'utilisateur
	 * @param $pId_handler Handler de l'utilisateur
	 */
	public function addValues ($pId_user, $pId_handler) {
		
		$extends = $this->getAll ();
		
		foreach ($extends as $extend) {
			
			$request = 'authextend_'.$extend->module.'_'.$extend->id;
			
			$insert = true;
			if ($record = _ioDAO ('dbuserextendvalue')->get ($extend->id, $pId_user, $pId_handler)) {
				$insert = false;
			} else {
				$record = _record ('dbuserextendvalue');
				
				$record->id_extend      = $extend->id;
				$record->id_user        = $pId_user;
				$record->id_userhandler = $pId_handler;
			}
		
			switch ($extend->type) {
				
				case self::TYPE_TEXT : {
					$value = _request($request, '');
					switch ($extend->parameters ['filter']) {
						case self::FILTER_TEXT_MAIL : $value = CopixFormatter::getMail($value); break;
						case self::FILTER_TEXT_TEL  : $value = CopixFormatter::getTelephone($value); break;
					}
					$record->value = serialize ($value);
					break;
				}
				
				case self::TYPE_PICTURE : {
					$name     = uniqid ('pict_');
					$path     = COPIX_VAR_PATH.'authextend/';
					$pathCache= COPIX_TEMP_PATH.'cache'.DIRECTORY_SEPARATOR.'authextend'.DIRECTORY_SEPARATOR;
					$file     = CopixRequest::getFile ($request);
					
					// Supression demandé
					if (_request ($request.'_remove', 0)) {
						$oldName = unserialize ($record->value);
						// Supression du fichier
						CopixFile::delete ($path . $oldName);
						// Supression du cache
						CopixFile::removeDir ($pathCache.$oldName);
						_ioDAO ('dbuserextendvalue')->delete ($extend->id, $pId_user, $pId_handler);
						continue 2;
					}
					
					if ($file) {
						
						$ext = $this->arPictTypemimeList [$file->getType ()];
						$file->move ($path, $name.$ext);
						if (!$insert) {
							$oldName = unserialize ($record->value);
							CopixFile::delete ($path . $oldName);
							// Supression du cache
							CopixFile::removeDir ($pathCache.$oldName);
						}
						$record->value = serialize ($name.$ext);
					} else {
						continue 2;
					}
					break;
				}
				
				default : throw new CopixException (_i18n ('authextend.error.type_unknow'));
			}
			
			if ($insert) {
				_ioDAO ('dbuserextendvalue')->insert ($record);
			} else {
				_ioDAO ('dbuserextendvalue')->update ($record);
			}
		}
	}
	
	/**
	 * Enregistre en session les valeurs courantes du formulaire
	 * @param string $idForm Identifiant du formulaire dans le cas ou plusieurs pages d'édition sont ouvertes
	 * @return string Identifiant du formulaire 
	 */
	public function setEditSession ($idForm = NULL) {
		
		if (!$idForm) {
			$idForm = uniqid ();
		}
		
		$extends = $this->getAll ();
		
		foreach ($extends as $extend) {
			$request = 'authextend_'.$extend->module.'_'.$extend->id;
			
			switch ($extend->type) {
				case self::TYPE_TEXT : $value = _sessionSet($request, _request ($request, ''), $idForm);
				case self::TYPE_PICTURE : break;
				default : throw new CopixException (_i18n ('authextend|authextend.error.type_unknow'));
			}
		}
		
		return $idForm;
	}
	
	/**
	 * Renvoie les types disponibles pour les champs à ajouter
	 * 
	 * @return array
	 */
	public function getTypes () {
		return $this->_types;
	}
	
	/**
	 * Renvoie les filtre disponibles pour les champs textes
	 * 
	 * @return array
	 */
	public function getFiltersText () {
		return $this->_filtersText;
	}
	
	/**
	 * Renvoie la liste des champs suplémentaire
	 * @param $pId_user Identifiant de l'utilisateur
	 * @param $pId_handler Handler de l'utilisateur
	 * @param $pIdForm Identifiant du formulaire en edition pour recupérer les modifications des champs
	 * @return Iterator
	 */
	public function getAll ($pId_user=NULL, $pId_handler=NULL, $pIdForm=NULL) {
		$extends = _ioDao ('dbuserextend')->findBy (_daoSP()->orderBy ('position'));
		$return = array ();
		
		foreach ($extends as $key=>$extend) {
			
			$extendObj              = new AuthExtendObject;
			$extendObj->id          = $extend->id;
			$extendObj->type        = $extend->type;
			
			$explode = explode ('|', $extend->name);
			
			$extendObj->module      = $explode[0];
			$extendObj->name        = $explode[1];
			$extendObj->caption     = $extend->caption;
			$extendObj->position    = $extend->position;
			$extendObj->required    = $extend->required;
			$extendObj->active      = $extend->active;
			$extendObj->parameters  = unserialize ($extend->parameters);
			if ($extendObj->type == self::TYPE_PICTURE && 
			    $extendObj->parameters['maxsize'] > ($max = CopixUploadedFile::getMaxFileSize()) ) {
				$extendObj->parameters['maxsize'] = $max;
			}
			
			$extendObj->captionType = $this->_types[$extend->type];
			$extendObj->iconType    = _resource ('authextend|img/icon/'.$extend->type.'.gif');
			
			if ($pIdForm) {
				$extendObj->value = _sessionGet ('authextend_'.$extendObj->module.'_'.$extendObj->id, $pIdForm);
			}
			
			if ($extendObj->value === NULL && $pId_user!= NULL && $pId_handler!= NULL) {
				$dao = _ioDAO('dbuserextendvalue')->get ($extendObj->id, $pId_user, $pId_handler);
				if ($dao) {
					$extendObj->value = unserialize ($dao->value);
				}
			}
			
			$return[] = $extendObj;
			
		}
		
		return $return;
	}	
	
	/**
	 * Renvoie vrai si le chamsp name existe deja
	 * @param string $pName
	 * @return boolean
	 */
	public function nameExist ($pName) {
		return _ioDao('dbuserextend') ->countBy (_daoSP ()->addCondition ('name', '=', strtolower ($pName))) != 0;
	}
	
	/**
	 * Renvoie une exception si le chamsp name existe deja
	 * @param string $pName
	 */
	public function assertNameExist ($pName) {
		if ($this->nameExist ($pName)) { 
			throw new CopixException (_i18n ('authextend.error.name_exist'));
		}
	}
	
	/**
	 * Insert un nouvaux type d'information utilisateur
	 * @param string $pType        Type du champs
	 * @param string $pName        Nom du champs
	 * @param string $pCaption     Libellé du champs
	 * @param boool  $pRequired    Requis ou non
	 * @param boool  $pActive      Activé ou non
	 * @param array  $pParameters  Liste de parametres appliqués au champs
	 */
	public function insert ($pType, $pName, $pCaption, $pRequired, $pActive, array $pParameters) {
		
		$this->assertNameExist ($pName);
		
		$record = _record ('dbuserextend');
		$record->position   = 99999999;
		$record->caption    = $pCaption;
		$record->type       = $pType;
		$record->name       = strtolower ($pName);
		$record->required   = (int)$pRequired;
		$record->active     = (int)$pActive;
		$record->parameters = serialize ($pParameters);
		
		_ioDAO ('dbuserextend')->insert ($record);
		
		$record->position   = $record->id;
		_ioDAO ('dbuserextend')->update ($record);
		
	}
	
	/**
	 * Active ou désactive le champs suplémentaire
	 * @param int    $pId          Identifiant du champs suplémentaire
	 * @param string $pType        Type du champs
	 * @param string $pName        Nom du champs
	 * @param string $pCaption     Libellé du champs
	 * @param boool  $pRequired    Requis ou non
	 * @param boool  $pActive      Activé ou non
	 * @param array  $pParameters  Liste de parametres appliqués au champs
	 */
	public function update ($pId, $pType, $pName, $pCaption, $pRequired, $pActive, array $pParameters) {
		
		$record         = _ioDAO ('dbuserextend')->get ($pId);
		if ($record) {
			
			if ($pName != $record->name) {
				$this->assertNameExist ($pName);
				$record->name       = strtolower ($pName);
			}
			$record->caption    = $pCaption;
			$record->type       = $pType;
			$record->required   = (int)$pRequired;
			$record->active     = (int)$pActive;
			$record->parameters = serialize ($pParameters);
			_ioDAO ('dbuserextend')->update ($record);
			return;
		}
		throw new CopixException (_i18n ('authextend.error.id_unknow'));
	}
	
	/**
	 * Supprime le champs suplémentaire
	 * @param int $pId Identifiant du champs suplémentaire
	 */
	public function delete ($pId) {
		$record         = _ioDAO ('dbuserextend')->get ($pId);
		if ($record) {
			_ioDAO ('dbuserextend')->delete ($pId);
			return;
		}
		throw new CopixException (_i18n ('authextend.error.id_unknow'));
	}
	

	/**
	 * Monte le champs le champs suplémentaire
	 * @param int $pId Identifiant du champs suplémentaire
	 */
	public function up ($pId) {
		$record         = _ioDAO ('dbuserextend')->get ($pId);
		
		if ($record) {
			
			$dao = _ioDAO ('dbuserextend')->findBy (
				_daoSP()
					->addCondition('position', '<', $record->position)
					->orderBy (array  ('position', 'DESC'))
					->setCount (1)
			);
			
			if (count($dao)) {
				$dao = $dao[0];
				
				$position         = $dao->position;
				$dao->position    = $record->position;
				$record->position = $position;
				
				_ioDAO ('dbuserextend')->update ($record);
				_ioDAO ('dbuserextend')->update ($dao);
			}
			
			return;
		}
		throw new CopixException (_i18n ('authextend.error.id_unknow'));
	}
	
	/**
	 * Descend le champs suplémentaire
	 * @param int $pId Identifiant du champs suplémentaire
	 */
	public function down ($pId) {
		$record         = _ioDAO ('dbuserextend')->get ($pId);
		
		if ($record) {
			
			$dao = _ioDAO ('dbuserextend')->findBy (
				_daoSP()
					->addCondition('position', '>', $record->position)
					->orderBy (array  ('position', 'ASC'))
					->setCount (1)
			);
			
			if (count($dao)) {
				$dao = $dao[0];
				
				$position         = $dao->position;
				$dao->position    = $record->position;
				$record->position = $position;
				
				_ioDAO ('dbuserextend')->update ($record);
				_ioDAO ('dbuserextend')->update ($dao);
			}
			
			return;
		}
		throw new CopixException (_i18n ('authextend.error.id_unknow'));
	}
	
}
	
class AuthExtendObject {
	
	var $id;
	var $name;
	var $module;
	var $caption;
	var $position;
	var $required;
	var $active;
	var $parameters;
	var $captionType;
	var $iconType;
	var $value = NULL;
	
}
	
?>