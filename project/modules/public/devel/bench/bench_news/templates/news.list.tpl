{foreach from=$ppo->arNews item=newsElement}
<h1>{$newsElement->title_bench_news}</h1>
<h2>Le {$newsElement->datetime_bench_news} par {$newsElement->author_bench_news}</h2>
<div>{$newsElement->content_bench_news}</div>
{/foreach}