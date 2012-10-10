/**
 * SelectSlider
 * Transforme un select en slider
 * Mootools 1.11
 */
var SelectSlider = new Class({

    initialize: function(el, options){
    
        this.setOptions(options);
        this.select = $(el);

        this.createSlider();
        
		if(this.options.wheel == null || this.options.wheel){
			this.slider.addEvent("mousewheel",this.scrolledElement.bindWithEvent(this));
		}
		if(this.options.hideSelect){
			this.select.setStyle('display','none');
		}	

		if(this.options.showTitle){
			this.divTitle.setText( (this.select.options[this.select.selectedIndex].title) ? this.select.options[this.select.selectedIndex].title : this.select.options[this.select.selectedIndex].text );
		}

		//init
		this.mootoolsSlider.set (this.select.selectedIndex);	
		
		//events
		this.mootoolsSlider.drag.addEvent('onDrag', function(position){
			if (this.options.snap){	
				this.mootoolsSlider.set(this.snapStep);
			}
			this.displayToolTip(true);
		}.bind (this));

		this.mootoolsSlider.addEvent ('onChange', function(step){	
			if (this.options.snap){	// apres un clic			
				this.mootoolsSlider.set(step);
				this.select.fireEvent ('change');
			}		
			this.select.options[step].selected=true;
			if(this.options.showTitle){
				this.divTitle.setText( (this.select.options[this.select.selectedIndex].title) ? this.select.options[this.select.selectedIndex].title : this.select.options[this.select.selectedIndex].text );
			}
			this.select.fireEvent ('change');
		}.bind (this));
		
		this.mootoolsSlider.addEvent ('onTick', function(step){	
			this.displayToolTip (true);
		}.bind (this));
		
		this.select.addEvent ('change', function(){
			if(this.mootoolsSlider.step != this.select.selectedIndex){	
				this.mootoolsSlider.set (this.select.selectedIndex);
			}
		}.bind(this));
		
		this.slider.addEvent ('mouseover', function(e){
			this.mouseOver = true;
		}.bind(this));
		
		this.slider.addEvent ('mousemove', function(e){
			if(this.options.snap && this.mouseOver){
				var stepPosition = this.mootoolsSlider.toPosition(this.mootoolsSlider.step);
				var mousePosition = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft - (this.knob.getPosition().x - stepPosition) - (this.knob.getSize().size.x / 2);
				
				if (mousePosition < stepPosition && this.mootoolsSlider.step > 0){
					var middleStep = this.mootoolsSlider.toPosition(this.mootoolsSlider.step - 1) + ((stepPosition - this.mootoolsSlider.toPosition(this.mootoolsSlider.step - 1))/2);
					this.snapStep = (mousePosition < middleStep) ? this.mootoolsSlider.step - 1 : this.mootoolsSlider.step;			
				}
				else if (mousePosition > stepPosition && this.mootoolsSlider.step < this.mootoolsSlider.options.steps){
					var middleStep = stepPosition + ((this.mootoolsSlider.toPosition(this.mootoolsSlider.step + 1) - stepPosition)/2);
					this.snapStep = (mousePosition < middleStep) ? this.mootoolsSlider.step : this.mootoolsSlider.step +1;			
				}
				else{
					this.snapStep = this.mootoolsSlider.step;
				}
			}
		}.bind(this));
		
		this.slider.addEvent ('mouseout', function(){
			this.mouseOver = false;
		}.bind(this));
		
		document.onkeydown = function (e){
		e = new Event( e );
			if (this.mouseOver){		
				if (e.key == 'left' && (this.mootoolsSlider.step - 1) >= 0){
					this.mootoolsSlider.fireEvent ('onChange',this.mootoolsSlider.step - 1);
					e.preventDefault();
				} else if(e.key == 'right' & this.mootoolsSlider.step < (this.select.options.length - 1)){					
					this.mootoolsSlider.fireEvent ('onChange',this.mootoolsSlider.step + 1);
					e.preventDefault();
				}
			}
		}.bind(this);
		
		this.knob.addEvent ('focus', function(){
			this.displayToolTip (true);
		}.bind(this));

		this.knob.addEvent ('blur', function(){
			this.displayToolTip (false);
		}.bind(this));
		
        this.displayToolTip(true);
    },
    
    createSlider : function (){
    	this.slider = new Element('div', {'class' : 'slider'});
        this.slider_parent = new Element('div', {'class' : 'slider_parent'});
        this.slider.injectInside(this.slider_parent);


        
        this.knob = new Element('a', {'class' : 'knob', 'href': 'javascript:void(0)' }).injectInside(this.slider);        
        this.divToolTip = new Element('div', {'class' : 'tool_tip'}).injectInside(this.slider_parent);
        
		this.select.parentNode.appendChild(this.slider_parent);
		
		if(this.options.showTitle){
	        this.divTitle = new Element('div', {'class':'slider_title'});
	        this.divTitle.setText( (this.select.options[this.select.selectedIndex].title) ? this.select.options[this.select.selectedIndex].title : this.select.options[this.select.selectedIndex].text );
	        this.select.parentNode.appendChild(this.divTitle);
        }
          
        var snap = this.options.snap;
        var SliderSelect = Slider.extend({
			clickedElement: function(event) {
				var position = event.page[this.z] - this.getPos() - this.half;
				position = position.limit(-this.options.offset, this.max -this.options.offset);
				this.step = this.toStep(position);
				this.checkStep();
				this.end();
				if(!snap){
					this.fireEvent('onTick', position);
				}
			}
		});
        
		this.mootoolsSlider = new SliderSelect(this.slider, this.knob, {
		    wheel : this.options.wheel,
		    range : this.select.options.length - 1,
		    steps : this.select.options.length - 1
		});
		
		this.knob.setStyles({'position':'absolute'});
		
		
		if(this.options.showLegend){
			var begin = new Element ('div', {'class':'begin_value'});
	    	var end = new Element ('div', {'class':'end_value'});
	        begin.innerHTML = this.select.options[0].value;
			end.innerHTML = this.select.options[this.select.options.length-1].value;
	     	this.slider.appendChild(begin);
	        this.slider.appendChild(end);
		}
		
		if(this.options.showTic || this.options.showTic == null){
			for (i=1 ; i < this.select.options.length -1 ; i++){
				var span = new Element('span', {'class' : 'span_tic'}).injectInside(this.slider);
				if( this.options.useTextInTic ){
					span.setText( this.select.options[i].text );
				}
				span.setStyles ({
					'left': (this.mootoolsSlider.toPosition(i) + (this.knob.getSize().size.x)/2)+'px'
				});
			}
		}
    },

	getValue : function (){
		//ie
		if(this.select.options[this.select.selectedIndex].value == '' ||
				this.select.options[this.select.selectedIndex].value == null){
			return this.select.options[this.select.selectedIndex].text;
		}
		return this.select.options[this.select.selectedIndex].value;
	},
	
	setValue : function (value){
		for( i = 0 ; i < this.select.options.length ; i++){
			var option = this.select.options[i];		
			if (option.value != value){
				option.selected=false;
			} else {
		    	option.selected=true;
		    	this.mootoolsSlider.set (i);
		    }
		}
	},
	
	scrolledElement : function (event){
		var condition = (this.mootoolsSlider.options.mode=="horizontal") ? (event.wheel<0) : (event.wheel>0);
		var step = condition ? this.mootoolsSlider.step-1 : this.mootoolsSlider.step+1;
		this.mootoolsSlider.set (step);
		event.stop();
	},
	
	displayToolTip : function (display){
		if (display){
			if(this.options.showTitle){
				this.divTitle.setText( (this.select.options[this.select.selectedIndex].title) ? this.select.options[this.select.selectedIndex].title : this.select.options[this.select.selectedIndex].text );
			}
			this.divToolTip.setText( this.select.options[this.select.selectedIndex].text );
			//obligatoire pour le placement initial
			this.divToolTip.setStyles ({
				'display':'block'
			});
			left = this.knob.getPosition().x - (
					this.slider.getPosition().x
					+ (this.divToolTip.getSize ().size.x / 2)
					- ( this.knob.getSize().size.x / 2)
			);
			this.divToolTip.setStyles ({
				'left' : left +'px'
			});
		} else{
			if( !this.options.alwaysShowTooltip ){
				this.divToolTip.setStyles({
					'display':'none'
				});
			}
		}
	}

});

SelectSlider.implement(new Options);

/********************************************************/
Element.extend({
	slider: null,
	
	makeSlider : function (options){
		this.slider = new SelectSlider(this,options);
		return this.slider;
	}

});