<?php
	$print		= new printer();
	
	$id_pa	= $_GET['id_pa'];
	$tRange	= $_GET['tRange']; //--- all = 1 range = 2
	$from		= $tRange == 2 ? fromDate($_GET['from']) : date('Y-01-01 00:00:00');
	$to		= $tRange == 2 ? toDate($_GET['to']) : date('Y-m-d 23:59:59');
	
	$title			= 'รายงานสรุปสินค้าค้างรับแยกตามรายการสินค้า';
	$pTitle		= get_product_reference($id_pa);
	$tTitle			= thaiDate($from, '/') . ' - ' . thaiDate($to, '/');
	
	$sc		= $print->doc_header($title);
	
	$sc 	.= '<table class="table">';
	$sc	.= '<tr style="font-size:14px;">';
	$sc 	.= '<td colspan="2" align="center">'. $title.'</td>';
	$sc	.= '</tr>';
	$sc	.= '<tr style="font-size:14px; border-bottom: solid 1px #ccc;">';
	$sc	.= '<td align="center" style="width:50%;">สินค้า | '. $pTitle .'</td>';
	$sc	.= '<td align="center" style="width:50%;">วันที่ | '. $tTitle .'</td>';
	$sc 	.= '</tr>';
	$sc	.= '</table>';
	
	$sc .= '<table class="table" style="border: solid 1px #ccc">';
	$sc .= '<thead>';
	$sc .= '<tr style="font-size:12px;">';
	$sc .= '<th style="width:10%; text-align:center;">ลำดับ</th>';
	$sc .= '<th style="width:30%;">รหัสสินค้า</th>';
	$sc .= '<th style="width:30%;">ใบสั่งซื้อ</th>';
	$sc .= '<th style="width:10%; text-align:right;">สั่งซื้อ</th>';
	$sc .= '<th style="width:10%; text-align:right;">รับแล้ว</th>';
	$sc .= '<th style="width:10%; text-align:right;">ค้างรับ</th>';
	$sc .= '</tr>';
	$sc .= '</thead>';
	
	
	
	$qr = "SELECT tbl_product_attribute.reference AS item_code, tbl_po.reference AS reference, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product_attribute ON tbl_po_detail.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr .= "LEFT JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color ";
	$qr .= "LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
	$qr .= "LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "WHERE tbl_po_detail.id_product_attribute = ".$id_pa." AND ";
	$qr .= "tbl_po.valid = 0 AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' ";
	$qr .= "GROUP BY tbl_po.reference, tbl_po_detail.id_product_attribute ";
	$qr .= "ORDER BY tbl_product_attribute.reference ASC, tbl_color.position ASC, tbl_size.position ASC, tbl_attribute.position";
	
	
	$qs = dbQuery($qr);
	
	if( dbNumRows( $qs ) > 0 )
	{
		$no = 1;
		$totalPo	= 0;
		$totalReceived = 0;
		$totalBalance = 0;
		while( $rs = dbFetchObject($qs) )
		{
			$balance = $rs->qty - $rs->received;
			$sc .= '<tr style="font-size:12px;">';
			$sc .= 	'<td align="center">'.$no.'</td>';
			$sc .= 	'<td>'. $rs->item_code .'</td>';
			$sc .= 	'<td>'. $rs->reference .'</td>';
			$sc .= 	'<td align="right">'. number_format($rs->qty) .'</td>';
			$sc .= 	'<td align="right">'. number_format($rs->received) .'</td>';
			$sc .= 	'<td align="right">'. number_format($balance) .'</td>';
			$sc .= '</tr>';
			
			$no++;
			$totalPo			+= $rs->qty;
			$totalReceived	+= $rs->received;
			$totalBalance	+= $balance;
		}
		
		$sc .= '<tr style="font-size:14px;">';
		$sc .= 	'<td colspan="3" align="right">รวม</td>';
		$sc .= 	'<td align="right">'. number_format($totalPo) .'</td>';
		$sc .= 	'<td align="right">'. number_format($totalReceived) .'</td>';
		$sc .=	'<td align="right">'. number_format($totalBalance) .'</td>';
		$sc .= '</tr>'; 	
	}	
	else
	{
		$sc .= '<tr style="font-size:14px;">';
		$sc .= 	'<td colspan="7" align="center"><h3>ไม่พบรายการค้างรับตามเงื่อนไขที่กำหนด</h3></td>';
		$sc .= '</tr>'; 		
	}
	$sc .= '</table>';
	
	$sc .= $print->doc_footer();
	
	echo $sc;
	
	?>