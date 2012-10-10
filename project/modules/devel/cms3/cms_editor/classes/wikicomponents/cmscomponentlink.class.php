<?php
/**
 * @package		cms_editor
 * @subpackage	cms3
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Sylvain VUIDART
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|abstracttokenizercomponent');

class CmsComponentLink extends AbstractTokenizerComponent {
    protected $_startTag = null;
    protected $_endTag = null;
    private $nested_brackets_depth = 6;
	private $nested_brackets_re;

	private $nested_url_parenthesis_depth = 4;
	private $nested_url_parenthesis_re;
    
	public function render ($pText, $pToken) {
		$this->nested_brackets_re =
		str_repeat('(?>[^\[\]]+|\[', $this->nested_brackets_depth).
		str_repeat('\])*', $this->nested_brackets_depth);

		$this->nested_url_parenthesis_re =
		str_repeat('(?>[^()\s]+|\(', $this->nested_url_parenthesis_depth).
		str_repeat('(?>\)))*', $this->nested_url_parenthesis_depth);

		preg_match('{
			(				# wrap whole match in $1
			  \[
				('.$this->nested_brackets_re.')	# link text = $2
			  \]
			  \(			# literal paren
				[ ]*
				(?:
					<(\S*)>	# href = $3
				|
					('.$this->nested_url_parenthesis_re.')	# href = $4
				)
				[ ]*
				(			# $5
				  ([\'"])	# quote char = $6
				  (.*?)		# Title = $7
				  \6		# matching quote
				  [ ]*	# ignore any spaces/tabs between closing quote and )
				)?			# title is optional
			  \)
			)
			}xs',$pToken->getStartTag (), $matches);
		
		$result = "<a href=\"$matches[4]\"";
		if (array_key_exists(7, $matches) && $matches[7] != "") {
			$title = htmlentities($matches[7], ENT_COMPAT, 'UTF-8');
			$result .=  " title=\"$title\"";
		}

		$link_text = $matches[2];
		$link_text = _class('cms_editor|cmswikiparser')->transform($link_text);
		$result .= ">$link_text</a>";
		
		return $result;
	}
    
    public function getStartTagLength ($pData = null) {
    	return strlen ($pData);
    }
    
	public function getEndTagLength ($pData = null) {
    	return strlen ($pData);
    } 
        
	public function getStartTagsPosition ($pString) {
		$this->nested_brackets_re =
		str_repeat('(?>[^\[\]]+|\[', $this->nested_brackets_depth).
		str_repeat('\])*', $this->nested_brackets_depth);

		$this->nested_url_parenthesis_re =
		str_repeat('(?>[^()\s]+|\(', $this->nested_url_parenthesis_depth).
		str_repeat('(?>\)))*', $this->nested_url_parenthesis_depth);

		preg_match_all('{
			(				# wrap whole match in $1
			  \[
				('.$this->nested_brackets_re.')	# link text = $2
			  \]
			  \(			# literal paren
				[ ]*
				(?:
					<(\S*)>	# href = $3
				|
					('.$this->nested_url_parenthesis_re.')	# href = $4
				)
				[ ]*
				(			# $5
				  ([\'"])	# quote char = $6
				  (.*?)		# Title = $7
				  \6		# matching quote
				  [ ]*	# ignore any spaces/tabs between closing quote and )
				)?			# title is optional
			  \)
			)
			}xs',$pString, $matches, PREG_OFFSET_CAPTURE);

		$arPos = array();
		foreach ($matches[0] as $key=>$match){
			$arPos[$match[1]] = $match[0];	
		}
		return $arPos;
    }
    
	public function getEndingTag ($pString, $pToken) {
		return '';
    }
}

?>