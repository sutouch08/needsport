
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.png">

    <title><?php echo COMPANY; ?> : confirm payment</title>

    <!-- core CSS -->
    <link href="library/css/bootstrap.css" rel="stylesheet" />
    <link href="library/css/template.css" rel="stylesheet" />
    <link href="library/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
    <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/sweet-alert.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT; ?>library/css/sweet-alert.css">
    <style>
	    .ui-autocomplete { 	height: 400px; overflow-y: scroll; overflow-x: hidden; }
	  </style>

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

<body>
<div class="container">
<div class="row margin-top-15 margin-bottom-15">
  <div class="col-xs-12 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4 ">
    <!-------------  แจ้งชำระเงิน  --------->
    <form id="paymentForm" name="paymentForm" enctype="multipart/form-data" method="post">
    <input type="hidden" name="id_order" id="id_order" value="<?php echo $id_order; ?>" />
    <input type="hidden" name="id_account" id="id_account" value="<?php echo $_GET['id_account']; ?>"/>
    <input type="hidden" name="orderAmount" id="orderAmount" value="<?php echo $payAmount; ?>" />
    <input type="file" name="image" id="image" accept="image/*" style="display:none;" />
    <input type="hidden" name="bade_url" id="base_url" value="<?php echo $base_url; ?>" />
    <input type="hidden" name="customer_id" id="customer_id" value="0" />


      <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
        <span style="font-size:25px; color:#75ce66;">ยอดเงิน : <?php echo number_format($payAmount, 2); ?> ฿ </span>
      </div>
      <div class="col-sm-12" style="border-bottom:solid 1px #eee;">
        <table class="table margin-bottom-10">
          <tr>
            <td style="width:50px; border:0px;"><img src="<?php echo bankLogoUrl($account['bankcode']); ?>" height="50px"/></td>
            <td style="border:0px;">
              <?php echo $account['bank_name']; ?>
              สาขา  <?php echo $account['branch']; ?> <br/>
              เลขที่บัญชี <?php echo $account['acc_no']; ?> <br/>
              ชื่อบัญชี  <?php echo $account['acc_name']; ?> <br/>
            </td>

          </tr>
        </table>
      </div>

    <div class="col-sm-12 margin-top-15" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
     <div class="row">
        <div class="col-sm-12 margin-bottom-15">
          <div id="block-image" style="opacity:0;">
            <div id="previewImg" ></div>
            <span onClick="removeFile()" style="position:absolute; right:0px; top:0px; cursor:pointer; color:red;">
              <i class="fa fa-times fa-2x"></i>
            </span>
          </div>
          <button type="button" class="btn btn-lg btn-block btn-default" id="btn-select-file" onClick="selectFile()">
            <i class="fa fa-file-image-o"></i> แนบสลิป
          </button>
        </div>

        <div class="col-sm-12">
          <label class="display-block">ยอดเงิน(บาท)</label>
          <input type="number" class="form-control input-lg text-center" name="payAmount" id="payAmount" value="<?php echo $payAmount; ?>" />
        </div>

        <div class="col-sm-12 margin-top-15">
          <label class="display-block">วันที่</label>
          <div class="input-group">
            <input type="text" class="form-control input-lg text-center" name="payDate" id="payDate" readonly="true" value="<?php echo date('d/m/Y'); ?>" />
            <span class="input-group-btn">
              <button type="button" class="btn btn-lg btn-default" style="line-height:1.35;" onClick="dateClick()">
                <i class="fa fa-calendar"></i>
              </button>
            </span>
          </div>
        </div>

        <?php $hour = date('h'); ?>
        <?php $min = date('i'); ?>
        <div class="col-sm-12 margin-top-15">
          <label class="display-block">เวลา</label>
          <table class="table">
            <tr>
              <td class="width-50" style="padding:0px 0px 8px 0px; border:0px;">
                <select id="payHour" name="payHour" class="form-control input-lg"><?php echo selectHour($hour); ?></select>
              </td>
              <td class="middle text-center" style="padding:0px 8px 8px 8px; border:0px;">:</td>
              <td class="width-50" style="padding:0px 0px 8px 0px; border:0px;">
                <select id="payMin" name="payMin" class="form-control input-lg"><?php echo selectMin($min); ?></select>
              </td>
            </tr>
          </table>
        </div>

        <div class="col-sm-12 margin-top-15">
          <button type="button" class="btn btn-lg btn-primary btn-block" onClick="submitPayment()" >
            ดำเนินการ
          </button>
        </div>

       </div><!--/ row -->
   </div>

  <div class="col-sm-12">
    <button type="button" class="btn btn-lg btn-warning btn-block" onclick="goBack()"><i class="fa fa-arrow-left"></i>&nbsp; ย้อนกลับ</button>
  </div>

  </form>
  </div>
</div>
<script src="invent/script/payment.js?<?php echo date('dmY'); ?>"></script>
<script src="invent/script/template.js?<?php echo date('dmY'); ?>"></script>
<script>

function goBack(){
  var id_order = $('#id_order').val();
  window.location.href = "<?php echo $base_url; ?>"+"payment.php?id_order="+id_order;
}

function submitPayment()
{
  //swal('submit');
  //return false;
	var id_order	= $("#id_order").val();
	var id_account	= $("#id_account").val();
	var image		= $("#image")[0].files.length;

	var payAmount	= $("#payAmount").val();
	var orderAmount = $("#orderAmount").val();
	var payDate		= $("#payDate").val();
	var payHour		= $("#payHour").val();
	var payMin		= $("#payMin").val();
  var customer_id = $('#customer_id').val();

	if( id_order == '' ){
    swal('ข้อผิดพลาด', 'ไม่พบไอดีออเดอร์กรุณาออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง', 'error');
    return false;
  }

	if( id_account == '' ){
    swal('ข้อผิดพลาด', 'ไม่พบข้อมูลบัญชีธนาคาร กรุณาออกจากหน้านี้แล้วลองแจ้งชำระอีกครั้ง', 'error');
    return false;
  }


	if( image === 0 ){
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
  fd.append('customer_id', customer_id);


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

				setTimeout(function(){
          window.location.href = $('#base_url').val()+"addressForm.php?id_order="+id_order;
        }, 1500);

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

</script>
</div><!--/ contaianer -->
</body>
</html>
