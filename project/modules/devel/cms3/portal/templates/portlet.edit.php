<form id="formPortlet" action="<?php echo _url ('portal|adminportlet|valid', array ('editId' => _request ('editId'))); ?>" method="POST">
	<input type="hidden" name="publish" id="publish" />
	<input type="hidden" name="published_date" id="published_date" />
	<input type="hidden" name="end_published_date" id="end_published_date" />
</form>
<?php echo $ppo->editedElement->render (RendererMode::HTML, $ppo->renderContext, array ('editId' => $ppo->editId)) ?>
<div style="clear: both;"></div>
<center><?php echo CopixZone::process ('portal|HeadingElementPortletMenu', array ('renderContext' => $ppo->renderContext, 'id_portlet'=>$ppo->editedElement->id_portlet)) ?></center>