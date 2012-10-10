<?php
/**
 * @package		tools
 * @subpackage	repository
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Zone d'affichage du formulaire d'upload
 * @todo : Ajouter un
 * @package tools
 * @subpackage repository
 */
class ZoneUploadForm extends CopixZone{

	/**
	 * Crééation du contenu
	 *
	 * @param boolean $toReturn
	 */
	protected function _createContent (& $toReturn){
		$tpl = new CopixTpl ();
		$tpl-> assign ('uploader', 'Test');
		$jsCode = $this->_getJsCode ();
		CopixHTMLHeader::addJsCode ($jsCode);
		CopixHTMLHeader::addJSLink(_resource ('js/swfupload/swfupload.js'));
		CopixHTMLHeader::addJSLink(_resource ('js/swfupload/swfupload.graceful_degradation.js'));
		// CopixHTMLHeader::addJSLink(_resource ('js/swfupload/swfupload.queue.js'));
		CopixHTMLHeader::addJSLink(_resource ('js/swfupload/handlers.js'));
		$toReturn = $tpl->fetch ('upload.form.php');
		return true;
	}

	/**
	 * Retourner le code JS nécessaire à l'upload
	 * @return string
	 */
	private function _getJsCode (){
		$upload_url = _url ('repository|ajax|upload');
		$flash_url =  '../../..//swf/swfupload_f9.swf';

		$upload_script = <<<EOF
		var swf_upload_control;

		window.onload = function(){
		swf_upload_control = new SWFUpload({
		// Backend settings
		upload_url: "$upload_url", // Relative to the SWF file, you can use an absolute URL as well.
		file_post_name: "resume_file",

		// Flash file settings
		file_size_limit: "10240", // 10 MB
		file_types: "*.*", // or you could use something like: "*.doc;*.wpd;*.pdf",
		file_types_description: "All Files",
		file_upload_limit: "0", // Even though I only want one file I want the user to be able to try again if an upload fails
		file_queue_limit: "1", // this isn't needed because the upload_limit will automatically place a queue limit
		// Event handler settings
		swfupload_loaded_handler: myShowUI,

		//file_dialog_start_handler : fileDialogStart,		// I don't need to override this handler
		file_queued_handler: fileQueued,
		file_queue_error_handler: fileQueueError,
		file_dialog_complete_handler: fileDialogComplete,

		//upload_start_handler : uploadStart,	// I could do some client/JavaScript validation here, but I don't need to.
		upload_progress_handler: uploadProgress,
		upload_error_handler: uploadError,
		upload_success_handler: uploadSuccess,
		upload_complete_handler: uploadComplete,

		// Flash Settings
		flash_url: "$flash_url", // Relative to this file
        // UI settings
        swfupload_element_id: "flashUI", // setting for the graceful degradation plugin
        degraded_element_id: "degradedUI",
        
        custom_settings: {
            progress_target: "fsUploadProgress",
            upload_successful: false
        },
        
        // Debug settings
        debug: false
    });    
}

function myShowUI(){
	var btnSubmit = document.getElementById("btnSubmit");    
    var txtFileTitle = document.getElementById("file_title");
	var selectFileCategory = document.getElementById("file_category");
	var selectFileSubCategory = document.getElementById("file_subcategory");
	
	btnSubmit.onclick = doSubmit;
    btnSubmit.disabled = true;

	txtFileTitle.onchange = validateForm;
	selectFileCategory.onchange = validateForm;
	selectFileSubCategory.onchange = validateForm;
			
    SWFUpload.swfUploadLoaded.apply(this);  // Let SWFUpload finish loading the UI.
	validateForm();
    
}

		function validateForm() {
			var txtFileTitle = document.getElementById("file_title");
			var selectFileCategory = document.getElementById("file_category");
			var selectFileSubCategory = document.getElementById("file_subcategory");
			var txtFileName = document.getElementById("txtFileName");
			
			var is_valid = true;
			if (txtFileTitle.value === "") is_valid = false;
			if (selectFileCategory.value === "") is_valid = false;
			if (selectFileSubCategory.value === "") is_valid = false;
			if (txtFileName.value === "") is_valid = false;
			
			document.getElementById("btnSubmit").disabled = !is_valid;
		
		}
		
function fileBrowse(){
    var txtFileName = document.getElementById("txtFileName");
    txtFileName.value = "";
    
    this.cancelUpload();
    this.selectFile();
}


// Called by the submit button to start the upload
function doSubmit(e){
    e = e || window.event;
    if (e.stopPropagation) 
        e.stopPropagation();
    e.cancelBubble = true;
    
    try {
        swf_upload_control.startUpload();
    } 
    catch (ex) {
    
    }
    return false;
}

// Called by the queue complete handler to submit the form
function uploadDone(){
    try {
        document.getElementById("uploadform").submit();
    } 
    catch (ex) {
        alert("Error submitting form");
    }
}

EOF;
		return $upload_script;
	}

}