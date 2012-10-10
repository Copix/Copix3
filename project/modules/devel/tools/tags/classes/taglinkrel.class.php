<?php
/**
 * @package		tools
 * @subpackage	tags
 * @author		Croes Gérald
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Classe qui représente les différentes valeurs possibles pour le type de relation
 */
class TagLinkRel {
	public static function getList (){
		return array (
			'alternate'=>_i18n ('An alternate version of the document (i.e. print page, translated or mirror)'), 
			'stylesheet'=>i18n ('An external style sheet for the document'),
			'start'=>i18n ('The first document in a selection'),
			'next'=>i18n ('The next document in a selection'),
			'prev'=>i18n ('The previous document in a selection'),
			'contents'=>i18n ('A table of contents for the document'),
			'index'=>i18n ('An index for the document'), 
			'glossary'=>i18n ('A glossary (explanation) of words used in the document'),
			'copyright'=>i18n ('A document containing copyright information'),
			'chapter'=>i18n ('A chapter of the document'),
			'section'=>i18n ('A section of the document'),
			'subsection'=>i18n ('A subsection of the document'),
			'appendix'=>i18n ('An appendix for the document'),
			'help'=>i18n ('A help document'),
			'bookmark'=>i18n ('A related document')
		);
	}
}