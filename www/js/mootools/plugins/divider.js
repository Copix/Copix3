/**
* Class Divider v0.3 beta 2
* Authors: Patrice Ferlet (Metal3d) metal3d at copix.org
           Harald Kirschner [url=http://www.digitarald.de/]http://www.digitarald.de/[/url]
* Licence: MIT
* Usage: $('element).divide(options)
*   options is an object with 
    'cols'-> columns number (needed), 
    'border' -> border separations as CSS (1px solid red...), 
    'padding' -> padding beetween column, 
    'correct' -> correction value (0.1=10%) is the acceptable error size we can have for 2 columns
    'order' -> first (default) or last, modify algorithm
*/ 

Element.extend({
    divide: function (options,onlyresize){
   	   options = $merge({
        	cols: 2,
        	border: 0,
        	padding: 0,
        	correct: 0,
        	order: 'first',
        	firstclass: null,
        	nextclass : null
        }, options || {});

		 if(!onlyresize){
		 	//console.log('colone')
	        // get initial size and new width of element to divide
	        var element = this.clone().injectAfter(this);
	 
	        var size = this.getSize();
	        var percent = (100 / options.cols).toInt() - 2;
	 
	        var w = size.size.x / options.cols - (options.padding * (options.cols - 1)).toInt();
	 
	        if (options.border){
	            options.padding /= 2;
	            options.padding = options.padding.toInt();
	        }
	        //we create the first column width good width
	        element.setStyle('width', percent + "%");
	 
	        //reinit size now
	        size = element.getSize();
	        if (options.correct){
	            options.correct = size.size.y * (options.correct.toInt()/100);
	        }
	 
	        //the best height for columns
	        var bestH = size.size.y/options.cols;
	 
	        var div = new Element('div').injectAfter(element.id);
	        var column = new Element('div').injectInside(div).setStyles({
	            'width': percent+"%",//w+"px",
	            'float': 'left'
	        });
	 
	        if(options.border){
	            column.setStyle('border-right',options.border);
	            column.setStyle('padding-right',options.padding);
	        }
	       
	        if(options.firstclass){
	          	column.setProperty('class',options.firstclass);
	        }

 
	        var i = 0
	        element.getChildren().each(function (el){
	            if(options.order=="first"){
	                el.injectInside(column);
	            }
	 
	            if(column.getSize().size.y>bestH && i<options.cols-1){
	                column = new Element('div').setStyles({
	                    'width': percent+"%",//w+"px",
	                    'float': 'left',
	                    'margin-left': options.padding
	                });
	                if(options.nextclass){
	                	column.setProperty('class',options.nextclass);
	                }
	                if(options.border && i<options.cols-2){
	                    column.setStyle('border-right',options.border);
	                    column.setStyle('padding-right',options.padding);
	                }
	                column.injectInside(div);
	                i++;
	            }
	            if(options.order=="last"){
	                el.injectInside(column);
	            }
	 
	        });
	 
	        //this.empty();
	        this.setHTML(div.innerHTML);
	 
	        //remove temporary elements
	        div.remove();
	        element.remove();
 		}        
 		else{
		 	//console.log('resize')
	 		var size = this.getSize();
	 		var bestH = size.size.y/options.cols;
 		}
        //and now, we refine sizes
        var acceptableError = bestH * options.correct; // ~ 10% 
        var cols = Array();
        var diff=0;
        for (j=0;j<options.cols;j++){
            var maxh = 0;
            cols = this.getChildren();
            cols.each(function (el,i){
                if(maxh<el.getSize().size.y){
                    maxh=el.getSize().size.y
                }
                //el = new Element(el);
                /*if(i<cols.length-1){
                    diff = el.getSize().size.y.toInt() - cols[i+1].getSize().size.y.toInt()
                    while((diff>0  && diff>acceptableError) || (diff<0 && diff>acceptableError)){
                        var children = el.getChildren()
                        if(window.ie){
                             children = el.childNodes
                             cols[i+1].insertBefore(children[children.length-1],cols[i+1].firstChild);
                        }else{
                             children[children.length-1].injectBefore(cols[i+1].getChildren()[0]);
                        }
                        diff = el.getSize().size.y.toInt() - cols[i+1].getSize().size.y.toInt()
                    }
                }*/
            });
        }
        this.setStyle('height',maxh);
    }
});