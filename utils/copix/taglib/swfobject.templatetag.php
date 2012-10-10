<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 *
 */
class TemplateTagSWFObject extends CopixTemplateTag  {
	
	private function _filterAttribute($pValue) {
		return ($pValue !== null && $pValue !== false);
	}

	public function process ($pContent=null){
		$pParams = $this->getParams ();

		// Récupère les paramètres
		$src = $this->requireParam ('src');
		
		$attributes = array();	
		$attributes['width'] = $this->requireParam('width', 'integer');
		$attributes['height'] = $this->requireParam('height', 'integer');
		$attributes['quality'] = $this->getParam('quality', 'high');
	
		$id = $this->getParam('id', uniqid('swf'));
		$name = $this->getParam('name', $id);
		$version = $this->getParam('version', '8');
		
		$params = $this->getParam ('params', array());
		$vars = $this->getParam ('vars', array());
		$extra = $this->getParams ();

		// Reformate la version
		@list($major, $minor, $rev) = explode('.', $version);
		$version = sprintf('%d.%d.%d', $major, $minor, $rev);

		// Filtre les attributs
		$attributes = array_filter($attributes, array($this, '_filterAttribute'));
		
		// Intègre les paramètres de la forme params_X et vars_X dans les bons tableaux
		foreach($extra as $key=>$value) {
			if(substr($key, 0, 7) == 'params_') {
				$params[substr($key, 7)] = $value;
				unset($extra[$key]);
			} elseif(substr($key, 0, 5) == 'vars_') {
				$vars[substr($key, 5)] = $value;
				unset($extra[$key]);
			}
		}
		
		// Calcule la chaîne de variables
		$flashVars = (count($vars) > 0) ? http_build_query($vars) : '';
		
		$toReturn = array();

		// Génère le tag Object (pour IE et compat HTML 4)
		$toReturn[] = '<object';
		$toReturn[] = ' id="'.$id.'"';
		$toReturn[] = ' classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"';
		$toReturn[] = ' codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version='.str_replace('.',',',$version).',0" ';		
		foreach(array_merge($attributes, $extra) as $key=>$value) {
			if($value === true) {
				$toReturn[] = ' '.$key;
			} elseif($vale !== false) {
				$toReturn[] = sprintf(' %s="%s"', $key, htmlspecialchars($value));
			}
		}
		$toReturn[] = '>';
		$toReturn[] = '<param name="movie" value="'.$src.'" />';
		if($flashVars) {
			
			$toReturn[] = '<param name="flashvars" value="'.$flashVars.'" />';
		}
		foreach($params as $key=>$value) {
			$toReturn[] = sprintf('<param name="%s" value="%s" />', $key, htmlspecialchars($value));
		}

		// Génère le tag Embed (pour FF/Moz)
		$toReturn[] = '<embed ';
		$toReturn[] = ' id="'.$id.'"';
		$toReturn[] = ' src="'.$src.'"';
		foreach(array_merge($attributes, $extra, $params) as $key=>$value) {
			if($value === true) {
				$toReturn[] = ' '.$key;
			} elseif($vale !== false) {
				$toReturn[] = sprintf(' %s="%s"', $key, htmlspecialchars($value));
			}
			
		}
		if($flashVars) {
			$toReturn[] = ' flashvars="'.$flashVars.'"';
		}
		$toReturn[] = ' name="'.htmlspecialchars($name).'"';
		$toReturn[] = ' type="application/x-shockwave-flash"';
		$toReturn[] = ' pluginspage="http://www.macromedia.com/go/getflashplayer"';
		$toReturn[] = ' />';
		
		// Fin du tag Object
		$toReturn[] = '</object>';
				
		// IE a besoin d'un coup de pouce (ça ne fait pas de mal aux navigateurs qui prétendraient être IE).
		if(strpos($_SERVER['HTTP_USER_AGENT'], ' MSIE ') !== false) {
			CopixHTMLHeader::addJSLink(_resource('js/taglib/swfobject.js'), array('id'=>'tag_swfobject_js', 'defer'=>true));
			
			// Si on est en AJAX, force un appelle à processSWFObjects
			if(CopixAJAX::isAJAXRequest()) {
				CopixHTMLHeader::addJSDOMReadyCode('processSWFObjects();', 'tag_swfobject_processSWFObjects');
			}
		}
		
		return implode("", $toReturn);
	}
}