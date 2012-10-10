
{mootools plugins='mootblsort;smoothbox'}

{copixzone process=formmenu mode='content'}
<h1>Contenu du formulaire</h1>

<div id="form_params_div">
	{copixzone process=formparametrage}
</div>
<div id="form_fields_div">
	{copixzone process=formfields}
</div>

<script type="text/javascript">
	var myTblSort;
	var myNewElementSlide;
	var myUpdateElementSlide;
</script>

{copixhtmlheader kind='jsdomreadycode'}
	
	/**
	 *	Affichage du block de mise à jour d'un élément
	 */
	function showNewDiv()
	{ldelim}
		myNewElementSlide.slideIn();
	{rdelim}
	
	//Gestion du bouton ajouter un champ
	$('addfield_btn').addEvent('click', function () {ldelim}
		//On supprime le formulaire d'update pour éviter les conflits d'id et éclaircir
		myUpdateElementSlide.slideOut();
		$('update_element_div').innerHTML = '';
	
		var ajax = new Request.HTML ({ldelim}
							 'url' : '{copixurl dest="adminajax|newelement"}', 
							 'evalScripts':true, 
							 'update':'newelement_div',
							 'onSuccess' : showNewDiv
							 {rdelim}
		).post ();
	{rdelim});
	
	//Initialisation des effets
	myNewElementSlide = new Fx.Slide('newelement_div');
	myNewElementSlide.slideOut();
	
	myUpdateElementSlide = new Fx.Slide('update_element_div');
	myUpdateElementSlide.slideOut();
	
{/copixhtmlheader}

<div id="addfield_btn" class="ajoutPortlet clear" style="margin: 10px 0px;">
	<a title="" id="" href="javascript:void (null);">
		<img alt="" src="{copixresource path='img/tools/add.png'}"/>Ajouter un champ
	</a>
</div>
<div id="newelement_div"></div>
