var ImageChooser = {
	init: function (pEditor) {
		var node = pEditor.selection.getNode ();
		var form = document.forms.imageChooser;
		var dom = tinyMCEPopup.editor.dom;

		if (node.nodeName == 'IMG') {
			document.getElementById ('tr_source').style.display = 'none';
			form.public_id.value = dom.getAttrib (node, 'public_id');
			form.src.src = tinyMCEPopup.editor.previewImageChooser_url;
			form.alt.value = dom.getAttrib (node, 'alt');
			form.title.value = dom.getAttrib (node, 'title');
			form.align.value = dom.getAttrib (node, 'align');
			form.hspace.value = dom.getAttrib (node, 'hspace');
			form.vspace.value = dom.getAttrib (node, 'vspace');
			form.classes.value = dom.getAttrib (node, 'class');
			form.stylesStr.value = dom.getAttrib (node, 'styles');
			form.thumb_enabled[0].checked = (dom.getAttrib (node, 'thumb_show_image') != '');
			form.thumb_enabled[1].checked = !form.thumb_enabled[0].checked;
			form.thumb_width.value = dom.getAttrib (node, 'width');
			form.thumb_height.value = dom.getAttrib (node, 'height');
			form.thumb_keep_proportions[0].checked = (dom.getAttrib (node, 'thumb_keep_proportions') == 'true');
			form.thumb_keep_proportions[1].checked = !form.thumb_keep_proportions[0].checked;
			form.thumb_show_image.value = dom.getAttrib (node, 'thumb_show_image');
			form.thumb_galery_id.value = dom.getAttrib (node, 'thumb_galery_id');
		}
	},

	insert: function () {
		var form = document.forms.imageChooser;
		var html = '<img';
		html += ' public_id="' + form.public_id.value + '"';
		html += ' src="' + form.src.src + '"';
		html += ' alt="' + form.alt.value + '"';
		html += ' title="' + form.title.value + '"';
		html += ' align="' + form.align.value + '"';
		html += ' hspace="' + form.hspace.value + '"';
		html += ' vspace="' + form.vspace.value + '"';
		html += ' class="' + form.classes.value + '"';
		html += ' styles="' + form.stylesStr.value + '"';
		if (form.thumb_enabled[0].checked) {
			html += ' width="' + form.thumb_width.value + '"';
			html += ' height="' + form.thumb_height.value + '"';
			if (form.thumb_keep_proportions[0].checked) {
				html += ' thumb_keep_proportions="true"';
			} else {
				html += ' thumb_keep_proportions="false"';
			}
			html += ' thumb_show_image="' + form.thumb_show_image.value + '"';
			html += ' thumb_galery_id="' + form.thumb_galery_id.value + '"';
		}
		tinyMCEPopup.editor.execCommand ('mceInsertContent', false, html);
		tinyMCEPopup.close ();
	}
}

tinyMCEPopup.onInit.add (ImageChooser.init, ImageChooser);