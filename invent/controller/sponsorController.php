<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/sponsor_helper.php";
///////////////////  AutoComplete //////////////////////
if(isset($_GET['customer_name'])&&isset($_REQUEST['term'])){
	$qstring = "SELECT id_sponsor, tbl_sponsor.id_customer, first_name, last_name FROM tbl_customer LEFT JOIN tbl_sponsor ON tbl_customer.id_customer = tbl_sponsor.id_customer WHERE tbl_sponsor.active =1 AND (first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%')";
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{ 
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['id_sponsor'].":".$row['id_customer'].":".$row['first_name']." ".$row['last_name'];
		}
		echo  json_encode($data);//format the array into json data
	}else {
		echo "error";
	}
}

function get_id_customer_by_id_sponsor($id_sponsor)
{
	$id = "";
	$qs = dbQuery("SELECT id_customer FROM tbl_sponsor WHERE id_sponsor = ".$id_sponsor." LIMIT 1");
	if( dbNumRows($qs) == 1 )
	{
		$r = dbFetchArray($qs);
		$id = $r['id_customer'];
	}
	return $id;
}

/*************************************  ส่งกลับข้อมูลเพื่อแก้ไข budget  **********************/

if( isset($_GET['get_budget']) && isset($_GET['id_sponsor']) && isset($_GET['id_sponsor_budget']) )
{
	$id_sponsor = $_GET['id_sponsor'];
	$id_sponsor_budget = $_GET['id_sponsor_budget'];
	$data = "";
	$sql = dbQuery("SELECT * FROM tbl_sponsor_budget WHERE id_sponsor_budget = ".$id_sponsor_budget." AND id_sponsor = ".$id_sponsor." LIMIT 1");
	while($rs = dbFetchArray($sql))
	{	
		$data = $id_sponsor_budget." : ".$id_sponsor." : ".$rs['reference']." : ".$rs['limit_amount']." : ".thaiDate($rs['start'])." : ".thaiDate($rs['end'])." : ".$rs['remark']." : ".$rs['active']." : ".$rs['year'];
	}
	echo $data;
}

//************************************  เปลี่ยนปีงบประมาณที่ใช้  ******************//

if( isset($_GET['set_year']) && isset($_GET['id_sponsor']) && isset($_GET['year']) )
{
	$id_sponsor = $_GET['id_sponsor'];
	$id_customer = get_id_customer_by_id_sponsor($id_sponsor);
	$customer_name = customer_name($id_customer);
	$year = $_GET['year'];
	$current_year = get_sponsor_current_year($id_sponsor);
	$rs = "false";
	$qs =dbQuery("SELECT limit_amount, start, end, year FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor." AND year = '".$year."'");
	$rw = dbNumRows($qs);
	if($rw >0){
		$sql = dbQuery("UPDATE tbl_sponsor SET year = '".$year."' WHERE id_sponsor = ".$id_sponsor);
		if($sql){ 
			add_sponsor_log($id_sponsor, 0, "edit", "เปลี่ยนปีงบประมาณที่ใช้ ของ ".$customer_name, "ปี ".$current_year, "ปี ".$year);
			while($rd = dbFetchArray($qs)){
				$rs = number_format($rd['limit_amount'],2)." : ".thaiDate($rd['start'], "/")." - ".thaiDate($rd['end'], "/")." : ".$rd['year'];
				}
		}
	}else{
		$rs = "noyear";
	}
	echo $rs;			
}

/***************************  ตรวจสอบปีงบประมาณซ้ำหรือไม่ ก่อนเพิ่มใหม่  ******************/
if( isset($_GET['check_valid_year']) && isset($_GET['id_sponsor']) && isset($_GET['year']) )
{
	$id_sponsor = $_GET['id_sponsor'];
	$year = $_GET['year'];
	$rs = 0;
	$rw = dbNumRows(dbQuery(	"SELECT year FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor." AND year = '".$year."'"));
	if( $rw >0 ){ $rs = 1; }
	echo $rs;
}

/**************************  ตรวจสอบรายชื่อสปอนเซอร์ซ้ำ *****************************/

if( isset($_GET['valid_duplicate']) && isset($_GET['id_customer']) ){
	$rs = 0;
	$row = dbNumRows(dbQuery("SELECT id_sponsor FROM tbl_sponsor WHERE id_customer = ".$_GET['id_customer']));
	if($row > 0){ $rs = 1; }
	echo $rs;	
}

/***********************  add member ********************************/
if(isset($_GET['add_member'])&&isset($_POST['id_customer'])){
	$id_customer = $_POST['id_customer'];
	$sponsor_name = customer_name($id_customer);
	$reference = $_POST['reference'];
	$limit_amount = $_POST['budget'];
	$start_date = dbDate($_POST['from_date']);
	$end_date = dbDate($_POST['to_date']);
	$remark = $_POST['remark'];
	$active = $_POST['active'];
	$year = $_POST['year'];
	$sql = dbQuery("INSERT INTO tbl_sponsor (id_customer, active, year) VALUES (".$id_customer.", ".$active.", '".$year."')");
	$id_sponsor = dbInsertId();
	$qr = dbQuery("INSERT INTO tbl_sponsor_budget (id_sponsor, reference, id_customer, limit_amount, start, end, remark, active, year, balance) 
						VALUES (".$id_sponsor.", '".$reference."', ".$id_customer.", ".$limit_amount.", '".$start_date."', '".$end_date."', '".$remark."', ".$active.", '".$year."', ".$limit_amount.")");
	if($sql && $qr){
		$id_sponsor_budget = dbInsertId();
		add_sponsor_log($id_sponsor, $id_sponsor_budget, "add", "เพิ่มผู้รับสปอนเซอร์", "", $sponsor_name);
		$message = "เพิ่มรายชื่อเรียบร้อยแล้ว";
		header("location: ../index.php?content=add_sponsor&message=$message");
	}else{
		$message = "เพิ่มรายชื่อไม่สำเร็จ";
		header("location: ../index.php?content=add_sponsor&add=y&error=$message");
	}
}

/****************************************  Add New Budget  *****************************/
if( isset($_GET['add_budget']) && isset($_GET['id_sponsor']) && isset($_POST['budget']) )
{
	$id_sponsor = $_GET['id_sponsor'];
	$reference = $_POST['reference'];
	$budget = $_POST['budget'];
	$from = dbDate($_POST['from_date']);	
	$to = dbDate($_POST['to_date']);
	$remark = $_POST['remark'];
	$active = $_POST['active'];
	$year = $_POST['year'];
	$id_customer = get_id_customer_by_id_sponsor($id_sponsor);
	$customer_name = customer_name($id_customer);
	$qr = dbQuery("INSERT INTO tbl_sponsor_budget (id_sponsor, reference, id_customer, limit_amount, start, end, remark, active, year, balance) VALUES (".$id_sponsor.", '".$reference."', ".$id_customer.", ".$budget.", '".$from."', '".$to."', '".$remark."', ".$active.", '".$year."', ".$budget.")");
	if($qr)
	{
		$id_sponsor_budget = dbInsertId();
		add_sponsor_log($id_sponsor, $id_sponsor_budget, "add", "เพิ่มงบประมาณใหม่ ของ $customer_name", "", $budget);
		$message = "เพิ่มงบประมาณเรียบร้อยแล้ว";
		header("location: ../index.php?content=add_sponsor&edit&id_sponsor=".$id_sponsor."&message=".$message);
	}else{
		$message = "เพิ่มงบประมาณไม่สำเร็จ";
		header("location: ../index.php?content=add_sponsor&edit&id_sponsor=".$id_sponsor."&error=".$message);
	}
}


/***************************************** Edit Budget  *********************************/
if( isset($_GET['edit_budget']) && isset($_POST['id_sponsor_budget']) )
{
	$id_sponsor = $_POST['id_sponsor'];
	$id_customer = get_id_customer_by_id_sponsor($id_sponsor);
	$customer_name = customer_name($id_customer);
	$id_sponsor_budget = $_POST['id_sponsor_budget'];
	$reference = $_POST['reference'];
	$budget = $_POST['budget'];
	$start_date = dbDate($_POST['from_date']);
	$end_date = dbDate($_POST['to_date']);
	$rank = get_current_sponsor_rank($id_sponsor_budget);
	$remark = $_POST['remark'];
	$active = $_POST['active'];
	$year = $_POST['year'];
	$old_year = get_current_sponsor_budget_year($id_sponsor_budget);
	$balance = get_sponsor_balance($id_sponsor_budget);
	$old_budget = get_sponsor_budget($id_sponsor_budget);
	$new_balance = $balance + ($budget - $old_budget);
	$qs = dbQuery("UPDATE tbl_sponsor_budget SET reference = '".$reference."', limit_amount = ".$budget.", start = '".$start_date."', end = '".$end_date."', remark = '".$remark."', active = ".$active.", year = '".$year."', balance = ".$new_balance." WHERE id_sponsor_budget = ".$id_sponsor_budget);
	if($qs){
		$action = "";
		if( $year != $old_year ){ $action .= "เปลี่ยนปีของงบประมาณของ $customer_name จาก $old_year เป็น $year "; $sep = "/"; }else{ $sep = ''; }
		if( $rank['start'] != $start_date || $rank['end'] != $end_date){ $action .= " $sep เปลี่ยนแปลงวันที่ในงบประมาณของ $customer_name เริ่มต้น จาก ".thaiDate($rank['start']) ." เป็น ".thaiDate($start_date)." และ สิ้นสุด จาก ".thaiDate($rank['end'])." เป็น ".thaiDate($end_date)." "; $sep = "/"; }else{ $sep = ''; }
		if( $budget != $old_budget ){ $action .= " $sep เปลี่ยนแปลงงบประมาณของ $customer_name";  $sep = '/';}else{ $sep = ''; }
		add_sponsor_log($id_sponsor, $id_sponsor_budget, "edit", $action, $old_budget, $budget);
		
		$message = "ปรับปรุงข้อมูลเรียบร้อยแล้ว";
		header("location: ../index.php?content=add_sponsor&edit=y&id_sponsor=".$id_sponsor."&message=".$message);
	}else{
		$message = "ไม่สามารถปรับปรุงข้อมูลได้";
		header("location: ../index.php?content=add_sponsor&edit=y&id_sponsor=".$id_sponsor."&error=".$message);
	}
}

/******************************************  DELETE Budget  ************************************/
if( isset($_GET['delete_budget']) && isset($_GET['id_sponsor']) && isset($_GET['id_sponsor_budget']) )
{
	$id_sponsor = $_GET['id_sponsor'];
	$id_sponsor_budget = $_GET['id_sponsor_budget'];
	$customer_name = customer_name(get_id_customer_by_id_sponsor($id_sponsor));
	$budget = get_sponsor_budget($id_sponsor_budget);
	$year = get_current_sponsor_budget_year($id_sponsor_budget);
	$qs = dbQuery("DELETE FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor." AND id_sponsor_budget = ".$id_sponsor_budget);
	if($qs){
		add_sponsor_log($id_sponsor, $id_sponsor_budget, "delete", "ลบงบประมาณปี $year ของ $customer_name", $budget, "");
		$message = "ลบงบประมาณเรียบร้อยแล้ว";
		header("location: ../index.php?content=add_sponsor&edit=y&id_sponsor=".$id_sponsor."&message=".$message);
	}else{
		$message = "ลบงบประมาณไม่สำเร็จ";
		header("location: ../index.php?content=add_sponsor&edit=y&id_sponsor=".$id_sponsor."&error=".$message);
	}
}

/***************************************** edit member  ****************************************/
if(isset($_GET['edit_member']) && isset($_GET['id_sponsor']) && isset($_GET['id_customer']) ){
	$id_sponsor 	= $_GET['id_sponsor'];
	$id_customer 	= $_GET['id_customer'];
	$name 			= customer_name($id_customer);
	$old_name 		= customer_name(get_id_customer_by_id_sponsor($id_sponsor));
	$rs = "false";
	$qs = dbQuery("UPDATE tbl_sponsor SET id_customer = ".$id_customer." WHERE id_sponsor = ".$id_sponsor);
	if($qs){
		add_sponsor_log($id_sponsor, 0, "edit", "เปลี่ยนแปลงผู้รับงบประมาณ", $old_name, $name);
		$qr = dbQuery("UPDATE tbl_sponsor_budget SET id_customer = ".$id_customer." WHERE id_sponsor = ".$id_sponsor);
		if($qr){ $rs = $name; }
	}
	echo $rs;
}

/********************************************* DELETE MEMBER ******************************/
if( isset($_GET['delete_member']) && isset($_GET['id_sponsor']) )
{
	$id_sponsor = $_GET['id_sponsor'];
	$name 	= customer_name(get_id_customer_by_id_sponsor($id_sponsor));
	$rw = dbNumRows(dbQuery("SELECT id_sponsor_budget FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor));
	if($rw > 0)
	{
		$message = "ไม่สารมารถลบรายการนี้ได้ เนื่องจากยังมีรายการ งบประมาณค้างอยู่";
		header("location: ../index.php?content=add_sponsor&error=".$message);
	}else{
		$qs = dbQuery("DELETE FROM tbl_sponsor WHERE id_sponsor = ".$id_sponsor);
		if($qs){
			add_sponsor_log($id_sponsor, 0, "delete", "ลบผู้รับงบประมาณ", $name, "");
			$message = "ลบรายการเรียบร้อยแล้ว";
			header("location: ../index.php?content=add_sponsor&message=".$message);			
		}else{
			$message = "ลบรายการไม่สำเร็จ";
			header("location: ../index.php?content=add_sponsor&error=".$message);
		}
	}		
	
}

/******************************************************************************************************   ORDER  ***********************************************************************/
//*********************************  เพิ่มออเดอร์  ***************************//
if( isset($_GET['add_order']) && isset($_POST['id_customer']) )
{
	$id_employee 	= $_POST['id_employee'];
	$id_customer 	= $_POST['id_customer'];
	$date_add 		= dbDate($_POST['doc_date'], true);
	$reference 		= get_max_role_reference("PREFIX_SPONSOR",4);
	$payment 		= "สปอนเซอร์สโมสร";
	$role 				= 4;
	$id_cart			= 0;
	$id_address	= 0;
	$current_state = 1;
	$comment = $_POST['remark'];
	$valid = 0;
	$status = 0;
	$amount = 0.00;
	$id_user = $_COOKIE['user_id'];
	list($id_sponsor, $year) = dbFetchArray(dbQuery("SELECT id_sponsor, year FROM tbl_sponsor WHERE id_customer = ".$id_customer));
	list($id_budget) = dbFetchArray(dbQuery("SELECT id_sponsor_budget FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor." AND year = '".$year."' AND active = 1"));
	$qr = dbQuery("INSERT INTO tbl_order (reference, id_customer, id_employee, id_cart, current_state, payment, comment, valid, role, date_add, order_status) VALUES ('".$reference."', ".$id_customer.", ".$id_employee.", ".$id_cart.", ".$current_state.", '".$payment."', '".$comment."', ".$valid.", ".$role.", '".$date_add."', ".$status.")");
	if($qr){
		$id_order = dbInsertId();
		$qs = dbQuery("INSERT INTO tbl_order_sponsor(id_order, id_customer, id_employee, id_sponsor, id_budget, year, amount, status, date_add, id_user) VALUES (".$id_order.", ".$id_customer.", ".$id_employee.", ".$id_sponsor.", ".$id_budget.", '".$year."', ".$amount.", ".$status.", '".$date_add."', ".$id_user.")");
		$id_order_sponsor = dbInsertId();
		order_state_change($id_order, $current_state, $id_user);
		header("location: ../index.php?content=order_sponsor&edit=y&id_order=".$id_order."&id_order_sponsor=".$id_order_sponsor);
	}else{
		header("location: ../index.php?content=order_sponsor&error=เพิ่มออเดอร์ไม่สำเร็จ");
	}
}


//*************************ajax  แก้ไขออเดอร์  ajax ***************************//
if( isset($_GET['edit_order']) && isset($_GET['id_order']) && isset($_GET['id_order_sponsor']) )
{
	$id_order = $_GET['id_order'];
	$id_order_sponsor = $_GET['id_order_sponsor'];
	$id_employee = $_POST['id_employee'];
	$id_customer = $_POST['id_customer'];
	$date_add = dbDate($_POST['doc_date'],true);
	$remark = $_POST['remark'];
	$id_user = $_COOKIE['user_id'];
	$old_id_budget = $_GET['id_budget'];
	$order = new order($id_order);
	list($id_sponsor, $year) = dbFetchArray(dbQuery("SELECT id_sponsor, year FROM tbl_sponsor WHERE id_customer = ".$id_customer));
	list($id_budget) = dbFetchArray(dbQuery("SELECT id_sponsor_budget FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor." AND year = '".$year."' AND active = 1"));
	$order_amount = $order->getCurrentOrderAmount($id_order);
	$budget_balance = get_sponsor_balance($id_budget);
	if($order_amount > $budget_balance){
		echo "over_budget : ".$id_budget;
	}else{
		$qs = dbQuery("UPDATE tbl_order SET id_customer = ".$id_customer.", id_employee = ".$id_employee.", comment = '".$remark."', date_add = '".$date_add."' WHERE id_order = ".$id_order);
		if($qs){
			$qr = dbQuery("UPDATE tbl_order_sponsor SET id_customer = ".$id_customer.", id_employee = ".$id_employee.", id_sponsor = ".$id_sponsor." , id_budget = ".$id_budget.", year = '".$year."', id_user=".$id_user." WHERE id_order_sponsor = ".$id_order_sponsor);
			if($qr){
				$balance = $budget_balance - $order_amount;
				update_sponsor_balance($id_budget, $balance);
				$old_balance = get_sponsor_balance($old_id_budget);
				$old_balance += $order_amount;
				update_sponsor_balance($old_id_budget, $old_balance);
				echo "success : ".$id_budget;
			}else{
				echo "false : ".$id_budget;
			}
		}else{
			echo "fail : ".$id_budget;
		}
	}
}


///***********************************************  เพิ่มรายการสั่งสินค้า  ************************************//
if(isset($_GET['add_to_order'])){
	$id_order= $_POST['id_order'];
	$order= new order($id_order);
	$id_employee = $order->id_employee;
	$id_customer = $order->id_customer;
	$id_order_sponsor = $_POST['id_order_sponsor'];
	$id_budget = $_POST['id_budget'];
	$order_qty = $_POST['qty'];
	$n = 0;
	$missing = "";
	foreach ($order_qty as $id_color => $items ){	
		foreach($items as $id => $qty)
		{
			if($qty !=""){	
				$product = new product();
				$id_product = $product->getProductId($id);
				$product->product_attribute_detail($id);
				$total_amount = $qty * $product->product_price; 
				
				$balance = get_sponsor_balance($id_budget);
				if($total_amount <= $balance){  /// ถ้ายอดสั่งน้อยกว่าหรือเท่ากับ งบคงเหลือ อนุญาติให้สั่งได้
					if(!ALLOW_UNDER_ZERO)  //// ถ้าไม่อนุญาติให้สต็อกติดลบได้
					{
								$instock = $product->available_order_qty($id); /// ตรวจสอบยอดคงเหลือในสต็อก (คำนวนแล้ว)
								if($qty > $instock)   									/// ถ้ายอดสั่งมากกว่ายอดคงเหลือในสต็อก
								{
									$missing .= $product->reference." : มียอดคงเหลือไม่เพียงพอ &nbsp;<br/>";   //// บันทึกข้อผิดพลาด แล้วไม่ต้องทำอะไร
								}
								else 												/// ถ้ายอดสั่งน้อยกว่ายอดคงเหลือในสต็อก
								{
											if($order->insert_support_detail($id, $qty))		/// ใช้ฟังชันก์เดียวกับ เบิกอภินันท์ เพราะใช้ร่วมกันได้
											{
												$amount = $balance - $total_amount;
												update_sponsor_balance($id_budget, $amount);
												$n++;
											}
											else
											{
												$missing .= $product->reference. " : ".$order->error_message. "<br/>";
											}
									}
						}
						else
						{
								if($order->insert_support_detail($id, $qty))			/// ใช้ฟังชันก์เดียวกับ เบิกอภินันท์ เพราะใช้ร่วมกันได้
								{
									$amount = $balance - $total_amount;
									update_sponsor_balance($id_budget, $amount);
									$n++;
								}
								else
								{
									$missing .= $product->reference. " : ".$order->error_message. "<br/>";
								}
						}
				}
				else
				{
					$missing .= 	$product->reference." : งบประมาณคงเหลือไม่เพียงพอ";
				}//if($order_amount <= $balance)
			}// if qty !=0
		}// foreach
	}//foreach
	if($missing ==""){
		$message = "เพิ่ม $n รายการเรียบร้อย";
		header("location: ../index.php?content=order_sponsor&edit=y&id_order=".$id_order."&id_order_sponsor=".$id_order_sponsor."&message=$message");
	}else{
		$message = $missing;
		header("location: ../index.php?content=order_sponsor&edit=y&id_order=".$id_order."&id_order_sponsor=".$id_order_sponsor."&error=$message");
	}
}

/// ลบในหน้า แก้ไข
if( isset($_GET['delete_item']) && isset($_GET['id_order_detail']) && isset($_GET['id_budget']) )
{
	$id_order = $_GET['id_order'];
	$id_order_detail = $_GET['id_order_detail'];
	$id_order_sponsor = $_GET['id_order_sponsor'];
	$id_budget = $_GET['id_budget'];
	$amount = $_GET['amount'];
	$balance = get_sponsor_balance($id_budget);
	$qr = dbQuery("DELETE FROM tbl_order_detail WHERE id_order_detail = ".$id_order_detail." AND id_order = ".$id_order);
	if($qr){
		$balance += $amount;
		$rs = update_sponsor_balance($id_budget, $balance);
		if($rs)
		{
			$message = "ลบรายการเรียบร้อยแล้ว";
			header("location: ../index.php?content=order_sponsor&edit=y&id_order=".$id_order."&id_order_sponsor=".$id_order_sponsor."&message=".$message);
		}else{
			$message = "ลบรายการสำเร็จแต่ปรับปรุงงบคงเหลือไม่สำเร็จ";
			header("location: ../index.php?content=order_sponsor&edit=y&id_order=".$id_order."&id_order_sponsor=".$id_order_sponsor."&error=".$message);
		}
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=order_sponsor&edit=y&id_order=".$id_order."&id_order_sponsor=".$id_order_sponsor."&error=".$message);
	}	
}



//***************************** เปลี่ยนสถานะออเดอร์ในหน้ารายละเอียด  **********************//
if( isset($_GET['state_change']) && isset($_GET['id_order']) &&  isset($_POST['id_state']) )
{
	$id_order = $_GET['id_order'];
	$id_order_state = $_POST['id_state'];
	$id_user = $_POST['id_user'];
	$rs = false;
	if($id_order_state != 0)
	{
		$rs = order_state_change($id_order, $id_order_state, $id_user);			
	}
	if($rs)
	{
		header("location: ../index.php?content=order_sponsor&id_order=".$id_order."&view_detail");
	}else{
		$message = "เปลี่ยนสถานะไม่สำเร็จ";
		header("location: ../index.php?content=order_sponsor&id_order=".$id_order."&view_detail&error=".$message);
	}
}

//*****************************  Save order  **********************//
if( isset($_GET['save_order']) && isset($_GET['id_order']) )
{
	$qr = dbQuery("UPDATE tbl_order SET order_status = 1 WHERE id_order =".$_GET['id_order']);
	header("location: ../index.php?content=order_sponsor");
}


if(isset($_GET['check_add'])){
	$user_id = $_COOKIE['user_id'];
	$qs = dbQuery("SELECT tbl_order_sponsor.id_order FROM tbl_order_sponsor JOIN tbl_order ON tbl_order_sponsor.id_order = tbl_order.id_order WHERE id_user = ".$user_id ." AND order_status = 0 AND role = 4 LIMIT 1");
	if(dbNumRows($qs) < 1) {
		header("location: ../index.php?content=order_sponsor&add=y");
	}else{
		$rs = dbFetchArray($qs);
		$id_order = $rs['id_order'];
		$id_order_sponsor = get_id_order_sponsor($id_order);
		$message = "ยังไม่ได้บันทึกออร์เดอร์นี้";
		header("location: ../index.php?content=order_sponsor&edit=y&id_order=".$id_order."&id_order_sponsor=".$id_order_sponsor."&warning=".$message);
	}
}



if(isset($_GET['clear_filter'])){
		setcookie("sponsor_from_date","",time()-3600,"/");
		setcookie("sponsor_to_date","",time()-3600,"/");
		setcookie("sponsor_search-text", $text, time() - 3600, "/");
		setcookie("sponsor_filter",$filter, time() - 3600,"/");
		header("location: ../index.php?content=order_sponsor");
}

//// ปริ๊นออเดอร์ไปนำเข้า  formula
if(isset($_GET['print_order'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$employee = employee_name($order->id_employee);
	$id_order_sponsor = $_GET['id_order_sponsor'];
	$customer = new customer($order->id_customer);
	$remark = $order->comment;
	$title = "ใบเบิกอภินันท์/Sponsored Order";
	$qty = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_qty = "";/////วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$row = 17;
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order ORDER BY barcode ASC");
	$rs = dbNumRows($sql);
	$total_page = ceil($rs/$row);
	$page = 1;
	$count = 1;
	$n = 1;
	$i = 0;
	$html = "	<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>ออเดอร์</title>
					<!-- Core CSS - Include with every page -->
					<link href='../../library/css/bootstrap.css' rel='stylesheet'>
					<link href='../../library/css/font-awesome.css' rel='stylesheet'>
					<link href='../../library/css/bootflat.min.css' rel='stylesheet'>
					 <link rel='stylesheet' href='../../library/css/jquery-ui-1.10.4.custom.min.css' />
					 <script src='../../library/js/jquery.min.js'></script>
					<script src='../../library/js/jquery-ui-1.10.4.custom.min.js'></script>
					<script src='../../library/js/bootstrap.min.js'></script>  
					<!-- SB Admin CSS - Include with every page -->
					<link href='../../library/css/sb-admin.css' rel='stylesheet'>
					<link href='../../library/css/template.css' rel='stylesheet'>
				</head>";
				$doc_body_top = "<body style='padding-top:0px; margin-top:-15px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px; '>
				<div class='hidden-print' style='margin-bottom:0px; margin-top:10px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=order_sponsor&id_order=".$id_order."&id_order_sponsor=".$id_order_sponsor."&view_detail=y' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> ";
				function doc_head($order,$company, $customer, $employee, $title, $page, $total_page){
					$result = "
	<h4>$title</h4><p class='pull-right'>หน้า $page / $total_page</p>
	<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:20mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr>
					<td align='right' style='width:20%; padding:10px; height:5mm; vertical-align:text-top;'>ผู้เบิก :</td>
					<td style='padding:10px; vertical-align:text-top; height:5mm;'>".$employee."</td>
				</tr>
				<tr>
					<td align='right' style='width:20%; padding:10px; vertical-align:text-top;'>ผู้รับ :</td>
					<td style='padding:10px; height:30mm; vertical-align:text-top;'>".$customer->full_name."</td>
				</tr>
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:20mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td align='right' style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".thaiDate($order->date_add,"/")."</td></tr>
				<tr><td align='right' style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$order->reference."</td></tr>
				</table>	</div></td></tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px; ' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td><td style='text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:30%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ราคา</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>มูลค่า</td>
	</tr>"; return $result; }
	function page_summary($total_order_amount, $total_discount_amount, $net_total, $remark, $total_qty=""){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		if($total_discount_amount !=""){ $total_discount_amount = number_format($total_discount_amount,2); }
		if($net_total !=""){ $net_total = number_format($net_total,2); }
		echo"	<tr style='height:12mm;'><td colspan='7' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top; text-align:right;'>รวม $total_qty หน่วย</td></tr>
				<tr style='height:12mm;'><td rowspan='3' colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
					<td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_order_amount."</td></tr>
				<tr style='height:12mm;'><td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ส่วนลด</td>
					<td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_discount_amount."</td></tr>
				<tr style='height:12mm;'><td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ยอดเงินสุทธิ</td>
					<td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$net_total."</td></tr>
				</table>";
	}
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($order, $company, $customer, $employee, $title,$page, $total_page);
	while($i<$rs){
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $total_amount)= dbFetchArray($sql);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		$total = $product_price * $product_qty;
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%";}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;}
		echo"<tr style='height:12mm;'><td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$n</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'><img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$barcode."' style='width:100px;' /></td>
		<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($product_qty)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$discount</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($total_amount,2)."</td>
				</tr>";
				$total_qty += $product_qty;
				$total_order += $total;
				$total_discount_order += $total_discount;
				$i++; $count++;
				if($n==$rs){ 
				$ba_row = $row - $count -4; 
				$ba = 0;
				if($ba_row >0){
					while($ba <= $ba_row){
						if($count+1 >$row){  $css_ba_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_ba_row ="border-top: 0px;";}
						echo"<tr style='height:12mm;'>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
				</tr>";
						$ba++; $count++;
					}
				}
				$total_all_qty = $total_qty;
				$total_order_amount = $total_order;
				$total_discount_amount = $total_discount_order;
				$net_total = $total_order_amount - $total_discount_amount;
				page_summary($total_order_amount, $total_discount_amount, $net_total, $remark, $total_all_qty);
				}else{
				if($count>$row){  $page++; echo "</table><div style='page-break-after:always;'></div>".doc_head($order, $company, $customer, $title, $page, $total_page); $count = 1;  }
				}
				$n++; 
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo "</div></body></html>";
	 }
?>