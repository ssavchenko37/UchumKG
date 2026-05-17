<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re = [];

if ($post['mode'] === "get-books") {
	$books_all = $DB->select('SELECT ST.*, (ST.qty_total - ST.qty_paid - ST.qty_sold - ST.qty_defect) AS available, BK.*'
		. ' FROM ?_bk_book_stock ST'
		. ' INNER JOIN ?_bk_books BK ON ST.book_id=BK.book_id'
		. ' WHERE branch_id=?'
		. ' ORDER BY BK.title'
		, $post['branchID']
	);

	foreach ($books_all as $r) {
		if ($r['available'] > 0) {
			$re[$r['book_id']] = $r['title'] . "; " . $r['author'];
		}
	}
}

if ($post['mode'] === "get-book") {
	$re['max'] = $DB->selectCell('SELECT (qty_total - qty_paid - qty_sold - qty_defect) AS available FROM ?_bk_book_stock WHERE branch_id=? AND book_id=?', $post['branchID'], $post['bookID']);
	$re['price'] = $DB->selectCell('SELECT price FROM ?_bk_books WHERE book_id=?', $post['bookID']);
}

echo json_encode($re, JSON_UNESCAPED_UNICODE);
exit;