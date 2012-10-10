{mootools plugin="selectslider"}
<h2>On définit avant tout la longueur des selects</h2>
{copixhtmlheader kind="style"}
{literal}
.slider_parent {
   width: 200px;
}
.span_tic {
   border-color: #999999;
}

{/literal}
{/copixhtmlheader}

<pre>
{literal}
{copixhtmlheader kind="style"}
.slider_parent {
   width: 200px;
}
{/copixhtmlheader}
{/literal}
</pre>

<h2>Avec un select simple</h2>
<p>{select name=select1 values="1;2;3;4;5"|toarray emptyShow=false}</p>

<pre>
{literal}
{copixhtmlheader kind="jsDomReadyCode"}
   var slider1 = new SelectSlider($('select1'));
{/copixhtmlheader}
{/literal}
</pre>

{copixhtmlheader kind="jsDomReadyCode"}
   var slider1 = new SelectSlider($('select1'));
{/copixhtmlheader}

<h2>Avec un select simple (masque du select)</h2>
<p>{select name=select2 values="1;2;3;4;5"|toarray emptyShow=false}</p>

<pre>
{literal}
{copixhtmlheader kind="jsDomReadyCode"}
   var slider2 = new SelectSlider($('select2'), {hideSelect: true});
{/copixhtmlheader}
{/literal}
</pre>

{copixhtmlheader kind="jsDomReadyCode"}
{literal}
   var slider2 = new SelectSlider($('select2'), {hideSelect: true});
{/literal}
{/copixhtmlheader}

<h2>Affichage les légendes</h2>
<p>{select name=select3 values="1=>Premier;2=>Second;3=>Troisième;4=>Quatrième"|toarray emptyShow=false}</p>

<pre>
{literal}
{copixhtmlheader kind="jsDomReadyCode"}
   var slider3 = new SelectSlider($('select3'), {hideSelect: true, showLegend : true});
{/copixhtmlheader}
{/literal}
</pre>

{copixhtmlheader kind="jsDomReadyCode"}
{literal}
   var slider3 = new SelectSlider($('select3'), {hideSelect: true, showLegend : true});
{/literal}
{/copixhtmlheader}

<h2>Affichage des "titles"</h2>
<p>{select name=select4 values="1=>Premier;2=>Second;3=>Troisième;4=>Quatrième"|toarray emptyShow=false}</p>

<pre>
{literal}
{copixhtmlheader kind="jsDomReadyCode"}
   var slider4 = new SelectSlider($('select4'), {hideSelect: true, showLegend : true, showTitle : true});
{/copixhtmlheader}
{/literal}
</pre>

{copixhtmlheader kind="jsDomReadyCode"}
{literal}
   var slider4 = new SelectSlider($('select4'), {hideSelect: true, showLegend : true, showTitle : true});
{/literal}
{/copixhtmlheader}

<h2>Affichage des "ticks"</h2>
<p>{select name=select5 values="1=>Premier;2=>Second;3=>Troisième;4=>Quatrième"|toarray emptyShow=false}</p>

<pre>
{literal}
{copixhtmlheader kind="jsDomReadyCode"}
   var slider5 = new SelectSlider($('select5'), {hideSelect: true, showLegend : true, showTick : false, snap : true});
{/copixhtmlheader}
{/literal}
</pre>

{copixhtmlheader kind="jsDomReadyCode"}
{literal}
   var slider5 = new SelectSlider($('select5'), {hideSelect: true, showLegend : true, showTick : false, snap : true});
{/literal}
{/copixhtmlheader}