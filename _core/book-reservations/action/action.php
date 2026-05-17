<?php
/** @var array $tldata */

$admin_id = ($tldata['umod'] == 'a') ? $tldata['id']: 1;
$phone = cleanPhone($_POST['phone']);

if ($_POST['mode'] === 'add') {
	$expected_amount = $_POST['qty']*$_POST['price'];
	$resId = $BS->reserve($_POST['book_id'], $_POST['branch_id'], $admin_id, $_POST['qty'], $phone, $_POST['where_go'], $_POST['delivery_to'], $_POST['for_courier']);
	if ($_POST['amount'] <= 0) {
		// ❌ нет оплаты
		$amount = 0;
		$paid_amount = 0;
		$paymentId = $BS->createPayment($resId, $amount, $paid_amount, 'transfer', $admin_id, $phone, $_POST['comment']);
		$BS->confirmPayment($paymentId, $admin_id);
	}
	elseif ($expected_amount > 0 && $_POST['amount'] < $expected_amount) {
		// ⚠️ частичная оплата
		$amount = 0;
		$paymentId = $BS->createPartialPayment($resId, $admin_id, $expected_amount, 'transfer', $phone, $_POST['comment']);
		$partId = $BS->addPaymentPart($paymentId, $_POST['amount'], 'transfer', $_POST['comment']);
	}
	else {
		// ✅ полная оплата
		$amount = $_POST['amount'];
		$paid_amount = $expected_amount;
		$paymentId = $BS->createPayment($resId, $amount, $paid_amount, 'transfer', $admin_id, $phone, $_POST['comment']);
		$BS->confirmPayment($paymentId, $admin_id);
	}

}

if ($_POST['mode'] === 'edit') {
	$existQty = $DB->selectCell('SELECT qty FROM ?_bk_reservations WHERE reservation_id=?', $_POST['pid']);
	$diff = $_POST['qty'] - $existQty;
	$fullPrice = $_POST['qty']*$_POST['price'];

	$BS->updateReservation($_POST['pid'], $diff, $phone, $admin_id, $_POST['branch_id'], $_POST['where_go'], $_POST['delivery_to'], $_POST['for_courier']);
	if ($diff !== 0) {
		$DB->query('UPDATE ?_bk_payments SET expected_amount = ? WHERE reservation_id=? AND expected_amount IS NOT NULL', $fullPrice, $_POST['pid']);
	}

	$exist = $DB->selectRow('SELECT R.*, P.payment_id, P.amount, P.comment, P.expected_amount, P.paid_amount 
		FROM ?_bk_reservations R
		JOIN ?_bk_payments P ON R.reservation_id = P.reservation_id
		WHERE R.reservation_id=?'
		, $_POST['pid']
	);

	if ($_POST['amount'] > 0) {
		if ($exist['expected_amount'] > 0) {
			p('уже partial-flow -> addPaymentPart');
			$partId = $BS->addPaymentPart($exist['payment_id'], $_POST['amount'], 'transfer', $_POST['comment']);
		} else {
			// single-flow
			if ($_POST['amount'] < $fullPrice) {
				p('🔥 автоматически переводим в partial payment и добавляем первую часть ->addPaymentPart');
				$BS->convertToPartialPayment($exist['payment_id'], $fullPrice);
				$partId = $BS->addPaymentPart($exist['payment_id'], $_POST['amount'], 'transfer', $_POST['comment']);
			} else {
				p('полная оплата updatePayment');
				$BS->updatePayment($exist['payment_id'], $_POST['amount'], $admin_id, $_POST['comment']);
			}
		}
	}
}

if ($_POST['mode'] === 'delete') {
	$BS->cancelReservation($_POST['pid'], $admin_id);
}

// header("Cache-control: private");
// header("HTTP/1.1 301 Moved Permanently");
// header("Location: " . $_SERVER['REQUEST_URI']);
// exit;