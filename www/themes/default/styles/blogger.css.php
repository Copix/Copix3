<?php
  header('content-type:text/css');
  header("Expires: ".gmdate("D, d M Y H:i:s", (time()+900)) . " GMT"); 
  
  //some defines
  $softgrey='#4b4d46';
  $hardgrey='#868e74';
  $importantcolor= "#AA2314";
  $titlecolor = "#b60000";
  $contentwidth = "75%";
?>

#blogger_panel{
	width: 16% !important;
	border: 1px solid <?php echo $softgrey; ?>;
}

#blogger_panel p, #blogger_panel ul{
	border: 1px solid <?php echo $softgrey; ?>;
	margin-top: -12px;
	padding: 3px;
	list-style-type: none;
	padding-bottom: 8px;
	margin-bottom: 3px;
}

.calendar{
	margin-left: auto;
	margin-right: auto;
}

#blogger_panel h3{
	font-size: 9pt !important;
	background-color: <?php echo $softgrey; ?>;
	color: #FFF !important;
	padding-top: 4px ;
	padding-bottom: 4px;
	text-align: center !important;
	margin-top: 3px;
}