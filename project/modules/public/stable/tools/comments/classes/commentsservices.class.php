<?php
/**
* @package		tools 
 * @subpackage	comments
 * @author	 	Fersing Estelle
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */


 /**
  * Services sur les commentaires
* @package		tools 
 * @subpackage	comments
  */
class CommentsServices {
	/*
	 * Tableau privé qui contiendra les différents erreurs trouvées
	 */
	private $_arrErrors = array ();

	/*
	 * Cette fonction permet de récupérer un identifiant de page 
	 * à partir d'information passée dans $pId et séparée par des ;
	 * @param $pId identifiant initial (différent champs séparés par des ;)
	 * @return string identifiant de notre page
	 */
	public function getId ($pId) {
		$ar = array ();

		foreach (explode(';', $pId) as $name){
			if (count ($parts = explode ('=', $name)) == 2){
				$ar[$parts[0]] = $parts[1];  
			}else{
			   	$ar[$name] = _request ($name);
			}
		}

		$idString = array ();
		asort($ar);
		foreach ($ar as $key=>$elem){
		    $idString[] = $key.'='.$elem;
		}

		$idString = implode ('&', $idString);
		return $idString;
	}
	
	/**
	 * Ajoute dans la liste des commentaires authorisés un élément et en indique l'identifiant
	 * @param	array	$pCommentInformations informations sur le commentaire authorisé
	 * @return 	int	l'identifiant du commentaire 
	 */
	public function addEnabled ($pCommentInformations) {
		//_log ('Ajout '.$pCommentInformations['id']);
		//_log ('Page '.$pCommentInformations['fromPage']);		
		$_SESSION['COMMENTS']['Enabled'][$pCommentInformations['id']] = $pCommentInformations;
	}
	
	/**
	 * Récupère les inforamtions sur le commentaire authorisé. 
	 * @return	array	informations sur le commentaire ou false si non authorisé
	 */
	public function getEnabled ($pId) {
		if (isset ($_SESSION['COMMENTS']['Enabled'][$pId])){
			$toReturn = $_SESSION['COMMENTS']['Enabled'][$pId]; 
			if (isset ($toReturn['object'])){
				_daoInclude ('comments');
				$toReturn['object'] = unserialize ($toReturn['object']);
			}
			return $toReturn;			
		}
		return  false;
	}
	
	/**
	 * On enlève l'élément commentable de la liste.
	 * @param	string	$pId	L'élément que l'on supprime des éléments commentables
	 */
	public function removeEnabled ($pId) {
		$_SESSION['COMMENTS']['Enabled'][$pId] = null;		
	}
	
	/**
	 * Met à jour en session les valeurs que l'utilisateur à tenté d'insérer.
	 * @param	object	$pObject	L'élément qui contient le record 
	 */
	public function updateEnabled ($pObject){
		if (isset ($_SESSION['COMMENTS']['Enabled'][$pObject->page_comment])){
			$_SESSION['COMMENTS']['Enabled'][$pObject->page_comment]['object'] = serialize ($pObject);
		}		
	}
}
?>