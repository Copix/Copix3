<?php
/**
 * @package		copix
 * @subpackage	forms
 */

/**
 * Gère l'affichage des stats et contenus des formulaires saisis.
 * @package		copix
 * @subpackage	forms
 * @author		Sylvain VUIDART
 */
class ActionGroupStats extends CopixActionGroup{
	
    protected function _beforeAction ($pAction) {
        CopixPage::add ()->setIsAdmin (true);
        if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
        _ioClass ('HeadingElementInformationServices')->breadcrumbAdmin ();
    }
		
	public function processGetStats (){
		$ppo = _ppo();
		$ppo->TITLE_PAGE = "Saisies des formulaires";
		$sessionOptionName = 'form|stats|options';
		$options = CopixSession::exists ($sessionOptionName) ? CopixSession::get($sessionOptionName) : _ppo();
		$options->page = _request('page', 1);
		$options->dateDebut = _request('dateDebut', $options->dateDebut);
		$options->dateFin = _request('dateFin', $options->dateFin);
		$options->selectedForm = _request('form_id', $options->selectedForm);
		$options->nbrParPage = _request('nbrParPage', $options->nbrParPage) ? _request('nbrParPage', $options->nbrParPage) : 20;
		
		$liste = DAOcms_form::instance ()->getList();
		$arForms = array();
		
		foreach ($liste as $id=>$form){
			$heading = _ioClass('heading|headingelementinformationservices')->getById($id, 'form');
			$hierarchy = explode('-', $heading->hierarchy_hei);
			if (!array_key_exists($hierarchy[1], $arForms)){
				$arForms[$hierarchy[1]] = array();
			}
			$arForms[$hierarchy[1]][$id] = $form;
		}
		
		
		$ppo->arCMSForms = $arForms; 
		
		if ($options->selectedForm){
			$ppo->formFields = _ioClass('form|form_service')->getFormFields ($options->selectedForm);
			
			$dateDebut = null;
			if($options->dateDebut){
				list($day, $month, $year) = explode('/', $options->dateDebut);
				$dateDebut = $year . "-" . $month . "-" . $day;
			}
			$dateFin = null;
			if($options->dateFin){
				list($day, $month, $year) = explode('/', $options->dateFin);
				$dateFin = $year . "-" . $month . "-" . $day;
			}
			$datesEnvois = _ioClass('form|form_service')->getDatesEnvois ($options->selectedForm, $dateDebut, $dateFin);
			$options->nbrPages = ceil (count ($datesEnvois) / $options->nbrParPage);
			// Extraction des éléments à afficher parmi les pages obtenues
			$ppo->datesEnvois = _request('all', false) ? $datesEnvois : array_slice ($datesEnvois, ($options->page - 1) * $options->nbrParPage, $options->nbrParPage);			
		}
		
		$ppo->options = $options;
		CopixSession::set($sessionOptionName, $options);

		return _request('csv', false) ? $this->_getCsv($ppo) : _arPPO($ppo, 'stats/saisies.php');
	}
	
	private function _getCsv ($pPpo){
		CopixFile::createDir(COPIX_CACHE_PATH."form/");
		try{
			CopixFile::delete(COPIX_CACHE_PATH."form/formulaire".$pPpo->options->selectedForm.".csv");
		} catch (Exception $e){
			_log('Fichier de stat '.COPIX_CACHE_PATH."form/formulaire".$pPpo->options->selectedForm.".csv supprimé avant nouvelle création", 'debug');
		}
		$csv = new CopixCsv(COPIX_CACHE_PATH."form/formulaire".$pPpo->options->selectedForm.".csv");		

		if ($pPpo->options->selectedForm){ 
			$arTh = array("Date");
			if (!empty($pPpo->formFields)){
				foreach ($pPpo->formFields as $field){
					$arTh[] = $field->cfe_label;
				}
			}
			$csv->addLine($arTh);
		}
		if (!empty($pPpo->datesEnvois)){
			foreach ($pPpo->datesEnvois as $envoi){
				$arInfosEnvoi = array();
				$arInfosEnvoi[] = $envoi->cfv_date;
				$values = _ioClass('form|form_service')->getValues($envoi->cfv_date);
				foreach ($pPpo->formFields as $field){
					if (array_key_exists($field->cfe_id, $values)){
						$arInfosEnvoi[] = is_array($values[$field->cfe_id]->cfv_value) ? implode(" - ", $values[$field->cfe_id]->cfv_value) : $values[$field->cfe_id]->cfv_value;
					} else {
						$arInfosEnvoi[] = "-";
					}
				}
				$csv->addLine($arInfosEnvoi);
			}
		}
		return _arFile(COPIX_CACHE_PATH."form/formulaire".$pPpo->options->selectedForm.".csv");
	}
}
?>