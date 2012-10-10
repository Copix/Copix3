<?php
$editId = _request('editId');
$portletRandomId = $portlet->getRandomId ();
$updateUrl = _url ('portal|ajax|updateOptions');
?>
<div class="portletOptions">
    <label for="portletTitle<?php echo $portletRandomId;?>">Titre de la portlet (facultatif) : </label>
    <input type="text" id="portletTitle<?php echo $portlet->getRandomId ();?>" name="portletTitle<?php echo $portletRandomId;?>" size="30" value="<?php echo $portlet->getOption ('title');?>" />
    <label for="portletNbItem<?php echo $portletRandomId;?>">Nb d'éléments à afficher : </label>
    &nbsp;&nbsp;
    <input type="text" id="portletNbItem<?php echo $portlet->getRandomId ();?>" name="portletNbItem<?php echo $portletRandomId;?>" size="2" value="<?php echo $portlet->getOption ('nb_item', 4);?>" />
</div>
<?php
CopixHTMLHeader::addJSCode (
<<<EOF
function updateTitlePortlet{$portletRandomId} () {
	var myHTMLRequest = new Request.HTML({
        url:'{$updateUrl}'
	}).post({
        'title':$('portletTitle{$portletRandomId}').value,
        'nb_item': (isNaN($('portletNbItem{$portletRandomId}').value) ? 4 : $('portletNbItem{$portletRandomId}').value),
		'portletId' : '{$portletRandomId}',
		'editId' : '{$editId}'
    });
}
EOF
);

CopixHTMLHeader::addJSDOMReadyCode (
<<<EOF
$('portletTitle{$portletRandomId}').addEvent('change', function() {
    updateTitlePortlet{$portletRandomId} ();
    if(typeof updateToolBar =='function'){
        updateToolBar('{$portletRandomId}', '{$editId}');
    }
});
$('portletNbItem{$portletRandomId}').addEvent('change', function() {
    updateTitlePortlet{$portletRandomId} ();
    if(typeof updateToolBar =='function'){
        updateToolBar('{$portletRandomId}', '{$editId}');
    }
});

EOF
);
?>