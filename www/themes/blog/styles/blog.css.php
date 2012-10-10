<?php
  header('content-type:text/css');
  header("Expires: ".gmdate("D, d M Y H:i:s", (time()+900)) . " GMT"); 
  
?>
.blog_ticket{
	border-bottom: 1px solid #EFEFEF; 
}


.blog_ticket h2{
	padding: 10px;
	
}

.blog_date{
	float: right;
	color: white;
	margin-right: 8px;
	margin-top: 1px;
}

.blog_pager{
	text-align: center;
	padding-top: 5px;
	padding-bottom: 5px;
	font-weight: bold;
}

