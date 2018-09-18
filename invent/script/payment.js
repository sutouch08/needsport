/*
function submitPayment()
{
  swal('submit');
  return false;
	var id_order	= $("#id_order").val();
	var id_account	= $("#id_account").val();
	var image		= $("#image")[0].files[0];
	var payAmount	= $("#payAmount").val();
	var orderAmount = $("#orderAmount").val();
	var payDate		= $("#payDate").val();
	var payHour		= $("#payHour").val();
	var payMin		= $("#payMin").val();

	if( id_order == '' ){
    swal('ข้อผิดพลาด', 'ไม่พบไอดีออเดอร์กรุณาออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง', 'error');
    return false;
  }

	if( id_account == '' ){
    swal('ข้อผิดพลาด', 'ไม่พบข้อมูลบัญชีธนาคาร กรุณาออกจากหน้านี้แล้วลองแจ้งชำระอีกครั้ง', 'error');
    return false;
  }


	if( image == '' ){
    swal('ข้อผิดพลาด', 'ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง', 'error');
    return false;
  }


	if( payAmount == 0 || isNaN( parseFloat(payAmount) ) || parseFloat(payAmount) < parseFloat(orderAmount) ){
    swal("ข้อผิดพลาด", "ยอดชำระไม่ถูกต้อง", 'error');
     return false;
   }


	if( !isDate(payDate) ){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }


	$("#paymentModal").modal('hide');

	var fd = new FormData();

	fd.append('image', $('input[type=file]')[0].files[0]);
	fd.append('id_order', id_order);
	fd.append('id_account', id_account);
	fd.append('payAmount', payAmount);
	fd.append('orderAmount', orderAmount);
	fd.append('payDate', payDate);
	fd.append('payHour', payHour);
	fd.append('payMin', payMin);

	load_in();
	$.ajax({
		url:"invent/controller/orderController.php?confirmPayment",
		type:"POST",
    cache: "false",
    data: fd,
    processData:false,
    contentType: false,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({
          title : 'สำเร็จ',
          text : 'แจ้งชำระเงินเรียบร้อยแล้ว',
          type: 'success',
          timer: 1000 }
        );

				//window.location.href = $('#base_url').val()+"addressFrom.php?id_order="+id_order;
			}
			else if( rs == 'fail' )
			{
				swal("ข้อผิดพลาด", "ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง", "error");
			}
			else
			{
				swal("ข้อผิดพลาด", rs, "error");
			}
		}
	});
}

*/




function readURL(input)
{
   if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#previewImg').html('<img id="previewImg" src="'+e.target.result+'" style="max-width:100%;" alt="รูปสลิปของคุณ" />');
        }
        reader.readAsDataURL(input.files[0]);
    }
}





$("#image").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;
		if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
		{
			swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
			$(this).val('');
			return false;
		}
		if( size > 2000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 2 MB", "error");
			$(this).val('');
			return false;
		}
		readURL(this);
		$("#btn-select-file").css("display", "none");
		$("#block-image").animate({opacity:1}, 1000);
	}
});



function clearPaymentForm()
{
	$("#id_account").val('');
	$("#payAmount").val('');
	$("#payDate").val('');
	$("#payHour").val('00');
	$("#payMin").val('00');
	removeFile();
}



function removeFile()
{
	$("#previewImg").html('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');
	$("#image").val('');
}




$("#payAmount").focusout(function(e) {
	if( $(this).val() != '' && isNaN(parseFloat($(this).val())) )
	{
		swal('กรุณาระบุยอดเงินเป็นตัวเลขเท่านั้น');
	}
});




$('#payDate').datepicker({
  'dateFormat' : 'dd/mm/yy',
  'ignoreReadonly' : true,
  'allowInputToggle' : true
});




$('#payDate').focusout(function(){
  $('#payDate').datepicker('hide');
});




//--- เมื่อคลิกปุ่มปฏิทิน
function dateClick(){
  $('#payDate').focus();
}


function selectFile()
{
	$("#image").click();
}
