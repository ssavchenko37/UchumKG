<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re = [];

if ($post['mode'] === "book-branches") {
	$re['book'] = $DB->selectRow('SELECT * FROM ?_bk_books WHERE book_id=?', $post['bookID']);
	
	$branches = $DB->select('SELECT (ST.qty_total - ST.qty_paid - ST.qty_sold - ST.qty_defect) AS available
		, BR.branch_id, BR.name, BR.is_store
		FROM ?_bk_book_stock ST
		JOIN ?_bk_branches BR ON ST.branch_id=BR.branch_id
		WHERE book_id=?'
		, $post['bookID']
	);
	foreach ($branches as $br) {
		if ($br['available'] >= $post['qtyReserv']) {
			$re['branches'][$br['branch_id']] = $br['name'] . "; доступно: " . $br['available'];
		} else {
			$diff = $post['qtyReserv'] - $br['available'];
			$re['branches'][$br['branch_id']] = $br['name'] . "; доступно: " . $br['available'] . "; в минус: " . $diff;
		}		
	}
}

if ($post['mode'] === "date-verification") {
	$re = $DB->select('SELECT R.phone, R.where_go, R.delivery_to
		, P.amount, P.method, P.comment, P.status
		, PP.amount AS part_amount, PP.comment AS part_comment
		FROM ?_bk_payments P 
		JOIN ?_bk_reservations R ON P.reservation_id=R.reservation_id
		LEFT JOIN ?_bk_payment_parts PP ON P.payment_id=PP.payment_id
		WHERE P.comment=? OR PP.comment=?'
		, $post['dateStr'], $post['dateStr']
	);
}

echo json_encode($re, JSON_UNESCAPED_UNICODE);
exit;