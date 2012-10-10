<?php
/**
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * DAORecord de la table cms_form_content
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */
class DAORecordcms_form_content extends CompiledDAORecordcms_form_content {

    /**
     * Flag obligatoire
     * @var int
     */
	public $cfc_required = 0;

    public $cfc_orientation = 0;

	/**
	 * Indique si le chmaps est obligatoire
	 * @return boolean
	 */
	public function isRequired() {
		return $this->cfc_required != 0;
	}
	
}
