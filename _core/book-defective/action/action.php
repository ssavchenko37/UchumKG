<?php
/** @var array $tldata */
$id = $_POST['pid'];
$mode = $_POST['mode'];
$admin_id = $tldata['id'];

if ($_POST['mode'] == "add") {
	$BS->defect($_POST['book_id'], $_POST['branch_id'], $_POST['qty'], $admin_id, $_POST['comment']);
}

if ($_POST['mode'] == "edit") {
	$returned = ($_POST['returned'] ?? '') ? $_POST['returned']: 0;
	$BS->updateDefect($id, $admin_id, $_POST['qty'], $_POST['comment'], $returned);
		
}
if ($_POST['mode'] == "delete") {
	//$book = $DB->selectRow('SELECT * FROM ?_bk_books WHERE book_id=?', $id);
	p('Deleaem Delete');
	// $DB->query('DELETE FROM ?_groups WHERE book_id=?', $id);
	// $DB->query('DELETE FROM ?_tutors WHERE book_id=?', $id);
}

header("Cache-control: private");
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $_SERVER['REQUEST_URI']);
exit;