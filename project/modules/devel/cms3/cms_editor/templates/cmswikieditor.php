<?php 
	_eTag('mootools', array('plugins'=>"resize"));
	CopixHTMLHeader::addJSLink(_resource('cms_editor|js/tools.js'));
	CopixHTMLHeader::addCSSLink(_resource('cms_editor|styles/style.css'));
	CopixHTMLHeader::addCSSLink(_resource('cms_editor|styles/customstyle.css'));
	
	CopixHTMLHeader::addJSDOMReadyCode("
		new Resizing('".$name."',{'min':100,'max':800, 'userpreference': 'articles|contentHeight'});
	");
?>
<div class="cmsWikiEditor" style="margin-bottom:10px;">
	<div style="vertical-align:bottom;" class="cmsWikiEditorToolbar">
		<input title="Titre" type="button" class="cmsEditorBouton" value="Titre" style="width:50px;" onclick="setCmsWikiPolice('#', false, '<?php echo $name;?>')" />
		<input title="Titre 2" type="button" class="cmsEditorBouton" value="Titre 2" style="width:50px;" onclick="setCmsWikiPolice('##', false, '<?php echo $name;?>')" /> | 
		<input title="Gras" type="button" class="cmsEditorBouton" value="G" style="width:26px;font-weight:bold" onclick="setCmsWikiPolice('**', '**', '<?php echo $name;?>')" />
		<input title="Italique" type="button" class="cmsEditorBouton" value="I" style="width:26px;font-style:italic;font-weight:bold;" onclick="setCmsWikiPolice('_', '_', '<?php echo $name;?>')" /> |
		<input title="Lien" type="image" src="<?php echo _resource('portal|img/link.png'); ?>" class="cmsEditorBoutonImage" value="url" onclick="setCmsWikiPolice('[', '](http://adresse_du_lien \'infobulle du lien\')', '<?php echo $name;?>'); return false;" /> |
		<input title="Mentions légales" type="button" class="cmsEditorBouton" value="*" style="font-style:italic; font-size: small;" onclick='setCmsWikiPolice("{{block-legal}}", "{{/block-legal}}", "<?php echo $name;?>")' /> |
		<input title="Aligné à gauche" type="image" src="<?php echo _resource('portal|img/left_align.gif'); ?>" value="gauche" class="cmsEditorBoutonImage" onclick='setCmsWikiPolice("{{align-left}}", "{{/align-left}}", "<?php echo $name;?>"); return false;' />
		<input title="Centré" type="image" src="<?php echo _resource('portal|img/center_align.gif'); ?>" value="center" class="cmsEditorBoutonImage" onclick='setCmsWikiPolice("{{align-center}}", "{{/align-center}}", "<?php echo $name;?>"); return false;' />
		<input title="Aligné à droite" type="image" src="<?php echo _resource('portal|img/right_align.gif'); ?>" value="droite" class="cmsEditorBoutonImage" onclick='setCmsWikiPolice("{{align-right}}", "{{/align-right}}", "<?php echo $name;?>"); return false;' />
		<input title="Justifié" type="image" src="<?php echo _resource('portal|img/justified_align.gif'); ?>" value="justifié" class="cmsEditorBoutonImage" onclick='setCmsWikiPolice("{{align-justify}}", "{{/align-justify}}", "<?php echo $name;?>"); return false;' /> |
		<?php if ($iconImage) { ?>
			<span title="Image" class="boutonElementChooser"><?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array('class'=>"bouton-image", 'img'=>_resource('images|img/images.png'), 'arTypes'=>array('image'),'selectHeading'=>false, 'inputElement'=>'textEditorAddImage_'.$name, 'identifiantFormulaire'=>'image'.$name, 'multipleSelect'=>true, 'mode'=>ZoneHeadingElementChooser::IMAGE_CHOOSER_MOD, 'showSelection'=>false)); ?></span>
		<?php } ?>
		<?php if ($iconLink) { ?>
			<span title="Lien vers un élément" class="boutonElementChooser"><?php echo CopixZone::process ('heading|headingelement/headingelementchooser', array('showAnchor'=>true, 'class'=>"bouton-image", 'img'=>_resource('heading|img/links.png'), 'inputElement'=>'textEditorAddLink_'.$name, 'identifiantFormulaire'=>'link'.$name, 'showSelection'=>false)); ?></span> |
		<?php } ?>
		<input title="Paragraphe aligné à gauche" type="image" src="<?php echo _resource('portal|img/paragraph_left.png'); ?>" value="paragraphe aligné à gauche" class="cmsEditorBoutonImage" onclick='setCmsWikiPolice("{{block-left}}", "{{/block-left}}", "<?php echo $name;?>"); return false;' />
		<input title="Paragraphe aligné à droite" type="image" src="<?php echo _resource('portal|img/paragraph_right.png'); ?>" value="paragraphe aligné à droite" class="cmsEditorBoutonImage" onclick='setCmsWikiPolice("{{block-right}}", "{{/block-right}}", "<?php echo $name;?>"); return false;' :> |
		<input title="Saut de ligne" type="image" src="<?php echo _resource('portal|img/hr.png'); ?>" value="saut de ligne" class="cmsEditorBoutonImage" onclick='setCmsWikiPolice("[br]", "", "<?php echo $name;?>"); return false;' :>
		<input title="Ligne" type="image" src="<?php echo _resource('portal|img/line.png'); ?>" value="Ligne" class="cmsEditorBoutonImage" onclick='setCmsWikiPolice("\n----\n", "", "<?php echo $name;?>"); return false;' />
        <?php if(sizeof($style) > 0) {?>
         |
        <select class="cmsEditorSelect" onchange="if(this.value != '') {setCmsWikiPolice('{{block-style class=\''+this.value+'\'}}', '{{/block-style}}', '<?php echo $name;?>'); this.value = '';}">
            <option style="text-align:center" value="">--Style--</option>
            <?php foreach($style as $s) {?>
            <option value="<?php echo $s->class;?>" class="<?php echo $s->class;?>"><?php echo $s->name;?></option>
            <?php }?>
        </select>
        <?php }?>
		<a onclick="window.open('<?php echo _url('cms_editor|ajax|gethelpwiki'); ?>','_blank','toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=0, copyhistory=0, menuBar=0, width=600, height=600');" href="javascript:void(0);" id="helpviewer">
			<img src="<?php echo _resource('img/tools/help.png'); ?>" alt="Syntaxe" title="Guide d'utilisation du wiki (popup)" />
		</a>
	</div>
	<div class="cmsWikiEditorContent"><textarea name="<?php echo $name;?>" id="<?php echo $name;?>" style="height: <?php echo $height ?>px; width:100%;"><?php echo $text; ?></textarea></div>
</div>
<?php if ($preview) { ?>
<?php _eTag ('button', array ('img' => 'img/tools/show.png', 'caption' => 'Prévisualiser', 'id' => 'preview_button_' . $name, 'type' => 'button')) ?>
<div id="preview<?php echo $name;?>"></div>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
	$('preview_button_".$name."').addEvent('click', function (){ cmsWikiPreview('".$name."')});
");
}?>
<?php
if ($iconImage) {
	CopixHTMLHeader::addJSDOMReadyCode("
		$('textEditorAddImage_".$name."').addEvent('change', function (){
			var textArea = $('".$name."');
			var selectedText = textArea.value.substring( textArea.selectionStart ,textArea.selectionEnd);
			var text = selectedText == '' ? $('libelleElementimage".$name."').innerHTML : '';
			setCmsWikiPolice('![', text+']((image:'+$('textEditorAddImage_".$name."').value+'))', '".$name."');
		});
	");
}
if ($iconLink) {
	CopixHTMLHeader::addJSDOMReadyCode("
		$('textEditorAddLink_".$name."').addEvent('change', function (){
			var textArea = $('".$name."');
			var selectedText = textArea.value.substring( textArea.selectionStart ,textArea.selectionEnd);
			var text = selectedText == '' ? $('libelleElementlink".$name."').innerHTML : '';
			setCmsWikiPolice('[', text+']((cms:'+$(\"textEditorAddLink_".$name."\").value+') \"'+text+'\")', '".$name."');
		});
	");
}
?>
<script>
function addElements<?php echo 'image'.$name;?> (mutex){
	var selectedElements = getSelectedElements ('<?php echo 'image'.$name;?>');
	for (i = 0;i < selectedElements.length; i++){
		var selectedElement = selectedElements[i];
		var textArea = $('<?php echo $name;?>');
		var selectedText = textArea.value.substring( textArea.selectionStart ,textArea.selectionEnd);
		var text = selectedText == '' ? $('libelleElementimage<?php echo $name;?>').innerHTML : '';
		setCmsWikiPolice('![', selectedElement.get('libelle')+']((image:'+selectedElement.get('pih')+'))\n', '<?php echo $name;?>');
	}
	return false;
}
</script>