<?php 
	//include("function/tools.php");
	$page_menu = "invent_stock_report";
	$page_name = "รายงานสินค้าคงเหลือ";
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class='fa fa-tags'></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-6">
        	<p class='pull-right' style="margin-bottom:0px;">
            	<button type='button' class='btn btn-success btn-sm' id='show_all'>แสดงทั้งหมด</button>
                <button type='button' class='btn btn-primary btn-sm' id='instock'>เฉพาะที่มียอด</button>
                <button type='button' class='btn btn-danger btn-sm' id='non_stock'>เฉพาะที่ไม่มียอด</button>
           </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />

<div class='row'>
	<div class='col-lg-12' id='result'>
	</div>
</div>

</div>
<script>
function getData(id_product){
	$.ajax({
		url:"controller/reportController.php?getData&id_product="+id_product,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}

function getData1(id_product){
	$.ajax({
		url:"controller/reportController.php?getData&id_product="+id_product,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal1").css("width",table_w+"px");
				$("#modal_title1").html(title);
				$("#modal_body1").html(data);
				$("#btn_toggle1").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
$("#show_all").click(function(e) {
    var option = "show_all";
	get_report(option);
});
$("#instock").click(function(e) {
    var option = "in_stock";
	get_report(option);
});
$("#non_stock").click(function(e) {
    var option = "non_stock";
	get_report(option);
});

function get_report(option){
	//$("#result").html("");
	$("#result").animate({opacity:0.0},300);		
	load_in();
	$.ajax({
		url:"controller/reportController.php?get_stock&option="+option, type:"GET", cache:false,
		success: function(data){
			$("#result").html(data);
			$("#result").animate({opacity:1},300);	
			load_out();
		}
	});
}
</script>
