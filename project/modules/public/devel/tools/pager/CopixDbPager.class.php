<?php
/**
* @package   copix
* @subpackage utils
* @author   David Derigent
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
Copix::RequireClass ('CopixPager');

/**
 * Classe de pagination pour les bases de données
 * @package copix
 * @subpackage utils
 */
class CopixDbPager extends CopixPager {

	/**
     * Tableau des données à traiter
     *
     * Valeur par défaut : tableau vide
     * @var string $query
     * @see createSQL(), sql2array()
     */
	var $recordSet;
	
	/**
     * nbre total  d'enregistrements
     *
     * @access public
     * @var int $nbItemTotal
     */
    var $nbItemTotal;
	
	
	/**
     * permet de passer d'autres paramètres dans l'url d'affichage d'une page
     *
     * Valeur par défaut : chaine vide
     * @var string $othersUriParameters
     * 
     */
	var $othersUriParameters;
	
	/**
	 * 
	 */
	public function __construct ($options) {
		$this-> recordSet = '';
		$this-> nbItemTotal = '';
		$this->othersUriParameters='';
		parent::CopixPager($options);
	}


	/**
     * Retourne le nombre d'enregistrements totals 
     *
     * @access private
     * @since 3.2
     */
	public function getNbRecord() {
		return  $this->nbItemTotal;
	} 
	
	/**
     * Retourne le tableau des données de la page en cours d'affichage
     *
     * @access private
     * @return array
     * @since 3.2
     */
	public function getRecords() {
		return $this-> recordSet;
	}

	/**
     * Initialisation de la classe mode tableau
     *
     * @access private
     * @return void
     * @since 3.2
      */
	public function init() {
		if (!is_array($this-> recordSet)) trigger_error('Propriété <b>recordSet</b> mal configurée <br>', E_USER_ERROR);
	}

	/**
     * Termine l'appel à la classe
     *
     * @access public
     */
	public function close() {
		unset($this-> recordSet);
		return true;
	}
	
	 /**
     * Lien pour la page spécifiée
     *
     * Retourne l'URI complète pour accèder à la page $num_page. Le retour diffère en fonction de la propriété $tplUri.
     * @param  int num_page, numéro de la page pour laquelle il faut créer le lien
     * @access private
     * @return string URI d'accès à la page
     * @since 2.0
     */
    public function getLink($num_page) {
        if (!empty($this-> tplUri)) return preg_replace($this-> getTplPattern('PAGE'), $num_page, $this-> tplUri);
        else {                     
        	$urlPage = $this-> page_file . $this-> varUrl . '=' . $num_page;
        	if($this->othersUriParameters!=''){
        		if( !substr($this->othersUriParameters,0,1)=='&' ){
        			$urlPage.='&';
        		}
        		$urlPage.=$this->othersUriParameters;
        		
        	}
        	return  $urlPage;
        }
    }
}
?>