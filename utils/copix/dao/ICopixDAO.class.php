<?php
/**
 * @package	copix
 * @subpackage	dao
 * @author	Croës Gérald
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Définition de l'interface ICopixDAO
 * On ne met pas get ni delete car les paramètres varient en fonction du nombre de clef
 *
 * @package		copix
 * @subpackage	dao
 */
interface ICopixDAO {
    /**
     * Recherche selon des critères
     *
     * @param CopixDAOSearchParams $pSp Paramètres de recherche
     * @param array $pLeftJoin Jointure gauche. 'tableName' => array (0 => 'champGauche', 1 => 'opérateur', 2 => 'champDroit')
     * @return CopixDAORecordIterator
     */
    public function findBy (CopixDAOSearchParams $pSp, $pLeftJoin = array ());

    /**
     * Renvoie le nombre d'enregistrements, selon une recherche avec les paramètres $pSp
     *
     * @param CopixDAOSearchParams $pSp Paramètres de recherche
     * @return int
     */
    public function countBy (CopixDAOSearchParams $pSp);

    /**
     * Supprime un ou des enregistrements, selon une recherche avec les paramètres $pSp
     *
     * @param CopixDAOSearchParams $pSp
     * @return mixed Retour de l'instruction SQL DELETE, dépendant du moteur de base de données utilisé
     */
    public function deleteby (CopixDAOSearchParams $pSp);

    /**
     * Met à jour un enregistrement
     *
     * @param ICopixDAORecord $pRecord Enregistrement à modifier
     * @return int Nombre d'enregistrements affectés
     * @throws CopixDAOCheckException
     */
    public function update ($pRecord);

    /**
     * Insertion d'un enregistrement
     *
     * @param ICopixDAORecord $pRecord Enregistrement à insérer
     * @throws CopixDAOCheckException
     */
    public function insert ($pRecord);

    /**
     * Retourne tous les enregistrements d'une table
     *
     * @return CopixDAORecordIterator
     */
    public function findAll ();
}