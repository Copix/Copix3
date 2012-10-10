<div id="portletColumn<?php echo $portlet->getRandomId();?>">
	<br />
	<div id="portletColumnLeft<?php echo $portlet->getRandomId();?>" class="portletColumnUpdate" style="width:<?php echo $portlet->getOption("leftColumn", "50") - 1;?>%;"><?php echo ${PortletColumns::LEFT_COLUMN}; ?></div>
	<div id="portletColumnSplitter<?php echo $portlet->getRandomId();?>" class="portletColumnSplitter">&nbsp;</div>
	<div id="portletColumnRight<?php echo $portlet->getRandomId();?>" class="portletColumnUpdate" style="width:<?php echo $portlet->getOption("rightColumn", "49");?>%;"><?php echo ${PortletColumns::RIGHT_COLUMN}; ?></div>
	<div style="clear:both;"></div>
</div>

<?php 
CopixHTMLHeader::addJSDOMReadyCode("
		
		//resizable splitter
		$('portletColumnLeft".$portlet->getRandomId()."').makeResizable({
	    	handle: $('portletColumnSplitter".$portlet->getRandomId()."'),
	    	modifiers:{x: 'width', y:false}, 
	    	limit: {x: [0, $('portletColumn".$portlet->getRandomId()."').getCoordinates ().width-15]},
		    onComplete: function(el){
				ajaxOn();
				var request = new Request.HTML({
					url : Copix.getActionURL('portal|ajax|updateColumns'),
					onComplete : function (){
						ajaxOff();
					}
				}).post({'editId' : '"._request('editId')."',
					'portletId' : '".$portlet->getRandomId()."',
					'leftColumn' : $('portletColumnLeft".$portlet->getRandomId()."').getCoordinates ().width * 100 / $('portletColumn".$portlet->getRandomId()."').getCoordinates ().width,
					'rightColumn' : $('portletColumnRight".$portlet->getRandomId()."').getCoordinates ().width * 100 / $('portletColumn".$portlet->getRandomId()."').getCoordinates ().width
				});
		    }    	
		});
		$('portletColumnRight".$portlet->getRandomId()."').makeResizable({
	    	handle: $('portletColumnSplitter".$portlet->getRandomId()."'),
	    	modifiers:{x: 'width', y:false}, 
	    	invert : true,
	    	limit: {x: [0, $('portletColumn".$portlet->getRandomId()."').getCoordinates ().width-15]},
		});
		var maxHeight = $('portletColumnRight".$portlet->getRandomId()."').getCoordinates().height > $('portletColumnLeft".$portlet->getRandomId()."').getCoordinates().height ? $('portletColumnRight".$portlet->getRandomId()."').getCoordinates().height : $('portletColumnLeft".$portlet->getRandomId()."').getCoordinates().height;
		maxHeight -= 2; //border
		$('portletColumnSplitter".$portlet->getRandomId()."').setStyle ('min-height', maxHeight);
		$('portletColumnRight".$portlet->getRandomId()."').setStyle ('min-height', maxHeight);
		$('portletColumnLeft".$portlet->getRandomId()."').setStyle ('min-height', maxHeight);
");
?>