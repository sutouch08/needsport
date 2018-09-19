<?php
class cancle
{
  public function __construct()
  {
    
  }

  public function isExists($id_order, $id_pa, $id_zone)
  {
    $qs = dbQuery("SELECT id_cancle FROM tbl_cancle WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND id_zone = ".$id_zone);
    if(dbNumRows($qs) == 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add(array $ds = array())
  {
    if( ! empty( $ds ) )
		{
			$fields = "";
			$values = "";
			$i = 1;
			foreach( $ds as $field => $value )
			{
				$fields .= $i == 1 ? $field : ", ".$field;
				$values .= $i == 1 ? "'".$value."'" : ", '".$value."'";
				$i++;
			}

			return dbQuery("INSERT INTO tbl_cancle (".$fields.") VALUES (".$values.")");
		}

    return FALSE;
  }


  public function update(array $ds = array())
  {
    if(! empty($ds))
    {
      $qr  = "UPDATE tbl_cancle SET qty = qty+".$ds['qty']." ";
      $qr .= "WHERE id_order = ".$ds['id_order']." ";
      $qr .= "AND id_product_attribute = ".$ds['id_product_attribute']." ";
      $qr .= "AND id_zone = ".$ds['id_zone'];

      return dbQuery($qr);
    }

    return FALSE;
  }


  public function addCancle(array $ds = array())
  {
    if(! empty($ds))
    {
      $isExists = $this->isExists($ds['id_order'], $ds['id_product_attribute'], $ds['id_zone']);
      if($isExists === TRUE)
      {
        return $this->update($ds);
      }

      return $this->add($ds);
    }

    return FALSE;
  }



} //--- end class


 ?>
