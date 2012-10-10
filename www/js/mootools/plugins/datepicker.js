/********************************************************/
// Author: Rick Hopkins
// Modify by Estelle Fersing
// Web: http://www.rickhopkins.com/
// Requirements: Moo.js + Native Scripts + Dom.js (http://www.mootools.net/)
/********************************************************/
// create the object
var DatePicker = new Class({
	/*********************************************/
	// function initialize() will set and create the date picker text box
	initialize: function(rsID, options){
		// set some variables
		this.inputName = rsID;
		this.id = rsID + '_cal';
		this.containerID = this.id + '_container';
		this.input = options.input || false;
		this.image = false;
		this.calendar = false;
		this.firstClick = true;
		this.calImageURL = options.imageCalendar || '';
		this.format = options.format || 'dd/mm/yyyy';
		this.value = options.value || '';
		this.language = options.language || 'fr';
		this.sizeday = options.sizeday || 3;
		this.classe = options.classe || 'calendar';
		//myCopix = new CopixClass ();
		this.closeButtonSrc = options.closebuttonsrc || null;
		this.title = options.title || null;
		this.draggable = options.draggable;
		this.modeCalendar = options.modecalendar || false; 
		// pour avoir la valeur 0 en option
		if((options.duration).empty) {
			this.duration =  1000;
		}
		else {
			this.duration = options.duration;
		}
		if((options.afteryear).empty) {
			this.afteryear =  10;
		}
		else {
			this.afteryear = options.afteryear;
		}
		if((options.beforeyear).empty) {
			this.beforeyear =  10;
		}
		else {
			this.beforeyear = options.beforeyear;
		}
		
		this.monthNames = ['fr', 'en'];
		this.monthNames['fr'] = ['Janvier', 'F&eacute;vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao&ucirc;t', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre'];
		this.monthNames['en'] = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		this.daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		this.dayNames = ['fr', 'en'];
		this.dayNames['fr'] = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
		this.dayNames['en'] = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		this.interval = null;
		this.active = false;
		
		// create the parent container
		//document.write('<div class="calContainer" id="' + this.containerID + '"></div>');
		this.calendarContainer = new Element('div', {'class':'calContainer','id':this.containerID});
		
		// set some actions for the container div
		this.calendarContainer.addEvent('mouseover', function(){
			$clear(this.interval);
			}.bind(this));
		this.calendarContainer.addEvent('mouseleave' , function(){			
			/*
			// suppression de la fermeture automatique de la fen�tre quand la souris sort
			// sinon on ferme la fen�tre en cliquant sur un select de cette fen�tre (...)
			this.interval = setInterval(
				function(){
						if (this.calendar && !this.active){ 
								this.calendar.setStyles({'display':'none'});
								this.posLeft = "";
								this.posTop = "";
								this.year = "";
								this.month = "";
								this.firstClick = true;
								this.calendar.drag = false
						}
					}.bind(this), 
				500);*/
			}.bind(this));

		// create the text box
		if (!options.input){
			this.input = new Element('input');
			this.input.setProperties({'type': 'text', 'name': this.inputName, 'value': this.value, 'readOnly': true});
			
		}
		
		this.calendarContainer.injectBefore(this.input);
		this.input.injectInside(this.calendarContainer);
		// create the image only if we have a path
		if (this.calImageURL) {
			this.image = new Element('img');
			this.image.setProperties({'src': this.calImageURL});
		
			//this.image.injectAfter(this.input);
			this.image.injectInside(this.calendarContainer);
			// create the onclick action
	
			this.image.addEvent('click',function(event) {
					this.createCal(event);
				}.bindWithEvent(this)
			);
		} else {
			this.createCal (null);
			this.active = false;
			this.calendar.drag=false
		}
		
		}, 
	
	/*********************************************/
	// function createCal() will create the calendar and allow the user to select a date
	createCal: function(event){
		if(this.value){
			var beginYear = this.format.search(/yyyy/);
			var beginMonth = this.format.search(/mm/);
			var beginDay = this.format.search(/dd/);
			year = this.value.substr(beginYear, 4);
			month = this.value.substr(beginMonth, 2) - 1;
			valueday = this.value.substr(beginDay, 2);
			//today
			var dtValue = new Date();
			dtValue.setFullYear(year, month);
			dtValue.setDate(valueday);
		}
		
		if(this.month){
			var month = this.month
		}
		if(this.year){
			var year = this.year
		}
				
		$clear(this.interval);
		
		// create the div container if it doesn't exist
		if (!this.calendar){
//			this.calendarContainer.injectInside(document.body);
//			this.calendar = new Element('div').injectInside(this.calendarContainer);
			if (this.modeCalendar) {
				this.calendar = new Element('div').injectInside(this.calendarContainer);
			} else {
				this.calendar = new Element('div').injectInside(document.body);
			}
			this.calendar.drag = false;
			this.calendar.addEvent('mousedown',function(){
				// merci IE, qui lance mousemove apr�s mousedown, alors qu'on n'a pas boug� ...
				this.calendar.firstDragMove = true;				
				this.calendar.drag = false;
			}.bindWithEvent(this));
			
			this.calendar.addEvent('mouseup',function(){
				this.calendar.drag = false
			}.bindWithEvent(this));			
			
			document.addEvent('mousemove',function(e){
					if(this.draggable && this.calendar.drag==true && !this.calendar.firstDragMove){
						this.posLeft = e.page.x;
						this.posTop = e.page.y;
						this.calendar.setStyles({
							'left': (this.posLeft - 10) +"px",
							'top': (this.posTop  - 10) +"px"
						});
					}
					if (this.calendar.drag==true) {
						this.calendar.firstDragMove = false;
					}
				}.bindWithEvent(this)
			);
		}
		if(this.firstClick) {
			this.calendar.effect('opacity').set(0);
		}
		
		//today
		var dtToday = new Date();
			
		// create the date object
		var date = new Date();
		// create the date object
		// set the day to first of the month
		date.setDate(1);
		if (month && year){
			date.setFullYear(year, month);
		}
		
		// Attention années bissextiles : (divisibles par 4 mais pas par 100) || 400
		date.getYear() % 4 == 0 ? this.daysInMonth[1] = 29 : this.daysInMonth[1] = 28;

		var firstDay;
		// Set the first day of the week according to the language
		if (this.language == 'fr') {
			firstDay = 2 - date.getDay();
			if (date.getDay() == 0){
				//cas du dimanche
				firstDay = -5;
			}
		} else {
			firstDay = 1 - date.getDay();			
		}
		// get the position of the input box, position div and show with styles
		if(!this.posTop){
			try{
				this.posTop = event.page.y || 0;
				this.posLeft = event.page.x || 0;
			}catch(er){};
		}
		
		var hdStyle = 'font-weight: bold; text-align: center;';
		var dayStyle = 'text-align: center;';
		
		// set the styles on the div
		if(this.posTop && this.posLeft){
			this.calendar.setStyles({'z-index':'1000', 'width':'auto', 'height':'auto', 'position': 'absolute', 'display':'', 'left':this.posLeft + 'px' , 'top':this.posTop + 'px',  'backgroundColor':'#FFFFFF', 'border':'1px solid black', 'padding':'1px', 'margin':'0px 0px 3px 0px'});
			this.calendar.addClass('calBackground');
		}

		// create the month select box
		var monthSelStr = '<select id="' + this.id + '_monthSelect">';
		for (var m = 0; m < this.monthNames[this.language].length; m++){
			if (date.getMonth() == m){
				monthSelStr += '<option selected="selected" value="' + m + '">' + this.monthNames[this.language][m] + '</option>';
				} else {
				monthSelStr += '<option value="' + m + '">' + this.monthNames[this.language][m] + '</option>';
				}
			}
		monthSelStr += '</select>';
		
		// create the year select box
		var yearSelStr = ' <select id="' + this.id + '_yearSelect">';
		var dateStart = date.getFullYear() - this.beforeyear;
		var dateEnd = date.getFullYear() + this.afteryear;
		for (; dateStart <= dateEnd; dateStart++) {
			if (dateStart == date.getFullYear()) {
				yearSelStr += '<option selected="selected" value="' + dateStart + '">' + dateStart + '</option>';
			} else {
				yearSelStr += '<option value="' + dateStart + '">' + dateStart + '</option>';
			}
		}
		yearSelStr += '</select>';
		
		if (!this.modeCalendar){
			// create the close button
			if (this.closeButtonSrc == null) {
				this.closeButtonSrc = Copix.getResourceURL('img/tools/delete.png');
			}
			var closeButton = ' <img src="' + this.closeButtonSrc + '" alt="Fermer" title="Fermer" ';
			closeButton += ' class="calendar_closeButton bouton" onclick="$(\''+this.inputName+'\').hideDatePicker();" />';
		}
		
		// start creating calendar
		var calStr = '';
		if (this.title != null && this.title != '') {			
			calStr += '<div class="' + this.classe + '_title">';
			calStr += '<span class="' + this.classe + '_title_close">' + closeButton + '</span>';
			calStr += this.title;
			calStr += '</div>';
		}
		
		calStr += '<table class="'+this.classe+'">';
		calStr += '<tr class="calendar_header">';
		calStr += '<td class="calendar_header" colspan="7" style="' + hdStyle + '">' + monthSelStr + yearSelStr;
		if ((this.title == null || this.title == '') && !this.modeCalendar) {
			calStr += closeButton;
		}
		calStr += '</td>';
		calStr += '</tr>';
		
		// create day names
		calStr += '<tr class="calendar_header">';
		for (var i = 0; i < this.dayNames[this.language].length; i++){
			calStr += '<td class="calendar_header" style="' + hdStyle + '">' + this.dayNames[this.language][i].substr(0, this.sizeday) + '</td>';
			}
		calStr += '</tr>';
		
		// create the day cells
		while (firstDay <= this.daysInMonth[date.getMonth()]){
			calStr += '<tr>';
			for (i = 0; i < 7; i++){
				if ((firstDay <= this.daysInMonth[date.getMonth()]) && (firstDay > 0)){
					if( dtValue){
						if( dtToday.getFullYear() == date.getFullYear() && dtToday.getMonth() == date.getMonth() && dtToday.getDate() == firstDay){
							calStr += '<td class="calendar_today';
						}else{
							calStr += '<td class="calendar_day'; 
						}
						if( dtValue.getFullYear() == date.getFullYear() && dtValue.getMonth() == date.getMonth() && dtValue.getDate() == firstDay){
							calStr += ' calendar_value';
						}
						calStr += '" style="' + dayStyle + '"><a href="javascript:void(0);" class="' + this.id + '_calDay' + '" rel="' + date.getFullYear() + '|' + (date.getMonth() + 1) + '|' + firstDay + '"><div>' + firstDay + '</div></a></td>';
					}else{
						if( dtToday.getFullYear() == date.getFullYear() && dtToday.getMonth() == date.getMonth() && dtToday.getDate() == firstDay){
							calStr += '<td class="calendar_today" style="' + dayStyle + '"><a href="javascript:void(0);" class="' + this.id + '_calDay' + '" rel="' + date.getFullYear() + '|' + (date.getMonth() + 1) + '|' + firstDay + '">' + firstDay + '</a></td>';
						}else{
							calStr += '<td class="calendar_day" style="' + dayStyle + '"><a href="javascript:void(0);" class="' + this.id + '_calDay' + '" rel="' + date.getFullYear() + '|' + (date.getMonth() + 1) + '|' + firstDay + '">' + firstDay + '</a></td>';
						}
					}
				} else {
					calStr += '<td class="calendar_noday" style="' + dayStyle + '">&nbsp;</td>';
					}
				firstDay++;
				}
			calStr += '</tr>';
			}
		
		// close the date picker table
		calStr += '</table>';

		// put html string into div container
		if(this.firstClick) {
			this.calendar.effect('opacity', {duration:this.duration, transition:Fx.Transitions.linear}).start(0,1);
			this.firstClick = false;
		}
		this.calendar.innerHTML=calStr;
		// set the onclick events for all calendar days
		$$('a.' + this.id + '_calDay').each(function(el){
			el.onclick = function(){
				ds = el.rel.split('|');
				this.input.value = this.formatValue(ds[0], ds[1], ds[2]);
				if (!this.modeCalendar){
					this.calendar.remove();
				}else {
					$$('.calendar_value').each(function (el){
						el.removeClass("calendar_value");
					});
					el.getParent().addClass("calendar_value");
				}
				this.calendar = false;
				this.posLeft = "";
				this.posTop = "";
				this.year = "";
				this.month = "";
				this.firstClick = true;
				this.input.fireEvent('change');
				}.bind(this);
			}.bind(this));
		
		// set the onchange event for the month & year select boxes
		$(this.id + '_monthSelect').onfocus = function(){
			this.active = true;
		}.bind(this);
		$(this.id + '_monthSelect').onblur = function(){
			this.active = false;
		}.bind(this);
		$(this.id + '_monthSelect').onchange = function(){
			this.month = $(this.id + '_monthSelect').value;
			this.year = $(this.id + '_yearSelect').value;
			this.createCal(event);
			this.active = false;
			this.calendar.drag=false;
			}.bind(this);
		$(this.id+'_monthSelect').addEvent("mouseover",function(){
			this.active = true; 
			}.bindWithEvent(this)
		);
		
		$(this.id+'_monthSelect').addEvent("mouseout",function(){
			this.active = false;  
			}.bindWithEvent(this)
		);
		
		$(this.id + '_yearSelect').onfocus = function(){ 
			this.active = true; 
		}.bind(this);
		$(this.id + '_yearSelect').onblur = function(){
			this.active = false;  
		}.bind(this);

		$(this.id + '_yearSelect').onchange = function(){
			this.month = $(this.id + '_monthSelect').value
			this.year = $(this.id + '_yearSelect').value;
			// beforeyear and afteryear need to be recalculated otherwise the years add up incorrectly
			var yearSelect = $(this.id + '_yearSelect');
			this.beforeyear = yearSelect.value - yearSelect.options[0].value;
			this.afteryear = yearSelect.options[ yearSelect.options.length -1 ].value - yearSelect.value;
			this.createCal(event);
			this.active = false;
			this.calendar.drag=false
			}.bind(this);	
		$(this.id+'_yearSelect').addEvent("mouseover",function(){
			this.active = true;
			}.bindWithEvent(this)
		);
		
		$(this.id+'_yearSelect').addEvent("mouseout",function(){
			this.active = true;
			}.bindWithEvent(this)
		);

		}, 
	
	/*********************************************/
	// function formatValue() will format the returning date value according to the selected formation
	formatValue: function(year, month, day){
		// setup the date string variable
		var dateStr = '';
		
		// check the length of day
		if (day < 10){ day = '0' + day; }
		if (month < 10){ month = '0' + month; }
		
		// check the format & replace parts // thanks O'Rey
		dateStr = this.format.replace( /dd/i, day ).replace( /mm/i, month ).replace( /yyyy/i, year );
		
		// return the date string value
		return dateStr;
		}
	
	/*********************************************/
	});

/********************************************************/
Element.extend({
	datePicker: null,
	
	makeDatePicker : function (options){
		options.input=this;
		this.datePicker = new DatePicker(this.id,options);
		return this.datePicker;
	},
	
	hideDatePicker : function () {
		if(this.id!=null){
			$(this.id).datePicker.calendar.setStyles({'display':'none'});
			$(this.id).datePicker.posLeft = "";
			$(this.id).datePicker.posTop = "";
			$(this.id).datePicker.year = "";
			$(this.id).datePicker.month = "";
			$(this.id).datePicker.firstClick = true;
			$(this.id).datePicker.calendar.drag = false
		}
		else{
			$$('.calBackground').setStyles({'display':'none'});
		}
	}
})