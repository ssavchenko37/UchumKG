<?php
/** @var array $tldata */

$id = $_POST['pid'];
$mode = $_POST['mode'];
$admin_id = $tldata['id'];
$phone = cleanPhone($_POST['phone']);

if ($mode === 'direct') {
	if ($_POST['amount'] && $_POST['book_id']) {
		$paymentId = $BS->createPayment(0, $_POST['amount'], 'cash', $admin_id, $phone, $_POST['comment']);
		$BS->confirmPayment($paymentId, $admin_id);
	
		$orderId = $BS->sell($_POST['book_id'], $_POST['branch_id'], $_POST['amount'], $admin_id, $_POST['qty'], $paymentId);

		$request = $TL->request_encode('order_id', $orderId);

		header("Cache-control: private");
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /book-sold/?" . $request);
		exit;
	}
}

header("Cache-control: private");
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $_SERVER['REQUEST_URI']);
exit;