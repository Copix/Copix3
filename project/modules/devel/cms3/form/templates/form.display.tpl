
{copixhtmlheader kind='jsdomreadycode'}
{literal}
	var myAccordion = new Accordion($('accordion'), 'div.toggler', 'div.element', {
		opacity: false,
		onActive: function(toggler, element){
			toggler.setStyle('background', '#7BB802');
			toggler.setStyle('color', '#FFFFFF');
			element.setStyle('padding-top', '20px');
			element.setStyle('padding-bottom', '20px');
		},
		onBackground: function(toggler, element){
			toggler.setStyle('background', '#DBDFC9');
			toggler.setStyle('color', '#595959');
			element.setStyle('padding-top', '0px');
			element.setStyle('padding-bottom', '0px');
		}
	});
{/literal}
{/copixhtmlheader}

{copixzone process=formmenu mode='display'}

<div id="links" style="width:100%;text-align:center;">
|&nbsp;
{foreach from=$ppo->arForms key=description item=form}
	<a href="#" id="form_div_{counter name='link'}" onclick="$('toggler_{counter name='atoggler'}').fireEvent('click');">{$description}</a> &nbsp;|&nbsp;
{/foreach}
</div>

<div id="accordion">
	{foreach from=$ppo->arForms key=description item=form}
	<div>	
		<div id="toggler_{counter name='divtoggler'}" class="toggler">{$description}&nbsp;&nbsp;</div>
		<div class="element">
			{$form->getAllHTML()}
		</div>
	</div>
	{/foreach}
</div>