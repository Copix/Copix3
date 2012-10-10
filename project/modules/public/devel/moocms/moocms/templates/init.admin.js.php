<?php
foreach($boxes as $box){
	    echo "
	    var tmp = new Hash();\n";
	    foreach($box->other as $par=>$infos){
	        if(!in_array($par,array("boxtype",
	        						"zone",
        							"id",
        							"order",
        							"boxdatas",
        							"pagedate",
        							"pagename"))){
	        
	        	$infos = str_replace("/","__COPIX_add_slashes_COPIX__",$infos);        							
	        	$infos = str_replace("'","\'",$infos);
	        	$infos = str_replace("\n",'',$infos);	
	        	$infos = str_replace("\r",'',$infos);
        	
        		//$infos = htmlspecialchars($infos);					
        		echo "	    tmp.set('$par','".addslashes($infos)."');\n";
			}
	    }
	    
	    echo "
		var box = {
			boxtype : '".$box->other['boxtype']."',
			boxdatas : tmp.obj,
			zone: '".$box->other['zone']."',
			id: '".trim($box->other['id'])."',
			order: ".$box->other['order']."
		}
		WebBoxes.boxes.push(box);
";
}
?>