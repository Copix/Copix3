<?php $id = uniqid(); ?>
<a href="javascript:void(0);" id="clicker<?php echo $id; ?>"><?php echo $portlet->getOption('clicker', 'clicker'); ?></a>
<div id="<?php echo $id; ?>"><?php echo $text; ?></div>
<?php
CopixHTMLHeader::addJSDOMReadyCode("
	var myFx".$portlet->getRandomId()." = new Fx.Slide('".$id."');
	myFx".$portlet->getRandomId().".hide ();
	$('clicker".$id."').addEvent('click', function(){
		myFx".$portlet->getRandomId().".toggle('vertical');
	});
");
?>