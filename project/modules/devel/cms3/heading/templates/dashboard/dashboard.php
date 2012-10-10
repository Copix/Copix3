<div id="dashboardContent">
	<?php 
	$margin = "margin-right: 20px;";
	switch ($ppo->columns) {
		case 1 :
			$style = "width:100%;";
			break;
		case 2 :
			$style = "width:49%;";
			break;
		case 3 :
			$style = "width:32%;";
			break;
	}
	
	$i = 0;
	foreach ($ppo->positions as $id=>$column){
		if ($i >= $ppo->columns){
			break;
		}
		$i++;
		echo "<div id='$id' class='dashboardColumn' style='".$style.($i < $ppo->columns ? $margin : '')."'>";
		foreach ($column as $idZone){
			echo $ppo->zones[$idZone];
		}
		echo "</div>"; 
	}
	 
	CopixHTMLHeader::addJSDOMReadyCode("
	
		$$('.cmsbloc').each(function (el){
			el.toggleClass('cmsdroppablewidget');
			createDraggableWidget(el, 'handle'+el.id);
			el.addEvent('mouseover', function(){
				$('showdiv'+ el.id).setStyle ('display','inline');
			});
			el.addEvent('mouseout', function(){
				$('showdiv'+ el.id).setStyle ('display','none');
			});
		});
		
		$ ('cols').addEvent ('change', function (pEvent) {
			if($('exCols').value != pEvent.target.value){
				$('exCols').value = pEvent.target.value;
				if (pEvent.target.value == 1){
					var column1 = new Element('div', {'id':'column1', 'class':'dashboardColumn', styles:{width:'100%'}});
					$$('.cmsbloc').each(function(el){
						el.inject(column1);
					});
					$('dashboardContent').getElements('.dashboardColumn').each(function(el){
						el.dispose();
					});
					column1.inject($('dashboardContent'));
				}
				else if (pEvent.target.value == 2){
					var column1 = new Element('div', {'id':'column1', 'class':'dashboardColumn', styles:{'width':'49%', 'margin-right':'20px'}});
					var hiddenWidget = new Element('div', {'class':'cmshiddenwidget cmsdroppablewidget'});
					var column2 = new Element('div', {'id':'column2', 'class':'dashboardColumn', styles:{'width':'49%'}});
					column2.innerHTML = '<div class=\'cmshiddenwidget cmsdroppablewidget\'></div>';
					$$('.cmsbloc').each(function(el){
						el.inject(column1);
					});
					hiddenWidget.inject(column1);
					$('dashboardContent').getElements('.dashboardColumn').each(function(el){
						el.dispose();
					});
					column1.inject($('dashboardContent'));
					column2.inject($('dashboardContent'));
				}
				else if (pEvent.target.value == 3){
					var column1 = new Element('div', {'id':'column1', 'class':'dashboardColumn', styles:{'width':'32%', 'margin-right':'20px'}});
					var column2 = new Element('div', {'id':'column2', 'class':'dashboardColumn', styles:{'width':'32%', 'margin-right':'20px'}});
					column2.innerHTML = '<div class=\'cmshiddenwidget cmsdroppablewidget\'></div>';
					var column3 = new Element('div', {'id':'column3', 'class':'dashboardColumn', styles:{'width':'32%'}});
					column3.innerHTML = '<div class=\'cmshiddenwidget cmsdroppablewidget\'></div>';
					var hiddenWidget = new Element('div', {'class':'cmshiddenwidget cmsdroppablewidget'});
					$$('.cmsbloc').each(function(el){
						el.inject(column1);
					});
					hiddenWidget.inject(column1);
					$('dashboardContent').getElements('.dashboardColumn').each(function(el){
						el.dispose();
					});
					column1.inject($('dashboardContent'));
					column2.inject($('dashboardContent'));
					column3.inject($('dashboardContent'));
				}

				Copix.savePreference ('heading|dashBoardColumns', pEvent.target.value);
			}
		});
	");
	?>
</div>