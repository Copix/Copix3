<?php
/**
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * DAORecord de la table cms_form_bloc_content
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */
class DAORecordcms_form_bloc_content extends CompiledDAORecordcms_form_bloc_content {

    /**
     * Flag champs obligatoire
     * @var int
     */
	public $cfbc_required = 0;

	/**
	 * Indique si le chmaps est obligatoire
	 * @return boolean
	 */
	public function isRequired() {
		return $this->cfbc_required != 0;
	}

}