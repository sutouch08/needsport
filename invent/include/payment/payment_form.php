
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
<div class="row">
  <div class="col-xs-12 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4 ">
    <div class="row">
      <div class="col-sm-12">
        <h4 class="text-center">กรุณาเลือกช่องทางการชำระเงิน</h4>
      </div>

    </div><!-- row -->

<?php if(dbNumRows($bank) > 0) : ?>
  <div class="row">
<?php   while ($rs = dbFetchObject($bank)) : ?>
      <div class="col-sm-12" style="padding-top:15px; padding-bottom:15px; border-top:solid 1px #ccc; ">
        <table style="width:100%; border:0px;">
          <tr>
            <td width="25%" style="vertical-align:text-top;">
              <img src="<?php echo bankLogoUrl($rs->bankcode); ?>" height="50px"/>
            </td>
            <td>
              <?php echo $rs->bank_name; ?>
              สาขา  <?php echo $rs->branch; ?> <br/>
              เลขที่บัญชี <?php echo $rs->acc_no; ?> <br/>
              ชื่อบัญชี  <?php echo $rs->acc_name; ?> <br/>
              <button type="button" class="btn btn-lg btn-primary btn-block margin-top-10"  onClick="payOnThis(<?php echo $id_order; ?>,<?php echo $rs->id_account; ?>)">
                ชำระด้วยช่องทางนี้
              </button>
            </td>
          </tr>
        </table>
      </div>
<?php   endwhile; ?>
  </div>
<?php else : ?>
    <hr/>
    <div class="row">
      <div class="col-sm-12">
        <div class="alert alert-danger" role="alert">
          <h4 class="text-center">ไม่พบช่องทางการชำระเงิน<br/>กรุณาติดต่อเจ้าหน้าที่</h4>
        </div>
      </div>
    </div>
<?php endif; ?>


  </div>
</div>
<script>
function payOnThis(id_order, id_account){
  window.location.href = "<?php echo WEB_ROOT; ?>payment.php?id_order="+id_order+"&id_account="+id_account;
}

</script>

</div><!--/ contaianer -->
</body>
</html>
