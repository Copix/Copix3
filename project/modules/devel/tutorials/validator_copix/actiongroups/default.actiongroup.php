<?php
/**
 * @package		tutorials
 * @subpackage 	validator_copix
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions par défaut de validator_copix
 * @package		tutorials
 * @subpackage 	validator_copix
 */
class ActionGroupDefault extends CopixActionGroup {

	/**
	 * Action d'accueil
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault (){
		$ppo = new CopixPpo ();
		return _arPpo ($ppo, 'default.form.php');
	}

	/**
	 * Opération de validation selon la méthode demandeée
	 *
	 * @return CopixActionRetugn
	 */
	public function processValid (){
		switch (_request ('method')) {
			case 'std':
				return $this->processValidStd ();
				break;
			case 'i18n':
				return $this->processValidI18n ();
				break;
			case 'complex':
				return $this->processValidComplex ();
				break;
		}
	}
	
	/**
	 * Validation à base d'élements standard
	 * 
	 * @return CopixActionReturn
	 */
	public function processValidStd (){
		$arError = array();		
		if (_validator ('notempty')->check (_request('nom')) !== true){
			$arError[] = 'Nom de l\'utilisateur obligatoire';
		}

		if (_validator ('notempty')->check (_request('prenom')) !== true){
			$arError[] = 'Prénom de l\'utilisateur obligatoire';
		}

		if (_validator ('date')->check (_request('date')) !== true){
			$arError[] = 'Erreur sur la date de naissance';
		}

		if (_cValidator ()->attach (_validator ('notEmpty'))->attach (_validator ('phone'))->check (_request('telephone')) !== true){
			$arError[] = 'Erreur sur le téléphone';
		}

		$ppo = new CopixPpo ();
		$ppo->errors = $arError;
		return _arPpo ($ppo, 'default.form.php');
	}

	/**
	 * Même fonction que précèdemment mais internationalisée
	 *
	 * @return CopixActionReturn
	 */
	public function processValidI18n (){
		$arError = array();
		if (_validator ('notempty')->check (_request('nom')) !== 'true') {
			$arError[] = _i18n ('validator_copix.form.error.nom');
		}

		if (_validator ('notempty')->check (_request('prenom')) !== 'true'){
			$arError[] = _i18n ('validator_copix.form.error.prenom');
		}

		if (_validator ('date')->check (_request('date')) !== 'true'){
			$arError[] = _i18n ('validator_copix.form.error.date');
		}

		if (_ctValidator ()->attach (_validator ('notEmpty'))->attach (_validator ('phone'))->check (_request('telephone')) !== 'true'){
			$arError[] = _i18n ('validator_copix.form.error.phone');
		}

		$ppo = new CopixPpo ();
		$ppo->errors = $arError;
		return _arPpo ($ppo, 'default.form.php');
	}
	
	/**
	 * Même fonction que précèdemment mais internationalisée
	 *
	 * @return CopixActionReturn
	 */
	public function processValidComposite (){
		$arError = array();
		if (_validator ('notempty')->check (_request('nom')) !== 'true') {
			$arError[] = _i18n ('validator_copix.form.error.nom');
		}

		if (_validator ('notempty')->check (_request('prenom')) !== 'true'){
			$arError[] = _i18n ('validator_copix.form.error.prenom');
		}

		if (_validator ('date')->check (_request('date')) !== 'true'){
			$arError[] = _i18n ('validator_copix.form.error.date');
		}

		if (_ctValidator ()->attach (_cValidator() ->attach (_validator ('notEmpty'))->attach (_validator ('phone')), 'telephone') !== 'true'){
			$arError[] = _i18n ('validator_copix.form.error.phone');
		}

		$ppo = new CopixPpo ();
		$ppo->errors = $arError;
		return _arPpo ($ppo, 'default.form.php');
	}
	
	/**
	 * Validation à l'aide de validateurs complexes
	 * 
	 * @return CopixActionReturn 
	 */
	public function processValidComplex (){
		$ppo = new CopixPpo ();
		$validateur = _ctValidator (); // _ctValidator équivaut à un new CopixComplexTypeValidator  ();
		$validateur->attachTo (_validator ('notempty'), array ('nom', 'prenom')); 
		$validateur->attachTo (_validator ('date'), 'datenaissance');
		// Dernière étape on attache un validateur composite
		$validateur->attachTo (_cValidator() ->attach (_validator ('notEmpty'))->attach (_validator ('phone')), 'telephone');
		
		$ppo->errors = $validateur->check (CopixRequest::asArray());;
		return _arPpo ($ppo, 'default.form.php');		
	}

}