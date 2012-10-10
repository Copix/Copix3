<?php
/**
 * Gestion des exception pour la classe PHPDoc
 * 
 * @package		copix
 * @subpackage	coding
 */
class CopixPHPDocException extends CopixException {}

/**
 * Parse des commentaires de type PHPDoc
 *
 * @package		copix
 * @subpackage	coding
 */
class CopixPHPDoc {
	/**
	 * Paramètres d'un code PHPDoc qui ne doivent apparaitre qu'une seule fois
	 *
	 * @var array
	 */
	private static $_onlyOne = array ('@return', '@package', '@subpackage');
	
	/**
	 * Parse les commentaires PHPDoc, et renvoie un tableau plus facile à utiliser
	 *
	 * @param string $pDoc PHPDoc à parser
	 * @param boolean $pCommentIsFirst Indique si on doit avoir un commentaire avant tout paramètre
	 * @return array Tableau des commentaires, ou false en cas de commentaire invalide
	 * @throws CopixPHPDocException Si le format du commentaire ne commence pas par /** et ne se termine pas par * /
	 */
	public static function parse ($pDoc, $pCommentIsFirst = true) {
		$toReturn = array ();
		$toReturn['parse_errors'] = array ();
		$docs = explode ("\n", $pDoc);
		array_walk ($docs, array ('CopixPHPDoc', '_array_trim'));
		// suppression de la 1ère ligne (/**) et de la dernière ligne (* /)
		if ($docs[0] !== '/**' || $docs[count ($docs) - 1] !== '*/') {
			throw new CopixPHPDocException (_i18n ('copix:copixphpdoc.errors.invalidComment', $pDoc));
		}
		unset ($docs[0]);
		unset ($docs[count ($docs)]);
		// ksort pour re indexer le tableau, et supprimer les "trous" dus aux unset
		ksort ($docs);
		// préparation des commentaires, pour faciliter leur lecture
		array_walk ($docs, array ('CopixPHPDoc', '_array_prepare_comment'));
		$vide = 0;
		$haveParams = false;
		$lastDoc = null;
		
		foreach ($docs as $doc) {
			
			// les commentaires qui commencent par @ sont dans des clefs spécifiques
			if (substr ($doc, 0, 1) == '@') {
				
				if ($pCommentIsFirst) {
					if (is_null ($lastDoc)) {
						$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.commentIsNotFirst', $doc);
					
					// si on n'a pas mit de ligne vide avant le 1er paramètre
					} else if (!$haveParams && strlen ($lastDoc) > 0) {
						$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.noEmtpyLineBeforeParameters', $doc);
					}
				}
				
				$haveParams = true;				
				
				// @param type $pVar Commentaire
				if (substr ($doc, 1, 5) == 'param') {
					$docExplode = explode (' ', $doc, 4);
					$paramIndex = (isset ($toReturn['param'])) ? count ($toReturn['param']) : 0;
					if (count ($docExplode) <> 4) {
						$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.invalidParam', $doc);
					} else {
						$toReturn['param'][$paramIndex]['type'] = $docExplode[1];
						$toReturn['param'][$paramIndex]['name'] = $docExplode[2];
						$toReturn['param'][$paramIndex]['comment'] = $docExplode[3];
					}
				
				// @return type Commentaire
				} else if (substr ($doc, 1, 6) == 'return') {
					$docExplode = explode (' ', $doc, 3);
					if (count ($docExplode) <= 1) {
						$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.invalidReturn', $doc);
					} else if (isset ($toReturn['return'])) {
						$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.commentAlreadyExists', '@return');
					} else {
						$toReturn['return']['type'] = $docExplode[1];
						$toReturn['return']['comment'] = (isset ($docExplode[2])) ? $docExplode[2] : null;
					}
					
				// @throws type Commentaire
				} else if (substr ($doc, 1, 6) == 'throws') {
					$docExplode = explode (' ', $doc, 3);
					$throwsIndex = (isset ($toReturn['throws'])) ? count ($toReturn['throws']) - 1 : 0;
					if (count ($docExplode) <> 3) {
						$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.invalidThrows', $doc);
					} else {
						$toReturn['throws'][$throwsIndex]['type'] = $docExplode[1];
						$toReturn['throws'][$throwsIndex]['comment'] = $docExplode[2];
					}
				
				// tous les autres commentaires qui commencent par @ sont en deux parties
				} else {
					$docExplode = explode (' ', $doc, 2);
					if (count ($docExplode) <= 1) {
						$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.invalidMembersCount', array ($doc, 2, count ($docExplode)));
					} else {
						$paramKey = substr ($docExplode[0], 1);
						// certains commentaires ne sont possibles qu'une seule fois
						if (in_array ($docExplode[0], self::$_onlyOne) && isset ($toReturn[$paramKey])) {
							$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.commentAlreadyExists', $paramKey);
						} else {
							if (in_array ($docExplode[0], self::$_onlyOne)) {
								$toReturn[$paramKey] = $docExplode[1];
							} else {
								$toReturn[$paramKey][] = $docExplode[1];
							}
						}
					}
				}
				
			// les autres commentaires sont dans la clef comment
			} else if (strlen ($doc) > 0) {
				$toReturn['comment'][] = $doc;
			} else {
				$vide++;
			}
			
			$lastDoc = $doc;
		}
		
		// si on a plus d'un commentaire "vide" (saut de ligne)
		if (($haveParams && $vide >= 2) || (!$haveParams && $vide >= 1)) {
			$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.tooMuchEmptyLines');
		}
		// si la dernière ligne avant la fin du bloc était une ligne vide
		if (strlen ($lastDoc) == 0) {
			$toReturn['parse_errors'][] = _i18n ('copix:copixphpdoc.errors.lastLineIsEmpty');
		}
		
		return $toReturn;
	}
	
	/**
	 * Vérifie que les paramètres contenus dans $pCheck existent dans $pDocs
	 *
	 * @param array $pDocs Retour de la méthode parse
	 * @param array $pParams Commentaires dont on veut vérifier l'existance. Clef : nom de paramètre, valeur = occurences
	 * @param boolean $pOnlyThis Indique si on ne veut que les paramètres $pOnlyThis dans $pDocs, ou "entre autres" les paramètres $pParams
	 * @return array Erreurs de vérification
	 */
	public static function check ($pDocs, $pCheck, $pOnlyThis = false) {
		$toReturn = array ();
		foreach ($pCheck as $name => $nbr) {
			if (!is_null ($nbr)) {
				// si on n'a pas définit ce commentaire
				if (!isset ($pDocs[$name])) {
					$toReturn[] = _i18n ('copix:copixphpdoc.errors.nbrCommentsInvalid', array ($name, $nbr, 0));
				// si on attendait un ou plusieurs commentaires de ce type, mais qu'on n'a pas le nombre attendu
				// on test lexistance de l'index 0 parceque on peut avoir un tableau associatif (exemple : return, qui a type et comment en clefs) 
				} else if (is_array ($pDocs[$name]) && isset ($pDocs[$name][0]) && count ($pDocs[$name]) != $nbr) {
					$toReturn[] = _i18n ('copix:copixphpdoc.errors.nbrCommentsInvalid', array ($name, $nbr, count ($pDocs[$name])));
				// si on attendait un seul commentaire de ce type, mais qu'on n'a pas le nombre attendu
				} else if (!is_array ($pDocs[$name]) && $nbr != 1) {
					$toReturn[] = _i18n ('copix:copixphpdoc.errors.nbrCommentsInvalid', array ($name, $nbr, 1));
				}
			}
		}
		
		if ($pOnlyThis) {
			foreach (array_diff (array_keys ($pDocs), array_keys ($pCheck)) as $name) {
				// parse_errors et comment sont ajoutés par la méthode parse, et sont donc "autorisés"
				if (!in_array ($name, array ('parse_errors', 'comment'))) {
					$toReturn[] = _i18n ('copix:copixphpdoc.errors.commentNotAllowed', $name);
				}
			}
		}
		return $toReturn;
	}
	
	/**
	 * Effectue un trim sur chaque élément d'un tableau, à appeler avec array_walk
	 *
	 * @param mixed $pItem
	 * @param mixed $pKey
	 */
	private static function _array_trim (&$pItem, $pKey) {
		$pItem = trim ($pItem);
	}
	
	/**
	 * Supprime le '* ' avant chaque ligne de commentaire, à appeler avec array_walk, sans /** et * /
	 *
	 * @param mixed $pItem
	 * @param mixed $pKey
	 * @return boolean
	 * @throws CopixPHPDocException Si le commentaire ne commence pas par '*' ou '* '
	 */
	private static function _array_prepare_comment (&$pItem, $pKey) {
		if ((strlen ($pItem) == 1 && $pItem != '*') || (strlen ($pItem) > 1 && substr ($pItem, 0, 2) != '* ')) {
			throw new CopixPHPDocException (_i18n ('copix:copixphpdoc.errors.invalidComment', $pItem));
		} else {
			$pItem = trim (substr ($pItem, 1));
			if (substr ($pItem, 0, 1) == '@') {
				$pItem = str_replace ("\t", ' ', $pItem);
				$pItem = str_replace ('  ', ' ', $pItem);
			}
		}
	}
}