<?php 
	$page_menu = "invent_stock_report";
	$page_name = "รายงานสินค้าคงเหลือ";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='index.php?content=stock_report&stock_report=y' method='post'>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-list"></span>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
      	<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' id='report'><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button></a></li>
		<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' id='gogo'><span class='fa fa-file-excel-o' style='color:#5cb85c; font-size:35px;'></span><br />ส่งออก</button></a></li>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<!-- End page place holder -->
<?php 
	echo"
<div class='row'>
	<div class='col-lg-6'>
    <table width='100%' style='border-right:0px'>
    <tr><td colspan='4' style='text-align:center; border-bottom:1px solid #CCC; padding:10px;'><h4 style='margin:0px;'>สินค้า</h4></td></tr>
    <tr><td style='width:20%; padding-top:10px;'><input type='radio' name='product' id='product1' value='0' checked='checked' /><label for='product1' style='padding-left:15px;'>ทั้งหมด</label></td><td colspan='3' style='padding-right:10px;'></td></tr>
    <tr><td style='width:20%; padding-top:10px;'><input type='radio' name='product' id='product2' value='1'/><label for='product2' style='padding-left:15px; margin-right:10px;'>จาก</label></td>
    	  <td style='width:35%; padding-top:10px;'><input type='hidden' name='product_from' id='product_from' /><input type='text' class='form-control' id='txt_product_from' disabled /></td>
         <td style='width:10%; text-align: center; padding-top:10px;'>ถึง</td>
          <td style='width:35%; padding-top:10px;'><input type='hidden' name='product_to' id='product_to' /><input type='text' class='form-control' id='txt_product_to' disabled /></td>
	</tr>
     <tr>
     		<td style='width:20%; padding-top:10px;'><input type='radio' name='product' id='product3' value='2'/><label for='product3' style='padding-left:15px;'>เฉพาะ</label></td>
            <td colspan ='3' style='width:30%; padding-right:10px; padding-top:10px;'><input type='hidden' name='product_selected' id='product_selected' /><input type='text' class='form-control' id='txt_product_selected' disabled /></td>	
  </tr>
</table>
	</div>
    <div class='col-lg-3'>
    <table width='100%' style='border-right:0px'>
    <tr><td colspan='2' style='text-align:center; border-bottom:1px solid #CCC; padding:10px;'><h4 style='margin:0px;'>คลัง</h4></td></tr>
	<tr><td colspan='2' style='width:100%; padding-top:10px;'><input type='radio' name='warehouse' id='warehouse1' value='0'/><label for='warehouse1' style='padding-left:15px;'>ทุกคลัง</label></td></tr>
    <tr><td style='width:30%; padding-top:10px;'><input type='radio' name='warehouse' id='warehouse2' value='1' checked='checked' /><label for='warehouse2' style='padding-left:15px;'>เฉพาะ :</label></td>
    	  <td style='width:80%; padding-left:10px; padding-top:10px;'><select name='warehouse_selected' id='warehouse_selected' class='form-control' >"; warehouseList(); echo"</select></td>	
    </tr>
    </table>
    </div>
    <div class='col-lg-3'>
    <table width='100%' style='border-right:0px'>
    <tr><td colspan='2' style='text-align:center; border-bottom:1px solid #CCC; padding:10px;'><h4 style='margin:0px;'>วันที่</h4></td></tr>
    <tr><td colspan='2' style='width:100%; padding-top:10px;'><input type='radio' name='date' id='date1' value='0' checked='checked' /><label for='date1' style='padding-left:15px;'>ปัจจุบัน</label></td></tr>
    <tr><td style='width:40%; padding-top:10px;'><input type='radio' name='date' id='date2' value='1' /><label for='date2' style='padding-left:15px;'>ณ วันที่ :</label></td>
    	  <td style='width:60%; padding-left:10px; padding-top:10px;'><input type='text' name='date_selected' id='date_selected' class='form-control input-sm' required='required'disabled='disabled' /></td>	
    </tr>
    </table>
    </form>
    </div>
</div> ";	
?>   
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<div class='row'>
	<div class='col-lg-12' id='result'></div>
    </div>
</div>     <input type='hidden' id='warehouse' value='1' /><input type='hidden' id='product' value='0' /><input type='hidden' id='view' value='0' />
<script>
$(document).ready(function(e) {
    $("#txt_product_from").autocomplete({
		source:"controller/orderController.php?product_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#txt_product_from").val();
			var arr = data.split(':');
			//var id = arr[0];
			var name = arr[1];
			$("#product_from").val(name);
			$(this).val(name);
		}
	});			
});
$(document).ready(function(e) {
    $("#txt_product_to").autocomplete({
		source:"controller/orderController.php?product_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#txt_product_to").val();
			var arr = data.split(':');
			//var id = arr[0];
			var name = arr[1];
			$("#product_to").val(name);
			$(this).val(name);
		}
	});			
});
$(document).ready(function(e) {
   $("#txt_product_selected").autocomplete({
		source:"controller/orderController.php?product_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#txt_product_selected").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[1];
			$("#product_selected").val(id);
			$(this).val(name);
		}
	});			
});
$(document).ready(function() {
   	    $("#product1").change(function(){
		$("#product").val(0);
		$("#txt_product_from").attr("disabled","disabled");
		$("#txt_product_to").attr("disabled","disabled");
		$("#txt_product_selected").attr("disabled","disabled");
	});
});

$(document).ready(function() {
   	    $("#product2").change(function(){
		$("#product").val(1);
		$("#txt_product_from").removeAttr("disabled");
		$("#txt_product_to").removeAttr("disabled");
		$("#txt_product_selected").attr("disabled","disabled");
		
	});
});

$(document).ready(function() {
   	    $("#product3").change(function(){
		$("#product").val(2);
		$("#txt_product_from").attr("disabled","disabled");
		$("#txt_product_to").attr("disabled","disabled");
		$("#txt_product_selected").removeAttr("disabled");
	});
});
$(document).ready(function() {
   	    $("#warehouse1").change(function(){
		$("#warehouse_selected").attr("disabled","disabled");
		$("#warehouse").val(0);
	});
});
$(document).ready(function() {
   	    $("#warehouse2").change(function(){
		$("#warehouse_selected").removeAttr("disabled");
		$("#warehouse").val(1);
	});
});
$(document).ready(function() {
   	    $("#date2").click(function(){
		$("#date_selected").removeAttr("disabled");
		$("#view").val(1);
	});
});
$(document).ready(function() {
   	    $("#date1").click(function(){
		$("#date_selected").attr("disabled","disabled");
		$("#view").val(0);
	});
});
$(function() {
    $("#date_selected").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
$(document).ready(function(e) {
    $("#gogo").click(function(){	
		var product = $("#product").val();
	var warehouse = $("#warehouse").val();
	var view = $("#view").val();
	if(view ==0){
		var view_report = 'now';
	}else if(view ==1){
		var view_select = $("#date_selected").val();
		if(view_select ==""){
			alert("กรุณาเลือกวันที่ต้องการดูรายงาน");
		}
		var view_report = "view_selected="+view_select;
	}else{
		var view_report = 'now';
	}
	if(product ==0){
		var product_rank = 'product_all';
	}else if(product ==1){
		var product_from = $("#product_from").val();
		var product_to = $("#product_to").val();
		var product_rank = "product_from="+product_from+"&product_to="+product_to;
	}else if(product==2){
		var product_selected = $("#product_selected").val();
		var product_rank = "product_selected="+product_selected+"&product_code_selected="+$("#txt_product_selected").val();
	}else{
		var product_rank = "product_all";
	}
	if(warehouse ==0){
		var warehouse_report = 'warehouse_all';
	}else if(warehouse ==1){
		var warehouse_select = $("#warehouse_selected").val();
		var warehouse_report = "warehouse_selected="+warehouse_select;	
	}else{
		var warehouse_report = 'warehouse_all';
	}
	$("#report_form").attr("action","controller/reportController.php?export_stock_report&view="+view+"&"+view_report+"&product="+product+"&"+product_rank+"&warehouse="+warehouse+"&"+warehouse_report );
	$(this).attr("type", "submit");
});
});

$(document).ready(function(e) {
    $("#report").click(function(e) {
		get_report();
    });
});
function get_report(){
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังประมวลผล....</h4></td></tr></table>");
	var product = $("#product").val();
	var warehouse = $("#warehouse").val();
	var view = $("#view").val();
	if(view ==0){
		var view_report = 'now';
	}else if(view ==1){
		var view_select = $("#date_selected").val();
		if(view_select ==""){
			alert("กรุณาเลือกวันที่ต้องการดูรายงาน");
		}
		var view_report = "view_selected="+view_select;
	}else{
		var view_report = 'now';
	}
	if(product ==0){
		var product_rank = 'product_all';
	}else if(product ==1){
		var product_from = $("#product_from").val();
		var product_to = $("#product_to").val();
		var product_rank = "product_from="+product_from+"&product_to="+product_to;
	}else if(product==2){
		var product_selected = $("#product_selected").val();
		var product_rank = "product_selected="+product_selected+"&product_code_selected="+$("#txt_product_selected").val();
	}else{
		var product_rank = "product_all";
	}
	if(warehouse ==0){
		var warehouse_report = 'warehouse_all';
	}else if(warehouse ==1){
		var warehouse_select = $("#warehouse_selected").val();
		var warehouse_report = "warehouse_selected="+warehouse_select;	
	}else{
		var warehouse_report = 'warehouse_all';
	}
	$.ajax({
		url:"controller/reportController.php?stock_report&view="+view+"&"+view_report+"&product="+product+"&"+product_rank+"&warehouse="+warehouse+"&"+warehouse_report , type:"GET",cache:false,
		success: function(dataset){
			$("#result").html(dataset);
		}
	});
		
}

</script>