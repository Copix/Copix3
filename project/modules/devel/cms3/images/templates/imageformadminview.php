<div style="text-align:<?php echo array_key_exists('align_image', $options) ? $options['align_image'] : "center"; ?>">
	<?php
	_eTag("mootools", array("plugin"=>"mooresize"));
	
	
	foreach ($listeImage as $image){
	$imageSrcLite = _url('images|imagefront|GetImage', array('id_image'=>$image->id_helt, 'width'=>$options['thumb_width'], 'height'=>$options['thumb_height'], 'keepProportions'=>1, 'v'=>uniqid()));
	CopixHTMLHeader::addJSDOMReadyCode("
		//REDIMENSIONNEMENT DE L'IMAGE
		//----------------------------
		
		var resize$identifiantFormulaire = new MooResize('image$identifiantFormulaire$image->id_helt',{
			handleSize: 7,
			top : 0,
			marginLeft : $('image_$identifiantFormulaire').getCoordinates().left,
			marginTop : $('image_$identifiantFormulaire').getCoordinates().top,
			ratio : true,
			handleStyle: {
				background: '#FFB55F',
				border: '1px solid #000'
			},
			onStart: function(){
				document.id(this).setStyle('opacity',0.5);
			},
			onComplete: function(size){
				document.id(this).setStyle('opacity',1);
				$('thumb_enabled_yes_$identifiantFormulaire').checked = true;
				$('thumb_enabled_no_$identifiantFormulaire').checked = false;
				$('thumb_width_$identifiantFormulaire').set('value', size.x);
				$('thumb_height_$identifiantFormulaire').set('value', size.y);
				//on enregistre
				$('formOptionImageSubmit$identifiantFormulaire').fireEvent('click');
			},
			minSize: {
				x: 50,
				y: 50
			},
			/*maxSize: {
				x: 900,
				y: 900
			}*/
		});
		
		\$each(resize$identifiantFormulaire.handles,function(handle,key){						
			handle.el.setStyle('display', 'none');
		});
		
		//proportions
		$('thumb_keep_proportions_no$identifiantFormulaire').addEvent('click', function(){
			resize$identifiantFormulaire.setRatio (false);
			console.debug('onEnter');
		});
		$('thumb_keep_proportions_yes$identifiantFormulaire').addEvent('click', function(){
			resize$identifiantFormulaire.setRatio (true);
			console.debug('onEnter');
		});	
		
		$('image_$identifiantFormulaire').addEvents({
			mouseover : function(){
				\$each(resize$identifiantFormulaire.handles,function(handle,key){						
					handle.el.setStyle('display', '');
				});
			},
			mouseout :function(){
				\$each(resize$identifiantFormulaire.handles,function(handle,key){						
					handle.el.setStyle('display', 'none');
				});
			}
		});
		
		//DRAG & DROP POUR L'ALIGNEMENT DE L'IMAGE
		//----------------------------------------
		var dropFx = new Fx('background-color', {wait: false});
		 	 
		$('image$identifiantFormulaire$image->id_helt').addEvent('mousedown', function(e) {
			e = new Event(e).stop();
	 
			var clone = this.clone()
				.setStyles(this.getCoordinates()) 
				.setStyles({'position': 'absolute'})
				.addEvent('emptydrop', function() {
					this.remove();
					drop.removeEvents();
				}).inject(document.body);
	 		this.setStyle('opacity', 0.7);
	 
			var drag = clone.makeDraggable({
				onDrag: function (){
					\$each(resize$identifiantFormulaire.handles,function(handle,key){						
						handle.el.setStyle('display', 'none');
					});
					var imageCoordinates = $('image_$identifiantFormulaire').getCoordinates();
					//zone de changement d'alignement au 1/6e et 5/6e
					var leftPosition = imageCoordinates.width / 6 + imageCoordinates.left;
					var rightPosition = imageCoordinates.width * 5 / 6 + imageCoordinates.left;
					cloneLeftPosition = clone.getCoordinates().left;
					if (clone.getCoordinates().left < leftPosition){
						$('targetImage$identifiantFormulaire').getParent().setStyle('text-align','left');
						$('align_image_$identifiantFormulaire').value = 'left';
						$('image$identifiantFormulaire$image->id_helt').setStyle('margin', '10px 10px 10px 0');
					} else if (clone.getCoordinates().left + $('image$identifiantFormulaire$image->id_helt').getCoordinates().width > rightPosition){
						$('targetImage$identifiantFormulaire').getParent().setStyle('text-align','right');
						$('align_image_$identifiantFormulaire').value = 'right';
						$('image$identifiantFormulaire$image->id_helt').setStyle('margin', '10px 0 10px 10px');
					} else {
						$('targetImage$identifiantFormulaire').getParent().setStyle('text-align','center');
						$('align_image_$identifiantFormulaire').value = 'center';
						$('image$identifiantFormulaire$image->id_helt').setStyle('margin', '10px');
					}
				},
				onDrop: function(el,droppable) { 
					\$each(resize$identifiantFormulaire.handles,function(handle,key){						
						handle.el.setStyle('display', '');
					});
					clone.dispose();
					dropFx.start('7389AE').chain(dropFx.start.pass('ffffff', dropFx));
					$('image$identifiantFormulaire$image->id_helt').setStyle('opacity', 1);
				},
				onEnter: function(el,droppable) { 
					console.debug('onEnter');
				},
				onLeave: function(el,droppable) { 
					dropFx.start('ffffff');
				},
				onComplete : function (){
					var coord = $('image$identifiantFormulaire$image->id_helt').getCoordinates ();
					resize$identifiantFormulaire.elCoords = coord;
					resize$identifiantFormulaire.prepareCoordinates ();
					\$each(resize$identifiantFormulaire.handles,function(handle,key){						
						handle.setPosition(coord.width, coord.height);
					});
					//on enregistre
					$('formOptionImageSubmit$identifiantFormulaire').fireEvent('click');
					this.detach(); 
				}
			}); 
	 
			drag.start(e);
		});

	");
	
	if (array_key_exists('align_image', $options)){
		switch ($options['align_image']){
			case 'left' : $margin = "10px 10px 10px 0";
				break;
			case 'right' : $margin = "10px 0 10px 10px ";
				break;
			default : $margin = "10px";
		}
	} else {
		$margin = "10px";
	}
	?>
	<a id="targetImage<?php echo $identifiantFormulaire;?>">
		<img class="galleryImage" style="margin:<?php echo $margin; ?>;width:<?php echo $options['thumb_width'];?>px;height:<?php echo $options['thumb_height'];?>px" id="image<?php echo $identifiantFormulaire.$image->id_helt;?>" alt="<?php echo $image->caption_hei; ?>" src="<?php echo $imageSrcLite; ?>" title="<?php echo $image->caption_hei; ?>" />
	</a>
	<div class="imageLegend" id="legendImage<?php echo $identifiantFormulaire;?>" style="margin-top: -10px;"><?php echo array_key_exists('legend_image', $options) ? $options['legend_image'] : ""; ?></div>
	<?php } ?>
</div>