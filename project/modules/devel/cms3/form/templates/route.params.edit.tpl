<script>
var formIdentifiantObserver = new CopixFormObserver ('form_route_params', 
	{ldelim}
		onChanged : function (){ldelim}
			var myHTMLRequest = new Request.HTML({ldelim}url:'{copixurl dest="adminajax|updateRouteParams"}'{rdelim});
			myHTMLRequest.get($('form_route_params'));
		{rdelim},
		checkIntervall :50
	{rdelim}
);
</script>

{$ppo->form->getAllHTML()}