{mootools plugin="selectbetweenslider"}
<h2>Avec un select simple</h2>
<p>{select name=select11 values="1;2;3;4;5"|toarray emptyShow=false} à {select name=select12 selected=2 values="1;2;3;4;5"|toarray emptyShow=false}</p>

<pre>
{literal}
{copixhtmlheader kind="jsDomReadyCode"}
   var slider1 = new SelectBetweenSlider($$('#select11','#select12'));
{/copixhtmlheader}
{/literal}
</pre>

{copixhtmlheader kind="jsDomReadyCode"}
{literal}
   var slider1 = new SelectBetweenSlider($$('#select11','#select12'), {'hideSelect':false, 'showLegend':true});
{/literal}
{/copixhtmlheader}