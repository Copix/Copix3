<table class="CopixTable">
<thead>
 <tr>
  <th><?php echo _i18n ('Id'); ?></th>
  <th><?php echo _i18n ('Type'); ?></th>
  <th><?php echo _i18n ('First occurence'); ?></th>
  <th><?php echo _i18n ('Last occurence'); ?></th>
  <th><?php echo _i18n ('Nummber of occurences'); ?></th>
  <th><?php echo _i18n ('In'); ?></th>
  <th><?php echo _i18n ('copix:Action'); ?></th>
 </tr>
</thead> 
<?php
   foreach ($ppo->arMonitored as $monitorInformation){
      echo "
      <tr"._tag ('cycle', array ('values'=>', class="alternate"')).">
        <td>".$monitorInformation->cleartypeid_cmon."</td>
        <td>".$monitorInformation->type_cmon."</td>
        <td>".CopixDateTime::yyyymmddhhiissToDateTime ($monitorInformation->datecreate_cmon)."</td>
        <td>".CopixDateTime::yyyymmddhhiissToDateTime ($monitorInformation->dateupdate_cmon)."</td>
        <td>".$monitorInformation->count_cmon."</td>
        <td>".nl2br ($monitorInformation->url_cmon)."</td>
        <td><a href=\""._url ('monitor|delete', array ('id'=>$monitorInformation->id_cmon))."\"><img src=\""._resource ('img/tools/delete.png')."\" /></a></td>        
      </tr>";
   }
?>
</table>

<?php

?>