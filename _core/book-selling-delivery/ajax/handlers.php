<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re = [];

if ($post['mode'] === "date-verification") {
	$re = $DB->select('SELECT R.phone, R.where_go, R.delivery_to
		, P.amount, P.method, P.comment, P.status
		FROM ?_bk_payments P 
		JOIN ?_bk_reservations R ON P.reservation_id=R.reservation_id
		WHERE P.comment=?'
		, $post['dateStr']
	);
}

echo json_encode($re, JSON_UNESCAPED_UNICODE);
exit;