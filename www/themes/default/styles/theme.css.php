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


body{ 
	font-family:"Verdana","Sans","LucidaGrande","Arial","Helvetica","sans-serif"; 
	font-size:9pt;
	font-size-adjust:none; 
	font-stretch:normal; 
	font-style:normal;
	font-variant:normal; 
	font-weight:normal; 
	line-height:normal;
	background-color: <?php echo $hardgrey; ?>;
	/*background-color: <?php echo $softgrey; ?>;*/
    margin: 0;
    padding: 0;
}

#colright{
	float: left;
	position: absolute;
	margin-left: 3px;
	width: 180px;
	margin-top: 128px;
	left: 0
}

#allcontent{
	margin: 0px;
	padding: 0px;
}


#banner{
	background-color: <?php echo $softgrey; ?>;
	background-image: url('../img/logo.png');
	background-repeat: no-repeat;
	background-position: top left;
	height: 133px;
	width: 100%;
	margin: 0px;
}

#slogan{
	margin-left: 200px;
	margin-top: 15px;
	color: #FFF;
	font-weight: bold;
	font-size: 11pt;
}


/* The title */
h1.main{
	font-size: 11px !important;
	background-color: <?php echo $softgrey; ?>;
	color: white;
	width: 33%;
	margin: 8px;
	padding: 8px;
	margin-left: 0px;
	margin-top: 0px;
}

#maincontent{
	position: relative;
	margin-top: -96px;
	padding-top: 16px;
	background-color: #FFF;
	padding-bottom: 10px;
	margin-left: 195px;
	padding-left: 8px;
	width: <?php echo $contentwidth; ?>;
}
#maincontent h1 {
	font-size: 14pt;
}

#maincontent h2, #maincontent h3,#maincontent h4{
	color: <?php echo $titlecolor; ?>;
}

#maincontent h2{
	font-size: 12pt;
}

#maincontent h3{
	font-size: 10pt;
}

#maincontent h4{
	font-size: 8pt;
}

#footer{
	color: white;
	text-align: center;
	font-weight: bold;
	background-color: <?php echo $softgrey; ?>;
	margin-left: -8px;
	margin-bottom: -10px;
	padding-bottom: 12px;
	padding-top: 4px;
}

#footer a{
	color: white;
}

#footer a:hover{
	background:none;
	color: white;
}

#menu{
	float: left;
}

#menu li{
	list-style-type: none;
	color: #FFF;
	background-color: <?php echo $softgrey; ?>;
	margin-bottom: 2px;
	margin-left: -40px;
	width: 140px;
	padding: 5px;
	font-weight: bold;
	position: relative;
	clear:both;
	font-size: 10px;
	/*Only at start, mootools effect will set display*/
	display: none;
}

#menu li:hover{
	color: #333;
}

#menu li a{
    display: block;
    width: 140px;
	color: white;
	background-color: transparent !important;
	margin: 0;

}

#menu li a:hover{
    color: #ffffff;
    margin: 0;
}

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
	background-color: <?php echo $softgrey; ?>;
	color: white;
}

input[type=text]:hover,input[type=password]:hover,input[type=listbox]:hover{
	background-color: <?php echo $softgrey; ?>;
	color: white;
	border: 1px solid <?php echo $importantcolor; ?>
}

.rtabs li{
	display:inline;
	padding: 10px;
	border: 1px solid <?php echo $softgrey; ?>;
}
.rtabs {
	padding:0;
	margin: 10 0 10 0;
	list-style-type:none;
}

.rtab {
	background-color: #CCCCCC;
}

.rtab_selected {
	background-color: #FFFFFF;
}

h2 {
	width: 98%;
}