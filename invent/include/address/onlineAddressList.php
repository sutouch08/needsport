
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.png">

    <title><?php echo COMPANY; ?> : สมุดที่อยู่</title>

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
<?php $ado = getOnlineAddress($id_order); ?>
  <div class="col-xs-12 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4 ">
    <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
      <h4 class="text-center">สมุดที่อยู่</h4>
    </div>
  <?php if($ado !== FALSE) : ?>
    <?php $countAddress = dbNumRows($ado); ?>
    <?php while($rs = dbFetchObject($ado)) : ?>
    <div class="col-sm-12 margin-bottom-15" style="border-bottom:solid 1px #eee;">
      <table class="table margin-bottom-10">
        <tr class="font-size-14 blod">
          <td style="border:0px;"><?php echo $rs->first_name.' '.$rs->last_name; ?></td>
        </tr>
        <tr class="font-size-14 blod">
          <td style="border:0px;"><?php echo $rs->phone; ?></td>
        </tr>
        <tr class="font-size-10" style="color:#888;">
          <td style="border:0px;"><?php echo $rs->address1; ?></td>
        </tr>
        <tr class="font-size-10" style="color:#888;">
          <td style="border:0px;"><?php echo $rs->address2; ?></td>
        </tr>
        <tr class="font-size-10" style="color:#888;">
          <td style="border:0px;"><?php echo $rs->province.'  '.$rs->postcode ?></td>
        </tr>
        <tr>
          <td style="border:0px;">
            <?php if($countAddress > 0 && $rs->is_default == 0) : ?>
              <button type="button" class="btn btn-sm btn-succcess" onclick="setDefault(<?php echo $rs->id_address; ?>,'<?php echo $rs->customer_code; ?>')">
                ตั้งค่าเป็นที่อยู่เริ่มต้นสำหรับจัดส่งสินค้า
              </button>
            <?php else : ?>
              <span class="label label-success">ที่อยู่เริ่มต้นสำหรับจัดส่งสินค้า</span>
              <button type="button" class="btn btn-sm btn-warning pull-right" onclick="editAddress(<?php echo $rs->id_address; ?>, '<?php echo $rs->customer_code; ?>')">
                <i class="fa fa-pencil"></i>
              </button>
            <?php endif; ?>
          </td>
        </tr>
      </table>
    </div>
  <?php endwhile; ?>
<?php endif; ?>
    <div class="col-sm-12">
      <button type="button" class="btn btn-lg btn-primary btn-block" onclick="addNewAddress()">เพิ่มที่อยู่ใหม่</button>
    </div>
  </div>
</div>
<script src="invent/script/address.js?<?php echo date('dmY'); ?>"></script>
<script src="invent/script/template.js?<?php echo date('dmY'); ?>"></script>
<script>

</script>
</div><!--/ contaianer -->
</body>
</html>
