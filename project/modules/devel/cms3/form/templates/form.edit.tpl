{mootools plugins="copixformobserver"}
{copixhtmlheader kind="JSDomReadyCode"}

var formIdentifiantObserver = new CopixFormObserver ('cms_form', 
	{ldelim}
		onChanged : function (){ldelim}
			var myHTMLRequest = new Request.HTML({ldelim}url:'{copixurl dest="adminajax|updateCmsForm"}'{rdelim});
			myHTMLRequest.get($('cms_form'));
		{rdelim},
		checkIntervall :50
	{rdelim}
);

$('cf_route').addEvent('change',
	function () {ldelim}
		var routeParamsRequest = new Request.HTML(
			{ldelim}
				url:'{copixurl dest="adminajax|getRouteParams"}',
				method : 'get',
				evalScripts:true,
				update : 'route_params_div'
			{rdelim}
		);
		routeParamsRequest.send('cf_route=' + $('cf_route').value + '&editId={"editId"|request}');
	{rdelim}
);
if ($('form_route_params')) {ldelim}
	var formIdentifiantObserver = new CopixFormObserver ('form_route_params', 
		{ldelim}
			onChanged : function (){ldelim}
				var myHTMLRequest = new Request.HTML({ldelim}url:'{copixurl dest="adminajax|updateRouteParams"}'{rdelim});
				myHTMLRequest.get($('form_route_params'));
			{rdelim},
			checkIntervall :50
		{rdelim}
	);
{rdelim}
{/copixhtmlheader}


{copixzone process=formmenu}

{$ppo->form->getAllHTML()}

<div id="route_params_div">
	&nbsp;
	{if $ppo->route_form}
		{$ppo->route_form->getAllHTML()}
	{/if}
	
</div>