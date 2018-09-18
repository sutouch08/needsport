<?php
	if( isset($_GET['id_order']) )
	{
		require_once 'library/config.php';
		require_once 'library/functions.php';
		include 'invent/function/tools.php';
		include 'invent/function/order_helper.php';
    include 'invent/function/address_helper.php';

		$id_order = $_GET['id_order'];
		$base_url = getConfig('WEB_ROOT_URL');

    include 'invent/include/address/onlineAddressList.php';

   }
   else
   {

   }
?>
