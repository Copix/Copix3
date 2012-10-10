<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagHtmlEditor extends CopixTemplateTag {
    public function process($pParams) {
        extract($pParams);

        if (empty ($content)) {
            $content = '&nbsp;';
        }
        //name of the textarea.
        if (empty ($name)) {
            throw new CopixTemplateTagException ('htmleditor: missing name parameter');
        }else {
            if (!isset ($width)) {
                $width = 400;
            }
            if (!isset ($height)) {
                $height = 400;
            }
        }

        if (empty ($editor) || ($editor === 'CKEditor')) {
            return $this->_doCKEditor ($name, $content, $width, $height);
        }else{
            return $this->_doTinyMCE ($name, $content, $width, $height);
        }
    }

    private function _doTinyMCE ($name, $content, $width, $height) {
        CopixHTMLHeader::addJSLink (_resource('js/tiny_mce/tiny_mce_src.js'), array ('concat' => false));

    
        $jsCode = <<<EOF
tinyMCE.init({
        language : "fr",
        skin : "o2k7",
        skin_variant : "silver",
        mode : "exact",
        setup : function(ed) {
        ed.onChange.add(function(ed, l) {
                try {
                        //fonction à definir si on veut capturer l'evenement
                   updateWysiwygform$name(ed.getContent());
                } catch (err) {
        }
      });
        },
        theme: "copixcms",
        elements : '$name',
        relative_urls : false,
        convert_urls : false,
        plugins : "table",
        table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
        table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
        table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
        table_cell_limit : 1000,
        table_row_limit : 500,
        table_col_limit : 500,
        extended_valid_elements : "img[class|src|border|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|thumb_title|thumb_title_pos|thumb_keep_proportions|public_id|thumb_show_image|thumb_galery_id]"

});
EOF;

        CopixHTMLHeader::addJSCode($jsCode);

        return '<textarea id="'.$name.'" name="'.$name.'" style="width:99%; height: '.$height.'px">'.$content.'</textarea>';
    }

    private function _doCKEditor ($name, $content, $width, $height) {
        CopixHTMLHeader::addJSLink (_resource ('js/ckeditor/ckeditor.js'));
        CopixHTMLHeader::addJSDOMReadyCode ("
        	if(CKEDITOR.instances['$name'] != undefined){
			    CKEDITOR.remove(CKEDITOR.instances['$name']);
			    CKEDITOR.replace('$name');
		    } else{
		        CKEDITOR.replace('$name');
		    }
        ");
    
        return '<textarea id="'.$name.'" name="'.$name.'" style="width:99%; height: '.$height.'px">'.$content.'</textarea>';
    }
}