<div id="Page">

<table width="100%" summary="">
 <tr valign="top">
  <td width="50%">
     <div class="portlet" id="portlet1">
        	<h2>Portlet 1</h2>
	        <p>Lorem ipsum bla bla je sais plus ce qu'il y a écrit dans ces <br /> trucs la normalement, <strong>alors</strong> on fait sans </p>
	        <p>Lorem ipsum bla bla je sais plus ce qu'il y a écrit dans ces <br /> trucs la normalement, alors <strong>pouet</strong>.</p>
     </div>
     <div class="portlet" id="portlet3">
			<div class="editPortlet"><a href="#">{copixicon type="update"}</a></div>
        	<h2>Portlet 3</h2>
	        <p>Lorem ipsum bla bla je sais plus ce qu'il y a écrit dans ces <br /> trucs la normalement, <strong>alors</strong> on fait sans </p>
	        <p>Lorem ipsum bla bla je sais plus ce qu'il y a écrit dans ces <br /> trucs la normalement, alors <strong>pouet</strong>.</p>
     </div>
     <div class="ajoutPortlet">
        <a href="#">Ajouter une portlet</a>
     </div>
  </td>

  <td width="50%">
     <div class="portlet" id="portlet2">
        	<h2>Portlet 2</h2>
	        <p>Lorem ipsum bla bla je sais plus ce qu'il y a écrit dans ces <br /> trucs la normalement, <strong>alors</strong> on fait sans </p>
	        <p>Lorem ipsum bla bla je sais plus ce qu'il y a écrit dans ces <br /> trucs la normalement, alors <strong>pouet</strong>.</p>
    </div>
     <div class="ajoutPortlet">
        <a href="#">Ajouter une portlet</a>
     </div>
  </td>
 </tr>
</table>
</div>


{copixhtmlheader kind="jsdomreadycode"}
{literal}
var options = {
	handleText: 'Déplacer',
	onDragover: function(el){console.log('dragover '+el.className)},
    onDragend: function(element, position, droppable){
    	console.log( element, position, droppable );
    }
};
$$('.portlet').each (function (portlet){
	new DraggablePortlet( portlet, options);
});
{/literal}
{/copixhtmlheader}