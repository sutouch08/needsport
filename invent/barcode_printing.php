<?php
require '../library/config.php';
require '../library/functions.php';
require "function/tools.php";
require "function/sponsor_helper.php";
require "function/support_helper.php";
?>

<!DOCTYPE HTML>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="../favicon.ico" />
    <title>ทดสอบระบบ</title>

    <!-- Core CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/paginator.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/bootflat.min.css" rel="stylesheet">
     <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
     <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>

  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>



    <!-- SB Admin CSS - Include with every page
    <link href="<?php echo WEB_ROOT; ?>library/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/template.css" rel="stylesheet">-->
</head>

<body>
<div class="container">
  <!--
<div class="row">
    <div class="col-sm-3">
        <label>บาร์โค้ด</label>
        <input type="text" class="form-control" id="input-barcode" placeholder="ใส่รหัสบาร์โค้ดที่ต้องการพิมพ์"/>
    </div>
    <div class="col-sm-3">
        <label>Text</label>
        <input type="text" class="form-control" id="input-name" placeholder="ใส่ตัวอักษรที่ต้องการพิมพ์"/>
    </div>
    <div class="col-sm-2">
        <label style="display:block; visibility: hidden;">button</label>
        <button type="button" class="btn btn-primary" onclick="addToList()">เพิ่มในรายการ</button>
    </div>
</div>
-->
<form id="genForm" method="post">
<div class="row hidden-print">
  <div class="col-sm-2">
      <label>Prefix</label>
      <input type="text" class="form-control" id="input-prefix" name="prefix" placeholder=""/>
  </div>
    <div class="col-sm-3">
        <label>ตัวเลขเริ่มต้น</label>
        <input type="text" class="form-control" id="input-start" name="start" placeholder=""/>
    </div>
    <div class="col-sm-3">
        <label>ตัวเลขสิ้นสุด</label>
        <input type="text" class="form-control" id="input-end" name="end" placeholder=""/>
    </div>
    <div class="col-sm-2">
        <label style="display:block; visibility: hidden;">button</label>
        <button type="button" class="btn btn-primary" onclick="genBarcode()">สร้างบาร์โค้ด</button>
    </div>
</div>
</form>
<hr class="hidden-print"/>
<div class="row">
<div class="col-sm-12">
<table class="table">
    <tbody id="rs">
      <!--
      <td id="field-2" style="width:25%; text-align:center;"></td>
      <td id="field-3" style="width:25%; text-align:center;"></td>
      <td id="field-3" style="width:25%; text-align:center;"></td>
      <td id="field-4" style="width:25%; text-align:center;"></td>
    -->
<?php

        $letter = isset($_POST['prefix']) ? $_POST['prefix'] : '';
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $end = isset($_POST['end']) ? $_POST['end'] : 0;
        $c = 1;
        if($start > 1 && $end >= $start)
        {
          while($start <= $end)
          {
            if($c == 1)
            {
              echo '<tr>';
            }

            echo '<td style="width:25%; text-align:center;">';
            echo '<img src="../library/class/barcode/barcode.php?text='.$letter.$start.'" style="height:25mm;">';
            //echo '<center>'.$letter.$start.'</center>';
            echo '</td>';

            $c++;
            if($c > 4)
            {
              echo '</tr>';
              $c = 1;
            }

            $start++;

          }
        }

        ?>

    </tbody>
</table>

</div>
</div>


</div>


    <script>
        var td = 1;
        function addToList(){
            var barcode = $("#input-barcode").val();
            var name  = $("#input-name").val();
            if( barcode != '' && name != ''){
                if(td >4){
                    td = 1
                }

                var temp = '<img src="../library/class/barcode/barcode.php?text='+barcode+'" style="height:25mm;"><center>'+name+'</center>';
                $("#field-"+td).append(temp);
                td++;
            }
        }

        function genBarcode(){
          var prefix = $('#input-prefix').val();
          var start = $('#input-start').val();
          var end = $('#input-end').val();

          if(start < 1){
            swal('ค่าเริ่มต้นต้องมากกว่า 0');
            return false;
          }

          if(end < 1){
            swal('ค่าสิ้นสุดต้องมากกว่า 0');
            return false;
          }

          $('#genForm').submit();
        }
    </script>

</body>

</html>
