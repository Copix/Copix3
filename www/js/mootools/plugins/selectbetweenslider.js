/**
 * SelectBetweenSlider
 */
var SelectBetweenSlider = new Class({

    initialize: function(el, options){
        this.setOptions(options);
        this.selects = $$(el);
        this.beginSelect = $$(el)[0];
        this.endSelect = $$(el)[1];

        this.createSlider();

        this.sliders = [{slider:this.beginMootoolsSlider, knob:this.beginKnob, divToolTip:this.beginDivToolTip, select:this.beginSelect, divTitle:this.beginDivTitle},	
        			{slider:this.endMootoolsSlider, knob:this.endKnob, divToolTip:this.endDivToolTip, select:this.endSelect, divTitle:this.endDivTitle}];

		if(this.options.showLegend){
			this.showLegend();
		}	
				
		if(this.options.wheel == null || this.options.wheel){
			this.slider.addEvent("mousewheel",this.scrolledElement.bindWithEvent(this));
		}	

		this.sliders.each(function(item, index){
		
			if(this.options.hideSelect){
				item.select.setStyle('display','none');
			}
			
			if(this.options.showValues == false){
				item.divTitle.setStyle('display','none');
			}
		
			//init
			item.divTitle.innerHTML = item.select.options[ item.select.selectedIndex].title;
			
			//events
			item.slider.drag.addEvent('onDrag', function(knob){
				if (this.options.snap){
					item.slider.set(this.snapStep);
				}
				if(item.slider == this.beginMootoolsSlider){
					this.checkBeginPosition(knob);
					this.checkBeginStep();
					this.displayBeginToolTip(true);
				}		
				else{
					this.checkEndPosition(knob);
					this.checkEndStep();
					this.displayEndToolTip(true);
				}
				this.displayDivBetween();
			}.bind (this));
			
			item.slider.drag.addEvent('onComplete', function(knob){
				if(item.slider == this.beginMootoolsSlider){
					this.checkBeginPosition(knob);
				}		
				else{
					this.checkEndPosition(knob);
				}
			}.bind (this));
					
			item.slider.addEvent ('onChange', function(step, refresh){
				if (item.select.options[step].disabled) {
					return false;
				}
				item.select.options[step].selected=true;
				item.divTitle.innerHTML = item.select.options[item.select.selectedIndex].title;
				//en mode refresh on ne fait pas d'appel ajax, on ne passe pas dans le change
				if (!refresh){
					item.select.fireEvent ('change');
				}
				if(item.slider == this.beginMootoolsSlider){
					this.checkBeginStep();
				}
				else{
					this.checkEndStep();
				}
			}.bind (this));
			
			item.slider.set = function (step, refresh){
				this.step = step.limit(0, this.options.steps);
				this.checkStep(refresh);
				this.end();
				this.fireEvent('onTick', this.toPosition(this.step));
				return this;
			}
			
			item.slider.checkStep = function (refresh){
				if (this.previousChange != this.step){
					this.previousChange = this.step;
					this.fireEvent('onChange', [this.step, refresh]);
				}
			}
			
			item.slider.addEvent ('onTick', function(step){
				if(item.slider == this.beginMootoolsSlider){
					this.displayBeginToolTip (true);
				}
				else{
					this.displayEndToolTip (true);
				}
			}.bind (this));

			item.select.addEvent ('refresh', function(){
				if(item.slider.step != item.select.selectedIndex){
					item.slider.set (item.select.selectedIndex, true);
					this.displayDivBetween();
				}
			}.bind(this));
			
			item.select.addEvent ('change', function(){
				if(item.slider.step != item.select.selectedIndex){	
					item.slider.set (item.select.selectedIndex);
					this.displayDivBetween();
				}
			}.bind(this));
			
			item.knob.addEvent ('click', function(){
				if(item.slider == this.beginMootoolsSlider){
					this.displayBeginToolTip (true);
				}
				else{
					this.displayEndToolTip (true);
				}									
			}.bind(this));
			
			item.knob.addEvent ('mouseover', function(){
				this.selectedKnob = item.knob;
				this.selectedSlider = item.slider;
			}.bind(this));
			
			item.slider.addEvent('onclick', function(step){
				if(Math.abs(step-this.beginMootoolsSlider.step) < Math.abs(step-this.endMootoolsSlider.step)){
					this.beginMootoolsSlider.fireEvent('onChange', step);
				}
				else{
					this.endMootoolsSlider.fireEvent('onChange', step);
				}
			}.bind(this));
			
		}.bind(this));

		this.slider.addEvent ('mouseover', function(e){
			this.mouseOver = true;
		}.bind(this));
		
		this.slider.addEvent ('mouseout', function(){
			this.mouseOver = false;
		}.bind(this));
		
		this.slider.addEvent ('mousemove', function(e){
			if(this.options.snap && this.mouseOver){
				var stepPosition = this.selectedSlider.toPosition(this.selectedSlider.step);
				var mousePosition = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft - (this.selectedKnob.getPosition().x - stepPosition) - (this.selectedKnob.getSize().size.x / 2);
				
				if (mousePosition < stepPosition && this.selectedSlider.step > 0){
					var middleStep = this.selectedSlider.toPosition(this.selectedSlider.step - 1) + ((stepPosition - this.selectedSlider.toPosition(this.selectedSlider.step - 1))/2);
					this.snapStep = (mousePosition < middleStep) ? this.selectedSlider.step - 1 : this.selectedSlider.step;			
				}
				else if (mousePosition > stepPosition && this.selectedSlider.step < this.selectedSlider.options.steps){
					var middleStep = stepPosition + ((this.selectedSlider.toPosition(this.selectedSlider.step + 1) - stepPosition)/2);
					this.snapStep = (mousePosition < middleStep) ? this.selectedSlider.step : this.selectedSlider.step +1;			
				}
				else{
					this.snapStep = this.selectedSlider.step;
				}
			}
		}.bind(this));
		
		this.displayDivBetween();
    },
    
    scrolledElement : function (event){
    	var step = this.beginMootoolsSlider.toStep(event.event.clientX);
		if(Math.abs(step-this.beginMootoolsSlider.step) < Math.abs(step-this.endMootoolsSlider.step)){
			step = (event.wheel<0) ? this.beginMootoolsSlider.step-1 : this.beginMootoolsSlider.step+1;
			if(step >= 0){
				this.beginMootoolsSlider.fireEvent('onChange', step);
			}
		}
		else{
			step = (event.wheel<0) ? this.endMootoolsSlider.step-1 : this.endMootoolsSlider.step+1;
			if(step <= this.endMootoolsSlider.options.steps){
				this.endMootoolsSlider.fireEvent('onChange', step);
			}
		}

		event.stop();
	},
      
    checkBeginPosition : function (knob) {
		if((knob.getPosition().x - this.slider.getPosition().x) > this.beginMootoolsSlider.toPosition(this.endMootoolsSlider.step-1)){
			this.beginMootoolsSlider.set(this.endMootoolsSlider.step-1);
		}
    },
    
    checkEndPosition : function (knob) {
		if(knob.getPosition().x < this.endMootoolsSlider.toPosition(this.beginMootoolsSlider.step+1)){
			this.endMootoolsSlider.set(this.beginMootoolsSlider.step+1);
		}
    },
    
    checkBeginStep : function () {
    	if (this.beginSelect.options[this.beginMootoolsSlider.step].disabled) {
    		var option = null,
    			options = this.beginSelect.options,
    			i = this.beginMootoolsSlider.step,
    			j = options.length;
    		while (i < j) {
    			i++;
    			option = options[i];
    			if (option && !option.disabled) {
		    		this.beginMootoolsSlider.set(i);
		    		break;
    			}
    		}
    		return;
    	}
		if(this.beginMootoolsSlider.step >= this.endMootoolsSlider.step){
			this.beginMootoolsSlider.set(this.endMootoolsSlider.step-1);
		}
    },
    
    checkEndStep : function () {
    	if (this.endSelect.options[this.endMootoolsSlider.step].disabled) {
    		var option = null,
    			options = this.endSelect.options,
    			i = options.length,
    			j = this.endMootoolsSlider.step;
    		while (i > j) {
    			i--;
    			option = options[i];
    			if (!option.disabled) {
		    		this.endMootoolsSlider.set(i);
		    		break;
    			}
    		}
    		return;
    	}
		if(this.beginMootoolsSlider.step >= this.endMootoolsSlider.step && this.sliders[1].select.options[this.endMootoolsSlider.step].disabled == false){
			this.endMootoolsSlider.set(this.beginMootoolsSlider.step+1);
		}
    },
    
    createSlider : function (){
    	this.slider = new Element('div', {'class' : 'slider'});
        this.slider_parent = new Element('div', {'class' : 'slider_parent'});
        this.beginSelect.parentNode.appendChild(this.slider_parent);
        this.slider.injectInside(this.slider_parent);
        this.beginDivTitle = new Element('div', {'id' : 'begintitle', 'class':'slider_title begin'});
        this.endDivTitle = new Element('div', {'id' : 'endtitle', 'class':'slider_title end'});
        this.beginSelect.parentNode.appendChild(this.beginDivTitle);
        this.beginSelect.parentNode.appendChild(this.endDivTitle);
        this.beginKnob = new Element('div', {'class' : 'knob', 'id':'beginknob'}).injectInside(this.slider);
        this.divBetween = new Element('div', {'class' : 'divBetween'}).injectInside(this.slider);
        this.endKnob = new Element('div', {'class' : 'knob', 'id':'endknob'}).injectInside(this.slider);
        if (this.options.alwaysShowTooltip) {
	        this.beginDivToolTip = new Element('div', {'class' : 'tool_tip begintool_tip'}).injectInside(this.slider_parent);
	        this.endDivToolTip = new Element('div', {'class' : 'tool_tip endtool_tip'}).injectInside(this.slider_parent);
       	}

		var SliderPerso = Slider.extend({
			clickedElement: function(event) {
   				var position = event.page[this.z] - this.getPos() - this.half;
				position = position.limit(-this.options.offset, this.max -this.options.offset);
				this.fireEvent('onclick', this.toStep(position));
			}
		});
		
		this.beginMootoolsSlider = new SliderPerso(this.slider, this.beginKnob, {
		    wheel : this.options.wheel,
		    range : this.beginSelect.options.length - 1,
		    steps : this.beginSelect.options.length - 1
		});
		this.beginKnob.setStyles ({
			'position':'absolute'
		});

		this.beginMootoolsSlider.set (this.beginSelect.selectedIndex);
		
		this.endMootoolsSlider = new SliderPerso(this.slider, this.endKnob, {
		    wheel : this.options.wheel,
		    range : this.endSelect.options.length - 1,
		    steps : this.endSelect.options.length - 1
		});
		this.endKnob.setStyles ({
			'position':'absolute'
		});
		
		for (var i=1 ; i < this.beginSelect.options.length -1 ; i++){
			var span = new Element('span', {'class' : 'span_tic'});
			if (this.options.useTextInTic) {
				span.setText(this.beginSelect.options[i].getText());
			}
			span.setStyles ({
				'left': Math.round(this.beginMootoolsSlider.toPosition(i) + (this.beginKnob.getSize().size.x)/2)+'px'
			});
			span.injectInside(this.slider);
		}
		
		this.selectedKnob = this.beginKnob;
		this.selectedSlider = this.beginMootoolsSlider;
		this.endMootoolsSlider.set (this.endSelect.selectedIndex);
		
		if (this.options.alwaysShowTooltip) {
	     	this.displayBeginToolTip (true);
			this.displayEndToolTip (true);
       	}
    },
    
    showLegend : function (){
    	var begin = new Element ('div', {'id' : 'begin', 'class':'begin_value'});
    	var end = new Element ('div', {'id' : 'end', 'class':'end_value'});
     	this.slider.parentNode.appendChild (begin);
        this.slider.parentNode.appendChild (end);
        begin.innerHTML = this.beginSelect.options[0].text;
		end.innerHTML = this.endSelect.options[this.endSelect.options.length-1].text;
    },

	getValue : function (element){
		//ie
		if(element.options[element.selectedIndex].text == '' ||
				element.options[element.selectedIndex].text == null){
			return element.options[element.selectedIndex].value;
		}
		return element.options[element.selectedIndex].text;
	},
	
	setValue : function (value){
		var i, option, j = this.select.options.length;
		for (i = 0; i < j; i++) {
			option = this.select.options[i];
			if (option.value != value) {
				option.selected = false;
			} else {
		    	option.selected=true;
		    	this.mootoolsSlider.set (i);
		    }
		}
	},
	
	displayDivBetween : function (){
		this.divBetween.setStyles({
			'left':(this.beginKnob.getPosition().x - this.slider.getPosition().x)+'px',
			'width':(this.endKnob.getPosition().x - this.beginKnob.getPosition().x)+'px'
		});
	},
	
	displayBeginToolTip : function (display){
		if (this.options.alwaysShowTooltip && display){
			this.beginDivToolTip.innerHTML = $(this.beginSelect.options[this.beginSelect.selectedIndex]).getText();
			//en 2 fois obligatoire pour connaitre la taille de la div
			this.beginDivToolTip.setStyles ({
				'display':'block',
				'position':'absolute'
			});
			//on a maintenant la taille
			var left = this.beginKnob.getPosition().x - (
					this.slider.getPosition().x
					+ (this.beginDivToolTip.getSize ().size.x / 2)
					- ( this.beginKnob.getSize().size.x / 2)
			);
			this.beginDivToolTip.setStyles ({
				'left' : left+'px'
			});	
		}
		else{
			this.beginDivToolTip.setStyles({
				'display':'none'
			});
		}
	},
	
	displayEndToolTip : function (display){
		if (this.options.alwaysShowTooltip && display){
			this.endDivToolTip.innerHTML = $(this.endSelect.options[this.endSelect.selectedIndex]).getText();
			//en 2 fois obligatoire pour connaitre la taille de la div
			this.endDivToolTip.setStyles ({
				'display':'block',
				'position':'absolute'
			});
			var left = this.endKnob.getPosition().x - (
					this.slider.getPosition().x
					+ (this.endDivToolTip.getSize ().size.x / 2)
					- ( this.endKnob.getSize().size.x / 2)
			);
			//on a maintenant la taille
			this.endDivToolTip.setStyles ({
				'left' : left+'px'
			});	
		}
		else{
			this.endDivToolTip.setStyles({
				'display':'none'
			});
		}
	}

});

SelectBetweenSlider.implement(new Options);