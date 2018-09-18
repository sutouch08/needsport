// JavaScript Document


function toggleActive(i){
	$('#isActive').val(i);
	if(i == 1 ){
		$('#btn-active-no').removeClass('btn-danger');
		$('#btn-active-yes').addClass('btn-success');
	}else{
		$('#btn-active-yes').removeClass('btn-success');
		$('#btn-active-no').addClass('btn-danger');
	}
}




function toggleOnlineAdd(i){
	if( i == 1 ){
		$("#btn-add-online-no").removeClass('btn-danger');
		$("#btn-add-online-yes").addClass('btn-success');
	}else{
		$("#btn-add-online-yes").removeClass('btn-success');
		$("#btn-add-online-no").addClass('btn-danger');
	}
	$("#isOnline").val(i);
}


function toggleOnlineEdit(i){
	if( i == 1 ){
		$("#btn-edit-online-no").removeClass('btn-danger');
		$("#btn-edit-online-yes").addClass('btn-success');
	}else{
		$("#btn-edit-online-yes").removeClass('btn-success');
		$("#btn-edit-online-no").addClass('btn-danger');
	}
	$("#isOnline").val(i);
}


// แสดง modal แก้ไขชนิดสินค้า
function getEdit(id){
	$.ajax({
		url:"controller/channelsController.php?getData",
		type:"GET",
		cache:"false",
		data:{
			"id" : id
		},
		success: function(rs){
			var rs = $.trim(rs);
			var arr = rs.split(' | ');
			if( arr.length == 5 ){
				$("#id_channels").val( arr[0] );
				$("#editCode").val( arr[1] );
				$("#editName").val( arr[2] );
				toggleActive(arr[3]);
				toggleOnlineEdit(arr[4]);
				$("#edit-modal").modal('show');
			}else{
				swal("ข้อผิดพลาด !!", "ไม่พบข้อมูลที่ต้องการแก้ไข", "error");
			}
		}
	});
}

//  บันทึกการแก้ไข
// เพิ่มชนิดสินค้าใหม่
function saveEdit(){
	var id 	= $("#id_channels").val();
	var code = $("#editCode").val();
	var name = $("#editName").val();
	var isActive = $("#isActive").val();
	var isOnline = $("#isOnline").val();
	if( code.length == 0 || name.length == 0 ){
		swal("ข้อมูลไม่ครบถ้วน");
		return false;
	}

	$.ajax({
		url:"controller/channelsController.php?saveEditChannels",
		type:"POST",
		cache:"false",
		data:{
			"id" : id,
			"code" : code,
			"name" : name,
			"isActive" : isActive,
			"isOnline" : isOnline
		},
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				$("#edit-modal").modal('hide');
				swal({
					title: "Updated",
					type: "success",
					timer: 1000
				});

				setTimeout(function(){ window.location.reload(); }, 1200);

			}else if( rs == 'nameError' ){

				var message = "ชื่อชนิดซ้ำ";
				addError($("#editName"), $("#editName-error"), message);

			}else if( rs == 'codeError' ){

				var message = "รหัสชนิดซ้ำ";
				addError($("#editCode"), $("#editCode-error"), message);

			}else{

				$("#edit-modal").modal('hide');
				swal({ title: "ข้อผิดพลาด !!", text: rs, type: "error" });

			}
		}
	});
}


// เพิ่มชนิดสินค้าใหม่
function addNew(){
	var code = $("#addCode").val();
	var name = $("#addName").val();
	//var isDefault = $("#isDefault").val();
	var isOnline = $("#isOnline").val();
	if( code.length == 0 || name.length == 0 ){
		swal("ข้อมูลไม่ครบถ้วน");
		return false;
	}

	$.ajax({
		url:"controller/channelsController.php?addChannels",
		type:"POST",
		cache:"false",
		data:{
			"code" : code,
			"name" : name,
			"isOnline" : isOnline
		},
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				$("#add-modal").modal('hide');
				swal({
					title: "Success",
					type: "success",
					timer: 1000
				});

				setTimeout(function(){ window.location.reload(); }, 1200);

			}else if( rs == 'nameError' ){

				var message = "ชื่อชนิดซ้ำ";
				addError($("#addName"), $("#addName-error"), message);

			}else if( rs == 'codeError' ){

				var message = "รหัสชนิดซ้ำ";
				addError($("#addCode"), $("#addCode-error"), message);

			}else{

				$("#add-modal").modal('hide');
				swal({ title: "ข้อผิดพลาด !!", text: rs, type: "error" });

			}
		}
	});
}



function setDefault(id){
	$.ajax({
		url:'controller/channelsController.php?setDefault',
		type:'GET',
		cache:'false',
		data:{
			'id' : id
		},
		success:function(rs){
			var rs = $.trim(rs);
			if(rs == 'success'){
				swal({
					title:'Success',
					type:'success',
					timer: 1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1200);
			}else{
				swal({ title:"ข้อผิดพลาด !!", text:rs, type:"error"});
			}
		}
	});
}



function remove(id, code){
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ "'+code+'" หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:"controller/channelsController.php?deleteChannels",
			type:"GET",
			cache:"false",
			data: {
				"id" : id
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title: "Deleted",
						text: "ลบรายการเรียบร้อยแล้ว",
						type: "success",
						timer: 1000 
					});
					$("#row_"+id).remove();
				}else{
					swal("ข้อผิดพลาด !", "ลบรายการไม่สำเร็จ", "error");
				}
			}
		});
	});
}




function clearAddFields(){
	$("#addCode").val('');
	$("#addName").val('');
	removeError($("#addCode"), $("#addCode-error"), "");
	removeError($("#addName"), $("#addName-error"), "");
}


function clearEditFields(){
	$("#id_channels").val('');
	$("#editCode").val('');
	$("#editName").val('');
	removeError($("#editCode"), $("#editCode-error"), "");
	removeError($("#editName"), $("#editName-error"), "");
}


$("#add-modal").on('shown.bs.modal', function(e){ $("#addCode").focus(); });
$(".search-box").keyup(function(e) {
    if( e.keyCode == 13 ){
		getSearch();
	}
});

function goAdd(){
	$("#add-modal").modal('show');
}



function goBack(){
	window.location.href = "index.php?content=channels";
}



function getSearch(){
	var code = $("#sCode").val();
	var name = $("#sName").val();
	if( code.length > 0 || name.length > 0 ){
		$("#searchForm").submit();
	}
}




function clearFilter(){
	$.get("controller/channelsController.php?clearFilter", function(){ goBack(); });
}
