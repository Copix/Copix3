<?php
/**
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */


/**
 * DAO de la table cms_form_bloc
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */
class DAOCms_form_bloc extends CompiledDAOCms_form_bloc {

    /**
     * Sauvegarde d'un bloc
     * @param DAORecordCms_form_bloc $record
     * @return boolean
     */
    public function save ($pRecord) {

        try {
            CopixDB::begin();
            if ($pRecord->cfb_id == null) {
                $this->insert($pRecord);
            } else {
                $this->update($pRecord);
            }

            //Suppression de l'ancien contenu
            $sp = _daoSP()->addCondition('cfbc_id_bloc', '=', $pRecord->cfb_id);
            DAOcms_form_bloc_content::instance ()->deleteBy($sp);

            //Ajout des nouveaux champs
            if (isset($pRecord->form_field) && is_array($pRecord->form_field)) {
                foreach ($pRecord->form_field as $key => $fieldId) {
                    $cfbc = DAORecordcms_form_bloc_content::create ();
                    $cfbc->cfbc_id_bloc = $pRecord->cfb_id;
                    $cfbc->cfbc_id_element = $fieldId;
                    $cfbc->cfbc_order = ($key + 1) * Form_Service::ORDER_STEP;
                    DAOcms_form_bloc_content::instance ()->insert($cfbc);
                }
            }
            CopixDB::commit();

        } catch (Exception $e) {
            CopixDB::rollback();
            return false;
        }

        return true;
    }

    public function getWithContent($pIdBloc) {
        $record = $this->get($pIdBloc);
        if ($record === false) {return false;}
        $sp = _daoSP()->addCondition('cfbc_id_bloc', '=', $record->cfb_id);
        $record->content = DAOcms_form_bloc_content::instance ()->findBy($sp);
        return $record;
    }
    
    /**
     * Récupération des données pour l'affichage du bloc
     */
    public function getForDisplay($pIdBloc) {
        $record = $this->get($pIdBloc);
        if ($record === false) {return false;}

        $query = <<<QUERY
SELECT cms_form_bloc_content.*, cms_form_element.*
FROM cms_form_bloc_content
INNER JOIN cms_form_element on (cms_form_bloc_content.cfbc_id_element = cms_form_element.cfe_id)
WHERE cfbc_id_bloc = :idBloc
AND cms_form_element.cfe_deleted_at IS NULL
QUERY;

        $arResult = _doQuery($query, array(':idBloc' => $pIdBloc));

		$arContent = array();
		foreach ($arResult as $blocContent) {
			$record = DAORecordcms_form_bloc_content::create ();
			$record->initFromDBObject($blocContent);
			$record->cfe_label = $blocContent->cfe_label;
			$record->cfe_type = $blocContent->cfe_type;
			$record->cfe_orientation = $blocContent->cfe_orientation;
			$record->cfe_columns = $blocContent->cfe_columns;
			$record->cfe_default = $blocContent->cfe_default;
			$record->cfe_default_data = $blocContent->cfe_default_data;
			$arContent[] = $record;
		}

        $record->content = $arContent;

        return $record;
    }

}