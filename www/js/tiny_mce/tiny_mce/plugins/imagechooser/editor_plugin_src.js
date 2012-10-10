/**
 *
 * @author Vuidart Sylvain & Steevan BARBOYON
 */

(function() {
	tinymce.create('tinymce.plugins.ImageChooserPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function (ed, url) {
			var divImage = new Element ('div' , {'style': 'display:none', 'id':'div'+ed.editorId + 'image'});
			document.body.adopt(divImage);
			
			var ajax = new Request.HTML({
				url : Copix.getActionURL ('cms_editor|ajax|getelementchooser', {'mode' : 1, 'name':ed.editorId + 'image'}),
				update : divImage,
				onComplete : function () {
					$ ('wysiwygEditor_' + ed.editorId + 'image').addEvent ('change', function () {this.showPopUp (ed, url)}.bind (this));
				}.bind (this),
				evalScripts : true
			});
			
			ajax.send();

			ed.addCommand('mceElementChooserImage', function () {
				if (ed.selection.getNode ().nodeName != 'IMG') {
					$ ('clicker' + ed.editorId + 'image').fireEvent ('click');
				} else {
					this.showPopUp (ed, url);
				}
			}.bind (this));
			
			ed.addButton('elementChooserImage', {
				title : 'Image',
				cmd : 'mceElementChooserImage',
				image : Copix.getResourceURL('images|img/images.png')
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('elementChooserImage', n.nodeName == 'IMG');
			});
		},

		showPopUp: function (ed, url) {
			var popupURL = url + '/imagechooser.php?name=' + $ ('libelleElement' + ed.editorId + 'image').innerHTML;
			popupURL += '&public_id=' + $ ('wysiwygEditor_' + ed.editorId + 'image').value;
			popupURL += '&src=' + Copix.getActionURL ('heading||', {'public_id' : $ ('wysiwygEditor_' + ed.editorId + 'image').value});
			if (ed.selection.getNode ().nodeName == 'IMG') {
				// oui c'est pas terrible, mais je n'ai trouvé que ça
				ed.previewImageChooser_url = Copix.getActionURL ('heading||', {'public_id' : ed.dom.getAttrib (ed.selection.getNode (), 'public_id')});
			}
			ed.windowManager.open ({
				file : popupURL,
				width : 500,
				height : 380,
				inline : 1
			}, {
				plugin_url : url // Plugin absolute URL
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
	tinymce.PluginManager.add('imagechooser', tinymce.plugins.ImageChooserPlugin);
})();