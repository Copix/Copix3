<?php 
function buildMenu($ptree, $pCurrentLevel, $pMaxLevel, $pElementsTypes, $pFirst){
	if($pCurrentLevel <$pMaxLevel){
		echo "<ul class='".(!$pFirst ? "sortableul" : "menuroot")."' rel='".(empty($ptree) ? '' : current($ptree)->parent_heading_public_id_hei)."'>";
		foreach ($ptree as $item){
			$visibility_inherited_from = false;
			$visibility = _ioClass("heading|headingelementinformationservices")->getVisibility ($item->public_id_hei, $visibility_inherited_from);
			echo '<li class="'.($visibility ? 'visible' : 'invisible').'">';
			echo '<input rel="visibility_inherited_from" type="hidden" name="elements['.$item->public_id_hei.'][visibility_inherited_from]" value="'.($visibility_inherited_from === false ? '' : $visibility_inherited_from).'" id="element_visibility_inherited_from_'.$item->public_id_hei.'" />';
			echo '<input type="hidden" name="elements['.$item->public_id_hei.'][visibility]" value="'.$visibility.'" id="element_visibility_'.$item->public_id_hei.'" />';
			echo '<input type="hidden" name="elements['.$item->public_id_hei.'][order]" value="'.$item->display_order_hei.'" id="element_order_'.$item->public_id_hei.'" />';
			echo '<input type="hidden" name="elements['.$item->public_id_hei.'][parent]" value="'.$item->parent_heading_public_id_hei.'" id="element_parent_'.$item->public_id_hei.'" />';
			echo '<img src="'._resource ($pElementsTypes[$item->type_hei]["icon"]).'" /> '.$item->caption_hei;
			echo '<a type="'.$item->type_hei.'" rel="'.$item->public_id_hei.'" class="clickervisibility menuelement'.($visibility ? 'visible' : 'invisible').'"  href="javascript:;">&nbsp;</a> ';
			echo '</li>';
			if (isset($item->children) || $item->type_hei == 'heading'){
				echo "</ul><div id='bloc_heading_$item->public_id_hei' style='margin-left:30px;'>";
				if(isset($item->children)){
					buildMenu($item->children, $pCurrentLevel +1, $pMaxLevel, $pElementsTypes, false);
				} else {
					echo "<ul class='sortableul' rel='".$item->public_id_hei."'><li rel='vide' class='invisible'><em>Rubrique Vide</em></li></ul>";
				}
				echo "</div><ul class='sortableul' rel='".$item->parent_heading_public_id_hei."'>";
			}
		}
		echo "</ul>";
	}
}
?>
<p>Seuls les éléments publiés et visibles peuvent apparaître dans les menus.</p>
<br />
<div id="menupreview">
	<?php 
	buildMenu($tree, 0, 4, $elementsTypes, true); ?>		
</div>
<?php 
CopixHTMLHeader::addJSDOMReadyCode("
  		var sb = new Sortables($$('.sortableul'), {
		    clone:true,
		    revert: true,
		    onStart: function(el) { 
		    	if ($('bloc_heading_'+el.getFirst('a').get('rel'))){
		    		$('bloc_heading_'+el.getFirst('a').get('rel')).setStyle('display', 'none');
		    	}
		      	el.addClass('draggedmenuelement');
		    },
		    onSort:function(el, clone){
		    	//
		    },
		    onComplete:function (el){
			    //on supprime la class de dragg
		    	el.removeClass('draggedmenuelement');
		    	//on supprime les styles inline rajoutés par Sortables
		    	el.erase('style');
		    	var firsta = el.getFirst('a');
		    	
		    	//si on a deplacé une rubrique, on deplace son bloc d'enfant 
				if ($('bloc_heading_'+firsta.get('rel'))){
					var arNextLi = el.getAllNext('li');
					if (!(arNextLi.length == 1 && arNextLi[0].get('rel') == 'vide') && arNextLi.length>0){
						var ul = new Element('ul', {class:'sortableul', rel:el.getParent().get('rel')});
						ul.inject(el.getParent(), 'after');
						arNextLi.each(function(li){
							ul.adopt(li);
						});
						this.addLists(ul);
					}
					$('bloc_heading_'+firsta.get('rel')).inject(el.getParent(), 'after');
		    		$('bloc_heading_'+firsta.get('rel')).setStyle('display', '');
		    	}
		    	
		    	//on arrive dans une rubrique vide : on supprime l'element vide
		    	if(el.getParent().getFirst('li[rel=vide]')){
		    		el.getParent().getFirst('li[rel=vide]').dispose();
		    	}
		    	
		    	//on vient d'une rubrique qui devient vide, on ajoute un element vide dans la rubrique
		    	var oldParent = $('element_parent_'+firsta.get('rel')).value;
		    	if(oldParent != el.getParent().get('rel')){
		    		var uls = $('menupreview').getElements('ul[rel='+oldParent+']');
		    		if(uls.length == 1){
		    			if(uls[0].getChildren().length == 0){
			    			var li = new Element('li', {'rel':'vide', class:'invisible'});
			    			li.innerHTML = '<em>Rubrique vide</em>';
			    			uls[0].adopt(li);
						}
		    		}
		    	}
		    	
		    	//on change le parent
		    	$('element_parent_'+firsta.get('rel')).value = el.getParent().get('rel');
		    	//on vérifie l'heritage
		    	if ($('element_visibility_inherited_from_'+firsta.get('rel')).value != ''){
		    		$('element_visibility_inherited_from_'+firsta.get('rel')).value = el.getParent().get('rel');
		    		$('menupreview').getElements('a[rel='+el.getParent().get('rel')+']').each(function(el){
		    			firsta.removeClass('menuelementvisible');
		    			firsta.removeClass('menuelementinvisible');
		    			firsta.addClass(el.hasClass('menuelementvisible') ? 'menuelementinvisible' : 'menuelementvisible');
		    			checkElementMenuVisibility(firsta);
		    		});
		    	}
		    	
		    	//on met à jour les ordres
		    	var ordre = 1;
		    	$('menupreview').getElements('ul[rel='+$('element_parent_'+firsta.get('rel')).value+']').each(function(elem){
			    	var children = elem.getChildren();
			    	for(i=0;i<children.length;i++){
			    		$('element_order_'+children[i].getFirst('a').get('rel')).value = ordre;
			    		ordre++;
			    	}
		    	});
		    }
		});
		
		function checkElementMenuVisibility(el){
			if (el.hasClass('menuelementvisible')){
				el.removeClass('menuelementvisible');
				el.addClass('menuelementinvisible');
				el.getParent().removeClass('visible');
				el.getParent().addClass('invisible');
			} else {
				el.removeClass('menuelementinvisible');
				el.addClass('menuelementvisible');
				el.getParent().removeClass('invisible');
				el.getParent().addClass('visible');
			}
		}
		
		$$('.clickervisibility').each(function(el){
			el.addEvent('click', function(){
				checkElementMenuVisibility(el);
				$('element_visibility_inherited_from_'+el.get('rel')).value= '';
				$('element_visibility_'+el.get('rel')).value = el.hasClass('menuelementvisible') ? 1 : 0;
				if(el.get('type') == 'heading'){
					var inputs = $('menupreview').getElements('input[rel=visibility_inherited_from]').filter(function(item){
						return (item.get('value')==el.get('rel')); 
					});
					inputs.each(function(elem){
						checkElementMenuVisibility(elem.getParent().getFirst('a'));
					});
				}
			});
		});
		");
	?>