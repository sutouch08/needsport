<?php
class channels
{
	public $id;
	public $code;
	public $name;
	public $isOnline = 0;
	public $isDefault = 0;
	public $active = 1;
	public $date_upd;
	public $emp_upd = '';
	public $isDeleted = 0;


	public function __construct($id = '')
	{
		if( $id != '' )
		{
			$qs = dbQuery("SELECT * FROM tbl_channels WHERE id = '".$id."'");
			if( dbNumRows($qs) == 1 )
			{
				$rs 				= dbFetchObject($qs);
				$this->id 		= $rs->id;
				$this->code 	= $rs->code;
				$this->name	 	= $rs->name;
				$this->isOnline	= $rs->isOnline;
				$this->isDefault	= $rs->isDefault;
				$this->active = $rs->active;
				$this->date_upd = $rs->date_upd;
				$this->emp_upd = $rs->emp_upd;
				$this->isDeleted = $rs->isDeleted;
			}
		}
	}




	public function add(array $ds )
	{
		$sc = FALSE;
		if( count($ds) > 0 )
		{
			$fields	= "";
			$values	= "";
			$i			= 1;
			foreach( $ds as $field => $value )
			{
				$fields	.= $i == 1 ? $field : ", ".$field;
				$values	.= $i == 1 ? "'". $value ."'" : ", '". $value ."'";
				$i++;
			}
			$sc = dbQuery("INSERT INTO tbl_channels (".$fields.") VALUES (".$values.")");
		}
		return $sc;
	}





	public function update($id, array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$set 	= "";
			$i		= 1;
			foreach( $ds as $field => $value )
			{
				$set .= $i == 1 ? $field . " = '" . $value . "'" : ", ".$field . " = '" . $value . "'";
				$i++;
			}
			$sc = dbQuery("UPDATE tbl_channels SET " . $set . " WHERE id = '".$id."'");
		}
		return $sc;
	}






	public function setDefault($id)
	{
		$sc = FALSE;
		if( $this->clearDefault() )
		{
			$sc = dbQuery("UPDATE tbl_channels SET isDefault = 1 WHERE id = ".$id);
		}
		return $sc;
	}



	public function isDefault($id)
	{
		$qs = dbQuery("SELECT code FROM tbl_channels WHERE id = ".$id." AND isDefault = 1");
		if(dbNumRows($qs) == 1)
		{
			return TRUE;
		}

		return FALSE;
	}


	public function clearDefault()
	{
		return dbQuery("UPDATE tbl_channels SET isDefault = 0");
	}


	public function setActive($id)
	{
		return dbQuery("UPDATE tbl_channels SET active = 1 , emp_upd = ".getCookie('user_id')." WHERE id = ".$id);
	}


	public function disActive($id)
	{
		return dbQuery("UPDATE tbl_channels SET active = 0 , emp_upd = ".getCookie('user_id')." WHERE id = ".$id);
	}




	public function removeChannels($id)
	{
		//--- เมื่อมี่ transection แล้วไม่ให้ลบ
		if($this->hasTransections($id) === TRUE)
		{
			return $this->setDelete($id);
		}

		//-- หากยังไม่มี transection ให้ลบได้
		return $this->delete($id);
	}




	private function setDelete($id)
	{
		//--- เมื่อมี่ transection แล้วไม่ให้ลบ
		return dbQuery("UPDATE tbl_channels SET isDeleted = 1 , emp_upd = ".getCookie('user_id')." WHERE id = ".$id);
	}




	private function delete($id)
	{
		//-- หากยังไม่มี transection ให้ลบได้
		return dbQuery("DELETE FROM tbl_channels WHERE id = ".$id);
	}

	private function hasTransections($id)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id_order FROM tbl_order WHERE id_channels = ".$id." LIMIT 1");
		if(dbNumRows($qs) > 0)
		{
			$sc = TRUE;
		}

		return $sc;
	}



	public function setOnline($id, $val)
	{
		return dbQuery("UPDATE tbl_channels SET isOnline = ".$val." WHERE id = '".$id);
	}





	public function isExists($field, $value, $id='')
	{
		$sc = FALSE;
		if( $id != '' )
		{
			$qs = dbQuery("SELECT id FROM tbl_channels WHERE ".$field." = '".$value."' AND id != '".$id."'");
		}
		else
		{
			$qs = dbQuery("SELECT id FROM tbl_channels WHERE ".$field." = '".$value."'");
		}

		if( dbNumRows($qs) > 0 )
		{
			$sc = TRUE;
		}
		return $sc;
	}



	public function getCode($id)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT code FROM tbl_channels WHERE id = '".$id."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}





	public function getId($code)
	{
		$sc = 0;
		$qs = dbQuery("SELECT id FROM tbl_channels WHERE code = '".$code."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}




	public function getNameByCode($code)
	{
		$sc = "";
		$qs = dbQuery("SELECT name FROM tbl_channels WHERE code = '".$code."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}





	public function getName($id)
	{
		$sc = "";
		$qs = dbQuery("SELECT name FROM tbl_channels WHERE id = '".$id."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}




	public function getData()
	{
		return dbQuery("SELECT * FROM tbl_channels");
	}


	public function getOnlineChannels()
	{
		return dbQuery("SELECT * FROM tbl_channels WHERE isOnline = 1 AND active = 1 AND isDeleted = 0");
	}



	public function getOfflineChannels()
	{
		return dbQuery("SELECT * FROM tbl_channels WHERE isOnline = 0 AND active = 1 AND isDeleted = 0");
	}


	public function getDefaultId()
	{
		$sc = "";
		$qs = dbQuery("SELECT id FROM tbl_channels WHERE isDefault = 1");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}



	public function searchId($txt)
	{
		return dbQuery("SELECT id FROM tbl_channels WHERE name LIKE '%".$txt."%' OR code LIKE '%".$txt."%'");
	}
}

?>
