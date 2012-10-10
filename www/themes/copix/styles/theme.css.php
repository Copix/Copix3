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

body, th, td {
 font: normal 13px verdana,arial,'Bitstream Vera Sans',helvetica,sans-serif;
}

h1, h2, h3, h4 {
 font-family: arial,verdana,'Bitstream Vera Sans',helvetica,sans-serif;
 font-weight: bold;
 letter-spacing: -0.018em;
}

body{
    margin: 0;
    padding: 0;
	background-color: <?php echo  $hardgrey; ?>  ;
}

#banner{
	background-color: <?php echo $softgrey; ?>;
	margin: 0px;
}

/* The title */
#banner h1 {
	position: absolute;
	top: 0px; 
	right: 10px;
	text-align: right;
	color: #ffffff;
	font-size: 16pt !important;
}

#searchengine {
	position: absolute;
	top: 70px; 
	right: 10px;
	text-align: right;
	color: #ffffff;
	font-size: 16pt !important;
}

#maincontent{
	margin: 10px;
	margin-bottom: 0px;
	border: 1px solid #000;
	background-color: #FFF;
	padding: 5px;
	border-bottom: 0px;	
}

#maincontent h2, #maincontent h3, #maincontent h4{
	padding-top: 5px;
	color: <?php echo $titlecolor; ?>;
}
#maincontent h2 { font-size: 18px; margin: .15em 1em 0 0 }
#maincontent h3 { font-size: 16px }
#maincontent h4 { font-size: 14px }

#footer{
	margin: 0px;
	margin-left: 10px;
	margin-right: 10px;

	color: white;
	text-align: center;
	font-weight: bold;
	background-color: <?php echo $softgrey; ?>;
	padding-bottom: 12px;
	padding-top: 4px;
	border: 1px solid #000;
	border-top: 0px;
}

#footer a{
	color: white;
}

#footer a:hover{
	background:none;
	color: white;
}

/* Navigation */
#menu ul { font-size: 10px; list-style: none; margin: 0; text-align: right; margin-top: -15px; }
#menu li {
 border-right: 1px solid #d7d7d7;
 display: inline;
 padding: 0 .75em;
 white-space: nowrap;
}
#menu li.last { border-right: none }

img{
	border: none;
}

a{
	color: #c97729;
	text-decoration: none;
	font-weight: bold;
}

a:visited{
}

a:hover{
	color: #FF0000;
}

input[type=text],input[type=password],input[type=listbox],textarea{
	border: 1px solid <?php echo $softgrey; ?>;
	margin: 1px;
}

input[type=text]:focus,input[type=password]:focus,input[type=listbox]:focus{
	background-color: <?php echo $hardgrey; ?>;
	color: white;
}

input[type=text]:hover,input[type=password]:hover,input[type=listbox]:hover{
	background-color: <?php echo $hardgrey; ?>;
	color: white;
	border: 1px solid <?php echo $importantcolor; ?>
}