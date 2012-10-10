<?php

class TemplateTagColorpicker extends CopixTemplateTag {
  
    public function process ($pParams, $pContent=null) {
	   extract($pParams);

	   //input check
	   if (empty($name)) {
	   	  throw new CopixTemplateTagException ("[plugin colorpicker] parameter 'name' cannot be empty");
	   }
       if (empty($id)) {
	   	  throw new CopixTemplateTagException ("[plugin colorpicker] parameter 'name' cannot be empty");
	   }

	   //input check
	   /*if (empty($value)) {
	   	  throw new CopixTemplateTagException ("[plugin colorpicker] parameter 'value' cannot be empty");
	   }*/
	   CopixHTMLHeader::addJSFramework();
	   
	   
	   
	   
	   CopixHTMLHeader::addJSLink(_resource('js/taglib/tag_colorpicker.js'), array('id' => 'colorpicker'));
	   
	   $urlImages = _resource('commons/themes/taglib/img/');
	   
	   $js = <<< EOJS
	   
	   	$$("div[class='colorpicker']").each(
		function(elem){
			var input = $('input' + elem.id);
			
			
			
			var startColorHex = input.value ? input.value : '#ffffff';
			var rain = new MooRainbow(elem.id, {
				id: 'rainbow'+elem.id,
				wheel: true,
				startColor : startColorHex.hexToRgb(true),
				/*onChange: function(color) {
				elem.setStyle('background-color', color.hex);
					input.value = color.hex;
				},*/
				imgPath: '$urlImages/',
				onComplete: function(color) {
					elem.setStyle('background-color', color.hex);
					input.value = color.hex;
				}
			});
			input.addEvent('blur', function(event){
    			rain.manualSet(input.value, 'hex');
    			elem.setStyle('background-color', input.value);
    		});
			elem.setStyle('background-color', startColorHex);
		}
	);
EOJS;

	   CopixHTMLHeader::addJSDOMReadyCode($js, 'colorpickerInit');
	   CopixHTMLHeader::addCSSLink(_resource('commons/themes/taglib/style/colorpicker.css'), array('id' => 'colorpickerCss'));
	   
	   $titleCode = '';
	   if (!empty($title)) {
	   	 $titleCode = 'title="'.$title.'"';
	   }
	   $toReturn = '
	   <div '.$titleCode.' class="colorpicker" id="'.$id.'" >&nbsp;</div>
		<input id="input'.$id.'" name="'.$name.'" value="'.$value.'" type="text" size="13" class="colorpickerInput" />
	   ';
	   
	   return $toReturn;
    }
}
?>