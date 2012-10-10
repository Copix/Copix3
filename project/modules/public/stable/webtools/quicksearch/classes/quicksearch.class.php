<?php
/**
 * @package		webtools
 * @subpackage	quicksearch
* @author	Croës Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * @package		webtools
 * @subpackage	quicksearch
* Classe de description de la recherche.
*/
class QuickSearchParam {
	/**
	* Le nom du champ à rechercher
	* @param string
	*/
	var $FieldName;

	/**
	* Le poid du champ pour pondérer les résultats de la recherche
	* @var int
	*/
	var $FieldWeigth;

	/**
    * Constructeur
    */
	function QuickSearchParam ($pFieldName, $pField = 0) {
		$this->FieldName   = $pFieldName;
		$this->FieldWeigth = $pField;
	}
}

/**
 * @package		webtools
 * @subpackage	quicksearch
* Objet de recherche dans une base de données.
*/
class QuickSearch {
	/**
	* La table dans laquelle on va rechercher
	*/
	var $TableName;
	
	//@desc: Le nom du champ qui est utilisé comme identifiant.
	var $FieldIdName;

	//@desc: Tableau de HoundBoneToGet qui décrit les paramètres de la recherche a effectuer.
	var $arParams;
	
	//@desc: Liste des autes champs a retourner.
	var $TableListeFields;
	
	//@desc: Les conditions de recherche supplémentaire. (chaine de caractère)
	var $WhereMore;
	
	//@desc: Une requete à utiliser au lieu de la requête automatique. mettre --LOOKFOR-- à l'endroit ou le mot de recherche doit être inséré. et --FIELD-- a l'endroit ou l'on parle du champ.
	var $RequestToUse;
	
	//@desc: Si l'on est en mode recherche avancée.
	var $AdvancedSearch;

	//@desc: Un tableau avec $Tableau[IdValue] = IdPoidsResultat. Résultat de la recherche. Ce tableau est trié après la recherche.
	var $TableResult;

	//@desc: Un tableau d'objets contenant les enregistrements. De la forme Tableau[Id] = ObjetLigne.
	var $TableLineResult;

	/**
    * Efface tout le contenu de l'objet. (résultats et paramètres de recherche.)
    */
	function ClearAll () {
		$this->AdvancedSearch   = false;
		$this->TableName        = "none";
		$this->FieldIdName      = "none";
		$this->arParams   = array ();
		$this->TableListeFields = array ();
		$this->WhereMore = "";

		$this->TableResult      = array ();
		$this->TableLineResult  = array ();
		$this->RequestToUse     = "";
	}

	/**
    * Ajoute un paramètre à la recherche.
    * @param $ObjBoneToGet - Un objet de type HounbdBoneToGet a ajouter. Accepte aussi les tableaux de la forme["FieldName"]=Poids
    */
	function AssignLookParams ($pSearchParams) {
		if (is_object ($pSearchParams)) {
			//Directement un objet.
			$this->arParams[] = $pSearchParams;
		}else{
			//compatible aussi avec un tableau de type Tableau["FieldName"] = Poids
			if (is_array ($pSearchParams)) {
				foreach ($pSearchParams as $key=>$elem) {
					$this->arParams[] = new QuickSearchParam ($key, $elem);//Ajout dans la liste.
				}
			}
		}
	}

	/**
    * Donne le poids du contenu du champs. (nombre de fois la chaine dans le contenu * poids du champs)
    * @return (Nombre de $ToSearch dans $InThat) * $Poids
    * @param $InThat - l'endroit ou rechercher $ToSearch.
    * @param $ToSearch - Ce qu'il faut rechercher.   
    * @param $Poids - le poids de chaque occurence de $ToSearch dans $InThat.
    */
	function AdvancedSearchValue ($InThat, $ToSearch, $Poids) {
		if (strlen (trim ($ToSearch))){
			$Times = substr_count (strtoupper ($InThat), strtoupper ($ToSearch));
			$ToReturn = $Times * $Poids;
			return ($Times * $Poids);
		}
		return 0;
	}

	/**
    * Traite l'ajout de l'objet dans le cadre ou on l'est trouvée dans la requete.
    */
	function ComptabiliseObjet ($Id, $ObjLine, $Poids) {
		//On regarde si déja référencé.
		if (isset ($this->TableResult[$Id])){
			//on rajoute le nouveau poids trouvé à l'ancien
			$this->TableResult[$Id] += $Poids;
		}else{
			//On crée un Nouvel Element.
		$this->TableResult[$Id] = $Poids;
		if (strlen ($this->RequestToUse) <= 0) {
			//Si requête auto, création de l'objet uniquement en fonction des champs que l'on veut.
			//création de l'objet avec les infos désirées.
			$obj = new StdClass ();
			$obj->{$this->FieldIdName} = $Id;
			foreach ($this->TableListeFields as $FieldName) {
				//Stockage des champs supplémentaires.
				$obj->$FieldName = $ObjLine->$FieldName;
			}
			//stockage des infos désirées de l'objet.
			$this->TableLineResult[$Id] = $obj;
		}else{
			//On ne maîtrise pas les champs a récupérer, on utilise le résultat de la requête brute.
			//création de l'objet avec les infos désirées.
			$obj->{$this->FieldIdName} = $Id;

			//Ajout de l'objet lui même.
			$this->TableLineResult[$Id] = $ObjLine;
		}
		}
	}

	/**
    *  Ajoute la liste des champs desquels récupérer l'information.
    *  On ne vérifie pas l'existence d'un champ de même nom.
    * @param $ListeFields - Tableau qui contient la liste des champs a rajouter
    */
	function AddFields ($ListeFields) {
		foreach ($ListeFields as $Elem){
			$this->TableListeFields[] = $Elem;
		}
	}

	/**
    * donne la requete à utiliser.
    * @param  $LookForThat - le mot a rechercher.
    */
	function GetRequest ($LookForThat, $InField) {
		if (strlen($this->RequestToUse) <= 0){
			//On utilise la construction de requete par défaut.

			//construction de la requête.
			$sqlQuery = "Select ".$this->FieldIdName;
			foreach ($this->TableListeFields as $elem) {//ajout des champs supplémentaires à récupérer.
			$sqlQuery .= ', '.$elem;
			}

			//Si recherche avancée, vérification de la présence de tout les paramètres nécessaires.
			if ($this->AdvancedSearch) {
				//Si le champ de recherche actuel ne fait pas parti des champs a récupérer.
				//on le rajoute a la liste des à récupérer (pour la recherche avancée, parsing avec substr)
				if (!isset ($this->TableListeField[$InField])) {
					//               echo "Rajoute le champs $InField<br>";
					$sqlQuery.= ", ".$InField;
				}
			}
			//Clause from et where.
			$sqlQuery.=" from ".$this->TableName." where ";
			$sqlQuery.= $InField.' like \'%'.AddSlashes ($LookForThat).'%\'';
			$sqlQuery.= $this->WhereMore;
		}else{
			//On a demandé à utiliser une requête particulière.
			$sqlQuery = preg_replace ("/--LOOKFOR--/", AddSlashes ($LookForThat), $this->RequestToUse);
			$sqlQuery = preg_replace ("/--FIELD--/", $InField, $sqlQuery);
		}

		return $sqlQuery;

	}

	/**
    * Demande l'exécution de la recherche.
    * @param  $BoneShape - ce que l'on a réellement rechercher. (le paramètre du Like %$BoneShape%)
    * @return Le nombre de résultat trouvé.
    */
	function ExecuteRequest ($BoneShape) {
		$this->TableResult = array ();

		//Séparation des mots clefs dans un tableau.
		$ListeLooking = explode (" ", $BoneShape);

		foreach ($ListeLooking as $BoneShapeElem) {
			//Parcours des différents mot clef.
			foreach ($this->arParams as $Element) {
				//Récupère la requete à effectuer.
				$sqlQuery = $this->GetRequest ($BoneShapeElem, $Element->FieldName);

				//récupération des lignes correspondants a la recherche.
				$result = CopixDB::getConnection ()->doQuery ($sqlQuery);

				if ($result) {
					//Parcours de l'ensemble des résultats.
					foreach ($result as $line) {
						//Valeur de l'id du champ a ajouter / modifier.
						$IdActuel   = $line->{$this->FieldIdName};
						$PoidsTotal = $Element->FieldWeigth;
						if ($this->AdvancedSearch) {
							//Recherche avancée ? Ajout du nombre d'occurence.
							$PoidsTotal += $this->AdvancedSearchValue ($line->{$Element->FieldName}, $BoneShapeElem, $Element->FieldWeigth);
						}

						//Demande d'ajout de l'objet à la liste des résultats.
						$this->ComptabiliseObjet ($IdActuel, $line, $PoidsTotal);
					}
				}
			}
		}

		//Tri du résultat par ordre de correspondance, décroissant.
		arsort ($this->TableResult);

		//retourne le nombre d'éléments trouvés.
		return count ($this->TableResult);
	}
}
?>