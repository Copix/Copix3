<?php
$pTypeElement = '';
	$optionsContent = '
        <span class="cmsBlueMenu"> Options d\'affichage</span>
        <div class="cmsBlueMenuOption">
            <label for="height_'.$identifiantFormulaire.'">'._i18n('Height').' : </label>'.
            _Tag ('inputtext', array ('id' => 'height_'.$identifiantFormulaire, 'value' => $options->getParam ('height', '170'), 'maxlength' => 4, 'size' => 4)).' px
            <br />
            <label for="width_'.$identifiantFormulaire.'">'._i18n('Width').' : </label>'.
            _Tag ('inputtext', array ('id' => 'width_'.$identifiantFormulaire, 'value' => $options->getParam ('width', '170'), 'maxlength' => 4, 'size' => 4)).' px
            <br />
            <label for="color_'.$identifiantFormulaire.'">'._i18n('Color of the tags').' : </label>'.
            _Tag ('inputtext', array ('id' => 'color_'.$identifiantFormulaire, 'value' => $options->getParam ('color', '000000'), 'maxlength' => 6, 'size' => 4)).'
            <br />
            <label for="rollover_'.$identifiantFormulaire.'">'._i18n('Color on rollover').' : </label>'.
            _Tag ('inputtext', array ('id' => 'rollover_'.$identifiantFormulaire, 'value' => $options->getParam ('rollover', '000000'), 'maxlength' => 6, 'size' => 4)).'
            <br />
            <label for="bgcolor_'.$identifiantFormulaire.'">'._i18n('Background color').' : </label>'.
            _Tag ('inputtext', array ('id' => 'bgcolor_'.$identifiantFormulaire, 'value' => $options->getParam ('bgcolor', 'ffffff'), 'maxlength' => 6, 'size' => 4)).'
            <br />
            <label for="transparent_'.$identifiantFormulaire.'">'._i18n('Use transparent mode').' : </label>
            <input type="checkbox" id="transparent_'.$identifiantFormulaire.'" name="transparent_'.$identifiantFormulaire.'" value="1"';
    if ($options->getParam('transparent', 0) == 1) {
        $optionsContent .= ' checked="checked"';
    }
    $optionsContent .= ' />
        <br />
        <label for="speed_'.$identifiantFormulaire.'">'._i18n('Rotation speed (between 25 and 500)').' : </label>'.
        _Tag ('inputtext', array ('id' => 'speed_'.$identifiantFormulaire, 'value' => $options->getParam ('speed', '100'), 'maxlength' => 100, 'size' => 4)).'
        <br />
    </div>';
?>
<div class="portletOptions">
<?php
	_etag ('popupinformation', array ('img'=>_resource ('img/tools/view_top_bottom.png'), 'text'=>"options d'affichage", 'handler'=>'clickdelay'), $optionsContent);
?>
</div>
<?php
CopixHTMLHeader::addJSDOMReadyCode ("
$('height_".$identifiantFormulaire."').addEvent('change', function(){updateNuage('".$identifiantFormulaire."', '".$portlet_id."', '"._request('editId')."');});
$('width_".$identifiantFormulaire."').addEvent('change', function(){updateNuage('".$identifiantFormulaire."', '".$portlet_id."', '"._request('editId')."');});
$('color_".$identifiantFormulaire."').addEvent('change', function(){updateNuage('".$identifiantFormulaire."', '".$portlet_id."', '"._request('editId')."');});
$('rollover_".$identifiantFormulaire."').addEvent('change', function(){updateNuage('".$identifiantFormulaire."', '".$portlet_id."', '"._request('editId')."');});
$('bgcolor_".$identifiantFormulaire."').addEvent('change', function(){updateNuage('".$identifiantFormulaire."', '".$portlet_id."', '"._request('editId')."');});
$('transparent_".$identifiantFormulaire."').addEvent('change', function(){updateNuage('".$identifiantFormulaire."', '".$portlet_id."', '"._request('editId')."');});
$('speed_".$identifiantFormulaire."').addEvent('change', function(){updateNuage('".$identifiantFormulaire."', '".$portlet_id."', '"._request('editId')."');});
");
?>