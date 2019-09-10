<?php
	$page_name 	= "ตรวจสอบยอดชำระเงิน";
	$id_tab 			= 62;
	$id_profile 		= $_COOKIE['profile_id'];
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	include 'function/bank_helper.php';
	include 'function/order_helper.php';

?>

<div class="container">
<?php if(isset($_GET['validate'])) : ?>
<?php
$sCode = getFilter('sCode', 'sCode', '');
$fromDate = getFilter('fromDate', 'fromDate', '');
$toDate = getFilter('toDate', 'toDate', '');
 ?>
<div class="row top-row">
	<div class="col-sm-6 top-col">
		<h4 class="title">รายการที่ยืนยันแล้ว</h4>
	</div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()">
				<i class="fa fa-arrow-left"></i> กลับ
			</button>
		</p>
	</div>
</div>
<hr style="margin-bottom:15px;" />
<form id="searchForm" method="post">
<div class="row">
	<div class="col-sm-3 padding-5 first">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center search-box" name="sCode" id="sCode" value="<?php echo $sCode; ?>" />
	</div>
	<div class="col-sm-2 paddng-5">
		<label class="display-block">วันที่</label>
		<input type="text" class="form-control input-sm text-center input-discount" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" />
		<input type="text" class="form-control input-sm text-center input-unit" name="toDate" id="toDate" value="<?php echo $toDate; ?>" />
	</div>
	<div class="col-sm-1 padding-5">
		<label class="display-block not-show">search</label>
		<button type="button" class="btn btn-sm btn-primary btn-block"  onclick="getSearch()">ค้นหา</button>
	</div>
	<div class="col-sm-1 padding-5">
		<label class="display-block not-show">reset</label>
		<button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()">Reset</button>
	</div>
</div>
</form>

<hr style="margin-bottom:15px;" />
<?php
	$where = "WHERE p.valid = 1 ";

	if($sCode != '')
	{
		createCookie('sCode', $sCode);
		$where .= "AND o.reference LIKE '%".$sCode."%' ";
	}

	if($fromDate != '' && $toDate != '')
	{
		createCookie('fromDate', $fromDate);
		createCookie('toDate', $toDate);
		$where .= "AND p.date_add >= '".fromDate($fromDate)."' ";
		$where .= "AND p.date_add <= '".toDate($toDate)."' ";
	}

	$where .= "ORDER BY p.date_add DESC";

	$qx = "SELECT p.*, o.reference, o.id_employee, ol.customer FROM ";
	$qr = "tbl_payment AS p ";
	$qr .= "LEFT JOIN tbl_order AS o ON p.id_order = o.id_order ";
	$qr .= "LEFT JOIN tbl_order_online AS ol ON o.id_order = ol.id_order ";

	$paginator	= new paginator();
	$get_rows	= get_rows();
	$paginator->Per_Page($qr, $where, $get_rows);
	$paginator->display($get_rows, 'index.php?content=payment_order&validated=Y');
	$qx = $qx . $qr . $where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page;
	$qs = dbQuery($qx);
 ?>
<div class="row">
	<div class="col-sm-12">
	<table class="table" style="border:solid 1px #ccc;">
		<table class="table" style="border:solid 1px #ccc;">
			<thead>
	    	<tr class="font-size-10">
	        <th class="width-5 text-center">No.</th>
					<th class="width-10 text-center">วันที่</th>
	        <th class="width-15">Order No.</th>
	        <th class="">ลูกค้า</th>
	        <th class="width-8 text-center">ยอดชำระ</th>
	        <th class="width-8 text-center">ยอดโอน</th>
	        <th class="width-10 text-center">เลขที่บัญชี</th>
	        <th class="width-10 text-right"></th>
	      </tr>
	    </thead>
  <tbody id="orderTable">
		<?php if( dbNumRows($qs) > 0 ) : ?>
		<?php	$no = row_no(); 	?>
		<?php	while( $rs = dbFetchObject($qs) ) : ?>
		<?php		$bank = getBankAccount($rs->id_account); ?>
					<tr style="font-size:12px;" id="<?php echo $rs->id_order; ?>">
		        <td class="text-center"><?php echo $no; ?></td>
						<td class="text-center"><?php echo thaiDate($rs->date_add); ?></td>
		        <td><?php echo $rs->reference; ?></td>
		        <td><?php echo $rs->customer; ?></td>
		        <td class="text-center"><?php echo number_format($rs->order_amount, 2); ?></td>
		        <td class="text-center"><?php echo number_format($rs->pay_amount, 2); ?></td>
		        <td class="text-center"><?php echo $bank['acc_no']; ?></td>
		        <td class="text-right">
		        	<button type="button" class="btn btn-xs btn-warning" onclick="viewValidDetail(<?php echo $rs->id_order; ?>)"><i class="fa fa-eye"></i></button>
							<?php if($delete) : ?>
		          <button type="button" class="btn btn-xs btn-danger" onclick="removeValidPayment(<?php echo $rs->id_order; ?>, '<?php echo $rs->reference; ?>')">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
		        </td>
		      </tr>
		<?php	$no++;	?>
		<?php 	endwhile; ?>

		<?php else : ?>
				<tr><td colspan="10" class="text-center"><h4>ไม่พบรายการ</h4></td></tr>
		<?php endif; ?>
	</tbody>
    </table>
    </div>
</div>



<?php else : ?>
<div class="row top-row">
	<div class="col-sm-6 top-col">
    	<h4 class="title"><i class="fa fa-exclamation-triangle"></i>&nbsp;<?php echo $page_name; ?></h4>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-primary" onClick="reloadOrderTable()">
					<i class="fa fa-refresh"></i> โหลดรายการ
				</button>
				<button type="button" class="btn btn-sm btn-info" onclick="goConfirmed()">
					<i class="fa fa-check"></i> รายการที่ยืนยันแล้ว
				</button>
      </p>
    </div>
</div><!-- / row -->

<hr style="margin-bottom:15px;" />

<div class="row">
	<div class="col-sm-12">
	<table class="table" style="border:solid 1px #ccc;">
            <thead>
            	<tr style="font-size:11px;">
                <th style='width:10%;'>เลขที่อ้างอิง</th>
                <th style='width:12%;'>ลูกค้า</th>
                <th style='width:8%; text-align:center;'>ค่าสินค้า</th>
                <th style='width:8%; text-align:center;'>ค่าจัดส่ง</th>
                <th style='width:8%; text-align:center;'>อื่นๆ</th>
                <th style='width:8%; text-align:center;'>ยอดชำระ</th>
                <th style='width:8%; text-align:center;'>ยอดโอน</th>
                <th style='width:10%; text-align:center;'>ธนาคาร</th>
                <th style='width:10%; text-align:center;'>เลขที่บัญชี</th>
                <th style='width:12%; text-align:center;'>เวลาโอน</th>
                <th style='text-align:center;'></th>
                </tr>
            </thead>
        <tbody id="orderTable">   </tbody>
    </table>
    </div>
</div>

<script>
$(document).ready(function(e) {
		reloadOrderTable();
});

setInterval(function(){ reloadOrderTable(); }, 1000*60);
</script>
<?php endif; ?>
<div class='modal fade' id='confirmModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:350px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            </div>
            <div class='modal-body' id="detailBody">

            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id='imageModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:500px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>
            </div>
            <div class='modal-body' id="imageBody">

            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>

</div><!--/ container -->

<script id="detailTemplate" type="text/x-handlebars-template">
<div class="row">
	<div class="col-sm-12 text-center">ข้อมูลการชำระเงิน</div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-4 label-left">ยอดที่ต้องชำระ :</div><div class="col-sm-8">{{ orderAmount }}</div>
	<div class="col-sm-4 label-left">ยอดโอนชำระ : </div><div class="col-sm-8"><span style="font-weight:bold; color:#E9573F;">฿ {{ payAmount }}</span></div>
	<div class="col-sm-4 label-left">วันที่โอน : </div><div class="col-sm-8">{{ payDate }}</div>
	<div class="col-sm-4 label-left">ธนาคาร : </div><div class="col-sm-8">{{ bankName }}</div>
	<div class="col-sm-4 label-left">สาขา : </div><div class="col-sm-8">{{ branch }}</div>
	<div class="col-sm-4 label-left">เลขที่บัญชี : </div><div class="col-sm-8"><span style="font-weight:bold; color:#E9573F;">{{ accNo }}</span></div>
	<div class="col-sm-4 label-left">ชื่อบัญชี : </div><div class="col-sm-8">{{ accName }}</div>
	{{#if imageUrl}}
		<div class="col-sm-12 top-row top-col text-center"><a href="javascript:void(0)" onClick="viewImage('{{ imageUrl }}')">รูปสลิปแนบ <i class="fa fa-paperclip fa-rotate-90"></i></a> </div>
	{{else}}
		<div class="col-sm-12 top-row top-col text-center">---  ไม่พบไฟล์แนบ  ---</div>
	{{/if}}
	{{#unless valid}}
		<div class="col-sm-12 top-col"><button type="button" class="btn btn-warning btn-block" onClick="confirmPayment({{ id }})"><i class="fa fa-check-circle"></i> ยืนยันการชำระเงิน</button></div>
	{{/unless}}
</div>
</script>
<script id="orderTableTemplate" type="text/x-handlebars-template">
{{#each this}}
<tr id="{{ id }}" style="font-size:12px;">
<td> {{ reference }}</td>
<td>{{ customer }}</td>
<td align="center">{{ orderAmount }}</td>
<td align="center">{{ deliveryAmount }}</td>
<td align="center">{{ serviceAmount }}</td>
<td align="center">{{ totalAmount }}</td>
<td align="center">{{ payAmount }}</td>
<td align="center">{{ bankName }}</td>
<td align="center">{{ accNo }}</td>
<td align="center">{{ payDate }}</td>
<td align="center">
	<button type="button" class="btn btn-xs btn-warning" onClick="viewDetail({{ id }})"><i class="fa fa-eye"></i></button>
	<button type="button" class="btn btn-xs btn-danger" onClick="removePayment({{ id }})"><i class="fa fa-trash"></i></button>
 </td>
</tr>
{{/each}}
</script>
<script>

$('#fromDate').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd){
		$('#toDate').datepicker('option', 'minDate', sd);
	}
});

$('#toDate').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd){
		$('#fromDate').datepicker('option', 'maxDate', sd);
	}
});


function goBack(){
	window.location.href = "index.php?content=payment_order";
}

function removePayment(id_order)
{
	swal({
		title: 'ต้องการลบ ?',
		text: 'คุณแน่ใจว่าต้องการลบรายการนี้ ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/paymentController.php?removePayment",
				type:"POST", cache:"false", data:{ "id_order" : id_order },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
						$("#"+id_order).remove();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ หรือ มีการยืนยันการชำระเงินแล้ว", "error");
					}
				}
			});
		});
}



function viewValidDetail(id_order)
{
	load_in();
	$.ajax({
		url:"controller/paymentController.php?viewPaymentDetail",
		type:"POST",
		cache:"false",
		data:{
			"id_order" : id_order
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูล หรือ การชำระเงินถูกยืนยันไปแล้ว', 'error');
			}else{
				var source 	= $("#detailTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#detailBody");
				render(source, data, output);
				$("#confirmModal").modal('show');
			}
		}
	});
}



function removeValidPayment(id_order)
{
	swal({
		title: 'ต้องการลบ ?',
		text: 'คุณแน่ใจว่าต้องการลบรายการนี้ ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/paymentController.php?removeValidPayment",
				type:"POST",
				cache:"false",
				data:{
					"id_order" : id_order
				},
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title : "สำเร็จ",
							text: "ลบรายการเรียบร้อยแล้ว",
							timer: 1000,
							type: "success"
						});

						$("#"+id_order).remove();

					}else{
						swal("ข้อผิดพลาด!!", rs, "error");
					}
				}
			});
		});
}

function confirmPayment(id_order)
{
	$("#confirmModal").modal('hide');
	load_in();
	$.ajax({
		url:"controller/paymentController.php?confirmPayment",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title : 'เรียบร้อย', text: '', timer: 1000, type: 'success' });
				$("#"+id_order).remove();
			}else{
				swal("ข้อผิดพลาด", "ยืนยันการชำระเงินไม่สำเร็จ", "error");
			}
		}
	});
}


function viewImage(imageUrl)
{
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}


function viewDetail(id_order)
{
	load_in();
	$.ajax({
		url:"controller/paymentController.php?getPaymentDetail",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูล หรือ การชำระเงินถูกยืนยันไปแล้ว', 'error');
			}else{
				var source 	= $("#detailTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#detailBody");
				render(source, data, output);
				$("#confirmModal").modal('show');
			}
		}
	});
}

function goConfirmed(){
	window.location.href = "index.php?content=payment_order&validate=Y";
}


function reloadOrderTable()
{
	load_in();
	$.ajax({
		url:"controller/paymentController.php?getOrderTable",
		type:"GET", cache: false, success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs != 'fail' )
			{
				var source 	= $("#orderTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#orderTable");
				render(source, data, output);
			}else{
				$("#orderTable").html('<tr><td colspan="11" align="center"><strong>ไม่พบรายการรอตรวจสอบ</strong></td></tr>');
			}
		}
	});
}

function getSearch(){
	$('#searchForm').submit();
}

$('.search-box').keyup(function(event) {
	if(event.keyCode == 13){
		getSearch();
	}

});

function clearFilter(){
	$.get('controller/paymentController.php?clearFilter',function(){
		goConfirmed();
	});
}
</script>
