/**
Shadows v0.3
Author: Patice Ferlet (metal3d@copix.org)
Licence: MIT

To use with IE, add the excanvas developed by google
*/

Element.extend({
	addShadow: function (width){
	  if (this.getTag()!="img") return false;
	  if (!width) width=10;
	  
	  var pix = this;
	  var w = pix.getStyle('width').toInt()+width;
	  var h = pix.getStyle('height').toInt()+width;
	  
	  var canvas = new Element('canvas').setStyles({
	  		'width' : w,
	  		'height' : h
	  }).setProperties({
	  		'width' : w,
	  		'height' : h
	  });
	  var ctx = canvas.getContext('2d');
	  
	  var lingrad = ctx.createLinearGradient(width,h-width,width,h);
	  lingrad.addColorStop(0, 'rgba(0,0,0,0.8)');
	  lingrad.addColorStop(0.3, 'rgba(0,0,0,0.5)');
	  lingrad.addColorStop(1, 'rgba(0,0,0,0)');
	  ctx.fillStyle = lingrad;
	  ctx.fillRect(width,h-width,w-(width*2),width);
	  
	  var lingrad2 = ctx.createLinearGradient(w-width,width,w,width);
	  lingrad2.addColorStop(0, 'rgba(0,0,0,0.8)');
	  lingrad2.addColorStop(0.3, 'rgba(0,0,0,0.5)');
	  lingrad2.addColorStop(1, 'rgba(0,0,0,0)');
	  ctx.fillStyle = lingrad2;
	  ctx.fillRect(w-width,width,width,h-(width*2));
	  
	  var radgrad = ctx.createRadialGradient(w-width,h-width,0,w-width,h-width,width);
	  radgrad.addColorStop(0, 'rgba(0,0,0,0.8)');
	  radgrad.addColorStop(0.3, 'rgba(0,0,0,0.5)');
	  radgrad.addColorStop(1, 'rgba(0,0,0,0)');
	  ctx.fillStyle = radgrad;
	  ctx.fillRect(w-width,h-width,w,h);
	  	  
	  var radgrad = ctx.createRadialGradient(w-width,width,0,w-width,width,width);
	  radgrad.addColorStop(0, 'rgba(0,0,0,0.8)');
	  radgrad.addColorStop(0.3, 'rgba(0,0,0,0.5)');
	  radgrad.addColorStop(1, 'rgba(0,0,0,0)');
	  ctx.fillStyle = radgrad;
	  ctx.fillRect(w-width,0,width,width);
	  
	  var radgrad = ctx.createRadialGradient(width,h-width,0,width,h-width,width);
	  radgrad.addColorStop(0, 'rgba(0,0,0,0.8)');
	  radgrad.addColorStop(0.3, 'rgba(0,0,0,0.5)');
	  radgrad.addColorStop(1, 'rgba(0,0,0,0)');
	  ctx.fillStyle = radgrad;
	  ctx.fillRect(0,h-width,width,width);
	  
	  var pix_cpy = new Image();
	  pix_cpy.src = pix.src;

	  //getStyle
	  canvas.setStyles({
	  	'float': pix.getStyle('float'),
	  	'margin': pix.getStyle('margin')
	  });
	  
	  canvas.id=pix.id;
	  canvas.setStyle('margin-bottom',pix.getStyle('margin-bottom').toInt()-width);
	  ctx.drawImage(pix,0,0,w-width,h-width);
	  canvas.cloneEvents(pix);
	  pix.replaceWith(canvas);
  }
});