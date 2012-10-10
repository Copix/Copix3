<?php _eTag ('mootools', array ('plugin' => 'copixformobserver')) ?>

<form id="headingElementInformationForm" name="headingElementInformationForm" action="<?php echo _url ('heading|admin|save') ?>" method="POST">
<input type="hidden" name="uniqId" value="<?php echo $uniqId ?>" />

<?php if ($uniqueElement) { ?>
	<input type="hidden" name="id_helt" value="<?php echo $record->id_helt ?>" />
	<input type="hidden" name="type_hei" value="<?php echo $record->type_hei ?>" />
<?php } ?>

<div id="HeadingElementInformationsPanel">
	<?php
	foreach ($zones as $zone) {
		echo $zone;
	}
	?>
</div>

<br />
<div id="headingElementInformationFormSaverDiv">
	<?php
	if ($rightsToSave) {
		_eTag ('button', array ('id' => 'headingSave', 'img' => 'img/tools/save.png', 'caption' => 'Sauvegarder', 'type' => 'button'));
		CopixHTMLHeader::addJSDOMReadyCode (" $ ('headingSave').addEvent ('click', function () { saveHeadingElementInformationForm () });");
	}
	?>
</div>

<div id="retour"></div>
</form>

<?php
$js = <<<JS
//create our Accordion instance
var myAccordion = new Accordion($('HeadingElementInformationsPanel'), 'a.toggler', 'div.element', {
	opacity: false,
	show : lastTab == null ? 0 : lastTab,
	onActive: function(toggler, element){
		lastOpenElement= element;
		lastTab = this.elements.indexOf(element) == 0 ? null : this.elements.indexOf(element);
		toggler.addClass ('togglerCollapse');
		toggler.getParent ().addClass ('togglerCollapse');
		if (element.get('rel') == 'menu'){
			showMenus ();
		}
		else {
			hideMenus ();
		}
	},
	onBackground: function(toggler, element){
		toggler.removeClass ('togglerCollapse');
		toggler.getParent ().removeClass ('togglerCollapse');
	},
	onComplete : function (elem){
		if (elem.getStyle('height') != "0px"){
			elem.setStyle('height','');
		}
		if(lastOpenElement){
			lastOpenElement.setStyle('height','');
		}
		refreshMenus ();
	}
});

//Affichage / masquage des infos sauvegarde / annulation
$('HeadingElementInformationsPanel').setStyle ('display', 'block');
var headingElementInformationFormObserver = new CopixFormObserver ('headingElementInformationForm', {
	onChanged: function (){
		$('headingElementInformationFormSaverDiv').setStyle ('display', 'block');
		refreshMenus ();
	},
	onChangeCanceled : function (){
		$('headingElementInformationFormSaverDiv').setStyle ('display', 'none');
		refreshMenus ();
	}
});

if (window.addEventListener) {
	window.addEventListener("resize", refreshMenus, false);
} else if (window.attachEvent) {
	window.attachEvent("onresize", refreshMenus);
}

arrowPosition ();
refreshMenus ();
JS;
CopixHTMLHeader::addJSDOMReadyCode ($js);
?>