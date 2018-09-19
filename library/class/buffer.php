<?php

class buffer
{
  public function __construct()
  {

  }

  public function getDetails($id_order, $id_pa)
  {
    return dbQuery("SELECT * FROM tbl_buffer WHERE id_order = '".$id_order."' AND id_product_attribute = '".$id_pa."'");
  }


  public function dropBuffer($id)
  {
    return dbQuery("DELETE FROM tbl_buffer WHERE id_buffer = ".$id);
  }


} ///---- end class

 ?>
