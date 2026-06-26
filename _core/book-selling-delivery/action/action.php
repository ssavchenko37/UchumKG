<?php
/** @var array $tldata */

$id = $_POST['pid'];
$mode = $_POST['mode'];
$admin_id = $tldata['id'];

if ($mode === 'order') {
	$payment = $DB->selectRow('SELECT r.reservation_id, r.book_id, r.qty, r.phone, r.created_at, r.expires_at
		, b.title, b.author, b.price
		, br.branch_id, br.name AS branch_name
		, p.payment_id, p.amount, p.paid_amount, p.comment
		FROM ?_bk_payments p
		JOIN ?_bk_reservations r ON r.reservation_id = p.reservation_id
		JOIN ?_bk_books b ON b.book_id = r.book_id
		JOIN ?_bk_branches br ON br.branch_id = r.branch_id
		WHERE p.payment_id=?'
		, $id
	);

	if (($_POST['surcharge'] ?? '')) {
		$BS->updatePayment($payment['payment_id'], $_POST['surcharge'], $admin_id, $_POST['comment']);
		$payment['paid_amount'] = $DB->selectCell('SELECT paid_amount FROM ?_bk_payments WHERE payment_id=?', $id);
	}

	$orderId = $BS->sellFromReservation($payment['reservation_id'], $admin_id, $payment['paid_amount'], $_POST['delivery_to'], $payment['payment_id']);

	$request = $TL->request_encode('order_id', $orderId);

	header("Cache-control: private");
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /book-sold/?" . $request);
	exit;
}
