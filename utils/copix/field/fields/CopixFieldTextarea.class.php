<?php
class CopixFieldTextarea extends CopixAbstractField implements ICopixField  {
	
	/**
	 * (non-PHPdoc)
	 * @see field/ICopixField#getHTML()
	 */
	public function getHTML($pName, $pValue, $pMode = 'edit') {
            if($pMode == 'edit'){
                return $this->getHTMLFieldEdit($pName, $pValue);
            }else{
                return $pValue;
            }
	}
	
	/**
	 * Affichage du champ en édition
	 * @param $pName
	 * @param $pValue
	 * @return string
	 */
	public function getHTMLFieldEdit($pName, $pValue) {
		$params = $this->getParams();
		$toReturn = '<textarea id="' . $pName . '" name="' . $pName . '" ' . (array_key_exists('extra', $params) ? $params['extra'] : '') . ' >';
		$toReturn .= $pValue;
		$toReturn .= '</textarea>';
		return $toReturn;
	}
	
}