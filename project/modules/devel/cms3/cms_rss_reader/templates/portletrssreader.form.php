<?php
    CopixHTMLHeader::addJSLink (_resource ('cms_rss_reader|js/tools.js'));

    //initialisation des variables
    $position = 0;
    $urlBtnDelete = _resource ('img/tools/delete.png');
    $editId = _request ('editId');
?>
<!--<div class="error" id="error<?php echo $portlet->getRandomId ();?>"></div>-->
<form class="headForm" id="portlerForm<?php echo $portlet->getRandomId ();?>" method="post" action="<?php echo _url('cms_rss_reader|ajax|getFeed');?>">
    <input type="hidden" name="editId" value="<?php echo $editId;?>" />
    <input type="hidden" name="portletId" value="<?php echo $portlet->getRandomId ();?>" />
    <?php foreach($feeds as $feed) {
        if ($feed != '') {
            $identifiantFormulaire = $portlet->getRandomId ()."_pos_".$position;
    ?>
    <div id="feed_<?php echo $identifiantFormulaire;?>">
        <label for="url_feed_<?php echo $identifiantFormulaire;?>">Url du flux <?php echo $position+1;?> </label><input size="40" name="url_feed_<?php echo $portlet->getRandomId ();?>[]" id="url_feed_<?php echo $identifiantFormulaire;?>" type="text" value="<?php echo $feed;?>" /><input type="image" id="deleteFeed<?php echo $identifiantFormulaire;?>" width="14px" src="<?php echo $urlBtnDelete;?>" title="supprimer" alt="supprimer" onclick="deleteFeed('<?php echo $portlet->getRandomId ();?>', '<?php echo $editId;?>', 'feed_<?php echo $identifiantFormulaire;?>')"/>
    </div>
    <?php
            $position++;
        }
    }?>
</form>
<div id="addFeed_<?php echo $portlet->getRandomId ();?>">
	<input type="submit" value="ajouter un flux RSS" onclick="addFeed('<?php echo $portlet->getRandomId ();?>');"/>
	<input type="hidden" id="position_<?php echo $portlet->getRandomId ();?>" value="<?php echo $position; ?>"/>
</div>
<?php
CopixHTMLHeader::addJSCode(
<<<EOF
    var position{$portlet->getRandomId ()} = {$position};
    var urlDeleteBtn{$portlet->getRandomId ()} = '{$urlBtnDelete}';
    var editId{$portlet->getRandomId ()} = '{$editId}';
EOF
);
CopixHTMLHeader::addJSDOMReadyCode(
<<<EOF
    $$('input[id^=deleteFeed{$portlet->getRandomId ()}]').addEvent('click', function (e) {
        e.stop();
    });
	$$('input[id^=url_feed_{$portlet->getRandomId ()}]').addEvent('change', function(){
        updateFeed('{$portlet->getRandomId ()}', '{$editId}');
    });

    $('portlerForm{$portlet->getRandomId ()}').addEvent('submit', function (e) {
        //Prevents the default submit event from loading a new page.
        if(e) {
            e.stop();
        }
		this.set('send', {
            onComplete: function(response) {
                ajaxOff();
            }
            //,update : 'error{$portlet->getRandomId ()}'
        });
		//Send the form.
		this.send ();
	});
EOF
);