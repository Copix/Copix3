<?php
echo _tag('select', array('id'=>'cfe_type', 'name'=>'cfe_type', 'values'=>$ppo->arTypeElement, 'emptyShow'=>false, 'selected'=>$ppo->selectedType));
?>
<script type="text/javascript">
$('cfe_type').addEvent ('change', function (){
		$('td_button_type_save').setStyle ('display', $('cfe_type').value == '<?php echo $ppo->selectedType; ?>' ? 'none' : '');
	});
</script>