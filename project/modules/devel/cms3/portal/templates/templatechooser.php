<?php
CopixHTMLHeader::addJSLink(_resource('portal|js/tools.js'));

ob_start();

$hasManyTemplates = count($listeTemplates) > 1;

?>
<div class="templateChooserpopupinfo">
	<table id="tableTemplateChooser<?php echo $identifiant; ?>" class="TemplateChooser"><?php
	$i = 0;
	
	$urlGetOptions = _url ('portal|ajax|getOptions');
	$urlUpdateOptions = _url ('portal|ajax|updateOptions');
	
	if($hasOptions) {
		$sCurrentTemplateOption = str_replace('"', '\'', json_encode(''));
	}
	foreach ($listeTemplates as $template){
	?>
		<tr <?php echo ($i%2 == 0) ? 'class="alternate"' : ''; ?>>
			<td class="image">
				<?php if($template->image != ''){ ?>
				<img src="<?php echo _resource($module.'|img/templates/'.$template->image, $theme); ?>" alt="" />
				<?php } ?>
			</td>
			<td style="text-align: center">
				<strong id="<?php echo $i.$identifiant; ?>" <?php if($selected == $template->tpl || sizeof($listeTemplates) == 1) { ?>style="font-style:italic;"<?php }?>><?php echo $template->name; ?></strong>
				<br />
				<?php echo $template->description; ?>
				<br />
				<br />
				<input type="button" id="input<?php echo $i.$identifiant; ?>"
					rel="<?php echo $i.$identifiant; ?>"
					<?php if($selected == $template->tpl || sizeof($listeTemplates) == 1) { ?>
					value="Visuel sélectionné" disabled="disabled"
					<?php } else { ?>
					value="Choisir ce visuel"
					<?php } ?>
				/>
			</td>
		</tr>
		<?php
		
		
		if($hasManyTemplates || $hasOptions){
		CopixHTMLHeader::addJSDOMReadyCode("
		$('input".$i.$identifiant."').addEvent('click', function(){
			chooseTemplate($('input".$i.$identifiant."'), '".$template->tpl."', '".addslashes((String)$template->name)."', ".($hasOptions ? str_replace('"', '\'', json_encode($template->options)) : "null").", ".(($showSelection) ? 'true':'false' ).", '".$identifiant."', '".$inputId."', '".$portletId."',  '".$urlUpdateOptions."', '".$urlGetOptions."', '"._request('editId')."');
		});
		");
		}
		if($hasOptions) {
			// Récupération des options du template courant
			if($selected == $template->tpl || sizeof($listeTemplates) == 1) {
				
				$sCurrentTemplateOption = str_replace('"', '\'', json_encode($template->options));
			}
		}
		$i++;
	}
	?>
	</table>
</div>

<?php
$content = ob_get_contents();
ob_end_clean();

$value = ($selected == '') ? ((sizeof($listeTemplates) == 1) ? $listeTemplates[0]->tpl : "" ) : $selected;
if($hasManyTemplates || $hasOptions){
_etag ('copixwindow', array ('clicker' => 'clicker_templateChooser'.$identifiant, 'fixed' => 1, 'id' => 'templateChooser'.$identifiant, 'title' => 'Modèle de page'), $content);
	?>
	<a href="javascript: void(0);" id="clicker_templateChooser<?php echo $identifiant; ?>"><img src="<?php echo _resource ($img) ?>" /> 
		<span id="captionTemplateChooser<?php echo $identifiant; ?>"><?php echo $showText ? $text : ""; ?></span>
	</a>
	<?php
}
// Pour les template de portlets qui auraient des options
if($hasOptions) {
    $contentOptions = '<div>';
    $contentOptions .= '<form  id="optionsForm'.$identifiant.'" action="'.$urlUpdateOptions.'" method="post">';
    $contentOptions .= '<input type="hidden" value="'.$portletId.'" id="portletId" name="portletId" />';
    $contentOptions .= '<input type="hidden" value="'._request('editId').'" id="editId" name="editId" />';
    $contentOptions .= '<div class="optionContent" id="optionContent'.$identifiant.'"></div>';
    $contentOptions .= '</form>';
    $contentOptions .= '</div>';
?>
<div id="templateOptions<?php echo $identifiant?>">
    <?php echo $contentOptions; ?>
</div>
<?php
    unset($contentOptions);
}
?>
<input type="hidden" name="<?php echo $inputId; ?>" id="<?php echo $inputId; ?>" value="<?php echo $value; ?>" />
<?php

// DOMReadyCode
$sJSDOMReadyCode = "";
if ($hasOptions) {
   $sJSDOMReadyCode .= "
   $('optionsForm".$identifiant."').addEvent('submit', function(e) {
        e.stop();
        updateTemplateOptions('$identifiant', '".$urlUpdateOptions.".');
    });
    ";
    if (isset($sCurrentTemplateOption)) {
        $sJSDOMReadyCode .= 'updateFormOptions('.$sCurrentTemplateOption.', \''.$identifiant.'\', \''.$portletId.'\', \''.$urlUpdateOptions.'\', \''.$urlGetOptions.'\', \''._request('editId').'\');';
    }
}
CopixHTMLHeader::addJSDOMReadyCode($sJSDOMReadyCode);
?>