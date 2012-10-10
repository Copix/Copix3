<?php
/**
 * @package webtools
 * @subpackage editarea
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Transforme un textarea en editeur avec coloration syntaxique
 * Pour les paramètres, voir http://www.cdolivet.com/editarea/editarea/docs/configuration.html
 *
 * @package webtools
 * @subpackage editarea
 */
class ZoneEditArea extends CopixZone {
	/**
	 * Met l'HTML généré dans $pToReturn
	 *
	 * @param string $pToReturn
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$syntaxs = array(
			'.bas' => 'basic',
			'.c' => 'c',
			'.cfm' => 'coldfusion',
			'.cpp' => 'cpp',
			'.css' => 'css',
			'.html' => 'html' ,
			'.java' => 'java',
			'.js' => 'js',
			'.pas' => 'pas',
			'.pl' => 'perl',
			'.php' => 'php',
			'.py' => 'python',
			'.txt' => 'robotstxt',
			'.sql' => 'sql',
			'.vbs' => 'vb',
			'.xml' => 'xml',
			'.rb' => 'ruby'
		);
		$editorExt = (array_key_exists ($this->getParam ('ext'), $syntaxs)) ? $syntaxs[$this->getParam ('ext')] : 'html';
		$lang = CopixI18N::getLang ();
		$id = $this->getParam ('id');
		// should contain a code of the syntax definition file that must be used for the highlight mode
		$syntax = $this->getParam ('syntax', 'html');
		// set if the editor should start with highlighted syntax displayed
		$start_highlight = $this->getParam ('start_highlight', 'true');
		// determine if the editor load the content of the textarea (false) or if it wait for an openFile() call for allowing file editing
		$is_multi_files = $this->getParam ('is_multi_files', 'false');
		// define the minimum width of the editor 
		$min_width = $this->getParam ('min_width', '400');
		// define the minimum height of the editor 
		$min_height = $this->getParam ('min_height', '100');
		// define one with axis the editor can be resized by the user
		// String ("no" (no resize allowed), "both" (x and y axis), "x", "y") 
		$allow_resize = $this->getParam ('allow_resize', 'both');
		// define if a toggle button must be added under the editor in order to allow to toggle between the editor and the orginal textarea
		$allow_toggle = $this->getParam ('allow_toggle', 'false');
		// a comma separated list of plugins to load
		$plugins = $this->getParam ('plugins', '');
		// define if the editor must be loaded only when the user navigotr is known to be a working one, or if it will be loaded for all navigators
		// String ("all" or "known") 
		$browsers = $this->getParam ('browsers', 'known');
		// specify when the textarea will be converted into an editor. If set to "later", the toogle button will be displayed to allow later conversion
		// String ("onload" or "later") 
		$display = $this->getParam ('display', 'onload');
		// define the toolbar that will be displayed, each element being separated by a ",".
		// Type: String (combinaison of: "|", "*", "search", "go_to_line", "undo", "redo", "change_smooth_selection", "reset_highlight", "highlight", "word_wrap", "help", "save", "load", "new_document", "syntax_selection")
		// "|" or "separator" make appears a separator in the toolbar.
		// "*" or "return" make appears a line-break in the toolbar
		$toolbar = $this->getParam ('browsers', 'search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, word_wrap, |, help');
		// toolbar button list to add before the toolbar defined by the "toolbar" option
		$begin_toolbar = $this->getParam ('begin_toolbar', '');
		// toolbar button list to add after the toolbar defined by the "toolbar" option
		$end_toolbar = $this->getParam ('end_toolbar', '');
		// define the font-size used to display the text in the editor
		$font_size = $this->getParam ('browsers', '8');
		// define the font-familly used to display the text in the editor. (eg: "monospace" or "verdana,monospace"). Opera will always use "monospace"
		$font_family = $this->getParam ('font_family', 'monospace');
		// define if the cursor should be placed where it was in the textarea before replacement (auto) or at the beginning of the file (begin)
		$cursor_position = $this->getParam ('cursor_position', 'begin');
		// allow to disable/enable the Firefox 2 spellchecker
		$gecko_spellcheck = $this->getParam ('gecko_spellcheck', 'false');
		// number of undo action allowed
		$max_undo = $this->getParam ('max_undo', '20');
		// determine if EditArea start in fullscreen mode or not
		$fullscreen = $this->getParam ('fullscreen', 'false');
		// determine if EditArea display only the highlighted syntax (no edition possiblities, no toolbars). It's possible to switch the editable mode whenever you want (code example for a toggle edit mode: editAreaLoader.execCommand('editor_id', 'set_editable', !editAreaLoader.execCommand('editor_id', 'is_editable'));).
		$is_editable = $this->getParam ('is_editable', 'true');
		// determine if the text will be automatically wrapped to the next line when it reach the end of a line. This is linked ot the word_wrap icon available in the toolbar
		$word_wrap = $this->getParam ('word_wrap', 'false');
		// define the number of spaces that will replace tabulations (\t) in text. If tabulation should stay tabulation, set this option to false
		$replace_tab_by_spaces = $this->getParam ('replace_tab_by_spaces', 'false');
		// used to display some debug information into a newly created textarea. Can be usefull to display trace info in it if you want to modify the code
		$debug = $this->getParam ('debug', 'false');
		// indique si on veut voir les couleurs des lignes
		$show_line_colors = $this->getParam ('show_line_colors', 'true');

		// the function name that will be called when the user will press the "load" button in the toolbar. This function will reveice one parameter that will be the id of the textarea. You can update the content of the textarea by using "editAreaLoader.setValue(the_id, new_value);". 
		$load_callback = $this->getParam ('load_callback', '');
		// the function name that will be called when the user will press the "save" button in the toolbar. This function will reveice two parameters, the first being the id of the textarea and the second containing the content of the textarea.
		$save_callback = $this->getParam ('save_callback', '');
		// the function name that will be called when the onchange event of the textarea of EditArea will be triggered. This function will reveice one parameter that will be the id of the textarea. Will be triggered only is EditArea is displayed.
		$change_callback = $this->getParam ('change_callback', '');
		// the function name that will be called when the form containing the EditArea will be submitted. This function will reveice one parameter that will be the id of the textarea. Will be triggered regardless the state of EditArea (displayed or not).
		$submit_callback = $this->getParam ('submit_callback', '');
		// the function name that will be called just after the editAreaLoader.init() function, once EditAreaLoader will be initalized but still not displayed. This function will receive one parameter that will be the id of the textarea.
		$EA_init_callback = $this->getParam ('EA_init_callback', '');
		// the function name that will be called when EditArea will be destroyed regardless the fact that it has been displayed or not. This function will reveice one parameter that will be the id of the textarea.
		$EA_delete_callback = $this->getParam ('EA_delete_callback', '');
		// the function name that will be called when EditArea will be toogled on for. This function will reveice one parameter that will be the id of the textarea. 
		$EA_toggle_on_callback = $this->getParam ('EA_toggle_on_callback', '');
		// the function name that will be called when EditArea will be toggled off. This function will reveice one parameter that will be the id of the textarea.
		$EA_toggle_off_callback = $this->getParam ('EA_toggle_off_callback', '');
		// the function name that will be called when EditArea will be displayed for the first time. This function will reveice one parameter that will be the id of the textarea.
		$EA_load_callback = $this->getParam ('EA_load_callback', '');
		// the function name that will be called when EditArea will be destroyed (if it have been displayed at least one time). This function will reveice one parameter that will be the id of the textarea.
		$EA_unload_callback = $this->getParam ('EA_unload_callback', '');
		// the function name that will be called when the tabulation of the file will be selected. This function will reveice one parameter that will be an associative array containing all file's infos.
		$EA_file_switch_on_callback = $this->getParam ('EA_file_switch_on_callback', '');
		// the function name that will be called when the tabulation of the file will be blur (the file was selected, and another file receive focus). This function will reveice one parameter that will be an associative array containing all file infos.
		$EA_file_switch_off_callback = $this->getParam ('EA_file_switch_off_callback', '');
		// the function name that will be called when the tabulation of a file will be closed. This function will reveice one parameter that will be an associative array containing all file infos. If the callback function return false, the file won't be closed. 
		$EA_file_close_callback = $this->getParam ('EA_file_close_callback', '');

		$js = <<<EOJS
			var lang = '$lang';
			var ext = '$editorExt';
			if(lang.length == 0) {
				lang = 'en';
			}
			editAreaLoader.init ({
				id: '$id',
				'syntax': '$syntax',
				'start_highlight': $start_highlight,
				'is_multi_files': $is_multi_files,
				'min_width': $min_width,
				'min_height': $min_height,
				'allow_resize': '$allow_resize',
				'allow_toggle': $allow_toggle,
				'plugins': '$plugins',
				'browsers': '$browsers',
				'display': '$display',
				'toolbar': '$toolbar',
				'begin_toolbar': '$begin_toolbar',
				'end_toolbar': '$end_toolbar',
				'font_size': $font_size,
				'font_family': '$font_family',
				'cursor_position': '$cursor_position',
				'gecko_spellcheck': $gecko_spellcheck,
				'max_undo': $max_undo,
				'fullscreen': $fullscreen,
				'is_editable': $is_editable,
				'word_wrap': $word_wrap,
				'replace_tab_by_spaces': $replace_tab_by_spaces,
				'debug': $debug,
				'show_line_colors': $show_line_colors,

				'load_callback': '$load_callback',
				'save_callback': '$save_callback',
				'change_callback': '$change_callback',
				'submit_callback': '$submit_callback',
				'EA_init_callback': '$EA_init_callback',
				'EA_delete_callback': '$EA_delete_callback',
				'EA_toggle_on_callback': '$EA_toggle_on_callback',
				'EA_toggle_off_callback': '$EA_toggle_off_callback',
				'EA_load_callback': '$EA_load_callback',
				'EA_unload_callback': '$EA_unload_callback',
				'EA_file_switch_on_callback': '$EA_file_switch_on_callback',
				'EA_file_switch_off_callback': '$EA_file_switch_off_callback',
				'EA_file_close_callback': '$EA_file_close_callback'
			});
EOJS;
		CopixHTMLHeader::addJSLink (_resource ('editarea|edit_area_full.js'), array ('concat' => false));
		CopixHTMLHeader::addJSDOMReadyCode ($js);

		return true;
	}
}