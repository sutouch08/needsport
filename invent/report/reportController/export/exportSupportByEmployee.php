<?php

require "../../../../library/config.php";
require "../../../../library/functions.php";
require "../../../function/tools.php";
require "../../../function/report_helper.php";


if(isset($_GET['export_support_by_employee']) && isset($_GET['view']) )
{
	$view  		= $_GET['view'];
	$rank	 		= $_GET['rank'];
	$em_rank 	= $_GET['employee_rank'];
	if($em_rank == 1 ){ $id_employee = $_GET['employee_id']; $qr = "id_employee = ".$id_employee; $em_title = employee_name($id_employee); }else{ $id_employee = ""; $qr = "id_employee != ''";  $em_title = "ทั้งหมด"; }
	if($rank == 1 ){ $from_date = dbDate($_GET['from_date'])." 00:00:00"; $to_date = dbDate($_GET['to_date'])." 23:59:59"; }else{ $from_date = date("Y-01-01 00:00:00"); $to_date = date("Y-12-31 23:59:59"); }
	$excel 	= array();
	if($view == 0 ) /// แยกตามเลขที่เอกสาร
	{
		$sql = dbQuery("SELECT id_order, id_customer, id_employee, reference, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd FROM tbl_order_detail_sold WHERE id_role = 7 AND ".$qr." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY reference");
		
		$title 		= array("รายงานยอดเบิกอภินันท์ ของพนักงาน : $em_title แยกตามเลขที่เอกสาร วันที่ ".thaiDate($from_date)." ถึง ".thaiDate($to_date));		
		$header 	= array("วันที่", "ผู้เบิก(พนักงาน)", "ผู้รับ", "เลขที่เอกสาร", "จำนวน", "มูลค่า", "หมายเหตุ");
		array_push($excel, $title);
		array_push($excel, $header);
		if( dbNumRows($sql) >0 )
		{	 
			$total_qty 		= 0;
			$total_amount 	= 0;
			while($rs = dbFetchArray($sql) )
			{
				$arr = array(thaiDate($rs['date_upd']), employee_name($rs['id_employee']), customer_name($rs['id_customer']), $rs['reference'], $rs['qty'], $rs['amount'], get_remark($rs['id_order']));
				array_push($excel, $arr);
				$total_qty		+= $rs['qty'];
				$total_amount	+= $rs['amount'];
			}
			$arr = array("", "", "", "รวม", $total_qty, $total_amount, "");
			array_push($excel, $arr);			
		}else{
			$arr = array("----------- ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------");
			array_push($excel, $arr);	
		}
		
	}else{  //// แยกตามรายการสินค้า id_order, id_customer, id_employee, reference, SUM(sold_qty) AS qty, SUM(total_amount) AS amount, date_upd
		$sql = dbQuery("SELECT id_customer, id_employee, id_product_attribute, product_reference, product_name, SUM(sold_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role = 7 AND ".$qr." AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') GROUP BY id_product_attribute ORDER BY product_reference ASC");
		$title 		= array("รายการสินค้าอภินันท์ ของพนักงาน : ".$em_title."  ตั้งแต่  ".thaiDate($from_date)." ถึง  ".thaiDate($to_date)." แยกตามรายการสินค้า");
		$header 	= array("รหัส", "สินค้า", "จำนวน", "มูลค่า");
		array_push($excel, $title);
		array_push($excel, $header);
			if(dbNumRows($sql) > 0 )
			{
				$total_qty 		= 0;
				$total_amount 	= 0;
				while($rs = dbFetchArray($sql) )
				{
					$arr = array($rs['product_reference'], $rs['product_name'], $rs['qty'], $rs['amount']);
					array_push($excel, $arr);
					$total_qty 		+= $rs['qty'];
					$total_amount	+= $rs['amount'];
				}
				$arr = array("", "รวม", $total_qty, $total_amount);
				array_push($excel, $arr);
				
			}else{
				$arr = array("----------  ไม่มีรายการตามเงื่อนไขที่กำหนด  ----------");
				array_push($excel, $arr);
			}				
	}	
	$Excel = new Excel_XML();
	$Excel->setEncoding("UTF-8");
	$Excel->setWorksheetTitle('Support by Employee');
	$Excel->addArray($excel);
	$Excel->generateXML('Support_by_Employee');
}


?>