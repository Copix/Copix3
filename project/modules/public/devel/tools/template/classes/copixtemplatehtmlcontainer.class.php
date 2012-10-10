<?php
/**
* Page HTML Standard
*/
class CopixTemplateHTMLContainer extends CopixTemplateContainer {
	/**
	* Constructor
	*/
	function CopixTemplateHTMLContainer (){
		parent::CopixTemplateElement();
		$this->_caption = 'Page HTLM';
		$this->_addProperty (new CopixTemplateStyleSheetProperty ('css', 'Feuille de Style', ''));
	}
	
    /**
    * Retourne le code HTML du container
    * @return string
    */
    function getHtml (){
    	$buffer = '';
    	/*
    	$buffer = '<html>
    	 <head>
    	 </head>
    	 <body>
    	 ';
    	*/
    	if (($css = $this->getPropertyValue ('css')) != null){
    		$cssPath = str_replace ('./', CopixUrl::get (), $css);
     	    $buffer .= '<style type="text/css" media="screen">@import "'.$cssPath.'";</style>';
    	}
    	foreach ($this->_elements as $elementId => $element){
    		$buffer .= $element->getHtml ();
    	}
    	/*
    	$buffer .= '</body>
    	 </html>';
    	*/
    	return $buffer;
    }
}
?>