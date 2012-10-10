Vous pouvez créer une page ou modifier une page existante:
<h2>Pages existantes</h2>
<ul>
<?php

foreach ($ppo->pages as $page){
	echo "<li>";
	echo "<a href=\""._url('moocms|admin|edit',array('title'=>$page->name_moocmspage))."\">";
	echo $page->name_moocmspage;
	echo "</a>";
	echo "</li>";
}
?>
</ul>

<h2>Nouvelle page</h2>

<select id="template" name="template">
<?php 
foreach($ppo->templates as $name=>$val){
	echo "<option value=\"".$val."\">".$name."</option>";
}
?>
</select>
<a id="addpage" href="#">Create New</a>
<?php 
_eTag('mootools');
CopixHTMLHeader::addJSCode('
window.addEvent("domready",function(){
	$("addpage").addEvent("click",function(){
		document.location.href = "'._url('moocms|admin|edit').'?template="+$("template").value
	});
})
');
?>