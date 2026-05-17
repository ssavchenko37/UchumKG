<?php
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$post = json_decode($json, true);

$re = [];
if ($post['mode'] === "book-branches") {
	$exists = $DB->selectCol('SELECT book_id FROM ?_bk_book_stock WHERE branch_id=?', $post['branchID']);
	if (count($exists) === 0) {
		$exists[] = 0;
	}
	$re['books'] = $DB->selectCol('SELECT book_id AS ARRAY_KEY, CONCAT(title, " / ", author) AS book_meta FROM ?_bk_books WHERE book_id NOT IN(?a)', $exists);

	$exists = $DB->selectCol('SELECT branch_id FROM ?_bk_book_stock WHERE book_id=?', $post['bookID']);
	if (count($exists) === 0) {
		$exists[] = 0;
	}
	$re['branches'] = $DB->selectCol('SELECT branch_id AS ARRAY_KEY, name FROM ?_bk_branches WHERE branch_id NOT IN(?a)', $exists);
}

if ($post['mode'] === "transfer") {
	$tmp_branches = $DB->select('SELECT BR.*, BR.name AS branch_name
		, (ST.qty_total - ST.qty_reserved - ST.qty_paid - ST.qty_sold - ST.qty_defect) AS available
		FROM ?_bk_branches BR
		JOIN ?_bk_book_stock ST ON BR.branch_id=ST.branch_id
		WHERE BR.branch_id<>? AND ST.book_id=?'
		, $post['branch_from'], $post['bookID']
	);
	foreach ($tmp_branches as $r) {
		$re[$r['branch_id']] = $r['branch_name'] . "; кол-во сейчас: " . $r['available'];
	}
}

echo json_encode($re, JSON_UNESCAPED_UNICODE);
exit;