<?php
  header ('content-type:text/css');
  header ("Expires: ".gmdate("D, d M Y H:i:s", (time()+900)) . " GMT"); 
  
  //some defines
  $softgrey='#e5e3d3';;
  $hardgrey='#97A563';
?>

@CHARSET "UTF-8";

/** WIKI **/
a.wiki_toolbar{
	cursor: pointer;
}
._wiki_preview{
	height: 100%;
	width: 100%;
    clear: both;
}

/* code highlighter */
.wiki_code{
	background-color : #EEEEEE;
	margin-left:auto;
	margin-right:auto;
	width: 90%;
	margin-bottom: 3px;
	padding: 4px;
	padding-top: 16px;
	padding-bottom: 16px;
	border: 1px solid <?php echo $hardgrey; ?>;
}

/* File download link */
a.wiki_dl_file{
	padding-left: 15px;
	height: 15px;
	background-image: url(../img/tools/next.png);
	background-repeat: no-repeat;
	background-position: bottom left;
}

/* content */
.wiki_content{
	height: 100%;
}
.wiki_author{
    padding-top: 10px;
	font-style: italic;
}
#wiki_nav_bar{
	padding: 8px;
	padding-top: 0px;
	text-align: left;
	margin-top: 5px;
}

#wiki_nav_bar h2{
	font-size: 10px !important;
	background-color: <?php echo $hardgrey; ?>;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 3px;
	color: white;
}

.wiki_exists{

}
a.wiki_no_exists, a.wiki_no_exists:visited{
	color: #FF0000;
}

#wiki_toc{
	background-color: <?php echo $hardgrey; ?>;
	float: right;
	padding: 18px;
	margin: 2px;
}

#wiki_toc strong{
	color: #FFF;
}

#wiki_toc a{
	color: #FFF;
}

#wiki_footnotes{
	background-color: #FFF;
	margin-top: 5px;
}

#wiki_footnotes h2{
	font-size: 11px !important;
	background-color: <?php echo $softgrey; ?>;
	color: white;
	margin: 8px;
	padding: 8px;
}

#_area_content{
	width: 100%;
	height: 100%;
}


/* Tables */
table.wiki_table{
	margin: 0px;
	margin-left: auto; 
	margin-right: auto;
	padding: 0px;
    border-left: 1px solid black;
    border-top: 1px solid black;
	
}
table.wiki_table tr{	
	margin: -1px;
	padding: 0px;
	
}
table.wiki_table td{
	margin:  0px;
	padding: 5px;
	border-right: 1px solid black;
	border-bottom: 1px solid black;
}
table.wiki_table th{
	margin:  0px;
	padding: 5px;
	border-right: 1px solid black;
	border-bottom: 1px solid black;
	background-color: #EFEFEF;
}