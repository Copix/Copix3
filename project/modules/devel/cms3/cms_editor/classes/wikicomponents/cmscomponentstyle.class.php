<?php
/**
 * @package		cms_editor
 * @subpackage	cms3
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Selvi ARIK
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|abstracttokenizercomponent');

class CmsComponentStyle extends AbstractTokenizerComponent {
    protected $_startTag = null;
    protected $_endTag = '{{/block-style}}';

    public function getStartTagLength ($pData = null) {
    	return strlen ($pData);
    }

	public function getStartTagsPosition ($pString) {
        //preg_match_all ('%(\{\{block-style class=\'\w*\'\}\})\w*\{\{\/block-style\}\}%', $pString, $matches,  PREG_OFFSET_CAPTURE);
        preg_match_all ('%(\{\{block-style class=\'\w*\'\}\})%', $pString, $matches,  PREG_OFFSET_CAPTURE);
        $arPos = array ();
        foreach ($matches[1] as $match) {
    		$pos = $match[1];
    		$tag = $match[0];
    		$arPos[$pos] = $tag;
        }
        return $arPos;
    }
    
    public function render ($pText, $pToken) {
        $sBalise = $pToken->getStartTag();
        preg_match_all('%\{\{block-style class=\'(\w*)\'\}\}%', $sBalise, $matches, PREG_SET_ORDER);
    	foreach ($matches as $itemToReplace) {
            $sBalise = str_replace($itemToReplace[0], '<span class="'.$itemToReplace[1].'">', $sBalise);
		}
        return str_replace($this->_endTag, '', $sBalise.$pText).'</span>';
    }
}

?>