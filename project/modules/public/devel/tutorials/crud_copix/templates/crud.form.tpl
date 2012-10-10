{copixform_start form='crud_form' dao='tutorial_crud_copix' mode="edit" validUrl='crud_copix||'}
<table class="CopixVerticalTable">
     <tr>
         <th style="width:150px">Libellé</th>
         <td>{copixform_field field='caption_crud' type='varchar'}</td>
     </tr>
     <tr>
         <th>Description</th>
         <td>{copixform_field field='description_crud' type='textarea'}</td>
     </tr>
</table>
{copixform_end}
