<?php
	$id_tab 		= 66;
  $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);

?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col">
    	<h4 class="title"><i class="fa fa-bolt"></i>&nbsp;<?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p" >
	<?php if( $add ) : ?>
    		<button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
    <?php endif; ?>
        </p>
    </div>
</div><!-- / row -->

<hr style="margin-bottom:15px;" />
<?php
			$sCode 	= isset( $_POST['sCode'] ) ? trim( $_POST['sCode'] ) : ( getCookie('sChannelsCode') ? getCookie('sChannelsCode') : '' );
			$sName	= isset( $_POST['sName'] ) ? trim( $_POST['sName'] ) : ( getCookie('sChannelsName') ? getCookie('sChannelsName') : '' );
?>


    <form id="searchForm" method="post">
    <div class="row">
    	<div class="col-sm-2 padding-5 first">
        	<label>รหัส</label>
            <input type="text" class="form-control input-sm text-center search-box" name="sCode" id="sCode"  value="<?php echo $sCode; ?>"  />
        </div>
        <div class="col-sm-2 padding-5">
        	<label>ชื่อ</label>
            <input type="text" class="form-control input-sm text-center search-box" name="sName" id="sName"  value="<?php echo $sName; ?>"  />
        </div>

        <div class="col-sm-2">
        	<label class="display-block not-show">Apply</label>
            <button type="button" class="btn btn-sm btn-primary btn-block" onClick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
        </div>
        <div class="col-sm-2">
        	<label class="display-block not-show">Apply</label>
            <button type="button" class="btn btn-sm btn-warning btn-block" onClick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
        </div>

    </div>
    </form>
    <hr class="margin-top-15" />

<?php
	$where	= "WHERE isDeleted = 0 ";
	if( $sCode != '' )
	{
		createCookie('sChannelsCode', $sCode);
		$where .= "AND code LIKE '%". $sCode ."%' ";
	}

	if( $sName != '' )
	{
		createCookie('sChannelsName', $sName);
		$where .= "AND name LIKE '%". $sName ."%' ";
	}


	$where .= "ORDER BY code ASC";

	$paginator	= new paginator();
	$get_rows	= get_rows();
	$paginator->Per_Page('tbl_channels', $where, $get_rows);
	$paginator->display($get_rows, 'index.php?content=channels');

	$qs = dbQuery("SELECT * FROM tbl_channels ".$where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page);
?>
	<div class="row">
    	<div class="col-sm-12">
        	<table class="table table-striped border-1">
            	<thead>
                	<tr class="font-size-12">
                    	<th class="width-5 text-center">ลำดับ</th>
                        <th class="width-15">รหัส</th>
                        <th class="width-40">ชื่อ</th>
                        <th class="width-10 text-center">ออนไลน์</th>
												<th class="width-10 text-center">เปิดใช้งาน</th>
												<th class="width-10 text-center">ค่าเริ่มต้น</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
<?php	if( dbNumRows($qs) > 0 ) : ?>
<?php		$no = row_no(); ?>
<?php		$cs = new channels();		?>
<?php		while( $rs = dbFetchObject($qs) ) : ?>
					<tr class="font-size-12" id="row_<?php echo $rs->id; ?>">
                    	<td class="middle text-center"><?php echo $no; ?></td>
                        <td class="middle"><?php echo $rs->code; ?></td>
                        <td class="middle"><?php echo $rs->name; ?></td>
                        <td class="middle text-center">
													<?php if($rs->isOnline == 1) : ?>
														<?php echo isActived($rs->isOnline); ?>
													<?php endif; ?>
												</td>
												<td class="middle text-center"><?php echo isActived($rs->active); ?></td>
                        <td class="middle text-center">
													<?php if($rs->isDefault == 1) : ?>
														<i class="fa fa-star green"></i>
													<?php endif; ?>
												</td>
                        <td class="middle text-right">
                        <?php if( $edit ) : ?>
													<?php if($rs->isDefault == 0) : ?>
														<button type="button" class="btn btn-xs btn-info" onclick="setDefault(<?php echo $rs->id; ?>)"><i class="fa fa-star"></i></button>
													<?php else : ?>
														<button type="button" class="btn btn-xs" disabled><i class="fa fa-star"></i></button>
													<?php endif; ?>
                        	<button type="button" class="btn btn-xs btn-warning" onClick="getEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <?php endif; ?>
                        <?php if( $delete && $rs->isDefault == 0 ) : ?>
                        	<button type="button" class="btn btn-xs btn-danger" onClick="remove('<?php echo $rs->id; ?>', '<?php echo $rs->code; ?>')">
                            	<i class="fa fa-trash"></i>
                            </button>
                        <?php endif; ?>
                        </td>
                    </tr>
<?php			$no++;	?>
<?php		endwhile; ?>
<?php	else : ?>
				<tr>
                	<td colspan="6" align="center"><h4>ไม่พบรายการ</h4></td>
				</tr>
<?php	endif; ?>
                </tbody>
            </table>
        </div>
    </div>


<input type="hidden" name="isActive" id="isActive" value="1" />
<input type="hidden" name="isOnline" id="isOnline" value="0" />

	<!---- Modal Add --->
	<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:300px;">
			<div class="modal-content">
	  			<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="modal_title">เพิ่มช่องทางการขาย</h4>
				 </div>
				 <div class="modal-body" id="modal_body">
                 <div class="row">
                 	<div class="col-sm-6">
                    	<label>รหัส</label>
                        <input type="text" class="form-control input-sm" id="addCode" placeholder="กำหนดรหัสช่องทางการขาย" />
                        <span class="help-block red" id="addCode-error"></span>
                    </div>

                    <div class="col-sm-12 margin-top-10">
                    	<label>ชื่อช่องทางการขาย</label>
                        <input type="text" class="form-control input-sm" id="addName" placeholder="กำหนดชื่อช่องทางการขาย" />
                        <span class="help-block red" id="addName-error"></span>
                    </div>


                     <div class="col-sm-12 margin-top-10">
                    	<label>ออนไลน์หรือไม่</label>
                        <div class="btn-group width-100">
                        	<button type="button" class="btn btn-sm width-25" id="btn-add-online-yes" onclick="toggleOnlineAdd(1)">ใช่</button>
                            <button type="button" class="btn btn-sm btn-danger width-25" id="btn-add-online-no" onclick="toggleOnlineAdd(0)">ไม่ใช่</button>
                        </div>
                    </div>

                 </div>
                 </div>
				 <div class="modal-footer">
					<button type="button" class="btn btn-sm btn-success" onClick="addNew()" ><i class="fa fa-save"></i> บันทึก</button>
				 </div>
			</div>
		</div>
	</div>



    <!---- Modal Edit --->
	<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:300px;">
			<div class="modal-content">
	  			<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="modal_title">เพิ่มช่องทางการขาย</h4>
                    <input type="hidden" id="id_channels" value="" />
				 </div>
				 <div class="modal-body" id="modal_body">
                 <div class="row">
                 	<div class="col-sm-6">
                    	<label>รหัส</label>
                        <input type="text" class="form-control input-sm" id="editCode" placeholder="กำหนดรหัสช่องทางการขาย" />
                        <span class="help-block red" id="editCode-error"></span>
                    </div>

                    <div class="col-sm-12 margin-top-10">
                    	<label>ชื่อช่องทางการขาย</label>
                        <input type="text" class="form-control input-sm" id="editName" placeholder="กำหนดชื่อช่องทางการขาย" />
                        <span class="help-block red" id="editName-error"></span>
                    </div>


                    <div class="col-sm-12 margin-top-10">
                    	<label>ออนไลน์หรือไม่</label>
                        <div class="btn-group width-100">
                        	<button type="button" class="btn btn-sm width-25" id="btn-edit-online-yes" onclick="toggleOnlineEdit(1)">ใช่</button>
                            <button type="button" class="btn btn-sm btn-danger width-25" id="btn-edit-online-no" onclick="toggleOnlineEdit(0)">ไม่ใช่</button>
                        </div>
                    </div>

                    <div class="col-sm-12 margin-top-10">
                    	<label>เปิดใช้งานหรือไม่</label>
                        <div class="btn-group width-100">
                        	<button type="button" class="btn btn-sm width-25" id="btn-active-yes" onclick="toggleActive(1)">ใช่</button>
                            <button type="button" class="btn btn-sm btn-danger width-25" id="btn-active-no" onclick="toggleActive(0)">ไม่ใช่</button>
                        </div>
                    </div>
                 </div>
                 </div>
				 <div class="modal-footer">
					<button type="button" class="btn btn-sm btn-success" onClick="saveEdit()" ><i class="fa fa-save"></i> บันทึก</button>
				 </div>
			</div>
		</div>
	</div>


</div><!--/ container -->
<script src="script/channels.js"></script>
<script src="script/validate.js"></script>
