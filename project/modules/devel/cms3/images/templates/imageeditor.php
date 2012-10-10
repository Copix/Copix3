<div id="divImageEditor" style="position: relative;">
	<form id="formImageEditor" method="post">
		<input type="hidden" name="submitImageEditor" value="1" />
		<input type="hidden" id="moveleft" name="moveleft" value="0" />
		<input type="hidden" id="moveright" name="moveright" value="0" />
		<input type="hidden" id="x" name="x" value="0" />
		<input type="hidden" id="y" name="y" value="0" />
		<input type="hidden" id="width" name="width" value="0" />
		<input type="hidden" id="height" name="height" value="0" />
		<input type="hidden" id="doblackwhite" name="doblackwhite" value="0" />
	</form>
	<?php
	_eTag("mootools", array('plugins'=>array('ysr-crop', 'mooresize')));
	$extension = pathinfo($ppo->editedElement->file_image, PATHINFO_EXTENSION);	
	$dimension =  getimagesize($ppo->path.$ppo->editedElement->file_image);	
	
	CopixHTMLHeader::addJSDOMReadyCode("
		//REDIMENSIONNEMENT DE L'IMAGE
		//----------------------------
		
		var resize = new MooResize('image',{
			handleSize: 7,
			top : 0,
			marginLeft : $('image').getCoordinates().left,
			marginTop : $('image').getCoordinates().top,
			ratio : true,
			corner:true,
			handleStyle: {
				background: '#FFB55F',
				border: '1px solid #000'
			},
			onStart: function(){
				document.id(this).setStyle('opacity',0.5);
			},
			onComplete: function(size){
				$('width').set('value', size.x);
				$('height').set('value', size.y);
				//on enregistre
				$('formImageEditor').submit();
			},
			minSize: {
				x: 50,
				y: 50
			}/*,
			maxSize: {
				x: 900,
				y: 900
			}*/
		});
		
		\$each(resize.handles,function(handle,key){						
			handle.el.setStyle('display', 'none');
		});
		
		
	");
	?>
	<div id="imgouter" style="display: none;">
		<div id="cropframe" class="cropframe" style="background-image: url('<?php echo $ppo->src; ?>')">
			<div id="draghandle" class="cropdraghandle"></div>
			<div id="resizeHandleXY" class="cropResizeHandle"></div>
			<div id="cropinfo" class="cropinfo">
				<div title="Cliquer pour rogner" id="cropbtn" class="cropbtn"></div>
				<div id="cropdims" class="cropdims"></div>
			</div>
		</div>
		
		<div id="imglayer" class="imglayer" style="width: <?php echo $dimension[0]; ?>px; height: <?php echo $dimension[1]; ?>px; background-position: center center; background-image: url('<?php echo $ppo->src; ?>')"></div>
	</div>
	
	
	
	<img id="image" style="width: <?php echo $dimension[0]; ?>px; height: <?php echo $dimension[1]; ?>px;" src="<?php echo $ppo->src; ?>" alt="(Image non trouvée)" />
	<a href="<?php echo $ppo->src.'&extension=.'.$extension; ?>" class="smoothbox" >
		
	</a>
	<div id="imageeditorbar">
		<ul>
			<li><a id='size' href="javascript:;" title="Redimensionner"><img src="<?php echo _resource("images|img/imageeditor/resize.png"); ?>" alt="Redimensionner" /></a></li>
			<li><a id='crop' href="javascript:;" title="Rogner"><img src="<?php echo _resource("images|img/imageeditor/crop.png"); ?>" alt="Rogner" /></a></li>
			<li><a id='left' href="javascript:;" title="Rotation gauche"><img src="<?php echo _resource("images|img/imageeditor/rotate_left.png"); ?>" alt="Rotation gauche" /></a></li>
			<li><a id='right' href="javascript:;" title="Rotation droite"><img src="<?php echo _resource("images|img/imageeditor/rotate_right.png"); ?>" alt="Rotation droite" /></a></li>
			<li><a id='contrast' href="javascript:;" title="Noir et blanc"><img src="<?php echo _resource("images|img/imageeditor/contrast.png"); ?>" alt="Noir et blanc" /></a></li>
		</ul>
	</div>
</div>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
	var ch = null;
	$('size').addEvent('click', function(){
		toggleCurrent($('size'));
		$('imgouter').setStyle('display', 'none');
		$('image').setStyle('display', '');
		var coord = $('image').getCoordinates ();
		resize.elCoords = coord;
		resize.prepareCoordinates ();
		\$each(resize.handles,function(handle,key){						
			handle.setPosition(coord.width, coord.height);
		});
		\$each(resize.handles,function(handle,key){						
			handle.el.setStyle('display', '');
		});
	});
	
	$('crop').addEvent('click', function(){
		toggleCurrent($('crop'));
		\$each(resize.handles,function(handle,key){						
			handle.el.setStyle('display', 'none');
		});
		$('image').setStyle('display', 'none');
		$('imgouter').setStyle('display', '');
		if (ch == null){
			ch = new CwCrop({
				minsize: {x: 100, y: 60},
				maxratio: {x: 0, y: 0},
				maxsize: {x: $dimension[0], y:$dimension[1]},					
				onCrop: function(values) {
					$('width').set('value', values.w);
					$('height').set('value', values.h);
					$('x').set('value', values.x);
					$('y').set('value', values.y);
					$('formImageEditor').submit();
			    }
			});
		}
	});
	
	$('left').addEvent('click', function(){
		toggleCurrent($('left'));
		\$each(resize.handles,function(handle,key){						
			handle.el.setStyle('display', 'none');
		});
		$('imgouter').setStyle('display', 'none');
		$('image').setStyle('display', '');
		$('moveleft').set('value', 1);
		$('formImageEditor').submit();
	});
	
	$('right').addEvent('click', function(){
		toggleCurrent($('right'));
		\$each(resize.handles,function(handle,key){						
			handle.el.setStyle('display', 'none');
		});
		$('imgouter').setStyle('display', 'none');
		$('image').setStyle('display', '');
		$('moveright').set('value', 1);
		$('formImageEditor').submit();
	});
	
	$('contrast').addEvent('click', function(){
		toggleCurrent($('contrast'));
		\$each(resize.handles,function(handle,key){						
			handle.el.setStyle('display', 'none');
		});
		$('imgouter').setStyle('display', 'none');
		$('image').setStyle('display', '');
		$('doblackwhite').set('value', 1);
		$('formImageEditor').submit();
	});
	
	parent.resizeFrameHeight($dimension[1]+60);
	
	function toggleCurrent(element){
		$('imageeditorbar').getElements('a').each(function(el){
			el.removeClass('current');
		});
		element.addClass('current');
	}
");
?>