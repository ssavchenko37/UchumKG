<?php
/** @var array $tldata */

$admin_id = ($tldata['umod'] == 'a') ? $tldata['id']: 1;
$phone = cleanPhone($_POST['phone']);
$overpayment = 0;
$expected_amount = $_POST['qty']*$_POST['price'];

if ($_POST['mode'] === 'add') {
	$amount = 0;

	$BS->begin();

	try {

		$resId = $BS->reserve($_POST['book_id'], $_POST['branch_id'], $admin_id, $_POST['qty'], $phone, $_POST['where_go'], $_POST['delivery_to'], $_POST['for_courier']);

		if ($_POST['amount'] <= 0) {
			// p('❌ нет оплаты');
			$paid_amount = 0;
			$paymentId = $BS->createPayment($resId, $amount, $paid_amount, 'transfer', $admin_id, $phone, $_POST['comment']);
			if ($_POST['where_go'] === 'hand') {
				$BS->confirmPayment($paymentId, $admin_id);
			}
		}
		elseif ($expected_amount > 0 && $_POST['amount'] < $expected_amount) {
			// p('⚠️ частичная оплата');
			$paymentId = $BS->createPartialPayment($resId, $admin_id, $expected_amount, 'transfer', $phone, $_POST['comment']);
			$part_return = $BS->addPaymentPart($paymentId, $admin_id, $_POST['amount'], 'transfer', $_POST['comment']);
			$partId = $part_return['part_id'];
			$overpayment = $part_return['overpayment'];
		}
		else {
			// p('✅ полная оплата');
			$amount = $_POST['amount'];
			$paid_amount = $expected_amount;
			$paymentId = $BS->createPayment($resId, $amount, $paid_amount, 'transfer', $admin_id, $phone, $_POST['comment']);
			$BS->confirmPayment($paymentId, $admin_id);
		}

		$BS->commit();

	} catch (\Throwable $e) {

		$BS->rollback();

		throw $e;
	}
}

if ($_POST['mode'] === 'edit') {
	$existQty = $DB->selectCell('SELECT qty FROM ?_bk_reservations WHERE reservation_id=?', $_POST['pid']);
	$existPaymentStatus = $DB->selectCell('SELECT status FROM ?_bk_payments WHERE reservation_id=?', $_POST['pid']);
	$diff = $_POST['qty'] - $existQty;
	$fullPrice = $_POST['qty']*$_POST['price'];

	$BS->begin();

	try {

		$BS->updateReservation($_POST['pid'], $diff, $phone, $admin_id, $_POST['branch_id'], $_POST['where_go'], $_POST['delivery_to'], $_POST['for_courier']);
		if ($diff !== 0) {
			$DB->query('UPDATE ?_bk_payments SET expected_amount = ? WHERE reservation_id=? AND expected_amount IS NOT NULL', $fullPrice, $_POST['pid']);
		}
		if ($_POST['where_go'] === 'hand' && $existPaymentStatus === 'new') {
			$DB->query('UPDATE ?_bk_payments SET status = ? WHERE reservation_id=? AND status = ?', 'confirmed', $_POST['pid'], 'new');
		}
		if ($_POST['where_go'] !== 'hand' && $existPaymentStatus === 'confirmed') {
			$DB->query('UPDATE ?_bk_payments SET status = ? WHERE reservation_id=? AND status = ?', 'new', $_POST['pid'], 'confirmed');
		}

		$exist = $DB->selectRow('SELECT R.*, P.payment_id, P.amount, P.comment, P.expected_amount, P.paid_amount 
			FROM ?_bk_reservations R
			JOIN ?_bk_payments P ON R.reservation_id = P.reservation_id
			WHERE R.reservation_id=?'
			, $_POST['pid']
		);

		if ($_POST['amount'] > 0) {
			if ($exist['expected_amount'] > 0) {
				// p('уже partial-flow -> addPaymentPart');
				$part_return = $BS->addPaymentPart($exist['payment_id'], $admin_id, $_POST['amount'], 'transfer', $_POST['comment']);
				$partId = $part_return['part_id'];
				$overpayment = $part_return['overpayment'];
			} else {
				// single-flow
				if ($_POST['amount'] < $fullPrice) {
					// p('🔥 автоматически переводим в partial payment и добавляем первую часть ->addPaymentPart');
					$BS->convertToPartialPayment($exist['payment_id'], $fullPrice);
					if ($exist['amount'] > 0) {
						$part_return = $BS->addPaymentPart($exist['payment_id'], $admin_id, $exist['amount'], 'transfer', $exist['comment']);
						$partId = $part_return['part_id'];
						$overpayment = $part_return['overpayment'];
					}
					$part_return = $BS->addPaymentPart($exist['payment_id'], $admin_id, $_POST['amount'], 'transfer', $_POST['comment']);
					$partId = $part_return['part_id'];
					$overpayment = $part_return['overpayment'];
				} else {
					// p('полная оплата updatePayment');
					$BS->updatePayment($exist['payment_id'], $_POST['amount'], $admin_id, $_POST['comment']);
					$BS->confirmPayment($exist['payment_id'], $admin_id);
				}
			}
		}

		$BS->commit();

	} catch (\Throwable $e) {

		$BS->rollback();

		throw $e;
	}
}

if ($_POST['mode'] === 'delete') {
	$BS->cancelReservation($_POST['pid'], $admin_id);
}

header("Cache-control: private");
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $_SERVER['REQUEST_URI']);
exit;