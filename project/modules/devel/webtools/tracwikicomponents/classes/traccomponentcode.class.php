<?php
/**
 * @package		webtools
 * @subpackage	tracwikicomponents
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */

_classInclude ('wikirenderer|abstracttokenizercomponent');

class TracComponentCode extends AbstractTokenizerComponent {
    protected $_startTag = '{{{';
    protected $_endTag = '}}}';
    protected $_mustBeParse = false;
    
    static private $_geshi = null;
    
    public function __construct () {
    	parent::__construct();
		require_once (CopixModule::getPath ('geshi').'lib/geshi/geshi.php');
        self::$_geshi = new GeSHi(null,null);
    }
    
	public function render ($pText, $pToken) {
		$code = null;
		if (preg_match_all('%#!(.*)%',$pText, $matches)) {
			$code = $matches[1][0];
		}
		if ($code != null) {
			self::$_geshi->set_source(substr($pText, strlen ($matches[0][0])+1));
			self::$_geshi->set_language($code);
        	self::$_geshi->set_header_type (GESHI_HEADER_DIV);
        	return '<div class="wiki_code">'.self::$_geshi->parse_code ().'</div>';

		}
    	return '<div class="wiki_code"><tt>'.htmlentities($pText).'</tt></div>';
    }
    
    
    public function __wakeup() {
    	require_once (CopixModule::getPath ('geshi').'lib/geshi/geshi.php');
        self::$_geshi = new GeSHi(null,null);
    }
}
?>