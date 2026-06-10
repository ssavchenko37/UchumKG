<?php
/** @var array $tldata */

$id = $_POST['pid'];
$mode = $_POST['mode'];
$fullPrice = $_POST['full_price'];

$admin_id = $tldata['id'];


if ($mode === 'order') {
	$exist = $DB->selectRow('SELECT R.*, P.payment_id, P.amount, P.comment, P.expected_amount, P.paid_amount 
		FROM ?_bk_reservations R
		JOIN ?_bk_payments P ON R.reservation_id = P.reservation_id
		WHERE P.payment_id=?'
		, $id
	);

	$BS->begin();

	try {

		if ($_POST['amount'] > 0) {
			if ($exist['expected_amount'] > 0) {
				$partId = $BS->addPaymentPart($exist['payment_id'], $admin_id, $_POST['amount'], 'transfer', $_POST['comment']);
			} else {
				if ($_POST['full_price'] == $_POST['amount']) {
					$BS->confirmPayment($exist['payment_id'], $admin_id);
				}
				$BS->updatePayment($exist['payment_id'], $_POST['amount'], $admin_id, $_POST['comment']);
			}
		}

		$payment = $DB->selectRow('SELECT * FROM ?_bk_payments WHERE payment_id=?', $exist['payment_id']);

		$orderId = $BS->sellFromReservation($exist['reservation_id'], $admin_id, $payment['amount'], $_POST['delivery_to'], $exist['payment_id']);

		$BS->commit();

	} catch (\Throwable $e) {

		$BS->rollback();

		throw $e;
	}

	$request = $TL->request_encode('order_id', $orderId);

	header("Cache-control: private");
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /book-sold/?" . $request);
	exit;
}
