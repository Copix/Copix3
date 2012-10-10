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

body{ 
	font-family:"Verdana","Sans","LucidaGrande","Arial","Helvetica","sans-serif"; 
	font-size:9pt;
	font-size-adjust:none; 
	font-stretch:normal; 
	font-style:normal;
	font-variant:normal; 
	font-weight:normal; 
	line-height:normal;
	background-color: #FFF;
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
	background-image: url(../img/logo.png);
	background-repeat: no-repeat;
	background-position: top left;
	height: 100px;
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
	font-size: 16pt !important;
	color: white;
	padding:2px;
	margin-top: 15px;
	margin-left: 160px;
	color: #b60000;
	background-color: #FFF;
	display: block;
	float: left;
}

#maincontent{
	position: relative;
	width: 99%;
	margin: 0;
	margin-top: 30px;
	padding-top: 16px;
	background-color: #FFF;
	padding-bottom: 10px;	
	padding-left: 1%;
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
	padding-bottom: 12px;
	padding-top: 4px;
	clear: both;
	width: 100%;
}

#footer a{
	color: white;
}

#footer a:hover{
	background:none;
	color: white;
}

#menu{
	margin-left: 400px;
	margin-top: -30px;
}

#menu li{
	float: left;
	list-style-type: none;
	background-color: #FFF;
	padding: 2px;
	font-weight: bold;
	font-size: 10px;
	padding-left: 5px;
	padding-right: 5px;
	margin-left: 3px;
	text-align: center;
}

#menu li a {
	display: block;
	padding: 2px;
}

#menu li a:hover {
	color: <?php echo $softgrey; ?>;
}

img{
	border: none;
}

a{
	color: #db994b;
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

/* PRO */
.homecontainer {
    margin-left: auto;
    margin-right: auto;
    width: 99%;
    height: 100%;
    clear: both
}
.tiers1{
    width: 16%;
    float: left;
}

.tiers1 h3{
    background-color: #4b4d46;
    color: #FFF !important;
    font-size: 9pt !important;
    margin-top:0px;
    padding: 5px;
    text-align: center;
    margin-top: 0px;
}
.tiers1 p, .tiers1 ul {
    border: 1px solid #DDD;
    margin-top: -13px;
    margin-bottom: 20px;
    list-style-type: square;
    padding: 5px;
    
}

.tiers1 li{
    margin-left: 8px;
}

.tiers2{
    float: left;
    width: 150px; 
}

.tiers2 p{
    width: 130px; 
    background-color: #4b4d46;
    border: 1px solid #DDD;
    text-align: center;
    margin-top: 0;
    margin-right: 10px;
    padding: 5px;
    color: #FFF;
    font-size: 10pt;
    font-weight: bold;
    float: left;
}

#oncenter{
    /*width: 64%;*/
    width: 80%;
    border: 1px solid #DDD;
    float: left;
    margin-right: 5px; 
    margin-left: 5px;
    padding: 8px;
}