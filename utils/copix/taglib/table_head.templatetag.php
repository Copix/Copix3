<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Croës Gérald
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet la génération rapide des en têtes de table
 * @package    copix
 * @subpackage taglib
 */
class TemplateTagTable_Head extends CopixTemplateTag {
	/**
	 * Buffer de sortie
	 *
	 * @var string
	 */
	protected $_b = '';

	/**
	 * Génération de la sortie du tag
	 *
	 * @param array  $pParams  paramètres du tag
	 * @param string $pContent contenu (pour les tag de type contenu, inutile ici) 
	 */
	public function process ($pContent=null) {
		$class = $this->getParam ('class', '');
		$columns = $this->getParam ('columns', null);

		//récupération des noms de colonne en I18N si pas défini en "standard"
		if (empty ($columns)){
			$columns = array ();
			foreach (_ppo ($this->requireParam ('columnsI18N')) as $columnName){
				$columns[] = _i18n ($columnName);
			}
		}else{
			$columns = _ppo ($columns);
		}

		$this->_b  .= '<table'.(empty ($class) ? '' : ' class="'.$class.'"').'/>';
		$this->_b .= '<thead><tr>'; 
		foreach ($columns as $columnName){
			$this->_b .= '<th>'.$columnName.'</th>';
		}
		$this->_b .= '</tr></thead>';
		return $this->_b;  
	}
}