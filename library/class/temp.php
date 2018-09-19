<?php
class temp
{
  public function __construct()
  {
    
  }

  public function dropProductTemp($id_order, $id_pa)
  {
    return dbQuery("DELETE FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
  }
}

 ?>
