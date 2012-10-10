<?php
/**
 * @package		cms_editor
 * @subpackage	cms3
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Sylvain VUIDART basé sur le markdownparser
 * 
 * PHP Markdown
 * Copyright (c) 2004-2008 Michel Fortin  
 * <http://www.michelf.com/projects/php-markdown/>
 *
 * Original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 * 
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|abstracttokenizercomponent');

class CmsComponentBoldItalic extends AbstractTokenizerComponent {
	protected $_startTag = null;
    protected $_endTag = null;
    
	private $em_relist = array(
		''  => '(?:(?<!\*)\*(?!\*)|(?<!_)_(?!_))(?=\S)(?![.,:;]\s)',
		'*' => '(?<=\S)(?<!\*)\*(?!\*)',
		'_' => '(?<=\S)(?<!_)_(?!_)',
		);
	private $strong_relist = array(
		''   => '(?:(?<!\*)\*\*(?!\*)|(?<!_)__(?!_))(?=\S)(?![.,:;]\s)',
		'**' => '(?<=\S)(?<!\*)\*\*(?!\*)',
		'__' => '(?<=\S)(?<!_)__(?!_)',
		);
	private $em_strong_relist = array(
		''    => '(?:(?<!\*)\*\*\*(?!\*)|(?<!_)___(?!_))(?=\S)(?![.,:;]\s)',
		'***' => '(?<=\S)(?<!\*)\*\*\*(?!\*)',
		'___' => '(?<=\S)(?<!_)___(?!_)',
		);
	private $em_strong_prepared_relist;
	
	/*
	 * Prepare regular expressions for seraching emphasis tokens in any
	 *  context.
	 */
	public function prepareItalicsAndBold() {
		foreach ($this->em_relist as $em => $em_re) {
			foreach ($this->strong_relist as $strong => $strong_re) {
				# Construct list of allowed token expressions.
				$token_relist = array();
				if (isset($this->em_strong_relist["$em$strong"])) {
					$token_relist[] = $this->em_strong_relist["$em$strong"];
				}
				$token_relist[] = $em_re;
				$token_relist[] = $strong_re;
				
				# Construct master expression from list.
				$token_re = '{('. implode('|', $token_relist) .')}';
				$this->em_strong_prepared_relist["$em$strong"] = $token_re;
			}
		}
	}
    
    public function render ($pText, $pToken) {
    	switch (strlen($pToken->getStartTag())){
        	case 1 :
    			return "<em>$pText</em>";
        	case 2 :
    			return "<strong>$pText</strong>";
    		case 3 :
    			return "<strong><em>$pText</em></strong>";
    	}
    	return '<strong>'.$pText.'</strong>';
    }
    
    public function getStartTagLength ($pData = null) {
    	return strlen ($pData);
    }
    
	public function getEndTagLength ($pData = null) {
    	return strlen ($pData);
    } 
    
	public function getStartTagsPosition ($pString) {
		$this->prepareItalicsAndBold();
		return $this->doItalicsAndBold ($pString);
    }
    
	public function getEndingTag ($pString, $pToken) {
		if (substr ($pString, 0, strlen($pToken->getStartTag())) == $pToken->getStartTag()) {
			return $pToken->getStartTag();
		} else {
			return false;
		}
    }
    
    public function doItalicsAndBold($text) {
		$token_stack = array('');
		$text_stack = array('');
		$em = '';
		$strong = '';
		$tree_char_em = false;
		
		$arPos = array ();
		$lastPlace = 0;
		$begin = true;
		
		while (1) {
			#
			# Get prepared regular expression for seraching emphasis tokens
			# in current context.
			#
			$token_re = $this->em_strong_prepared_relist["$em$strong"];
			
			#
			# Each loop iteration seach for the next emphasis token. 
			# Each token is then passed to handleSpanToken.
			#
			$parts = preg_split($token_re, $text, 2, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
			if (sizeof($parts) <2){
				break;
			}
			$text_stack[0] .= $parts[0][0];
			$token =& $parts[1][0];
			$text =& $parts[2][0];
			if ($begin){
				$arPos[$lastPlace + $parts[1][1]] = $parts[1][0];				
				$begin = false;
			} else {
				$begin = true;
			}
			$lastPlace += $parts[2][1];
			
			if (empty($token)) {
				# Reached end of text span: empty stack without emitting.
				# any more emphasis.
				while ($token_stack[0]) {
					$text_stack[1] .= array_shift($token_stack);
					$text_stack[0] .= array_shift($text_stack);
				}
				break;
			}
			
			$token_len = strlen($token);
			if ($tree_char_em) {
				# Reached closing marker while inside a three-char emphasis.
				if ($token_len == 3) {
					# Three-char closing marker, close em and strong.
					array_shift($token_stack);
					$span = array_shift($text_stack);
					/*$span = $this->runSpanGamut($span);
					$span = "<strong><em>$span</em></strong>";
					$text_stack[0] .= $this->hashPart($span);*/
					$em = '';
					$strong = '';
				} else {
					# Other closing marker: close one em or strong and
					# change current token state to match the other
					$token_stack[0] = str_repeat($token{0}, 3-$token_len);
					$tag = $token_len == 2 ? "strong" : "em";
					/*$span = $text_stack[0];
					$span = $this->runSpanGamut($span);
					$span = "<$tag>$span</$tag>";
					$text_stack[0] = $this->hashPart($span);*/
					$$tag = ''; # $$tag stands for $em or $strong
				}
				$tree_char_em = false;
			} else if ($token_len == 3) {
				if ($em) {
					# Reached closing marker for both em and strong.
					# Closing strong marker:
					for ($i = 0; $i < 2; ++$i) {
						$shifted_token = array_shift($token_stack);
						$tag = strlen($shifted_token) == 2 ? "strong" : "em";
						/*$span = array_shift($text_stack);
						$span = $this->runSpanGamut($span);
						$span = "<$tag>$span</$tag>";
						$text_stack[0] .= $this->hashPart($span);*/
						$$tag = ''; # $$tag stands for $em or $strong
					}
				} else {
					# Reached opening three-char emphasis marker. Push on token 
					# stack; will be handled by the special condition above.
					$em = $token{0};
					$strong = "$em$em";
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$tree_char_em = true;
				}
			} else if ($token_len == 2) {
				if ($strong) {
					# Unwind any dangling emphasis marker:
					if (strlen($token_stack[0]) == 1) {
						$text_stack[1] .= array_shift($token_stack);
						$text_stack[0] .= array_shift($text_stack);
					}
					# Closing strong marker:
					array_shift($token_stack);
					$span = array_shift($text_stack);
					/*$span = $this->runSpanGamut($span);
					$span = "<strong>$span</strong>";
					$text_stack[0] .= $this->hashPart($span);*/
					$strong = '';
				} else {
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$strong = $token;
				}
			} else {
				# Here $token_len == 1
				if ($em) {
					if (strlen($token_stack[0]) == 1) {
						# Closing emphasis marker:
						array_shift($token_stack);
						$span = array_shift($text_stack);
						/*$span = $this->runSpanGamut($span);
						$span = "<em>$span</em>";
						$text_stack[0] .= $this->hashPart($span);*/
						$em = '';
					} else {
						$text_stack[0] .= $token;
					}
				} else {
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$em = $token;
				}
			}
		}
		return $arPos;
    }
}

?>