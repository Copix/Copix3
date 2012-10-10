<?php
$popupInformation = _tag('popupinformation', array(), 'Séparer chaque adresse par une <strong>virgule</strong>');
$aRequestParams = CopixRequest::asArray();
unset($aRequestParams['prevaction']);
$aSelected = _request('selected');
$aSelected2 = array();
$headingElementType = new HeadingElementType ();
$aElementTitle = array ();
foreach ($aSelected as $selected) {
	// $pIdHelt|$pTypeHei
	$aTemp = explode ('|', $selected);
    $element = _class('heading|headingelementinformationservices')->getById($aTemp[0], $aTemp[1]);
	$typeInformations = $headingElementType->getInformations ($element->type_hei);
	if (CopixUserPreferences::get($typeInformations['module'].'|'.$element->type_hei.'Notification') == '1') {
		$aElementTitle[] = $element->caption_hei;
		$aSelected2[] = $selected;
	}
}
$aRequestParams['selected'] = $aSelected2;
unset($aSelected2);
$aErrors = _request ('error', array());
if(sizeof ($aErrors) > 0) {
    $aMessage = CopixSession::get ('heading|email|notify');
    $email = $aMessage['dest'];
?>
<ul class="error">
    <?php foreach ($aErrors as $error) {?>
    <li><?php echo $error;?></li>
    <?php }?>
</ul>
<?php
}
?>
<form id="headingElementNotificationForm" name="headingElementNotificationForm" action="<?php echo _url ('element|notify', $aRequestParams);?>" method="post">
    <br />
    <label for="emailNotification">Notifier par email que
    <?php if (sizeof ($aElementTitle) > 1) {?>
        les éléments suivants ont étés publiés <?php echo $popupInformation;?> :
    <ul>
        <li><?php echo implode('</li><li>', $aElementTitle);?></li></ul>
    <!--les éléments "<?php echo implode ('", "', $aElementTitle);?>" ont été publiés-->
    <?php }else {?>
    l'élément "<?php echo $aElementTitle[0];?>" a été publié
    <?php echo $popupInformation?> :
    <?php }?>
    </label><br />
    <?php _eTag('inputtext', array('id' => 'emailNotification', 'size' =>70, 'value' => isset($email) ? _tag('escape', $email) : ''));?>
    <br />
    <p class="center">
        <input type="submit" value="<?php echo _i18n('copix:common.buttons.send');?>" />
        <input type="button" onclick="javascript:$('<?php echo $windowId;?>').fireEvent('close');" value="<?php echo _i18n('copix:common.buttons.cancel');?>" />
    </p>
</form>