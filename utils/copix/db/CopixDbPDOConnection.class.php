<?php
/**
 * @package   copix
 * @subpackage db
 * @author   Croes Gérald
 * @copyright CopixTeam
 * @link      http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour les objets de connexion PDO
 * @package copix
 * @subpackage db
 */
abstract class CopixDBPDOConnection extends CopixDBConnection {
	/**
	 * L'objet PDO associé à la connexion
	 * @var PDO
	 */
	protected $_pdo = null;

	/**
	 * Constructeur
	 * @param	CopixDBProfil	$pProfil	le profil de connexion à utiliser
	 */
	public function __construct ($pProfil){
		parent::__construct ($pProfil);
		try {
			$this->_pdo = new PDO (substr ($pProfil->getConnectionString (), 4), $pProfil->getUser (), $pProfil->getPassword (), $pProfil->getOptions ());
		}catch (PDOException $e){
			throw new CopixDBException ($e->getMessage ());
		}
	}

	/**
	 * Lancement d'une requête SQL
	 * @param	string	$pQueryString	la requête à lancer
	 * @param	array	$pParameters	tableau de paramètres
	 * @param	int		$pOffset		l'offset à partir duquel nous allons lire les résultats => Si null, pas d'offset 
	 * @param	int		$pCount			le nombre d'élément que l'on souhaites récupérer depuis la base. Si null => le maximum
	 */
	public function doQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null){
		$resultsOfQueryParsing = $this->_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);
		CopixLog::log ($resultsOfQueryParsing['query'].var_export ($pParameters, true), "query", CopixLog::INFORMATION);

		if ($resultsOfQueryParsing['isSelect'] && ($resultsOfQueryParsing['offset'] === false || $resultsOfQueryParsing['count'] === false)){
			//Si nous sommes dans un select et que l'offset et le count ne sont pas gérés autoamtiquement, alors il nous faut un curseur "movable"
			//TODO: lorsque les curseurs movable seront supportés, mettre ça
			//         $stmt = $this->_pdo->prepare ($resultsOfQueryParsing['query'], array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
			$stmt = $this->_pdo->prepare($resultsOfQueryParsing['query']);
		}else{
			$stmt = $this->_pdo->prepare($resultsOfQueryParsing['query']);
		}

		if (! $stmt){
			throw new CopixDBException ('Impossible de préparer la requête ['.$resultsOfQueryParsing['query'].']'.serialize ($pParameters).implode ('-', $this->_pdo->errorInfo ()));
		}

		if (! $stmt->execute ($pParameters)){
			throw new CopixDBException ('Impossible d\'exécuter la requête ['.$resultsOfQueryParsing['query'].']'.serialize ($pParameters).implode ('-', $stmt->errorInfo ()));
		}
		if (! $resultsOfQueryParsing['isSelect']){
			return $stmt->rowCount ();
		}

		@$stmt->setFetchMode(PDO::FETCH_CLASS, 'StdClass');
		if ($resultsOfQueryParsing['offset'] && $resultsOfQueryParsing['count']){
			return $stmt->fetchAll ();
		}else{
			$results = array ();

			//hack pour déplacer à l'offset donné.
			$row = true;
			$pLeft = $pCount;
				
			while (($pLeft > 0 || $pLeft === null) && ($row !== false)){
				if ($row = $stmt->fetch ()){
					if ($pOffset == 0){
						$results[] = $row;
						if ($pLeft !== null){
							$pLeft--;
						}
					}else{
						$pOffset--;
					}
				}
			}
			/*

			for ($toFetch = $pCount, $row = $stmt->fetch(PDO::FETCH_CLASS, PDO::FETCH_ORI_REL, $pOffset === null ? 0 : $pOffset);
			$row !== false && ($toFetch-- > 0 || $toFetch === null);
			$row = $stmt->fetch()){
			$results[] = $row;
			}
			*
			*/
			$stmt->closeCursor ();
			return $results;
		}
	}
	
	/**
	 * Lancement d'une requête SQL
	 * @param	string	$pQueryString	la requête à lancer
	 * @param	array	$pParameters	tableau de paramètres
	 * @param	int		$pOffset		l'offset à partir duquel nous allons lire les résultats => Si null, pas d'offset 
	 * @param	int		$pCount			le nombre d'élément que l'on souhaites récupérer depuis la base. Si null => le maximum
	 */
	public function iDoQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null){
			$resultsOfQueryParsing = $this->_parseQuery ($pQueryString, $pParameters, $pOffset, $pCount);
		CopixLog::log ($resultsOfQueryParsing['query'].var_export ($pParameters, true), "query", CopixLog::INFORMATION);

		if ($resultsOfQueryParsing['isSelect'] && ($resultsOfQueryParsing['offset'] === false || $resultsOfQueryParsing['count'] === false)){
			//Si nous sommes dans un select et que l'offset et le count ne sont pas gérés autoamtiquement, alors il nous faut un curseur "movable"
			//TODO: lorsque les curseurs movable seront supportés, mettre ça
			//         $stmt = $this->_pdo->prepare ($resultsOfQueryParsing['query'], array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
			$stmt = $this->_pdo->prepare($resultsOfQueryParsing['query']);
		}else{
			$stmt = $this->_pdo->prepare($resultsOfQueryParsing['query']);
		}

		if (! $stmt){
			throw new CopixDBException ('Impossible de préparer la requête ['.$resultsOfQueryParsing['query'].']'.serialize ($pParameters).implode ('-', $this->_pdo->errorInfo ()));
		}

		if (! $stmt->execute ($pParameters)){
			throw new CopixDBException ('Impossible d\'exécuter la requête ['.$resultsOfQueryParsing['query'].']'.serialize ($pParameters).implode ('-', $stmt->errorInfo ()));
		}
		if (! $resultsOfQueryParsing['isSelect']){
			return $stmt->rowCount ();
		}

		@$stmt->setFetchMode(PDO::FETCH_CLASS, 'StdClass');
		if ($resultsOfQueryParsing['offset'] && $resultsOfQueryParsing['count']){
			return new CopixDBPDOResultSetIterator ($stmt);
		}else{
			$results = array ();

			//hack pour déplacer à l'offset donné.
			$row = true;
			$pLeft = $pCount;
				
			while (($pLeft > 0 || $pLeft === null) && ($row !== false)){
				if ($row = $stmt->fetch ()){
					if ($pOffset == 0){
						$results[] = $row;
						if ($pLeft !== null){
							$pLeft--;
						}
					}else{
						$pOffset--;
					}
				}
			}
			/*

			for ($toFetch = $pCount, $row = $stmt->fetch(PDO::FETCH_CLASS, PDO::FETCH_ORI_REL, $pOffset === null ? 0 : $pOffset);
			$row !== false && ($toFetch-- > 0 || $toFetch === null);
			$row = $stmt->fetch()){
			$results[] = $row;
			}
			*
			*/
			$stmt->closeCursor ();
			return $results;
		}
	}
	
	/**
	 * Dernier identifiant automatique affecté
	 * @param	string	$pFromSequence	le nom de la séquence à utiliser pour récupérer l'identifiant
	 * @return	int
	 */
	public function lastId ($pFromSequence = null){
		if ($pFromSequence !== null){
			return $this->_pdo->lastInsertId ($pFromSequence);
		}
		return $this->_pdo->lastInsertId ();
	}

	/**
	 * quote un élément 
	 */
	public function quote ($pString, $pCheckNull = true){
		if ($pCheckNull){
			return (is_null ($pString) ? 'NULL' : $this->_pdo->quote ($pString));
		}else{
			return $this->_pdo->quote ($pString);
		}
	}

	/**
	 * Valide une transaction en cours sur la connection
	 */
	public function commit (){
		$this->_pdo->commit ();
	}

	/**
	 * Annule une transcation sur la connection
	 */
	public function rollback (){
		$this->_pdo->rollback ();
	}

	/**
	 * Demarre une transaction sur la connection donnée
	 */
	public function begin (){
		$this->_pdo->beginTransaction ();
	}
}
?>