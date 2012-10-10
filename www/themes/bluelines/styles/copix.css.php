<?php       
    header("Content-type: text/css; charset: UTF-8");
    header("Cache-Control: must-revalidate");
    $offset = 60 * 60 ;
    $ExpStr = "Expires: " .
    gmdate("D, d M Y H:i:s",
    time() + $offset) . " GMT";
    header($ExpStr);

    //some defines
  	$softgrey='#4b4d46';
  	$hardgrey='#868e74';
  	$importantcolor= "#AA2314";
  	$titlecolor = "#b60000";
  	$contentwidth = "75%";
  	$root = $_GET['copixurl'];
?>
@CHARSET "UTF-8";

.errorMessage {
   border: 2px solid #aa0000;
   margin: 5px;
   padding: 5px;
   text-align: center;
}
.errorMessage h1 {
   background-color: #792206;
   background-image:url('../img/titre_rouge.gif');
   background-repeat:no-repeat;
   background-position: top right;
   color: #ffffff;
   text-align: center;
   font-size: 1em;
   margin-top: 0;
   padding: 3px;
}

.CopixTable {
   width: 100%;
   border: 1px solid #6597B0;
   border-collapse: collapse;
}
.CopixVerticalTable {
   width: 100%;
   border: 1px solid #6597B0;
   border-collapse: collapse;
}

.CopixVerticalTable th {
   text-align: left;
   background-color: #7BB0C1;
   color: #ffffff;
   border: 1px solid #ffffff;
}

.CopixVerticalTable tr:hover {
   background-color:#E0EFF4;
}

.CopixTable tr:hover {
   background-color:#E0EFF4;
}

.CopixTable thead {
    background-color:#4b4d46;
}

.CopixTable thead th {
   background-color:transparent;
   text-align: left;
}

.CopixTable tr th {
   text-align: left;
   background-color:#7BB0C1;
   height:25px;
   color: #ffffff;
}

.CopixTable td {
   height:25px;
}

.CopixTable th a,.CopixTable th a:visited {
   color: #ffffff;
   text-decoration: none;   
}

.alternate {
   background-color: #F3F8FA;
}

/** Mootools **/
.resizable{
	padding-bottom: 24px;
	padding-right: 20px;
	background-image: url('../img/modules/wiki/resize.png');
	background-position: bottom right;
	background-repeat: no-repeat;
}

.moocolumn {
	padding-right : 4px;
}

.moocolumn2 {
	padding-left : 4px;
	border-left : 1px dotted #AEAEAE;
}

/* CopixForms */
.copixforms_error {
	color:red;
}

/* AutoCompleter */
div.autocompleter-loading {
	float:					left;
	clear:					right;
	background:				#fff url('../images/spinner.gif') no-repeat center;
	width:					50px;
	height:					16px;
}

ul.autocompleter-choices {
	position:				absolute;
	margin:					0;
	padding:				0;
	list-style:				none;
	border:					1px solid #eee;
	background-color:		white;
	border-right-color:		#ddd;
	border-bottom-color:	#ddd;
	text-align:				left;
	font-family:			Verdana, Geneva, Arial, Helvetica, sans-serif;
	z-index:				50;
	overflow:auto;
	height:150px;
}

ul.autocompleter-choices li {
	position:				relative;
	padding:				0.1em 1.5em 0.1em 1em;
	cursor:					pointer;
	font-weight:			normal;
	font-size:				1em;
}

ul.autocompleter-choices li.autocompleter-selected {
	background-color:		#444;
	color:					#fff;
}

ul.autocompleter-choices span.autocompleter-queried {
	font-weight:			bold;
}

ul.autocompleter-choices li.autocompleter-selected span.autocompleter-queried {
	color:					#9FCFFF;
}

/** Calendar **/
div.calContainer {
	width:auto;
	height:auto;
	border :0px;
}
div.calBackgroud {
	background-color: #c0c0c0;
	border: 2px outset white;
}
table.calendar  {
	background-color: #c0c0c0;
}

.calendar th {
	background-color: #eeeeee;
	border: 1px solid #c0c0c0;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 8pt;
	text-align: center;
}
.calendar tr {
	background-color: #c0c0c0;
}
.calendar tr.calendar_header {
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 10pt;
	font-weight: bold;
}

.calendar td {
	color: #000000;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 8pt;
}
.calendar td.calendar_day {
	background-color: #eeeeee;
}

.calendar td.calendar_today {
	background-color: #dddddd;
}

.calendar td.calendar_noday {
	background-color: #ffffff;
}

.ma_class_calendar td.calendar_today {
	background-color: red;
}

.ma_class_calendar td.calendar_noday {
	background-color: #ffffff;
}

.ma_class_calendar td.calendar_day {
	background-color: blue;
}

.popupInformation {
   background-color: #C6CeB4;
   border:1px solid #4b4d46;
   padding:3px;
   position:absolute;
}