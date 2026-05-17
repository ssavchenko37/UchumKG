<?php
/** @var array $tldata */

$admin_id = ($tldata['umod'] == 'a') ? $tldata['id']: 1;
$id = $_POST['pid'];
$qty_add = $_POST['qty_total'];

if ( $id !== 'undefined' ) {
	$stock = $DB->selectRow('SELECT * FROM ?_bk_book_stock WHERE stock_id=?', $id);
	$qty_add =  $qty_add - $stock['qty_total'];
}

if ($_POST['mode'] === 'add') {
	$BS->receive($_POST['book_id'], $_POST['branch_id'], $qty_add, $admin_id);
}
if ($_POST['mode'] === 'edit') {
	$reason = ($qty_add < 0) ? "decreaseTotal": "increaseTotal";
	$BS->adjustStock($_POST['book_id'], $_POST['branch_id'], $qty_add, $admin_id, $reason);
}

if ($_POST['mode'] == "transfer") {
	$BS->transfer($_POST['book_id'], $_POST['branch_from'], $_POST['branch_to'], $_POST['qty_transfer'], $admin_id);
}

if ($_POST['mode'] == "delete") {
}

header("Cache-control: private");
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $_SERVER['REQUEST_URI']);
exit;