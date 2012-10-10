<script>
var updateFormulaire = function (identifiantFormulaire, portletId, pageId)
{
    if (typeof updateToolBar =='function') 
	{
        updateToolBar(portletId, pageId);
    } 

	var selectedFields = '';
	$('option_div_'+identifiantFormulaire).getElements('input:checked').each(
		function (el) {
			selectedFields += el.value + '.';
		}
	);
	
	var requestVar = {
		'editId' : pageId,
		'portletId' : portletId,
		'cmsform' : $('cmsform_' + identifiantFormulaire).value,
		'cf_theme' : $('cf_theme_' + identifiantFormulaire).value,
		'cf_public_id_confirmation' : $('cf_public_id_confirmation_' + identifiantFormulaire).value,
		'cf_confirmation' : $('confirm_page').checked,
		'selectedFields' : selectedFields
	};
	
    var request = new Request.HTML(
		{
			url : '<?php echo _url('form|adminajax|setCMSForm'); ?>',
			evalScripts : true
		}
	).get(requestVar);
}
</script>


<div id="div_<?php echo $identifiantFormulaire; ?>" style="padding:10px;margin-bottom:15px;" class="pageMenu">
	<div class="headForm" style="font-weight: bold;">Formulaire : </div>
	<div>
		<div  style="width:180px;float:left;"><label for="<?php echo $selectId; ?>">Type de formulaire : &nbsp;</label></div>
		<?php echo _tag("select", array('id'=>$selectId, 'name'=>$selectId, 'values'=>$arCMSForms, 'extra'=>'style="min-width:280px;"', 'selected'=>$selectedForm)) ;?>
		<div style="clear:both"></div>
	</div>	
	<div>
		<div  style="width:180px;float:left;"><label for="cf_theme_<?php echo $identifiantFormulaire; ?>">Thème : &nbsp;</label></div>
		<?php echo _tag("select", array('id'=>"cf_theme_$identifiantFormulaire", 'name'=>"cf_theme_$identifiantFormulaire", 'values'=>$arThemes, 'extra'=>'style="min-width:280px;"', 'selected'=>$selectedTheme)) ;?>
		<div style="clear:both"></div>
	</div>
	<div>
		<div  style="width:180px;float:left;"><label>Page de confirmation : &nbsp;</label></div>
		<input name="confirm" type="radio" id="confirm_defaut" <?php if(!$selectedConfirmationTheme || !$confirmation){echo "checked='checked'";} ?> value="0">
		<label for="confirm_defaut">par défaut</label>
		<input name="confirm" type="radio" id="confirm_page" <?php if($selectedConfirmationTheme && $confirmation){echo "checked='checked'";} ?> value="1">
		<label for="confirm_page">choisir une page : </label>
		<div id="confirmchoosepage" style="display:<?php echo $selectedConfirmationTheme && $confirmation ? 'inline' : 'none'; ?>">
		<?php 
		echo CopixZone::process ('heading|headingelement/headingelementchooser', array(
				'identifiantFormulaire' =>$identifiantFormulaire,
				'arTypes'=>array('page'),
				'selectedIndex'=>$selectedConfirmationTheme, 
				'id'=>'cf_public_id_confirmation_'.$identifiantFormulaire,
				'inputElement'=>'cf_public_id_confirmation_'.$identifiantFormulaire));
		?>
		</div>
		<div style="clear:both"></div>
	</div>
	<div class="headForm" style="font-weight: bold;cursor:pointer;" onclick="$('option_div_<?php echo $identifiantFormulaire; ?>').style.display='';">Options avancées </div>
	<div id="option_div_<?php echo $identifiantFormulaire; ?>" style="display:none;">
		<?php echo _tag("checkbox", array('name'=>"form_fields_$identifiantFormulaire", "values"=>$fields, 'selected'=>$selectedFields, 'separator'=>'<br />')); ?>
	</div>
</div>

<?php 
CopixHTMLHeader::addJSDOMReadyCode("
	$('confirm_defaut').addEvent('click', function(){
		$('confirmchoosepage').setStyle('display','none');
		updateFormulaire('$identifiantFormulaire', '$identifiantFormulaire', '"._request("editId")."');
	});
	$('confirm_page').addEvent('click', function(){
		$('confirmchoosepage').setStyle('display','inline');
		updateFormulaire('$identifiantFormulaire', '$identifiantFormulaire', '"._request("editId")."');
	});

	$('cmsform_$identifiantFormulaire').addEvent('change', 
		function (){
			updateFormulaire('$identifiantFormulaire', '$identifiantFormulaire', '"._request("editId")."');
		}
	);
	$('cf_theme_$identifiantFormulaire').addEvent('change', 
		function (){
			updateFormulaire('$identifiantFormulaire', '$identifiantFormulaire', '"._request("editId")."');
		}
	);
	$('cf_public_id_confirmation_$identifiantFormulaire').addEvent('change', 
		function (){
			updateFormulaire('$identifiantFormulaire', '$identifiantFormulaire', '"._request("editId")."');
		}
	);
	
	$$('#option_div_$identifiantFormulaire input').each (function (el){
		el.addEvent('click', 
		function (){
			updateFormulaire('$identifiantFormulaire', '$identifiantFormulaire', '"._request("editId")."');
		}
		)
	});
");