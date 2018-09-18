<?php
	if( isset($_GET['id_order']) )
	{
		require_once 'library/config.php';
		require_once 'library/functions.php';
		include 'invent/function/tools.php';
		include 'invent/function/order_helper.php';
		include 'invent/function/payment_helper.php';
		include 'invent/function/bank_helper.php';

		$id_order = $_GET['id_order'];
		$bank = getActiveBank();
		$base_url = getConfig('WEB_ROOT_URL');
		if(isset($_GET['id_account']))
		{
			$order = new order($id_order);
			$order->getTotalOrder($id_order);
			$l_discount = bill_discount($id_order);
			$fee 				= getDeliveryFee($id_order);
			$service		= getServiceFee($id_order);
			$payAmount  = ($order->total_amount - $l_discount) + $fee + $service;
			$account = getBankAccount($_GET['id_account']);
			include 'invent/include/payment/upload_slip.php';
		}
		else
		{
			include 'invent/include/payment/payment_form.php';
		}

	}
	else
	{
		include 'invent/include/page_error.php';
	}
?>
