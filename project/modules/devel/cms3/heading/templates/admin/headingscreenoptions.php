<div class="screenOptions">
	<div class="screenOptionsContent"><?php echo $zone ?></div>
</div>
<?php
$js = "
var myAccordion = new Fx.Accordion ($$ ('.screenOptionsToggler'), $$ ('.screenOptions'), {
	display: 2,
	alwaysHide: true
});";
CopixHTMLHeader::addJSDOMReadyCode ($js);
?>