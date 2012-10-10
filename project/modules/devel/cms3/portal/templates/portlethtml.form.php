<?php 
CopixHTMLHeader::addJSLink (_resource('portal|js/tools.js'));
//initialisation des variables
$identifiantFormulaire = $portlet->getRandomId ();	
$params = new CopixParameterHandler ();
$titreOptions = $portlet->getOptions ();
$params->setParams($titreOptions);
$idTextArea = 'htmltext_' . $identifiantFormulaire;;
?>
<div id="div_<?php echo $identifiantFormulaire;?>">
	<div id="htmldiv_<?php echo $identifiantFormulaire;?>" style="margin-bottom:10px;">
		<?php //_dump ($params->getParam ('htmltext')); ?>
		<textarea style="width: 100%; height: 500px" name="<?php echo $idTextArea;?>" id="<?php echo $idTextArea;?>"><?php echo utf8_encode (htmlentities (utf8_decode ($params->getParam ('htmltext')))) ?></textarea>
	</div>
</div>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
	$ ('".$idTextArea."').addEvent ('change', function (pEl) {
		console.debug (pEl.value);
		updateHtmlText ('" . $identifiantFormulaire . "', '" . $portlet->getRandomId () . "', '" . _request ('editId') . "');
	});
");

if (CopixModule::isEnabled ('editarea')) {
	CopixZone::process ('editarea|editarea', array ('change_callback' => 'changeEditArea', 'ext' => '.html', 'id' => 'htmltext_' . $identifiantFormulaire));
	?>
	<script type="text/javascript">
	var changeEditArea = function (el) {
		$ (el).value = editAreaLoader.getValue (el);
		updateHtmlText ('<?php echo $identifiantFormulaire;?>', '<?php echo $portlet->getRandomId ();?>', '<?php echo _request ('editId'); ?>');
	}
	</script>
<?php } ?>