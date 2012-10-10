<table id="tab_<?php echo $idlist; ?>" <?php echo (isset($class)) ? ' class="'.$class.'"' : ''; ?>>
	<thead>
	<tr>
<?php 
     foreach ($mapping as $field) { ?>
			<th>
			<?php echo $field; ?>
			</th>
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
