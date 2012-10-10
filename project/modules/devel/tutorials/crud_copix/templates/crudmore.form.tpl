{copixform_start form='crud_form_more' dao='tutorial_crud_copix' mode="edit" action='crud_copix||crudmore'}
<table class="CopixVerticalTable">
     <tr>
         <th style="width:150px">Libellé</th>
         <td>{copixform_field field='caption_crud' type='crud_copix|field::caption'}</td>
     </tr>
     <tr>
         <th>Description</th>
         <td>{copixform_field field='description_crud' type='textarea' valid='crud_copix|field::validDescription'}</td>
     </tr>
</table>
{copixform_button type="submit"}<img src="{copixresource path='img/tools/valid.png'}" />{/copixform_button}
{copixform_end}<input type="button" value="Retour" onClick='location.href="{copixurl dest=crud_copix||crudmore}"' />
