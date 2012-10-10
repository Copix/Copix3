<?php
/**
 * @package		devtools
 * @subpackage	devstandards
 * @author		Steevan BARBOYON
 * @link		http://www.copix.org
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des exceptions pour la classe Tokenizer
 *
 * @package		devtools
 * @subpackage	devstandards
 */
class CopixTokenizerException extends CopixException {}

/**
 * Parse de fichiers pour retrouver les non respects des normes de développement
 *
 * @package		devtools
 * @subpackage	devstandards
 */
class Tokenizer {
	
	/**
	 * Alias dont on veut vérifier l'utilisation
	 *
	 * @var array
	 */
	private static $_alias = array (
		'_i18n' => 'CopixI18N::get',
		'_url' => 'CopixUrl::get',
		'_resource' => 'CopixUrl::getResource',
		'_resourcePath' => 'CopixUrl::getResourcePath',
		'_class' => 'CopixClassesFactory::create',
		'_ioClass' => 'CopixClassesFactory::getInstanceOf',
		'_classInclude' => 'CopixClassesFactory::fileInclude',
		'_dao' => 'CopixDAOFactory::create',
		'_daoInclude' => 'CopixDAOFactory::fileInclude',
		'_ioDAO ' => 'CopixDAOFactory::getInstanceOf',
		'_daoSP' => 'CopixDAOFactory::createSearchParams',
		'_record ' => 'CopixDAOFactory::createRecord',
		'_tag' => 'CopixTpl::tag',
		'_service' => 'CopixServices::process',
		'_notify' => 'CopixEventNotifier::notify',
		'_arPPO' => 'new CopixActionReturn (CopixActionReturn::PPO',
		'_arRedirect' => 'new CopixActionReturn (CopixActionReturn::REDIRECT',
		'_arFile' => 'new CopixActionReturn (CopixActionReturn::FILE',
		'_arContent' => 'new CopixActionReturn (CopixActionReturn::CONTENT',
		'_arNone' => 'new CopixActionReturn (CopixActionReturn::NONE',
		'_arDisplay' => 'new CopixActionReturn (CopixActionReturn::DISPLAY',
		'_doQuery' => '->doQuery',
		'_iDoQuery' => '->iDoQuery',
		'_log' => 'CopixLog::log',
		'_request' => 'CopixRequest::get',
		'_currentUser' => 'CopixAuth::getCurrentUser',
		'_dump' => 'CopixDebug::var_dump',
		'_sessionGet' => 'CopixSession::get',
		'_sessionSet' => 'CopixSession::set',
		'_ppo' => 'new CopixPPO',
		'_rppo' => 'new CopixRPPO',
		'_validator' => 'CopixValidatorFactory::create',
		'_cValidator' => 'CopixValidatorFactory::createComposite',
		'_ctValidator' => 'CopixValidatorFactory::createComplexType',
		'_form' => 'CopixFormFactory::get',
		'_field' => 'CopixFieldFactory::get'
	);
	
	/**
	 * Type d'erreur "ERROR" renvoyé par parse
	 */
	const ERRTYPE_ERROR = 0;
	
	/**
	 * Type d'erreur "SYNTAX" renvoyé par parse
	 */
	const ERRTYPE_SYNTAX = 1;
	
	/**
	 * Type d'erreur "NOTICE" renvoyé par parse
	 */
	const ERRTYPE_NOTICE = 2;
	
	/**
	 * Type d'erreur "PHPDOC" renvoyé par parse
	 */
	const ERRTYPE_PHPDOC = 3;
	
	/**
	 * Index de la ligne actuellement lue par la méthode parseFile
	 *
	 * @var int
	 */
	private static $_line = 0;
	
	/**
	 * Tableau des erreurs générées par la méthode parseFile
	 *
	 * @var array
	 */
	private static $_errors = array ();
	
	/**
	 * Nom du fichier passé en paramètre à parseFile
	 *
	 * @var string
	 */
	private static $_file = null;
	
	/**
	 * Contenu du fichier à analyser dans parseFile, passé dans un explode sur \n
	 *
	 * @var array
	 */
	private static $_arFileContent = null;
	
	/**
	 * Parse le fichier $pFileName et retourne les erreurs de normes
	 *
	 * @param string $pFileName Fichier à parser
	 * @return array
	 * @throws CopixTokenizerException Fichier non trouvé
	 */
	public static function parseFile ($pFileName) {
		
		/**
		 * Tests effectués :
		 *   - <?php est le premier caractère du fichier
		 *   - on a un seul <?php par fichier
		 *   - on n'a pas indiqué de commentaire pour le fichier, ou le commentaire est invalide (cherche commentaire, package, subpackage, author, link, copyright et license)
		 *   - on n'a pas indiqué de commentaire pour une déclaration de classe, ou le commentaire est invalide (cherche commentaire, package et subpackage)
		 *   - on n'a pas indiqué de commentaire pour une déclaration de méthode, ou le commentaire est invalide (cherche commentaire, return et throws)
		 *   - on n'a pas indiqué de commentaire pour une déclaration de fonction, ou le commentaire est invalide (cherche commentaire, return et throws)
		 *   - visibilité d'une méthode qui doit forcément être indiquée, même en public
		 *   - recherche des alias non utilisés
		 *   - paramètres de méthodes / fonctions avec le bon nom, et commentés dans le PHPDoc
		 * 
		 * A faire :
		 *   - choisir ce qu'on veut rechercher (NOTICE, PHPDOC, SYNTAX)
		 *   - prendre en compte les interfaces, dont les méthodes n'ont pas de body (pas de { })
		 *   - aucune erreur renvoyée sur un bloc qui commence par des paramètres, alors qu'on veut un commentaire en premier (ex : _parseFile)
		 *   - remettre à vide $keyWords au début d'une méthode (si on a des paramètres avant une méthode, les mots-clefs des paramètres sont pris en compte)
		 *   - ordre des mots-clefs pour une méthode ([abstract final ][public protected private ][static ]function NOMMETHODE) à revoir
		 */
		
		// si le fichier n'existe pas, on lève une exception
		if (!file_exists ($pFileName)) {
			throw new CopixTokenizerException (_i18n ('tokenizer.errors.fileNotFound', $pFileName));
		}
		
		self::$_file = $pFileName;
		
		// recherche du type de fichier : actiongroup, classe, template PHP
		$typeActionGroup = true;
		$typeClasse = false;
		$typeTemplatePHP = false;
		
		$fileContent = file_get_contents ($pFileName);
		self::$_arFileContent = explode ("\n", $fileContent);
		$tokens = token_get_all ($fileContent);
		$tokensCount = count ($tokens);
		$isFirstChar = true;
		$nbrOpenTag = 0;
		$classCommentExists = true;
		$tokenIndex = 0;
		$lastPHPDoc = null;
		$keyWords = array ();
		$isInClass = false;
		$isInFunction = false;
		$functionInfos = array ();
		$lastFunctionName = null;
		$indexBloc = 0;
		$waitForParams = false;
		// si on doit sauvegarder le prochain token T_DOC_COMMENT (non dans le cas d'un T_OPEN_TAG qui vérifie le prochain T_DOC_COMMENT)
		$saveNextPHPDoc = true;
		self::$_line = 1;
		//_dump ($tokens);
		foreach ($tokens as $tokenIndex => $token) {
			//var_dump ($token);
			
			// chaine simple, pas de token particulier
			if (is_string ($token)) {
				self::$_line += substr_count ($token, "\n");
				
				// si c'est la fermeture des paramètres d'une fonction
				if ($waitForParams && $token == ')') {
					$waitForParams = false;
					$lastPHPDoc = null;
				}
				
				// si c'est la fin de la déclaration d'une méthode, on test les mots-clefs
				if ($isInClass > 0 && $isInFunction === null && $token == ')') {
					// vérification si on a bien la portée (public n'est pas obligatoire en PHP, mais dans les normes de Copix, si)
					$visibilityOk = false;
					foreach ($keyWords as $keyWord) {
						if (in_array ($keyWord, array ('public', 'protected', 'private'))) {
							$visibilityOk = true;
							break;
						}
					}
					if (!$visibilityOk) {
						self::_addError (_i18n ('tokenizer.errors.unknowMethodVisibility', $lastFunctionName), null, self::ERRTYPE_SYNTAX, null, _i18n ('tokenizer.help.methodKeyWordOrder'));
					}
					
					// vérification de l'ordre des mots clefs
					if (
						count ($keyWords) == 2 && (
							(array_search ('static', $keyWords) !== false && $keyWords[1] <> 'static') ||
							(
								!in_array ($keyWords[0], array ('abstract', 'final')) ||
								!in_array ($keyWords[1], array ('public', 'protected', 'private'))
							)
						) || (
							count ($keyWords) == 3 && (
								!in_array ($keyWords[0], array ('abstract', 'final')) ||
								!in_array ($keyWords[1], array ('public', 'protected', 'private')) ||
								$keyWords[2] <> 'static')
						)
					) {
						$string = implode (' ', $keyWords);
						self::_addError (
							_i18n ('tokenizer.errors.invalidMethodKeyWordOrder', array ($string, $lastFunctionName)),
							null, self::ERRTYPE_SYNTAX, null, _i18n ('tokenizer.help.methodKeyWordOrder')
						);
					}
					$keyWords = array ();
				}
				
				// {
				if ($token == '{') {
					$indexBloc++;
					// qui ouvre une classe
					if ($isInClass === null) {
						$isInClass = $indexBloc;
						$keyWords = array ();
					} else if ($isInFunction === null) {
						$isInFunction = $indexBloc;
					}
				
				// }
				} else if ($token == '}') {
					if ($isInClass == $indexBloc) {
						$isInClass = false;
						$functionInfos = array ();
					} else if ($isInFunction == $indexBloc) {
						$isInFunction = false;
						self::_parseFunctionDoc ($functionInfos);
						$functionInfos = array ();
					}
					$indexBloc--;
				}
				
			} else {
				list ($tokenId, $tokenText) = $token;
				self::$_line += substr_count ($tokenText, "\n");
				
				switch ($tokenId) {
					
					// ouverture de balise PHP : <?php
					case T_OPEN_TAG :
						$nbrOpenTag++;
						// si ce n'est pas le premier caractère du fichier
						if (($typeActionGroup || $typeClasse) && !$isFirstChar) {
							self::_addError (_i18n ('tokenizer.errors.T_OPEN_TAG.isNotFirst'), null, self::ERRTYPE_ERROR);
						}
						// si on a ouvert plusieurs balises PHP
						if (($typeClasse || $typeActionGroup) && $nbrOpenTag > 1) {
							self::_addError (_i18n ('tokenizer.errors.T_OPEN_TAG.onlyOneAllowed'), null, self::ERRTYPE_ERROR);
						}
						// si on n'a pas indiqué de commentaires sur le fichier
						if (isset ($tokens[1]) && $tokens[1][0] !== T_DOC_COMMENT) {
							self::_addError (_i18n ('tokenizer.errors.T_DOC_COMMENT.fileCommentIsNotFirst'), null, self::ERRTYPE_PHPDOC, null, _i18n ('tokenizer.help.docFile'));
						// si on a indiqué un commentaire sur le fichier, on vérifie sa validité
						} else {
							$lastPHPDoc = null;
							$saveNextPHPDoc = false;
							self::_parseFileDoc ($tokens[1][1]);
						}
						break;
						
					// fermeture de balise PHP : ? >
					case T_CLOSE_TAG :
						// @todo vérifier que dans le cas d'un actiongroup ou d'une classe, c'est le dernier caractère du fichier
						break;
					
					// block de commentaire PHPDoc : /** */
					case T_DOC_COMMENT :
						if ($saveNextPHPDoc) {
							$lastPHPDoc = $token[1];
						}
						$saveNextPHPDoc = true;
						break;
					
					// déclaration de classe : class
					case T_CLASS :
						// vérification des commentaires PHPDoc
						if (is_null ($lastPHPDoc)) {
							self::_addError (_i18n ('tokenizer.errors.T_CLASS.commentNotFound', self::_getNextT_STRING ($tokens, $tokenIndex)), null, self::ERRTYPE_PHPDOC, self::$_line - 1, _i18n ('tokenizer.help.docClass'));
						} else {
							self::_parseClassDoc ($lastPHPDoc);
							$lastPHPDoc = null;
						}
						
						$isInClass = null;
						$lastPHPDoc = null;
						break;
					
					// déclaration d'une fonction, ou d'une méthode
					case T_FUNCTION :
						$functionInfos = array ();
						$isInFunction = null;
						
						$functionInfos['name'] = self::_getNextT_STRING ($tokens, $tokenIndex);					
						$i18n = ($isInClass > 0) ? 'method' : 'function';
						// vérification du PHPDoc
						if (is_null ($lastPHPDoc)) {
							self::_addError (_i18n ('tokenizer.errors.T_FUNCTION.' . $i18n . 'CommentNotFound', $functionInfos['name']), null, self::ERRTYPE_PHPDOC, self::$_line - 1, _i18n ('tokenizer.help.doc' . ucfirst ($i18n)));
						} else {
							$functionInfos['phpdoc'] = $lastPHPDoc;
							$functionInfos['phpdocLine'] = self::$_line - 1;
						}
						
						$waitForParams = true;
						$lastFunctionName = $functionInfos['name'];
						break;
						
					// chaine, généralement des noms de classe, ou de fonction
					case T_STRING :
						if ($isInFunction && !isset ($functionInfos['name'])) {
							$functionInfos['name'] = $tokenText;
						}
						break;
					
					// mots clefs pour les propriétés ou méthodes d'une classe
					case T_ABSTRACT :
						$keyWords[] = 'abstract';
						break;
					case T_FINAL :
						$keyWords[] = 'final';
						break;
					case T_PRIVATE :
						$keyWords[] = 'private';
						break;
					case T_PROTECTED :
						$keyWords[] = 'protected';
						break;
					case T_PUBLIC :
						$keyWords[] = 'public';
						break;
					case T_STATIC :
						$keyWords[] = 'static';
						break;
						
					// return d'une fonction ou d'une méthode
					case T_RETURN :
						if ($isInFunction) {
							$functionInfos['return'] = true;
						}
						break;
						
					// throw exception
					case T_THROW :
						if ($isInFunction) {
							$functionInfos['throws'] = (isset ($functionInfos['throws'])) ? $functionInfos['throws'] + 1 : 1;
						}
						break;
						
					// $variable ou paramètre d'une fonction / méthode
					case T_VARIABLE :
						if ($waitForParams) {							
							$functionInfos['param'][] = $tokenText;
						}
						break;
				}
			}

			$isFirstChar = false;			
			$tokenIndex++;
		}
		
		// si on veut vérifier les utilisations des alias
		// @todo : faire en sorte qu'on puisse choisir si on veut chercher les alias ou pas
		if (true) {
			// on supprimer tous les espaces du contenu pour bien trouver tous les alias non utilisés, sinon on peut rater Class :: test par exemple
			$contentForAlias = str_replace (' ', '', $fileContent);
			foreach (self::$_alias as $alias => $base) {
				if (($pos = strpos ($contentForAlias, str_replace (' ', '', $base))) !== false) {
					$line = count (explode ("\n", substr ($contentForAlias, 0, $pos)));
					self::_addError (_i18n ('tokenizer.errors.useAlias', array ($base, $alias)), null, self::ERRTYPE_NOTICE, $line);
				}
			}
			unset ($contentForAlias);
		}
		
		unset ($fileContent);
		return self::$_errors;
	}
	
	/**
	 * Retourne des informations sur toutes les constantes utilisées par Tokenizer
	 *
	 * @return array
	 */
	public static function getConstantes () {
		// les lignes en commentaires ne sont pas définies en PHP 5.2
		$toReturn = array ();
		$toReturn[T_ABSTRACT] = array (T_ABSTRACT, 'T_ABSTRACT', 'abstract', 'Abstraction de classes (disponible depuis PHP 5.0.0)');
		$toReturn[T_AND_EQUAL] = array (T_AND_EQUAL, 'T_AND_EQUAL', '&=', 'opérateurs d\'assignation');
		$toReturn[T_ARRAY] = array (T_ARRAY, 'T_ARRAY', 'array()', 'array(), syntaxe de tableau');
		$toReturn[T_ARRAY_CAST] = array (T_ARRAY_CAST, 'T_ARRAY_CAST', '(array)', 'transtypage');
		$toReturn[T_AS] = array (T_AS, 'T_AS', 'as', 'foreach');
		$toReturn[T_BAD_CHARACTER] = array (T_BAD_CHARACTER, 'T_BAD_CHARACTER', null, 'Tous les caractères en dessous de ASCII 32 excepté \t (0x09), \n (0x0a) et \r (0x0d)');
		$toReturn[T_BOOLEAN_AND] = array (T_BOOLEAN_AND, 'T_BOOLEAN_AND', '&&', 'opérateurs logiques');
		$toReturn[T_BOOLEAN_OR] = array (T_BOOLEAN_OR, 'T_BOOLEAN_OR', '||', 'opérateurs logiques');
		$toReturn[T_BOOL_CAST] = array (T_BOOL_CAST, 'T_BOOL_CAST', '(bool) ou (boolean)', 'transtypage');
		$toReturn[T_BREAK] = array (T_BREAK, 'T_BREAK', 'break;', 'break');
		$toReturn[T_CASE] = array (T_CASE, 'T_CASE', 'case', 'switch');
		$toReturn[T_CATCH] = array (T_CATCH, 'T_CATCH', 'catch', 'Les exceptions (disponible depuis PHP 5.0.0)');
		$toReturn[T_CHARACTER] = array (T_CHARACTER, 'T_CHARACTER', null, null);
		$toReturn[T_CLASS] = array (T_CLASS, 'T_CLASS', 'class', 'classes et objets');
		$toReturn[T_CLASS_C] = array (T_CLASS_C, 'T_CLASS_C', '__CLASS__', 'constantes magiques (disponible depuis PHP 4.3.0)');
		$toReturn[T_CLONE] = array (T_CLONE, 'T_CLONE', 'clone', 'classes et objets. (disponible depuis PHP 5.0.0)');
		$toReturn[T_CLOSE_TAG] = array (T_CLOSE_TAG, 'T_CLOSE_TAG', '?> ou %>', null); 	 
		$toReturn[T_COMMENT] = array (T_COMMENT, 'T_COMMENT', '// ou #, et /* */ en PHP 5', 'commentaires');
		$toReturn[T_CONCAT_EQUAL] = array (T_CONCAT_EQUAL, 'T_CONCAT_EQUAL', '.=', 'opérateurs d\'assignation');
		$toReturn[T_CONST] = array (T_CONST, 'T_CONST', 'const', null); 	 
		$toReturn[T_CONSTANT_ENCAPSED_STRING] = array (T_CONSTANT_ENCAPSED_STRING, 'T_CONSTANT_ENCAPSED_STRING', '"foo" ou \'bar\'', 'syntaxe chaîne de caractères');
		$toReturn[T_CONTINUE] = array (T_CONTINUE, 'T_CONTINUE', 'continue', null); 	 
		$toReturn[T_CURLY_OPEN] = array (T_CURLY_OPEN, 'T_CURLY_OPEN', null, null);
		$toReturn[T_DEC] = array (T_DEC, 'T_DEC', '--', 'opérateurs d\'incrémention/décrémention');
		$toReturn[T_DECLARE] = array (T_DECLARE, 'T_DECLARE', 'declare', 'declare');
		$toReturn[T_DEFAULT] = array (T_DEFAULT, 'T_DEFAULT', 'default', 'switch');
		//$toReturn[T_DIR] = array (T_DIR, 'T_DIR', '__DIR__', 'constantes magiques (disponible depuis PHP 5.3.0)');
		$toReturn[T_DIV_EQUAL] = array (T_DIV_EQUAL, 'T_DIV_EQUAL', '/=', 'opérateurs d\'assignation');
		$toReturn[T_DNUMBER] = array (T_DNUMBER, 'T_DNUMBER', '0.12, etc.', 'nombres à virgule flottante');
		$toReturn[T_DOC_COMMENT] = array (T_DOC_COMMENT, 'T_DOC_COMMENT', '/** */', 'style de commentaire dans la PHPDoc (disponible depuis PHP 5.0.0)');
		$toReturn[T_DO] = array (T_DO, 'T_DO', 'do', 'do...while');
		$toReturn[T_DOLLAR_OPEN_CURLY_BRACES] = array (T_DOLLAR_OPEN_CURLY_BRACES, 'T_DOLLAR_OPEN_CURLY_BRACES', '${', 'syntaxe de variable complexe analysée');
		$toReturn[T_DOUBLE_ARROW] = array (T_DOUBLE_ARROW, 'T_DOUBLE_ARROW', '=>', 'syntaxe de tableau');
		$toReturn[T_DOUBLE_CAST] = array (T_DOUBLE_CAST, 'T_DOUBLE_CAST', '(real), (double) ou (float)', 'transtypage');
		$toReturn[T_DOUBLE_COLON] = array (T_DOUBLE_COLON, 'T_DOUBLE_COLON', '::', null);
		$toReturn[T_ECHO] = array (T_ECHO, 'T_ECHO', 'echo', 'echo()');
		$toReturn[T_ELSE] = array (T_ELSE, 'T_ELSE', 'else', 'else');
		$toReturn[T_ELSEIF] = array (T_ELSEIF, 'T_ELSEIF', 'elseif', 'elseif');
		$toReturn[T_EMPTY] = array (T_EMPTY, 'T_EMPTY', 'empty', 'empty()');
		$toReturn[T_ENCAPSED_AND_WHITESPACE] = array (T_ENCAPSED_AND_WHITESPACE, 'T_ENCAPSED_AND_WHITESPACE', null, null);
		$toReturn[T_ENDDECLARE] = array (T_ENDDECLARE, 'T_ENDDECLARE', 'enddeclare', 'declare, syntaxe alternative');
		$toReturn[T_ENDFOR] = array (T_ENDFOR, 'T_ENDFOR', 'endfor', 'for, syntaxe alternative');
		$toReturn[T_ENDFOREACH] = array (T_ENDFOREACH, 'T_ENDFOREACH', 'endforeach', 'foreach, syntaxe alternative');
		$toReturn[T_ENDIF] = array (T_ENDIF, 'T_ENDIF', 'endif', 'if, syntaxe alternative');
		$toReturn[T_ENDSWITCH] = array (T_ENDSWITCH, 'T_ENDSWITCH', 'endswitch', 'switch, syntaxe alternative');
		$toReturn[T_ENDWHILE] = array (T_ENDWHILE, 'T_ENDWHILE', 'endwhile', 'while, syntaxe alternative');
		$toReturn[T_END_HEREDOC] = array (T_END_HEREDOC, 'T_END_HEREDOC', null, 'syntaxe heredoc');
		$toReturn[T_EVAL] = array (T_EVAL, 'T_EVAL', 'eval()', 'eval()');
		$toReturn[T_EXIT] = array (T_EXIT, 'T_EXIT', 'exit or die', 'exit(), die()');
		$toReturn[T_EXTENDS] = array (T_EXTENDS, 'T_EXTENDS', 'extends', 'extends, classes et objets');
		$toReturn[T_FILE] = array (T_FILE, 'T_FILE', '__FILE__', 'constantes magiques');
		$toReturn[T_FINAL] = array (T_FINAL, 'T_FINAL', 'final', 'Mot-clé "final" (disponible depuis PHP 5.0.0)');
		$toReturn[T_FOR] = array (T_FOR, 'T_FOR', 'for', 'for');
		$toReturn[T_FOREACH] = array (T_FOREACH, 'T_FOREACH', 'foreach', 'foreach');
		$toReturn[T_FUNCTION] = array (T_FUNCTION, 'T_FUNCTION', 'function or cfunction', 'fonctions');
		$toReturn[T_FUNC_C] = array (T_FUNC_C, 'T_FUNC_C', '__FUNCTION__', 'constantes magiques (disponible depuis PHP 4.3.0)');
		$toReturn[T_GLOBAL] = array (T_GLOBAL, 'T_GLOBAL', 'global', 'scope de variable');
		$toReturn[T_HALT_COMPILER] = array (T_HALT_COMPILER, 'T_HALT_COMPILER', '__halt_compiler()', '__halt_compiler (disponible depuis PHP 5.1.0)');
		$toReturn[T_IF] = array (T_IF, 'T_IF', 'if', 'if');
		$toReturn[T_IMPLEMENTS] = array (T_IMPLEMENTS, 'T_IMPLEMENTS', 'implements', 'Interfaces (disponible depuis PHP 5.0.0)');
		$toReturn[T_INC] = array (T_INC, 'T_INC', '++', 'opérateurs d\'incrémention/décrémention');
		$toReturn[T_INCLUDE] = array (T_INCLUDE, 'T_INCLUDE', 'include()', 'include()');
		$toReturn[T_INCLUDE_ONCE] = array (T_INCLUDE_ONCE, 'T_INCLUDE_ONCE', 'include_once()', 'include_once()');
		$toReturn[T_INLINE_HTML] = array (T_INLINE_HTML, 'T_INLINE_HTML', null, null);
		$toReturn[T_INSTANCEOF] = array (T_INSTANCEOF, 'T_INSTANCEOF', 'instanceof', 'opérateurs de type (disponible depuis PHP 5.0.0)');
		$toReturn[T_INT_CAST] = array (T_INT_CAST, 'T_INT_CAST', '(int) ou (integer)', 'transtypage');
		$toReturn[T_INTERFACE] = array (T_INTERFACE, 'T_INTERFACE', 'interface', 'Interfaces (dipsonible depuis PHP 5.0.0)');
		$toReturn[T_ISSET] = array (T_ISSET, 'T_ISSET', 'isset()', 'isset()');
		$toReturn[T_IS_EQUAL] = array (T_IS_EQUAL, 'T_IS_EQUAL', '==', 'opérateurs de comparaison');
		$toReturn[T_IS_GREATER_OR_EQUAL] = array (T_IS_GREATER_OR_EQUAL, 'T_IS_GREATER_OR_EQUAL', '>=', 'opérateurs de comparaison');
		$toReturn[T_IS_IDENTICAL] = array (T_IS_IDENTICAL, 'T_IS_IDENTICAL', '===', 'opérateurs de comparaison');
		$toReturn[T_IS_NOT_EQUAL] = array (T_IS_NOT_EQUAL, 'T_IS_NOT_EQUAL', '!= ou <>', 'opérateurs de comparaison');
		$toReturn[T_IS_NOT_IDENTICAL] = array (T_IS_NOT_IDENTICAL, 'T_IS_NOT_IDENTICAL', '!==', 'opérateurs de comparaison');
		$toReturn[T_IS_SMALLER_OR_EQUAL] = array (T_IS_SMALLER_OR_EQUAL, 'T_IS_SMALLER_OR_EQUAL', '<=', 'opérateurs de comparaison');
		$toReturn[T_LINE] = array (T_LINE, 'T_LINE', '__LINE__', 'constantes magiques');
		$toReturn[T_LIST] = array (T_LIST, 'T_LIST', 'list()', 'list()');
		$toReturn[T_LNUMBER] = array (T_LNUMBER, 'T_LNUMBER', '123, 012, 0x1ac, etc', 'entiers');
		$toReturn[T_LOGICAL_AND] = array (T_LOGICAL_AND, 'T_LOGICAL_AND', 'and', 'opérateurs logiques');
		$toReturn[T_LOGICAL_OR] = array (T_LOGICAL_OR, 'T_LOGICAL_OR', 'or', 'opérateurs logiques');
		$toReturn[T_LOGICAL_XOR] = array (T_LOGICAL_XOR, 'T_LOGICAL_XOR', 'xor', 'opérateurs logiques');
		$toReturn[T_METHOD_C] = array (T_METHOD_C, 'T_METHOD_C', '__METHOD__', 'constantes magiques (disponible depuis PHP 5.0.0)');
		$toReturn[T_MINUS_EQUAL] = array (T_MINUS_EQUAL, 'T_MINUS_EQUAL', '-=', 'opérateurs d\'assignation');
		//$toReturn[T_ML_COMMENT] = array (T_ML_COMMENT, 'T_ML_COMMENT', '/* et */', 'commentaires (PHP 4 uniquement)');
		$toReturn[T_MOD_EQUAL] = array (T_MOD_EQUAL, 'T_MOD_EQUAL', '%=', 'opérateurs d\'assignation');
		$toReturn[T_MUL_EQUAL] = array (T_MUL_EQUAL, 'T_MUL_EQUAL', '*=', 'opérateurs d\'assignation');
		//$toReturn[T_NS_C] = array (T_NS_C, 'T_NS_C', '__NAMESPACE__', 'namespaces. Également défini comme T_NAMESPACE (disponible depuis PHP 5.3.0)');
		$toReturn[T_NEW] = array (T_NEW, 'T_NEW', 'new', 'classes et objets');
		$toReturn[T_NUM_STRING] = array (T_NUM_STRING, 'T_NUM_STRING', null, null);
		$toReturn[T_OBJECT_CAST] = array (T_OBJECT_CAST, 'T_OBJECT_CAST', '(object)', 'transtypage');
		$toReturn[T_OBJECT_OPERATOR] = array (T_OBJECT_OPERATOR, 'T_OBJECT_OPERATOR', '->', 'classes et objets');
		//$toReturn[T_OLD_FUNCTION] = array (T_OLD_FUNCTION, 'T_OLD_FUNCTION', null, 'old_function'); 	 
		$toReturn[T_OPEN_TAG] = array (T_OPEN_TAG, 'T_OPEN_TAG', '<?php, <? or <%', 'sortie du mode HTML');
		$toReturn[T_OPEN_TAG_WITH_ECHO] = array (T_OPEN_TAG_WITH_ECHO, 'T_OPEN_TAG_WITH_ECHO', '<?= ou <%=', 'sortie du mode HTML');
		$toReturn[T_OR_EQUAL] = array (T_OR_EQUAL, 'T_OR_EQUAL', '|=', 'opérateurs d\'assignation');
		$toReturn[T_PAAMAYIM_NEKUDOTAYIM] = array (T_PAAMAYIM_NEKUDOTAYIM, 'T_PAAMAYIM_NEKUDOTAYIM', '::', '::. Définie également en tant que T_DOUBLE_COLON.');
		$toReturn[T_PLUS_EQUAL] = array (T_PLUS_EQUAL, 'T_PLUS_EQUAL', '+=', 'opérateurs d\'assignation');
		$toReturn[T_PRINT] = array (T_PRINT, 'T_PRINT', 'print()', 'print()');
		$toReturn[T_PRIVATE] = array (T_PRIVATE, 'T_PRIVATE', 'private', 'classes et objets (disponible depuis PHP 5.0.0)');
		$toReturn[T_PUBLIC] = array (T_PUBLIC, 'T_PUBLIC', 'public', 'classes et objets (disponible depuis PHP 5.0.0)');
		$toReturn[T_PROTECTED] = array (T_PROTECTED, 'T_PROTECTED', 'protected', 'classes et objets (disponible depuis PHP 5.0.0)');
		$toReturn[T_REQUIRE] = array (T_REQUIRE, 'T_REQUIRE', 'require()', 'require()');
		$toReturn[T_REQUIRE_ONCE] = array (T_REQUIRE_ONCE, 'T_REQUIRE_ONCE', 'require_once()', 'require_once()');
		$toReturn[T_RETURN] = array (T_RETURN, 'T_RETURN', 'return', 'valeurs retournées');
		$toReturn[T_SL] = array (T_SL, 'T_SL', '<<', 'opérateurs sur les bits');
		$toReturn[T_SL_EQUAL] = array (T_SL_EQUAL, 'T_SL_EQUAL', '<<=', 'opérateurs d\'assignation');
		$toReturn[T_SR] = array (T_SR, 'T_SR', '>>', 'opérateurs sur les bits');
		$toReturn[T_SR_EQUAL] = array (T_SR_EQUAL, 'T_SR_EQUAL', '>>=', 'opérateurs d\'assignation');
		$toReturn[T_START_HEREDOC] = array (T_START_HEREDOC, 'T_START_HEREDOC', '<<<', 'syntaxe heredoc');
		$toReturn[T_STATIC] = array (T_STATIC, 'T_STATIC', 'static', 'scope de variable');
		$toReturn[T_STRING] = array (T_STRING, 'T_STRING', null, null);
		$toReturn[T_STRING_CAST] = array (T_STRING_CAST, 'T_STRING_CAST', '(string)', 'transtypage');
		$toReturn[T_STRING_VARNAME] = array (T_STRING_VARNAME, 'T_STRING_VARNAME', null, null);
		$toReturn[T_SWITCH] = array (T_SWITCH, 'T_SWITCH', 'switch', 'switch');
		$toReturn[T_THROW] = array (T_THROW, 'T_THROW', 'throw', 'Les exceptions (disponible depuis PHP 5.0.0)');
		$toReturn[T_TRY] = array (T_TRY, 'T_TRY', 'try', 'Les exceptions (disponible depuis PHP 5.0.0)');
		$toReturn[T_UNSET] = array (T_UNSET, 'T_UNSET', 'unset()', 'unset()');
		$toReturn[T_UNSET_CAST] = array (T_UNSET_CAST, 'T_UNSET_CAST', '(unset)', '(non documenté; forcé à NULL)');
		$toReturn[T_USE] = array (T_USE, 'T_USE', 'use', 'namespaces (disponible depuis PHP 5.3.0)');
		$toReturn[T_VAR] = array (T_VAR, 'T_VAR', 'var', 'classes et objets');
		$toReturn[T_VARIABLE] = array (T_VARIABLE, 'T_VARIABLE', '$foo', 'variables');
		$toReturn[T_WHILE] = array (T_WHILE, 'T_WHILE', 'while', 'while, do...while');
		$toReturn[T_WHITESPACE] = array (T_WHITESPACE, 'T_WHITESPACE', ' ', null);
		$toReturn[T_XOR_EQUAL] = array (T_XOR_EQUAL, 'T_XOR_EQUAL', '^=', 'opérateurs d\'assignation');
		
		return $toReturn;
	}
	
	/**
	 * Retourne des infos sur la constante qui a la valeur $pValue
	 *
	 * @param int $pValue Valeur de la constante dont on veut les informations
	 * @return array
	 */
	public static function getConstante ($pValue) {
		$constantes = self::getConstantes ();
		return isset ($constantes[$pValue]) ? $constantes[$pValue] : null;
	}
	
	/**
	 * Retourne la prochaine valeur qui a pour ID de token T_STRING, en partant de $pIndex
	 *
	 * @param array $pTokens Tokens du fichier
	 * @param int $pIndex Index à partir duquel on va commencer à chercher
	 * @return string
	 */
	private static function _getNextT_STRING (&$pTokens, $pIndex) {
		// recherche du nom de la fonction
		for ($boucle = ($pIndex + 1); $boucle < count ($pTokens); $boucle++) {
			if (!is_string ($pTokens[$boucle])) {
				list ($funcTokenId, $funcTokenText) = $pTokens[$boucle];
				if ($funcTokenId == T_STRING) {
					return $funcTokenText;
				}
			}
		}
		return null;
	}
	
	/**
	 * Ajoute une erreur au tableau d'erreur interne, pour le retour de la méthode parseFile
	 *
	 * @param string $pError Erreur à sauvegarder
	 * @param array $pSubErrors Erreurs internes à l'erreur générale
	 * @param int $pType Type d'erreur, utiliser self::ERRTYPE_x
	 * @param int $pLine Ligne de l'erreur, si on ne veut pas utiliser self::_line
	 * @param string $pHelp Aide en cas d'erreur
	 */
	private static function _addError ($pError, $pSubErrors = null, $pType = self::ERRTYPE_SYNTAX, $pLine = null, $pHelp = null) {
		//if ($pType != self::ERRTYPE_PHPDOC) {
			$errorIndex = count (self::$_errors);
			self::$_errors[$errorIndex]['lineIndex'] = (!is_null ($pLine)) ? $pLine : self::$_line;
			self::$_errors[$errorIndex]['error'] = $pError;
			self::$_errors[$errorIndex]['type'] = $pType;
			self::$_errors[$errorIndex]['typeI18n'] = _i18n ('tokenizer.errtype.' . $pType);
			self::$_errors[$errorIndex]['subErrors'] = $pSubErrors;
			self::$_errors[$errorIndex]['help'] = $pHelp;
		//}
	}
	
	/**
	 * Parse les PHPDoc pour un fichier et vérifie qu'ils sont conformes
	 *
	 * @param string $pDoc PHPDoc à vérifier
	 * @return array PHPDoc parsé via CopixPHPDoc::parse
	 */
	private static function _parseFileDoc ($pDoc) {
		$errorStr = _i18n ('tokenizer.errors.T_DOC_COMMENT.fileCommentIsNotValid');
		$toCheck = array ('onlyThis' => true, 'package' => 1, 'subpackage' => 1, 'author' => 1, 'copyright' => 1, 'link' => 1, 'license' => 1);
		return self::_parseDoc ($pDoc, $toCheck, $errorStr, self::$_line - 1, _i18n ('tokenizer.help.docFile'));
	}
	
	/**
	 * Parse les PHPDoc d'une classe et vérifie qu'ils sont conformes
	 *
	 * @param string $pDoc PHPDoc à vérifier
	 * @return array PHPDoc parsé via CopixPHPDoc::parse
	 */
	private static function _parseClassDoc ($pDoc) {
		$errorStr = _i18n ('tokenizer.errors.T_DOC_COMMENT.classCommentIsNotValid', 'CLASSNAME');
		$toCheck = array ('onlyThis' => true, 'package' => 1, 'subpackage' => 1);
		return self::_parseDoc ($pDoc, $toCheck, $errorStr, self::$_line - 1, _i18n ('tokenizer.help.docClass'));
	}
	
	/**
	 * Parse les PHPDoc pour une fonction et vérifie qu'ils sont conformes
	 *
	 * @param array $pFunctionInfos Tableau d'informations sur la fonction
	 * @param string $pType Type de fonction (function ou method)
	 * @return array PHPDoc parsé via CopixPHPDoc::parse
	 */
	private static function _parseFunctionDoc ($pFunctionInfos, $pType = 'function') {
		if (!isset ($pFunctionInfos['phpdoc'])) {
			return array ();
		}
		
		//_dump ($pFunctionInfos);
		
		if (isset ($pFunctionInfos['return'])) {
			$toCheck['return'] = 1;
		}
		if (isset ($pFunctionInfos['throws'])) {
			$toCheck['throws'] = $pFunctionInfos['throws'];
		}
		if (isset ($pFunctionInfos['param'])) {
			$toCheck['param'] = count ($pFunctionInfos['param']);
		}
		
		try {
			$docs = CopixPHPDoc::parse ($pFunctionInfos['phpdoc']);
			$errors = $docs['parse_errors'];
			$checkErrors = CopixPHPDoc::check ($docs, $toCheck);
			$errors = array_merge ($errors, $checkErrors);
			
			// vérification des paramètres
			if (isset ($docs['param'])) {
				$paramIndex = 0;
				if (isset ($pFunctionInfos['param'])) {
					foreach ($pFunctionInfos['param'] as $param) {
						// si ce paramètre existe bien dans le phpdoc						
						if (isset ($docs['param'][$paramIndex])) {
							// le nom indiqué dans le phpdoc n'est pas le bon
							if ($docs['param'][$paramIndex]['name'] != $param) {
								$errors[] = _i18n ('tokenizer.errors.T_DOC_COMMENT.' . $pType . 'InvalidParamName', array ($docs['param'][$paramIndex]['name'], $param));
							}
						}
						// le nom du paramètre ne commence pas par $pX
						if (substr ($param, 0, 2) != '$p' || substr ($param, 2, 1) != strtoupper (substr ($param, 2, 1))) {
							$errors[] = _i18n ('tokenizer.errors.invalidParamName', $param);
						}
						
						$paramIndex++;
					}
				}
			}
			
			if (count ($errors) > 0) {
				$errorStr = _i18n ('tokenizer.errors.T_DOC_COMMENT.' . $pType . 'CommentIsNotValid', $pFunctionInfos['name']);
				self::_addError ($errorStr, $errors, self::ERRTYPE_PHPDOC, $pFunctionInfos['phpdocLine'], _i18n ('tokenizer.help.doc' . ucfirst ($pType)));
			}
		} catch (Exception $e) {
			self::_addError ($e->getMessage (), null, self::ERRTYPE_PHPDOC, self::$_line - 1, _i18n ('tokenizer.help.doc' . ucfirst ($pType)));
		}
	}
	
	/**
	 * Parse un commentaire PHPDoc, et vérifie l'existance des commentaires $pCheck
	 *
	 * @param string $pDoc PHPDoc à vérifier
	 * @param array $pCheck Commentaires dont on veut vérifier l'existance
	 * @param string $pErrorStr Erreur "principale" à afficher en cas d'erreurs secondaires
	 * @param int $pErrorLine Numéro de la ligne d'erreur, si on ne veut pas utiliser self::_line
	 * @param string $pHelp Aide en cas d'erreur
	 * @return array PHPDoc parsé via CopixPHPDoc::parse
	 */
	private static function _parseDoc ($pDoc, $pCheck, $pErrorStr, $pErrorLine = null, $pHelp = null) {
		$docs = array ();
		try {
			$docs = CopixPHPDoc::parse ($pDoc);
			//var_dump ($docs);
			$subErrors = $docs['parse_errors'];
			// vérification de la présence de certains paramètres
			$onlyThis = (isset ($pCheck['onlyThis']) && $pCheck['onlyThis']);
			unset ($pCheck['onlyThis']);
			$checkErrors = CopixPHPDoc::check ($docs, $pCheck, $onlyThis);
			$subErrors = array_merge ($subErrors, $checkErrors);
			if (count ($checkErrors) > 0) {
				self::_addError ($pErrorStr, $subErrors, self::ERRTYPE_PHPDOC, $pErrorLine, $pHelp);
			}
		} catch (Exception $e) {
			self::_addError ($e->getMessage (), null, self::ERRTYPE_PHPDOC, $pErrorLine, $pHelp);
		}		
		return $docs;
	}
}
?>