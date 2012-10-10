<h2>Edit HTML Content</h2>
Titre : <input type="text" name="title"/>
<br />
Contenu: <br />
<textarea id="htmlcontent" name='htmlcontent'></textarea>


<script type="text/javascript" src="<?php echo _resource('/js/FCKeditor/fckeditor.js'); ?>" ></script>
<script type="text/javascript">
  window.addEvent('savebox',function(){
  	try{
      	//when save button pressed
      	var oEditor = FCKeditorAPI.GetInstance('htmlcontent') ;
      	$('htmlcontent').value = oEditor.GetHTML();
  	}catch(e){
  	}
  });
  
  //add fckeditor
  var oFCKeditor = new FCKeditor('htmlcontent','100%','400','Default');
  oFCKeditor.BasePath = "http://<?php echo CopixUrl::getRequestedBasePath();?>js/FCKeditor/";
  var html = oFCKeditor.ReplaceTextarea('htmlcontent');  
</script>