<?php       
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " .
gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($ExpStr);

$root = $_GET['copixurl'];
$tplPath = $root . 'themes/bluelines/';
$imgPath = $tplPath . 'img/';
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
	background-image:url(<?php echo $imgPath ?>background.png);
	margin: 0;
    padding: 0;
    cursor: default;
}

td.block_top_left {
	width: 6px;
	background-image: url(<?php echo $imgPath; ?>block_top_left.png);
}

td.block_top {
	height: 5px;
	background-image: url(<?php echo $imgPath; ?>block_top.png);	
}

td.block_top_right {
	background-image: url(<?php echo $imgPath; ?>block_top_right.png);
}

td.block_right {
	width: 8px;
	background-image: url(<?php echo $imgPath; ?>block_right.png);
}

td.block_bottom_right {
	height: 5px;
	background-image: url(<?php echo $imgPath; ?>block_bottom_right.png);
}

td.block_bottom {
	background-image: url(<?php echo $imgPath; ?>block_bottom.png);
}

td.block_bottom_left {
	background-image: url(<?php echo $imgPath; ?>block_bottom_left.png);
}

td.block_left {
	background-image: url(<?php echo $imgPath; ?>block_left.png);
}

td.block_content {
	background-color: white;	
}

td.title {	
	background-repeat: repeat-x;
	background-image: url(<?php echo $imgPath ?>title_center.png);
	width: 100%;
}

.title {
	font-size: 14px;
	padding-top: -18px;
	color: #2C25C4;
	font-weight: bold;
	margin-top: -5px;
	margin-left: 3px;
}

img{
	border: none;
}

a{
	color: #2F5778;
	text-decoration: none;
	font-weight: bold;
}

a:visited{
}

a:hover{
	color: #FF0000;
}

input[type=text],input[type=password],input[type=listbox],textarea{
	border: 1px solid #B7C8CF;
	margin: 1px;
}

input[type=text]:focus,input[type=password]:focus,input[type=listbox]:focus{
	background-color: #F4F9FB;
	border: 1px solid #51889D;
}

input[type=text]:hover,input[type=password]:hover,input[type=listbox]:hover{
	border: 1px solid #51889D;
}

h2 {
	background-image: url(<?php echo $imgPath ?>title_underline.png);
	background-repeat: no-repeat;
	background-position: bottom left;
	font-size: 11pt;
	height: 22px;
	margin-left: -10px;
}