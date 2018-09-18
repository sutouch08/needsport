<?php

function selectOnlineChannels($id="")
{
	$sc = "";
	$cs = new channels();
	$qs = $cs->getOnlineChannels();
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$sc .= '<option value="'.$rs->id.'" '.isSelected($id, $rs->id).'>'.$rs->name.'</option>';
		}
	}
	return $sc;
}


function selectOfflineChannels($id="")
{
	$sc = "";
	$cs = new channels();
	$qs = $cs->getOfflineChannels();
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$sc .= '<option value="'.$rs->id.'" '.isSelected($id, $rs->id).'>'.$rs->name.'</option>';
		}
	}
	return $sc;
}




function selectChannels($id="")
{
	$sc = '';
	$cs = new channels();
	$id = $id == "" ? $cs->getDefaultId() : $id;
	$qs = $cs->getData();
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$sc .= '<option value="'.$rs->id.'" '.isSelected($id, $rs->id).'>'.$rs->name.'</option>';
		}
	}
	return $sc;
}



function getChannelsIn($txt)
{
	$cs = new channels();
	$qs = $cs->searchId($txt);
	if( dbNumRows($qs) > 0 )
	{
		$i = 1;
		$sc = "";
		while( $rs = dbFetchObject($qs) )
		{
			$sc .= $i == 1 ? $rs->id : ", ".$rs->id;
			$i++;
		}
	}
	else
	{
		$sc = "0";
	}
	return $sc;
}


?>
