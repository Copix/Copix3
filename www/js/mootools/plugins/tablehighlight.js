/**
TableHighlight is a simple script to allow styling columns and rows on rollover
Author: Goulven CHAMPENOIS

Usage:
	new TableHighlight($('myTable'));
	new TableHighlight($('myTable'), {'hoverClass': 'rollover'} ); // to change the rollover classname
	new TableHighlight($('myTable'), {'allowRow': false, 'rolloverTfoot': false} ); // To avoid rollover on table row and tfoot
	
Styling:
	td.hover, th.hover { -your styles here- }
Compatible with:
	Mootools 1.22
Tested with:
	Firefox 3.5, Opera 9.5, Safari 4, IE6/7/8
Possible improvements:
	handle rowspan and colspan
*/
var TableHighlight = new Class({
	Implements: Options,
	
	options: {
		'hoverClass': 'hover',
		'clickClass': 'clicked', // this class is added or removed to the row and column when any cell is clicked
		// set to false if you don't want the entire row or column to be highlighted
		'allowRow': true,
		'allowCol': true,
		'downTo': false,
		// set to false if rolling over thead, tbody and/or tfoot shouldn't highlight or click anything
		'rolloverThead': true,
		'rolloverTbody': true,
		'rolloverTfoot': true
	},
	
	initialize: function(obj, options){
		if (!obj) { return; }
		obj = $(obj);
		if (obj.get('tag')  !== 'table') { return; }
		this.setOptions(options);
		this.allCells = [];
		this.setup(obj);
	},
	
	setup: function(table){
		var trs, cells, i, j, k, l;
		
		var colCells = [];
		var rowCells = [];
		var allCells = this.allCells;
		
		// Grab TRs in display order, not source order (which can be thead / tfoot / tbody
		trs = table.getElements('thead tr, tbody tr, tfoot tr');
		l = trs.length;
		for(i=0; i<l; i++){
			cells = trs[i].getChildren();
			rowCells[i] = cells;
			k = cells.length;
			for(j=0; j<k; j++){
				if(!colCells[j]) {
					colCells[j] = [];
				}
				if( this.rollover(cells[j])){
					allCells.push( cells[j] );
					colCells[j].push(cells[j]);
					// Store row and col index in each cell
					cells[j].rowIndex = i;
					cells[j].colIndex = j;
				}
			}
		}
		this.colCells = colCells;
		this.rowCells = rowCells;
		// Use bubbling to catch events at the highest level
		table.addEvent('mouseover', this.toggleOver.bind(this));
		table.addEvent('mouseout', this.toggleOver.bind(this));
		table.addEvent('click', this.toggleClick.bind(this));
	},
	toggleOver: function(e){
		var target = e.target;
		while( !['td','th'].contains(target.get('tag') ) ) {
			target = target.getParent();
			// break out if we're up to the table element
			if( !target || target.get('tag') == 'table') { return; }
		}
		// avoid errors
		if( target.rowIndex == null || target.colIndex == null ) { return; }
		if( e.type == 'mouseover' ){
			if(this.options.allowCol) {
				this.over( this.colCells[target.colIndex], e.target);
			}
			if (this.options.allowRow) {
				this.over( this.rowCells[target.rowIndex], e.target );
			}
		} else {
			this.out( this.allCells );
		}
	},
	over: function(cells, target){
		var className = this.options['hoverClass'];
		if(this.options.downTo){
			cells.each(function(cell){
				if( cell.colIndex <= target.colIndex && cell.rowIndex <= target.rowIndex){
					cell.addClass(className);
				}
			});
		} else {
			cells.each(function(cell){cell.addClass(className);});
		}
	},
	out: function(cells){
		var className = this.options['hoverClass'];
		cells.each(function(cell){cell.removeClass(className);});
	},
	rollover: function(cell){
		if( !this.options.rolloverThead && cell.getParent().getParent().get('tag') == 'thead'){
			return false;
		}
		if( !this.options.rolloverTbody && cell.getParent().getParent().get('tag') == 'tbody'){
			return false;
		}
		if( !this.options.rolloverTfoot && cell.getParent().getParent().get('tag') == 'tfoot'){
			return false;
		}
		return true;
	},
	toggleClick: function(e){
		var target = e.target;
		if( !['td','th'].contains(target.get('tag') ) || target.rowIndex == null || target.colIndex == null ) { return; }
		var hasClass = target.hasClass( this.options['clickClass'] );
		this.unClick( this.allCells );
		if( !hasClass && this.options.allowCol) {
			this.click( this.colCells[target.colIndex], target);
		}
		if ( !hasClass && this.options.allowRow) {
			this.click( this.rowCells[target.rowIndex], target );
		}
	},
	click: function(cells, target){
		var className = this.options['clickClass'];
		if(this.options.downTo){
			cells.each(function(cell){
				if( cell.colIndex <= target.colIndex && cell.rowIndex <= target.rowIndex){
					cell.addClass(className);
				}
			});
		} else {
			cells.each(function(cell){cell.addClass(className);});
		}
	},
	unClick: function(cells){
		var className = this.options['clickClass'];
		cells.each(function(cell){cell.removeClass(className);});
	}
});