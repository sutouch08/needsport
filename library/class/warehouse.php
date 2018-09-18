<?php

class warehouse
{

  public $id_warehouse;
  public $warehouse_name;
  public $active;
  public $is_default;

  public function __construct($id='')
  {
    if($id != '')
    {
      $qs = dbQuery("SELECT * FROM tbl_warehouse WHERE id_warehouse = ".$id);

      if(dbNumRows($qs) == 1)
      {
        $rs = dbFetchObject($qs);
        $this->id_warehouse = $rs->id_warehouse;
        $this->warehouse_name = $rs->warehouse_name;
        $this->active = $rs->active;
        $this->is_default = $rs->is_default;
      }
    }
  }



  
} //--- end class

 ?>
