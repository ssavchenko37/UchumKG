<?php
$id = $_POST['pid'];
$ins = [];
foreach ($_REQUEST as $rKey => $rVal) {
	if (in_array($rKey, array("title","author","isbn","price"))) {
		$ins[$rKey] = trim($rVal);
	}
}

if ($_POST['mode'] == "add") {
	$ins['created_at'] = date('Y-m-d H:i:s');
	$DB->query('INSERT INTO ?_bk_books (?#) VALUES (?a)', array_keys($ins), array_values($ins));
}
if ($_POST['mode'] == "edit") {
	$DB->query('UPDATE ?_bk_books SET ?a WHERE book_id=?', $ins, $id);
}
if ($_POST['mode'] == "delete") {
	$book = $DB->selectRow('SELECT * FROM ?_bk_books WHERE book_id=?', $id);
	p('Deleaem Delete');
	p($book);
	// $DB->query('DELETE FROM ?_groups WHERE book_id=?', $id);
	// $DB->query('DELETE FROM ?_tutors WHERE book_id=?', $id);
}

header("Cache-control: private");
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $_SERVER['REQUEST_URI']);
exit;