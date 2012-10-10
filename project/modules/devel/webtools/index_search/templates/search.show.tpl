<h1 class="hascorners">{i18n key="index_search.title.show"}<span class="cornertopleft"></span><span class="cornerbottomright"></span></h1>

{copixzone process="indexsearchform" criteria=$ppo->criteria standalone="non" path=$ppo->path theme=$ppo->theme}

{copixzone process="indexsearchresults" criteria=$ppo->criteria path=$ppo->path theme=$ppo->theme page=$ppo->currentPage}