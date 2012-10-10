/**
 *
 * @author Vuidart Sylvain
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('elementchooser');

	tinymce.create('tinymce.plugins.ElementChooserPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			var divLink = new Element ('div' , {'style': 'display:none', 'id':'div'+ed.editorId + 'link'});
			$(document.body).adopt(divLink);
			
			var ajax = new Request.HTML({
				url : Copix.getActionURL('cms_editor|ajax|getelementchooser', {'mode' : 0, 'name':ed.editorId + 'link'}),
				update : divLink,
				onComplete : function(){
					$('wysiwygEditor_' + ed.editorId + 'link').addEvent('change', function (){					
						e = ed.dom.getParent(ed.selection.getNode(), 'A');
						
						ed.execCommand("mceBeginUndoLevel");
				
						// Create new anchor elements
						if (e == null) {
							ed.getDoc().execCommand("unlink", false, null);
							ed.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo : 1});
				
							tinymce.each(ed.dom.select("a"), function(n) {
								if (ed.dom.getAttrib(n, 'href') == '#mce_temp_url#') {
									e = n;
				
									ed.dom.setAttribs(e, {
										href : "(cms:" + $('wysiwygEditor_' + ed.editorId + 'link').value + ")"/*,
									title : "toto",
									target : "toto",
									'class' : "toto"*/
									});
								}
							});
						} else {
							ed.dom.setAttribs(e, {
								href : "(cms:" + $('wysiwygEditor_' + ed.editorId + 'link').value + ")"/*,
									title : "toto",
									target : "toto",
									'class' : "toto"*/
							});
						}
						ed.execCommand("mceEndUndoLevel");
					});
				},
				evalScripts : true
			});
			
			ajax.send();

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceElementChooserLink', function() {
				$('clicker' + ed.editorId + 'link').fireEvent('click');
			});
			
			ed.addButton('elementChooserLink', {
				title : 'Liens vers un élément',
				cmd : 'mceElementChooserLink',
				image : Copix.getResourceURL('heading|img/links.png')
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('elementChooserLink', n.nodeName == 'A');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'ElementChooser Plugin',
				author : 'VUIDART Sylvain',
				authorurl : '',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('elementchooser', tinymce.plugins.ElementChooserPlugin);
})();