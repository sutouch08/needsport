<?php
class qc
{
  public function __construct()
  {
    
  }

  public function dropQc($id_order, $id_pa)
  {
    return dbQuery("DELETE FROM tbl_qc WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
  }

} //---


 ?>
