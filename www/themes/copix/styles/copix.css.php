<?php
  header('content-type:text/css');
  header("Expires: ".gmdate("D, d M Y H:i:s", (time()+900)) . " GMT"); 
  
  //some defines
  $softgrey='#4b4d46';
  $hardgrey='#868e74';
  $importantcolor= "#AA2314";
  $titlecolor = "#b60000";
  
  
  $contentwidth = "75%";
  
  $root = $_GET['copixurl'];
?>
@CHARSET "UTF-8";

.popupInformation {
   background-color: #C6CeB4;
   border:1px solid #4b4d46;
   padding:3px;
   position:absolute;
}
.errorMessage {
   border: 2px solid #aa0000;
   margin: 5px;
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
}

.CopixTable {
   margin-left: 5px;
   width: 98%;
  
   border:3px solid #4b4d46;
   border-collapse:collapse;
}
.CopixVerticalTable {
   margin-left: 5px;
   width: 98%;
   border:3px solid #4b4d46;
   border-collapse:collapse;
}

.CopixVerticalTable th {
   text-align: left;
   background-color: #4b4d46;
   color: #ffffff;
   border: 1px solid #ffffff;
}

.CopixVerticalTable tr:hover {
   background-color:#C6CeB4;
}

.CopixTable tr:hover {
   background-color:#C6CeB4;
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
   background-color:#4b4d46;
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
   background-color: #ecefda;
}

.resizable{
	padding-bottom: 24px;
	padding-right: 20px;
	background-image: url(../img/modules/wiki/resize.png);
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

.comments dt {
	position: relative;
	top: 1px;
	display: block;
	padding: 10px 0 30px 0;
	background: transparent url('../img/comments_start.png') no-repeat 30% 100%;
}

.comments dd {
	border: 1px solid <?php echo $softgrey;?>;
	border-width: 1px 2px 2px 1px;
	background: <?php echo $softgrey;?>;
	padding: 10px;
	margin: 0 20px 20px 0;
	font-size: 1.1em;
	line-height: 1.4;
}
.comment-id {
	font-weight: bold;
	font-size: 2em;
}
.comments dd * { padding: 5px 0; font-size: 1em; }
.comments-form input, textarea {width: 80%}
.comments-form input.preview, input.submit {width: auto}

/* Calendrier */
div.calContainer {
	width:auto;
	height:auto;
	border:0px;
}

div.calBackground {
	width:auto;
	height:auto;
	border: 2px outset white;
	background-color: #c0c0c0;
	padding:2px;
}

table.calendar  {
	width:auto;
	height:auto;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #c0c0c0;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 10pt;
	font-weight: normal;
	padding:2px;
	margin:0px;
}

.calendar th {
	width:auto;
	height:20px;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #eeeeee;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 8pt;
	font-weight: normal;
	padding:2px;
	margin:0px;
}

.calendar tr {
	width:auto;
	height:15px;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #c0c0c0;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 10pt;
	font-weight: normal;
	padding:2px;
	margin:0px;
}

.calendar tr.calendar_header {
	width:auto;
	height:20px;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #c0c0c0;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 10pt;
	font-weight: bold;
	padding:2px;
	margin:0px;
}

.calendar td {
	width:auto;
	height:15px;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #ffddcc;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 8pt;
	font-weight: bold; 
	vertical-align: middle;
	padding:2px;
	margin:0px;
}

.calendar td.calendar_header {
	width:auto;
	height:15px;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #c0c0c0;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 10pt;
	font-weight: bold;
	padding:2px;
	margin:0px;
}

.calendar td.calendar_today {
	width:auto;
	height:15px;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #c0c0c0;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 10pt;
	font-weight: bold;
	padding:2px;
	margin:0px;
}

.calendar td.calendar_day {
	width:auto;
	height:15px;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #dddddd;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 10pt;
	font-weight: bold;
	padding:2px;
	margin:0px;
}

.calendar td.calendar_noday {
	width:auto;
	height:15px;
	border: 1px solid #c0c0c0;
	border-collapse: collaspe;
	background-color: #ffffff;
	color: #000;
	text-align: center;
	font-family: Arial, Verdana, Helvetica, Sans-Serif;
	font-size: 10pt;
	font-weight: bold;
	padding:2px;
	margin:0px;
}