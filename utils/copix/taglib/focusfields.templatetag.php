<?php
/**
 * Génère des champs de saisie avec le suivi du focus lors de la saisie
 * @author fredericb
 *
 */
class TemplateTagFocusFields extends CopixTemplateTag {
	/**
	 * Retourne l'HTML du tag
	 */
	public function process ($pContent = null) {
		$toReturn = '';
		$config = $this->getParam('config', Array());
		$separator = $this->getParam('separator', null);
		
		for ($i = 0; $i<count($config); $i++){
			
			// get current element
			$current = $config[$i];
			// get parametters 
	        if (!isset ($current['id'])){
	        	$current['id'] = $current['name'];  
	        }elseif (!isset ($current['name'])){
	        	$current['name'] = $current['id'];
	        }
			$type = ( isset( $current['type'] ) ) ? ' type="'.$current['type'].'" ' : 'type="text"';
			$id = ( isset( $current['id'] ) ) ? ' id="'.$current['id'].'" ' : '';
			$name = ( isset( $current['name'] ) ) ? ' name="'.$current['name'].'" ' : '';
			$size = ( isset( $current['size'] ) ) ? ' size="'.$current['size'].'" ' : '';
			$value = ( isset( $current['value'] ) ) ? ' value="'.$current['value'].'" ' : '';
			$class = ( isset( $current['class'] ) ) ? ' class="text '.$current['class'].'" ' : 'class="text"';
			$maxlength = (isset( $current['maxlength'] ) ) ? ' maxlength="'.$current['maxlength'].'" ' : '';
			$style = (isset( $current['style'] ) ) ? ' style="'.$current['style'].'" ' : '';
			$title = (isset( $current['title'] ) ) ? ' title="'.$current['title'].'" ' : '';
			if( isset( $current['maxlength'] ) ){
				$width = ' width:'.($current['maxlength'] * 0.6).'em';
				if( isset( $current['style'] ) ){
					$style = ' style="'.$current['style'].'; '.$width.'"';
				} else {
					$style = ' style="'.$width.'"';
				}
			}
			
			$extra = '';
			if(array_key_exists('extra', $current)){
				if (is_array( $current['extra'] )) {
					foreach( $current['extra'] as $key => $value ){
						$extra .= $key.'="'.$value.'" ';
					}
				} else {
					$extra = $current['extra'];
				}
			}
			
			$label = (isset( $current['label'] ) ) ? '<label for="'.$id.'">'.$current['label'].'</label> ' : '';
			
			// create controls
			
			$toReturn .= $label.'<input '.$type.$class.$name.$id.$value.$size.$maxlength.$title.$extra.$style;
			
			$toReturn.= ' onkeydown="focusid(this, '.$current ['maxlength'].', event, ';
			// if there are no elements after this one, set null
			if($i < (count($config) - 1)){
				$toReturn.= '\''.$config[$i+1]['id'].'\', ';
			}else{
				$toReturn.= '\'null\', ';
			}
			if($i > 0){
				$toReturn.= '\''.$config[$i-1]['id'].'\'';
			}else{
				$toReturn.= '\'null\'';
			}
			$toReturn.= ');"';
			$toReturn.= '/>';
			if(isset($separator) && ($i<(count($config) - 1))){
				$toReturn.=$separator;	
			}
		}
		
        CopixHTMLHeader::addJSLink (_resource ('js/taglib/tag_inputtext.js'));
		
		return $toReturn;
	}
}