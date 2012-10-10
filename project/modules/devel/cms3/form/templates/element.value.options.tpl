

<script type="text/javascript">
    var myNewBlocSlide;
	var myUpdateBlocSlide;
	/**
	 *	Affichage du bloc d'ajout
	 */
	function showNewDiv() {ldelim} myNewBlocSlide.slideIn(); {rdelim}
	function hideNewDiv() {ldelim} myNewBlocSlide.slideOut(); {rdelim}
	function showUpdateDiv() {ldelim} myUpdateBlocSlide.slideIn(); {rdelim}
	function hideUpdateDiv() {ldelim} myUpdateBlocSlide.slideOut(); {rdelim}
	
	function updateBloc (idBloc)
	{ldelim}
		var myHTMLRequest = new Request.HTML({ldelim}
			url:'{copixurl dest="adminajax|getUpdateBlocDiv"}',
			method: 'get',
			update: 'update_bloc_div',
			evalScripts: true, 
			onSuccess: showUpdateDiv
		{rdelim});
		myHTMLRequest.send('id_bloc=' + idBloc);
		return false;
	{rdelim}
</script>

{copixhtmlheader kind="JSDomReadyCode"}

	//Initialisation des effets
	myNewBlocSlide = new Fx.Slide('new_bloc_div');
	myNewBlocSlide.slideOut();
	
	myUpdateBlocSlide = new Fx.Slide('update_bloc_div');
	myUpdateBlocSlide.slideOut();

	$('add_bloc_link').addEvent('click',
		function () {ldelim}
			var myHTMLRequest = new Request.HTML({ldelim}
				url:'{copixurl dest="adminajax|getNewBlocDiv"}',
				'update': 'new_bloc_div',
				'evalScripts':true, 
				'onSuccess':showNewDiv
			{rdelim});
			myHTMLRequest.get();
			return false;
		{rdelim}
	);

{/copixhtmlheader}


<h1>Paramétrage de l'option : "{$ppo->cfev_value}"</h1>

<p class="copix_help">
    <img src="{copixresource path='form|img/help.png'}" class="p_icon"/>
    Cet écran vous permet de paramétrer le comportement du formulaire lorsque l'utilisateur choisi l'option <em>"{$ppo->cfev_value}"</em>.<br/>
    Il vous permet de définir les champs à afficher lorsque cette option est sélectionnée.
    <br/><br/>
    Afin de mieux gérer ce comportement, vous devez dans un premier temps regrouper ces éléments en bloc.
    Ensuite choisissez le bloc qui sera relié à l'option.
    <br class="clear"/>
</p>

<form id="form_blocs" class="div_element_list" action="{copixurl dest='form|admin|setValueOption'}">
	<input type="hidden" name="cfev_id" value="{$ppo->cfev_id}" />
	<h3>Liste des blocs disponibles</h3>
	<div id="blocs_list">
	{section name=blocs loop=$ppo->arFormBlocs}
		<div id="form_bloc_line_{$ppo->arFormBlocs[blocs]->cfb_id}" class="form_bloc">
			<span class="form_field_chk">
				<input id="cb_form_bloc_{$ppo->arFormBlocs[blocs]->cfb_id}" name="form_bloc" type='radio' 
					   value="{$ppo->arFormBlocs[blocs]->cfb_id}" onclick="$('disable_form_bloc').checked = false;"
					   {if $ppo->arFormBlocs[blocs]->cfb_id == $ppo->cfev_id_bloc_to_display} checked="checked"{/if} />
			</span>
			<div id="cfb_label_{$ppo->arFormBlocs[blocs]->cfb_id}" class="form_bloc_label" onclick="updateBloc({$ppo->arFormBlocs[blocs]->cfb_id})">
				{$ppo->arFormBlocs[blocs]->cfb_nom}
			</div>
		</div>
	{sectionelse}
		<p>Aucun bloc disponible.</p>
	{/section}
	</div>
	<div class="flor clear">
		<input type="button" value="Valider" id="submitbutton" class="flor"/>
		<div class="flor">
			<span>
				<input id="parent_adopt" name="parent_adopt" type='checkbox' {if $ppo->cfev_parent_adopt == 1} checked="checked"{/if} value="1"/>
			</span>
			<label for="parent_adopt">Afficher directement le bloc aprés l'élément</label>
			
			<span>
				<input id="disable_form_bloc" name="disable_form_bloc" type='checkbox' {if $ppo->cfev_id_bloc_to_display == null} checked="checked"{/if} value="1"/>
			</span>
			<label for="disable_form_bloc">Aucun bloc dynamique</label>
		</div>
	</div>
	<div class="clear"></div>
</form>


<div id="update_bloc_div">{$updatebloc_div}</div>

<div id="addbloc_btn" class="ajoutPortlet clear" style="margin: 10px 0px;">
	<a title="" id="add_bloc_link" href="{copixurl dest='form|admin|getValueOption id=$ppo->cfev_id mode=add_bloc}">
		<img alt="" src="{copixresource path='img/tools/add.png'}"/>Ajouter un bloc
	</a>
</div>
<div id="new_bloc_div">{$newbloc_div}</div>

<script type="text/javascript">
if( $defined( parent ) && $defined( parent.TB_remove ) ){ldelim}
	$('submitbutton').addEvent('click', function(){ldelim}
		$('form_blocs').submit ();
		parent.TB_remove ();
	{rdelim});
{rdelim}
</script>



