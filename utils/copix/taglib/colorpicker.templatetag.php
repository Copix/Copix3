<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Biesse Frédéric, Arik Selvi
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @see			http://moorainbow.woolly-sheep.net/
 */

class TemplateTagColorPicker extends CopixTemplateTag {

    /**
     * Génération d'une boite de saisie pour les couleurs
     * @package copix
     * @subpackage taglib
     *
     * Paramètre requis
     * 	- id : nom et identifiant de l'input qui sera créé et qui contiendra la couleur
     * Paramètres optionnels
     * - value
     * - size
     * - mini : boolean (mini color picker)
     * - onchange : code JS à executer au changement de couleur
     * - onComplete : code JS à executer au changement de couleur
     */
    public function process ($pContent = null) {
        $aParams = $this->getParams ();
        if ((!isset ($aParams['id']) && !isset ($aParams['name'])) || (empty ($aParams['id']) && empty ($aParams['name']))) {
	   		throw new CopixTemplateTagException ("[plugin colorpicker] parameter 'name' or 'id' cannot be empty");
        }

        if (!isset ($aParams['id'])) {
        	$aParams['id'] = $aParams['name'];
        }
        elseif (!isset ($aParams['name'])) {
        	$aParams['name'] = $aParams['id'];
        }

        if ((!isset ($aParams['size'])) || (intval ($aParams['size'])) <= 0) {
        	$aParams['size'] = 6;
        }
        else {
        	$aParams['size'] = intval($aParams['size']);
        }

        if (empty ($aParams['value'])) {
            $aParams['value'] = '#ffffff';
        }

        if (array_key_exists('onComplete', $aParams) && (!empty ($aParams['onComplete']))) {
            $onCompleteFunction = ',
                onComplete: function() {'.
                    $aParams['onComplete'].';'
                .'}
            ';
        }
        else {
            $onCompleteFunction = '';
        }

        CopixHTMLHeader::addJSLink(_resource('js/mootools/plugins/moorainbow.js'), array('id' => 'colorpicker'));

        $urlImages = _resource ('js/mootools/css/moorainbow/img/', null, true);

        $js = <<<EOJS
        var aStartColor = '{$aParams['value']}'.hexToRgb(true);
        var myMooRainbow_{$aParams['id']} = new MooRainbow('pictoRainbow{$aParams['id']}', {
            id: 'rainbowDiv{$aParams['id']}',
			startColor : '{$aParams['value']}'.hexToRgb(true),
			wheel: true,
            imgPath: '{$urlImages}/',
			onChange: function(color) {
                $('{$aParams['id']}').setStyle('background-color', color.hex);
				$('{$aParams['id']}').value = color.hex;
            }{$onCompleteFunction}
        });

        $('{$aParams['id']}').addEvent('click', function () {
            myMooRainbow_{$aParams['id']}.toggle();
        });

        $('{$aParams['id']}').addEvent('change', function () {
            this.setStyle('background-color', $('{$aParams['id']}').value);
        });

        $('{$aParams['id']}').setStyle('background-color', $('{$aParams['id']}').value);
EOJS;

        CopixHTMLHeader::addJSDOMReadyCode ($js);
        if (isset ($aParams['mini']) && ($aParams['mini'])) {
            CopixHTMLHeader::addCSSLink (_resource ('js/mootools/css/moorainbow/mini_moorainbow.css'));
        }
        else {
            CopixHTMLHeader::addCSSLink(_resource ('js/mootools/css/moorainbow/moorainbow.css'));
        }
        $attributsHTML = array ('class', 'disabled', 'id', 'name', 'readonly', 'size', 'style', 'tabindex', 'title', 'value');
        $attributes = '';
		foreach( $attributsHTML as $attribut ){
			if(isset($aParams[$attribut])){
				$attributes .= ' '.$attribut.'="'.$aParams[$attribut].'"';
			}
		}
        $urlIcone = _resource ('img/tools/color.png');
        $toReturn = <<<EOHTML
        <input type="text" {$attributes} maxlength="7" />
        <img id="pictoRainbow{$aParams['id']}" src="{$urlIcone}" alt="[c]" width="16" height="16" />
EOHTML;

        return $toReturn;
    }
}
?>