<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";

//------- Add new channels
if( isset( $_GET['addChannels'] ) )
{
	$sc = 'success';
	$code = $_POST['code'];
	$name = $_POST['name'];
	//$isDefault = $_POST['isDefault'];
	$isOnline = $_POST['isOnline'];
	$cs = new channels();
	$nameExists = $cs->isExists('name', $name);
	$codeExists	 = $cs->isExists('code', $code);
	if( $nameExists === TRUE )
	{
		$sc = 'nameError';
	}

	if( $codeExists === TRUE )
	{
		$sc = 'codeError';
	}

	if( $codeExists === FALSE && $nameExists === FALSE )
	{
		$arr = array(
					'code' 	=> $code,
					'name' 	=> $name,
					'isOnline' => $isOnline,
					'emp_upd' => getCookie('user_id')
				);

		if( $cs->add($arr) === FALSE )
		{
			$sc = 'เพิ่มรายการไม่สำเร็จ';
		}
	}

	echo $sc;
}



if( isset( $_GET['saveEditChannels'] ) )
{
	$sc = 'success';
	$id			= $_POST['id'];
	$code	= $_POST['code'];
	$name	= $_POST['name'];
	$active = $_POST['isActive'];
	$isOnline		= $_POST['isOnline'];
	$cs = new channels();
	$nameExists = $cs->isExists('name', $name, $id);
	$codeExists	 = $cs->isExists('code', $code, $id);
	if( $nameExists === TRUE )
	{
		$sc = 'nameError';
	}
	if( $codeExists === TRUE )
	{
		$sc = 'codeError';
	}

	if( $codeExists === FALSE && $nameExists === FALSE )
	{
		$arr = array(
					'code' 	=> $code,
					'name' 	=> $name,
					'isOnline'	=> $isOnline,
					'active' => $active,
					'emp_upd' => getCookie('user_id')
				);

		if( $cs->update($id, $arr) === FALSE )
		{
			$sc = 'บันทึกรายการไม่สำเร็จ';
		}
	}

	echo $sc;

}


if( isset( $_GET['deleteChannels'] ) )
{
	$sc = 'fail';
	$id = $_GET['id'];
	$cs = new channels();
	if($cs->isDefault($id) === FALSE)
	{
		if( $cs->removeChannels($id) === TRUE )
		{
			$sc = 'success';
		}
	}

	echo $sc;
}


//----------- Get type data for edit
if( isset( $_GET['getData'] ) )
{
	$sc = "";
	$id = $_GET['id'];
	$cs = new channels($id);
	if( $cs->id != '' )
	{
		$sc = $cs->id .' | ' . $cs->code . ' | ' . $cs->name . ' | ' .$cs->active . ' | ' .$cs->isOnline;
	}
	echo $sc;
}


if( isset($_GET['setDefault']) )
{
	$sc = TRUE;
	$id = $_GET['id'];
	$cs = new channels($id);
	if($cs->setDefault($id) === FALSE)
	{
		$sc = 'ทำรายการไม่สำเร็จ';
	}

	echo $sc === TRUE ? 'success' : $sc;
}

if( isset( $_GET['clearFilter'] ) )
{
	deleteCookie('sChannelsCode');
	deleteCookie('sChannelsName');
	echo 'done';
}

?>
