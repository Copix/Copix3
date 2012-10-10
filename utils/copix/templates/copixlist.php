<?php
$tpl = new CopixTpl ();
$tpl->assign ('listid', $idlist);
$pager = $tpl->fetch ('copix:/templates/pager.php');
echo $pager;
?>
<table id="tab_<?php echo $idlist; ?>" <?php echo (isset($class)) ? ' class="'.$class.'"' : ''; ?>>
	<thead>
	<tr>
<?php 
     foreach ($mapping as $key=>$field) { ?>
			<th style='cursor:pointer' onClick="javascript:list.get('<?php echo $idlist; ?>').orderby('<?php echo $key ?>');" ?>
			<?php echo $field; ?>
			</th>
			<?php if (isset($more)) { echo '<th>'.$moretitle.'</th>'; } ?>
<?php } ?>
<?php	if (isset($editLink)) { echo "<th></th>"; } ?> 
	</tr>
	</thead>
	<tbody>
<?php if (count ($results)>0) { ?>
<?php foreach ($results as $key=>$result) { ?>
		<tr <?php echo ($key%2==1) ? 'class="alternate"' : ''; ?> >

    <?php foreach ($mapping as $key=>$field) { ?>
    				<td><?php echo $result->$key; ?></td>
    <?php } ?>
    <?php if (isset($more)) { echo '<td>'.$more.'</td>'; } ?>
<?php
        if (isset($editLink)) {
        $params = array ();
            foreach ($editLinkPk as $pk) {
                $params[$pk] = $result->$pk;
            }
            echo "<td>";
            echo '<a href="';
            echo _url($editLink,$params);
            echo '" ><img src="'._resource('img/tools/update.png').'" /></a>';
            if ($delete) {
                echo '<a href="';
                echo _url($editLink,array_merge($params,array('delete'=>true)));
                echo '" ><img src="'._resource('img/tools/delete.png').'" /></a>';
                
            }
            if (isset($moreLink)) {
                echo '<a href="'._url($moreLink,$params).'">'.$moreLinkTitle.'</a>';    
            }
            echo "</td>";
        }
?>
		</tr>
<?php  } //foreach ?>
<?php } else {
        echo '<tr><td colspan="'.count($mapping).'">'._i18n('copix:copixlist.message.zero').'</td></tr>'; 
      }
?>
	<tbody>
</table>
<?php echo $pager; ?>