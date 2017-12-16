<?php $val_cnt = count($data);?>

<style>
table.dataTable thead > tr > th:last-child {padding-right:0 !important; }
/*table.dataTable thead > tr > th:last-child,.table>tbody>tr>td:last-child{padding-left:0;}*/

table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after{display:none!important;}
</style>
<script type="text/javascript" charset="utf-8">
$.fn.dataTable.ext.order['dom-text'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).val();
    } );
}

/* Create an array with the values of all the input boxes in a column, parsed as numbers */
$.fn.dataTable.ext.order['dom-text-numeric'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).val() * 1;
    } );
}
 $(document).ready(function() {
   $('#kiosk_products').dataTable(
     { "lengthMenu": [ 25, 50, 100, 200 ],"paging": false,"bInfo":false,
     "columns": [
              { "orderDataType": "dom-text-numeric"}, // to sort all the input boxes in ascending or descending order
              { "orderDataType": "dom-text", type: 'string'},
              { "orderDataType": "dom-text-numeric"},
              { "orderDataType": "dom-text-numeric"},
              { "orderDataType": "dom-text-numeric"},
              { "orderDataType": "dom-text-numeric"},
              null
          ]});


});
</script>
<style>
/* to remove the search filter and data length filter for appearing before the table*/
div#kiosk_products_length.dataTables_length,div#kiosk_products_filter.dataTables_filter{display:none;}
</style>

<table class="table table-striped sortable" id="kiosk_products">
  <thead>
    <tr>
      <th style="width:30px;padding-right:10px">Position</th>
      <th style="width:35%">Item</th>
      <th>Price</th>
      <th>Capacity</th>
      <th>Par</th>
      <th>Spaces</th>
      <th><input type='checkbox' name='edit_attr' id="selectall" style="margin-right:15px;"/></th>
    </tr>
  </thead>
  <tbody>
    <?php if(is_array($data) > 0 ) {

      $cnt =1;
      $i = 0;
      foreach($data as $array){
        foreach($array as $id => $kioskData) {

            foreach($kioskData as $kioskStatusData) {
                  $allocation = isset($kioskStatusData['Active'])?$kioskStatusData['Active']:"";
        // $configuration= array();
        // foreach ($values as $value){
        //   for($j= 0; $j< count($attributes[$i]);$j++){
        //     $configuration[$attributes[$i][$j]->name] = $attributes[$i][$j]->value;
        //   }
          ?>
          <tr>
            <td><input name="position" id="position<?php echo $cnt;?>" value="<?php echo $allocation['position']; ?>"/></td>
            <td><input name="sku" id="sku<?php echo $cnt;?>" value ="<?php echo $allocation['sku-value'].' - '.$allocation['product'];?>"  autocomplete="off"/><input type="hidden" name="id_sku" id="id_sku" value="<?php echo $allocation['sku']; ?>"</td>

            <!-- <td><input name="sku_value" id="sku_value<?php echo $cnt;?>" value ="<?php echo $allocation['sku-value'];?>"/><input type="hidden" name="id_sku" id="id_sku" value="<?php echo $allocation['sku']; ?>"</td> -->
            <td><input name="price" id="price<?php echo $cnt;?>" value ="<?php echo (isset($allocation['price'])?$allocation['price']:''); ?>" /></td>
            <td><input name="capacity" id="capacity<?php echo $cnt;?>" value ="<?php echo (isset($allocation['capacity'])?$allocation['capacity']:''); ?>" /></td>
            <td><input name="par" id="par<?php echo $cnt;?>" value ="<?php echo (isset($allocation['par'])?$allocation['par']:''); ?>"/></td>
            <td><input name="space" id="space<?php echo $cnt;?>" value ="<?php echo (isset($allocation['width'])?$allocation['width']:''); ?>" /></td>
            <td><input type='checkbox'  name='edit_attri' id='edit_attr<?php echo $cnt;?>' tabindex="<?php echo $cnt;?>" /></td>
          </tr>
      <?php
      $i++;
      $cnt++;}

    }
  }

} ?>

  </tbody>
</table>
<input type="hidden" name="log" id="log" > <!-- All the table values are inserted into this hidden value as JSON -->
<script>
$(document).ready(function(){
  i=1;
  addNewRow(i);

  //if($('input[name="input_position"]'))
});
function addNewRow(i)
{

  var tab = "<?php echo $val_cnt; ?>";
  var nxt = parseInt(tab)+ parseInt(i);
  $('tbody').append('<tr><td><input type="text" name="position" id="position'+nxt+'"/></td><td><input type="text" name="sku" id="sku'+nxt+'"/><input type="hidden" name="id_sku" id="id_sku" /></td><td><input type="text" name="price" id="price'+nxt+'"/></td><td><input type="text" name="capacity" id="capacity'+nxt+'"/></td><td><input type="text" name="par" id="par'+nxt+'"/></td><td><input type="text" name="space" id="space'+nxt+'"/></td><td><input type="checkbox" name="edit_attri" id="edit_input'+nxt+'"/></td></tr>');

}
$(document).keypress(function(e){ // function to add a new row once all the values are entered in the last row and enter key is pressed
  var tab = "<?php echo $val_cnt; ?>";
  var nxt = parseInt(tab)+ parseInt(i);
  //console.log(i);
   if(e.keyCode == 13){
     event.preventDefault(e);
     var inp1 = $('#position'+nxt).val();
     var inp2 = $('#sku'+nxt).val();
    var inp3 = $('#price'+nxt).val();
     var inp4 = $('#capacity'+nxt).val();
     var inp5 = $('#par'+nxt).val();
     var inp6 = $('#space'+nxt).val();


     if((jQuery.trim(inp1).length > 0) && (jQuery.trim(inp2).length > 0)  && (jQuery.trim(inp3).length > 0) && (jQuery.trim(inp4).length > 0) && (jQuery.trim(inp5).length > 0) && (jQuery.trim(inp6).length > 0)  )
      {
        i=i+1;
        addNewRow(i);

      }
    }
 });
</script>
<script>
$('#selectall').click(function(){
  $(':checkbox').prop('checked', this.checked);
  });

</script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
 <script>
 $( function(){

   $( "input[name='sku']" ).autocomplete({
       source: function (request, response) {
        jQuery.get(appPath + "/Products/getActiveProducts", {
            term: request.term
        },
        function (data) {
          response($.map(data, function (item) {
                          return {
                              label: item.sku_value +' - '+ item.name,
                              val:  item.id,
                              id: item.id
                          }
        }))
    },
  'json');
  },
       minLength: 2,
       dataType: "json",
      // cache: false,
       select:function(event,ui){
          $(this).siblings('#id_sku').val(ui.item.id);
          //console.log(ui.item.id);
       }
     });
 });
 </script>
